<div class="container modal-size mt-1 mb-0 ms-auto me-auto">
    @if(session()->get('registro_agregado'))
        <div class="alert alert-success alert-dismissible text-center mt-3 mb-5 ms-auto me-auto fade show" role="alert">
          <strong>{{ session()->get('registro_agregado') }}</strong>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @elseif(session()->get('registro_actualizado'))
        <div class="alert alert-success alert-dismissible text-center mt-3 mb-5 ms-auto me-auto fade show" role="alert">
          <strong>{{ session()->get('registro_actualizado') }}</strong>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @elseif(session()->get('registro_eliminado'))
        <div class="alert alert-success alert-dismissible text-center mt-3 mb-5 ms-auto me-auto fade show" role="alert">
          <strong>{{ session()->get('registro_eliminado') }}</strong>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @elseif(session()->get('registro_no_modificado'))
        <div class="alert alert-warning alert-dismissible text-center mt-3 mb-5 ms-auto me-auto fade show" role="alert">
          <strong>{{ session()->get('registro_no_modificado') }}</strong>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
</div>