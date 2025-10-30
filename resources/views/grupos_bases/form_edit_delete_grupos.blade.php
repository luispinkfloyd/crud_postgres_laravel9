<div class="modal fade" id="modal-form-edit-delete-grupos" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-color">
                <h4>Grupos</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body modal-body-color">
				@foreach ($grupos as $grupo)
                    <form action="{{route('edit_grupo', $grupo->id)}}" method="POST">
                    @csrf
                        <div class="row form-group">
                            <div class="col-sm">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" aria-describedby="nombre_grupo" id="nombre_grupo" name="nombre_grupo" value="{{$grupo->nombre}}" required>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary me-1">Modificar</button>
                                </form>
                                <form action="{{ route('delete_grupo', $grupo->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de borrar el grupo {{$grupo->nombre}}? Esta acción no se puede deshacer.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Borrar</button>
                                </form>
                            </div>
                        </div>
                @endforeach
            </div>
            <div class="modal-footer modal-header-color">
                <button type="button" class="btn btn-secondary m-auto" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
