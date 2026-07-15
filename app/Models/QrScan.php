<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QrScan extends Model
{
    public const BITRIX_PENDING = 'pending';

    public const BITRIX_SENT = 'sent';

    public const BITRIX_FAILED = 'failed';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'scan_group_id',
        'nombre',
        'apellidos',
        'puesto',
        'empresa',
        'estado',
        'telefono',
        'rol',
        'correo',
        'campos_adicionales',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'campos_adicionales' => 'array',
            'bitrix_synced_at' => 'datetime',
            'last_scanned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ScanGroup::class, 'scan_group_id');
    }

    public function marcas(): BelongsToMany
    {
        return $this->belongsToMany(Marca::class, 'marca_qr_scan')
            ->withPivot('comentarios')
            ->withTimestamps();
    }
}
