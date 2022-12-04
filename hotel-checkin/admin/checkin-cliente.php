<?php

defined('ABSPATH') or die( "Bye bye" );

//Archivos externos
//include(VISITAS_RUTA.'/includes/opciones.php');
//include(CHECKIN_RUTA.'/includes/modificacionDatosReserva.php');
//include(VISITAS_RUTA.'/includes/crud.php');

//Comprueba que tienes permisos para acceder a esta pagina
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkin Cliente</title>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="/wp-content/plugins/hotel-checkin/admin/js/datos-reserva.js"></script>

	<link rel="stylesheet" href="/wp-content/plugins/hotel-checkin/admin/css/reserva.css">
	
</head>

<body>

		<?php
					
			$uri = $_SERVER['REQUEST_URI'];
		
			$idReserva;
		
			// Comprobar parametro
			if (isset($_POST['filtradoPor']) && isset($_POST['busqueda'])) {
				$idReserva=buscarReserva($_POST['filtradoPor'], $_POST['busqueda']);
			} else if (isset($_GET['busqueda'])) {
				$idReserva=$_GET['busqueda'];
			}
				
		?>

	<h2>Próximas Reservas:</h2>
	
	<table class="wp-list-table widefat fixed striped table-view-list posts">
	
	<thead>
	
		<tr class="title">
			<td scope="col" id="idReserva" class="manage-column column-status">Id Reserva</td>
			<td scope="col" id="nombre" class="manage-column column-status">Nombre</td>
			<td scope="col" id="apellido" class="manage-column column-status">Apellido</td>
			<td scope="col" id="email" class="manage-column column-status">Email</td>
			<td scope="col" id="telefono" class="manage-column column-status">Telefono</td>
			<td scope="col" id="alojamiento" class="manage-column column-status">Alojamiento</td>
			<td scope="col" id="fechaEntrada" class="manage-column column-status">Fecha Entrada</td>
			<td scope="col" id="fechaSalida" class="manage-column column-status">Fecha Salida</td>
			<td scope="col" id="reserva" class="manage-column column-status">Reserva</td>
		</tr>
		
	</thead>
		
	<?php
		
		$dias=7;
		
		// Calcular desde dias
		$fecha_actual = date("Y-m-d");
		$fecha_calcular_menos=date("Y-m-d",strtotime($fecha_actual."- {$dias} days"));
		$fecha_calcular_mais=date("Y-m-d",strtotime($fecha_actual."+ {$dias} days")); 
		
		$resultadosFiltro=consultaDatosReservaFiltro($fecha_calcular_menos, $fecha_calcular_mais);
		
		foreach ($resultadosFiltro as $resultado) {
			
			$idReservaAux=$resultado->post_id;
			
			// Comprobar estado post
			$post = buscarPostPorId($idReservaAux);
			
			if ($post->post_status == "confirmed") {
			
				$datosAloxamento = json_decode(buscarDatosReserva($idReservaAux, '_mphb_booking_price_breakdown'));
				
				echo '<tr>';
				echo '<td>'.$idReservaAux.'</td>';
				echo '<td>'.buscarDatosReserva($idReservaAux, 'mphb_first_name').'</td>';
				echo '<td>'.buscarDatosReserva($idReservaAux, 'mphb_last_name').'</td>';
				echo '<td>'.buscarDatosReserva($idReservaAux, 'mphb_email').'</td>';
				echo '<td>'.buscarDatosReserva($idReservaAux, 'mphb_phone').'</td>';
				echo '<td>'.$datosAloxamento->rooms[0]->room->type.'</td>';
				echo '<td>'.buscarDatosReserva($idReservaAux, 'mphb_check_in_date').'</td>';
				echo '<td>'.buscarDatosReserva($idReservaAux, 'mphb_check_out_date').'</td>';
				echo '<td><a class="add-new-h2 mphb-rules-list-add-button" href="'.$uri.'&busqueda='.$idReservaAux.'">Checkin</a></td>';
				echo '</tr>';
				
			}
		}
			
		?>
	
	</table>

	<h2>Reservas pendientes de validar:</h2>
	
	<table class="wp-list-table widefat fixed striped table-view-list posts">
	
	<thead>
	
		<tr class="title">
			<td scope="col" id="idReservaPendiente" class="manage-column column-status">Id Reserva</td>
			<td scope="col" id="nombrePendiente" class="manage-column column-status">Nombre</td>
			<td scope="col" id="apellidoPendiente" class="manage-column column-status">Apellido</td>
			<td scope="col" id="emailPendiente" class="manage-column column-status">Email</td>
			<td scope="col" id="telefonoPendiente" class="manage-column column-status">Telefono</td>
			<td scope="col" id="alojamientoPendiente" class="manage-column column-status">Alojamiento</td>
			<td scope="col" id="fechaEntradaPendiente" class="manage-column column-status">Fecha Entrada</td>
			<td scope="col" id="fechaSalidaPendiente" class="manage-column column-status">Fecha Salida</td>
			<td scope="col" id="reservaPendiente" class="manage-column column-status">Reserva</td>
		</tr>
		
	</thead>
		
	<?php
				
		// listar reservas
		$resultadosEnEstadoPendiente = consultaReservasPorEstado("pending");
		
		foreach ($resultadosEnEstadoPendiente as $reservaPendiente) {
			$reservaPendienteId=$reservaPendiente->ID;
			
			$datosAloxamento = json_decode(buscarDatosReserva($reservaPendienteId, '_mphb_booking_price_breakdown'));
				
			echo '<tr>';
			echo '<td>'.$reservaPendienteId.'</td>';
			echo '<td>'.buscarDatosReserva($reservaPendienteId, 'mphb_first_name').'</td>';
			echo '<td>'.buscarDatosReserva($reservaPendienteId, 'mphb_last_name').'</td>';
			echo '<td>'.buscarDatosReserva($reservaPendienteId, 'mphb_email').'</td>';
			echo '<td>'.buscarDatosReserva($reservaPendienteId, 'mphb_phone').'</td>';
			echo '<td>'.$datosAloxamento->rooms[0]->room->type.'</td>';
			echo '<td>'.buscarDatosReserva($reservaPendienteId, 'mphb_check_in_date').'</td>';
			echo '<td>'.buscarDatosReserva($reservaPendienteId, 'mphb_check_out_date').'</td>';
			echo '<td><a class="add-new-h2 mphb-rules-list-add-button" href="'.$uri.'&busqueda='.$reservaPendienteId.'">Checkin</a></td>';
			echo '</tr>';
		
			
		}
					
		?>
	
	</table>

	<h2>Buscar Datos reserva:</h2>
	
	<form action="<?=$uri;?>" method="post">
		<label for="filtradoPor">Buscar cliente por:</label>
		<select name="filtradoPor" id="filtradoPor">
			<option value="email">Email</option>
			<option value="telefono">Telefono</option>
			<option value="nombre">Nombre</option>
			<option value="idreserva">Id Reserva</option>
		</select>
		
		</br>
		</br>
		
		<input type="text" id="busqueda" name="busqueda" size="100">
		
		</br>
		</br>
		
		<input type="submit" value="Buscar" />
		
	</form>
		
	</br>
	</br>

	<?php

	if(isset($idReserva)) {
		
		$nomeCliente=buscarDatosReserva($idReserva, 'mphb_first_name');
		$apelidoCliente=buscarDatosReserva($idReserva, 'mphb_last_name');
		
		$emailReserva=buscarDatosReserva($idReserva, 'mphb_email');
		$telefonoReserva=buscarDatosReserva($idReserva, 'mphb_phone');
		
		$datosAloxamento = json_decode(buscarDatosReserva($idReservaAux, '_mphb_booking_price_breakdown'));
		
		$prezoReserva=buscarDatosReserva($idReserva, 'mphb_total_price');
		
		$fechaEntradaReserva=buscarDatosReserva($idReserva, 'mphb_check_in_date');
		$fechaSaidaReserva=buscarDatosReserva($idReserva, 'mphb_check_out_date');
		
		$notaReserva=buscarDatosReserva($idReserva, 'mphb_note');
		
		?>

	<fieldset>
            <!--<legend>JAVA:</legend>-->

		<div class="wrap">

		<h2>Datos Reserva:</h2>
		
		
		<table class="form-table" role="presentation">
		
		<tbody>
		
			<tr>
				<th scope="row">
					<label for="idReserva">ID Reserva:</label>
				</th>
				<td>
					<input name="idReserva" type="text" id="idReserva"  disabled value="<?=$idReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="nombreReserva">Nombre:</label>
				</th>
				<td>
					<input name="nombreReserva" type="text" id="nombreReserva"  disabled value="<?=$nomeCliente;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="apellidoReserva">Apellidos:</label>
				</th>
				<td>
					<input name="apellidoReserva" type="text" id="apellidoReserva"  disabled value="<?=$apelidoCliente;?>" class="regular-text">
				</td>
			</tr>
		
			<tr>
				<th scope="row">
					<label for="emailReserva">Apellidos:</label>
				</th>
				<td>
					<input name="emailReserva" type="text" id="emailReserva"  disabled value="<?=$emailReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="telefonoReserva">Telefono:</label>
				</th>
				<td>
					<input name="telefonoReserva" type="text" id="telefonoReserva"  disabled value="<?=$telefonoReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="nombreAlojamiento">Alojamiento:</label>
				</th>
				<td>
					<input name="nombreAlojamiento" type="text" id="nombreAlojamiento"  disabled value="<?=$datosAloxamento->rooms[0]->room->type;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="numeroAdultos">Nº Adultos:</label>
				</th>
				<td>
					<input name="numeroAdultos" type="text" id="numeroAdultos"  disabled value="<?=$datosAloxamento->rooms[0]->room->adults;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="numeroNinos">Nº Niños:</label>
				</th>
				<td>
					<input name="numeroNinos" type="text" id="numeroNinos"  disabled value="<?=$datosAloxamento->rooms[0]->room->children;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="entradaReserva">Fecha Entrada:</label>
				</th>
				<td>
					<input name="entradaReserva" type="date" id="entradaReserva"  disabled value="<?=$fechaEntradaReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="saidaReserva">Fecha Saida:</label>
				</th>
				<td>
					<input name="saidaReserva" type="date" id="saidaReserva"  disabled value="<?=$fechaSaidaReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="notasReserva">Notas:</label>
				</th>
				<td>
					<textarea name="textarea" id="notasReserva" rows="5" cols="50"  disabled class="regular-text"><?=$notaReserva;?></textarea>
				</td>
			</tr>
		
		</tbody>
				
		</table>
		
		</div>
		
		<h3>Datos pago</h3>
		
		<table class="wp-list-table widefat fixed striped table-view-list posts" id="tablaClientes">
		<thead>
	
			<tr class="title">
				<td>ID de pago</td>
				<td>Estado</td>
				<td>Tipo Pago</td>
				<td>Cantidad</td>
			</tr>
		</thead>
		<?php
		
			// Buscar lista de pagos
			$listaPagos = buscarListaDatosPago($idReserva);
		
			$sumaPago=0;
			
			$tipoPagoTransferencia=false;
		
			foreach ($listaPagos as $pagos) {
				
				$estadoPago=comprobarEstadoPago($pagos->post_id);
				
				// Suma pagos
				if ($estadoPago == "Pago completado") {
					$sumaPago=$sumaPago+intval(buscarDatosReserva($pagos->post_id, "_mphb_fee"));
				}
				
				// Comprobar forma
				if ($estadoPago == "Pendiente de confirmación" && buscarDatosReserva($pagos->post_id, "_mphb_gateway") == "bank") {
					$tipoPagoTransferencia=true;
				}
				
				?>
				
					<tr>
						<td><?=buscarDatosReserva($pagos->post_id, "_mphb_transaction_id");?></td>
						<td><?=comprobarEstadoPago($pagos->post_id);?></td>
						<td><?=buscarDatosReserva($pagos->post_id, "_mphb_payment_type");?></td>
						<td><?=buscarDatosReserva($pagos->post_id, "_mphb_fee");?> €</td>
					</tr>
				
				<?php
			}
		
		?>
					
			<tr>
				<td colspan="2">Total pagado</td>
				<td colspan="2"><?=$sumaPago;?> €</td>
			</tr>
			
			<tr>
				<td colspan="2">A pagar</td>
				<td colspan="2"><?=$prezoReserva;?> €</td>
			</tr>
			
			<tr>
				<td colspan="4"><a onClick="entrarPagoCliente()">Info pago</a></td>
			</tr>
		
		</table>
		
		</br>
		</br>
		
		<?php
		
			// Mostrar boton aceptar transferencia
			if ($tipoPagoTransferencia) {
				?>
					<input type="submit" id="validarPagoCliente" onClick="aceptarReservaCliente()" value="Validar transferencia cliente" />
				<?php
			}
		
		?>
				
		<?php
			$facturaBase64 = descargarFacturaCliente($idReserva);
			if ($facturaBase64 != null) {
		?>
			</br>
			</br>
		
			<a href="<?=$facturaBase64;?>" download="factura_cliente_<?=$idReserva;?>.pdf">
				<input type="submit" id="verFacturaCliente" value="Ver factura" />
			</a>
			
			</br>
			</br>
			
			<input type="submit" id="verFacturaCliente" onClick="reenviarCorreoFacturaCliente()" value="Reenviar mail factura" />
			
		<?php
			}
		?>
				
		</br>
		</br>

	</fieldset>

		</br>

	
		<h2>Datos Clientes</h2>
	

		</br>

		<script>
		
			var idReserva="<?=$idReserva;?>";
			var numeroClientes = 0;
			var anadidoClientePrincipal = false; 
				
			window.onload = function(){
				comprobarSiExisteReservaRealizada();
			};

			// Authorization
			var authorization = "<?=get_option('hotel_token_authorization', false);?>";

			function entrarPagoCliente() {
				
				url = "<?=get_option('siteurl', false);?>/wp-admin/post.php?post=<?=$idReserva;?>&action=edit";
				
				//window.open(url);
				window.open(url, '_blank').focus();

			}

		</script>
	
		<input type="submit" id="subirdocumentoHotelPolicia" onClick="crearContenedorDatosPoliciaDocumento()" value="Subir documento policia" />
	
		<input type="submit" id="clienteReserva" onClick="anadirClienteDatosReserva()" value="Crear con datos Reserva" />
		
		<input type="submit" id="clienteEnBlanco" onClick="anadirClienteNovo()" value="Crear Usuario" />
	
		</br>
		</br>
	
	<form action="<?=$uri;?>" method="post">
	
		<div id="contenedorDocumentoPolicia">
		</div>
	
		<div id="contenedorDatosClientes">
		</div>
	
	</form>
		
		</br>
		</br>

		<input type="submit" id="registrarVisitantes" onClick="registrarVisitantes()" value="Modificar datos reserva" DISABLED/>

		<?php
		
	}
	
	?>
	
</body>
