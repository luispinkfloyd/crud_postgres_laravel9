@foreach($columnas as $columna)
	@php($primera_columna = $columna->column_name)
	@break
@endforeach

@foreach($registros as $registro)
{{-- Delete Modal HTML --}}
<div id="deleteModal{{str_replace('.','_',$registro->$primera_columna)}}" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('home.destroy', $registro->$primera_columna)}}" method="get">
            	<input type="hidden" name="database" value="{{$database}}">
                <input type="hidden" name="schema" value="{{$schema}}">
                <input type="hidden" name="tabla_selected" value="{{$tabla_selected}}">
                <input type="hidden" name="primera_columna" value="{{$primera_columna}}">
                <div class="modal-header">
                    <h4 class="modal-title">Borrar Registro</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que quiere borrar el registro<br>{{$primera_columna}}: {{$registro->$primera_columna}}?</p>
                    <p class="text-danger"><small>*Esta acción no puede deshacerse</small></p>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancel">
                    <button class="btn btn-danger" type="submit">Borrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
