<?php

	function envioCorreoPhp ($destinatario, $asunto, $mensajeHtml) {
		
		$headers = array('Content-Type: text/html; charset=UTF-8'); //headers con soporte HTML
		wp_mail( $destinatario, $asunto, $mensajeHtml, $headers );
		
	}

	function enviarCorreoConfirmacionPago ($idReserva, $idPago) {
		
		$JSONdatosAloxamento = json_decode(buscarDatosReserva($idReserva, '_mphb_booking_price_breakdown'));

		// Envio correo confirmacion pago
		$plantillaConfirmacion = "";
		$plantillaConfirmacion = getPlantillaConfirmacionReserva ();
		$plantillaConfirmacion = str_replace("%ID_RESERVA%", $idReserva, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%BOOKING_KEY%", buscarDatosReserva($idReserva, "mphb_key"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%NOMBRE%", buscarDatosReserva($idReserva, "mphb_first_name"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%APELLIDO%", buscarDatosReserva($idReserva, "mphb_last_name"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%BASE_URL%", get_option('siteurl', false), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%PRECIO_TOTAL%", buscarDatosReserva($idReserva, "mphb_total_price"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%ALOJAMIENTO%", $JSONdatosAloxamento->rooms[0]->room->type, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%CORREO_ELECTRONICO%", buscarDatosReserva($idReserva, "mphb_email"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%TELEFONO_CONTACTO%", buscarDatosReserva($idReserva, "mphb_phone"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%TIPO_PAGO%", buscarDatosReserva($idPago, "_mphb_payment_type"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%ESTADO_PAGO%", comprobarEstadoPago ($idPago), $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%NUM_NINOS%", $JSONdatosAloxamento->rooms[0]->room->children, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%NUM_ADULTOS%", $JSONdatosAloxamento->rooms[0]->room->adults, $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%FECHA_LLEGADA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_in_date")), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%FECHA_SALIDA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_out_date")), $plantillaConfirmacion);
		
		$plantillaConfirmacion = str_replace("%HORA_PERMITIDA_LLEGADA%", get_option('booking_fecha_permitida_llegada', false), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%HORA_PERMITIDA_SALIDA%", get_option('booking_fecha_permitida_salida', false), $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%NOTA_CLIENTES%", buscarDatosReserva($idReserva, "mphb_note"), $plantillaConfirmacion);

		$asunto = "Confirmación Reserva #{$idReserva}";
		$destinatario = buscarDatosReserva($idReserva, "mphb_email");
		
		envioCorreoPhp($destinatario, $asunto, $plantillaConfirmacion);
	}

	function enviarCorreoRealizarPago ($idReserva) {
		
		$JSONdatosAloxamento = json_decode(buscarDatosReserva($idReserva, '_mphb_booking_price_breakdown'));

		// Url realizar pago
		$urlRealizarPago = get_option('siteurl', false) . "/confirmacion-de-reserva/reserva-confirmada/?booking_id={$idReserva}&booking_key=".buscarDatosReserva($idReserva, "mphb_key");

		// Envio correo confirmacion pago
		$plantillaRealizarPago = "";
		$plantillaRealizarPago = getPlantillaSolicitarPagoReservaCliente ();
		$plantillaRealizarPago = str_replace("%ID_RESERVA%", $idReserva, $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%BOOKING_KEY%", buscarDatosReserva($idReserva, "mphb_key"), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%NOMBRE%", buscarDatosReserva($idReserva, "mphb_first_name"), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%APELLIDO%", buscarDatosReserva($idReserva, "mphb_last_name"), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%BASE_URL%", get_option('siteurl', false), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%PRECIO_TOTAL%", buscarDatosReserva($idReserva, "mphb_total_price"), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%ALOJAMIENTO%", $JSONdatosAloxamento->rooms[0]->room->type, $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%CORREO_ELECTRONICO%", buscarDatosReserva($idReserva, "mphb_email"), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%TELEFONO_CONTACTO%", buscarDatosReserva($idReserva, "mphb_phone"), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%URL_PAGO%", $urlRealizarPago, $plantillaRealizarPago);

		$plantillaRealizarPago = str_replace("%NUM_NINOS%", $JSONdatosAloxamento->rooms[0]->room->children, $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%NUM_ADULTOS%", $JSONdatosAloxamento->rooms[0]->room->adults, $plantillaRealizarPago);

		$plantillaRealizarPago = str_replace("%FECHA_LLEGADA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_in_date")), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%FECHA_SALIDA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_out_date")), $plantillaRealizarPago);
		
		$plantillaRealizarPago = str_replace("%HORA_PERMITIDA_LLEGADA%", get_option('booking_fecha_permitida_llegada', false), $plantillaRealizarPago);
		$plantillaRealizarPago = str_replace("%HORA_PERMITIDA_SALIDA%", get_option('booking_fecha_permitida_salida', false), $plantillaRealizarPago);

		$plantillaRealizarPago = str_replace("%NOTA_CLIENTES%", buscarDatosReserva($idReserva, "mphb_note"), $plantillaRealizarPago);

		$asunto = "Realizar pago Reserva #{$idReserva}";
		$destinatario = buscarDatosReserva($idReserva, "mphb_email");
		
		envioCorreoPhp($destinatario, $asunto, $plantillaRealizarPago);
	}

	function enviarCorreoConfirmacionPagoAdmin ($idReserva, $idPago) {

		$JSONdatosAloxamento = json_decode(buscarDatosReserva($idReserva, '_mphb_booking_price_breakdown'));

		// Envio correo confirmacion pago
		$plantillaConfirmacion = "";
		$plantillaConfirmacion = getPlantillaConfirmacionReservaAdmin ();
		$plantillaConfirmacion = str_replace("%ID_RESERVA%", $idReserva, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%BOOKING_KEY%", buscarDatosReserva($idReserva, "mphb_key"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%NOMBRE%", buscarDatosReserva($idReserva, "mphb_first_name"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%APELLIDO%", buscarDatosReserva($idReserva, "mphb_last_name"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%BASE_URL%", get_option('siteurl', false), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%PRECIO_TOTAL%", buscarDatosReserva($idReserva, "mphb_total_price"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%ALOJAMIENTO%", $JSONdatosAloxamento->rooms[0]->room->type, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%CORREO_ELECTRONICO%", buscarDatosReserva($idReserva, "mphb_email"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%TELEFONO_CONTACTO%", buscarDatosReserva($idReserva, "mphb_phone"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%TIPO_PAGO%", buscarDatosReserva($idPago, "_mphb_payment_type"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%ESTADO_PAGO%", comprobarEstadoPago ($idPago), $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%NUM_NINOS%", $JSONdatosAloxamento->rooms[0]->room->children, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%NUM_ADULTOS%", $JSONdatosAloxamento->rooms[0]->room->adults, $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%FECHA_LLEGADA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_in_date")), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%FECHA_SALIDA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_out_date")), $plantillaConfirmacion);
		
		$plantillaConfirmacion = str_replace("%HORA_PERMITIDA_LLEGADA%", get_option('booking_fecha_permitida_llegada', false), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%HORA_PERMITIDA_SALIDA%", get_option('booking_fecha_permitida_salida', false), $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%NOTA_CLIENTES%", buscarDatosReserva($idReserva, "mphb_note"), $plantillaConfirmacion);

		$asunto = "ADMIN - Confirmación Reserva #{$idReserva}";
		$destinatario = get_option('booking_correos_admin', false);
		
		envioCorreoPhp($destinatario, $asunto, $plantillaConfirmacion);
	}
	
	function enviarCorreoFacturaCliente ($idReserva) {
		
		$JSONdatosAloxamento = json_decode(buscarDatosReserva($idReserva, '_mphb_booking_price_breakdown'));

		$urlFacturaCliente = get_option('siteurl', false) . "/wp-json/hotel/v2/descargarFacturaPDF/" . $idReserva . "/" . buscarDatosReserva($idReserva, "_mphb_checkout_id");

		// Envio correo confirmacion pago
		$plantillaConfirmacion = "";
		$plantillaConfirmacion = getPlantillaFacturaReserva ();
		$plantillaConfirmacion = str_replace("%ID_RESERVA%", $idReserva, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%BOOKING_KEY%", buscarDatosReserva($idReserva, "mphb_key"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%NOMBRE%", buscarDatosReserva($idReserva, "mphb_first_name"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%APELLIDO%", buscarDatosReserva($idReserva, "mphb_last_name"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%BASE_URL%", get_option('siteurl', false), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%PRECIO_TOTAL%", buscarDatosReserva($idReserva, "mphb_total_price"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%ALOJAMIENTO%", $JSONdatosAloxamento->rooms[0]->room->type, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%CORREO_ELECTRONICO%", buscarDatosReserva($idReserva, "mphb_email"), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%TELEFONO_CONTACTO%", buscarDatosReserva($idReserva, "mphb_phone"), $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%NUM_NINOS%", $JSONdatosAloxamento->rooms[0]->room->children, $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%NUM_ADULTOS%", $JSONdatosAloxamento->rooms[0]->room->adults, $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%FECHA_LLEGADA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_in_date")), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%FECHA_SALIDA%", transformarFechaCheckinCheckOut(buscarDatosReserva($idReserva, "mphb_check_out_date")), $plantillaConfirmacion);
		
		$plantillaConfirmacion = str_replace("%HORA_PERMITIDA_LLEGADA%", get_option('booking_fecha_permitida_llegada', false), $plantillaConfirmacion);
		$plantillaConfirmacion = str_replace("%HORA_PERMITIDA_SALIDA%", get_option('booking_fecha_permitida_salida', false), $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%NOTA_CLIENTES%", buscarDatosReserva($idReserva, "mphb_note"), $plantillaConfirmacion);

		$plantillaConfirmacion = str_replace("%URL_FACTURA_CLIENTE%", $urlFacturaCliente, $plantillaConfirmacion);

		$asunto = "Factura reserva #{$idReserva}";
		$destinatario = buscarDatosReserva($idReserva, "mphb_email");
		
		envioCorreoPhp($destinatario, $asunto, $plantillaConfirmacion);
	}
	
?>