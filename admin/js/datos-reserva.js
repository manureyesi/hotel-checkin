
function crearContenedorClientesDatosReserva() {
	
	numeroClientes = numeroClientes + 1;
	
	jQuery("<div id='cliente" + numeroClientes + "'><fieldset class='field_set'><h2>Cliente #" + numeroClientes + "</h2><label for='nombreCliente'>Nombre:</label><input type='text' class='nombreCliente' name='nombreCliente' size='100' minlength='5'></br></br><label for='apellidosCliente'>Apellidos:</label><input type='text' class='apellidosCliente' name='apellidosCliente' size='100' minlength='5'></br></br><label for='documentoCliente'>Documento:</label><input type='text' class='documentoCliente' name='documentoCliente' size='20' minlength='7'></br></br><label for='fotoDocumentoClienteCara'>Foto documento cara:</label><input type='file' class='fotoDocumentoClienteCara' capture='camera'><input type='hidden' class='foto_documento_1_guardado' name='foto_documento_1_guardado'><input type='hidden' class='foto_documento_2_guardado' name='foto_documento_2_guardado'></br></br><label for='fotoDocumentoClienteReverso'>Foto documento reverso:</label><input type='file' class='fotoDocumentoClienteReverso' capture='camera'></br></br><img class='imagenDocumento foto_documento_1_img'/></br></br><img class='imagenDocumento foto_documento_2_img'/></fieldset></div>")
			.appendTo("#contenedorDatosClientes");
		
	// Añadir boton registrar visitantes
	$("#registrarVisitantes").prop('disabled', false);
	
}

function crearContenedorDatosPoliciaDocumento() {
	
	jQuery("<label for='fotoDocumentoPoliciaSubido'>Foto documento checkin policia:</label><input type='file' id='fotoDocumentoPoliciaSubido' capture='camera'><input type='hidden' class='foto_documento_policia_guardado' name='foto_documento_policia_guardado'></br>")
			.appendTo("#contenedorDocumentoPolicia");
	
	// Añadir boton registrar visitantes
	$("#registrarVisitantes").prop('disabled', false);
	
}

function crearContenedorVisualizarDatosPoliciaDocumento() {
	
	jQuery("<a id='enlace_foto_policia_ver_externo' target='_blank'><img class='imagenDocumento' id='foto_documento_policia_firmado'/></a></br>")
			.appendTo("#contenedorDocumentoPolicia");
	
}

function subirdocumentoHotelPolicia(idReservaAux, archivoBase64) {
	
	// Subir documento policia firmado
	var jsonFotoDocumentoPolicia = new Object();
	jsonFotoDocumentoPolicia.idReserva = idReservaAux;
	jsonFotoDocumentoPolicia.fotoBase64 = archivoBase64;
	
	$.ajax({
		url: "/wp-json/hotel/v2/subirDocumentoFirmadoPolicia/", // url del recurso
		headers: { 'authorization': authorization },
		type: "post",
		data: JSON.stringify(jsonFotoDocumentoPolicia), // datos a pasar al servidor, en caso de necesitarlo
		success: function (r) {
		}
	});
	
}

function anadirClienteDatosReserva() {

	if (!anadidoClientePrincipal) {

		crearContenedorClientesDatosReserva();

		$("#cliente" + numeroClientes + " .nombreCliente").val($("#nombreReserva").val());
		$("#cliente" + numeroClientes + " .apellidosCliente").val($("#apellidoReserva").val());

		// Añadir boton registrar visitantes
		$("#registrarVisitantes").prop('disabled', false);

		// Desactivar boton clientes reservas
		$("#clienteReserva").prop('disabled', true);
	
		anadidoClientePrincipal = true;

	}

}

function anadirClienteNovo() {

	crearContenedorClientesDatosReserva();

}

function registrarVisitantes() {

	// Gardar datos Reserva
	guardarDatosReserva();

	console.info("Número de clientes analizar " + numeroClientes);

}

