<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use App\Models\QrScan;
use App\Models\ScanGroup;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('Administrador');

        $scoped = QrScan::query()->when(! $isAdmin, fn ($query) => $query->where('user_id', $user->id));

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'misEscaneos' => QrScan::where('user_id', $user->id)->count(),
                'totalSistema' => $isAdmin ? QrScan::count() : null,
                'escaneosHoy' => (clone $scoped)->whereDate('created_at', today())->count(),
                'grupos' => ScanGroup::when(! $isAdmin, fn ($query) => $query->where('user_id', $user->id))->count(),
                'empresas' => (clone $scoped)->whereNotNull('empresa')->where('empresa', '!=', '')->distinct()->count('empresa'),
                'marcas' => Marca::count(),
            ],
            'recentScans' => (clone $scoped)
                ->with('user:id,name,last_name')
                ->latest()
                ->take(8)
                ->get(['id', 'user_id', 'nombre', 'apellidos', 'empresa', 'scan_group_id', 'created_at']),
            'isAdmin' => $isAdmin,
        ]);
    }
}
