<?php

function notificarCobroOnline ($idReserva, $idTransaccion,  $estado) {
	
	// Buscar id ultimo post
	$idPost=buscarIdPostNew();
	
	// Crear post de pago
	insertarPost($idPost, date("Y-m-d h:i:sa.000"), $estado);
	
	insertarDatosPostMeta($idPost, "_mphb_key", buscarDatosReserva($idReserva, "mphb_key"));
	insertarDatosPostMeta($idPost, "_mphb_logs", "");
	insertarDatosPostMeta($idPost, "_edit_lock", "");
	insertarDatosPostMeta($idPost, "_edit_last", "1");
	insertarDatosPostMeta($idPost, "_id", "{$idPost}");
	insertarDatosPostMeta($idPost, "_mphb_gateway", "2checkout");
	insertarDatosPostMeta($idPost, "_mphb_gateway_mode", "live");
	insertarDatosPostMeta($idPost, "_mphb_amount", buscarDatosReserva($idReserva, "mphb_total_price"));
	insertarDatosPostMeta($idPost, "_mphb_fee", buscarDatosReserva($idReserva, "mphb_total_price"));
	insertarDatosPostMeta($idPost, "_mphb_currency", "EUR");
	insertarDatosPostMeta($idPost, "_mphb_payment_type", "Redsys");
	insertarDatosPostMeta($idPost, "_mphb_transaction_id", $idTransaccion);
	insertarDatosPostMeta($idPost, "_mphb_booking_id", $idReserva);
	insertarDatosPostMeta($idPost, "_mphb_first_name", buscarDatosReserva($idReserva, "mphb_first_name"));
	insertarDatosPostMeta($idPost, "_mphb_last_name", buscarDatosReserva($idReserva, "mphb_last_name"));
	insertarDatosPostMeta($idPost, "_mphb_email", buscarDatosReserva($idReserva, "mphb_email"));
	insertarDatosPostMeta($idPost, "_mphb_phone", buscarDatosReserva($idReserva, "mphb_phone"));
	insertarDatosPostMeta($idPost, "_mphb_country", buscarDatosReserva($idReserva, "mphb_country"));
	insertarDatosPostMeta($idPost, "_mphb_address1", "");
	insertarDatosPostMeta($idPost, "_mphb_address2", "");
	insertarDatosPostMeta($idPost, "_mphb_city", "");
	insertarDatosPostMeta($idPost, "_mphb_state", "");
	insertarDatosPostMeta($idPost, "_mphb_zip", "");
	insertarDatosPostMeta($idPost, "wptr_hide_title", "");
	
}

