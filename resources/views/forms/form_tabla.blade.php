<form id="database" action="{{ route('host') }}" method="post">
    @csrf
</form>
<form id="schema" action="{{ route('database') }}" method="get">
    <input type="hidden" name="database" value="{{$database}}">
</form>
<form id="table" action="{{ route('schema') }}" method="get">
    <input type="hidden" name="database" value="{{$database}}">
    <input type="hidden" name="schema" value="{{$schema}}">
</form>
<div class="form-general">
    <form action="{{ route('tabla') }}" method="get">
        <input type="hidden" name="database" value="{{$database}}">
        <input type="hidden" name="schema" value="{{$schema}}">
        <input type="hidden" name="selector_table_function" value="{{$selector_table_function_selected}}">
        <div class="row mt-2 ms-1 me-1">
            <div class="col-sm-4">
                <div class="input-group mb-2 flex-row">
                    <label class="input-group-text">
                        Data Base
                        <button class="btn btn-sm btn-link ml-2 pe-0 ps-1 pt-0 pb-0" type="submit" form="database">
                            <img src="{{ asset('img/recargar.png')}}" height="15">
                        </button>
                    </label>
                    <select class="form-select" disabled>
                        <option selected>{{$database}}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group mb-2 flex-row">
                    <label class="input-group-text">
                        Schemas
                        <button class="btn btn-sm btn-link ml-2 pe-0 ps-1 pt-0 pb-0" type="submit" form="schema">
                            <img src="{{ asset('img/recargar.png')}}" height="15">
                        </button>
                    </label>
                    <select class="form-select" disabled>
                        <option selected>{{$schema}}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="tablas_span">
                            <select class="select-form" name="selector_table_function" onChange="this.form.submit();" form="table" required>
                                <option @if(isset($selector_table_function_selected)) @if($selector_table_function_selected == 'table') {{'selected'}} @endif @endif value="table">Tablas</option>
                                <option @if(isset($selector_table_function_selected)) @if($selector_table_function_selected == 'function') {{'selected'}} @endif @endif value="function">Funciones</option>
                            </select>
                        </span>
                        <input type="text" autocomplete="off" name="tabla_selected" list="list_tablas" class="form-control rounded-end" onChange="this.form.submit();" required @isset($tabla_selected) {{'value='.$tabla_selected}} @endisset>
                        <datalist id="list_tablas">
                            @foreach($tablas as $tabla)
                                <option>{{$tabla->table_name}}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>