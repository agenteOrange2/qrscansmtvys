<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserQrScanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQrScanRequest;
use App\Http\Requests\Admin\StoreScanGroupRequest;
use App\Http\Requests\Admin\UpdateQrScanRequest;
use App\Jobs\SendScanGroupToBitrix;
use App\Jobs\SendScanToBitrix;
use App\Models\Marca;
use App\Models\QrScan;
use App\Models\ScanGroup;
use App\Services\Bitrix24Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QrScanController extends Controller
{
    /**
     * Página del escáner QR (modo individual y grupal).
     */
    public function scan(): Response
    {
        return Inertia::render('admin/scanner/Scan', [
            'marcas' => Marca::orderBy('nombre')->get(['id', 'nombre', 'descripcion', 'imagen']),
        ]);
    }

    /**
     * Listado de usuarios capturados con búsqueda, orden y paginación.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('Administrador');

        $search = (string) $request->query('search', '');
        $perPage = (int) $request->query('per_page', 10);
        $sortField = (string) $request->query('sort', 'created_at');
        $sortDirection = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sortField, ['id', 'nombre', 'apellidos', 'empresa', 'created_at'], true)) {
            $sortField = 'created_at';
        }

        $scans = QrScan::query()
            ->with(['user:id,name,last_name', 'group:id,empresa'])
            ->when(! $isAdmin, fn ($query) => $query->where('user_id', $user->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    foreach (['nombre', 'apellidos', 'puesto', 'empresa', 'telefono', 'correo'] as $field) {
                        $inner->orWhere($field, 'like', "%{$search}%");
                    }
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage > 0 && $perPage <= 100 ? $perPage : 10)
            ->withQueryString();

        return Inertia::render('admin/capturados/Index', [
            'scans' => $scans,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'sort' => $sortField,
                'direction' => $sortDirection,
            ],
            'stats' => [
                'misEscaneos' => QrScan::where('user_id', $user->id)->count(),
                'totalSistema' => $isAdmin ? QrScan::count() : null,
                'gruposHoy' => ScanGroup::whereDate('created_at', today())
                    ->when(! $isAdmin, fn ($query) => $query->where('user_id', $user->id))
                    ->count(),
            ],
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * Guarda un escaneo individual (petición JSON desde el escáner).
     */
    public function store(StoreQrScanRequest $request): JsonResponse
    {
        $data = $request->validated();

        $existing = $this->findDuplicate($data['correo'] ?? null);

        if ($existing !== null) {
            return response()->json([
                'error' => 'Este usuario ya fue registrado previamente.',
                'existingScanId' => $existing->id,
                'bitrixEnabled' => Bitrix24Service::isEnabled(),
            ], 409);
        }

        $scan = DB::transaction(function () use ($data) {
            $scan = QrScan::create([
                ...collect($data)->except('marcas')->all(),
                'user_id' => Auth::id(),
            ]);

            $this->syncMarcas($scan, $data['marcas'] ?? []);

            return $scan;
        });

        $this->queueBitrixSync($scan);

        return response()->json([
            'message' => 'Escaneo guardado exitosamente.',
            'scan' => $scan->load('marcas:id,nombre'),
        ], 201);
    }

    /**
     * Guarda un grupo de escaneos de la misma empresa en una sola operación.
     */
    public function storeGroup(StoreScanGroupRequest $request): JsonResponse
    {
        $data = $request->validated();

        $duplicates = [];
        $pending = [];
        $seenEmails = [];

        foreach ($data['scans'] as $index => $scanData) {
            $correo = trim((string) ($scanData['correo'] ?? ''));

            if ($correo !== '' && in_array(mb_strtolower($correo), $seenEmails, true)) {
                $duplicates[] = ['index' => $index, 'correo' => $correo, 'motivo' => 'Repetido dentro del grupo.'];

                continue;
            }

            $existing = $this->findDuplicate($correo);

            if ($existing !== null) {
                $duplicates[] = [
                    'index' => $index,
                    'correo' => $correo,
                    'existingScanId' => $existing->id,
                    'motivo' => 'Ya estaba registrado en el sistema.',
                ];

                continue;
            }

            if ($correo !== '') {
                $seenEmails[] = mb_strtolower($correo);
            }

            $pending[] = $scanData;
        }

        $existingIds = array_values(array_filter(array_column($duplicates, 'existingScanId')));

        if ($pending === []) {
            return response()->json([
                'error' => 'Todos los contactos del grupo ya estaban registrados.',
                'duplicates' => $duplicates,
            ], 409);
        }

        [$group, $scans] = DB::transaction(function () use ($data, $pending) {
            $group = ScanGroup::create([
                'user_id' => Auth::id(),
                'empresa' => $data['empresa'] ?? ($pending[0]['empresa'] ?? null),
                'notas' => $data['notas'] ?? null,
            ]);

            $scans = [];

            foreach ($pending as $scanData) {
                $scan = QrScan::create([
                    ...collect($scanData)->except('marcas')->all(),
                    'empresa' => $scanData['empresa'] ?? $data['empresa'] ?? null,
                    'user_id' => Auth::id(),
                    'scan_group_id' => $group->id,
                ]);

                $this->syncMarcas($scan, $data['marcas'] ?? []);

                $scans[] = $scan;
            }

            return [$group, $scans];
        });

        // Un grupo genera UN SOLO deal con todos sus contactos (incluidos
        // los ya registrados, que se agregan con su información existente).
        if (Bitrix24Service::isEnabled()) {
            $group->forceFill(['bitrix_status' => QrScan::BITRIX_PENDING])->save();

            foreach ($scans as $scan) {
                $scan->forceFill(['bitrix_status' => QrScan::BITRIX_PENDING])->save();
            }

            SendScanGroupToBitrix::dispatchAfterResponse($group, $existingIds);
        }

        return response()->json([
            'message' => sprintf('Grupo guardado: %d contacto(s) registrados.', count($pending)),
            'groupId' => $group->id,
            'saved' => count($pending),
            'duplicates' => $duplicates,
        ], 201);
    }

    /**
     * Re-escaneo confirmado de un contacto ya registrado: actualiza su registro
     * con los datos nuevos (lo vacío conserva lo anterior), agrega las marcas
     * seleccionadas, cuenta el re-escaneo y crea un nuevo deal en Bitrix24.
     */
    public function rescan(StoreQrScanRequest $request, QrScan $scan): JsonResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($scan, $data) {
            $updates = [];

            foreach (['nombre', 'apellidos', 'puesto', 'empresa', 'estado', 'telefono', 'rol', 'correo'] as $field) {
                $value = trim((string) ($data[$field] ?? ''));

                if ($value !== '') {
                    $updates[$field] = $value;
                }
            }

            $nuevosCampos = array_values(array_filter(
                $data['campos_adicionales'] ?? [],
                fn ($linea) => trim((string) $linea) !== '',
            ));

            if ($nuevosCampos !== []) {
                $updates['campos_adicionales'] = array_values(array_unique([
                    ...($scan->campos_adicionales ?? []),
                    ...$nuevosCampos,
                ]));
            }

            $scan->forceFill([
                ...$updates,
                'rescan_count' => $scan->rescan_count + 1,
                'last_scanned_at' => now(),
            ])->save();

            foreach ($data['marcas'] ?? [] as $marca) {
                $comentario = $marca['comentarios'] ?? null;

                if ($scan->marcas()->where('marca_id', $marca['id'])->exists()) {
                    if ($comentario !== null && trim($comentario) !== '') {
                        $scan->marcas()->updateExistingPivot($marca['id'], ['comentarios' => $comentario]);
                    }
                } else {
                    $scan->marcas()->attach($marca['id'], ['comentarios' => $comentario]);
                }
            }
        });

        $bitrixEnabled = Bitrix24Service::isEnabled();

        if ($bitrixEnabled) {
            $scan->forceFill(['bitrix_status' => QrScan::BITRIX_PENDING])->save();

            SendScanToBitrix::dispatchAfterResponse($scan, true);
        }

        return response()->json([
            'message' => $bitrixEnabled
                ? 'Registro actualizado y nuevo deal enviado a Bitrix24.'
                : 'Registro actualizado.',
        ]);
    }

    /**
     * Crea un nuevo deal en Bitrix24 para un contacto ya capturado,
     * sin modificar sus datos (acción "Reenviar" de la tabla).
     */
    public function resendBitrix(QrScan $usuarios_capturado): JsonResponse
    {
        if (! Bitrix24Service::isEnabled()) {
            return response()->json([
                'error' => 'La integración con Bitrix24 está deshabilitada o sin configurar.',
            ], 422);
        }

        $usuarios_capturado->forceFill(['bitrix_status' => QrScan::BITRIX_PENDING])->save();

        SendScanToBitrix::dispatchAfterResponse($usuarios_capturado, true);

        return response()->json([
            'message' => 'Se está enviando un nuevo deal a Bitrix24 con la información del contacto.',
        ]);
    }

    public function edit(QrScan $usuarios_capturado): Response
    {
        $usuarios_capturado->load(['marcas:id,nombre', 'group:id,empresa', 'user:id,name,last_name']);

        return Inertia::render('admin/capturados/Edit', [
            'scan' => [
                ...$usuarios_capturado->toArray(),
                'marcas' => $usuarios_capturado->marcas->map(fn (Marca $marca) => [
                    'id' => $marca->id,
                    'comentarios' => $marca->pivot->comentarios,
                ]),
                'campos_adicionales' => implode("\n", array_filter($usuarios_capturado->campos_adicionales ?? [])),
            ],
            'marcas' => Marca::orderBy('nombre')->get(['id', 'nombre', 'descripcion', 'imagen']),
        ]);
    }

    public function update(UpdateQrScanRequest $request, QrScan $usuarios_capturado): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($usuarios_capturado, $data) {
            $usuarios_capturado->update([
                ...collect($data)->except(['marcas', 'campos_adicionales'])->all(),
                'campos_adicionales' => array_values(array_filter(
                    preg_split('/\r\n|\r|\n/', (string) ($data['campos_adicionales'] ?? '')),
                    fn (string $line) => trim($line) !== ''
                )),
            ]);

            $usuarios_capturado->marcas()->detach();
            $this->syncMarcas($usuarios_capturado, $data['marcas'] ?? []);
        });

        return redirect()
            ->route('admin.usuarios-capturados.index')
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function destroy(QrScan $usuarios_capturado): RedirectResponse
    {
        $usuarios_capturado->delete();

        return back()->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Elimina varios registros seleccionados.
     */
    public function destroyMany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:qr_scans,id'],
        ]);

        $query = QrScan::whereIn('id', $validated['ids']);

        if (! $request->user()->hasRole('Administrador')) {
            $query->where('user_id', $request->user()->id);
        }

        $deleted = $query->delete();

        return back()->with('success', "{$deleted} registro(s) eliminados correctamente.");
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new UserQrScanExport, 'usuarios_capturados.xlsx');
    }

    private function findDuplicate(?string $correo): ?QrScan
    {
        $correo = trim((string) $correo);

        if ($correo === '') {
            return null;
        }

        return QrScan::where('correo', $correo)->first();
    }

    /**
     * Envía el escaneo a Bitrix24 como deal justo después de responder
     * (sin worker de colas — el hosting es cPanel sin terminal).
     */
    private function queueBitrixSync(QrScan $scan): void
    {
        if (! Bitrix24Service::isEnabled()) {
            return;
        }

        $scan->forceFill(['bitrix_status' => QrScan::BITRIX_PENDING])->save();

        SendScanToBitrix::dispatchAfterResponse($scan);
    }

    /**
     * @param  array<int, array{id: int, comentarios?: string|null}>  $marcas
     */
    private function syncMarcas(QrScan $scan, array $marcas): void
    {
        foreach ($marcas as $marca) {
            $scan->marcas()->attach($marca['id'], ['comentarios' => $marca['comentarios'] ?? null]);
        }
    }
}
