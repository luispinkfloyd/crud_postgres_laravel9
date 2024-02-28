<div class="container form-general">
    <form action="{{ route('host') }}" method="post">
        @csrf
        <div class="row form-group">
            <div class="col-sm">
                <h3 class="pl-3 pt-1 text-white">Buscador por tablas</h3>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="db_grupo">Grupo</label>
                    <select class="form-select" name="db_grupo" id="db_grupo" required>
                        <option value selected disabled>--Seleccione--</option>
                        @foreach ($grupos as $grupo)
                            <option value="{{$grupo->id}}">{{$grupo->nombre}}</option>
                        @endforeach
                    </select>
                </div>  
            </div>
            <div class="col-sm">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="db_host">Host</label>
                    <select class="form-select" id="db_host" name="db_host" required disabled>
                        <option value selected disabled>--Seleccione--</option>
                    </select>
                </div>  
            </div>
        </div>
        <div class="row form-group">
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success mb-2 me-2">Seleccionar</button>
                <a href="{{route('home')}}" class="btn btn-danger mb-2 me-2">Limpiar</a>
            </div>
        </div>
    </form>
</div>
<div class="container mt-3 text-center">
    <div class="row form-group">
        <div class="col-sm">
            <a href="#modal-form-grupos" class="btn btn-secondary mb-2 me-2" data-bs-toggle="modal" data-bs-target="#modal-form-grupos">Crear nuevo grupo</a>
        </div>
        <div class="col-sm">
            <a href="#modal-form-bases" class="btn btn-primary @if(count($grupos) < 1) {{'disabled'}} @endif mb-2 me-2" data-bs-toggle="modal" data-bs-target="#modal-form-bases" @if(count($grupos) < 1) {{'aria-disabled="true"'}} @endif>Crear nuevo host</a>
            <br>
            <small class="small-color">(*) Solo se activa si hay creado al menos un grupo.</small>
        </div>
    </div>  
</div>
@include('forms.form_grupos')

@if(count($grupos) > 0)
    @include('forms.form_bases')
@endif

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">

    $(document).ready(function() {
        
        $('#db_grupo').on('change', function (){
            var db_grupo = $(this).val();
            $('#db_host').empty().prop("disabled", true);
            $('#db_host').append($('<option>',{value:"",text:"--Seleccione--"}).prop("disabled", true).prop("selected", true));
            $.ajax({
                type:"GET",
                url:"{{url('verificar_grupo')}}?grupo_selected="+db_grupo,
                success:function(new_res){
                    if(new_res){
                        $('#db_host').empty().removeAttr("disabled");
                        $('#db_host').append($('<option>',{value:"",text:"--Seleccione--"}).prop("disabled", true).prop("selected", true));
                        $.each(new_res, function(k,v) {
                            $('#db_host').append($('<option>',{
                                value: v['id'],
                                text: v['servidor']
                            }));
                        });
                    }
                }
            });
        });
    });
</script>
 @endsection
