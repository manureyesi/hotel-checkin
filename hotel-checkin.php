<?php
/*
Plugin Name: Hotel Checkin
Plugin URI: https://github.com/manureyesi/hotel-checkin
Description: Plugin que realiza el checkin de las reservas y guarda los datos de los clientes
Version: 1.0
Author: Manuel Reyes
Author URI: https://github.com/manureyesi
License: CC BY-NC-SA 3.0 ES
*/

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );

	// Mostrar Errores
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);

	//Aqui se definen las constantes
	define('CHECKIN_RUTA',plugin_dir_path(__FILE__));
	define('CHECKIN_NOMBRE','Chekin Usuario');

	//Archivos externos
	include(CHECKIN_RUTA.'/includes/opciones.php');
	include(CHECKIN_RUTA.'/includes/functions.php');
	include(CHECKIN_RUTA.'/includes/modificacionDatosReserva.php');
	include(CHECKIN_RUTA.'/includes/crud.php');
	include(CHECKIN_RUTA.'/includes/datosCobroReserva.php');
	include(CHECKIN_RUTA.'/includes/envioCorreo.php');
	//include(CHECKIN_RUTA.'/includes/facturaCliente.php');
	
	// Templates
	include(CHECKIN_RUTA.'/templates/plantilla_correo_confirmacion_reserva_pago.php');
	include(CHECKIN_RUTA.'/templates/plantilla_correo_envio_admin_confirmacion_pago.php');
	include(CHECKIN_RUTA.'/templates/plantilla_correo_realizar_pago_reserva_cliente.php');
	include(CHECKIN_RUTA.'/templates/plantilla_correo_envio_factura_cliente.php');
	
	function activacionPluguinCheckinUsuario() {
		//A partir de aquí escribe todas las tareas que quieres realizar en la activación
		crearTablasCheckin();
		// Generar token authorizacion
		generarTokenAuthorizationHotel();
		
		// Crear evento schedule eliminacion
		if( ! wp_next_scheduled('comprobacion_eliminacion_reservas_sin_pago') ) {
			wp_schedule_event(time(), 'hourly', 'comprobacion_eliminacion_reservas_sin_pago' );
		}
		
		
		// Crear evento schedule enviar facturas
		if( ! wp_next_scheduled('crear_enviar_facturas_cliente_reserva') ) {
			wp_schedule_event(time(), 'hourly', 'crear_enviar_facturas_cliente_reserva' );
		}
		
		
	}

	add_action('crear_enviar_facturas_cliente_reserva', 'crearFacturasEnviarCorreo');
	add_action('comprobacion_eliminacion_reservas_sin_pago', 'eliminarReservasImpagadas');

	function desactivacionPluguinCheckinUsuario() {
		eliminarTablaCheckin();
		eliminarTablaCheckinUsuario();
		// Eliminar schedule
		wp_clear_scheduled_hook('comprobacion_eliminacion_reservas_sin_pago');
		wp_clear_scheduled_hook('crear_enviar_facturas_cliente_reserva');
	}

	register_activation_hook(__FILE__, 'activacionPluguinCheckinUsuario');

	register_deactivation_hook(__FILE__, 'desactivacionPluguinCheckinUsuario')

?>
