<?php

namespace App\Exports;

use DB;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

//use Illuminate\Http\Request;
use Config;
//use App\Exports\ExcelExport;
//use Maatwebsite\Excel\Facades\Excel;

use Session;

use ReflectionClass;


class ExcelExport implements FromView , ShouldAutoSize , WithEvents
{
    public function __construct($request)
    {
        $this->request = $request;
    }

	public function conexion($request, $database = NULL, $schema = NULL, $charset_def = NULL)
	{
		//Guardo el host, usuario y contraseña definidos en el form_host para hacer la conexión, en variables de sesión; mientras dure la sesión y no se modifiquen, la conexión siempre se va a realizar con estos valores (agregado el if para los casos de querer volver a seleccionar la database sin salirse de la conexión)

		
		if($request->session()->get('db_usuario') === NULL && $request->session()->get('db_host') === NULL){

			$base = Base::find($request->db_host);

			$request->session()->put('db_host',$base->host);

			$request->session()->put('db_usuario',$base->usuario);

			$request->session()->put('db_contrasenia',$base->password);

		}

		//Traigo los valores de la conexión para manejarlos como variantes directamente (menos la contraseña)
		$db_usuario = $request->session()->get('db_usuario');

		$db_host = $request->session()->get('db_host');

		//Si no se pasan los valores de schema database o charset_def los seteo a valores por defecto
		if(!isset($database)){
			$database = 'postgres';
		}

		if(!isset($schema)){
			$schema = 'public';
		}

		if(!isset($charset_def)){
			$charset_def = 'utf8';
		}

		//Genero el modelo de la conexión pgsql_variable con los valores definidos, y realizo la conexión
		Config::set('database.connections.pgsql_variable', array(
			'driver'    => 'pgsql',
			'host'      => $db_host,
			'database'  => $database,
			'username'  => $db_usuario,
			'password'  => $request->session()->get('db_contrasenia'),
			'charset'   => $charset_def,
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
			'schema'    => $schema,
		));

		$conexion = DB::connection('pgsql_variable');

		return $conexion;
	}
    
