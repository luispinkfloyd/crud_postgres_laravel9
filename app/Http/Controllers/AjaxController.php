<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use Session;
use Cache;
use DB;
use App\Models\Base;
use Auth;

class AjaxController extends Controller
{
    public function id(){
		return Auth::id();
	}

    public function conexion($request)
    {
        $db_usuario = $request->session()->get('db_usuario');
        $db_host = $request->session()->get('db_host');
        $charset_def = $request->session()->get('charset_def');
		$database = Cache::get('database'.$this->id());
		$schema = Cache::get('schema'.$this->id());
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
    
    public function ajax_columna(Request $request){
        
        $conexion = $this->conexion($request);
		$tabla_selected = Cache::get('tabla_selected'.$this->id());
		$columna = $request->columna;
		$sql = "SELECT distinct($columna) as columna from $tabla_selected order by 1;";
		$columna_valores = $conexion->select($sql);
		foreach($columna_valores as $columna_valor) {
            $columna_valores_array[] = (array) $columna_valor->columna;
        }
		return response()->json($columna_valores_array);

	}

    public function ajax_grupo(Request $request){

		$grupo = Base::where('grupo',$request->grupo_selected)->orderBy('servidor')->get();
        if(count($grupo) >= 1){
            return $grupo;
        }else{
            return false;
        }

	}

    public function ajax_host(Request $request){

		$base = Base::where('id',$request->host_selected)->get();
        if(count($base) == 1){
            return $base;
        }else{
            return false;
        }

	}

    /*public function ajax_get_bases_string(Request $request){

        $db_usuario = $request->db_usuario_string;
        $db_host = $request->db_host_string;
        $db_contrasenia_string = $request->db_contrasenia_string;

		Config::set('database.connections.pgsql_variable', array(
                    'driver'    => 'pgsql',
                    'host'      => $db_host,
                    'database'  => 'postgres',
                    'username'  => $db_usuario,
                    'password'  => $db_contrasenia_string,
                    'charset'   => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix'    => '',
                    'schema'    => 'public',
					));

		$conexion = DB::connection('pgsql_variable');

		$sql="select pg_database.datname
						  from pg_database
						 where pg_database.datname not in ('template0','template1')
					  order by pg_database.datname;";

		$bases = $conexion->select($sql);

		foreach($bases as $base) {
            $bases_valores_array[] = (array) $base->datname;
        }

		return response()->json($bases_valores_array);
    }*/ //No sé dónde se usa
}
