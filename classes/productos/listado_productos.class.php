<?php
class listadoProductos extends MySQL {
	
	// Variables de la clase
	var $num_serie = "";
	var $codigo_op = "";
	var $nombre_producto = "";
	// var $cabina = "";
	var $orden_produccion = "";
	var $estado = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $id_sede = "";
	var $paginacion = "";
	
	var $consultaSql = "";
	var $productos = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($num_serie,$codigo_op,$nombre_producto,$cabina,$orden_produccion,$estado,$fecha_desde,$fecha_hasta,$id_sede,$paginacion) {
		$this->num_serie = $num_serie;
		$this->codigo_op = $codigo_op;
		$this->nombre_producto = $nombre_producto;
		// $this->cabina = $cabina;
		$this->orden_produccion = $orden_produccion;
		$this->estado = $estado;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->id_sede = $id_sede;
		$this->paginacion = $paginacion;
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		if($this->id_sede != 0){
			$campos = "select distinct productos.id_producto from productos inner join orden_produccion_componentes on (productos.id_produccion=orden_produccion_componentes.id_produccion) inner join nombre_producto on (productos.id_nombre_producto=nombre_producto.id_nombre_producto) inner join orden_produccion on (orden_produccion.id_produccion=productos.id_produccion) where productos.id_producto is not null and productos.activo=1 
						and orden_produccion.id_sede=".$this->id_sede. " and orden_produccion.activo=1 ";
		}
		else {
			$campos = "select distinct productos.id_producto from productos inner join orden_produccion_componentes on (productos.id_produccion=orden_produccion_componentes.id_produccion) inner join nombre_producto on (productos.id_nombre_producto=nombre_producto.id_nombre_producto) inner join orden_produccion on (orden_produccion.id_produccion=productos.id_produccion) where productos.id_producto is not null and productos.activo=1 
						 and orden_produccion.activo=1 ";			
		}

		$condiciones = "";
		if($this->num_serie != "") {
			$condiciones .= " and productos.num_serie like '%".$this->num_serie."%'";
		}
		if($this->codigo_op != "") {
			$condiciones .= " and orden_produccion.codigo like '%".$this->codigo_op."%'";
		}
		if($this->nombre_producto != "") {
			$condiciones .= " and productos.id_nombre_producto=".$this->nombre_producto;
		}
		/*
		if($this->cabina != "") {
			$condiciones .= " and orden_produccion_componentes.id_componente=".$this->cabina;
		}
		*/
		if($this->orden_produccion != "") {
			$condiciones .= " and productos.id_produccion=".$this->orden_produccion;
		}
		if($this->estado != "") {
			$partesListado = explode(",",$this->estado);
			if(count($partesListado) <= 1) {
				$condiciones .= " and productos.estado like '%".$this->estado."%'";
			} else {
				for($i=0;$i<count($partesListado);$i++) {
					if($i==0) {
						$condiciones .= " and (";
					}
					if($i==count($partesListado)-1) {
						$condiciones .= "productos.estado like '%".$partesListado[$i]."%')";
					} else {
						$condiciones .= "productos.estado like '%".$partesListado[$i]."%' or ";
					}
				}
			}
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and productos.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and productos.fecha_creado <= '".$this->fecha_hasta."' ";	
		}

				
		$ordenado = " order by productos.fecha_creado DESC, productos.num_serie DESC";
		
		$this->consultaSql = $campos.$condiciones.$ordenado.$this->paginacion;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->productos = $this->getResultados();
	}
	
	function prepararNumSerie() {
		$campos = "select distinct productos.num_serie from productos where productos.id_producto is not null and productos.activo=1 ";
		$ordenado = "order by productos.num_serie";
					
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaNumSerie() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->num_serie = $this->getResultados();
	}
	
	function prepararCodigoOp() {
		$campos = "select distinct codigo from productos where productos.id_producto is not null and productos.activo=1 ";
		$ordenado = "order by productos.codigo";
					
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaCodigoOp() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->codigo_op = $this->getResultados();
	}
	
	
	function prepararOrdenProduccion() {
		$campos = "select distinct id_produccion from productos where productos.id_producto is not null and productos.activo=1 ";
		$ordenado = "order by productos.id_produccion";
					
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaOrdenProduccion() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->orden_produccion = $this->getResultados();
	}
	
	
}
?>