function crearDatosReservaPost() {

    // Crear/Actualizar Checkip
    var jsonCrearReserva = new Object();
    jsonCrearReserva.idUsuarioInterno = clienteAux.idInterno;
    jsonCrearReserva.nombreCliente = clienteAux.nombreCliente;
    jsonCrearReserva.apellidoCliente = clienteAux.apellidoCliente;
    jsonCrearReserva.identificadorIdentidad = clienteAux.identificadorIdentidad;
    jsonCrearReserva.requierDNI = clienteAux.requierDNI;

    $.ajax({
        url: "/wp-json/hotel/v2/crearReservaNew/", // url del recurso
        headers: { 'authorization': authorization },
        type: "post",
        data: JSON.stringify(jsonCrearReserva), // datos a pasar al servidor, en caso de necesitarlo
        success: function (r) {
            alert("Creada reserva de cliente correctamente");
        }
    });

}