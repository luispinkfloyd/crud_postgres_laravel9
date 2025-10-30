<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB; //se usa en la línea 746 en el método tabla
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use ReflectionClass;
use Cache;
use App\Models\Base; //se usa en el método create
use App\Models\Grupo;
use Illuminate\Support\Str;
use App\Traits\paginacionTrait;
use App\Traits\idUserTrait;
use App\Traits\conexionTrait;
use App\Traits\variosTrait;
use App\Traits\verfificarVPNTrait;

class RegistrosController extends Controller
{
    use paginacionTrait;
	use idUserTrait;
	use conexionTrait;
	use variosTrait;
	use verfificarVPNTrait;

    public function tabla(Request $request)
    {

		try
		{

			if(empty($request->tabla_selected) || $request->tabla_selected == NULL){ return back()->with('mensaje_error','Selección de tabla vacía');}



			if($request->session()->get('db_usuario') !== NULL && $request->session()->get('db_host') !== NULL){

				ini_set('memory_limit', -1);
				set_time_limit(500);

				if(isset($request->limpiar)){

					$this->cache_forget('tabla_selected'.$this->id());

				}

				$selector_table_function_selected = 'table';

				$database = $request->database;
				$this->cache_put('database'.$this->id(),$database);
				$request->session()->forget('database');

				$schema = $request->schema;
				$this->cache_put('schema'.$this->id(),$schema);
				$request->session()->forget('schema');

				$charset_def = $request->session()->get('charset_def');

				$conexion = $this->conexion($request, $database, $schema, $charset_def);

				$db_usuario = $request->session()->get('db_usuario');
				$db_host = $request->session()->get('db_host');

                $time_cache = 3600; //60 minutos

				if($request->selector_table_function == 'function'){

					$sql_funciones_como_tablas = "  SELECT      proname as table_name
													FROM        pg_proc
													WHERE       pronamespace = (    SELECT  pg_namespace.oid
																			        FROM    pg_namespace
																		            WHERE   nspname = '".$schema."')
													ORDER BY    proname;";

					$tablas = $conexion->select($sql_funciones_como_tablas);
					$this->cache_put('tablas'.$this->id(), $tablas, $time_cache);

					$tabla_selected = $request->tabla_selected;
					$this->cache_put('tabla_selected'.$this->id(),$tabla_selected,$time_cache);

					$sql_funcion_con_argumentos = " SELECT  pg_proc.prosrc,
														    --unnest(string_to_array((oidvectortypes(proargtypes)), ',')) as arguments_type,
														    pg_proc.oid as oid
													FROM    pg_proc
													WHERE   pronamespace = (SELECT  pg_namespace.oid
																		    FROM    pg_namespace
																		    WHERE   nspname = '".$schema."')
													AND     proname = '$tabla_selected'";

					$selector_table_function_selected = 'function';

					$funcion_con_argumentos = $conexion->select($sql_funcion_con_argumentos);

					$funcion_a_buscar = $funcion_con_argumentos[0]->oid;

					$sql_funcion = "SELECT replace(pg_get_functiondef($funcion_a_buscar), '\$function$', '\$BODY$') as funcion;";

					$funcion = $conexion->select($sql_funcion);

					return view('home',['database' => $database,
										'schema' => $schema,
										'tablas' => $tablas,
										'tabla_selected' => $tabla_selected,
										'funcion' => $funcion[0]->funcion,
										'db_usuario' => $db_usuario,
										'db_host' => $db_host,
										'charset_def' => $charset_def,
									    'selector_table_function_selected' => $selector_table_function_selected]);

				}else{

					if(Cache::get('tabla_selected'.$this->id()) != $request->tabla_selected){

						$this->forget_table_selected();

					}

					if(Cache::get('tabla_selected'.$this->id()) != $request->tabla_selected
					|| Cache::get('columna_selected1'.$this->id()) != $request->columna_selected1
					|| Cache::get('comparador1'.$this->id()) != $request->comparador1
					|| Cache::get('where1'.$this->id()) != $request->where1
					|| Cache::get('columna_selected2'.$this->id()) != $request->columna_selected2
					|| Cache::get('comparador2'.$this->id()) != $request->comparador2
					|| Cache::get('where2'.$this->id()) != $request->where2
					|| Cache::get('ordercol'.$this->id()) != $request->ordercol
					|| Cache::get('sort'.$this->id()) != $request->sort
					|| Cache::get('database'.$this->id()) != $request->database){

						if(isset($request->where1) && isset($request->caracteres_raros)){

							$this->cache_put('caracteres_raros'.$this->id(), $request->caracteres_raros, $time_cache);
							$function = 'public.f_limpiar_acentos_'.$db_usuario.'_'.$database.'_'.$schema;

						}else{

							$function = '';

						}

						if($charset_def != 'UTF8'){

							$originales = utf8_decode('ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ');
							$modificadas = utf8_decode('aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr');

						}else{

							$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
							$modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';

						}

						if(isset($request->where1) && isset($request->caracteres_raros)){

							if($request->comparador1 === 'ilike' && $function !== ''){

								$conexion->unprepared("CREATE OR REPLACE FUNCTION ".$function."(text) RETURNS text AS \$BODY$ SELECT translate($1,'".$originales."','".$modificadas."'); \$BODY$ LANGUAGE sql IMMUTABLE STRICT COST 100");

							}

						}

						$sql="SELECT table_name
										from information_schema.tables
									where table_schema = '".$schema."'
									order by table_name;";

						$tablas = $conexion->select($sql);
						$this->cache_put('tablas'.$this->id(), $tablas, $time_cache);

						$tabla_selected = $request->tabla_selected;
						$this->cache_put('tabla_selected'.$this->id(), $tabla_selected, $time_cache);

						$registros = $conexion->table($tabla_selected);

						$comparador1 = NULL;
						$columna_selected1 = NULL;
						$where1 = NULL;
						$comparador2 = NULL;
						$columna_selected2 = NULL;
						$where2 = NULL;

						$sql="  SELECT      --col.*,
                                            col.column_name,
                                            col.is_nullable as required,
                                            col.character_maximum_length as max_char,
                                            col.data_type as type,
                                            col.column_default as default,
                                            col.data_type||coalesce('('||col.character_maximum_length::text||')','') as data_type,
                                            CASE WHEN 
                                            (SELECT true FROM information_schema.table_constraints AS tc
                                            JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
                                            AND tc.table_schema = kcu.table_schema
                                            AND tc.table_name = kcu.table_name
                                            WHERE tc.constraint_type = 'PRIMARY KEY'
                                            AND kcu.table_schema = col.table_schema
                                            AND kcu.table_name = col.table_name
                                            AND kcu.column_name = col.column_name)
                                            THEN true ELSE false END as primary_key,
                                            CASE WHEN 
                                            (SELECT true FROM information_schema.table_constraints AS tc
                                            JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
                                            AND tc.table_schema = kcu.table_schema
                                            AND tc.table_name = kcu.table_name
                                            WHERE tc.constraint_type = 'FOREIGN KEY'
                                            AND kcu.table_schema = col.table_schema
                                            AND kcu.table_name = col.table_name
                                            AND kcu.column_name = col.column_name)
                                            THEN true ELSE false END as foreign_key,
                                            CASE WHEN col.column_default ILIKE 'nextval%regclass)' THEN true ELSE false END as default_serial
								FROM        INFORMATION_SCHEMA.columns col
							    WHERE       table_name = '".$tabla_selected."'
								AND         table_schema = '".$schema."'
							    ORDER BY    col.ordinal_position;";

						$columnas = $conexion->select($sql);

						$this->cache_put('columnas'.$this->id(),$columnas,$time_cache);

						$col_num = 1;
						$col_array = array();

						foreach($columnas as $columna){
							if(isset($request->ordercol)){ 
                                if($col_num == $request->ordercol){ $col_num = $col_num + 1; }else{ $col_array[] = $col_num++; }
							}else{ 
                                $col_array[] = $col_num++; 
                            }
						}

						$sort = 'asc';
						if(isset($request->sort)){ $sort = $request->sort; $this->cache_put('sort'.$this->id(), $sort, $time_cache); }

						if(isset($request->ordercol)){
							$ordercol = $request->ordercol;
							$col_string = $request->ordercol.' '.$sort.','.implode(",",$col_array);
						}else{
							$ordercol = NULL;
							$col_string = implode(",",$col_array);
						}

						$this->cache_put('ordercol'.$this->id(), $request->ordercol, $time_cache);

						if(isset($request->columna_selected1)){

							$this->cache_put('where1'.$this->id(), $request->where1, $time_cache);

							$comparador1 = $request->comparador1;
							$this->cache_put('comparador1'.$this->id(), $comparador1, $time_cache);

							$columna_selected1 = $request->columna_selected1;
							$this->cache_put('columna_selected1'.$this->id(),$columna_selected1,$time_cache);

							if($charset_def != 'UTF8'){
								$where1 = utf8_decode($request->where1);
							}else{
								$where1 = $request->where1;
							}

							$busqueda = str_replace("`'çÇ¨",'_',$where1);

							if($comparador1 === 'ilike'){
								$registros = $registros->whereRaw($function."($columna_selected1::text) ilike ".$function."('%".$busqueda."%')");
							}elseif($comparador1 === 'is_null'){
								$registros = $registros->whereRaw("$columna_selected1 is null");
							}elseif($comparador1 === 'not_null'){
								$registros = $registros->whereRaw("$columna_selected1 is not null");
							}else{
								$registros = $registros->where($columna_selected1,$comparador1,$busqueda);
							}

						}

						if(isset($request->columna_selected2)){

							$this->cache_put('where2'.$this->id(), $request->where2, $time_cache);

							$comparador2 = $request->comparador2;
							$this->cache_put('comparador2'.$this->id(), $comparador2, $time_cache);

							$columna_selected2 = $request->columna_selected2;
							$this->cache_put('columna_selected2'.$this->id(), $columna_selected2, $time_cache);

							if($charset_def != 'UTF8'){
								$where2 = utf8_decode($request->where2);
							}else{
								$where2 = $request->where2;
							}

							$busqueda2 = str_replace("`'çÇ¨",'_',$where2);

							if($comparador2 === 'ilike'){
								$registros = $registros->whereRaw($function."($columna_selected2::text) ilike ".$function."('%".$busqueda2."%')");
							}elseif($comparador2 === 'is_null'){
								$registros = $registros->whereRaw("$columna_selected2 is null");
							}elseif($comparador2 === 'not_null'){
								$registros = $registros->whereRaw("$columna_selected2 is not null");
							}else{
								$registros = $registros->where($columna_selected2,$comparador2,$busqueda2);
							}

						}

						$caracteres_raros = NULL;

						if($charset_def != 'UTF8'){
							$where1 = utf8_encode($where1);
							if(isset($request->where2)){
								$where2 = utf8_encode($where2);
							}
						}

						$count_registros = count($registros->get());

						if($comparador1 !== 'ilike'){
							$consulta_de_registros = $this->getSqlWithBindings($registros);
							$consulta_de_registros = str_replace('"','',$consulta_de_registros).';';
						}else{
							$consulta_de_registros = Str::replaceArray('?', $registros->getBindings(), $registros->toSql());
							//str_replace_array('?', $registros->getBindings(), $registros->toSql()); -- str_replace_array Deprecated
							$consulta_de_registros = str_replace('"','',$consulta_de_registros).';';

							if($schema != 'public'){
								$consulta_de_registros = str_replace($tabla_selected,$schema.'.'.$tabla_selected,$consulta_de_registros);
							}
						}

						if(isset($comparador2)){
							if($comparador1 !== 'ilike' || $comparador2 !== 'ilike'){
								$consulta_de_registros = $this->getSqlWithBindings($registros);
								$consulta_de_registros = str_replace('"','',$consulta_de_registros).';';
							}else{
								$consulta_de_registros = Str::replaceArray('?', $registros->getBindings(), $registros->toSql());
								//str_replace_array('?', $registros->getBindings(), $registros->toSql()); -- str_replace_array Deprecated
								$consulta_de_registros = str_replace('"','',$consulta_de_registros).';';
	
								if($schema != 'public'){
									$consulta_de_registros = str_replace($tabla_selected,$schema.'.'.$tabla_selected,$consulta_de_registros);
								}
							}
						}

						if($schema != 'public'){
							$consulta_de_registros = str_replace($tabla_selected,$schema.'.'.$tabla_selected,$consulta_de_registros);
						}

						$registros = $registros->orderBy(DB::raw($col_string))->get()->toArray();

						if(isset($request->where1) && isset($request->caracteres_raros)){

							if($request->comparador1 === 'ilike'){

								$conexion->unprepared('DROP FUNCTION '.$function.'(text)');

							}

							$caracteres_raros = 'S';

						}

						Cache::forget('registros'.$this->id());

						Cache::put('registros'.$this->id(),$registros,$time_cache);

						Cache::forget('consulta_de_registros'.$this->id());

						Cache::put('consulta_de_registros'.$this->id(),$consulta_de_registros,$time_cache);

						Cache::forget('count_registros'.$this->id());

						Cache::put('count_registros'.$this->id(),$count_registros,$time_cache);

					}else{

						$db_usuario = $request->session()->get('db_usuario');

						$db_host = $request->session()->get('db_host');

						$charset_def = $request->session()->get('charset_def');

						$database = Cache::get('database'.$this->id());

						$schema = Cache::get('schema'.$this->id());

						$where1 = Cache::get('where1'.$this->id());

						$where2 = Cache::get('where2'.$this->id());

						$caracteres_raros = Cache::get('caracteres_raros'.$this->id());

						$tablas = Cache::get('tablas'.$this->id());

						$tabla_selected = Cache::get('tabla_selected'.$this->id());

						$comparador1 = Cache::get('comparador1'.$this->id());

						$columna_selected1 = Cache::get('columna_selected1'.$this->id());

						$comparador2 = Cache::get('comparador2'.$this->id());

						$columna_selected2 = Cache::get('columna_selected2'.$this->id());

						$sort = Cache::get('sort').$this->id();

						$ordercol = Cache::get('ordercol'.$this->id());

						$columnas = Cache::get('columnas'.$this->id());

						$registros = Cache::get('registros'.$this->id());

						$count_registros = Cache::get('count_registros'.$this->id());

						$consulta_de_registros = Cache::get('consulta_de_registros'.$this->id());

					}

					/*-----------------------------------------------------*/

					$perPage = 8;
					$registros = $this->paginacion($registros,$request,$perPage);

					/*--------------- Acá termina el if del cache -----------------*/

					return view('home',['database' => $database,
										'schema' => $schema,
										'tablas' => $tablas,
										'tabla_selected' => $tabla_selected,
										'registros' => $registros,
										'columnas' => $columnas,
										'db_usuario' => $db_usuario,
										'db_host' => $db_host,
										'comparador1' => $comparador1,
										'columna_selected1' => $columna_selected1,
										'where1' => $where1,
										'comparador2' => $comparador2,
										'columna_selected2' => $columna_selected2,
										'where2' => $where2,
										'charset_def' => $charset_def,
										'count_registros' => $count_registros,
										'sort' => $sort,
										'ordercol_def' => $ordercol,
										'caracteres_raros' => $caracteres_raros,
										'consulta_de_registros' => $consulta_de_registros,
									    'selector_table_function_selected' => $selector_table_function_selected]);
				}

			}else{

				return redirect('home');

			}

		}catch (\Exception $e) {

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}

    }