function guardarDatosReserva() {

	// Crear/Actualizar Checkip
	var jsonCrearCheckin = new Object();
	jsonCrearCheckin.idReserva = idReserva;
	$.ajax({
		url: "/wp-json/hotel/v2/crearCkeckin/", // url del recurso
		headers: { 'authorization': authorization },
		type: "post",
		data: JSON.stringify(jsonCrearCheckin), // datos a pasar al servidor, en caso de necesitarlo
		success: function (r) {
			
			// Comprobar documento policia
			if (!$("#fotoDocumentoPoliciaSubido").val()=='') {
				// Imagenes documentos
				var reader = new FileReader();
				reader.readAsDataURL($("#fotoDocumentoPoliciaSubido").prop('files')[0]);
				reader.onload = function () {
					subirdocumentoHotelPolicia(idReserva, reader.result);
				};
			}
			
			for (let i = 1; i <= numeroClientes; i++) {
			
				let nombreCliente = $("#cliente" + i + " .nombreCliente").val();
				let apellidoCliente = $("#cliente" + i + " .apellidosCliente").val();
				let identificadorIdentidad = $("#cliente" + i + " .documentoCliente").val();

				var jsonCliente = new Object();
				jsonCliente.idInterno = i;
				jsonCliente.requierDNI = true;
				jsonCliente.nombreCliente = nombreCliente;
				jsonCliente.apellidoCliente = apellidoCliente;
				jsonCliente.identificadorIdentidad = identificadorIdentidad;
			
				// Subir datos Usuarios
				subirDatosClientesPorIdUsuarioInterno(idReserva, jsonCliente);
				
				// Comprobar subidas IMG 1
				if (!$("#cliente" + i + " .fotoDocumentoClienteCara").val()=='') {
					// Imagenes documentos
					getBase64($("#cliente" + i + " .fotoDocumentoClienteCara").prop('files')[0], i, idReserva, 'foto_documento_1');
				} 
				
				// Comprobar subidas IMG 2
				if (!$("#cliente" + i + " .fotoDocumentoClienteReverso").val()=='') {
					// Imagenes documentos
					getBase64($("#cliente" + i + " .fotoDocumentoClienteReverso").prop('files')[0], i, idReserva, 'foto_documento_2');  
				}
							
			}
			
			// Actualizar datos
			var opcion = confirm("Modificada Reserva correctamente. Para recargar clicka en Aceptar o Cancelar");
		    if (opcion == true) {
		        location.reload();
			}
		}
	});

}

function subirDatosClientesPorIdUsuarioInterno(idReservaAux, clienteAux) {
	
	// Crear/Actualizar Checkip
	var jsonCrearUsuario = new Object();
	jsonCrearUsuario.idReserva = idReservaAux;
	jsonCrearUsuario.idUsuarioInterno = clienteAux.idInterno;
	jsonCrearUsuario.nombreCliente = clienteAux.nombreCliente;
	jsonCrearUsuario.apellidoCliente = clienteAux.apellidoCliente;
	jsonCrearUsuario.identificadorIdentidad = clienteAux.identificadorIdentidad;
	jsonCrearUsuario.requierDNI = clienteAux.requierDNI;
	
	$.ajax({
		url: "/wp-json/hotel/v2/modificarDatosClienteReserva/", // url del recurso
		headers: { 'authorization': authorization },
		type: "post",
		data: JSON.stringify(jsonCrearUsuario), // datos a pasar al servidor, en caso de necesitarlo
		success: function (r) {
		}
	});
	
}

function comprobarSiExisteReservaRealizada() {

	$.ajax({
		url: "/wp-json/hotel/v2/getDatosReserva/" + idReserva, // url del recurso
		headers: { 'authorization': authorization },
		type: "get", // podría ser get, post, put o delete.
		success: function (response) {
			//alert("Modificada Reserva correctamente");
			crearDatosClienteDePeticionRest(response);
		}
	});

}

function aceptarReservaCliente() {

	$.ajax({
		url: "/wp-json/hotel/v2/aceptarReservaTransferencia/" + idReserva, // url del recurso
		headers: { 'authorization': authorization },
		type: "get", // podría ser get, post, put o delete.
		success: function (response) {
			alert("A reserva do cliente foi aceptada correctamente");
			location.reload();
		}
	});

}

function reenviarCorreoFacturaCliente() {

	$.ajax({
		url: "/wp-json/hotel/v2/reenviarCorreoFacturaCliente/" + idReserva, // url del recurso
		headers: { 'authorization': authorization },
		type: "get", // podría ser get, post, put o delete.
		success: function (response) {
			alert("Correo factura reenviado correctamente");
		}
	});

}