	public function view(): View
    {
		
		ini_set('memory_limit', -1);
		
		ini_set('max_execution_time','1000');
			
		$database = $this->request->database;

		$schema = $this->request->schema;

		$db_usuario = $this->request->session()->get('db_usuario');

		$db_host = $this->request->session()->get('db_host');

		$charset_def = $this->request->session()->get('charset_def');

		$conexion = $this->conexion($request, $database, $schema, $charset_def);

		$function = 'f_limpiar_acentos_'.$db_usuario.'_'.$database.'_'.$schema;

		if($charset_def != 'UTF8'){

			$originales = utf8_decode('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ');

			$modificadas = utf8_decode('aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr');

		}else{

			$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';

			$modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';

		}

		if(isset($this->request->where1)){

			if($this->request->comparador1 === 'ilike'){

				$conexion->unprepared("CREATE OR REPLACE FUNCTION ".$function."(text) RETURNS text AS \$BODY$ SELECT translate($1,'".$originales."','".$modificadas."'); \$BODY$ LANGUAGE sql IMMUTABLE STRICT COST 100");

			}

		}

		$tabla_selected = $this->request->tabla_selected;

		$registros = $conexion->table($tabla_selected);

		$comparador1 = NULL;

		$columna_selected1 = NULL;

		$where1 = NULL;
		
		$comparador2 = NULL;

		$columna_selected2 = NULL;

		$where2 = NULL;

		$sql="select column_name
					,is_nullable as required
					,character_maximum_length as max_char
					,data_type as type
					,data_type||coalesce('('||character_maximum_length::text||')','') as data_type
				from INFORMATION_SCHEMA.columns col 
			   where table_name = '".$tabla_selected."'
				 and table_schema = '".$schema."'
			order by col.ordinal_position";

		$columnas = $conexion->select($sql);

		$col_num = 1;

		$col_array = array();

		foreach($columnas as $columna){

			if(isset($this->request->ordercol)){


				if($col_num == $this->request->ordercol){

					$col_num = $col_num + 1;

				}else{

					$col_array[] = $col_num++;

				}
			}else{

				$col_array[] = $col_num++;

			}

		}

		$sort = 'asc';

		if(isset($this->request->sort)) $sort = $this->request->sort;

		if(isset($this->request->ordercol)){

			$col_string = $this->request->ordercol.' '.$sort.','.implode(",",$col_array);

		}else{

			$col_string = implode(",",$col_array);

		}

		if(isset($this->request->where1)){

			$comparador1 = $this->request->comparador1;

			$columna_selected1 = $this->request->columna_selected1;

			$where1 = $this->request->where1;

			$busqueda = str_replace("´`'çÇ¨",'_',$where1);

			if($comparador1 === 'ilike'){

				$registros = $registros->whereRaw($function."($columna_selected1::text) ilike ".$function."('%".$busqueda."%')");

			}else{

				$registros = $registros->where($columna_selected1,$comparador1,$busqueda);

			}

		}
		
		if(isset($this->request->where2)){

			$comparador2 = $this->request->comparador2;

			$columna_selected2 = $this->request->columna_selected2;

			$where2 = $this->request->where2;

			$busqueda2 = str_replace("´`'çÇ¨",'_',$where2);

			if($comparador2 === 'ilike'){

				$registros = $registros->whereRaw($function."($columna_selected2::text) ilike ".$function."('%".$busqueda2."%')");

			}else{

				$registros = $registros->where($columna_selected2,$comparador2,$busqueda2);

			}

		}

		$registros = $registros->orderBy(DB::raw($col_string))->get();

		if(isset($this->request->where1)){

			if($this->request->comparador1 === 'ilike'){

				$conexion->unprepared('DROP FUNCTION '.$function.'(text)');

			}

		}

		$date = date('dmYGis');

		return view('export.export_excel', ['db_host' => $db_host
										   ,'db_usuario' => $db_usuario
										   ,'database' => $database
										   ,'tabla_selected' => $tabla_selected
										   ,'columna_selected1' => $columna_selected1
										   ,'comparador1' => $comparador1
										   ,'where1' => $where1
										   ,'columna_selected2' => $columna_selected2
										   ,'comparador2' => $comparador2
										   ,'where2' => $where2
										   ,'registros' => $registros
										   ,'columnas' => $columnas
										   ,'charset_def' => $charset_def]);
		
    }
	
	public function registerEvents(): array
	{
		
		return [
			
			AfterSheet::class    => function(AfterSheet $event) {
				
				$styleRegistros = array(
					'borders' => array(
						'allBorders' => array(
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							'color' => ['argb' => '000000'],
						)
					)
				);
				
				$styleBotones = array(
					'borders' => array(
						'allBorders' => array(
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
							'color' => ['argb' => '000000'],
						)
					),
					'alignment' => array(
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					),
				);
				
				$font_size = 14;
				
				$event->sheet->getStyle(
					'A1:B4'
				)->applyFromArray($styleBotones)->getFont()->setBold(true)->setName('Calibri')->setSize($font_size);
				
				if(isset($this->request->where1)){
					
					$primera_celda_registros = 'A10:';
					
					if(isset($this->request->where2)){
						
						$ultima_celda_columnas = 'D8';
						
					}else{
						
						$ultima_celda_columnas = 'B8';
						
					}
					
					$event->sheet->getStyle(
						'A6:'.$ultima_celda_columnas
					)->applyFromArray($styleBotones)->getFont()->setBold(true)->setName('Calibri')->setSize($font_size);
					
				}else{
					
					$primera_celda_registros = 'A6:';
					
				}
				
				$event->sheet->getStyle(
					$primera_celda_registros . 
					$event->sheet->getHighestColumn() . 
					$event->sheet->getHighestRow()
				)->applyFromArray($styleRegistros);
				
            },
		];
	}
}