	public function export_excel(Request $request)
	{
		try

		{
			ini_set('memory_limit', -1);

			$tabla_selected = $request->tabla_selected;

			$date = date('dmYGis');

			return Excel::download(new ExcelExport($request), 'registros_'.$tabla_selected.'_'.$date.'.xlsx');

		}
		catch (\Exception $e)
		{

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}

	}

	public function store(Request $request)
    {

		try
		{

			$database = $request->database;

			$schema = $request->schema;

			$db_usuario = $request->session()->get('db_usuario');

			$db_host = $request->session()->get('db_host');

			$charset_def = $request->session()->get('charset_def');

			$conexion = $this->conexion($request, $database, $schema, $charset_def);

			$tabla_selected = $request->tabla_selected;

			$sql="SELECT --col.*,
						 col.column_name
						,col.is_nullable as required
						,col.character_maximum_length as max_char
						,col.data_type as type
						,col.column_default as default
						,col.data_type||coalesce('('||col.character_maximum_length::text||')','') as data_type
						,CASE WHEN 
						(SELECT true FROM information_schema.table_constraints AS tc
    					 JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
						 AND tc.table_schema = kcu.table_schema
						 AND tc.table_name = kcu.table_name
    					 WHERE tc.constraint_type = 'PRIMARY KEY'
						 AND kcu.table_schema = col.table_schema
						 AND kcu.table_name = col.table_name
						 AND kcu.column_name = col.column_name)
						THEN true ELSE false END as primary_key
						,CASE WHEN 
						(SELECT true FROM information_schema.table_constraints AS tc
    					 JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
						 AND tc.table_schema = kcu.table_schema
						 AND tc.table_name = kcu.table_name
    					 WHERE tc.constraint_type = 'FOREIGN KEY'
						 AND kcu.table_schema = col.table_schema
						 AND kcu.table_name = col.table_name
						 AND kcu.column_name = col.column_name)
						THEN true ELSE false END as foreign_key
						,CASE WHEN col.column_default ILIKE 'nextval%regclass)' THEN true ELSE false END as default_serial
					from INFORMATION_SCHEMA.columns col
				   where table_name = '".$tabla_selected."'
					 and table_schema = '".$schema."'
				order by col.ordinal_position;";

			$columnas = $conexion->select($sql);

			$insert = '';

			$columnas_registro = '';

			foreach($columnas as $columna){

				$columnas_registro = $columnas_registro.$columna->column_name.',';

				$columna_registro = $columna->column_name;

				if($columna->default_serial === true && $columna->primary_key === true){

					$insert = $insert.'default,';

				}else{

					if($request->$columna_registro === NULL){

						$insert = $insert.'NULL,';

					}else{

						if($columna->type === 'timestamp without time zone'){

							$timestamp_without_time_zone = date('Y-m-d H:i:s', strtotime($request->$columna_registro));

							$insert = $insert."'".$timestamp_without_time_zone."',";

						}else{

							if($charset_def !== 'UTF8'){

								$insert = $insert."'".utf8_decode($request->$columna_registro)."',";

							}else{

								$insert = $insert."'".$request->$columna_registro."',";

							}

						}

					}

				}

			}

			$columnas_registro = trim($columnas_registro, ',');

			$insert = trim($insert, ',');

			$conexion->insert('insert into '.$tabla_selected.' ('.$columnas_registro.') values ('.$insert.');');

			Cache::forget('tabla_selected'.$this->id());

			return back()->withInput()->with('registro_agregado', 'El registro se agregó correctamente');

		}
		catch (\Exception $e) {

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}

    }

