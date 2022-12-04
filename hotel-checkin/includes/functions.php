<?php

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );
	
	//Archivos externos
	include(CHECKIN_RUTA.'/includes/redsysHMAC256_API_PHP_7.0.0/apiRedsys.php');

	function notificar_pago_redsys_ko($data) {
		
		comprobarFirmaRegistrarEstadoPago ($data,  "mphb-p-failed");
	}

	function notificar_pago_redsys($data) {
		
		$idReserva=$data['bookingID'];
		
		// Se modifica el estado de la reserva mientras no es pagada
		modificarEstadoPost($idReserva, "confirmed");
		
		comprobarFirmaRegistrarEstadoPago ($data,  "mphb-p-completed");
		
	}
	
	function comprobarFirmaRegistrarEstadoPago ($data,  $estado) {
		
		// Id Interno Cliente
		$idReserva=$data['bookingID'];
		$checkoutID=$data['checkoutID'];
		$transactionID=$data['transacctionID'];
		
		// Se crea Objeto
		$miObj = new RedsysAPI;


		if (!empty( $_POST ) ) {//URL DE RESP. ONLINE
							
			$version = $_POST["Ds_SignatureVersion"];
			$datos = $_POST["Ds_MerchantParameters"];
			$signatureRecibida = $_POST["Ds_Signature"];


			$decodec = $miObj->decodeMerchantParameters($datos);	
			$kc = get_option('redsys_clave_secreta', false); //Clave recuperada de CANALES
			$firma = $miObj->createMerchantSignatureNotif($kc,$datos);	

			echo PHP_VERSION."<br/>";
			echo $firma."<br/>";
			echo $signatureRecibida."<br/>";
			if ($firma === $signatureRecibida){
				notificarCobroOnline($idReserva, $transactionID, $estado);
				echo "FIRMA OK";
				$bookingKey=buscarDatosReserva($idReserva, "mphb_key");
				$url=get_option('siteurl', false)."/confirmacion-de-reserva/reserva-confirmada/?booking_id={$idReserva}&booking_key={$bookingKey}";
				header("Location: {$url}");
			} else {
				$url=get_option('siteurl', false)."/confirmacion-de-reserva/reserva-confirmada/?booking_id={$idReserva}&booking_key={$bookingKey}";
				header("Location: {$url}");
				echo "FIRMA KO";
			}
		} else{
			if (!empty( $_GET ) ) {//URL DE RESP. ONLINE

				$version = $_GET["Ds_SignatureVersion"];
				$datos = $_GET["Ds_MerchantParameters"];
				$signatureRecibida = $_GET["Ds_Signature"];


				$decodec = $miObj->decodeMerchantParameters($datos);
				$kc = get_option('redsys_clave_secreta', false); //Clave recuperada de CANALES
				$firma = $miObj->createMerchantSignatureNotif($kc,$datos);

				if ($firma === $signatureRecibida){
					notificarCobroOnline($idReserva, $transactionID, $estado);
					echo "FIRMA OK";
					$bookingKey=buscarDatosReserva($idReserva, "mphb_key");
					$url=get_option('siteurl', false)."/confirmacion-de-reserva/reserva-confirmada/?booking_id={$idReserva}&booking_key={$bookingKey}&enviar_correo=true";
					header("Location: {$url}");
					die();
				} else {
					echo "FIRMA KO";
					$url=get_option('siteurl', false)."/confirmacion-de-reserva/reserva-confirmada/?booking_id={$idReserva}&booking_key={$bookingKey}";
					header("Location: {$url}");
					die();
				}
			}
			else{
				$url=get_option('siteurl', false)."/confirmacion-de-reserva/reserva-confirmada/?booking_id={$idReserva}&booking_key={$bookingKey}";
				header("Location: {$url}");
				die("No se recibió respuesta");
			}
		}
	}

	function crearMetodosPago($booking, $bookingKey, $idPago) {
				
		// Buscar datos importe
		$importe=buscarDatosReserva($booking, "mphb_total_price");
		$checkoutID=buscarDatosReserva($booking, "_mphb_checkout_id");
		$importe=intval($importe)*100;
		
		// Se crea Objeto
		$miObj = new RedsysAPI;

		// Valores de entrada que no hemos cmbiado para ningun ejemplo
		$fuc=get_option('redsys_id_comercio', false);
		$terminal=get_option('redsys_terminal_pago', false);
		$moneda="978";
		$trans="0";
		$url=get_option('siteurl', false);
		$id=date('Y')."00".$booking;
		$urlOK=get_option('siteurl', false)."/wp-json/hotel/v2/notificarPagoReserva/{$booking}/{$checkoutID}/{$id}/";
		$urlKO=get_option('siteurl', false)."/wp-json/hotel/v2/notificarPagoReservaKO/{$booking}/{$checkoutID}/{$id}/";
		$amount=$importe;// Se tiene que multiplicar por 100	
		
		// Se Rellenan los campos
		$miObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
		$miObj->setParameter("DS_MERCHANT_ORDER",$id);
		$miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$fuc);
		$miObj->setParameter("DS_MERCHANT_CURRENCY",$moneda);
		$miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$trans);
		$miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
		$miObj->setParameter("DS_MERCHANT_MERCHANTURL",$url);
		$miObj->setParameter("DS_MERCHANT_URLOK",$urlOK);
		$miObj->setParameter("DS_MERCHANT_URLKO",$urlKO);

		// Comprobar Entorno activado
		if (get_option('redsys_entorno', false)==null || get_option('redsys_entorno', false)=="TEST") {
			$urlRedsys="https://sis-t.redsys.es:25443/sis/realizarPago";
		} else {
			$urlRedsys="https://sis.redsys.es/sis/realizarPago";
		}

		//Datos de configuración
		$version="HMAC_SHA256_V1";
		$kc = get_option('redsys_clave_secreta', false);//Clave recuperada de CANALES
		// Se generan los parámetros de la petición
		$request = "";
		$params = $miObj->createMerchantParameters();
		$signature = $miObj->createMerchantSignature($kc);
		
		// Comprobar estado error_get_last
		$divPagoError;
		if ($idPago != null) {
			$divPagoError = "
			<div>
			<p style='color:red;'>El pago ha sido rechazado, vuelve a intentar realizar el pago en unos minutos.</p>
			</div>
			";
		} else {
			// Comprobamos estado de reserva
			$estadoPago = buscarPostPorId($idPago)->post_status;
			if ($estadoPago != "pending-payment") {
				// Se modifica el estado de la reserva mientras no es pagada
				modificarEstadoPost($booking, "pending-payment");
			}
			
		}
		
		// Comprobar activado pago redsys
		$formPagoRedsys;
		if (!get_option('redsys_activada_redsys', false)==null) {
			$formPagoRedsys = "
			<form name='frm' action='{$urlRedsys}' method='POST'>
				<input type='hidden' type='text' name='Ds_SignatureVersion' value='{$version}'/>
				<input type='hidden' type='text' name='Ds_MerchantParameters' value='{$params}'/>
				<input type='hidden' type='text' name='Ds_Signature' value='{$signature}'/>
				<input type='submit' style='width:300px;' value='Realizar Pago Tarjeta'>
			</form>
			";
		}
		
		// Comprobar activado pago Transferencia
		$formPagoTransferencia;
		if (!get_option('transferencia_activada_transferencia', false)==null) {
			$formPagoTransferencia = "
			<form action='{$uri}' name='pagoTransferencia' method='GET'>
				<input type='hidden' type='text' name='booking_id' value='{$booking}'/>
				<input type='hidden' type='text' name='booking_key' value='{$bookingKey}'/>
				<input type='hidden' type='text' name='tipoPago' value='transferencia'/>
				<input type='submit' style='width:300px;' value='Realizar Pago Transferencia'>
			</form>
			";
		}
		
		return "
		{$divPagoError}
		<p>
		Para que tu reserva sea efectiva es necesarío realizar el pago en el siguiente enlace.
		En caso de no realizarlo en 24H la reserva se cancelara automaticamente.</p>
			
		{$formPagoRedsys}
		
		{$formPagoTransferencia}
		";
	}
	
	function modificar_estado_pagos_pendientes () {
		
		$booking = $_GET["booking_id"];
		
		$idPago = buscarDatosPago($booking);
		
		// Comprobamos estado de reserva
		$estadoPago = buscarPostPorId($booking)->post_status;
		if ($idPago == null && $estadoPago != "pending-payment") {
			// Se modifica el estado de la reserva mientras no es pagada
			modificarEstadoPost($booking, "pending-payment");
		}
		
	}

	function crear_url_pago_hotel() {
		
		$uri = $_SERVER['REQUEST_URI'];
		
		$booking = $_GET["booking_id"];
		$bookingKey = $_GET["booking_key"];
		
		// Comprobar estado pago
		$idPago = buscarDatosPago($booking);
		
		$estadoPago = buscarPostPorId($idPago)->post_status;
		
		if ($idPago==null || $estadoPago != "mphb-p-completed") {
			
			// Comrprobar pago transferencia
			if (isset($_GET["tipoPago"]) && $_GET["tipoPago"]=="transferencia") {
				notificarCobroTransferencia($booking);
				
				// Buscamos datos pago
				$idPago = buscarDatosPago($booking);
				
				// Mostrar estado pago
				return verInfoEstadoPagado($booking, $idPago);
				
			} else {
				return crearMetodosPago($booking, $bookingKey, $idPago);
			}
			
		} else if (isset($_GET["enviar_correo"]) && $_GET["enviar_correo"]=="true") {
			// Envio correo confirmacion pago
			enviarCorreoConfirmacionPago($booking, $idPago);
			enviarCorreoConfirmacionPagoAdmin ($booking, $idPago);
			
			// Mostrar estado pago
			return verInfoEstadoPagado($booking, $idPago);
		} else {
			// Mostrar estado pago
			return verInfoEstadoPagado($booking, $idPago);
		}
	}
	
	function verInfoEstadoPagado ($idReserva, $idPagoPost) {
		
		$estadoPago = buscarPostPorId($idPagoPost)->post_status;
		
		// Comprobar activado pago Transferencia
		$datosTransferenciaBancaria;
		if (buscarDatosReserva($idPagoPost, '_mphb_payment_type')=="Transferencia bancaria" && $estadoPago!="mphb-p-completed") {
			
			//Numero cuenta propiedades
			$numeroCuenta = get_option('transferencia_numero_cuenta_transferencia', false);
			
			//Nombre titular Cuenta
			$nombreTitularCuenta = get_option('transferencia_titular_cuenta_transferencia', false);
			
			//Nombre banca
			$nombreEntidadCuenta = get_option('transferencia_entidad_cuenta_transferencia', false);
			
			$datosTransferenciaBancaria = "
			<div class='datosTransferencia'>
			<h4 class='mphb-booking-details-title'>Datos transferencia</h4>
			<p>Los datos para hacer la trasnferencia son los siguientes:</p>
			<p>Número cuenta: {$numeroCuenta}</p>
			<p>Nombre titular: {$nombreTitularCuenta}</p>
			<p>Nombre Entidad: {$nombreEntidadCuenta}</p>
			<p>Concepto: Reserva {$idReserva} - ".buscarDatosReserva($idReserva, 'mphb_first_name')." ".buscarDatosReserva($idReserva, 'mphb_last_name')."</p>
			</div>
			";
		}
		
		// Comprobar estado pago
		$divPagoOK;
		if ($estadoPago=="mphb-p-completed") {
			$divPagoOK = "
			<div>
			<p style='color:green;'>El pago ha sido realizado correctamente. La reserva ya está completada.</p>
			<p>Para concretar la hora de la llegada nos puede contactar al siguiente número de telefono <a href='tel:+34654345678'>654345678</a></p>
			</div>
			";
		}
		
		
		return "
		
		<div class='mphb_sc_booking_confirmation'>
		<div class='mphb-booking-details-section booking'>
			<h3 class='mphb-booking-details-title'>Detalles de pago</h3>
			<ul class='mphb-booking-details'>
				<li class='booking-number'>
					<span class='label'>ID Pago:</span>
					<span class='value'>{$idPagoPost}</span>
				</li>
				<li class='booking-check-in'>
					<span class='label'>Tipo Pago:</span>
					<span class='value'>".buscarDatosReserva($idPagoPost, '_mphb_payment_type')."</span>
				</li>
				<li class='booking-price'>
					<span class='label'>Total:</span>
					<span class='value'><span class='mphb-price'>".buscarDatosReserva($idPagoPost, '_mphb_amount')."<span class='mphb-currency'>€</span></span></span>
				</li>
				<li class='booking-check-out'>
					<span class='label'>Estado pago:</span>
					<span class='value'>".comprobarEstadoPago($idPagoPost)."</span>
				</li>	
			</ul> 
		</div>
		{$divPagoOK}
		{$datosTransferenciaBancaria}
		</div>
		";
	}
	
	function comprobarEstadoPago ($idPagoPost) {
		
		$estadoPago = buscarPostPorId($idPagoPost)->post_status;
		
		// Estado de pago traducido
		$strEstadoPago;
		
		if ($estadoPago=="mphb-p-completed") {
			$strEstadoPago = "Pago completado"; 
		} else if ($estadoPago=="mphb-p-failed") {
			$strEstadoPago = "Error en el pago"; 
		} else {
			$strEstadoPago = "Pendiente de confirmación"; 
		} 
		
		return $strEstadoPago;
	}
	
	function guardarDatosContacto() {
		
		// Email Empresa contacto
		if (get_option('contacto_email_empresa', false)==null) {
			add_option('contacto_email_empresa', $_POST["idEmailEmpresa"] ,'','yes');
		} else {
			update_option('contacto_email_empresa', $_POST["idEmailEmpresa"]);
		}
		
		// Telefono contacto
		if (get_option('contacto_telefono_empresa', false)==null) {
			add_option('contacto_telefono_empresa', $_POST["idTelefonoEmpresa"] ,'','yes');
		} else {
			update_option('contacto_telefono_empresa', $_POST["idTelefonoEmpresa"]);
		}
		
	}
	
	function guardarDatosFacturacion() {
		
		// Nombre Factura cliente
		if (get_option('factura_nombre_empresa', false)==null) {
			add_option('factura_nombre_empresa', $_POST["idNombreFacturaEmpresa"] ,'','yes');
		} else {
			update_option('factura_nombre_empresa', $_POST["idNombreFacturaEmpresa"]);
		}
		
		// CIF Empresa	
		if (get_option('factura_cif_empresa', false)==null) {
			add_option('factura_cif_empresa', $_POST["idCifFacturaEmpresa"] ,'','yes');
		} else {
			update_option('factura_cif_empresa', $_POST["idCifFacturaEmpresa"]);
		}
		
		// Dirección Facturacion Empresa
		if (get_option('factura_direccion_empresa', false)==null) {
			add_option('factura_direccion_empresa', $_POST["idDireccionFacturaEmpresa"] ,'','yes');
		} else {
			update_option('factura_direccion_empresa', $_POST["idDireccionFacturaEmpresa"]);
		}
		
		// Foto Factura Empresa
		if (get_option('factura_url_foto_empresa', false)==null) {
			add_option('factura_url_foto_empresa', $_POST["idFotoFacturaEmpresa"] ,'','yes');
		} else {
			update_option('factura_url_foto_empresa', $_POST["idFotoFacturaEmpresa"]);
		}
		
	}
	
	function guardarDatosRedsys() {
		
		// Comprobar activar/desactivar
		if (isset($_POST["activoRedys"])) {
			add_option('redsys_activada_redsys','activo','','yes');
		} else {
			delete_option('redsys_activada_redsys');
		}
		
		// Id Comercio
		if (get_option('redsys_id_comercio', false)==null) {
			add_option('redsys_id_comercio', $_POST["idComercio"] ,'','yes');
		} else {
			update_option('redsys_id_comercio', $_POST["idComercio"]);
		}
		
		// Clave secreta
		if ($_POST["idCalveSecreta"]!="***********") {
			if (get_option('redsys_clave_secreta', false)==null) {
				add_option('redsys_clave_secreta', $_POST["idCalveSecreta"] ,'','yes');
			} else {
				update_option('redsys_clave_secreta', $_POST["idCalveSecreta"]);
			}
		}
		
		// Terminal Pago
		if (get_option('redsys_terminal_pago', false)==null) {
			add_option('redsys_terminal_pago', $_POST["idTerminalPago"] ,'','yes');
		} else {
			update_option('redsys_terminal_pago', $_POST["idTerminalPago"]);
		}
		
		// Entorno
		if (get_option('redsys_entorno', false)==null) {
			add_option('redsys_entorno', $_POST["entorno"] ,'','yes');
		} else {
			update_option('redsys_entorno', $_POST["entorno"]);
		}
		
	}
	
	function guardarDatosTrasnferenciaBancaria() {
		
		// Comprobar activar/desactivar
		if (isset($_POST["activoTransferencia"])) {
			add_option('transferencia_activada_transferencia','activo','','yes');
		} else {
			delete_option('transferencia_activada_transferencia');
		}
		
		// Número cuenta
		if (get_option('transferencia_numero_cuenta_transferencia', false)==null) {
			add_option('transferencia_numero_cuenta_transferencia', $_POST["numeroCuenta"] ,'','yes');
		} else {
			update_option('transferencia_numero_cuenta_transferencia', $_POST["numeroCuenta"]);
		}
		
		// Nombre Titular
		if (get_option('transferencia_titular_cuenta_transferencia', false)==null) {
			add_option('transferencia_titular_cuenta_transferencia', $_POST["nombreTitular"] ,'','yes');
		} else {
			update_option('transferencia_titular_cuenta_transferencia', $_POST["nombreTitular"]);
		}
		
		// Entidad Bancaria
		if (get_option('transferencia_entidad_cuenta_transferencia', false)==null) {
			add_option('transferencia_entidad_cuenta_transferencia', $_POST["nombreBancoCuenta"] ,'','yes');
		} else {
			update_option('transferencia_entidad_cuenta_transferencia', $_POST["nombreBancoCuenta"]);
		}
		
		
	}

	function guardarDatosConfiguracionHotel() {
	
		// Hora Permitida Entrada
		if (get_option('booking_fecha_permitida_llegada', false)==null) {
			add_option('booking_fecha_permitida_llegada', $_POST["horaPermitidaEntrada"] ,'','yes');
		} else {
			update_option('booking_fecha_permitida_llegada', $_POST["horaPermitidaEntrada"]);
		}

		// Hora Permitida Salida
		if (get_option('booking_fecha_permitida_salida', false)==null) {
			add_option('booking_fecha_permitida_salida', $_POST["horaPermitidaSalida"] ,'','yes');
		} else {
			update_option('booking_fecha_permitida_salida', $_POST["horaPermitidaSalida"]);
		}
		
		// Correos envios Admin
		if (get_option('booking_correos_admin', false)==null) {
			add_option('booking_correos_admin', $_POST["correosAdminEnvioCorreo"] ,'','yes');
		} else {
			update_option('booking_correos_admin', $_POST["correosAdminEnvioCorreo"]);
		}

	}
	
	function eliminarReservasImpagadas() {
		
		// Dias eliminacion reserva
		$horas = 24;
		
		$fechaActual = date("Y-m-d");
		$fechaActualFormato = date("Y-m-d h:i:sa.000");
		
		$fecha_calcular_mais=date("Y-m-d h:i:sa.000",strtotime($fechaActualFormato."+ {$horas} hour"));
		
		// listar reservas
		$resultados = consultaReservasEnEstadoNoPagado("pending-payment", $fechaActual);
		
		foreach ($resultados as $resultado) {
			$postId=$resultado->ID;
			
			$datosPost = buscarPostPorId($postId);
			
			// Comprobamos fecha creacion post
			if ($datosPost->post_modified > $fecha_calcular_mais) {
				// Cancelamos reserva
				modificarEstadoPost($postId, "abandoned");
				
			}
			
		}
		
	}
	
	function crearFacturasEnviarCorreo () {
		
		// Buscar Reservas sin factura
		$checkinSinFactura = buscardatosCheckinSinFactura();
		
		foreach ($checkinSinFactura as $resultado) {
			// Crear factura
			guardarFacturaClientePorIdReserva($resultado->id_pedido);
			// Envio correo factura
			enviarCorreoFacturaCliente($resultado->id_pedido);
		}
	}
	
	function crearReservaManualUsuario() { 
		
		// Buscar id ultimo post
		$idPost=buscarIdPostNew();
		
		$baseUrl = get_option('siteurl', false);
		
		// Crear post de Reserva
		insertarPostDatos($idPost, date("Y-m-d h:i:sa.000"), "confirmed", "mphb_booking", 0, "{$baseUrl}/?post_type=mphb_booking&#038;p={$idPost}");
		
		// Json reservas
		$jsonReservas = '{"rooms":[{"room":{"type":"';
		$jsonReservas .= $_POST["nombreAlojamiento"];
		$jsonReservas .= '","rate":"';
		$jsonReservas .= $_POST["nombreAlojamiento"];
		$jsonReservas .= '","list":{';
		for($i=$_POST['entradaReserva'];$i<$_POST["saidaReserva"];$i = date("Y-m-d", strtotime($i ."+ 1 days"))) {
			if ($_POST["entradaReserva"]!=$i) {
				$jsonReservas .= ',';
			}
			$jsonReservas .= '"';
			$jsonReservas .= $i;
			$jsonReservas .= '": 0';
		}
		$jsonReservas .= '},"total":';
		$jsonReservas .= $_POST["precioReserva"];
		$jsonReservas .= ',"discount":0,"discount_total":';
		$jsonReservas .= $_POST["precioReserva"];
		$jsonReservas .= ',"adults":';
		$jsonReservas .= $_POST["numeroAdultos"]; 
		$jsonReservas .= ',"children":';
		$jsonReservas .= $_POST["numeroNinos"];
		$jsonReservas .= ',"children_capacity":';
		$jsonReservas .= $_POST["numeroNinos"];
		$jsonReservas .= '},"services":{"list":[],"total":0},"fees":{"list":[],"total":0},"taxes":{"room":{"list":[],"total":0},"services":{"list":[],"total":0},"fees":{"list":[{"label":"IVA","price":0}],"total":0}},"total":';
		$jsonReservas .= $_POST["precioReserva"];
		$jsonReservas .= ',"discount_total":';
		$jsonReservas .= $_POST["precioReserva"];
		$jsonReservas .= '}],"total":';
		$jsonReservas .= $_POST["precioReserva"];
		$jsonReservas .= '}';
		
		$unixDate = date("Ymdhis");
		$md5DateSubstring = substr(md5($unixDate), 14);
		$keyBooking = "booking_{$idPost}_{$md5DateSubstring}.{$unixDate}";
		
		insertarDatosPostMeta($idPost, "mphb_key", $keyBooking);
		insertarDatosPostMeta($idPost, "mphb_check_in_date", $_POST["entradaReserva"]);
		insertarDatosPostMeta($idPost, "mphb_check_out_date", $_POST["saidaReserva"]);
		insertarDatosPostMeta($idPost, "mphb_note", $_POST["notasReserva"]);
		//insertarDatosPostMeta($idPost, "mphb_customer_id", "0");
		insertarDatosPostMeta($idPost, "mphb_email", $_POST["emailReserva"]);
		insertarDatosPostMeta($idPost, "mphb_first_name", $_POST["nombreReserva"]);
		insertarDatosPostMeta($idPost, "mphb_last_name", $_POST["apellidoReserva"]);
		insertarDatosPostMeta($idPost, "mphb_phone", $_POST["telefonoReserva"]);
		insertarDatosPostMeta($idPost, "mphb_country", $_POST["paisReserva"]);
		insertarDatosPostMeta($idPost, "mphb_state", $_POST["provinciaReserva"]);
		insertarDatosPostMeta($idPost, "mphb_city", $_POST["ciudadReserva"]);
		insertarDatosPostMeta($idPost, "mphb_zip", $_POST["postalCodeReserva"]);
		insertarDatosPostMeta($idPost, "mphb_address1", $_POST["direccionReserva"]);
		insertarDatosPostMeta($idPost, "mphb_total_price", $_POST["precioReserva"]);
		insertarDatosPostMeta($idPost, "mphb_ical_prodid", "");
		insertarDatosPostMeta($idPost, "mphb_ical_summary", "");
		insertarDatosPostMeta($idPost, "mphb_ical_description", "");
		insertarDatosPostMeta($idPost, "mphb_language", "es");
		insertarDatosPostMeta($idPost, "_mphb_checkout_id", md5($idPost));
		insertarDatosPostMeta($idPost, "_mphb_booking_price_breakdown", "{$jsonReservas}");
		insertarDatosPostMeta($idPost, "_id", $idPost);
		insertarDatosPostMeta($idPost, "mphb_coupon_id", "");
		
		// POst para reservar habitacion
		// Buscar id ultimo post
		$idPostReserva=buscarIdPostNew();
				
		// Crear post de Reserva habitacion
		insertarPostDatos($idPostReserva, date("Y-m-d h:i:sa.000"), "publish", "mphb_reserved_room", $idPost, "{$baseUrl}/mphb_reserved_room/{$idPostReserva}/");
		
		// Datos alojamiento reserva
		$idAlojamiento="";
		$listaAlojamiento = consultarPostPorTipoYEstado("mphb_room", "publish");
		foreach ($listaAlojamiento as $resultado) {
			if ($resultado->post_title==$_POST["nombreAlojamiento"]) {
				$idAlojamiento=$resultado->ID;
			}
		}
		
		// Datos alojamiento tarifa
		$idTarifa="";
		$listaAlojamiento = consultarPostPorTipoYEstado("mphb_rate", "publish");
		foreach ($listaAlojamiento as $resultado) {
			if ($resultado->post_title==$_POST["nombreAlojamiento"]) {
				$idTarifa=$resultado->ID;
			}
		}
		
		insertarDatosPostMeta($idPostReserva, "_mphb_room_id", $idAlojamiento);
		insertarDatosPostMeta($idPostReserva, "_mphb_rate_id", $idTarifa);
		insertarDatosPostMeta($idPostReserva, "_mphb_adults", $_POST["numeroAdultos"]);
		insertarDatosPostMeta($idPostReserva, "_mphb_children", $_POST["numeroNinos"]);
		insertarDatosPostMeta($idPostReserva, "_mphb_services", "a:0:{}");
		insertarDatosPostMeta($idPostReserva, "_mphb_guest_name", "");
		insertarDatosPostMeta($idPostReserva, "_mphb_uid", uniqid());
		
		// Comprobar checkbox envio correo pago
		if ($_POST["envioCorreoPago"] == "envioCorreoPago") {
			// Se modifica el estado de la reserva mientras no es pagada
			modificarEstadoPost($idPost, "pending-payment");
			// Enviamos correo para pago
			enviarCorreoRealizarPago ($idPost);						
		} else {
			// Enviamos correo de confirmacion y creamos pago manual
			notificarCobroManual($idPost);
			enviarCorreoConfirmacionPago($idPost, $idPago);
		}
		
		return $idPost;
	}
	
	function notificar_aceptacion_transferencia($data) {
		$idReserva=$data['id'];
		
		// Comprobar headers
		if (comprobarHeadersHotel()) {
			
			// Modificamos estado reserva
			modificarEstadoPost($idReserva, "confirmed");
			
			// Buscar lista de pagos
			$listaPagos = buscarListaDatosPago($idReserva);
			
			foreach ($listaPagos as $pagos) {
			
				// Buscar estado pago
				$estadoPago = buscarPostPorId($pagos->post_id)->post_status;
			
				// Comprobar forma pago
				if (buscarDatosReserva($pagos->post_id, "_mphb_gateway") == "bank" && $estadoPago != "mphb-p-failed") {
					modificarEstadoPost($pagos->post_id, "mphb-p-completed");
					// Envio correo confirmacion pago
					enviarCorreoConfirmacionPago($idReserva, $pagos->post_id);
				}
				
			}
			
			$response = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$response = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $response;	
	}

	function transformarFechaCheckinCheckOut ($date) {
		$dateAux = strtotime($date);
		return date('d/m/Y', $dateAux);
	}
	
	function descargarFacturaCliente($idReserva) {
		
		// Comprobar se existe registro
		$datosCheckin=buscardatosCheckinPorIdExterno($idReserva);
		
		// comprobar datos checkin
		$facturaBase64;
		if ($datosCheckin != null) {
			$facturaBase64 = $datosCheckin->factura_cliente;
		}
		return $facturaBase64;
	}
	
	function guardarFacturaClientePorIdReserva($idReserva) {
		
		// Comprobar se existe registro
		$idInterno=buscarCheckinPorIdExterno($idReserva);
		
		$facturaBase64 = "";
		// Comprobar existe reserva
		if (!$idInterno == null) {

			$b64Doc = base64_encode(crearFacturaCliente($idReserva));

			$facturaBase64 = "data:application/pdf;base64,{$b64Doc}";
			// Insertar factura en BBDD
			insertarFacturaClienteCheckin($idInterno, $facturaBase64);
		}
		
		return $facturaBase64;
	}
	
	function crearDireccionPorIdReserva($idReserva) {
		$direccion = "";
		
		$direccion .= buscarDatosReserva($idReserva, 'mphb_address1');
		$direccion .= " ";
		$direccion .= buscarDatosReserva($idReserva, 'mphb_zip');
		$direccion .= " ";
		$direccion .= buscarDatosReserva($idReserva, 'mphb_city');
		$direccion .= " ";
		$direccion .= buscarDatosReserva($idReserva, 'mphb_state');
		
		return $direccion;
	}
	
	function crearFacturaCliente($idReserva) {
				
		$JSONdatosAloxamento = json_decode(buscarDatosReserva($idReserva, '_mphb_booking_price_breakdown'));
		
		$importeTotal = (double)buscarDatosReserva($idReserva, "mphb_total_price");
		$importeSinIva = $importeTotal / 1.21;
				
		// 1.- add your json
		$data = '{
				"num_factura": "'.crearIdentificadorFactura($idReserva).'",
				"foto_factura": "'.get_option('factura_url_foto_empresa', false).'",
				"nombre_empresa": "'.get_option('factura_nombre_empresa', false).'",
				"cif_empresa": "'.get_option('factura_cif_empresa', false).'",
				"direccion_empresa": "'.get_option('factura_direccion_empresa', false).'",
				"email_empresa": "'.get_option('contacto_email_empresa', false).'",
				"telefono_empresa": "'.get_option('contacto_telefono_empresa', false).'",
				"nombre_factura_cliente": "'.buscarDatosReserva($idReserva, 'mphb_first_name').' '.buscarDatosReserva($idReserva, 'mphb_last_name').'",
				"direccion_factura_cliente": "'.crearDireccionPorIdReserva($idReserva).'",
				"fecha_factura": "'.date("d/m/Y").'",
				"listaServicios": [
					{
						"servicio": "'.$JSONdatosAloxamento->rooms[0]->room->type.'",
						"descripcion": "Alojamiento entre '.buscarDatosReserva($idReserva, 'mphb_check_in_date').' '.buscarDatosReserva($idReserva, 'mphb_check_out_date').'",
						"precio": "'.number_format((double)$importeSinIva, 2, '.', '').' €",
						"cantidad": 1,
						"total": "'.number_format((double)$importeSinIva, 2, '.', '').' €"
					}
				],
				"sub_total": "'.number_format((double)$importeSinIva, 2, '.', '').' €",
				"desccripcion_impuesto": "IVA 21%",
				"importe_impuesto": "'.number_format((double)($importeTotal - $importeSinIva), 2, '.', '').' €",
				"total": "'.number_format((double)$importeTotal, 2, '.', '').' €"
			}';
		
		// 2.- add api endpoint
		$url = get_option('siteurl', false)."/wp-content/plugins/hotel-checkin/includes/facturaCliente.php"; 

		// 3.- fire
		$response = sendRequest($data, $url);
		
		return $response;
	}
	
	function sendRequest($data, $url) {
		$opts = array('http' =>
		  array(
			  'method'  => 'POST',
			  'header'  => "Content-type: application/json \r\n",
			  'content' => $data,
			  'ignore_errors' => true,
			  'timeout' =>  300,
		  )
		);
		$context  = stream_context_create($opts);
		return file_get_contents($url, false, $context); 
	}
	
	function crearIdentificadorFactura ($bookingId) {
		return "F".date("Y")."000".$bookingId;
	}
	
	function reenviar_factura_pdf_cliente($data) {
		$idReserva=$data['id'];
		
		
		// Comprobar headers
		if (comprobarHeadersHotel()) {
		
			// Envio correo factura
			enviarCorreoFacturaCliente($idReserva);
		
			$response = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$response = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $response;
	}
	
	function descarga_factura_pdf_cliente($data) {
		
		$idReserva=$data['id'];
		$checkoutID=$data['checkoutID'];
		
		// Comprobar reserva
		$checkoutIDBBDD = buscarDatosReserva($idReserva, '_mphb_checkout_id');
		$response = "";
		if ($checkoutIDBBDD === $checkoutID) {
			// Descargar factura
			$response = descargarFacturaCliente($idReserva);
			header("Location: {$response}");
			exit();
		} else {
			$url = get_option('siteurl', false);
			header("Location: {$url}");
			exit();
		}
	}
	
?>