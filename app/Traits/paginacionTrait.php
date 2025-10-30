<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait paginacionTrait
{
    public function paginacion($array, $request, $perPage)
	{

		$page = $request->input('page', 1);

		$offset = ($page * $perPage) - $perPage;

		return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,['path' => $request->url(), 'query' => $request->query()]);

	}
}
