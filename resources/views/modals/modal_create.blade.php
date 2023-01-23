{{-- create Modal HTML --}}
<div id="addModal" class="modal fade">
	<div class="modal-dialog modal-size">
		<div class="modal-content">
			<form method="get" action="{{ route('home.store') }}">
            	<input type="hidden" name="database" value="{{$database}}">
                <input type="hidden" name="schema" value="{{$schema}}">
                <input type="hidden" name="tabla_selected" value="{{$tabla_selected}}">
                <div class="modal-header">						
					<h4 class="modal-title">Agregar Registro</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
				</div>
				<div class="modal-body">
                	@foreach($columnas as $columna)
                        @if($columna->type === 'character' | $columna->type === 'character varying' | $columna->type === 'text' | $columna->type === 'char' | $columna->type === 'varchar')
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
                                placeholder="texto...">
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
                            placeholder="número...">
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
                            >
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
                            <input type="datetime-local" class="form-control" name="{{$columna->column_name}}"
                                @if($columna->required === 'NO')
                                    {{' required '}}
                                @endif
                            >
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
                                <option>true</option>
                                <option>false</option>
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
                            >
                        </div>
                        @endif
                    @endforeach
                </div>
				<div class="modal-footer">
					<input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancelar">
					<input type="submit" class="btn btn-success" value="Agregar">
				</div>
			</form>
		</div>
	</div>
</div>