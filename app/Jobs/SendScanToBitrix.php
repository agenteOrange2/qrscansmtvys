<?php

namespace App\Jobs;

use App\Models\Marca;
use App\Models\QrScan;
use App\Models\Setting;
use App\Services\Bitrix24Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Empuja un escaneo QR a Bitrix24 como deal (contacto con dedupe + deal
 * en el pipeline configurado). Reintenta con backoff si el CRM falla;
 * el escaneo local nunca se pierde.
 */
class SendScanToBitrix implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [60, 300, 900];

    public bool $deleteWhenMissingModels = true;

    /**
     * @param  bool  $force  Re-escaneo: crea un nuevo deal aunque el contacto ya tenga uno.
     */
    public function __construct(public QrScan $scan, public bool $force = false) {}

    public function handle(): void
    {
        if (! Bitrix24Service::isEnabled()) {
            return;
        }

        $scan = $this->scan->fresh(['marcas', 'user', 'group']);

        if ($scan === null || (! $this->force && $scan->bitrix_deal_id !== null)) {
            return;
        }

        $previousDealId = $scan->bitrix_deal_id;

        $scan->forceFill(['bitrix_attempts' => $this->attempts()])->save();

        try {
            $bitrix = Bitrix24Service::fromSettings();

            $contactId = $bitrix->findOrCreateContact([
                'name' => $scan->nombre,
                'last_name' => $scan->apellidos,
                'email' => $scan->correo,
                'phone' => $scan->telefono,
                'company' => $scan->empresa,
            ]);

            $dealId = $bitrix->addDeal($this->dealFields($scan, $contactId, $previousDealId));

            $scan->forceFill([
                'bitrix_deal_id' => $dealId,
                'bitrix_contact_id' => $contactId,
                'bitrix_status' => QrScan::BITRIX_SENT,
                'bitrix_error' => null,
                'bitrix_synced_at' => now(),
            ])->save();
        } catch (Throwable $e) {
            $scan->forceFill([
                'bitrix_status' => QrScan::BITRIX_FAILED,
                'bitrix_error' => mb_substr($e->getMessage(), 0, 500),
            ])->save();

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        QrScan::whereKey($this->scan->getKey())->update([
            'bitrix_status' => QrScan::BITRIX_FAILED,
            'bitrix_error' => mb_substr($exception?->getMessage() ?? 'Error desconocido', 0, 500),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dealFields(QrScan $scan, int $contactId, ?int $previousDealId = null): array
    {
        $categoryId = (int) Setting::get('bitrix.category_id', '0');
        $stageId = trim((string) Setting::get('bitrix.stage_id', ''));

        // Bitrix espera etapas con prefijo de pipeline (p. ej. C13:NEW)
        if ($stageId !== '' && $categoryId > 0 && ! str_contains($stageId, ':')) {
            $stageId = "C{$categoryId}:{$stageId}";
        }

        $marcas = $scan->marcas->map(function (Marca $marca): string {
            $comentario = trim((string) $marca->pivot->comentarios);

            return '• '.$marca->nombre.($comentario !== '' ? " — {$comentario}" : '');
        })->all();

        $extras = array_values(array_filter(
            $scan->campos_adicionales ?? [],
            fn ($linea) => trim((string) $linea) !== '',
        ));

        $capturadoPor = $scan->user
            ? trim($scan->user->name.' '.($scan->user->last_name ?? ''))
            : '—';

        $lineas = array_values(array_filter([
            'Puesto: '.($scan->puesto ?: '—'),
            'Empresa: '.($scan->empresa ?: '—'),
            'Estado: '.($scan->estado ?: '—'),
            'Rol: '.($scan->rol ?: '—'),
            $marcas !== [] ? '' : null,
            $marcas !== [] ? 'Marcas de interés:' : null,
            ...$marcas,
            $extras !== [] ? '' : null,
            $extras !== [] ? 'Campos adicionales:' : null,
            ...array_map(fn ($linea) => '• '.$linea, $extras),
            '',
            $previousDealId !== null
                ? 'Re-escaneo de contacto ya registrado (deal anterior #'.$previousDealId.')'
                : null,
            'Capturado por: '.$capturadoPor,
            $scan->group
                ? 'Grupo #'.$scan->group->id.($scan->group->empresa ? " — {$scan->group->empresa}" : '')
                : null,
            'Escaneo QR #'.$scan->id.' — '.$scan->created_at->format('Y-m-d H:i'),
        ], fn ($linea) => $linea !== null));

        $nombreCompleto = trim($scan->nombre.' '.($scan->apellidos ?? ''));

        return array_filter([
            'TITLE' => 'Escaneo QR — '.$nombreCompleto.($scan->empresa ? " — {$scan->empresa}" : '')
                .($previousDealId !== null ? ' — Re-escaneo' : ''),
            'CONTACT_ID' => $contactId,
            'COMMENTS' => nl2br(e(implode("\n", $lineas))),
            'SOURCE_ID' => 'WEB',
            'SOURCE_DESCRIPTION' => 'App Escaneo QR SMTVYS',
            'CATEGORY_ID' => $categoryId,
            'STAGE_ID' => $stageId !== '' ? $stageId : null,
            'ASSIGNED_BY_ID' => Setting::get('bitrix.responsible_id') ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
