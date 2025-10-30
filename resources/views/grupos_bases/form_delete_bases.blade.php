@foreach($datos as $dato)
    <div class="modal fade" id="modal_delete_base{{$dato->id}}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{route('delete_base', $dato->id)}}" method="POST">
                @csrf
                <div class="modal-content ">
                    <div class="modal-header modal-header-color">
                        <h4>Eliminar Servidor</h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body modal-body-color">
                        <div class="row form-group">
                            <div class="col-sm">
                                <h6>¿Está seguro que desea eliminar el servidor <b>{{$dato->servidor}}</b> con host <b>{{$dato->host}}</b>?</h6>
                                <small class="small-color">Esta acción no se puede deshacer.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-body-color">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">NO (Cerrar ventana)</button>
                        <button type="submit" class="btn btn-danger">SÍ (Eliminar)</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach
