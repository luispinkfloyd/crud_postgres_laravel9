<table>
    <thead>
    	<tr>
            <td colspan="8">
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
            <th style="color:#FFFFFF;background-color:#160f30;">Red</th>
            <th style="color:#FFFFFF;background-color:#160f30;">Activo</th>
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
                <td>{{$dato->tipo_red}}</td>
                @if($dato->activo)
                    <td style="color: green">Sí</td>
                @else
                    <td style="color: red">No</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
