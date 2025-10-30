<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Traits\variosTrait;
use App\Traits\verfificarVPNTrait;

class HomeController extends Controller
{
	use variosTrait;
	use verfificarVPNTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->session_cache_erase();

		$grupos = Grupo::all();

		$verificar_vpn = NULL;

		$vpn_rectorado = 'Sin verificar';
		$vpn_arsat = 'Sin verificar';

		if(isset($request->verificar_vpn)){

			$verificar_vpn = 1;
			$ping_rectorado = '192.168.50.3'; //Base de datos guaraní grado 3.20.0
			//$ping_arsat	= '172.16.169.167'; //Base de datos guaraní unificado 3.21.3

			$vpn_rectorado = $this->verificarVPN($ping_rectorado);
		}

		return view('home',compact('grupos','vpn_rectorado','vpn_arsat','verificar_vpn'));
    }

}
