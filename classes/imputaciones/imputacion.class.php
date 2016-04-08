<?php
class Imputacion extends MySQL {
	
	var $id = "";
	var $id_usuario = "";
	var $orden_produccion = "";
	var $codigo = "";
	var $tipo_trabajo = "";
	var $horas = "";
	var $fecha = "";
	var $descripcion = "";
	var $fecha_grabado = "";
	var $fecha_eliminacion = "";
	
	var $consultaSql = "";
	var $imputacion = NULL;
	
	
	function cargarDatosImputacionId($id) {
		$consultaSql = sprintf("select i.*,o.codigo,DATE_ADD(i.fecha_grabado, INTERVAL 1 DAY) as fecha_eliminacion from imputaciones as i inner join orden_produccion as o on (o.id_produccion=i.id_orden_produccion) where i.id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id"],
			$resultados["id_usuario"],
			$resultados["codigo"],
			$resultados["tipo_trabajo"],
			$resultados["horas"],
			$resultados["fecha"],
			$resultados["descripcion"],
			$resultados["fecha_grabado"],
			$resultados["fecha_eliminacion"]
		);
	}
	
	function cargarDatos($id,$id_usuario,$codigo,$tipo_trabajo,$horas,$fecha,$descripcion,$fecha_grabado,$fecha_eliminacion) {
		$this->id = $id;
		$this->id_usuario = $id_usuario;
		$this->codigo = $codigo;
		$this->tipo_trabajo = $tipo_trabajo;
		$this->horas = $horas;
		$this->fecha = $fecha;
		$this->descripcion = $descripcion;
		$this->fecha_grabado = $fecha_grabado;
		$this->fecha_eliminacion = $fecha_eliminacion;
	}	
	
	// Se hace la carga de datos  de una nueva cabina
	function datosNuevaImputacion($id = NULL,$fecha,$tipo_trabajo,$horas,$orden_produccion,$descripcion,$id_usuario) {
		$this->id = $id;
		$this->fecha = $fecha;
		$this->tipo_trabajo = $tipo_trabajo;
		$this->orden_produccion = $orden_produccion;
		$this->descripcion = $descripcion;
		$this->id_usuario = $id_usuario;
		$this->horas = $horas;
	}
	
	// Guarda los cambios realizados en la cabina
	function guardarCambios() {
		// Si el id_componente es NULL lo toma como una nueva cabina
		if($this->id == NULL) {
			// Comprueba si hay otra cabina con el mismo nombre
			//if(!$this->comprobarCabinaDuplicada()) {
				$consulta = sprintf("insert into imputaciones (id_usuario,id_orden_produccion,tipo_trabajo,horas,fecha,descripcion,fecha_grabado) value (%s,%s,%s,%s,%s,%s,current_timestamp)",
					$this->makeValue($this->id_usuario, "int"),
					$this->makeValue($this->orden_produccion, "int"),
					$this->makeValue($this->tipo_trabajo, "int"),
					$this->makeValue($this->horas, "double"),
					$this->makeValue($this->fecha, "text"),
					$this->makeValue($this->descripcion, "text"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id = $this->getUltimoID();
					return 1;
				}
			}
	}
	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe una cabina con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar la nueva cabina<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos de la cabina<br/>';
			break;
			case 5;
				return 'Se produjo un error al eliminar la cabina<br/>';
			break;
			case 5;
				return 'Las horas imputadas han sido eliminadas<br/>';
			break;
			case 8;
				return 'Se produjo un error al eliminar las referencias de la cabina<br/>';
			break;
			case 9;
				return 'Se produjo un error al insertar los archivos de mecanica de la cabina<br/>';
			break;
			case 10:
				return 'Se produjo un error al desactivar un archivo adjunto de la cabina<br/>';
			break;
		}
	}
	
	function eliminar(){
		//ELIMINAR
		$consulta = sprintf("delete from imputaciones where id=%s",	
			$this->makeValue($this->id, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}
	}

	
	function cFechaMy ($fecha) {
 		$f = explode("/",$fecha);
 		return $f[2]."-".$f[1]."-".$f[0];
	}
}
?>