@section('style')

<style type="text/css">
/* Modal styles */
	.modal .modal-dialog {
		max-width: 400px;
	}
	.modal .modal-header, .modal .modal-body, .modal .modal-footer {
		padding: 20px 30px;
	}
	.modal .modal-content {
		border-radius: 3px;
	}
	.modal .modal-footer {
		background: #ecf0f1;
		border-radius: 0 0 3px 3px;
	}
    .modal .modal-title {
        display: inline-block;
    }
	.modal .form-control {
		border-radius: 2px;
		box-shadow: none;
		border-color: #dddddd;
	}
	.modal textarea.form-control {
		resize: vertical;
	}
	.modal .btn {
		border-radius: 2px;
		min-width: 100px;
	}
	.modal form label {
		font-weight: normal;
	}
	.autocomplete-items {
	  position: absolute;
	  border: 1px solid #d4d4d4;
	  border-bottom: none;
	  border-top: none;
	  z-index: 99;
	  /*position the autocomplete items to be the same width as the container:*/
	  top: 100%;
	  left: 0;
	  right: 0;
	}
</style>

@endsection

@include('tablas.alerts_table')
<div class="table-responsive tabla-resultados borde-top-bottom table-size">
    <table class="table table-sm table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Tabla</th>
                <th>Columna</th>
            </tr>
        </thead>
        <tbody>
        	@forelse($resultados as $resultado)
                <tr>
                    <td>{{$resultado->tabla}}</td>
                    <td>{{$resultado->columna}}</td>
                </tr>
            @empty
				<td colspan="2" class="alert-danger" align="left" style="padding-left:50px"><h3><b>Sin registros encontrados</b></h3></td>
            @endforelse
       </tbody>
    </table>
</div>
