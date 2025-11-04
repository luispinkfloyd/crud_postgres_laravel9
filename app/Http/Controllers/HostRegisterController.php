<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\Base;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BasesGruposExport;
use App\Traits\paginacionTrait;
use App\Traits\conexionTrait;
use Cache;
use Auth;

class HostRegisterController extends Controller
{
    use paginacionTrait;
    use conexionTrait;

    public function grupos_bases(Request $request)
    {
        $grupos = Grupo::all()->sortBy('nombre');
        $datos = Base::orderBy('id','asc')->with('grupo_relacion');

        $grupo_selected = null;
        $busqueda = null;
        $tipo_red_selected = null;
        $activo_selected = null;
        $conectado = null;
        
        if(isset($request->grupo)){
            $grupo_selected = $request->grupo;
            $datos = $datos->where('grupo', $grupo_selected);
        }

        if(isset($request->tipo_red)){
            $tipo_red_selected = $request->tipo_red;
            $datos = $datos->where('tipo_red', $tipo_red_selected);
        }

        if(isset($request->activo)){
            $activo_selected = $request->activo;
            if($activo_selected == '1'){
                $datos = $datos->where('activo', true);
            }elseif($activo_selected == '0'){
                $datos = $datos->where('activo', false);
            }
        }

        if(isset($request->busqueda)){
            $busqueda = $request->busqueda;
            $datos = $datos->where(function($query) use ($busqueda) {
                $query->where('servidor', 'ilike', '%'.$busqueda.'%')
                      ->orWhere('host', 'ilike', '%'.$busqueda.'%')
                      ->orWhere('usuario', 'ilike', '%'.$busqueda.'%')
                      ->orWhere('password', 'ilike', '%'.$busqueda.'%');
            });
        }

        $total_datos = $datos->get();
        $count_datos = $total_datos->count();

        Cache::forget('datos_grupos_bases'.Auth::id());
        Cache::put('datos_grupos_bases'.Auth::id(), $total_datos, 3600);

        $perPage = 10;
        $datos = $datos->paginate($perPage);

        if(isset($request->host) && isset($request->usuario) && isset($request->contrasenia)){
            $conectado = $this->test_conexion($request->host, $request->usuario, $request->contrasenia);
        }
        
        return view('grupos_bases.grupos_bases', [  'grupos' => $grupos,
                                                    'datos' => $datos,
                                                    'count_datos' => $count_datos,
                                                    'grupo_selected' => $grupo_selected,
                                                    'busqueda' => $busqueda,
                                                    'tipo_red_selected' => $tipo_red_selected,
                                                    'activo_selected' => $activo_selected,
                                                    'conectado' => $conectado]);
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
            $base->tipo_red = $request->tipo_red_bases;
            if($request->activo_bases == 'true'){
                $base->activo = true;
            }else{
                $base->activo = false;
            }
            $base->save();

            return back()->withInput()->with('ok', "El servidor $request->servidor_bases se agregó correctamente");

        }
        catch (\Exception $e)
		{

			$mensaje_error = $e->getMessage();

			return back()->withInput()->with('mensaje_error',$mensaje_error);

		}
    }

    public function edit_base(Request $request, $id)
    {
        try
        {
            $base = Base::findOrFail($id);
            $base->servidor = $request->servidor_bases;
            $base->host = $request->host_bases;
            $base->usuario = $request->usuario_bases;
            $base->password = $request->password_bases;
            $base->grupo = $request->grupo_bases;
            $base->tipo_red = $request->tipo_red_bases;
            if($request->activo_bases == 'true'){
                $base->activo = true;
            }else{
                $base->activo = false;
            }
            $base->save();

            return back()->withInput()->with('ok', "El servidor $request->servidor_bases se modificó correctamente");
        }
        catch (\Exception $e)
        {
            $mensaje_error = $e->getMessage();
            return back()->withInput()->with('mensaje_error',$mensaje_error);
        }

    }

    public function delete_base($id)
    {
        try
        {
            $base = Base::findOrFail($id);
            $base->delete();

            return back()->withInput()->with('ok', "El servidor se eliminó correctamente");
        }
        catch (\Exception $e)
        {
            $mensaje_error = $e->getMessage();
            return back()->withInput()->with('mensaje_error',$mensaje_error);
        }
    }

    public function edit_grupo(Request $request, $id)
    {
        try
        {
            $grupo = Grupo::findOrFail($id);
            $grupo->nombre = $request->nombre_grupo;
            $grupo->save();

            return back()->withInput()->with('ok', "El grupo $request->nombre_grupo se modificó correctamente");
        }
        catch (\Exception $e)
        {
            $mensaje_error = $e->getMessage();
            return back()->withInput()->with('mensaje_error',$mensaje_error);
        }

    }

    public function delete_grupo($id)
    {
        try
        {
            $grupo = Grupo::findOrFail($id);
            $existen_bases_asociadas = Base::where('grupo', $id)->count();
            if ($existen_bases_asociadas > 0) {
                return back()->withInput()->with('mensaje_error',"No se puede eliminar el grupo porque tiene bases de datos asociadas.");
            }else{
                $grupo->delete();
                return back()->withInput()->with('ok', "El grupo se eliminó correctamente");
            }
        }
        catch (\Exception $e)
        {
            $mensaje_error = $e->getMessage();
            return back()->withInput()->with('mensaje_error',$mensaje_error);
        }
    }

    public function exportar_grupos_bases_excel()
    {
        $nombre_hoja = 'grupos_bases_'.date('dmYHis').'.xlsx';
        return Excel::download(new BasesGruposExport, $nombre_hoja);
    }
}
