<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Cache;
use Auth;

class BasesGruposExport implements FromView , ShouldAutoSize , WithEvents
{
    public function view(): View
    {
        $id = Auth::id();

		$datos = Cache::get('datos_grupos_bases'.$id);

		return view('export.export_grupos_bases', [
            'datos' => $datos
        ]);

    }

	public function registerEvents(): array
	{

		return [

			AfterSheet::class    => function(AfterSheet $event) {

				$styleBorde = array(
					'borders' => array(
						'allBorders' => array(
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							'color' => ['argb' => '000000'],
						)
					)
                );


				$event->sheet->getStyle(
					'A1:' .
					$event->sheet->getHighestColumn() .
					$event->sheet->getHighestRow()
				)->applyFromArray($styleBorde);

				$styleAlineamiento = array(
					'alignment' => array(
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					)
				);

				$event->sheet->getStyle(
					'A1:'.$event->sheet->getHighestColumn().'1'
				)->applyFromArray($styleAlineamiento)->getFont()->setBold(true)->setName('Calibri')->setSize(24);

            },
		];
	}
}
