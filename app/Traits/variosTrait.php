<?php

namespace App\Traits;

use Cache;
use Session;
use App\Traits\idUserTrait;

trait variosTrait
{
	use idUserTrait;

    public function getSqlWithBindings($query)
	{
		return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
			return is_numeric($binding) ? $binding : "'{$binding}'";
		})->toArray());
	}

	public function session_cache_erase($cache = true, $session = true)
	{
		if($cache){
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
		}
		
		if($session){
			session()->forget('db_usuario');
			session()->forget('db_host');
			session()->forget('db_contrasenia');
			session()->forget('db_usuario_string');
			session()->forget('db_host_string');
			session()->forget('db_contrasenia_string');
			session()->forget('db_database_string');
			session()->forget('buscador_string_view');
		}
	}

	public function session_forget($session)
	{
		session()->forget($session);
	}

	public function cache_forget($cache)
	{
		Cache::forget($cache);
	}

	public function cache_put($cache, $value, $time = 3600)
	{
		$this->cache_forget($cache);
		Cache::put($cache, $value, $time);
	}

	public function cache_get($cache)
	{
		return Cache::get($cache);
	}

	public function forget_table_selected()
	{
		Cache::forget('columna_selected1'.$this->id());
		Cache::forget('comparador1'.$this->id());
		Cache::forget('where1'.$this->id());
		Cache::forget('columna_selected2'.$this->id());
		Cache::forget('comparador2'.$this->id());
		Cache::forget('where2'.$this->id());
		Cache::forget('ordercol'.$this->id());
		Cache::forget('tabla_selected'.$this->id());
	}
}
