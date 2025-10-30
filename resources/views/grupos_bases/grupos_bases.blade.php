@extends('layouts.app')

@section('content')

@include('alerts')

<div class="tabla-resultados borde-bottom mb-0">
    <div class="row">
        <div class="col-auto">
            <a href="#modal-form-grupos" class="btn btn-secondary m-2" data-bs-toggle="modal" data-bs-target="#modal-form-grupos">Crear nuevo grupo</a>
        </div>
        <div class="col-auto">
        	<a href="#modal-form-bases" class="btn btn-primary @if(count($grupos) < 1) {{'disabled'}} @endif m-2" data-bs-toggle="modal" data-bs-target="#modal-form-bases" @if(count($grupos) < 1) {{'aria-disabled="true"'}} @endif>Crear nuevo host</a>
            <br>
            <small class="small-color">(*) Solo se activa si hay creado al menos un grupo.</small>
        </div>
        <div class="col-6 text-center">
            <h3 class="mt-1 text-white">Grupos y Hosts 
                <small>Total registros = 
                    <b>{{$count_datos}}</b>
                </small>
            </h3>
        </div>
        <div class="col-auto text-end">
            @if($count_datos > 0)
                <form method="get" action="{{route('exportar_grupos_bases_excel')}}">
                    <button type="submit" class="btn btn-success m-1">
                        <img src="{{asset('img/excel.png')}}" height="20" class="float-start pe-2">
                        <span>Exportar a  Excel</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
<div class="table-responsive tabla-resultados borde-top mt-0">
    <table class="table table-sm table-dark table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th scope="col" class="text-center"></th>
                <th scope="col" class="text-center">ID</th>
                <th scope="col" class="text-center">Nombre</th>
                <th scope="col" class="text-center">IP (host)</th>
                <th scope="col" class="text-center">Usuario</th>
                <th scope="col" class="text-center">Contraseña</th>
                <th scope="col" class="text-center">
                    Grupo (<a href="#modal-form-edit-delete-grupos" data-bs-toggle="modal" data-bs-target="#modal-form-edit-delete-grupos" title="Editar grupos" class="edit">Editar grupos</a>)
                </th>
            </tr>
        </thead>
        <tbody>
            @if($count_datos == 0)
                <tr>
                    <td colspan="7" class="text-center">No hay registros disponibles.</td>
                </tr>
            @else
                @foreach($datos as $dato)
                    <tr>
                        <td class="text-center align-middle"  style="max-width: 30px">
                            <a href="#modal_edit_base{{$dato->id}}" data-bs-toggle="modal" data-bs-target="#modal_edit_base{{$dato->id}}" class="delete"><i class="material-icons text-info" data-bs-toggle="tooltip" title="Editar">edit</i></a>
                            <a href="#modal_delete_base{{$dato->id}}" data-bs-toggle="modal" data-bs-target="#modal_delete_base{{$dato->id}}" class="edit me-1"><i class="material-icons text-danger" data-bs-toggle="tooltip" title="Borrar">delete</i></a>
                        </td>
                        <th scope="row" class="text-center align-middle">{{$dato->id}}</th>
                        <td class="align-middle">{{$dato->servidor}}</td>
                        <td class="align-middle">{{$dato->host}}</td>
                        <td class="text-center align-middle">{{$dato->usuario}}</td>
                        <td class="align-middle">{{$dato->password}}</td>
                        <td class="align-middle">
                            @if($dato->grupo_relacion)
                                {{$dato->grupo_relacion->nombre}}
                            @else
                                <em class="text-muted">Sin grupo</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
       </tbody>
    </table>
</div>
{{-- <div class="container div-paginacion text-center borde"> --}}
<div class="d-flex justify-content-center">
    {{$datos->withPath('grupos_bases')->appends(request()->except('page'))->links()}}
</div>
<div class="d-flex justify-content-center mt-3">
    <a href="{{route('home')}}" class="btn btn-link mb-2 me-2">Volver</a>
</div>

@include('grupos_bases.form_grupos')

@if(count($grupos) > 0)
    @include('grupos_bases.form_bases')
    @include('grupos_bases.form_edit_bases')
    @include('grupos_bases.form_delete_bases')
    @include('grupos_bases.form_edit_delete_grupos')
@endif

@endsection