<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendScanGroupToBitrix;
use App\Jobs\SendScanToBitrix;
use App\Models\QrScan;
use App\Models\ScanGroup;
use App\Models\Setting;
use App\Services\Bitrix24Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class BitrixSettingController extends Controller
{
    /**
     * Página de configuración de la integración Bitrix24.
     */
    public function edit(): Response
    {
        return Inertia::render('admin/integraciones/Bitrix', [
            'settings' => [
                'enabled' => Setting::getBool('bitrix.enabled'),
                'webhook' => Setting::get('bitrix.webhook', ''),
                'category_id' => Setting::get('bitrix.category_id', '13'),
                'stage_id' => Setting::get('bitrix.stage_id', ''),
                'responsible_id' => Setting::get('bitrix.responsible_id', ''),
            ],
            'stats' => [
                'enviados' => QrScan::where('bitrix_status', QrScan::BITRIX_SENT)->count(),
                'pendientes' => QrScan::where('bitrix_status', QrScan::BITRIX_PENDING)->count(),
                'fallidos' => QrScan::where('bitrix_status', QrScan::BITRIX_FAILED)->count(),
                'sinEnviar' => QrScan::whereNull('bitrix_deal_id')->whereNull('bitrix_status')->count(),
            ],
            'fallidos' => QrScan::where('bitrix_status', QrScan::BITRIX_FAILED)
                ->latest('updated_at')
                ->limit(10)
                ->get(['id', 'nombre', 'apellidos', 'correo', 'bitrix_error', 'bitrix_attempts', 'updated_at']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['required', 'boolean'],
            'webhook' => ['nullable', 'required_if_accepted:enabled', 'url', 'regex:~/rest/~'],
            'category_id' => ['nullable', 'integer', 'min:0'],
            'stage_id' => ['nullable', 'string', 'max:100'],
            'responsible_id' => ['nullable', 'integer', 'min:1'],
        ], [
            'webhook.required_if_accepted' => 'El webhook es obligatorio para habilitar la integración.',
            'webhook.regex' => 'Debe ser un webhook entrante de Bitrix24 (contiene /rest/).',
        ]);

        Setting::set('bitrix.enabled', $data['enabled'] ? '1' : '0');
        Setting::set('bitrix.webhook', trim((string) ($data['webhook'] ?? '')));
        Setting::set('bitrix.category_id', (string) ($data['category_id'] ?? ''));
        Setting::set('bitrix.stage_id', trim((string) ($data['stage_id'] ?? '')));
        Setting::set('bitrix.responsible_id', (string) ($data['responsible_id'] ?? ''));

        return back()->with('success', 'Configuración de Bitrix24 guardada.');
    }

    /**
     * Prueba la conexión llamando a `profile` con el webhook indicado.
     */
    public function test(Request $request): JsonResponse
    {
        $data = $request->validate(['webhook' => ['required', 'url']]);

        try {
            $profile = (new Bitrix24Service($data['webhook']))->profile();
        } catch (RuntimeException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }

        $nombre = trim(($profile['NAME'] ?? '').' '.($profile['LAST_NAME'] ?? ''));

        return response()->json([
            'ok' => true,
            'user' => $nombre !== '' ? $nombre : 'Usuario ID '.($profile['ID'] ?? '¿?'),
        ]);
    }

    /**
     * Lista los pipelines (categorías) de deals del portal.
     */
    public function pipelines(Request $request): JsonResponse
    {
        $data = $request->validate(['webhook' => ['required', 'url']]);

        try {
            $categories = (new Bitrix24Service($data['webhook']))->dealCategories();
        } catch (RuntimeException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }

        return response()->json(['ok' => true, 'categories' => $categories]);
    }

    /**
     * Lista las etapas de un pipeline.
     */
    public function stages(Request $request): JsonResponse
    {
        $data = $request->validate([
            'webhook' => ['required', 'url'],
            'category_id' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $stages = (new Bitrix24Service($data['webhook']))->dealStages((int) $data['category_id']);
        } catch (RuntimeException $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }

        return response()->json(['ok' => true, 'stages' => $stages]);
    }

    /**
     * Reenvía lo que no tiene deal en Bitrix (nunca enviado o fallido):
     * escaneos individuales por separado y cada grupo como un solo deal.
     * Se procesa justo después de responder (sin worker de colas).
     */
    public function syncPending(): JsonResponse
    {
        if (! Bitrix24Service::isEnabled()) {
            return response()->json([
                'ok' => false,
                'error' => 'Habilita la integración y guarda el webhook antes de sincronizar.',
            ], 422);
        }

        $scans = QrScan::whereNull('scan_group_id')->whereNull('bitrix_deal_id')->get();

        foreach ($scans as $scan) {
            $scan->forceFill([
                'bitrix_status' => QrScan::BITRIX_PENDING,
                'bitrix_error' => null,
            ])->save();

            SendScanToBitrix::dispatchAfterResponse($scan);
        }

        $groups = ScanGroup::whereNull('bitrix_deal_id')->has('scans')->get();

        foreach ($groups as $group) {
            $group->forceFill([
                'bitrix_status' => QrScan::BITRIX_PENDING,
                'bitrix_error' => null,
            ])->save();

            $group->scans()->update([
                'bitrix_status' => QrScan::BITRIX_PENDING,
                'bitrix_error' => null,
            ]);

            SendScanGroupToBitrix::dispatchAfterResponse($group);
        }

        return response()->json([
            'ok' => true,
            'queued' => $scans->count() + $groups->count(),
        ]);
    }
}
