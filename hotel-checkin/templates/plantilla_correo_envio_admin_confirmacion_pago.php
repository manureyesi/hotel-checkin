<?php

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );

	function getPlantillaConfirmacionReservaAdmin () {
    $plantillaHtml = '
    <div id="x_wrapper" style="background-color: rgb(55, 55, 55) !important; margin: 0px; padding: 70px 0px; width: 100%;" data-ogsb="rgb(245, 245, 245)">
    <div style="height: 826px; width: 100%;" class="R1UVb" has-hovered="true">
        <div class="qF8_5"><button type="button" class="ms-Button ms-Button--icon wD8TJ root-285" title="Mostrar tamaño original" aria-label="Mostrar tamaño original" data-is-focusable="true"><span class="ms-Button-flexContainer flexContainer-164" data-automationid="splitbuttonprimary"><i data-icon-name="FullScreen" aria-hidden="true" class="ms-Icon root-90 css-172 ms-Button-icon icon-166" style="font-family: &quot;controlIcons&quot;;"></i></span></button></div>
        <table style="transform: scale(0.868771); transform-origin: left top 0px;" min-scale="0.8687707641196013" width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
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
                                            <h1 style="color:#ffffff; font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; font-size:30px; font-weight:300; line-height:150%; margin:0">Reserva confirmada</h1>
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
                                                            La reserva #%ID_RESERVA% ha sido confirmada por el cliente.<br aria-hidden="true"><br aria-hidden="true"><a href="%BASE_URL%/wp-admin/admin.php?page=hotel-checkin%2Fadmin%2Fcheckin-cliente.php&busqueda=%ID_RESERVA%" target="_blank" rel="noopener noreferrer" data-auth="NotApplicable" style="color: rgb(141, 180, 218) !important; font-weight: normal; text-decoration: underline;" data-linkindex="0" data-ogsc="rgb(85, 125, 161)">Ver reserva</a> 
                                                            <h4>Detalles de reserva</h4>
                                                            Llegada: : %FECHA_LLEGADA%, hasta las %HORA_PERMITIDA_LLEGADA%<br aria-hidden="true">
                                                            Salida: %FECHA_SALIDA%, hasta las %HORA_PERMITIDA_SALIDA%<br aria-hidden="true">
                                                            
                                                            <h4>Alojamiento #1</h4>
                                                            Adultos: %NUM_ADULTOS%<br aria-hidden="true">
                                                            Niños: %NUM_NINOS%<br aria-hidden="true">
                                                            Alojamiento: %ALOJAMIENTO%<br aria-hidden="true">

                                                            <h4>Datos pago:</h4>
                                                            Precio Total: %PRECIO_TOTAL% €<br aria-hidden="true">
                                                            Tipo Pago: %TIPO_PAGO%<br aria-hidden="true">
                                                            Estado: %ESTADO_PAGO%<br aria-hidden="true">

                                                            <h4>Información de cliente</h4>
                                                            Nombre: %NOMBRE% %APELLIDO%<br aria-hidden="true">
                                                            Correo electrónico: %CORREO_ELECTRONICO%<br aria-hidden="true">
                                                            Teléfono: %TELEFONO_CONTACTO%<br aria-hidden="true">
                                                            Nota: %NOTA_CLIENTES%<br aria-hidden="true">
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
    </div>
    </div>';
    return $plantillaHtml;
    }

?>
