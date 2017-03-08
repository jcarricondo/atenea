<?php
class Referencia_Compatible extends MySQL {

	var $id;
	var $id_grupo;
	var $id_referencia;
	var $activo;
	var $fecha_creado;

	var $id_referencia_principal;
	var $referencias_compatibles;


	// Función que devuelve todos los datos de un grupo según su id
	function dameDatosGrupoId($id_grupo){
		$consultaSql = sprintf("select * from referencias_compatibles where activo=1 and id_grupo=%s",
							$this->makeValue($id_grupo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $res_grupo = $this->getResultados();
	}

	// Función que devuelve las referencias compatibles a las que pertenece la referencia sin incluirse
	function dameReferenciasCompatiblesSinElla($id_referencia){
		$consultaSql = sprintf("select * from referencias_compatibles where activo=1 and id_grupo in
 									(select id_grupo from referencias_compatibles where activo=1 and id_referencia=%s)
								and id_referencia <> %s order by id_referencia",
				$this->makeValue($id_referencia, "int"),
				$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que devuelve el grupo de una referencia compatible
	function dameGrupoReferencia($id_referencia){
		$consultaSql = sprintf("select id_grupo from referencias_compatibles where activo=1 and id_referencia=%s",
							$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res_grupo = $this->getPrimerResultado();
		return $res_grupo;
	}

	// Función que devuelve la fecha del grupo
	function dameFechaGrupo($id_grupo){
		$consultaSql = sprintf("select fecha_creado from referencias_compatibles where activo=1 and id_grupo=%s",
							$this->makeValue($id_grupo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res_fecha_grupo = $this->getPrimerResultado();
		return $res_fecha_grupo;
	}

	// Metodo para establecer la referencia principal y el array de referencias compatibles dentro de la clase
	function setReferenciasCompatibles($id_referencia_principal,$referencias_compatibles){
		$this->id_referencia_principal = $id_referencia_principal;
		$this->referencias_compatibles = $referencias_compatibles;
	}

	// Funcion que devuelve el último grupo creado
	function dameUltimoGrupo(){
		$consultaSql = "select max(id_grupo) as last_id_grupo from referencias_compatibles where activo=1";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res_ult_grupo = $this->getPrimerResultado();
		return $res_ult_grupo;
	}

	// Función que crea un grupo y guarda las referencias compatibles
	function creaGrupo($array_referencias){
		$fecha_hoy =  date('Y-m-d H:i:s');
		$res_id_grupo = $this->dameUltimoGrupo();
		$id_grupo = (int)$res_id_grupo["last_id_grupo"];
		$id_grupo++;
		// Guardamos las referencias con el nuevo grupo y la fecha
		for($i=0;$i<count($array_referencias);$i++){
			$id_referencia = $array_referencias[$i];
			$insertSql = sprintf("insert into referencias_compatibles (id_grupo,id_referencia,activo,fecha_creado) values (%s,%s,1,%s)",
							$this->makeValue($id_grupo, "int"),
							$this->makeValue($id_referencia, "int"),
							$this->makeValue($fecha_hoy, "date"));
			$this->setConsulta($insertSql);
			if(!$this->ejecutarSoloConsulta()) {
				echo '<script>alert("Se ha producido un error al guardar la referencia en el grupo")</script>';
				$error = true;
			}
		}
		return $error;
	}

	// Función que guarda nuevas referencias en un grupo
	function actualizaGrupo($array_referencias,$id_grupo){
		// Obtenemos la fecha del grupo existente
		$res_fecha_grupo = $this->dameFechaGrupo($id_grupo);
		$fecha_grupo = $res_fecha_grupo["fecha_creado"];
		// Guardamos las nuevas referencias compatibles en el grupo
		for($i=0;$i<count($array_referencias);$i++) {
			$id_referencia = $array_referencias[$i];
			$insertSql = sprintf("insert into referencias_compatibles (id_grupo,id_referencia,activo,fecha_creado) values (%s,%s,1,%s)",
					$this->makeValue($id_grupo, "int"),
					$this->makeValue($id_referencia, "int"),
					$this->makeValue($fecha_grupo, "date"));
			$this->setConsulta($insertSql);
			if(!$this->ejecutarSoloConsulta()) {
				echo '<script>alert("Se ha producido un error al guardar la referencia en el grupo existente")</script>';
				$error = true;
			}
		}
		return $error;
	}

	// Función que desactiva un grupo
	function desactivaGrupo($id_grupo){
		$updateSql = sprintf("update referencias_compatibles set activo=0 where activo=1 and id_grupo=%s",
						$this->makeValue($id_grupo,"int"));
		$this->setConsulta($updateSql);
		if(!$this->ejecutarSoloConsulta()) {
			echo '<script>alert("Se ha producido un error al desactivar el grupo")</script>';
			return true;
		}
		return false;
	}


	// Función que guarda las referencias compatibles
	function guardarReferenciasCompatibles(){
		// Guardamos en un array las referencias compatibles y la referencia principal
		$array_referencias = $this->referencias_compatibles;
		$array_referencias[] = $this->id_referencia_principal;

		$id_grupo_mas_antiguo = NULL;
		// De todas las referencias obtenemos el grupo más antiguo
		for($i=0;$i<count($array_referencias);$i++) {
			$res_grupo = $this->dameGrupoReferencia($array_referencias[$i]);
			$id_grupo = $res_grupo["id_grupo"];

			if($id_grupo != NULL) {
				if(is_null($id_grupo_mas_antiguo)) $id_grupo_mas_antiguo = $id_grupo;
				// Añadimos el grupo al array de grupos y eliminamos los grupos duplicados
				$array_id_grupo[] = $id_grupo;
				$array_id_grupo = array_unique($array_id_grupo);
				sort($array_id_grupo);
				if($id_grupo < $id_grupo_mas_antiguo) $id_grupo_mas_antiguo = $id_grupo;
			}
		}

		// Si las referencias no pertenecen a ningún grupo
		if($id_grupo_mas_antiguo == NULL){
			$array_referencias = array_unique($array_referencias);
			sort($array_referencias);
			// Creamos el grupo y  guardamos las referencias
			$error_crear_grupo = $this->creaGrupo($array_referencias);
			if($error_crear_grupo) echo '<script>alert("Se produjo un error durante el proceso de guardado de la referencia en el grupo. Consulte con el administrador")</script>';
		}
		else {
			// Obtenemos las referencias del grupo mas antiguo
			$res_grupo_antiguo = $this->dameDatosGrupoId($id_grupo_mas_antiguo);
			// Guardamos en un array las referencias del grupo mas antiguo
			for($i=0;$i<count($res_grupo_antiguo);$i++) $array_refs_gr_antiguo[] = $res_grupo_antiguo[$i]["id_referencia"];
			sort($array_refs_gr_antiguo);

			// Obtenemos las referencias de todos los grupos menos el del grupo mas antiguo
			for($i=0;$i<count($array_id_grupo);$i++){
				$id_grupo = $array_id_grupo[$i];
				if($id_grupo != $id_grupo_mas_antiguo){
					// Añadimos al array las referencias de los grupos que vamos a desactivar
					$res_grupo_eliminar = $this->dameDatosGrupoId($id_grupo);
					for($j=0;$j<count($res_grupo_eliminar);$j++) $array_refs_grupo_eliminar[] = $res_grupo_eliminar[$j]["id_referencia"];
				}
			}

			// Añadimos al array de referencias compatibles las referencias de los grupos a desactivar
			if($array_refs_grupo_eliminar != NULL) $array_referencias = array_merge($array_referencias,$array_refs_grupo_eliminar);
			$array_referencias = array_unique($array_referencias);
			sort($array_referencias);

			// Eliminamos del array las referencias que ya esten en el array del grupo mas antiguo
			$array_referencias = array_diff($array_referencias,$array_refs_gr_antiguo);
			sort($array_referencias);

			// Guardamos las referencias de los otros grupos en el grupo antiguo en la BBDD
			$error_actualizar_grupo = $this->actualizaGrupo($array_referencias,$id_grupo_mas_antiguo);
			if($error_actualizar_grupo) echo '<script>alert("Se produjo un error durante el proceso de guardado de la referencia en la actualización del grupo. Consulte con el administrador")</script>';

			// Desactivamos los grupos menos el grupo más antiguo
			for($i=0;$i<count($array_id_grupo);$i++){
				$id_grupo = $array_id_grupo[$i];
				if($id_grupo != $id_grupo_mas_antiguo){
					$error_desactivar_grupo = $this->desactivaGrupo($id_grupo);
					if($error_desactivar_grupo) echo '<script>alert("Se produjo un error durante el proceso de desactivación de un grupo. Consulte con el administrador")</script>';
				}
			}
		}
		$error_general = ($error_crear_grupo || $error_actualizar_grupo || $error_desactivar_grupo);
		return $error_general;
	}


	// Función que desactiva una referencia de un grupo
	function quitaReferenciaGrupo($id_referencia){
		$updateSql = sprintf("update referencias_compatibles set activo=0 where activo=1 and id_referencia=%s",
							$this->makeValue($id_referencia, "int"));
		$this->setConsulta($updateSql);
		if(!$this->ejecutarSoloConsulta()){
			return true;
		}
		else return false;
	}

	// Función que devuelve los tipos de motivos de compatibilidad de una referencia
	function dameTipoMotivosReferencia(){
		$consultaSql = "select * from tipos_motivos_referencias where activo=1";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res_motivos = $this->getResultados();
		return $res_motivos;
	}

	// Función que devuelve el nombre de la imagen del motivo de compatibilidad
	function dameNombreImagenMotivoCompatibilidad($id_motivo_compatibilidad){
		switch ($id_motivo_compatibilidad){
			case "1":
				$nombre_imagen = "global.jpg";
			break;
			case "2":
				$nombre_imagen = "mexico.jpg";
			break;
			case "3";
				$nombre_imagen = "brasil.jpg";
			break;
			case "4":
				$nombre_imagen = "francia.jpg";
			break;
			case "5":
				$nombre_imagen = "chile.jpg";
			break;
			default:
				$nombre_imagen = "global.jpg";
			break;
		}
		return $nombre_imagen;
	}

	// Función que devuelve el pais de la imagen del motivo de compatibilidad
	function damePaisImagenMotivoCompatibilidad($id_motivo_compatibilidad){
		switch ($id_motivo_compatibilidad){
			case "1":
				$nombre_pais = "GLOBAL";
				break;
			case "2":
				$nombre_pais = "MEXICO";
				break;
			case "3";
				$nombre_pais = "BRASIL";
				break;
			case "4":
				$nombre_pais = "FRANCIA";
				break;
			case "5":
				$nombre_pais = "CHILE";
				break;
			default:
				$nombre_pais = "GLOBAL";
				break;
		}
		return $nombre_pais;
	}




	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al eliminar una referencia compatible de un grupo<br/>';
			break;

		}
	}
}
?>