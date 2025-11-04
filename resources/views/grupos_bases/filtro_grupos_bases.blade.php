<div class="form-general">
	<form action="{{route('grupos_bases')}}" method="get">
		<div class="row me-2">
			<div class="col-sm-auto">
				<div class="input-group m-2">
					<span class="input-group-text">Grupo</span>
					<select class="form-select" name="grupo">
						<option selected disabled value>--Seleccione--</option>
						@foreach($grupos as $grupo)
						  	<option value="{{$grupo->id}}" @if($grupo_selected == $grupo->id) {{'selected'}} @endif>{{$grupo->nombre}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-sm-auto">
				<div class="input-group m-2">
					<span class="input-group-text">Activo</span>
					<select class="form-select" name="activo">
						<option selected disabled value>--Seleccione--</option>
						<option value="1" @if($activo_selected == '1') {{'selected'}} @endif>Sí</option>
						<option value="0" @if($activo_selected == '0') {{'selected'}} @endif>No</option>
					</select>
				</div>
			</div>
			<div class="col-sm-auto">
				<div class="input-group m-2">
					<span class="input-group-text">Red</span>
					<select class="form-select" name="tipo_red">
						<option selected disabled value>--Seleccione--</option>
						<option value="local" @if($tipo_red_selected == 'local') {{'selected'}} @endif>Local</option>
						<option value="publica" @if($tipo_red_selected == 'publica') {{'selected'}} @endif>Pública</option>
					</select>
				</div>
			</div>
			<div class="col-sm">
				<div class="input-group m-2">
					<span class="input-group-text">Búsqueda</span>
					<input type="text" class="form-control" name="busqueda" @if(isset($busqueda)) value="{{$busqueda}}" @endif 
					placeholder="Buscar por nombre, ip, usuario y/o contraseña ...">
				</div>
			</div>
			<div class="col-auto m-auto text-center m-2 mb-2">
				<input class="btn btn-success ml-auto mr-2" type="submit" value="Filtrar" onClick="open_modal()">
				<a href="{{route('grupos_bases')}}" class="btn btn-danger">Limpiar</a>
			</div>
		</div>
	</form>
</div>