function notificarCobroTransferencia ($idReserva) {
	
	// Buscar id ultimo post
	$idPost=buscarIdPostNew();
	
	// Crear post de pago
	insertarPost($idPost, date("Y-m-d h:i:sa.000"), "mphb-p-on-hold");
	
	insertarDatosPostMeta($idPost, "_mphb_key", buscarDatosReserva($idReserva, "mphb_key"));
	insertarDatosPostMeta($idPost, "_mphb_logs", "");
	insertarDatosPostMeta($idPost, "_edit_lock", "");
	insertarDatosPostMeta($idPost, "_edit_last", "1");
	insertarDatosPostMeta($idPost, "_id", "{$idPost}");
	insertarDatosPostMeta($idPost, "_mphb_gateway", "bank");
	insertarDatosPostMeta($idPost, "_mphb_gateway_mode", "live");
	insertarDatosPostMeta($idPost, "_mphb_amount", buscarDatosReserva($idReserva, "mphb_total_price"));
	insertarDatosPostMeta($idPost, "_mphb_fee", buscarDatosReserva($idReserva, "mphb_total_price"));
	insertarDatosPostMeta($idPost, "_mphb_currency", "EUR");
	insertarDatosPostMeta($idPost, "_mphb_payment_type", "Transferencia bancaria");
	insertarDatosPostMeta($idPost, "_mphb_transaction_id", $idReserva);
	insertarDatosPostMeta($idPost, "_mphb_booking_id", $idReserva);
	insertarDatosPostMeta($idPost, "_mphb_first_name", buscarDatosReserva($idReserva, "mphb_first_name"));
	insertarDatosPostMeta($idPost, "_mphb_last_name", buscarDatosReserva($idReserva, "mphb_last_name"));
	insertarDatosPostMeta($idPost, "_mphb_email", buscarDatosReserva($idReserva, "mphb_email"));
	insertarDatosPostMeta($idPost, "_mphb_phone", buscarDatosReserva($idReserva, "mphb_phone"));
	insertarDatosPostMeta($idPost, "_mphb_country", buscarDatosReserva($idReserva, "mphb_country"));
	insertarDatosPostMeta($idPost, "_mphb_address1", "");
	insertarDatosPostMeta($idPost, "_mphb_address2", "");
	insertarDatosPostMeta($idPost, "_mphb_city", "");
	insertarDatosPostMeta($idPost, "_mphb_state", "");
	insertarDatosPostMeta($idPost, "_mphb_zip", "");
	insertarDatosPostMeta($idPost, "wptr_hide_title", "");
	
	// Modificar reserva pendiente usuario - pending
	modificarEstadoPost($idReserva, "pending");
	
}

function notificarCobroManual ($idReserva) {
	
	// Buscar id ultimo post
	$idPost=buscarIdPostNew();
	
	// Crear post de pago
	insertarPost($idPost, date("Y-m-d h:i:sa.000"), "mphb-p-completed");
	
	insertarDatosPostMeta($idPost, "_mphb_key", buscarDatosReserva($idReserva, "mphb_key"));
	insertarDatosPostMeta($idPost, "_mphb_logs", "");
	insertarDatosPostMeta($idPost, "_edit_lock", "");
	insertarDatosPostMeta($idPost, "_edit_last", "1");
	insertarDatosPostMeta($idPost, "_id", "{$idPost}");
	insertarDatosPostMeta($idPost, "_mphb_gateway", "manual");
	insertarDatosPostMeta($idPost, "_mphb_gateway_mode", "live");
	insertarDatosPostMeta($idPost, "_mphb_amount", buscarDatosReserva($idReserva, "mphb_total_price"));
	insertarDatosPostMeta($idPost, "_mphb_fee", buscarDatosReserva($idReserva, "mphb_total_price"));
	insertarDatosPostMeta($idPost, "_mphb_currency", "EUR");
	insertarDatosPostMeta($idPost, "_mphb_payment_type", "Pago en mano");
	insertarDatosPostMeta($idPost, "_mphb_transaction_id", $idReserva);
	insertarDatosPostMeta($idPost, "_mphb_booking_id", $idReserva);
	insertarDatosPostMeta($idPost, "_mphb_first_name", buscarDatosReserva($idReserva, "mphb_first_name"));
	insertarDatosPostMeta($idPost, "_mphb_last_name", buscarDatosReserva($idReserva, "mphb_last_name"));
	insertarDatosPostMeta($idPost, "_mphb_email", buscarDatosReserva($idReserva, "mphb_email"));
	insertarDatosPostMeta($idPost, "_mphb_phone", buscarDatosReserva($idReserva, "mphb_phone"));
	insertarDatosPostMeta($idPost, "_mphb_country", buscarDatosReserva($idReserva, "mphb_country"));
	insertarDatosPostMeta($idPost, "_mphb_address1", "");
	insertarDatosPostMeta($idPost, "_mphb_address2", "");
	insertarDatosPostMeta($idPost, "_mphb_city", "");
	insertarDatosPostMeta($idPost, "_mphb_state", "");
	insertarDatosPostMeta($idPost, "_mphb_zip", "");
	insertarDatosPostMeta($idPost, "wptr_hide_title", "");
	
}


?>