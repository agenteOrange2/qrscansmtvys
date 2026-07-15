<?php

namespace App\Exports;

use App\Models\Marca;
use App\Models\QrScan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserQrScanExport implements FromCollection, WithColumnFormatting, WithCustomStartCell, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    /**
     * @var Collection<int, Marca>
     */
    private Collection $marcas;

    public function __construct()
    {
        $this->marcas = Marca::orderBy('id')->get();
    }

    public function collection(): Collection
    {
        return QrScan::with(['marcas', 'group'])
            ->when(
                ! Auth::user()->hasRole('Administrador'),
                fn ($query) => $query->where('user_id', Auth::id())
            )
            ->get();
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        $headings = [
            'ID',
            'Capturado por (User ID)',
            'Grupo',
            'Nombre',
            'Apellido',
            'Puesto',
            'Empresa',
            'Estado',
            'Teléfono',
            'Rol en Expo',
            'Correo Electrónico',
            'Datos Adicionales',
            'Creado en',
            'Actualizado en',
        ];

        foreach ($this->marcas as $marca) {
            $headings[] = 'Marca: '.$marca->nombre;
            $headings[] = 'Comentario: '.$marca->nombre;
        }

        return $headings;
    }

    /**
     * @param  QrScan  $qrScan
     * @return array<int, mixed>
     */
    public function map($qrScan): array
    {
        $row = [
            $qrScan->id,
            $qrScan->user_id,
            $qrScan->scan_group_id ? 'Grupo #'.$qrScan->scan_group_id : '',
            $qrScan->nombre,
            $qrScan->apellidos,
            $qrScan->puesto,
            $qrScan->empresa,
            $qrScan->estado,
            $qrScan->telefono,
            $qrScan->rol,
            $qrScan->correo,
            implode(' | ', array_filter($qrScan->campos_adicionales ?? [])),
            Date::dateTimeToExcel($qrScan->created_at),
            Date::dateTimeToExcel($qrScan->updated_at),
        ];

        $seleccionadas = $qrScan->marcas->pluck('pivot.comentarios', 'id');

        foreach ($this->marcas as $marca) {
            if ($seleccionadas->has($marca->id)) {
                $row[] = 'Sí';
                $row[] = $seleccionadas->get($marca->id);
            } else {
                $row[] = 'No';
                $row[] = '';
            }
        }

        return $row;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function columnFormats(): array
    {
        return [
            'M' => 'dd/mm/yyyy hh:mm',
            'N' => 'dd/mm/yyyy hh:mm',
        ];
    }
}
