<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreScanGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('scan');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'empresa' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:2000'],
            'scans' => ['required', 'array', 'min:1'],
            'scans.*.nombre' => ['required', 'string', 'max:255'],
            'scans.*.apellidos' => ['nullable', 'string', 'max:255'],
            'scans.*.puesto' => ['nullable', 'string', 'max:255'],
            'scans.*.empresa' => ['nullable', 'string', 'max:255'],
            'scans.*.estado' => ['nullable', 'string', 'max:255'],
            'scans.*.telefono' => ['nullable', 'string', 'max:255'],
            'scans.*.rol' => ['nullable', 'string', 'max:255'],
            'scans.*.correo' => ['nullable', 'string', 'max:255'],
            'scans.*.campos_adicionales' => ['nullable', 'array'],
            'scans.*.campos_adicionales.*' => ['nullable', 'string', 'max:1000'],
            'marcas' => ['nullable', 'array'],
            'marcas.*.id' => ['required', 'integer', 'exists:marcas,id'],
            'marcas.*.comentarios' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'scans.required' => 'No hay escaneos pendientes por guardar.',
            'scans.*.nombre.required' => 'Cada escaneo debe incluir al menos el nombre.',
        ];
    }
}
