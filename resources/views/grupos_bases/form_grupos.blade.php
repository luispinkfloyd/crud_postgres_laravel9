<div class="modal fade" id="modal-form-grupos" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{route('create_grupo')}}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header modal-header-color">
                    <h4>Crear Grupo</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body modal-body-color">
					<div class="row form-group">
                        <div class="col-sm">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="nombre_grupo">Nombre <small class="small-color">(*)</small></span>
                                <input type="text" class="form-control" aria-describedby="nombre_grupo" id="nombre_grupo" name="nombre_grupo" required>
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
