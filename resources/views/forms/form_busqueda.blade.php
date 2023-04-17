<div class="form-general">
    <form action="{{ route('tabla') }}" method="get" id="form_tabla">
        <input type="hidden" name="database" value="{{$database}}">
        <input type="hidden" name="schema" value="{{$schema}}">
        <input type="hidden" name="tabla_selected" value="{{$tabla_selected}}">
        @isset($sort)
            <input type="hidden" name="sort" value="{{$sort}}">
        @endisset
        @isset($ordercol_def)
            <input type="hidden" name="ordercol" value="{{$ordercol_def}}">
        @endisset
        {{-- Form 1 (siempre visible) --}}
        <div class="row mt-2 ms-1 me-1">
            <div class="col-sm-3">
                <div class="input-group flex-row mb-2">
                    <span class="input-group-text">Columna</span>
                    <select class="form-select" name="columna_selected1" id="columna_selected1" onChange="select_columna1()" required>
                        <option disabled selected value>--Seleccione--</option>
                        @foreach($columnas as $columna)
                            <option @if(isset($columna_selected1)) @if($columna_selected1 === $columna->column_name) {{'selected'}} @endif @endif>{{$columna->column_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="input-group flex-row mb-2">
                    <span class="input-group-text">Comparador</span>
                    <select class="form-select" name="comparador1" onchange="where_null()" id="comparador1" required>
                        <option value="ilike" @if(isset($comparador1)) @if($comparador1 === 'ilike') {{'selected'}} @endif @endif>Contiene</option>
                        <option value="=" @if(isset($comparador1)) @if($comparador1 === '=') {{'selected'}} @endif @endif>Igual</option>
                        <option value="<>" @if(isset($comparador1)) @if($comparador1 === '<>') {{'selected'}} @endif @endif>Distinto</option>
                        <option value="is_null" @if(isset($comparador1)) @if($comparador1 === 'is_null') {{'selected'}} @endif @endif>Is Null</option>
                        <option value="not_null" @if(isset($comparador1)) @if($comparador1 === 'not_null') {{'selected'}} @endif @endif>Is Not Null</option>
                        <option value=">=" @if(isset($comparador1)) @if($comparador1 === '>=') {{'selected'}} @endif @endif>Mayor o Igual</option>
                        <option value="<=" @if(isset($comparador1)) @if($comparador1 === '<=') {{'selected'}} @endif @endif>Menor o Igual</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <div class="input-group flex-row mb-2">
                        <span class="input-group-text" id="where1_span">Parámetro</span>
                        <input type="text" name="where1" id="where1" list="where1_datalist" autocomplete="off" class="form-control rounded-end" placeholder="Parámetro de búsqueda..." @if(isset($comparador1)) @if($comparador1 == 'is_null' || $comparador1 == 'not_null') {{'disabled'}} @else {{'required'}} @endif @else {{'required'}} @endif @if(isset($where1)) value="{{$where1}}" @endif>
                        <datalist id="where1_datalist">
                        </datalist>
                    </div>
                </div>
            </div>
            {{-- <div class="col-sm-1">
                <div class="custom-control custom-checkbox" id="check_acentos_1" style="top:7px" @if(isset($where2)) {{'hidden'}} @endif>
                <input type="checkbox" name="caracteres_raros" class="custom-control-input" id="customCheck1" @if($caracteres_raros === 'S') {{'checked'}} @endif>
                <span class="custom-control-span" for="customCheck1">Acentos</span>
                </div>
            </div> --}}
            <div class="col-sm-2 text-center" id="div_botones_1_consulta_1" @if(isset($where2)) {{'hidden'}} @endif>
                <button type="submit" class="btn btn-primary d-inline-block" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Buscar">
                    <img src="{{asset('img/lupa.png')}}" height="20">
                </button>
                <button type="button" class="btn btn-danger d-inline-block" onclick="javascript:location.href='tabla?ordercol={{$ordercol_def}}&limpiar=1&database={{$database}}&schema={{$schema}}&tabla_selected={{$tabla_selected}}&sort={{$sort}}'" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Limpiar búsqueda">
                    <img src="{{asset('img/limpiar.png')}}" height="20">
                </button>
                <button type="button" class="btn btn-outline-success d-inline-block" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Agregar segunda búsqueda" onClick="agregar_busqueda_1()">+</button>
            </div>
            <div class="col-sm-2 text-center" id="div_botones_2_consulta_1" hidden="">
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Quitar segunda búsqueda" onClick="quitar_busqueda_1()">-</button>
            </div>
        </div>
        {{-- Fin del form 1 --}}
        {{-- Form 2 (visible sólo cuando se selecciona o está seteado where2) --}}
        <div class="row mt-2 ms-1 me-1" id="div_consulta_2" @if(!isset($where2)) {{'hidden'}} @endif>
            <div class="col-sm-3">
                <div class="input-group flex-row mb-2">
                    <span class="input-group-text">Columna</span>
                    <select class="form-select" name="columna_selected2" id="columna_selected2" onChange="select_columna2()">
                        <option disabled selected value>--Seleccione--</option>
                        @foreach($columnas as $columna)
                            <option @if(isset($columna_selected2)) @if($columna_selected2 === $columna->column_name) {{'selected'}} @endif @endif>{{$columna->column_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="input-group flex-row mb-2">
                    <span class="input-group-text">Comparador</span>
                    <select class="form-select" name="comparador2" onchange="where_null()" id="comparador2">
                        <option value="ilike" @if(isset($comparador2)) @if($comparador2 === 'ilike') {{'selected'}} @endif @endif>Contiene</option>
                        <option value="=" @if(isset($comparador2)) @if($comparador2 === '=') {{'selected'}} @endif @endif>Igual</option>
                        <option value="<>" @if(isset($comparador2)) @if($comparador2 === '<>') {{'selected'}} @endif @endif>Distinto</option>
                        <option value=">=" @if(isset($comparador2)) @if($comparador2 === '>=') {{'selected'}} @endif @endif>Mayor o Igual</option>
                        <option value="<=" @if(isset($comparador2)) @if($comparador2 === '<=') {{'selected'}} @endif @endif>Menor o Igual</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <div class="input-group flex-row mb-2">
                        <span class="input-group-text" id="where2_span">Parámetro</span>
                        <input type="text" name="where2" id="where2" list="where2_datalist" autocomplete="off" class="form-control rounded-end" placeholder="Parámetro de búsqueda..." @if(isset($where2)) value="{{$where2}}" @endif>
                        <datalist id="where2_datalist">
                        </datalist>
                    </div>
                </div>
            </div>
            {{-- <div class="col-sm-1">
                <div class="custom-control custom-checkbox" style="top:7px">
                <input type="checkbox" name="caracteres_raros" class="custom-control-input" id="customCheck1" @if($caracteres_raros === 'S') {{'checked'}} @endif>
                <span class="custom-control-span" for="customCheck1">Acentos</span>
                </div>
            </div> --}}
            <div class="col-sm-2 text-center">
                <button type="submit" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Buscar"><img src="{{asset('img/lupa.png')}}" height="20"></button>&nbsp;
                <button type="button" class="btn btn-danger" onclick="javascript:location.href='tabla?ordercol={{$ordercol_def}}&limpiar=1&database={{$database}}&schema={{$schema}}&tabla_selected={{$tabla_selected}}&sort={{$sort}}'" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Limpiar búsqueda"/><img src="{{asset('img/limpiar.png')}}" height="20"></button>
            </div>
        </div>
        {{-- Fin del form 2 --}}

    </form>
</div>

 @section('script')
 <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

 <script type="text/javascript">

 	function select_columna1(){

		$('#where1_datalist').empty();
		var consulta1;
        //hacemos focus al campo de búsqueda
        $("#columna_selected1").focus();
        //obtenemos el texto introducido en el campo de búsqueda
        consulta1 = $("#columna_selected1").val();
        $.ajax({
              type: "GET",
              url: "{{ route('ajax_columna')}}",
              data: "columna="+consulta1,
              dataType: "json",
              error: function(){
                    alert("error petición ajax");
              },
              success: function(result){

                  $.each( result, function(k,v) {

                        $('#where1_datalist').append($('<option>', {text:v}));
                  });

              }
        });

	}

	function select_columna2(){

		$('#where2_datalist').empty();

		var consulta2;

        //hacemos focus al campo de búsqueda
        $("#columna_selected2").focus();

        //obtenemos el texto introducido en el campo de búsqueda
        consulta2 = $("#columna_selected2").val();


		//alert(consulta)

        //hace la búsqueda

        $.ajax({
              type: "GET",
              url: "{{ route('ajax_columna')}}",
              data: "columna="+consulta2,
              dataType: "json",
              error: function(){
                    alert("error petición ajax");
              },
              success: function(result){

                  $.each( result, function(k,v) {

                        $('#where2_datalist').append($('<option>', {text:v}));
                  });

              }
        });

	}

	function agregar_busqueda_1(){

		document.getElementById("div_consulta_2").removeAttribute("hidden");

		document.getElementById("div_botones_1_consulta_1").setAttribute("hidden","true");

        //document.getElementById("div_botones_1_consulta_1").hidden = true;;

        document.getElementById("div_botones_2_consulta_1").removeAttribute("hidden");

		document.getElementById("columna_selected2").setAttribute("required","true");

		document.getElementById("where2").setAttribute("required","true");

		//document.getElementById("check_acentos_1").setAttribute("hidden","true"); //se eliminó temporalmente

	}

	function quitar_busqueda_1(){

		document.getElementById("div_consulta_2").setAttribute("hidden","true");

		document.getElementById("div_botones_1_consulta_1").removeAttribute("hidden");

		document.getElementById("div_botones_2_consulta_1").setAttribute("hidden","true");

		document.getElementById("columna_selected2").removeAttribute("required");

		document.getElementById("where2").removeAttribute("required");

		document.getElementById("columna_selected2").value = '';

		document.getElementById("where2").value = null;

		//document.getElementById("check_acentos_1").removeAttribute("hidden"); //se eliminó temporalmente

	}

    function where_null(){

        var comparador1 = document.getElementById("comparador1").value;

        if(comparador1 == 'is_null' || comparador1 == 'not_null'){
            document.getElementById("where1").setAttribute("disabled","true");
            document.getElementById("where1").removeAttribute("required");
        }else{
            document.getElementById("where1").setAttribute("required","true");
            document.getElementById("where1").removeAttribute("disabled");
        }

    }

 </script>

 @endsection
