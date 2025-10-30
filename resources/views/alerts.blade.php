@if(session()->get('mensaje_error'))
    <div class="alert alert-danger alert-dismissible fade show alert-style text-center" role="alert">
      <strong>{{ session()->get('mensaje_error') }}</strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if(session()->get('ok'))
    <div class="alert alert-success alert-dismissible fade show alert-style text-center" role="alert">
      <strong>{{ session()->get('ok') }}</strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif