<?php

namespace App\Jobs;

use App\Models\Marca;
use App\Models\QrScan;
use App\Models\ScanGroup;
use App\Models\Setting;
use App\Services\Bitrix24Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Throwable;

/**
 * Empuja un grupo de escaneos como UN SOLO deal en Bitrix24: crea/deduplica
 * el contacto de cada integrante, los vincula todos al deal y concentra la
 * información completa (datos, marcas y comentarios) en la descripción.
 * Los contactos que ya estaban registrados ($extraScanIds) se incluyen
 * con su información existente.
 */
class SendScanGroupToBitrix implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public bool $deleteWhenMissingModels = true;

    /**
     * @param  list<int>  $extraScanIds
     */
    public function __construct(public ScanGroup $group, public array $extraScanIds = []) {}

    public function handle(): void
    {
        // Corre después de la respuesta HTTP: no abortar si el cliente se desconecta
        ignore_user_abort(true);

        if (! Bitrix24Service::isEnabled()) {
            return;
        }

        $group = $this->group->fresh(['scans.marcas', 'scans.user', 'user']);

        if ($group === null || $group->bitrix_deal_id !== null) {
            return;
        }

        $extras = QrScan::with(['marcas', 'user'])
            ->whereIn('id', $this->extraScanIds)
            ->get();

        /** @var Collection<int, QrScan> $scans */
        $scans = $group->scans->concat($extras)->unique('id')->values();

        if ($scans->isEmpty()) {
            return;
        }

        $group->forceFill(['bitrix_attempts' => $this->attempts()])->save();

        try {
            $bitrix = Bitrix24Service::fromSettings();

            $contactIds = [];

            foreach ($scans as $scan) {
                $contactIds[$scan->id] = $bitrix->findOrCreateContact([
                    'name' => $scan->nombre,
                    'last_name' => $scan->apellidos,
                    'email' => $scan->correo,
                    'phone' => $scan->telefono,
                    'company' => $scan->empresa,
                ]);
            }

            $dealId = $bitrix->addDeal($this->dealFields($group, $scans, $contactIds, $extras));

            $group->forceFill([
                'bitrix_deal_id' => $dealId,
                'bitrix_status' => QrScan::BITRIX_SENT,
                'bitrix_error' => null,
                'bitrix_synced_at' => now(),
            ])->save();

            foreach ($scans as $scan) {
                $scan->forceFill([
                    // Los re-escaneados conservan el deal de su registro original
                    'bitrix_deal_id' => $scan->bitrix_deal_id ?? $dealId,
                    'bitrix_contact_id' => $contactIds[$scan->id],
                    'bitrix_status' => QrScan::BITRIX_SENT,
                    'bitrix_error' => null,
                    'bitrix_synced_at' => now(),
                ])->save();
            }
        } catch (Throwable $e) {
            $error = mb_substr($e->getMessage(), 0, 500);

            $group->forceFill([
                'bitrix_status' => QrScan::BITRIX_FAILED,
                'bitrix_error' => $error,
            ])->save();

            $group->scans()->update([
                'bitrix_status' => QrScan::BITRIX_FAILED,
                'bitrix_error' => $error,
            ]);

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $error = mb_substr($exception?->getMessage() ?? 'Error desconocido', 0, 500);

        ScanGroup::whereKey($this->group->getKey())->update([
            'bitrix_status' => QrScan::BITRIX_FAILED,
            'bitrix_error' => $error,
        ]);
    }

    /**
     * @param  Collection<int, QrScan>  $scans
     * @param  array<int, int>  $contactIds
     * @param  Collection<int, QrScan>  $extras
     * @return array<string, mixed>
     */
    private function dealFields(ScanGroup $group, Collection $scans, array $contactIds, Collection $extras): array
    {
        $categoryId = (int) Setting::get('bitrix.category_id', '0');
        $stageId = trim((string) Setting::get('bitrix.stage_id', ''));

        // Bitrix espera etapas con prefijo de pipeline (p. ej. C13:NEW)
        if ($stageId !== '' && $categoryId > 0 && ! str_contains($stageId, ':')) {
            $stageId = "C{$categoryId}:{$stageId}";
        }

        $extraIds = $extras->pluck('id')->all();

        $lineas = array_filter([
            'Empresa: '.(($group->empresa ?: $scans->first()->empresa) ?: '—'),
            $group->notas ? 'Notas del grupo: '.$group->notas : null,
        ], fn ($linea) => $linea !== null);

        foreach ($scans as $index => $scan) {
            $yaRegistrado = in_array($scan->id, $extraIds, true);

            $extrasScan = array_values(array_filter(
                $scan->campos_adicionales ?? [],
                fn ($linea) => trim((string) $linea) !== '',
            ));

            $lineas = [
                ...$lineas,
                '',
                sprintf(
                    'Contacto %d: %s%s',
                    $index + 1,
                    trim($scan->nombre.' '.($scan->apellidos ?? '')),
                    $yaRegistrado ? ' (ya registrado previamente)' : '',
                ),
                '• Puesto: '.($scan->puesto ?: '—'),
                '• Empresa: '.($scan->empresa ?: '—'),
                '• Estado: '.($scan->estado ?: '—'),
                '• Rol: '.($scan->rol ?: '—'),
                '• Teléfono: '.($scan->telefono ?: '—'),
                '• Correo: '.($scan->correo ?: '—'),
                ...array_map(fn ($linea) => '• '.$linea, $extrasScan),
            ];
        }

        // Marcas de interés (sin repetir): pares marca — comentario de todos los contactos
        $marcas = $scans
            ->flatMap(fn (QrScan $scan) => $scan->marcas->map(function (Marca $marca) {
                $comentario = trim((string) $marca->pivot->comentarios);

                return '• '.$marca->nombre.($comentario !== '' ? " — {$comentario}" : '');
            }))
            ->unique()
            ->values()
            ->all();

        if ($marcas !== []) {
            $lineas = [...$lineas, '', 'Marcas de interés:', ...$marcas];
        }

        $capturadoPor = $group->user
            ? trim($group->user->name.' '.($group->user->last_name ?? ''))
            : '—';

        $lineas = [
            ...$lineas,
            '',
            'Capturado por: '.$capturadoPor,
            'Escaneo QR Grupal #'.$group->id.' — '.$group->created_at->format('Y-m-d H:i'),
        ];

        $titulo = sprintf(
            'Escaneo QR Grupal — %s — %d contacto(s)',
            ($group->empresa ?: $scans->first()->empresa) ?: 'Grupo #'.$group->id,
            $scans->count(),
        );

        return array_filter([
            'TITLE' => $titulo,
            'CONTACT_IDS' => array_values(array_unique(array_filter($contactIds))),
            'COMMENTS' => nl2br(e(implode("\n", $lineas))),
            'SOURCE_ID' => 'WEB',
            'SOURCE_DESCRIPTION' => 'App Escaneo QR SMTVYS',
            'CATEGORY_ID' => $categoryId,
            'STAGE_ID' => $stageId !== '' ? $stageId : null,
            'ASSIGNED_BY_ID' => Setting::get('bitrix.responsible_id') ?: null,
        ], fn ($value) => $value !== null && $value !== '' && $value !== []);
    }
}
