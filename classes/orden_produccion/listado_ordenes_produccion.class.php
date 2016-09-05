<?php
class listadoOrdenesProduccion extends MySQL {
	
	// Variables de la clase
	var $unidades = "";
	var $cabina = "";
	var $periferico = "";
	var $num_ordenadores = "";
	var $ordenador = "";
	// var $software = "";
	var $fecha_inicio = "";
	var $fecha_entrega = "";
	var $fecha_entrega_deseada = "";
	var $estado = "";
	var $ref_libres = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	
	var $consultaSql = "";
	var $ordenes_produccion = NULL;
	
	var $fechas_inicio = ""; 
	var $fechas_entrega = "";
	var $fechas_entrega_deseada = "";
	var $alias_op = "";
	var $id_tipo = "";
	var $id_sede = "";
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($unidades,$cabina,$periferico,$num_ordenadores,$ordenador,$software,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada,$estado,$ref_libres,$fecha_desde,$fecha_hasta,$alias_op,$id_tipo,$id_sede) {
		$this->unidades = $unidades;
		$this->cabina = $cabina;
		$this->periferico = $periferico;
		$this->num_ordenadores = $num_ordenadores;
		$this->ordenador = $ordenador;
		// $this->software = $software;
		$this->fecha_inicio = $fecha_inicio;
		$this->fecha_entrega = $fecha_entrega;
		$this->fecha_entrega_deseada = $fecha_entrega_deseada;
		$this->estado = $estado;
		$this->ref_libres = $ref_libres;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->alias_op = $alias_op;
		$this->id_tipo = $id_tipo;
		$this->id_sede = $id_sede;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
				
		$campos = "select distinct orden_produccion.id_produccion from orden_produccion where orden_produccion.id_produccion is not null and orden_produccion.activo=1 ";
		
		if($this->unidades != "") {
			$condiciones .= " and unidades=".$this->unidades;
		}
		if($this->fecha_inicio != "") {
			$condiciones .= " and orden_produccion.fecha_inicio like '%".$this->fecha_inicio."%'";
		}
		if($this->fecha_entrega != "") {
			$condiciones .= " and orden_produccion.fecha_entrega like '%".$this->fecha_entrega."%'";
		}
		if($this->fecha_entrega_deseada != "") {
			$condiciones .= " and orden_produccion.fecha_entrega_deseada like '%".$this->fecha_entrega_deseada."%'";
		}
		if($this->estado != "") {
			$partesListado = explode(",",$this->estado);
			if(count($partesListado) <= 1) {
				$condiciones .= " and orden_produccion.estado like '%".$this->estado."%'";
			} else {
				for($i=0;$i<count($partesListado);$i++) {
					if($i==0) {
						$condiciones .= " and (";
					}
					if($i==count($partesListado)-1) {
						$condiciones .= "orden_produccion.estado like '%".$partesListado[$i]."%')";
					} else {
						$condiciones .= "orden_produccion.estado like '%".$partesListado[$i]."%' or ";
					}
				}
			}
		}
		if($this->alias_op != ""){
			$condiciones .= " and orden_produccion.alias="."'".$this->alias_op."'"; 	
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and orden_produccion.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and orden_produccion.fecha_creado <= '".$this->fecha_hasta."' ";	
		}
		if($this->id_tipo != ""){
			$condiciones .= " and orden_produccion.id_tipo=".$this->id_tipo; 	
		}
		if($this->cabina != "") {
			$condiciones .= " and orden_produccion.id_produccion in (select opc.id_produccion from orden_produccion_componentes as opc where opc.id_componente=".$this->cabina.")";
		}
		if($this->periferico != "") {
			$condiciones .= " and orden_produccion.id_produccion in (select opc.id_produccion from orden_produccion_componentes as opc where opc.id_componente=".$this->periferico.")";
		}
		/*
		if($this->software != "") {
			$condiciones .= " and orden_produccion.id_produccion in (select opc.id_produccion from orden_produccion_componentes as opc where opc.id_componente=".$this->software.")";
		}
		*/
		if($this->ref_libres != "") {
			$condiciones .= " and orden_produccion.id_produccion in (select opr.id_produccion from orden_produccion_referencias as opr where opr.id_tipo_componente=0 and opr.id_referencia=".$this->ref_libres.")";
		}	
		if($this->id_sede != ""){
			if($this->id_sede != 0){
				// Si no es ADMIN GLOBAL
				$condiciones .= " and orden_produccion.id_sede=".$this->id_sede;
			}
		}
			
		$ordenado = " order by orden_produccion.fecha_creado DESC, orden_produccion.id_produccion DESC";
					
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->ordenes_produccion = $this->getResultados();
	}

	function validarFecha ($fecha) {
 		$fecha = explode("/",$fecha);
 		if(sizeof($fecha) != 3) return false;
 		if(checkdate($fecha[1],$fecha[0],$fecha[2])) return true;
 		else return false;
	}
	
	function cFechaMy ($fecha) {
 		$f = explode("/",$fecha);
 		return $f[2]."-".$f[1]."-".$f[0];
	}
	
	function cFechaNormal($fecha) {
 		$f = explode("-",$fecha);
 		if(count($f) > 1) {
  			return $f[2]."/".$f[1]."/".$f[0];
 		} else {
  			return $fecha;
		 }
	}
	
	function prepararFechaInicio() {
		$campos = "select distinct orden_produccion.fecha_inicio from orden_produccion where orden_produccion.id_produccion is not null and orden_produccion.activo=1 ";
		$ordenado = "order by orden_produccion.fecha_inicio";
		
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaFechasInicio() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->fechas_inicio = $this->getResultados();
	}
	
	function prepararFechaEntrega() {
		$campos = "select distinct orden_produccion.fecha_entrega from orden_produccion where orden_produccion.id_produccion is not null and orden_produccion.activo=1 ";
		$ordenado = "order by orden_produccion.fecha_entrega";
		
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaFechasEntrega() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->fechas_entrega = $this->getResultados();
	}
	
	function prepararFechaEntregaDeseada() {
		$campos = "select distinct orden_produccion.fecha_entrega_deseada from orden_produccion where orden_produccion.id_produccion is not null and orden_produccion.activo=1 ";
		$ordenado = "order by orden_produccion.fecha_entrega_deseada";
			
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaFechasEntregaDeseada() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->fechas_entrega_deseada = $this->getResultados();
	}
	
	function prepararReferenciasLibres() {
		$campos = "select distinct opr.id_referencia from orden_produccion_referencias as opr where opr.activo=1 and opr.id_tipo_componente=0 ";
		$this->consultaSql = $campos;
	}
	
	function realizarConsultaReferenciasLibres() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->ref_libres = $this->getResultados();
	}
	
	function prepararAlias($id_sede){
		if($id_sede != 0){
			$campos = sprintf("select orden_produccion.id_produccion from orden_produccion where orden_produccion.alias is not null and id_sede=%s and orden_produccion.activo=1 order by fecha_creado desc",
				$this->makeValue($id_sede, "int"));
		}
		else {
			$campos = "select orden_produccion.id_produccion from orden_produccion where orden_produccion.alias is not null and orden_produccion.activo=1 order by fecha_creado desc";
		}
		$this->consultaSql = $campos;
	}
	
	function realizarConsultaAlias() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->alias_op = $this->getResultados();
	}
}
?>