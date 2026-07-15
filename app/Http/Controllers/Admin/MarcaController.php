<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MarcaRequest;
use App\Models\Marca;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MarcaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/marcas/Index', [
            'marcas' => Marca::withCount('qrScans')->orderBy('nombre')->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/marcas/Create');
    }

    public function store(MarcaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('marcas', 'public');
        }

        Marca::create($data);

        return redirect()
            ->route('admin.marcas.index')
            ->with('success', 'Marca creada correctamente.');
    }

    public function edit(Marca $marca): Response
    {
        return Inertia::render('admin/marcas/Edit', [
            'marca' => $marca,
        ]);
    }

    public function update(MarcaRequest $request, Marca $marca): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($marca->imagen) {
                Storage::disk('public')->delete($marca->imagen);
            }

            $data['imagen'] = $request->file('imagen')->store('marcas', 'public');
        } else {
            unset($data['imagen']);
        }

        $marca->update($data);

        return redirect()
            ->route('admin.marcas.index')
            ->with('success', 'Marca actualizada correctamente.');
    }

    public function destroy(Marca $marca): RedirectResponse
    {
        if ($marca->imagen) {
            Storage::disk('public')->delete($marca->imagen);
        }

        $marca->delete();

        return back()->with('success', 'Marca eliminada correctamente.');
    }
}
