<?php

	defined('ABSPATH') or die( "Bye bye" );

	/*
 	* Nuevo menu de administrador
 	*/

	// El hook admin_menu ejecuta la funcion rai_menu_administrador
	add_action( 'admin_menu', 'checkin_menu_administrador' );

	// Shorcode que crea pasarela de pago
	add_shortcode('pasarela_pago', 'crear_url_pago_hotel');
	
	// Shorcode modificar estado pagos pendientes
	add_shortcode('modificar_datos_pagos_pendientes', 'modificar_estado_pagos_pendientes');

	// Top level menu del plugin
	function checkin_menu_administrador() {
		add_menu_page(CHECKIN_NOMBRE, CHECKIN_NOMBRE, 'edit_pages', CHECKIN_RUTA.'/admin/checkin-cliente.php', '', 'dashicons-index-card', 6);
		add_submenu_page(CHECKIN_RUTA.'/admin/checkin-cliente.php', 'Alta Reserva', 'Alta Reserva', 'edit_pages', CHECKIN_RUTA.'/admin/creacion-reserva.php');
		add_submenu_page(CHECKIN_RUTA.'/admin/checkin-cliente.php', 'Configuración', 'Configuracion', 'manage_options', CHECKIN_RUTA.'/admin/configuracion.php');
	}
	
	function buscarReserva($filtradoPor, $busqueda) {
		
		$idReserva;
		
		// Comprobar busqueda
		if ($filtradoPor == 'telefono') {
			$idReserva=buscarReservaPorTelefono($busqueda);
		} else if ($filtradoPor == 'email') {
			$idReserva=buscarReservaPorEmail($busqueda);
		} else if ($filtradoPor == 'nombre') {
			$idReserva=buscarReservaPorNombre($busqueda);
		} else {
			$idReserva=$busqueda;
		}
		
		return $idReserva;
	}

	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/modificarDatosReserva/',
			array(
				'methods' => 'POST',
				'callback' => 'modificarDatosReservaRest'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/crearCkeckin/',
			array(
				'methods' => 'POST',
				'callback' => 'crearCheckinRest'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/modificarDatosClienteReserva/',
			array(
				'methods' => 'POST',
				'callback' => 'modificarDatosClienteCheckinRest'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/subirFotosClientes/',
			array(
				'methods' => 'POST',
				'callback' => 'subirFotosClienteCheckinRest'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/subirDocumentoFirmadoPolicia/',
			array(
				'methods' => 'POST',
				'callback' => 'subirDocumentoFirmadoPoliciaRest'
			)
			);
	});

	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/getDatosReserva/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => 'consultarDatosReserva'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/notificarPagoReserva/(?P<bookingID>\d+)/(?P<checkoutID>[a-zA-Z0-9-]+)/(?P<transacctionID>\d+)/',
			array(
				'methods' => 'GET',
				'callback' => 'notificar_pago_redsys'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/notificarPagoReserva/(?P<bookingID>\d+)/(?P<checkoutID>[a-zA-Z0-9-]+)/(?P<transacctionID>\d+)/',
			array(
				'methods' => 'POST',
				'callback' => 'notificar_pago_redsys'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/notificarPagoReservaKO/(?P<bookingID>\d+)/(?P<checkoutID>[a-zA-Z0-9-]+)/(?P<transacctionID>\d+)/',
			array(
				'methods' => 'GET',
				'callback' => 'notificar_pago_redsys_ko'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/notificarPagoReservaKO/(?P<bookingID>\d+)/(?P<checkoutID>[a-zA-Z0-9-]+)/(?P<transacctionID>\d+)/',
			array(
				'methods' => 'POST',
				'callback' => 'notificar_pago_redsys_ko'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/aceptarReservaTransferencia/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => 'notificar_aceptacion_transferencia'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/descargarFacturaPDF/(?P<id>\d+)/(?P<checkoutID>[a-zA-Z0-9-]+)/',
			array(
				'methods' => 'GET',
				'callback' => 'descarga_factura_pdf_cliente'
			)
			);
	});
	
	add_action( 'rest_api_init', function () {
			register_rest_route( 'hotel/v2', '/reenviarCorreoFacturaCliente/(?P<id>\d+)',
			array(
				'methods' => 'GET',
				'callback' => 'reenviar_factura_pdf_cliente'
			)
			);
	});
		
?>