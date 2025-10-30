<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    use HasFactory;
    protected $table = 'bases';
    public $timestamps = true;

    protected $fillable = [
         'servidor'
        ,'host'
        ,'usuario'
        ,'password'
        ,'grupo'
    ];

    public function grupo_relacion()
    {
        return $this->belongsTo(Grupo::class, 'grupo', 'id');
    }
}
