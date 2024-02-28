<form class="form-general table-size" action="{{ route('buscador_string') }}" method="get">
	{{-- Form 1 (siempre visible) --}}
    <div class="row form-select-row">
        <div class="col-sm form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="schemas_span">Schema</span>
                </div>
                <select class="custom-select" name="pschema" required>
                    <option disabled selected value>--Seleccione--</option>
                    @foreach($schemas as $schema)
                        <option <?php if(isset($pschema_selected)) if($pschema_selected === $schema->schema_name) echo 'selected';?>>{{$schema->schema_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="cadena_span">Parámetro</span>
                </div>
                <input type="text" name="pcadena" id="pcadena" autocomplete="off" class="form-control" placeholder="Parámetro de búsqueda..." @if(isset($pstring_selected)) value="{{$pstring_selected}}" @endif required>
            </div>
        </div>
        <div class="col-sm-1 form-group">
        	<button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Buscar"><img src="{{asset('img/lupa.png')}}" height="20"></button>
        </div>
    </div>
	<!-- Fin del form 1 -->
 </form>
