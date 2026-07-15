<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Marca extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
    ];

    public function qrScans(): BelongsToMany
    {
        return $this->belongsToMany(QrScan::class, 'marca_qr_scan')
            ->withPivot('comentarios')
            ->withTimestamps();
    }
}
