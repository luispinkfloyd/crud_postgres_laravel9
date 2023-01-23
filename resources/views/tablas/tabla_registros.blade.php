@if(isset($tabla_selected))
    @section('titulo', $tabla_selected.' - ')
@endif

@include('forms.form_busqueda')
@include('tablas.alerts_table')

<div class="tabla-resultados borde-bottom mb-0">
    <div class="row">
    	<div class="col text-end">
        	<a href="#addModal" class="btn btn-primary m-1" data-bs-toggle="modal">
                <i class="material-icons float-start pe-1">add</i>
                <span>Añadir Registro</span>
            </a>
        </div>
        <div class="col">
            @if($count_registros > 0)
            <form method="get" action="{{ route('export_excel') }}">
                <input type="hidden" name="database" value="{{$database}}">
                <input type="hidden" name="schema" value="{{$schema}}">
                <input type="hidden" name="tabla_selected" value="{{$tabla_selected}}">
                @if(isset($where1))
                    <input type="hidden" name="columna_selected1" value="{{$columna_selected1}}">
                    <input type="hidden" name="comparador1" value="{{$comparador1}}">
                    <input type="hidden" name="where1" value="{{$where1}}">
                @endif
				@if(isset($where2))
                    <input type="hidden" name="columna_selected2" value="{{$columna_selected2}}">
                    <input type="hidden" name="comparador2" value="{{$comparador2}}">
                    <input type="hidden" name="where2" value="{{$where2}}">
                @endif
                @if(isset($sort))
                    <input type="hidden" name="sort" value="{{$sort}}">
                @endif
                @if(isset($ordercol_def))
                	<input type="hidden" name="ordercol" value="{{$ordercol_def}}">
                @endif
                <button type="submit" class="btn btn-success m-1">
                    <img src="{{asset('img/excel.png')}}" height="20" class="float-start pe-2">
                    <span>Exportar a  Excel</span>
                </button>
            </form>
            @endif
        </div>
        <div class="col-8">
        	<h3 class="mt-1 text-white">Nombre de tabla:
                @if($schema == 'public')
                    <b>{{$tabla_selected}}</b> | 
                @else
                    <b>{{$schema.'.'.$tabla_selected}}</b> | 
                @endif
                <small>Total registros = 
                    <b>{{$count_registros}}</b>
                </small>
            </h3>
        </div>
    </div>
    @php($ordercol = 1)
</div>
<div class="table-responsive tabla-resultados borde-top mt-0">
    <table class="table table-sm table-dark table-bordered table-striped table-hover">
        <thead>
            <tr>
            <th scope="col"></th>
            @foreach($columnas as $columna)
                <th scope="col">{{$columna->column_name}}
                    <form method="get" action="{{route('tabla')}}" class="d-inline-block">
                        <input type="hidden" name="ordercol" value="{{$ordercol}}">
                        <input type="hidden" name="database" value="{{$database}}">
                        <input type="hidden" name="schema" value="{{$schema}}">
                        <input type="hidden" name="tabla_selected" value="{{$tabla_selected}}">
                        @if(isset($where1))
                            <input type="hidden" name="columna_selected1" value="{{$columna_selected1}}">
                            <input type="hidden" name="comparador1" value="{{$comparador1}}">
                            <input type="hidden" name="where1" value="{{$where1}}">
                        @endif
                        @if(isset($where2))
                            <input type="hidden" name="columna_selected2" value="{{$columna_selected2}}">
                            <input type="hidden" name="comparador2" value="{{$comparador2}}">
                            <input type="hidden" name="where2" value="{{$where2}}">
                        @endif
                        <input type="hidden" name="sort" value="asc">
                        <button type="submit" class="btn btn-sm btn-link pt-0 pb-0 pe-0 ps-0">
							<img src="{{ asset('img/arriba.png')}}" height="14">
                        </button>
                    </form>
                    <form method="get" action="{{route('tabla')}}" class="d-inline-block">
                        <input type="hidden" name="ordercol" value="{{$ordercol}}">
                        <input type="hidden" name="database" value="{{$database}}">
                        <input type="hidden" name="schema" value="{{$schema}}">
                        <input type="hidden" name="tabla_selected" value="{{$tabla_selected}}">
                        @if(isset($where1))
                            <input type="hidden" name="columna_selected1" value="{{$columna_selected1}}">
                            <input type="hidden" name="comparador1" value="{{$comparador1}}">
                            <input type="hidden" name="where1" value="{{$where1}}">
                        @endif
                        @if(isset($where2))
                            <input type="hidden" name="columna_selected2" value="{{$columna_selected2}}">
                            <input type="hidden" name="comparador2" value="{{$comparador2}}">
                            <input type="hidden" name="where2" value="{{$where2}}">
                        @endif
                        <input type="hidden" name="sort" value="desc">
                        <button type="submit" class="btn btn-sm btn-link pt-0 pb-0 pe-0 ps-0">
							<img src="{{ asset('img/abajo.png')}}" height="14">
                        </button>
                    </form>
                    <br>
                    <small>{{$columna->data_type}} @if($columna->required == 'YES') {{'(NULL)'}} @else {{'(NOT NULL)'}} @endif</small>
                </th>
                @php($ordercol++)
            @endforeach
            </tr>
        </thead>
        <tbody>
        	@forelse($registros as $registro)
                <tr>
                @foreach($columnas as $columna)
                    @php($primera_columna = $columna->column_name)
					@break
                @endforeach
                <td>
                	<a href="#deleteModal{{str_replace('.','_',$registro->$primera_columna)}}" class="delete" data-bs-toggle="modal"><i class="material-icons text-danger" data-bs-toggle="tooltip" title="Borrar">delete</i></a>
                    <a href="#editModal{{str_replace('.','_',$registro->$primera_columna)}}" class="edit" data-bs-toggle="modal"><i class="material-icons text-info" data-bs-toggle="tooltip" title="Editar">edit</i></a>
                </td>
                      @foreach($columnas as $columna)
                          <?php
                              $columna_registro = $columna->column_name;
                          ?>

                          	@if($charset_def !== 'UTF8')

                                @if($columna->data_type == 'boolean')

                                    <td>@if($registro->$columna_registro == true) {{'true'}} @else {{'false'}} @endif</td>

                                @else

                                    <td>{{utf8_encode($registro->$columna_registro)}}</td>

                                @endif


                            @else

                                @if($columna->data_type == 'boolean')

                                    <td>@if($registro->$columna_registro == true) {{'true'}} @else {{'false'}} @endif</td>

                                @else

                                    <td>{{$registro->$columna_registro}}</td>

                                @endif

                            @endif

                      @endforeach
                  </tr>
            @empty
				<?php
                    $count_columnas = count($columnas)+1;
                ?>
                	<td colspan="{{$count_columnas}}" class="alert-danger ps-5"><h3><b>Sin registros encontrados</b></h3></td>
            @endforelse
       </tbody>
    </table>
</div>
{{-- <div class="container div-paginacion text-center borde"> --}}
<div class="d-flex justify-content-center">
    {{$registros->withPath('tabla')->appends(request()->except('page'))->links()}}
</div>
<div class="alert alert-info mt-2 mb-0 ms-auto me-auto w-50" role="alert">
    <h1 class="display-6">Consulta</h1>
    <p class="lead"><b>{{$consulta_de_registros}}</b></p>
</div>
