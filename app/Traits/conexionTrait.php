<?php

namespace App\Traits;

use DB;
use Config;
use App\Models\Base;

trait conexionTrait
{
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
			'schema'    => $schema
		));

		$conexion = DB::connection('pgsql_variable');

		return $conexion;
	}

	public function test_conexion($db_host = 'localhost', $db_usuario = 'postgres', $db_contrasenia = NULL, $database = 'postgres', $schema = 'public', $charset_def = 'utf8')
	{
		if($db_contrasenia){
				
			Config::set('database.connections.test_conexion', array(
				'driver'    => 'pgsql',
				'host'      => $db_host,
				'database'  => $database,
				'username'  => $db_usuario,
				'password'  => $db_contrasenia,
				'charset'   => $charset_def,
				'collation' => 'utf8_unicode_ci',
				'prefix'    => '',
				'schema'    => $schema,
				'options'   => array(\PDO::ATTR_TIMEOUT => 3)
			));

			try
			{
				DB::connection('test_conexion')->getPdo();

				return 'conectado';
			}
			catch (\Exception $e)
			{
				return 'no_conectado';
			}

		}else{

			return null;
		}
		
	}
}
