{{-- resources/views/grupos_bases/test_conexion.blade.php --}}
<div class="container mt-4 mb-4 borde p-4 bg-white" style="max-width: 600px">
    <h2 class="text-center">Probar conexión</h2>

    @if($conectado == 'conectado')
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>{{ 'Conexión exitosa.' }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @elseif($conectado == 'no_conectado')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ 'No se pudo conectar con los datos proporcionados.' }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <form action="{{ route('grupos_bases') }}" method="GET">
        <div class="mb-3">
            <label for="host" class="form-label">Host</label>
            <input type="text" name="host" id="host" pattern="^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$" placeholder="Ej: 192.168.1.1" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="port" class="form-label">Puerto</label>
            <input type="text" name="port" id="port" pattern="^([0-9]{1,4}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])$" placeholder="Ej: 5432" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuario</label>
            <input type="text" name="usuario" id="usuario" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="contrasenia" class="form-label">Contraseña</label>
            <input type="text" name="contrasenia" id="contrasenia" required class="form-control">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Probar conexión</button>
        </div>
    </form>
</div>