	public function destroy($id,Request $request)
	{

		try
		{
			$database = $request->database;

			$schema = $request->schema;

			$db_usuario = $request->session()->get('db_usuario');

			$db_host = $request->session()->get('db_host');

			$charset_def = $request->session()->get('charset_def');

			$conexion = $this->conexion($request, $database, $schema, $charset_def);

			$tabla_selected = $request->tabla_selected;

			$primera_columna = $request->primera_columna;

			$sql_valores_repetidos_primera_columna = "SELECT $primera_columna
												  FROM $tabla_selected
												  GROUP BY $primera_columna
												  HAVING (COUNT($primera_columna) > 1)";


			$valores_repetidos_primera_columna = $conexion->select($sql_valores_repetidos_primera_columna);

			Cache::forget('tabla_selected'.$this->id());

			if(count($valores_repetidos_primera_columna) === 0){

				$conexion->delete('delete from '.$tabla_selected.' where '.$primera_columna."::text = '".$id."';");

				return back()->withInput()->with('registro_eliminado', 'El registro se eliminó correctamente');

			}else{

				$sql="SELECT --col.*,
						 col.column_name
						,col.is_nullable as required
						,col.character_maximum_length as max_char
						,col.data_type as type
						,col.column_default as default
						,col.data_type||coalesce('('||col.character_maximum_length::text||')','') as data_type
						,CASE WHEN 
						(SELECT true FROM information_schema.table_constraints AS tc
    					 JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
						 AND tc.table_schema = kcu.table_schema
						 AND tc.table_name = kcu.table_name
    					 WHERE tc.constraint_type = 'PRIMARY KEY'
						 AND kcu.table_schema = col.table_schema
						 AND kcu.table_name = col.table_name
						 AND kcu.column_name = col.column_name)
						THEN true ELSE false END as primary_key
						,CASE WHEN 
						(SELECT true FROM information_schema.table_constraints AS tc
    					 JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
						 AND tc.table_schema = kcu.table_schema
						 AND tc.table_name = kcu.table_name
    					 WHERE tc.constraint_type = 'FOREIGN KEY'
						 AND kcu.table_schema = col.table_schema
						 AND kcu.table_name = col.table_name
						 AND kcu.column_name = col.column_name)
						THEN true ELSE false END as foreign_key
						,CASE WHEN col.column_default ILIKE 'nextval%regclass)' THEN true ELSE false END as default_serial
					from INFORMATION_SCHEMA.columns col
				   where table_name = '".$tabla_selected."'
					 and table_schema = '".$schema."'
				order by col.ordinal_position;";

				$columnas = $conexion->select($sql);

				foreach($columnas as $columna){
					$columnas_array[] = $columna->column_name;
				}

				$columnas_string = implode(", ", $columnas_array);

				$sql_valores_repetidos_toda_la_fila = "SELECT $columnas_string
												  FROM $tabla_selected
												  GROUP BY $columnas_string
												  HAVING (COUNT(*) > 1)";

				$valores_repetidos_toda_la_fila = $conexion->select($sql_valores_repetidos_toda_la_fila);

				if(count($valores_repetidos_toda_la_fila) === 0){

					$where_value = json_decode($request->where_val, true);
					//print_r($where_value);exit; //Array ( [0] => Array ( [columna] => campo_x [valor] => 1 ) [1] => Array ( [columna] => campo_y [valor] => valor_a ) [2] => Array ( [columna] => campo_z [valor] => 2024-01-01 ) )

					$where = '';
					
					foreach($where_value as $key => $value){
						$columna = $value['columna'];
						$valor = $value['valor'];
						$where = $where.' AND '.$columna."::text = '".$valor."'";
						//$request->session()->put('where_delete_'.$key, ['columna' => $columna, 'valor' => $valor]);
					}

					$delete = 'delete from '.$tabla_selected.' where '.$primera_columna."::text = '".$id."'".$where.';';

					//echo $delete; exit;

					$conexion->delete($delete);

					return back()->withInput()->with('registro_eliminado', 'El registro se eliminó correctamente');

				}

				return back()->withInput()->with('registro_no_modificado', 'No se puede borrar el registro de la tabla '.$tabla_selected.' porque hay valores repetidos.');

			}

		}
		catch (\Exception $e) {

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}
    }

