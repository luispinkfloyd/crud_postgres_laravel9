<form id="database" action="{{ route('host') }}" method="post">
    @csrf
</form>
<div class="form-general">
    <form action="{{ route('schema') }}" method="get">
        <input type="hidden" name="database" value="{{$database}}">
        <div class="row form-select-row">
            <div class="col-sm-4">
                <div class="input-group flex-row mb-3">
                    <label class="input-group-text">
                        {{'Data Base'}}
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
                <div class="input-group mb-3">
                    <label class="input-group-text">Schemas</label>
                    <select class="form-select" name="schema" onChange="this.form.submit();" required>
                        <option disabled selected value>--Seleccione--</option>
                        @foreach($schemas as $schema)
                            <option>{{$schema->schema_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
