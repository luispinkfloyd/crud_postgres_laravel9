@extends('layouts.app')

@section('style')
    <style type="text/css">
        .form-switch {
            display: flex !important;
            flex-direction: row-reverse !important;
            justify-content: space-between !important;
        }
        .div-switch-vpn {
            max-width: 300px !important;
        }
    </style>
@endsection

@section('content')

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

@if(isset($db_host) && isset($db_usuario))
    <div class="alert alert-success cartel-host text-center">
        <div class="row">
            <div class="col mb-1">
                <div class="row">
                    <div class="col mt-1 pt-1">
                        <h5><b><small class="fw-lighter">Host:</small> {{$db_host}}</b></h5>
                    </div>
                    <div class="col mt-1 pt-1">
                        <h5><b><small class="fw-lighter">Usuario:</small> {{$db_usuario}}</b></h5>
                    </div>
                </div>
            </div>
            <div class="col mb-1">
                <div class="row">
                    <div class="col mt-1">
                        <a class="btn btn-success" href="{{ route('home') }}">Volver a seleccionar todo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session()->get('buscador_string_view'))

    @include('forms.form_busqueda_string')
    @if(isset($resultados))
        @include('tablas.tabla_resultados_buscador_string')
    @endif
    <div class="container mt-12 text-center">
        <a class="btn btn-sm btn-info" href="{{ route('home') }}">Volver a seleccionar todo</a>
    </div>

@else


    @if(isset($bases))

        @include('forms.form_database')

    @elseif(isset($database) && !isset($schema))

        @include('forms.form_schema')

    @elseif(isset($schema))

        @include('forms.form_tabla')

        @if(isset($funcion))

            @include('funciones.funcion')

        @endif

        @if(isset($registros))

            @include('tablas.tabla_registros')
            @include('modals.modal_create')
            @include('modals.modal_delete')
            @include('modals.modal_edit')

        @endif

    @else

        @include('forms.form_host')

    @endif

@endif

@endsection