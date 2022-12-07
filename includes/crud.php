<?php

	//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
	defined('ABSPATH') or die( "Bye bye" );

	function crearTablasCheckin() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$collate = $wpdb->collate;
		// Tabla checkin
		$nombre_tabla_checkin = $prefix.'checkin';
		$sql = "CREATE TABLE {$nombre_tabla_checkin} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			fecha date DEFAULT CURRENT_TIMESTAMP NOT NULL,
			hora time DEFAULT CURRENT_TIMESTAMP NOT NULL,
			precio varchar(6) NOT NULL,
			telefono varchar(14) NOT NULL,
			mail varchar(100) NOT NULL,
			id_pedido bigint(20) NOT NULL,
			documento_policia_firmado longtext,
			factura_cliente longtext,
			checkin_realizado boolean DEFAULT false  NOT NULL,
			PRIMARY KEY  (id)
			)
		COLLATE {$collate}";
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		// Tabla checkin usuario
		$nombre_tabla = $prefix.'checkin_usuario';
		$sql = "CREATE TABLE {$nombre_tabla} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			id_checkin bigint(20) NOT NULL,
			id_usuario_interno bigint(20) NOT NULL,
			nombre varchar(255) NOT NULL,
			apellidos varchar(255) NOT NULL,
			numero_documento varchar(255),
			foto_documento_1 longtext,
			foto_documento_2 longtext,
			PRIMARY KEY  (id),
			FOREIGN KEY (id_checkin) REFERENCES {$nombre_tabla_checkin}(id)
			)
		COLLATE {$collate}";
		dbDelta($sql);
		
	}
	
	function insertarCheckinReserva($precio, $telefono, $mail, $id_pedido) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin';
		$fila=array(
			'precio'=>$precio,
			'telefono'=>$telefono,
			'mail'=>$mail,
			'id_pedido'=>$id_pedido,
			'checkin_realizado'=>'1'
		);
		$resultado=$wpdb->insert($nombre_tabla,$fila); 
		
	}
	
	function modificarCheckinReserva($idReserva, $precio, $telefono, $mail) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin';
		$sql="UPDATE {$nombre_tabla} SET precio='{$precio}', telefono='{$telefono}', mail='{$mail}' WHERE id_pedido={$idReserva}";
		$wpdb->query($sql);
	}
	
	function buscarCheckinPorIdExterno($idPedido) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'checkin';
		$query="SELECT * FROM {$nombre_tabla} WHERE id_pedido = '{$idPedido}'";
		$resultados=$wpdb->get_results($query);
		
		$idInterno;
		foreach ($resultados as $resultado) {
			$idInterno=$resultado->id;
		}
		
		return $idInterno;
	}
	
	function buscardatosCheckinPorIdExterno($idPedido) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'checkin';
		$query="SELECT * FROM {$nombre_tabla} WHERE id_pedido = '{$idPedido}'";
		$resultados=$wpdb->get_results($query);
		
		$resultadoAux;
		foreach ($resultados as $resultado) {
			$resultadoAux=$resultado;
		}
		
		return $resultadoAux;
	}
	
	function buscardatosCheckinSinFactura() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'checkin';
		$query="SELECT * FROM {$nombre_tabla} WHERE factura_cliente IS NULL";
		$resultados=$wpdb->get_results($query);
		
		return $resultados;
	}
	
	function consultaDatosReservaFiltro($fecha_calcular_menos, $fecha_calcular_mais) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'postmeta';
		$query="SELECT * FROM {$nombre_tabla} WHERE (meta_key = 'mphb_check_in_date' AND meta_value > '{$fecha_calcular_menos}') AND (meta_key = 'mphb_check_in_date' AND meta_value < '{$fecha_calcular_mais}')";
		$resultados=$wpdb->get_results($query);
				
		return $resultados;		
	}
	

	function insertarCheckinUsuarioReserva($idInterno, $idUsuarioInterno, $nome, $apellido, $identificadorIdentidad, $foto1, $foto2) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin_usuario';
		$fila=array(
			'id_checkin'=>$idInterno,
			'id_usuario_interno'=>$idUsuarioInterno,
			'nombre'=>$nome,
			'apellidos'=>$apellido,
			'numero_documento'=>$identificadorIdentidad,
			'foto_documento_1'=>$foto1,
			'foto_documento_2'=>$foto2
		);
		$resultado=$wpdb->insert($nombre_tabla,$fila); 
		
	}
	
	function insertarCheckinUsuarioReservaSinFotos($idInterno, $idUsuarioInterno, $nome, $apellido, $identificadorIdentidad) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin_usuario';
		$fila=array(
			'id_checkin'=>$idInterno,
			'id_usuario_interno'=>$idUsuarioInterno,
			'nombre'=>$nome,
			'apellidos'=>$apellido,
			'numero_documento'=>$identificadorIdentidad
		);
		$resultado=$wpdb->insert($nombre_tabla,$fila); 
		
	}
	
	function insertarfotosUsuarioClienteCheckin($idReservaInterno, $idUsuarioInterno, $tipoFoto, $foto) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$nombre_tabla = $prefix.'checkin_usuario';
		$sql="UPDATE {$nombre_tabla} SET {$tipoFoto}='{$foto}' WHERE id_checkin={$idReservaInterno} AND id_usuario_interno={$idUsuarioInterno}";
		$wpdb->query($sql);
	}
	
	function insertarFacturaClienteCheckin($idReservaInterno, $fotoBase64) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$nombre_tabla = $prefix.'checkin';
		$sql="UPDATE {$nombre_tabla} SET factura_cliente='{$fotoBase64}' WHERE id={$idReservaInterno}";
		$wpdb->query($sql);
	}
	
	function insertarResguardoFirmadoPoliciaClienteCheckin($idReservaInterno, $fotoBase64) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$nombre_tabla = $prefix.'checkin';
		$sql="UPDATE {$nombre_tabla} SET documento_policia_firmado='{$fotoBase64}' WHERE id={$idReservaInterno}";
		$wpdb->query($sql);
	}
	
	function actualizarDatosUsuarioClienteCheckin($idUsuario, $nombre, $apellido, $identificadorIdentidad) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$nombre_tabla = $prefix.'checkin_usuario';
		$sql="UPDATE {$nombre_tabla} SET nombre='{$nombre}', apellidos='{$apellido}', numero_documento='{$identificadorIdentidad}' WHERE id={$idUsuario}";
		$wpdb->query($sql);
	}

	function eliminarCheckinUsuarioReserva($idInterno) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin_usuario';
		$sql="DELETE FROM {$nombre_tabla} WHERE id_checkin={$idInterno}";
		$wpdb->query($sql);
	}
	
	function buscarCheckinUsuarioReservaPorIdInternoYIdUsuario($idCheckinInterno, $idUsuarioInterno) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin_usuario';
		$query="SELECT * FROM {$nombre_tabla} WHERE id_checkin={$idCheckinInterno} AND id_usuario_interno={$idUsuarioInterno}";
		$resultados=$wpdb->get_results($query);
				
		$idUsuario;
		foreach ($resultados as $resultado) {
			$idUsuario=$resultado->id;
		}
		
		return $idUsuario;
	}
	
	function eliminarDatosUsuarioReservaPorIdInternoYIdUsuario($idCheckinInterno, $idUsuarioInterno) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin_usuario';
		$sql="DELETE FROM {$nombre_tabla} WHERE id_checkin={$idCheckinInterno} AND id_usuario_interno={$idUsuarioInterno}";
		$wpdb->query($sql);
	}

	function buscarDatosCheckinUsuariosReservasPorIdInterno($idInterno) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'checkin_usuario';
		$sql="SELECT * FROM {$nombre_tabla} WHERE id_checkin= {$idInterno}";
		return $resultados=$wpdb->get_results($sql);
	}

	function eliminarTablaCheckin() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$nombre_tabla = $prefix.'checkin_usuario';
		$sql="DROP TABLE IF EXISTS {$nombre_tabla}";
		$wpdb->query($sql);
	}
	
	function eliminarTablaCheckinUsuario() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		$nombre_tabla = $prefix.'checkin';
		$sql="DROP TABLE IF EXISTS {$nombre_tabla}";
		$wpdb->query($sql);
	}
	
	function buscarReservaPorTelefono($telefono) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'postmeta';
		$query="SELECT * FROM {$nombre_tabla} WHERE meta_key = 'mphb_phone' and meta_value = '{$telefono}'";
		$resultados=$wpdb->get_results($query);
		
		$post;
		foreach ($resultados as $resultado) {
			$post=$resultado->post_id;
		}
		
		return $post;
	}
	
	function buscarReservaPorEmail($email) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'postmeta';
		$query="SELECT * FROM {$nombre_tabla} WHERE meta_key = 'mphb_email' and meta_value = '{$email}'";
		$resultados=$wpdb->get_results($query);
		
		$post;
		foreach ($resultados as $resultado) {
			$post=$resultado->post_id;
		}
		
		return $post;
	}
	
	function buscarReservaPorNombre($nombre) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'postmeta';
		$query="SELECT * FROM {$nombre_tabla} WHERE meta_key = 'mphb_last_name' and meta_value = '{$nombre}'";
		$resultados=$wpdb->get_results($query);
		
		$post;
		foreach ($resultados as $resultado) {
			$post=$resultado->post_id;
		}
		
		return $post;
	}
	
	function buscarDatosReserva($postId, $campoBusqueda) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'postmeta';
		$query="SELECT * FROM {$nombre_tabla} WHERE post_id = '{$postId}' and meta_key = '{$campoBusqueda}'";
		$resultados=$wpdb->get_results($query);
		
		$post;
		foreach ($resultados as $resultado) {
			$post=$resultado->meta_value;
		}
		
		return $post;
	}
		
	function buscarDatosPago($idReserva) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'postmeta';
		$query="SELECT * FROM {$nombre_tabla} WHERE meta_key = '_mphb_booking_id' and meta_value = '{$idReserva}'";
		$resultados=$wpdb->get_results($query);
		
		$post;
		foreach ($resultados as $resultado) {
			$post=$resultado->post_id;
		}
		
		return $post;
	}
	
	function buscarListaDatosPago($idReserva) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'postmeta';
		$query="SELECT * FROM {$nombre_tabla} WHERE meta_key = '_mphb_booking_id' and meta_value = '{$idReserva}'";
		$resultados=$wpdb->get_results($query);
		
		return $resultados;
	}
	
	function buscarPostPorId($idPost) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla = $prefix.'posts';
		$query="SELECT * FROM {$nombre_tabla} WHERE ID = {$idPost}";
		$resultados=$wpdb->get_results($query);
		
		$post;
		foreach ($resultados as $resultado) {
			$post=$resultado;
		}
		
		return $post;
	}
		
	// Buscar ultimo post
	function buscarIdPostNew() {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'posts';
		$query="SELECT MAX(ID)+1 as id_new_post from {$nombre_tabla}";
		$resultados=$wpdb->get_results($query);
		
		$post;
		foreach ($resultados as $resultado) {
			$post=$resultado->id_new_post;
		}
		
		return $post;
	}
	
	// Insertar datos Post
	function insertarPost($postId, $fecha, $estado) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'posts';
		$sql="INSERT INTO {$nombre_tabla}
		(ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
		VALUES({$postId}, 1, '{$fecha}', '{$fecha}', '', 'Borrador automático', '', '{$estado}', 'closed', 'closed', '', 'borrador-automatico-6', '', '', '{$fecha}', '{$fecha}', '', 0, 'https://www.reservas.casamanola.gal/?post_type=mphb_payment&#038;p={$postId}', 0, 'mphb_payment', '', 0)";
		$wpdb->query($sql);
	}
	
	// Insertar datos Post
	function insertarPostDatos($postId, $fecha, $estado, $tipoPost, $postParent, $urlPost) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'posts';
		$sql="INSERT INTO {$nombre_tabla}
		(ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
		VALUES({$postId}, 1, '{$fecha}', '{$fecha}', '', '', '', '{$estado}', 'closed', 'closed', '', '{$postId}', '', '', '{$fecha}', '{$fecha}', '', {$postParent}, '{$urlPost}', 0, '{$tipoPost}', '', 0)";
		$wpdb->query($sql);
	}
	
	function modificarEstadoPost($postId, $estado, $dateModificacion) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'posts';
		$sql="UPDATE {$nombre_tabla} SET post_status = '{$estado}', post_modified = '{$dateModificacion}', post_modified_gmt = '{$dateModificacion}'  WHERE ID = {$postId}";
		$wpdb->query($sql);
	}
	
	function insertarDatosPostMeta($postId, $metaKey, $metaValue) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla=$prefix.'postmeta';
		$sql="INSERT INTO {$nombre_tabla} (post_id, meta_key, meta_value) VALUES({$postId}, '{$metaKey}', '{$metaValue}')";
		$wpdb->query($sql);
	}
	
	function consultaReservasPorEstado ($estadoPago) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla_posts=$prefix.'posts';
		$sql="SELECT ID FROM {$nombre_tabla_posts} WHERE post_status = '{$estadoPago}'";
		return $resultados=$wpdb->get_results($sql);
	}
	
	function consultarPostPorTipoYEstado ($tipo, $estado) {
		global $wpdb;
		$prefix=$wpdb->prefix;
		$nombre_tabla_posts=$prefix.'posts';
		$sql="SELECT * FROM {$nombre_tabla_posts} WHERE post_status = '{$estado}' AND post_type = '{$tipo}'";
		return $resultados=$wpdb->get_results($sql);
	}
	
?>