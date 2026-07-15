<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQrScanRequest extends FormRequest
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
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['nullable', 'string', 'max:255'],
            'puesto' => ['nullable', 'string', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'rol' => ['nullable', 'string', 'max:255'],
            'correo' => ['nullable', 'string', 'max:255'],
            'campos_adicionales' => ['nullable', 'array'],
            'campos_adicionales.*' => ['nullable', 'string', 'max:1000'],
            'marcas' => ['nullable', 'array'],
            'marcas.*.id' => ['required', 'integer', 'exists:marcas,id'],
            'marcas.*.comentarios' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
