<?php
class listadoMaterialInformatico extends MySQL {

	// Variables de la clase
	var $id_tipo;
    var $id_subtipo;
	var $num_serie;
	var $descripcion;
	var $precio;
	var $asignado_a;
	var $estado;
	var $observaciones; 
	var $fecha_desde;
	var $fecha_hasta;
    var $paginacion;

	var $consultaSql = "";
	var $materiales_informaticos = NULL;

	// Se Pasan los valores de las variables del buscador a las de la clase
	function setValores($id_tipo,$id_subtipo,$num_serie,$descripcion,$precio,$asignado_a,$estado,$observaciones,$fecha_desde,$fecha_hasta,$paginacion) {
		$this->id_tipo = $id_tipo;
        $this->id_subtipo = $id_subtipo;
		$this->num_serie = $num_serie;
		$this->descripcion = $descripcion;
		$this->precio = $precio;
		$this->asignado_a = $asignado_a;
		$this->estado = $estado;
		$this->observaciones = $observaciones;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
        $this->paginacion = $paginacion;

		$this->prepararConsulta();
	}

	// Prepara la consulta a la base de datos
	function prepararConsulta() {
		$consulta = "select id_material from material_informatico as mi where mi.id_material is not null and mi.activo=1";

		if($this->id_tipo != ""){
			$consulta .= " and id_tipo=".$this->id_tipo;
		}
        if($this->id_subtipo != ""){
            $consulta .= " and id_subtipo=".$this->id_subtipo;
        }
		if($this->num_serie != ""){
			$consulta .= " and num_serie='".$this->num_serie."'";
		}
		if($this->descripcion != ""){
			$consulta .= " and descripcion like '%".$this->descripcion."%'";
		}
		if($this->precio != ""){
			$consulta .= " and precio=".$this->precio;
		}
		if($this->asignado_a != ""){
			$consulta .= " and asignado_a like '%".$this->asignado_a."%'";
		}
		if($this->estado != ""){
			$consulta .= " and estado='".$this->estado."'";
		}
		if($this->observaciones != ""){
			$consulta .= " and observaciones like '%".$this->observaciones."%'";
		}
		if($this->fecha_desde != ""){
			$consulta .= " and fecha_creado >= '".$this->fecha_desde."'";
		}
		if($this->fecha_hasta != ""){
			$consulta .= " and fecha_creado <= '".$this->fecha_hasta."'";
		}
		
		$ordenado = " order by mi.fecha_creado DESC, mi.id_tipo, mi.id_subtipo, mi.num_serie, precio DESC";		

		$this->consultaSql = $consulta.$ordenado.$this->paginacion;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->materiales_informaticos = $this->getResultados();
	}
}
?>