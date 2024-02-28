@foreach($columnas as $columna)
	@php($primera_columna = $columna->column_name)
	@break
@endforeach

@foreach($registros as $registro)
{{-- Delete Modal HTML --}}
<div id="editModal{{str_replace('.','_',$registro->$primera_columna)}}" class="modal fade">
    <div class="modal-dialog modal-size">
        <div class="modal-content">
            <form action="{{ route('home.edit', $registro->$primera_columna)}}" method="get">
            	<input type="hidden" name="database" value="{{$database}}">
                <input type="hidden" name="schema" value="{{$schema}}">
                <input type="hidden" name="tabla_selected" value="{{$tabla_selected}}">
                <input type="hidden" name="primera_columna" value="{{$primera_columna}}">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Registro</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    @foreach($columnas as $columna)
                    	@php($nombre_columna = $columna->column_name)
                        @if($columna->column_name === $primera_columna)
                            <div class="mb-2">
                                <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}:</label>
                                <input type="text" class="form-control" name="{{$columna->column_name}}" value="{{$registro->$nombre_columna}}" readonly>
                            </div>
                        @elseif($columna->type === 'character' | $columna->type === 'character varying' | $columna->type === 'text' | $columna->type === 'char' | $columna->type === 'varchar')
                        <div class="mb-2">
                            <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}
                                <small>
                                    @if($columna->required === 'NO')
                                        {{'(Obligatorio)'}}
                                    @else
                                        {{'(No obligatorio)'}}
                                    @endif
                                </small>:
                            </label>
                            <input type="text" class="form-control" name="{{$columna->column_name}}"
                                @if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
                                @isset($columna->max_char)
                                    {{' maxlength="'.$columna->max_char.'" '}}
                                @endisset
							    @if($charset_def !== 'UTF8')
								    @php($value = utf8_encode($registro->$nombre_columna))
							    @else
								    @php($value = $registro->$nombre_columna)
                                @endif
                            value="{{$value}}">
                        </div>
                        @elseif($columna->type === 'int' | $columna->type === 'integer' | $columna->type === 'smallint' or $columna->type === 'bigint' | $columna->type === 'numeric')
                        <div class="mb-2">
                            <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}
                                <small>
                                    @if($columna->required === 'NO')
                                        {{'(Obligatorio)'}}
                                    @else
                                        {{'(No obligatorio)'}}
                                    @endif
                                </small>:
                            </label>
                            <input type="number" class="form-control" name="{{$columna->column_name}}"
                                @if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
                                @if($charset_def !== 'UTF8')
                                    @php($value = utf8_encode($registro->$nombre_columna))
                                @else
                                    @php($value = $registro->$nombre_columna)
                                @endif
                            value="{{$value}}">
                        </div>
                        @elseif($columna->type === 'date')
                        <div class="mb-2">
                            <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}
                                <small>
                                    @if($columna->required === 'NO')
                                        {{'(Obligatorio)'}}
                                    @else
                                        {{'(No obligatorio)'}}
                                    @endif
                                </small>:
                            </label>
                            <input type="date" class="form-control" name="{{$columna->column_name}}"
                                @if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
                                @if($charset_def !== 'UTF8')
                                    @php($value = utf8_encode($registro->$nombre_columna))
                                @else
                                    @php($value = $registro->$nombre_columna)
                                @endif
                            value="{{$value}}">
                        </div>
                        @elseif($columna->type === 'timestamp without time zone' or $columna->type === 'timestamp with time zone')
                        <div class="mb-2">
                            <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}
                                <small>
                                    @if($columna->required === 'NO')
                                        {{'(Obligatorio)'}}
                                    @else
                                        {{'(No obligatorio)'}}
                                    @endif
                                </small>:
                            </label>
                            <input type="text" class="form-control" name="{{$columna->column_name}}"
                                @if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
                                @if($charset_def !== 'UTF8')
                                    @php($value = utf8_encode($registro->$nombre_columna))
                                @else
                                    @php($value = $registro->$nombre_columna)
                                @endif
                            value="{{$value}}">
                        </div>
                        @elseif($columna->type === 'boolean')
                        <div class="mb-2">
                            <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}
                                <small>
                                    @if($columna->required === 'NO')
                                        {{'(Obligatorio)'}}
                                    @else
                                        {{'(No obligatorio)'}}
                                    @endif
                                </small>:
                            </label>
                            <select class="form-select" name="{{$columna->column_name}}"
								@if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
                            >
                                <option disabled selected value>--Seleccione--</option>
                                <option @if($registro->$nombre_columna === true) {{'selected'}} @endif>true</option>
                                <option @if($registro->$nombre_columna !== true) {{'selected'}} @endif>false</option>
                            </select>
                        </div>
                        @elseif($columna->type === 'time without time zone')
                        <div class="mb-2">
                            <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}
                                <small>
                                    @if($columna->required === 'NO')
                                        {{'(Obligatorio)'}}
                                    @else
                                        {{'(No obligatorio)'}}
                                    @endif
                                </small>:
                            </label>
                            <input type="time" class="form-control" name="{{$columna->column_name}}"
                                @if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
							    @php($value = $registro->$nombre_columna)
                            value="{{$value}}">
                        </div>
                        @else
                        <div class="mb-2">
                            <label class="form-label" for="{{$columna->column_name}}">{{$columna->column_name}}
                                <small>
                                    @if($columna->required === 'NO')
                                        {{'(Obligatorio)'}}
                                    @else
                                        {{'(No obligatorio)'}}
                                    @endif
                                </small>:
                            </label>
                            <input type="text" class="form-control" name="{{$columna->column_name}}"
                                @if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
                                @if($charset_def !== 'UTF8')
                                    @php($value = utf8_encode($registro->$nombre_columna))
                                @else
                                    @php($value = $registro->$nombre_columna)
                                @endif
                            value="{{$value}}">
                        </div>
                        @endif
                    @endforeach
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancel">
                    <button class="btn btn-success" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
