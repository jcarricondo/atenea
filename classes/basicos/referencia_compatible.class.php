<?php
class Referencia_Compatible extends MySQL {

	var $id;
	var $id_grupo;
	var $id_referencia;
	var $activo;
	var $fecha_creado;

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
		$this->setConsulta($consultaSql); var_dump($consultaSql);
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




	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return '<br/>';
			break;

		}
	}
}
?>