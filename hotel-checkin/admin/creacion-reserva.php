<?php

    //Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );

    //Comprueba que tienes permisos para acceder a esta pagina
    if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkin Cliente</title>
	
	<script src="/wp-content/plugins/hotel-checkin/admin/js/crear-reserva.js"></script>

	<link rel="stylesheet" href="/wp-content/plugins/hotel-checkin/admin/css/reserva.css">
	
</head>

<body>

	<?php
	
		$uri = $_SERVER['REQUEST_URI'];
		
		if(isset($_POST["crearNuevaReserva"])) {
			$idReservaManual = crearReservaManualUsuario(); 
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
					<label for="nombreReserva">Nombre:</label>
				</th>
				<td>
					<input name="nombreReserva" type="text" id="nombreReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="apellidoReserva">Apellidos:</label>
				</th>
				<td>
					<input name="apellidoReserva" type="text" id="apellidoReserva" value="" class="regular-text">
				</td>
			</tr>
		
			<tr>
				<th scope="row">
					<label for="emailReserva">Email:</label>
				</th>
				<td>
					<input name="emailReserva" type="text" id="emailReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="telefonoReserva">Telefono:</label>
				</th>
				<td>
					<input name="telefonoReserva" type="text" id="telefonoReserva" value="" class="regular-text">
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
					<input name="direccionReserva" type="text" id="direccionReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="postalCodeReserva">Codigo Postal:</label>
				</th>
				<td>
					<input name="postalCodeReserva" type="text" id="postalCodeReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="ciudadReserva">Ciudad:</label>
				</th>
				<td>
					<input name="ciudadReserva" type="text" id="ciudadReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="provinciaReserva">Provincia:</label>
				</th>
				<td>
					<input name="provinciaReserva" type="text" id="provinciaReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="paisReserva">Codigo Pais:</label>
				</th>
				<td>
					<input name="paisReserva" type="text" id="paisReserva" value="" class="regular-text">
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
						<?php
						
							$listaAlojamiento = consultarPostPorTipoYEstado("mphb_room", "publish");
							
							foreach ($listaAlojamiento as $resultado) {
								?>
									<option value="<?=$resultado->post_title;?>"><?=$resultado->post_title;?></option>
								<?php
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
					<input name="precioReserva" type="text" id="precioReserva" value="" class="regular-text" size="5">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="numeroAdultos">Nº Adultos:</label>
				</th>
				<td>
					<input name="numeroAdultos" type="text" id="numeroAdultos" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="numeroNinos">Nº Niños:</label>
				</th>
				<td>
					<input name="numeroNinos" type="text" id="numeroNinos" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="entradaReserva">Fecha Entrada:</label>
				</th>
				<td>
					<input name="entradaReserva" type="date" id="entradaReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="saidaReserva">Fecha Saida:</label>
				</th>
				<td>
					<input name="saidaReserva" type="date" id="saidaReserva" value="" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<!-- No se envia por defecto en caso de no marcar se envia correo confirmacion -->
					<label for="envioCorreoPago"><input type="checkbox" name="envioCorreoPago" id="idenvioCorreoPago" value="envioCorreoPago">Envio de correo de pago</label>
				</th>
				
			</tr>
			
			<tr>
				<th scope="row">
					<label for="notasReserva">Notas:</label>
				</th>
				<td>
					<textarea name="notasReserva" id="notasReserva" rows="5" cols="50" class="regular-text">

                    </textarea>
				</td>
			</tr>
		
		</tbody>
				
		</table>
        
        <input type="submit" name="crearNuevaReserva" id="crearNuevaReserva" value="Crear Reserva" />
		
		</form> 
		
	</div>
	
	<?php
		}
	?>

</body>