	public function edit($id,Request $request)
	{

		try
		{

			$database = $request->database;

			$schema = $request->schema;

			$db_usuario = $request->session()->get('db_usuario');

			$db_host = $request->session()->get('db_host');

			$charset_def = $request->session()->get('charset_def');

			$conexion = $this->conexion($request, $database, $schema, $charset_def);

			$tabla_selected = $request->tabla_selected;

			$sql="SELECT --col.*,
						 col.column_name
						,col.is_nullable as required
						,col.character_maximum_length as max_char
						,col.data_type as type
						,col.column_default as default
						,col.data_type||coalesce('('||col.character_maximum_length::text||')','') as data_type
						,CASE WHEN 
						(SELECT true FROM information_schema.table_constraints AS tc
						JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
						AND tc.table_schema = kcu.table_schema
						AND tc.table_name = kcu.table_name
						WHERE tc.constraint_type = 'PRIMARY KEY'
						AND kcu.table_schema = col.table_schema
						AND kcu.table_name = col.table_name
						AND kcu.column_name = col.column_name)
						THEN true ELSE false END as primary_key
						,CASE WHEN 
						(SELECT true FROM information_schema.table_constraints AS tc
						JOIN information_schema.key_column_usage AS kcu  ON tc.constraint_name = kcu.constraint_name
						AND tc.table_schema = kcu.table_schema
						AND tc.table_name = kcu.table_name
						WHERE tc.constraint_type = 'FOREIGN KEY'
						AND kcu.table_schema = col.table_schema
						AND kcu.table_name = col.table_name
						AND kcu.column_name = col.column_name)
						THEN true ELSE false END as foreign_key
						,CASE WHEN col.column_default ILIKE 'nextval%regclass)' THEN true ELSE false END as default_serial
					from INFORMATION_SCHEMA.columns col
				where table_name = '".$tabla_selected."'
					and table_schema = '".$schema."'
				order by col.ordinal_position;";

			$columnas = $conexion->select($sql);

			foreach($columnas as $columna){

				$primera_columna = $columna->column_name;

				break;

			}

			$sql_valores_repetidos_primera_columna = "SELECT $primera_columna
												  FROM $tabla_selected
												  GROUP BY $primera_columna
												  HAVING (COUNT($primera_columna) > 1)";


			$valores_repetidos_primera_columna = $conexion->select($sql_valores_repetidos_primera_columna);

			if(count($valores_repetidos_primera_columna) === 0){

				$count_modificaciones = 0;


				foreach($columnas as $columna){

					$columna_registro = $columna->column_name;

					if($columna_registro != $primera_columna){

						if($request->$columna_registro === NULL){

							$update = 'NULL';

						}else{

							if($columna->type === 'timestamp without time zone'){

								$timestamp_without_time_zone = date('Y-m-d H:i:s', strtotime($request->$columna_registro));

								$update = "'".$timestamp_without_time_zone."'";

							}else{

								if($charset_def !== 'UTF8'){

									$update = "'".utf8_decode($request->$columna_registro)."'";

								}else{

									$update = "'".$request->$columna_registro."'";

								}

							}

                        }

						$sql_select_columna = "SELECT $columna_registro::text as $columna_registro from $tabla_selected where ($primera_columna)::text = ('".$id."')::text;";

                        $select_columna = $conexion->select($sql_select_columna);

						$select_columna = $select_columna[0]->$columna_registro;

						if( $select_columna !== $request->$columna_registro){

							$conexion->update('update '.$tabla_selected.' set '.$columna_registro.' = '.$update.' where ('.$primera_columna.")::text = ('".$id."')::text;");

							$count_modificaciones++;

						}

					}

				}

				if($count_modificaciones === 0){

					return back()->withInput()->with('registro_no_modificado', 'Nada fue modificado.');

				}else{

					Cache::forget('tabla_selected'.$this->id());

					return back()->withInput()->with('registro_actualizado', 'El registro se actualizó correctamente (campos modificados = '.$count_modificaciones.').');

				}

			}else{

				return back()->withInput()->with('registro_no_modificado', 'No se puede modificar '.$tabla_selected.' porque hay valores repetidos en la columna '.$primera_columna.' usada como primary key por la aplicación.');

			}

		}
		catch (\Exception $e)
		{

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}
    }

    
}
