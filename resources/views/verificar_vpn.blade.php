<div class="container mt-3">
    <div class="row form-group">
        <div class="col-sm text-white text-end h4">
            VPN Rectorado: 
        </div>
        <div class="col-sm text-start h4
        @if($vpn_rectorado == 'Sin verificar') {{'text-warning'}} @endif
        @if($vpn_rectorado == 'Conectado') {{'text-success'}} @endif
        @if($vpn_rectorado == 'Desconectado') {{'text-danger'}} @endif
        ">
            {{$vpn_rectorado}}
        </div>
    </div>
    {{-- <div class="row form-group">
        <div class="col-sm text-white text-end h4">
            VPN Arsat: 
        </div>
        <div class="col-sm text-start h4
        @if($vpn_arsat == 'Sin verificar') {{'text-warning'}} @endif
        @if($vpn_arsat == 'Conectado') {{'text-success'}} @endif
        @if($vpn_arsat == 'Desconectado') {{'text-danger'}} @endif
        ">
            {{$vpn_arsat}}
        </div>
    </div> --}}
</div>