<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Config;

class BuscadorController extends Controller
{
    public function buscador_string(Request $request)
    {
		try
		{

			if($request->session()->get('db_usuario_string') === NULL && $request->session()->get('db_host_string') === NULL){

				//echo 'entré al if'; exit;
                $request->session()->put('db_host_string',$request->db_host_string);
				$request->session()->put('db_usuario_string',$request->db_usuario_string);
				$request->session()->put('db_contrasenia_string',$request->db_contrasenia_string);
                $request->session()->put('db_database_string',$request->db_database_string);

			}

            //echo 'no entré al if'; exit;

            $db_usuario = $request->session()->get('db_usuario_string');
			$db_host = $request->session()->get('db_host_string');
            $db_database = $request->session()->get('db_database_string');
            $db_contrasenia = $request->session()->get('db_contrasenia_string');


            Config::set('database.connections.pgsql_variable', array(
				'driver'    => 'pgsql',
				'host'      => $db_host,
				'database'  => $db_database,
				'username'  => $db_usuario,
				'password'  => $db_contrasenia,
				'charset'   => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'prefix'    => '',
				'schema'    => 'public',
			));

            $conexion = DB::connection('pgsql_variable');

            $request->session()->put('buscador_string_view',true);

            $pschema_selected = null;
            $pstring_selected = null;

            $sql="select schema_name
						from information_schema.schemata
					   where not schema_name ilike 'pg%'
						 and schema_name <> 'information_schema'
						 and catalog_name = '".$db_database."'
					order by schema_name;";

			$schemas = $conexion->select($sql);

            if(isset($request->pcadena) && isset($request->pschema)){

                $conexion->unprepared('DROP FUNCTION IF EXISTS buscarCadena(character varying,character varying);');

                $conexion->unprepared("CREATE OR REPLACE FUNCTION buscarCadena(pcadena character varying, pesquema character varying)
                                    RETURNS TABLE(tabla character varying, columna character varying)
                                            LANGUAGE 'plpgsql'
                                            COST 100
                                            VOLATILE PARALLEL UNSAFE
                                            ROWS 1000
                                    AS \$BODY$
                                    DECLARE
                                        tabla_record character varying;
                                        columna_record character varying;
                                        tabla_guardada character varying;
                                        columna_guardada character varying;
                                        r record;
                                        BEGIN
                                        CREATE TEMPORARY TABLE tmp_rst (
                                            tabla character varying
                                        ,columna character varying);
                                        FOR tabla_record IN
                                            select table_name from information_schema.tables where table_schema = pesquema
                                        LOOP
                                            FOR columna_record IN
                                                SELECT column_name FROM information_schema.columns WHERE table_schema = pesquema
                                                    AND table_name = tabla_record and data_type = 'character varying'
                                            LOOP
                                                FOR r IN EXECUTE format('SELECT 1 FROM %I . %I where %I = %L',pesquema,tabla_record,columna_record,pcadena)
                                                    --select 1 from tabla where columna ilike '%pcadena%'
                                                LOOP
                                                    if not exists (select '' from tmp_rst t where t.tabla = tabla_record and t.columna = columna_record) then
                                                        insert into tmp_rst(tabla,columna) values (tabla_record,columna_record);
                                                    end if;
                                                END LOOP;
                                            END LOOP;
                                        END LOOP;
                                        RETURN QUERY SELECT t.* FROM tmp_rst t order by t.tabla;
                                        DROP TABLE tmp_rst;
                                        END;
                                    \$BODY$;");

                $pschema_selected = $request->pschema;
                $pstring_selected = $request->pcadena;

                $sql = "select * from buscarCadena('$pstring_selected','$pschema_selected');";

                $resultados = $conexion->select($sql);

                $conexion->unprepared('DROP FUNCTION IF EXISTS buscarCadena(character varying,character varying);');

                //echo 'function guardada correctamente';
                return view('home',['resultados' => $resultados
                                   ,'pschema_selected' => $pschema_selected
                                   ,'pstring_selected' => $pstring_selected
                                   ,'schemas' => $schemas]);

            }else{

                return view('home',['pschema_selected' => $pschema_selected
                                   ,'pstring_selected' => $pstring_selected
                                   ,'schemas' => $schemas]);

            }

        }
		catch (\Exception $e) {

			//En caso de error retorno al home con el mensaje del error
			$mensaje_error = $e->getMessage();

			return redirect('home')->withInput()->with('mensaje_error',$mensaje_error);

		}

    }
}
