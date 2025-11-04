<div class="modal fade" id="modal-form-bases" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <form action="{{route('create_base')}}" method="POST">
            @csrf
            <div class="modal-content ">
                <div class="modal-header modal-header-color">
                    <h4>Crear Servidor</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body modal-body-color">
					<div class="row form-group">
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="servidor_bases">Nombre del Servidor <small class="small-color">(*)</small></span>
                                <input type="text" class="form-control" aria-describedby="servidor_bases" id="servidor_bases" name="servidor_bases" required>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="grupo_bases">Grupo <small class="small-color">(*)</small>:</label>
                                <select class="form-select" name="grupo_bases" id="grupo_bases" required>
                                    <option value selected disabled>--Seleccione--</option>
                                    @foreach ($grupos as $grupo)
                                        <option value="{{$grupo->id}}">{{$grupo->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="host_bases">Host <small class="small-color">(*)</small></span>
                                <input type="text" class="form-control" aria-describedby="host_bases" id="host_bases" name="host_bases" required>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="usuario_bases">Usuario <small class="small-color">(*)</small></span>
                                <input type="text" class="form-control" aria-describedby="usuario_bases" id="usuario_bases" name="usuario_bases" required>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="password_bases">Contraseña <small class="small-color">(*)</small></span>
                                <input type="text" class="form-control" aria-describedby="password_bases" id="password_bases" name="password_bases" required>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="tipo_red_bases">Red <small class="small-color">(*)</small>:</label>
                                <select class="form-select" name="tipo_red_bases" id="tipo_red_bases" required>
                                    <option value selected disabled>--Seleccione--</option>
                                    <option value="local">Local</option>
                                    <option value="publica">Pública</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="tipo_red_bases">Activo <small class="small-color">(*)</small>:</label>
                                <select class="form-select" name="activo_bases" id="activo_bases" required>
                                    <option value selected disabled>--Seleccione--</option>
                                    <option value="true">Sí</option>
                                    <option value="false">No</option>
                                </select>
                            </div>
                        </div>
					</div>
                </div>
                <div class="modal-footer modal-body-color">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
