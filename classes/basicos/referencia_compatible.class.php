<?php
class Referencia_Compatible extends MySQL {

	var $id;
	var $id_grupo;
	var $id_referencia;
	var $activo;
	var $fecha_creado;

	var $id_referencia_principal;
	var $referencias_compatibles;

	function cargarDatos($id,$id_grupo,$id_referencia,$activo,$fecha_creado) {
		$this->id = $id;
		$this->id_grupo = $id_grupo;
		$this->id_referencia = $id_referencia;
		$this->activo = $activo;
		$this->fecha_creado = $fecha_creado;
	}

	function cargaDatosGrupoId($id_grupo) {
		$consultaSql = sprintf("select * from referencias_compatibles where referencias_compatibles.id_grupo=%s",
				$this->makeValue($id_grupo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
				$resultados["id"],
				$resultados["id_grupo"],
				$resultados["id_referencia"],
				$resultados["activo"],
				$resultados["fecha_creado"]
		);
	}

	// Función que devuelve las referencias compatibles a las que pertenece la referencia sin incluirse
	function dameReferenciasCompatiblesSinElla($id_referencia){
		$consultaSql = sprintf("select * from referencias_compatibles where activo=1 and id_grupo in
 									(select id_grupo from referencias_compatibles where activo=1 and id_referencia=%s)
								and id_referencia <> %s",
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
		// var_dump($this->id_referencia_principal); echo "<br/>";
		// var_dump($this->referencias_compatibles); echo "<br/>";
	}

	// Funcion que devuelve el último grupo creado
	function dameUltimoGrupo(){
		$consultaSql = "select max(id_grupo) as last_id_grupo from referencias_compatibles where activo=1";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res_ult_grupo = $this->getPrimerResultado();

		var_dump($res_ult_grupo); echo "<br/>";

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



	// Función que guarda las referencias compatibles
	function guardarReferenciasCompatibles(){
		// $res_grupo_ref_principal = $this->dameGrupoReferencia($this->id_referencia_principal);
		// $id_grupo_ref_principal = $res_grupo_ref_principal["id_grupo"];
		// $id_grupo_mas_antiguo = $id_grupo_ref_principal;
		// var_dump($this->id_referencia_principal); echo "<br/>";
		// var_dump($this->referencias_compatibles); echo "<br/>";

		// Guardamos en un array las referencias compatibles y la referencia principal
		$array_referencias = $this->referencias_compatibles;
		$array_referencias[] = $this->id_referencia_principal;
		// var_dump($array_referencias); echo "<br/>";

		$id_grupo_mas_antiguo = NULL;
		// De todas las referencias obtenemos el grupo más antiguo
		for($i=0;$i<count($array_referencias);$i++) {
			$res_grupo = $this->dameGrupoReferencia($array_referencias[$i]);
			$id_grupo = $res_grupo["id_grupo"];

			// var_dump($id_grupo); echo "<br/>";

			if($id_grupo != NULL) {
				if(is_null($id_grupo_mas_antiguo)) $id_grupo_mas_antiguo = $id_grupo;
				// Añadimos el grupo al array de grupos y eliminamos los grupos duplicados
				$array_id_grupo[] = $id_grupo;
				$array_id_grupo = array_unique($array_id_grupo);
				sort($array_id_grupo);
				if($id_grupo < $id_grupo_mas_antiguo) $id_grupo_mas_antiguo = $id_grupo;
			}
		}

		// var_dump($id_grupo_mas_antiguo); echo "<br/>";

		// Si las referencias no pertenecen a ningún grupo
		if($id_grupo_mas_antiguo == NULL){
			sort($array_referencias);
			// Creamos el grupo y  guardamos las referencias
			$error_crear_grupo = $this->creaGrupo($array_referencias);
			if($error_crear_grupo) echo '<script>alert("Se produjo un error durante el proceso de guardado de la referencia en el grupo. Consulte con el administrador")</script>';
		}
		else {
			// Obtenemos las referencias del grupo mas antiguo

			// Ordenamos las referencias del grupo mas antiguo

			// Guardamos en un array las referencias compatibles, la principal y las libres sin grupo en un array

			// Eliminamos del array las referencias que ya esten en el array del grupo mas antiguo

			// Eliminamos las referencias duplicadas

			// Guardamos las referencias de los otros grupos en el grupo antiguo en la BBDD

			// Recorremos los grupos y desactivamos todas las referencias menos el del array mas antiguo



		}
	}








	// Función que desactiva una referencia de un grupo
	function quitaReferenciaGrupo($id_referencia){
		$updateSql = sprintf("update referencias_compatibles set activo=0 where activo=1 and id_referencia=%s",
							$this->makeValue($id_referencia, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else return 2;
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