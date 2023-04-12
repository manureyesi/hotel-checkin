<?php

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	
	$json = file_get_contents('php://input');

	// Converts it into a PHP object
	$data = json_decode($json);
		
	$output = "<table width='100%' border='0' cellpadding='5' cellspacing='0'>
		<tr>
			<td colspan='2' style='font-size:30px'>
				<b>FACTURA #{$data->num_factura}</b>
			</td>
		</tr>
		<tr>
			<td style='width:190px;'>
				<img style='width:180px;' src='{$data->foto_factura}'>
			</td>
			<td align='left' style='font-size:18px'>
				<b>{$data->nombre_empresa}</b><br/>
				CIF: {$data->cif_empresa}<br/>
				{$data->direccion_empresa}<br/> 
				{$data->email_empresa}<br/>
				{$data->telefono_empresa}<br/>
			</td>
		</tr>
		<tr style='height:50px;'>
			<td colspan='2'></td>
		</tr>
		<tr>
		
		<td colspan='2'>
		<table width='100%' cellpadding='5'>
		<tr>
		<td width='65%'>
		Para,<br />
		<b>RECEPTOR (FACTURA A)</b><br />
		Nombre : {$data->nombre_factura_cliente}<br /> 
		Dirección de facturación : {$data->direccion_factura_cliente}<br />
		</td>
		<td width='35%'>         
		Factura No. : {$data->num_factura}<br />
		Factura Fecha : {$data->fecha_factura}<br />
		</td>
		</tr>
		</table>
		<br />
		<table width='100%' border='1' cellpadding='5' cellspacing='0'>
		<tr>
		<th align='left'>Cantidad</th>
		<th align='left'>Nombre</th>
		<th align='left'>Descripcion Producto</th>
		<th align='left'>Precio Unitario</th>
		<th align='left'>Importe</th> 
		</tr>";
		
	foreach ($data->listaServicios as $servicio) {
		$output .= 	"
		<tr>
			<td align='left'>{$servicio->cantidad}</td>
			<td align='left'>{$servicio->servicio}</td>
			<td align='left'>{$servicio->descripcion}</td>
			<td align='left'>{$servicio->precio}</td>
			<td align='left'>{$servicio->total}</td>   
		</tr>";
	}
		
	$output .= 	"
		
		<tr style='height:70px;'>
			<td colspan='5'></td>
		</tr>
		
		<tr>
		<td colspan='5'>
		<table width='100%' border='1' cellpadding='5' cellspacing='0'>
		<tr>
		<td align='right'><b>Sub Total</b></td>
		<td align='left'><b>{$data->sub_total}</b></td>
		</tr>
		<tr>
		<td align='right'><b>{$data->desccripcion_impuesto} :</b></td>
		<td align='left'>{$data->importe_impuesto}</td>
		</tr>
		<tr>
		<td align='right'>Total: </td>
		<td align='left'>{$data->total}</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>";
	
	
		// create pdf of invoice	
		//$invoiceFileName = 'Invoice-1233.pdf';
		// Importase dende o php principal
		/*require_once 'dompdf/src/Autoloader.php';
		Dompdf\Autoloader::register();
		use Dompdf\Dompdf;
		//$dompdf = new Dompdf();
		$dompdf = new Dompdf(array('enable_remote' => true));
		$dompdf->loadHtml(html_entity_decode($output));
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		//$dompdf->stream($invoiceFileName, array('Attachment' => false));

		//$archivoBase64='data:application/pdf;base64,'.base64_encode($dompdf->stream());

		return 'data:application/pdf;base64,'.base64_encode($dompdf->stream()));
	*/
	
	// create pdf of invoice	
$invoiceFileName = "Invoice-{$data->num_factura}.pdf";
require_once 'dompdf/src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
$dompdf = new Dompdf(array('enable_remote' => true));
$dompdf->loadHtml(html_entity_decode($output));
$dompdf->setPaper('A4', 'portrait'); 
$dompdf->render();
$dompdf->stream($invoiceFileName);

//$base64 = "data:application/pdf;base64,".base64_encode($dompdf->stream());
//$base64 = base64_encode($dompdf->stream());

//$response = array("resultado" => 0, "descripcion" => "OK", "archivoBase64" => $_POST['image']);
//header("Content-Type: application/json");
//echo json_encode($response);
?>   
