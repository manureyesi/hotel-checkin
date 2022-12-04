<?php

defined('ABSPATH') or die( "Bye bye" );

//Comprueba que tienes permisos para acceder a esta pagina
if (! current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));

	$uri = $_SERVER['REQUEST_URI'];

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuracion Checkin</title>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

	<link rel="stylesheet" href="/wp-content/plugins/hotel-checkin/admin/css/configuracion.css">
	
</head>

<body>

	<?php
		
		if (isset($_POST["configuracionContacto"])) {
			guardarDatosContacto();
		} else if (isset($_POST["configuracionFacturacion"])) {
			guardarDatosFacturacion();
		} else if(isset($_POST["configuracionRedsys"])) {
			guardarDatosRedsys();
		} else if (isset($_POST["configuracionCuenta"])) {
			guardarDatosTrasnferenciaBancaria();
		} else if (isset($_POST["configuracionParametrosHotel"])) {
			guardarDatosConfiguracionHotel();
		}
	
	
	?>
	
	<h1>Datos Comercio</h1>
	
	<h3>Datos Empresa Contacto</h3>
	
	<form action="<?=$uri;?>" method="post">
	
		<table class="form-table" role="presentation">
		
		<tbody>
		
			<input type="hidden" id="configuracionContacto" name="configuracionContacto" value="configuracionContacto">
		
			<tr>
				<th scope="row">
					<label for="idEmailEmpresa">Email empresa contacto:</label>
				</th>
				<td>
					<input type="text" id="idEmailEmpresa" name="idEmailEmpresa" value="<?=get_option('contacto_email_empresa', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idTelefonoEmpresa">Telefono empresa contacto:</label>
				</th>
				<td>
					<input type="text" id="idTelefonoEmpresa" name="idTelefonoEmpresa" value="<?=get_option('contacto_telefono_empresa', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<input type="submit" id="botonActualizar" value="Guardar Cambios" />
				</th>
			</tr>
		
		</tbody>

		</table>
	
	</form>
	
	<h3>Datos Empresa Facturación</h3>
	
	<form action="<?=$uri;?>" method="post">
	
		<table class="form-table" role="presentation">
		
		<tbody>
		
			<input type="hidden" id="configuracionFacturacion" name="configuracionFacturacion" value="configuracionFacturacion">
		
			<tr>
				<th scope="row">
					<label for="idNombreFacturaEmpresa">Nombre Empresa Factura:</label>
				</th>
				<td>
					<input type="text" id="idNombreFacturaEmpresa" name="idNombreFacturaEmpresa" value="<?=get_option('factura_nombre_empresa', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idCifFacturaEmpresa">CIF Empresa Factura:</label>
				</th>
				<td>
					<input type="text" id="idCifFacturaEmpresa" name="idCifFacturaEmpresa" value="<?=get_option('factura_cif_empresa', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idDireccionFacturaEmpresa">Dirección Factura:</label>
				</th>
				<td>
					<input type="text" id="idDireccionFacturaEmpresa" name="idDireccionFacturaEmpresa" value="<?=get_option('factura_direccion_empresa', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idFotoFacturaEmpresa">Foto Factura:</label>
				</th>
				<td>
					<input type="text" id="idFotoFacturaEmpresa" name="idFotoFacturaEmpresa" value="<?=get_option('factura_url_foto_empresa', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<input type="submit" id="botonActualizar" value="Guardar Cambios" />
				</th>
			</tr>
		
		</tbody>

		</table>
	
	</form>

	<h1>Configuración Pasarelas de Pago</h1>
	
	<h3>Pago Redsys</h3>
	
	<form action="<?=$uri;?>" method="post">
	
		<table class="form-table" role="presentation">
		
		<tbody>
	
			<input type="hidden" id="configuracionRedsys" name="configuracionRedsys" value="configuracionRedsys">
			
			<tr>
				<th scope="row">
					<?php
						$activadaRedsys = get_option('redsys_activada_redsys', false);
						
						// Comprobar Activada Trasnferencias
						if ($activadaRedsys==null) {
							?>
							<input type="checkbox" id="activoRedys" name="activoRedys" value="Activar">
							<?php
						} else {
							?>
							<input type="checkbox" id="activoRedys" name="activoRedys" value="Activar" checked>
							<?php
						}
						
					?>
					<label for="activoRedys">Activar Redsys</label>
				</th>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idComercio">ID Comercio Redsys:</label>
				</th>
				<td>
					<input type="text" id="idComercio" name="idComercio" value="<?=get_option('redsys_id_comercio', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idCalveSecreta">Clave Secreta Redsys:</label>
				</th>
				<td>
					<?php
				
						$claveSecretaRedsys = get_option('redsys_clave_secreta', false);
						if ($claveSecretaRedsys == null) {
							?>
							<input type="text" id="idCalveSecreta" name="idCalveSecreta" value="" size="100">
							<?php
						} else {
							?>
							<input type="text" id="idCalveSecreta" name="idCalveSecreta" value="***********" size="100">
							<?php
						}
					
					?>
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idTerminalPago">Terminal Pago Redsys:</label>
				</th>
				<td>
					<input type="text" id="idTerminalPago" name="idTerminalPago" value="<?=get_option('redsys_terminal_pago', false);?>" size="4">
				</td>
			</tr>
			
			<fieldset>
			
				<tr>
					<th scope="row">
						<legend>Entorno:</legend>
					</th>
				</tr>
				
				<tr>
					<th scope="row">
						<?php
				
						$entorno = get_option('redsys_entorno', false);
						if ($entorno == null || $entorno == "TEST") {
							?>
							<div>
							  <input type="radio" id="radioButonTest" name="entorno" value="TEST" checked>
							  <label for="radioButonTest">TEST</label>
							</div>

							<div>
							  <input type="radio" id="radioButonPro" name="entorno" value="PRO">
							  <label for="radioButonPro">PRO</label>
							</div>
							<?php
						} else if ($entorno == "PRO") {
							?>
							<div>
							  <input type="radio" id="radioButonTest" name="entorno" value="TEST">
							  <label for="radioButonTest">TEST</label>
							</div>

							<div>
							  <input type="radio" id="radioButonPro" name="entorno" value="PRO" checked>
							  <label for="radioButonPro">PRO</label>
							</div>
							<?php
						}
						
						?>
					</th>
				</tr>
				
			</fieldset>
			
			<tr>
				<th scope="row">
					<input type="submit" id="botonActualizar" value="Guardar Cambios" />
				</th>
			</tr>
		
		</tbody>

		</table>
		
	</form>

	</br>
	</br>

	<h3>Pago Transferencia Bancaria</h3>

	<form action="<?=$uri;?>" method="post">

		<input type="hidden" id="configuracionCuenta" name="configuracionCuenta" value="configuracionCuenta" type="hidden">
	
		<table class="form-table" role="presentation">
		
		<tbody>

			<tr>
				<th scope="row">
					<?php
						$activadaTransferenciaBancaria = get_option('transferencia_activada_transferencia', false);
						
						// Comprobar Activada Trasnferencias
						if ($activadaTransferenciaBancaria==null) {
							?>
							<input type="checkbox" id="activoTransferencia" name="activoTransferencia" value="Activar">
							<?php
						} else {
							?>
							<input type="checkbox" id="activoTransferencia" name="activoTransferencia" value="Activar" checked>
							<?php
						}
						
					?>
					<label for="activoTransferencia">Activar Trasnferencias</label>
				</th>
			</tr>

			<tr>
				<th scope="row">
					<label for="idNumeroCuenta">Número de cuenta:</label>
				</th>
				<td>
					<input name="numeroCuenta" type="text" id="idNumeroCuenta" value="<?=get_option('transferencia_numero_cuenta_transferencia', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idNombreTitular">Nombre titular cuenta:</label>
				</th>
				<td>
					<input name="nombreTitular" type="text" id="idNombreTitular" value="<?=get_option('transferencia_titular_cuenta_transferencia', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="idBancoCuenta">Nombre entidad bancaria:</label>
				</th>
				<td>
					<input name="nombreBancoCuenta" type="text" id="idBancoCuenta" value="<?=get_option('transferencia_entidad_cuenta_transferencia', false);?>" size="100">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<input type="submit" id="botonActualizar" value="Guardar Cambios" />
				</th>
			</tr>
		
		</tbody>

		</table>

	</form>

	<h3>Configuración Hotel</h3>

	<form action="<?=$uri;?>" method="post">

		<input type="hidden" id="configuracionParametrosHotel" name="configuracionParametrosHotel" value="configuracionParametrosHotel" type="hidden">
	
		<table class="form-table" role="presentation">
		
		<tbody>
		
			<tr>
				<th scope="row">
					<label for="horaPermitidaEntrada">Hora permitida entrada Alojamiento (Ex: 14:00):</label>
				</th>
				<td>
					<input name="horaPermitidaEntrada" type="text" id="horaPermitidaEntrada" value="<?=get_option('booking_fecha_permitida_llegada', false);?>" class="regular-text">
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="horaPermitidaSalida">Hora permitida salida Alojamiento (Ex: 12:00):</label>
				</th>
				<td>
					<input name="horaPermitidaSalida" type="text" id="horaPermitidaSalida" value="<?=get_option('booking_fecha_permitida_salida', false);?>" class="regular-text">
				</td>
			</tr>
			
			<tr>
				<th scope="row">
					<label for="correosAdminEnvioCorreo">Correos envio Admin:</label>
				</th>
				<td>
					<input name="correosAdminEnvioCorreo" type="text" id="correosAdminEnvioCorreo" value="<?=get_option('booking_correos_admin', false);?>" size="200">
				</td>
			</tr>

			<tr>
				<th scope="row">
					<input type="submit" id="botonActualizar" value="Guardar Cambios" />
				</th>
			</tr>
		
		</tbody>

		</table>


	</form>

</body>