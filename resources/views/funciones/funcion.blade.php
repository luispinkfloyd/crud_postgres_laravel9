@if(isset($tabla_selected))
    @section('titulo', $tabla_selected.' - ')
@endif


<div class="tabla-funciones borde-bottom mb-0">
    <div class="row">
        <div class="col-12 text-center">
        	<h3 class="mt-1 text-white">Nombre de la función:
                @if($schema == 'public')
                    <b>{{$tabla_selected}}</b>
                @else
                    <b>{{$schema.'.'.$tabla_selected}}</b>
                @endif
            </h3>
        </div>
    </div>
</div>
<hr class="text-white tabla-funciones mt-0 mb-0">
<div class="tabla-funciones borde-top mt-0">
    <textarea id="textarea">
        {{$funcion}}
    </textarea>
</div>


