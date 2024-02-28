<table>
	<tr>
		<td>Host:</td><td style="color: #2A267C">{{$db_host}}</td>
    </tr>
    <tr>
        <td>Usuario:</td><td style="color: #2A267C">{{$db_usuario}}</td>
    </tr>
    <tr>
        <td>Data Base:</td><td style="color: #2A267C">{{$database}}</td>
    </tr>
    <tr>
        <td>Tabla:</td><td style="color: #2A267C">{{$tabla_selected}}</td>
    </tr>
</table>
@if(isset($where1))
<table>
	<tr>
    	<td>Columna1:</td><td style="color: #2A267C">{{$columna_selected1}}</td>
		@if(isset($where2))
			<td>Columna2:</td><td style="color: #2A267C">{{$columna_selected2}}</td>
		@endif
    </tr>
    <tr>
        <td>Comparador1:</td><td style="color: #2A267C">{{$comparador1}}</td>
		@if(isset($where2))
			<td>Comparador2:</td><td style="color: #2A267C">{{$comparador2}}</td>
		@endif
    </tr>
    <tr>
        <td>Parámetro1:</td><td style="color: #2A267C">{{$where1}}</td>
		@if(isset($where2))
			<td>Parámetro2:</td><td style="color: #2A267C">{{$where2}}</td>
		@endif
    </tr>
    
</table>
@endif
<table class="registros">
    <tbody>
        <tr>
        @foreach($columnas as $columna)
            <td style="color:#FFFFFF;background-color:#160f30;">{{$columna->column_name}} ({{$columna->data_type}})</td>
        @endforeach
        </tr>
        @forelse($registros as $registro)
            <tr>
                  @foreach($columnas as $columna)
                      <?php 
                          $columna_registro = $columna->column_name;
                      ?>
                      
                      
                      
                          @if($charset_def !== 'UTF8')
                          
                            <td>{{utf8_encode($registro->$columna_registro)}}</td>
                          
                          @else
                          
                            <td>{{$registro->$columna_registro}}</td>
                          
                          @endif
                          
                           
                  @endforeach
              </tr>
        @empty
        
        @endforelse      
    </tbody>
</table>