function crearDatosClienteDePeticionRest(response) {
	
	console.info("Recuperando datos de reserva guardados");
	
	// Comprobar subido documento policia firmado
	if (response.documentoPolicia!=null) {
		console.info("El cliente tiene documento policia firmado");
		crearContenedorVisualizarDatosPoliciaDocumento();
		$("#foto_documento_policia_firmado").attr("src", response.documentoPolicia);
		$("#enlace_foto_policia_ver_externo").attr("href", response.documentoPolicia);
	}
	
	for (const element of response.listaClientesReserva) {
		
		crearContenedorClientesDatosReserva();
		
		$("#cliente" + numeroClientes + " .nombreCliente").val(element.nombre);
		$("#cliente" + numeroClientes + " .apellidosCliente").val(element.apellidos);
		$("#cliente" + numeroClientes + " .documentoCliente").val(element.numero_documento);
		
		// Guardar Imagenes
		$("#cliente" + numeroClientes + " .foto_documento_1_guardado").val(element.foto_documento_1);
		$("#cliente" + numeroClientes + " .foto_documento_2_guardado").val(element.foto_documento_2);	

		$("#cliente" + numeroClientes + " .foto_documento_1_img").attr("src", element.foto_documento_1);
		$("#cliente" + numeroClientes + " .foto_documento_2_img").attr("src", element.foto_documento_2);	
		
		
	}
	
	anadidoClientePrincipal = true;
	
}

function getBase64(file, idInternoAux, idReservaAux, tipoFoto) {
	var reader = new FileReader();
	reader.readAsDataURL(file);
	reader.onload = function () {
		subirFotosClientePorIdInterno(idReservaAux, idInternoAux, tipoFoto, reader.result);
	};
	reader.onerror = function (error) {
		console.log('Error: ', error);
	};
}

function subirFotosClientePorIdInterno(idReservaAux, idInterno, tipoFoto, archivoBase64) {

	// Crear/Actualizar Checkip
	var jsonFotosUsuarios = new Object();
	jsonFotosUsuarios.idReserva = idReservaAux;
	jsonFotosUsuarios.idUsuarioInterno = idInterno;
	jsonFotosUsuarios.tipoFoto = tipoFoto;
	jsonFotosUsuarios.data = archivoBase64;
	
	$.ajax({
		url: "/wp-json/hotel/v2/subirFotosClientes/", // url del recurso
		headers: { 'authorization': authorization },
		type: "post",
		data: JSON.stringify(jsonFotosUsuarios), // datos a pasar al servidor, en caso de necesitarlo
		success: function (r) {
		}
	});

}

/**
 * Resize a base 64 Image
 * @param {String} base64 - The base64 string (must include MIME type)
 * @param {Number} newWidth - The width of the image in pixels
 * @param {Number} newHeight - The height of the image in pixels
 */
function resizeBase64Img(base64, newWidth, newHeight) {
    return new Promise((resolve, reject)=>{
        var canvas = document.createElement("canvas");
        canvas.style.width = newWidth.toString()+"px";
        canvas.style.height = newHeight.toString()+"px";
        let context = canvas.getContext("2d");
        let img = document.createElement("img");
        img.src = base64;
        img.onload = function () {
            context.scale(newWidth/img.width,  newHeight/img.height);
            context.drawImage(img, 0, 0); 
            resolve(canvas.toDataURL());          
        }
    });
}

/**
 * Resize a base 64 Image
 * @param {String} base64 - The base64 string (must include MIME type)
 * @param {Number} newWidth - The width of the image in pixels
 * @param {Number} newHeight - The height of the image in pixels
 */
function resizeImageBase64(base64, fileType, newWidth, newHeight) {
    
	let img = document.createElement("img");
	img.className="img-"+Math.random();
	img.src = base64;

	let canvas = document.createElement("canvas");
	canvas.className="canvas-"+Math.random();
	var ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0);

	let MAX_WIDTH = newWidth;
	let MAX_HEIGHT = newHeight;
	let width = img.width;
	let height = img.height;

	if (width > height) {
		if (width > MAX_WIDTH) {
			height *= MAX_WIDTH / width;
			width = MAX_WIDTH;
		}
	} else {
		if (height > MAX_HEIGHT) {
			width *= MAX_HEIGHT / height;
			height = MAX_HEIGHT;
		}
	}
	canvas.width = width;
	canvas.height = height;
	var ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0, width, height);

	dataurl = canvas.toDataURL(fileType);
	return dataurl;
}

