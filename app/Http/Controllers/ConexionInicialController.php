<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\paginacionTrait;
use App\Traits\idUserTrait;
use App\Traits\conexionTrait;
use App\Traits\variosTrait;
use App\Traits\verfificarVPNTrait;

//Por si los necesito
use Session;
use DB;
use Cache;

class ConexionInicialController extends Controller
{
    use paginacionTrait;
	use idUserTrait;
	use conexionTrait;
	use variosTrait;
	use verfificarVPNTrait;

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
			$sql="  SELECT      pg_database.datname
					FROM        pg_database
					WHERE       pg_database.datname NOT IN ('template0','template1')
					ORDER BY    pg_database.datname;";

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
			$sql="  SELECT      schema_name
					FROM        information_schema.schemata
					WHERE NOT   schema_name ILIKE 'pg%'
					AND         schema_name <> 'information_schema'
					AND         catalog_name = '".$database."'
					ORDER BY    schema_name;";

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
			$mensaje_error = 'Sesión expirada. Por favor, vuelva a conectarse.';
			return redirect('home')->with('mensaje_error',$mensaje_error);

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
			if($selector_table_function_selected == 'table'){ //si selecciono para ver tablas

				$sql = "SELECT 		table_name
						FROM 		information_schema.tables
						WHERE 		table_schema = '".$schema."'
						ORDER BY 	table_name;";

			}else{ //si selecciono para ver funciones

				$sql = "	SELECT 		proname as table_name
							FROM 		pg_proc
							WHERE 		pronamespace = (	SELECT 	pg_namespace.oid
															FROM 	pg_namespace
															WHERE 	nspname = '".$schema."')
							ORDER BY proname;";

				$selector_table_function_selected = 'function';

			}

			$datos = $conexion->select($sql);

			//Retorno al home con los datos de las consultas
			return view('home',['database' => $database,
								'schema' => $schema,
								'tablas' => $datos,
								'db_usuario' => $db_usuario,
								'db_host' => $db_host,
								'selector_table_function_selected' => $selector_table_function_selected]);

		}else{

			//En caso que los input session no sigan seteados, redirecciono al home inicial
			$mensaje_error = 'Sesión expirada. Por favor, vuelva a conectarse.';
			return redirect('home')->with('mensaje_error',$mensaje_error);

		}

    }
}
