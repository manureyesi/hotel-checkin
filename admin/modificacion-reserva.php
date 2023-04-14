<?php

defined('ABSPATH') or die( "Bye bye" );

//Archivos externos
//include(VISITAS_RUTA.'/includes/opciones.php');
//include(CHECKIN_RUTA.'/includes/modificacionDatosReserva.php');
//include(VISITAS_RUTA.'/includes/crud.php');

//Comprueba que tienes permisos para acceder a esta pagina
if (! current_user_can ('edit_pages')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Reserva</title>
	
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
		
	<h2>Buscar Datos reserva:</h2>
	
	<form action="<?=$uri;?>" method="post">
		<label for="filtradoPor">Buscar cliente por:</label>
		<select name="filtradoPor" id="filtradoPor">
			<option value="idreserva">Id Reserva</option>
			<option value="email">Email</option>
			<option value="telefono">Telefono</option>
			<option value="nombre">Nombre</option>
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
	
		$uri = $_SERVER['REQUEST_URI'];
		
		if(isset($_POST["crearNuevaReserva"])) {
						
			eliminarReservaPorId($_POST["idReserva"]);
			$idReservaManual = crearReservaManualUsuarioConId($_POST["idReserva"]);
			$urlReserva = get_option('siteurl', false)."/wp-admin/admin.php?page=hotel-checkin%2Fadmin%2Fcheckin-cliente.php&busqueda={$idReservaManual}";
	?>
			
			<script>
			
				function refrescarPagina() {
					window.location.href = '<?=$uri;?>';					
				}
			
				function verDatosReservaCreados() {
					window.location.href = '<?=$urlReserva;?>';						
				}
			
			</script> 
			
			<h2>Reserva manual creada correctamente</h2>
			
			<input type="submit" id="idrefrescar" onclick="refrescarPagina()" value="Refrescar">
			
			<input type="submit" id="idverDatosContratacion" onclick="verDatosReservaCreados()" value="Ver datos Reserva creada">
			
		<?php
		} else {
			
			if(isset($idReserva)) {
				
				$nomeCliente=buscarDatosReserva($idReserva, 'mphb_first_name');
				$apelidoCliente=buscarDatosReserva($idReserva, 'mphb_last_name');
				
				$emailReserva=buscarDatosReserva($idReserva, 'mphb_email');
				$telefonoReserva=buscarDatosReserva($idReserva, 'mphb_phone');
				
				$datosAloxamento = json_decode(buscarDatosReserva($idReserva, '_mphb_booking_price_breakdown'));
				
				$prezoReserva=buscarDatosReserva($idReserva, 'mphb_total_price');
				
				$fechaEntradaReserva=buscarDatosReserva($idReserva, 'mphb_check_in_date');
				$fechaSaidaReserva=buscarDatosReserva($idReserva, 'mphb_check_out_date');
				
				$notaReserva=buscarDatosReserva($idReserva, 'mphb_note');
		
		?>

    <div class="wrap">

		<h2>Datos nueva Reserva:</h2>
		
		<form action="<?=$uri;?>" method="post">
		
		<table class="form-table" role="presentation">
		
		<tbody>
				
			<tr>
				<th scope="row">
					<h3>Datos cliente</h3>
				</th>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idReserva">ID Reserva:</label>
				</th>
				<td>
					<input name="idReserva" type="text" id="idReserva" disabled value="<?=$idReserva;?>" class="regular-text">
				</td>
			</tr>
				
			<tr>
				<th scope="row">
					<label for="nombreReserva">Nombre:</label>
				</th>
				<td>
					<input name="nombreReserva" type="text" id="nombreReserva"  value="<?=$nomeCliente;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="apellidoReserva">Apellidos:</label>
				</th>
				<td>
					<input name="apellidoReserva" type="text" id="apellidoReserva" value="<?=$apelidoCliente;?>" class="regular-text">
				</td>
			</tr>
		
			<tr>
				<th scope="row">
					<label for="emailReserva">Email:</label>
				</th>
				<td>
					<input name="emailReserva" type="text" id="emailReserva" value="<?=$emailReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="telefonoReserva">Telefono:</label>
				</th>
				<td>
					<input name="telefonoReserva" type="text" id="telefonoReserva" value="<?=$telefonoReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<h3>Datos dirección</h3>
				</th>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="direccionReserva">Direccion:</label>
				</th>
				<td>
					<input name="direccionReserva" type="text" id="direccionReserva" value="<?=buscarDatosReserva($idReserva, 'mphb_address1');?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="postalCodeReserva">Codigo Postal:</label>
				</th>
				<td>
					<input name="postalCodeReserva" type="text" id="postalCodeReserva" value="<?=buscarDatosReserva($idReserva, 'mphb_zip');?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="ciudadReserva">Ciudad:</label>
				</th>
				<td>
					<input name="ciudadReserva" type="text" id="ciudadReserva" value="<?=buscarDatosReserva($idReserva, 'mphb_city');?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="provinciaReserva">Provincia:</label>
				</th>
				<td>
					<input name="provinciaReserva" type="text" id="provinciaReserva" value="<?=buscarDatosReserva($idReserva, 'mphb_state');?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="paisReserva">Codigo Pais:</label>
				</th>
				<td>
					<input name="paisReserva" type="text" id="paisReserva" value="<?=buscarDatosReserva($idReserva, 'mphb_country');?>" class="regular-text">
				</td>
			</tr>
							
			<tr>
				<th scope="row">
					<h3>Datos alojamiento</h3>
				</th>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="nombreAlojamiento">Alojamiento:</label>
				</th>
				<td>
					<select name="nombreAlojamiento" id="nombreAlojamiento">
						
						<option value="<?=$datosAloxamento->rooms[0]->room->type;?>"><?=$datosAloxamento->rooms[0]->room->type;?></option>
							<?php
							$listaAlojamiento = consultarPostPorTipoYEstado("mphb_room", "publish");
							
							foreach ($listaAlojamiento as $resultado) {
								if ($resultado->post_title!=$datosAloxamento->rooms[0]->room->type) {
								?>
									<option value="<?=$resultado->post_title;?>"><?=$resultado->post_title;?></option>
								<?php
								}
							}
							
						?>
					</select>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="precioReserva">Precio:</label>
				</th>
				<td>
					<input name="precioReserva" type="text" id="precioReserva" value="<?=$prezoReserva;?>" class="regular-text" size="5">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="numeroAdultos">Nº Adultos:</label>
				</th>
				<td>
					<input name="numeroAdultos" type="text" id="numeroAdultos" value="<?=$datosAloxamento->rooms[0]->room->adults;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="numeroNinos">Nº Niños:</label>
				</th>
				<td>
					<input name="numeroNinos" type="text" id="numeroNinos" value="<?=$datosAloxamento->rooms[0]->room->children;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="entradaReserva">Fecha Entrada:</label>
				</th>
				<td>
					<input name="entradaReserva" type="date" id="entradaReserva" value="<?=$fechaEntradaReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="saidaReserva">Fecha Saida:</label>
				</th>
				<td>
					<input name="saidaReserva" type="date" id="saidaReserva" value="<?=$fechaSaidaReserva;?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<!-- No se envia por defecto en caso de no marcar se envia correo confirmacion -->
					<label for="envioCorreoPago"><input type="checkbox" name="envioCorreoPago" id="idenvioCorreoPago" value="envioCorreoPago">Reenvio de correo de pago</label>
				</th>
				
			</tr>
			
			<tr>
				<th scope="row">
					<label for="notasReserva">Notas:</label>
				</th>
				<td>
					<textarea name="notasReserva" id="notasReserva" rows="5" cols="50" class="regular-text"><?=$notaReserva;?></textarea>
				</td>
			</tr>
		
		</tbody>
				
		</table>
        
        <input type="submit" name="crearNuevaReserva" id="crearNuevaReserva" value="Crear Reserva" />
		
		</form> 
		
	</div>
	
	<?php
			}
		}
	?>
		
</body>