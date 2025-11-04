{{-- resources/views/grupos_bases/test_conexion.blade.php --}}
<div class="container mt-4 mb-4 borde p-4 bg-white" style="max-width: 600px">
    <h2 class="text-center">Probar conexión</h2>

    @if($conectado == 'conectado')
        <div class="alert alert-info">{{ 'Conexión exitosa.' }}</div>
    @elseif($conectado == 'no_conectado')
        <div class="alert alert-danger">{{ 'No se pudo conectar con los datos proporcionados.' }}</div>
    @endif

    <form action="{{ route('grupos_bases') }}" method="GET">
        <div class="mb-3">
            <label for="host" class="form-label">Host</label>
            <input type="text" name="host" id="host" required class="form-control">
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