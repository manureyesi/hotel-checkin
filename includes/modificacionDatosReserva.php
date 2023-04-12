<?php
	
	function modificarDatosReservaRest() {
			
		// Comprobar headers
		if (comprobarHeadersHotel()) {
			
			$json = file_get_contents('php://input');

			// Converts it into a PHP object
			$data = json_decode($json);
			
			$listaClientes = $data->listaClientes;
			$idReserva = $data->idReserva;
			
			$telefonoReserva=buscarDatosReserva($idReserva, 'mphb_phone');
			$mailReserva=buscarDatosReserva($idReserva, 'mphb_email');
			$prezoReserva=buscarDatosReserva($idReserva, 'mphb_total_price');

			// Comprobar se existe registro
			$idInterno=buscarCheckinPorIdExterno($idReserva);
			
			if (isset($idInterno)) {
				// Modificar Datos reserva
				modificarCheckinReserva($idReserva, $prezoReserva, $telefonoReserva, $mailReserva);
			} else {
				// Insertar Datos reserva
				insertarCheckinReserva($prezoReserva, $telefonoReserva, $mailReserva, $idReserva);
			}

			$idInterno=buscarCheckinPorIdExterno($idReserva);

			// Borrar datos por idInterno de Usuarios
			eliminarCheckinUsuarioReserva($idInterno);

		
			foreach($listaClientes as $cliente) {
				$nomeCliente=$cliente->nombreCliente;
				$apellidoCliente=$cliente->apellidoCliente;
				$identificadorIdentidad=$cliente->identificadorIdentidad;
				
				$foto1=null;
				$foto2=null;
				foreach($cliente->ficheros as $fichero) {
					if (!isset($foto1)) {
						$foto1=$fichero;
					} else {
						$foto2=$fichero;
					}
				}
				
				insertarCheckinUsuarioReserva($idInterno, 1, $nomeCliente, $apellidoCliente, $identificadorIdentidad, $foto1, $foto2);
		
			}

			$data = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$data = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $data;

	}
	
	/**
	* Crear Checkin para Usuario
	**/
	function crearCheckinRest() {
			
		// Comprobar headers
		if (comprobarHeadersHotel()) {
			
			$json = file_get_contents('php://input');

			// Converts it into a PHP object
			$data = json_decode($json);
			
			$idReserva = $data->idReserva;
			
			$telefonoReserva=buscarDatosReserva($idReserva, 'mphb_phone');
			$mailReserva=buscarDatosReserva($idReserva, 'mphb_email');
			$prezoReserva=buscarDatosReserva($idReserva, 'mphb_total_price');

			// Comprobar se existe registro
			$idInterno=buscarCheckinPorIdExterno($idReserva);
			
			if (isset($idInterno)) {
				// Modificar Datos reserva
				modificarCheckinReserva($idReserva, $prezoReserva, $telefonoReserva, $mailReserva);
			} else {
				// Insertar Datos reserva
				insertarCheckinReserva($prezoReserva, $telefonoReserva, $mailReserva, $idReserva);
			}

			$data = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$data = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $data;

	}

	
	function modificarDatosClienteCheckinRest() {
			
		// Comprobar headers
		if (comprobarHeadersHotel()) {
			
			$json = file_get_contents('php://input');

			// Converts it into a PHP object
			$data = json_decode($json);
			
			$idReserva = $data->idReserva;
			$idUsuarioInterno = $data->idUsuarioInterno;
			
			// Comprobar se existe registro
			$idReservaInterno=buscarCheckinPorIdExterno($idReserva);
			
			// Datos cliente
			$nomeCliente=$data->nombreCliente;
			$apellidoCliente=$data->apellidoCliente;
			$identificadorIdentidad=$data->identificadorIdentidad;

			// Buscar Usuario por ID Reserva e ID Usuario
			$idUsuario=buscarCheckinUsuarioReservaPorIdInternoYIdUsuario($idReservaInterno, $idUsuarioInterno);

			// Borrar datos por idInterno de Usuarios (Modificar a eliminar solo usuario)
			//eliminarDatosUsuarioReservaPorIdInternoYIdUsuario($idReservaInterno, $idUsuarioInterno);

			if (isset($idUsuario)) {
				// Actualizar Usuario
				actualizarDatosUsuarioClienteCheckin($idUsuario, $nomeCliente, $apellidoCliente, $identificadorIdentidad);
			} else {
				// Insertar usuario
				insertarCheckinUsuarioReservaSinFotos($idReservaInterno, $idUsuarioInterno, $nomeCliente, $apellidoCliente, $identificadorIdentidad);
			}
		
			$data = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$data = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $data;

	}
	
	function subirFotosClienteCheckinRest() { 
			
		// Comprobar headers
		if (comprobarHeadersHotel()) {
			
			$json = file_get_contents('php://input');

			// Converts it into a PHP object
			$data = json_decode($json);
			
			$idReserva = $data->idReserva;
			$idUsuarioInterno = $data->idUsuarioInterno;
			$tipoFoto = $data->tipoFoto;
			$foto = $data->data;
			
			// Comprobar se existe registro
			$idReservaInterno=buscarCheckinPorIdExterno($idReserva);
			
			insertarfotosUsuarioClienteCheckin($idReservaInterno, $idUsuarioInterno, $tipoFoto, $foto);
		
			$data = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$data = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $data;

	}
	
	function consultarDatosReserva($data) {

		// Id Interno Cliente
		$idReserva=$data['id'];
		
		// Comprobar headers
		if (comprobarHeadersHotel()) {

			// Comprobar se existe registro
			$idInterno=buscarCheckinPorIdExterno($idReserva);

			if (isset($idInterno)) {

				$datosReserva=buscardatosCheckinPorIdExterno($idReserva);

				$data = array(
					"resultado" => 0, 
					"descripcion" => "OK",
					"factura" => $datosReserva->factura_cliente,
					"documentoPolicia" => $datosReserva->documento_policia_firmado,
					"listaClientesReserva" => buscarDatosClientesPorReserva($idInterno)
				);
			} else {
				http_response_code(404);
				$data = array("resultado" => 1, "descripcion" => "No esiste el identificador de reserva");
			}

		} else {
			http_response_code(403);
			$data = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $data;
	}

	function buscarDatosClientesPorReserva($idReservaInterno) {

		$datosClientesConsulta=buscarDatosCheckinUsuariosReservasPorIdInterno($idReservaInterno);

		$contador=0;
		$arrayClientes = array();
		
		foreach ($datosClientesConsulta as $cliente) {
			
			$datosCliente = array(
				"nombre" => $cliente->nombre, 
				"apellidos" => $cliente->apellidos, 
				"numero_documento" => $cliente->numero_documento, 
				"foto_documento_1" => $cliente->foto_documento_1, 
				"foto_documento_2" => $cliente->foto_documento_2
			);
			array_push($arrayClientes, $datosCliente);
		}
		return $arrayClientes;
	}
	
	function buscarReservarSinCheckin() {
		
		$dias=2;
		
		// Calcular desde dias
		$fecha_actual = date("Y-m-d");
		$fecha_calcular_mais=date("Y-m-d",strtotime($fecha_actual."+ {$dias} days")); 
		
		$resultadosFiltro=consultaDatosReservaFiltroPorSalida($fecha_actual, $fecha_calcular_mais);
		
		foreach ($resultadosFiltro as $resultado) {
			
			$idReservaAux=$resultado->post_id;
			
			// Comprobar estado post
			$post = buscarPostPorId($idReservaAux);
			
			// Comprobar se existe registro
			$idInterno=buscarCheckinPorIdExterno($idReservaAux);
			
			if ($post->post_status == "confirmed" && $idInterno==null) {
			
				crearCheckingAutomaticoReserva($idReservaAux);
				
			}
			
		}
		
	}
	
	function crearCheckingAutomaticoReserva($idReserva) {
					
		$telefonoReserva=buscarDatosReserva($idReserva, 'mphb_phone');
		$mailReserva=buscarDatosReserva($idReserva, 'mphb_email');
		$prezoReserva=buscarDatosReserva($idReserva, 'mphb_total_price');
		
		// Insertar Datos reserva
		insertarCheckinReserva($prezoReserva, $telefonoReserva, $mailReserva, $idReserva);
		
		// Buscar id Reserva Interno
		$idReservaInterno=buscarCheckinPorIdExterno($idReserva);
		
		// Insertar usuario
		insertarCheckinUsuarioReservaSinFotos($idReservaInterno, 1, buscarDatosReserva($idReserva, 'mphb_first_name'), buscarDatosReserva($idReserva, 'mphb_last_name'), null);
		
	}
	
	function subirDocumentoFirmadoPoliciaRest($data) {
		
		// Comprobar headers
		if (comprobarHeadersHotel()) {
			
			$json = file_get_contents('php://input');

			// Converts it into a PHP object
			$data = json_decode($json);
			
			$idReserva = $data->idReserva;
			$fotoBase64 = $data->fotoBase64;
			
			// Comprobar se existe registro
			$idReservaInterno=buscarCheckinPorIdExterno($idReserva);
			
			insertarResguardoFirmadoPoliciaClienteCheckin($idReservaInterno, $fotoBase64);
			
			$response = array("resultado" => 0, "descripcion" => "OK");
		} else {
			http_response_code(403);
			$response = array("resultado" => -1, "descripcion" => "Error en el Authorization");
		}
		
		header("Content-Type: application/json");
		return $response;
	}

	/**
	* Comprobar Headers
	*/
	function comprobarHeadersHotel() {
		
		// ver headers
		$apache_headers= apache_request_headers();

		foreach ($apache_headers as $key => $value) {
			if ($key=="authorization" || $key=="Authorization") {
				$authorization=$value;
			}
		}
		
		$permitido=false;
		// Comprobar Header
		if ($authorization==getTokenAuthorizationHotel()) {
			$permitido=true;
		}
		
		return $permitido;
	}
	
	function generarTokenAuthorizationHotel() {
		
		// Generar token authorization
		$tokenAuthorization=md5(date('l jS \of F Y h:i:s A'));
		
		add_option('hotel_token_authorization',$tokenAuthorization,'','yes');
		
	}

	function getTokenAuthorizationHotel() {
		return get_option('hotel_token_authorization', false);
	}


?>