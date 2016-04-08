<?php
class Box extends MySQL {

	var $id_box;
	var $nombre;
	var $estado;

	var $nombre_boxes;
	
	function cargarDatos($id_box,$nombre,$estado) {
		$this->id_box = $id_box;
		$this->nombre = $nombre;
		$this->estado = $estado;
	}

	function cargaDatosBoxId($id_box) {
		$consultaSql = sprintf("select * from boxes where id_box=%s",
			$this->makeValue($id_box, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_box"],
			$resultados["nombre"],
			$resultados["estado"]);
	}


	// Funcion que devuelve los nombre de los boxes asignados al componente
	function dameNombreBoxesComponente($id_componente){
		$consultaSql = sprintf("select nombre from boxes inner join modulos_fabricacion_box on (boxes.id_box = modulos_fabricacion_box.id_box) where modulos_fabricacion_box.id_componente=%s",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$this->nombre_boxes = $this->getResultados();
	}

	// Funcion que devuelve el nombre del box
	function dameNombreBoxComponente($id_componente,$id_box){
		$consultaSql = sprintf("select nombre from boxes inner join modulos_fabricacion_box on (boxes.id_box = modulos_fabricacion_box.id_box) where modulos_fabricacion_box.id_componente=%s and modulos_fabricacion_box.id_box=%s",
			$this->makeValue($id_componente, "int"),
			$this->makeValue($id_box,"int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$this->nombre_boxes = $this->getResultados();
	}

	// Funcion que devuelve la informacion de los boxes de un componente
	function cargaDatosBoxPorComponente($id_componente,$id_sede){
		$consultaSql = sprintf("select * from modulos_fabricacion_box where modulos_fabricacion_box.id_componente=%s and id_sede=%s and modulos_fabricacion_box.activo=1",
			$this->makeValue($id_componente, "int"),
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve todos los boxes de la BBDD
	function cargaBoxes(){
		$consultaSql = "select id_box from modulos_fabricacion_box where activo=1 group by id_box order by id_box";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	function cargaDatosBoxPorIdBox($id_box,$id_sede){
		$consultaSql = sprintf("select * from modulos_fabricacion_box where modulos_fabricacion_box.id_box=%s and id_sede=%s and modulos_fabricacion_box.activo=1",
			$this->makeValue($id_box, "int"),
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Funcion que devuelve el nombre del box
	function dameNombreBox($id_box){
		$consultaSql = sprintf("select nombre from boxes where id_box=%s",
			$this->makeValue($id_box, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();		 
	}

	// Funcion que comprueba si existe el componente en alguno de los boxes activos
	function existeComponenteEnBoxes($id_componente,$id_sede){
		$consultaSql = sprintf("select id_componente from modulos_fabricacion_box where modulos_fabricacion_box.id_componente=%s and id_sede=%s and modulos_fabricacion_box.activo=1",
			$this->makeValue($id_componente, "int"),
			$this->makeValue($id_sede,"int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() != 0) {
   			return true;
   		}
   		else {
   			return false;
   		}
	}
}
?>