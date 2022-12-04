<?php

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );

	function getPlantillaFacturaReserva () {
		$plantillaHtml = '
		<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
			<tbody>
			<tr>
			<td valign="top" align="center">
			<table id="x_template_container" style="background-color: rgb(52, 52, 52) !important; border: 1px solid rgb(220, 220, 220); border-radius: 3px !important;" data-ogsb="rgb(253, 253, 253)" width="600" cellspacing="0" cellpadding="0" border="0">
			   <tbody>
				  <tr>
					 <td valign="top" align="center">
						<table id="x_template_header" style="background-color: rgb(81, 122, 157) !important; border-radius: 3px 3px 0px 0px !important; color: rgb(255, 255, 255); border-bottom: 0px none; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif;" data-ogsb="rgb(85, 125, 161)" width="600" cellspacing="0" cellpadding="0" border="0">
						   <tbody>
							  <tr>
								 <td id="x_header_wrapper" style="padding:36px 48px; display:block">
									<h1 style="color:#ffffff; font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; font-size:30px; font-weight:300; line-height:150%; margin:0">Envio factura reserva</h1>
								 </td>
							  </tr>
						   </tbody>
						</table>
					 </td>
				  </tr>
				  <tr>
					 <td valign="top" align="center"> 
						<table id="x_template_body" width="600" cellspacing="0" cellpadding="0" border="0">
						   <tbody>
							  <tr>
								 <td id="x_body_content" style="background-color: rgb(52, 52, 52) !important;" data-ogsb="rgb(253, 253, 253)" valign="top">
									<table width="100%" cellspacing="0" cellpadding="20" border="0">
									   <tbody>
										  <tr>
											 <td style="padding:48px" valign="top">
												<div id="x_body_content_inner" style="color: rgb(177, 177, 177) !important; font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif; font-size: 14px; line-height: 150%;" data-ogsc="rgb(115, 115, 115)">
												   <p style="margin:0 0 16px">Estimado/a %NOMBRE% %APELLIDO%, se ha generado su factura!</p>
												   <h4>Detalles de reserva</h4>
												   <p style="margin:0 0 16px">ID: #%ID_RESERVA%<br aria-hidden="true">Llegada: : %FECHA_LLEGADA%, hasta las %HORA_PERMITIDA_LLEGADA%<br aria-hidden="true">Salida: %FECHA_SALIDA%, hasta las %HORA_PERMITIDA_SALIDA%<br aria-hidden="true"><br aria-hidden="true">
												   <a href="%BASE_URL%/confirmacion-de-reserva/reserva-confirmada/?booking_id=%ID_RESERVA%&amp;booking_key=%BOOKING_KEY%" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color: rgb(141, 180, 218) !important; font-weight: normal; text-decoration: underline;" data-linkindex="0" data-ogsc="rgb(85, 125, 161)">Ver reserva</a> <br aria-hidden="true"></p>
												   <h4>Alojamiento #1</h4>
												   Adultos: %NUM_ADULTOS%<br aria-hidden="true">
												   Niños: %NUM_NINOS%<br aria-hidden="true">
												   Alojamiento: %ALOJAMIENTO%<br aria-hidden="true">
												   
												   <h4>Datos factura:</h4>
												   Precio Total: %PRECIO_TOTAL% €<br aria-hidden="true">
												   <a href="%URL_FACTURA_CLIENTE%" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color: rgb(141, 180, 218) !important; font-weight: normal; text-decoration: underline;" data-linkindex="0" data-ogsc="rgb(85, 125, 161)">Ver Factura</a> <br aria-hidden="true">

												   <h4>Información sobre cliente</h4>
												   <p style="margin:0 0 16px">Nombre: %NOMBRE% %APELLIDO%<br aria-hidden="true">Correo electrónico: %CORREO_ELECTRONICO%<br aria-hidden="true">Teléfono: %TELEFONO_CONTACTO%<br aria-hidden="true">Nota: %NOTA_CLIENTES%<br aria-hidden="true"><br aria-hidden="true">¡Gracias!</p>
												</div>
											 </td>
										  </tr>
									   </tbody>
									</table>
								 </td>
							  </tr>
						   </tbody>
						</table>
					 </td>
				  </tr>
				  <tr>
					 <td valign="top" align="center">
						<table id="x_template_footer" width="600" cellspacing="0" cellpadding="10" border="0">
						   <tbody>
							  <tr>
								 <td style="padding:0; -webkit-border-radius:6px" valign="top">
									<table width="100%" cellspacing="0" cellpadding="10" border="0">
									   <tbody>
										  <tr>
											 <td colspan="2" id="x_credit" style="padding:0 48px 48px 48px; -webkit-border-radius:6px; border:0; color:#99b1c7; font-family:Arial; font-size:12px; line-height:125%; text-align:center" valign="middle">
												<p><a href="%BASE_URL%" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color: rgb(141, 180, 218) !important; font-weight: normal; text-decoration: underline;" data-linkindex="2" data-ogsc="rgb(85, 125, 161)">Casa Manola</a></p>
											 </td>
										  </tr>
									   </tbody>
									</table>
								 </td>
							  </tr>
						   </tbody>
						</table>
					 </td>
				  </tr>
			   </tbody>
			</table>
			</td>
			</tr>
			</tbody>
		</table>
		';
		return $plantillaHtml;
	}

?>