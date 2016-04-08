<?php
class listadoRecuperacionEscandallos extends MySQL {
	
	// Variables de la clase
	var $id_sede = "";
    var $id_usuario = "";
    var $codigo = "";
    var $id_produccion = "";
    var $alias_op = "";
    var $num_tecnicos = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
    var $paginacion = "";
		
	var $consultaSql = "";
	var $escandallos = NULL;
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($id_sede,$id_usuario,$codigo,$id_produccion,$alias_op,$num_tecnicos,$fecha_desde,$fecha_hasta,$paginacion) {
		$this->id_sede = $id_sede;
        $this->id_usuario = $id_usuario;
        $this->codigo = $codigo;
        $this->id_produccion = $id_produccion;
        $this->alias_op = $alias_op;
        $this->num_tecnicos = $num_tecnicos;
        $this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
        $this->paginacion = $paginacion;

		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select * from escandallo_log where id is not null";
        $condiciones = "";

        if($this->id_sede != ""){
            if($this->id_sede != 0) {
                $condiciones .= " and id_produccion in (select id_produccion from orden_produccion where activo=1 and id_sede=" . $this->id_sede . ") ";
            }
		}
        if($this->id_usuario != ""){
            $condiciones .= " and id_usuario=".$this->id_usuario;
        }
        if($this->codigo != ""){
            $condiciones .= " and codigo='".$this->codigo."'";
        }
        if($this->id_produccion != ""){
            $condiciones .= " and id_produccion=".$this->id_produccion;
        }
        if($this->alias_op != ""){
            $condiciones .= " and id_produccion in (select id_produccion from orden_produccion where activo=1 and alias='".$this->alias_op."')";
        }
        if($this->num_tecnicos != ""){
            $condiciones .= " and numero_tecnicos=".$this->num_tecnicos;
        }
		if($this->fecha_desde != "") {
			$condiciones .= " and fecha_creacion >= '".$this->fecha_desde."'";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and fecha_creacion <= '".$this->fecha_hasta."'";
		}

		$agrupado = " group by codigo";
        $ordenado = " order by fecha_creacion DESC ";

		$this->consultaSql = $campos.$condiciones.$agrupado.$ordenado.$this->paginacion;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->escandallos = $this->getResultados();
	}
	
}
?>