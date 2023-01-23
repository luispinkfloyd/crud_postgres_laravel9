<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Config;
use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use ReflectionClass;
use Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Base;
use App\Models\Grupo;
use Illuminate\Support\Str;
use Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function paginacion($array, $request, $perPage)
	{

		$page = $request->input('page', 1);

		$offset = ($page * $perPage) - $perPage;

		return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,['path' => $request->url(), 'query' => $request->query()]);

	}

	public function id(){
		$id = Auth::id();
		return $id;
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

	public function getSqlWithBindings($query)
	{
		return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
			return is_numeric($binding) ? $binding : "'{$binding}'";
		})->toArray());
	}

    public function index()
    {
        Cache::forget('database'.$this->id());
		Cache::forget('schema'.$this->id());
		Cache::forget('where1'.$this->id());
		Cache::forget('where2'.$this->id());
		Cache::forget('caracteres_raros'.$this->id());
		Cache::forget('tablas'.$this->id());
		Cache::forget('tabla_selected'.$this->id());
		Cache::forget('comparador1'.$this->id());
		Cache::forget('columna_selected1'.$this->id());
		Cache::forget('comparador2'.$this->id());
		Cache::forget('columna_selected2'.$this->id());
		Cache::forget('sort'.$this->id());
		Cache::forget('ordercol'.$this->id());
		Cache::forget('columnas'.$this->id());
		Cache::forget('registros'.$this->id());
		Cache::forget('count_registros'.$this->id());
        Cache::forget('consulta_de_registros'.$this->id());

		session()->forget('db_usuario');

		session()->forget('db_host');

		session()->forget('db_contrasenia');

        session()->forget('db_usuario_string');

		session()->forget('db_host_string');

		session()->forget('db_contrasenia_string');

        session()->forget('db_database_string');

		session()->forget('buscador_string_view');

		$grupos = Grupo::all();

		return view('home',compact('grupos'));
    }

    public function host(Request $request){


		//Intenta hacer la conexión (en caso de fallar, retorna al home y muestra el mensaje de error)
		try
		{

			//Hago la conexión mediante una función
			$conexion = $this->conexion($request);

			//Traigo los valores de la conexión para manejarlos como variantes directamente (menos la contraseña)
			$db_usuario = $request->session()->get('db_usuario');

			$db_host = $request->session()->get('db_host');

			//Hago la consulta para traer las bases de datos que haya en el host
			$sql="SELECT pg_database.datname
						  from pg_database
						 where pg_database.datname not in ('template0','template1')
					  order by pg_database.datname;";

			$bases = $conexion->select($sql);

			//Retorno al home con los datos de la consulta
			return view('home',['bases' => $bases,'db_usuario' => $db_usuario,'db_host' => $db_host]);

		}
		catch (\Exception $e) {

			//En caso de error retorno al home con el mensaje del error
			$mensaje_error = $e->getMessage();

			return redirect('home')->withInput()->with('mensaje_error',$mensaje_error);

		}

	}

	public function database(Request $request)
    {

		//Verifico que los input session hechos en el método anterior sigan seteados
		if($request->session()->get('db_usuario') !== NULL && $request->session()->get('db_host') !== NULL){

			//Traigo los inputs session y la base de datos seleccionada en el form_database (todos los datos para armar la conexión, a partir de acá, se manejan por get)
			$database = $request->database;

			//Hago la conexión mediante una función
			$conexion = $this->conexion($request, $database);

			$db_usuario = $request->session()->get('db_usuario');

			$db_host = $request->session()->get('db_host');

			//Consulto los schemas disponibles de la base de datos seleccionada
			$sql="SELECT schema_name
						from information_schema.schemata
					   where not schema_name ilike 'pg%'
						 and schema_name <> 'information_schema'
						 and catalog_name = '".$database."'
					order by schema_name;";

			$schemas = $conexion->select($sql);

			//Consulto la codificación de la base y la almaceno en un input session para usarla en futuras consultas (hasta acá, siempre se usa la codificación UTF8)
			$sql_charset = 'SHOW SERVER_ENCODING';

			$charset_registro = $conexion->select($sql_charset);

			$charset = $charset_registro[0]->server_encoding;

			$request->session()->put('charset_def', $charset);

			//Si sólo existe un schema, me salteo la vista para seleccionar el schema y voy directo a la consulta con los valores del schema mismo y la base de datos.
			if(count($schemas) == 1){

				$request->session()->put('schema', $schemas[0]->schema_name);

				$request->session()->put('database', $database);

				return redirect()->route('schema');

			}

			//Retorno al home con los datos de las consultas
			return view('home',['database' => $database,'schemas' => $schemas,'db_usuario' => $db_usuario,'db_host' => $db_host]);

		}else{

			//En caso que los input session no sigan seteados, redirecciono al home inicial
			return redirect('home');

		}

    }

	public function schema(Request $request)
    {

		//Verifico que los input session hechos en el primer método sigan seteados
		if($request->session()->get('db_usuario') !== NULL && $request->session()->get('db_host') !== NULL){

			//Traigo los inputs session y la base de datos seleccionada más el schema seleccionado en el form_schema
			if(isset($request->database) && isset($request->schema)){

				$database = $request->database;

				$schema = $request->schema;

			}else{

				$schema = $request->session()->get('schema');

				$database = $request->session()->get('database');

			}

			$charset_def = $request->session()->get('charset_def');

			//Hago la conexión mediante una función
			$conexion = $this->conexion($request, $database, $schema, $charset_def);

			$db_usuario = $request->session()->get('db_usuario');

			$db_host = $request->session()->get('db_host');

			$selector_table_function_selected = 'table';
		
			if(isset($request->selector_table_function)){
				if($request->selector_table_function != 'table'){
					$selector_table_function_selected = $request->selector_table_function;
				}
			}

			//Consulto las tablas disponibles en el schema seleccionado
			if($selector_table_function_selected == 'table'){

				$sql = "SELECT table_name
							from information_schema.tables
						   where table_schema = '".$schema."'
						order by table_name;";

			}else{

				$sql = "SELECT proname as table_name
					from pg_proc
					where pronamespace = (select pg_namespace.oid
											from pg_namespace
										where nspname = '".$schema."')
					order by proname;";

				$selector_table_function_selected = 'function';

			}

			$datos = $conexion->select($sql);

			//Retorno al home con los datos de las consultas
			return view('home',['database' => $database,'schema' => $schema,'tablas' => $datos,'db_usuario' => $db_usuario,'db_host' => $db_host, 'selector_table_function_selected' => $selector_table_function_selected]);

		}else{

			//En caso que los input session no sigan seteados, redirecciono al home inicial
			return redirect('home');

		}

    }

	public function tabla(Request $request)
    {

		try
		{

			if(empty($request->tabla_selected) || $request->tabla_selected == NULL){ return back()->with('mensaje_error','Selección de tabla vacía');}



			if($request->session()->get('db_usuario') !== NULL && $request->session()->get('db_host') !== NULL){

				ini_set('memory_limit', -1);
				set_time_limit(500);

				if(isset($request->limpiar)){

					Cache::forget('tabla_selected'.$this->id());

				}

				$selector_table_function_selected = 'table';

				$database = $request->database;
				Cache::forget('database'.$this->id());
				Cache::put('database'.$this->id(),$database);
				$request->session()->forget('database');
				$schema = $request->schema;
				Cache::forget('schema'.$this->id());
				Cache::put('schema'.$this->id(),$schema);
				$request->session()->forget('schema');
				$charset_def = $request->session()->get('charset_def');

				$conexion = $this->conexion($request, $database, $schema, $charset_def);

				$db_usuario = $request->session()->get('db_usuario');
				$db_host = $request->session()->get('db_host');

				if($request->selector_table_function == 'function'){

					$sql_funciones_como_tablas = "SELECT proname as table_name
													from pg_proc
													where pronamespace = (select pg_namespace.oid
																			from pg_namespace
																		where nspname = '".$schema."')
													order by proname;";

					$tablas = $conexion->select($sql_funciones_como_tablas);

					Cache::forget('tablas'.$this->id());

					Cache::put('tablas'.$this->id(),$tablas,3600);

					$tabla_selected = $request->tabla_selected;

					Cache::forget('tabla_selected'.$this->id());

					Cache::put('tabla_selected'.$this->id(),$tabla_selected,3600);

					$sql_funcion_con_argumentos = "SELECT pg_proc.prosrc
														 --,unnest(string_to_array((oidvectortypes(proargtypes)), ',')) as arguments_type
														 ,pg_proc.oid as oid
													from pg_proc
													where pronamespace = (select pg_namespace.oid
																			from pg_namespace
																		where nspname = '".$schema."')
													and proname = '$tabla_selected'";

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

						Cache::forget('columna_selected1'.$this->id());
						Cache::forget('comparador1'.$this->id());
						Cache::forget('where1'.$this->id());
						Cache::forget('columna_selected2'.$this->id());
						Cache::forget('comparador2'.$this->id());
						Cache::forget('where2'.$this->id());
						Cache::forget('ordercol'.$this->id());
						Cache::forget('tabla_selected'.$this->id());

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

							Cache::forget('caracteres_raros'.$this->id());

							Cache::put('caracteres_raros'.$this->id(),$request->caracteres_raros,3600);

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

							if($request->comparador1 === 'ilike'){

								$conexion->unprepared("CREATE OR REPLACE FUNCTION ".$function."(text) RETURNS text AS \$BODY$ SELECT translate($1,'".$originales."','".$modificadas."'); \$BODY$ LANGUAGE sql IMMUTABLE STRICT COST 100");

							}

						}

						$sql="SELECT table_name
										from information_schema.tables
									where table_schema = '".$schema."'
									order by table_name;";

						$tablas = $conexion->select($sql);

						Cache::forget('tablas'.$this->id());

						Cache::put('tablas'.$this->id(),$tablas,3600);

						$tabla_selected = $request->tabla_selected;

						Cache::forget('tabla_selected'.$this->id());

						Cache::put('tabla_selected'.$this->id(),$tabla_selected,3600);

						$registros = $conexion->table($tabla_selected);

						$comparador1 = NULL;

						$columna_selected1 = NULL;

						$where1 = NULL;

						$comparador2 = NULL;

						$columna_selected2 = NULL;

						$where2 = NULL;

						$sql="SELECT column_name
									,is_nullable as required
									,character_maximum_length as max_char
									,data_type as type
									,column_default as default
									,case when data_type = 'integer' then data_type
										else data_type||coalesce('('||character_maximum_length::text||')','')||coalesce('('||numeric_precision::text||','||numeric_scale::text||')','')
									end as data_type
								from INFORMATION_SCHEMA.columns col
							where table_name = '".$tabla_selected."'
								and table_schema = '".$schema."'
							order by col.ordinal_position";

						$columnas = $conexion->select($sql);

						Cache::forget('columnas'.$this->id());

						Cache::put('columnas'.$this->id(),$columnas,3600);

						$col_num = 1;

						$col_array = array();

						foreach($columnas as $columna){

							if(isset($request->ordercol)){


								if($col_num == $request->ordercol){

									$col_num = $col_num + 1;

								}else{

									$col_array[] = $col_num++;

								}
							}else{

								$col_array[] = $col_num++;

							}

						}

						$sort = 'asc';

						if(isset($request->sort)){ $sort = $request->sort; Cache::put('sort'.$this->id(),$sort,3600); }

						if(isset($request->ordercol)){

							$ordercol = $request->ordercol;

							$col_string = $request->ordercol.' '.$sort.','.implode(",",$col_array);

						}else{

							$ordercol = NULL;

							$col_string = implode(",",$col_array);

						}

						Cache::forget('ordercol'.$this->id());

						Cache::put('ordercol'.$this->id(),$request->ordercol,3600);

						if(isset($request->columna_selected1)){

							Cache::forget('where1'.$this->id());

							Cache::put('where1'.$this->id(),$request->where1,3600);

							$comparador1 = $request->comparador1;

							Cache::forget('comparador1'.$this->id());

							Cache::put('comparador1'.$this->id(),$comparador1,3600);

							$columna_selected1 = $request->columna_selected1;

							Cache::forget('columna_selected1'.$this->id());

							Cache::put('columna_selected1'.$this->id(),$columna_selected1,3600);

							if($charset_def != 'UTF8'){

								$where1 = utf8_decode($request->where1);

							}else{

								$where1 = $request->where1;
							}

							$busqueda = str_replace("´`'çÇ¨",'_',$where1);

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

							Cache::forget('where2'.$this->id());

							Cache::put('where2'.$this->id(),$request->where2,3600);

							$comparador2 = $request->comparador2;

							Cache::forget('comparador2'.$this->id());

							Cache::put('comparador2'.$this->id(),$comparador2,3600);

							$columna_selected2 = $request->columna_selected2;

							Cache::forget('columna_selected2'.$this->id());

							Cache::put('columna_selected2'.$this->id(),$columna_selected2,3600);

							if($charset_def != 'UTF8'){

								$where2 = utf8_decode($request->where2);

							}else{

								$where2 = $request->where2;
							}

							$busqueda2 = str_replace("´`'çÇ¨",'_',$where2);

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

						$consulta_de_registros = $this->getSqlWithBindings($registros);
						$consulta_de_registros = str_replace('"','',$consulta_de_registros).';';

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

						Cache::put('registros'.$this->id(),$registros,3600);

						Cache::forget('consulta_de_registros'.$this->id());

						Cache::put('consulta_de_registros'.$this->id(),$consulta_de_registros,3600);

						Cache::forget('count_registros'.$this->id());

						Cache::put('count_registros'.$this->id(),$count_registros,3600);

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


	/*function object_to_array($object)
	{
		$reflectionClass = new ReflectionClass(get_class($object));
		$array = array();
		foreach ($reflectionClass->getProperties() as $property) {
			$property->setAccessible(true);
			$array[$property->getName()] = $property->getValue($object);
			$property->setAccessible(false);
		}
		return $array;
	}*/ //No se usa


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

			$sql="SELECT column_name
						,is_nullable as required
						,character_maximum_length as max_char
						,data_type as type
						,column_default as default
						,data_type||coalesce('('||character_maximum_length::text||')','') as data_type
					from INFORMATION_SCHEMA.columns col
				   where table_name = '".$tabla_selected."'
					 and table_schema = '".$schema."'
				order by col.ordinal_position";

			$columnas = $conexion->select($sql);

			$insert = '';

			$columnas_registro = '';

			foreach($columnas as $columna){

				$columnas_registro = $columnas_registro.$columna->column_name.',';

				$columna_registro = $columna->column_name;

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

				return back()->withInput()->with('registro_no_modificado', 'No se puede borrar el registro de la tabla '.$tabla_selected.' porque hay valores repetidos en la columna '.$primera_columna.' usada como primary key por la aplicación.');

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

			$sql="SELECT column_name
						,is_nullable as required
						,character_maximum_length as max_char
						,data_type as type
						,column_default as default
						,data_type||coalesce('('||character_maximum_length::text||')','') as data_type
					from INFORMATION_SCHEMA.columns col
				   where table_name = '".$tabla_selected."'
					 and table_schema = '".$schema."'
				order by col.ordinal_position";

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

    public function create_base(Request $request)
    {
        try
        {
            $base = new Base;
            $base->servidor = $request->servidor_bases;
			$base->host = $request->host_bases;
            $base->usuario = $request->usuario_bases;
            $base->password = $request->password_bases;
			$base->grupo = $request->grupo_bases;
            $base->save();

            return back()->withInput()->with('ok', "El servidor $request->servidor_bases se agregó correctamente");

        }
        catch (\Exception $e)
		{

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}
    }

	public function create_grupo(Request $request)
    {
        try
        {
            $grupo = new Grupo;
            $grupo->nombre = $request->nombre_grupo;
            $grupo->save();

            return back()->withInput()->with('ok', "El grupo $request->nombre_grupo se generó correctamente");

        }
        catch (\Exception $e)
		{

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}
    }

}
