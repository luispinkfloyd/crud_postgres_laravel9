@foreach($datos as $dato)
    <div class="modal fade" id="modal_edit_base{{$dato->id}}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form action="{{route('edit_base', $dato->id)}}" method="POST">
                @csrf
                <div class="modal-content ">
                    <div class="modal-header modal-header-color">
                        <h4>Modificar Servidor</h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body modal-body-color">
                        <div class="row form-group">
                            <div class="col-sm">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="servidor_bases">Nombre del Servidor <small class="small-color">(*)</small></span>
                                    <input type="text" class="form-control" aria-describedby="servidor_bases" id="servidor_bases" name="servidor_bases" value="{{$dato->servidor}}" required>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="grupo_bases">Grupo <small class="small-color">(*)</small>:</label>
                                    <select class="form-select" name="grupo_bases" id="grupo_bases" required>
                                        <option value selected disabled>--Seleccione--</option>
                                        @foreach ($grupos as $grupo)
                                            <option @if($dato->grupo_relacion->id == $grupo->id) {{'selected'}} @endif value="{{$grupo->id}}">{{$grupo->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-sm-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="host_bases">Host <small class="small-color">(*)</small></span>
                                    <input type="text" class="form-control" aria-describedby="host_bases" id="host_bases" name="host_bases" value="{{$dato->host}}" required>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="usuario_bases">Usuario <small class="small-color">(*)</small></span>
                                    <input type="text" class="form-control" aria-describedby="usuario_bases" id="usuario_bases" name="usuario_bases" value="{{$dato->usuario}}" required>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="password_bases">Contraseña <small class="small-color">(*)</small></span>
                                    <input type="text" class="form-control" aria-describedby="password_bases" id="password_bases" name="password_bases" value="{{$dato->password}}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-body-color">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Modificar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach
