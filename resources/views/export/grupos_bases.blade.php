<table>
    <thead>
    	<tr>
            <td colspan="6">
                <p><b>Grupos y bases (hosts)</b></p>
            </td>
        </tr>
        <tr>
            <th style="color:#FFFFFF;background-color:#160f30;">ID</th>
            <th style="color:#FFFFFF;background-color:#160f30;">Nombre</th>
            <th style="color:#FFFFFF;background-color:#160f30;">IP (host)</th>
            <th style="color:#FFFFFF;background-color:#160f30;">Usuario</th>
            <th style="color:#FFFFFF;background-color:#160f30;">Contraseña</th>
            <th style="color:#FFFFFF;background-color:#160f30;">Grupo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datos as $dato)
            <tr>
                <td>{{$dato->id}}</td>
                <td>{{$dato->servidor}}</td>
                <td>{{$dato->host}}</td>
                <td>{{$dato->usuario}}</td>
                <td>{{$dato->password}}</td>
                <td>
                    @if($dato->grupo_relacion)
                        {{$dato->grupo_relacion->nombre}}
                    @else
                        Sin grupo
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
