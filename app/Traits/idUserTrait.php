<?php

namespace App\Traits;

use Auth;

trait idUserTrait
{
    public function id(){
		$id = Auth::id();
		return $id;
	}
}
