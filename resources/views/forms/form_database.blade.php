<div class="form-general">
    <form action="{{ route('database') }}" method="get">
        <div class="row form-select-row">
            <div class="col-sm-4 form-group">
                <div class="input-group mb-3">
                    <label class="input-group-text">Data Base</label>
                    <select class="form-select" name="database" onChange="this.form.submit();" required>
                        <option disabled selected value>--Seleccione--</option>
                        @foreach($bases as $base)
                            <option>{{$base->datname}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>