<?php

namespace App\Traits;

use Acamposm\Ping\Ping;
use Acamposm\Ping\PingCommandBuilder;

trait verfificarVPNTrait
{
    public function verificarVPN($ping){

        $command = (new PingCommandBuilder($ping))->count(2)->interval(1)->ttl(128);

		try{
			$resultado_ping = (new Ping($command))->run();
			$vpn_conexion = 'Conectado';
		}catch(\Exception $e){
			$vpn_conexion = 'Desconectado';
		}

        return $vpn_conexion;
    }
}
