<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QrScan extends Model
{
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
