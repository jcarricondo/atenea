<?php
class listadoAlbaranesPeriferico extends MySQL {
	
	// Variables de la clase
	var $nombre_albaran = "";
	var $tipo_albaran = "";
	var $id_centro_logistico = "";
	var $id_usuario = "";
	var $fecha_creado = "";
	var $num_serie = "";
	var $motivo = "";
	var $id_almacen = "";
    var $id_sede = "";
	var $fecha_creado_inicio = "";
	var $fecha_creado_fin = "";
	var $paginacion = "";

	var $albaranes = "";
	var $consultaSql = "";

	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($nombre_albaran,$tipo_albaran,$id_centro_logistico,$id_usuario,$fecha_creado,$num_serie,$motivo,$id_almacen,$id_sede,$fecha_creado_inicio,$fecha_creado_fin,$paginacion) {
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_centro_logistico = $id_centro_logistico;
		$this->id_usuario = $id_usuario;
		$this->fecha_creado = $fecha_creado;
		$this->num_serie = $num_serie;
		$this->motivo = $motivo;
		$this->id_almacen = $id_almacen;
        $this->id_sede = $id_sede;
		$this->fecha_creado_inicio = $fecha_creado_inicio;
		$this->fecha_creado_fin = $fecha_creado_fin;
		$this->paginacion = $paginacion;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select distinct albaranes_perifericos.id_albaran from albaranes_perifericos where albaranes_perifericos.id_albaran is not null and albaranes_perifericos.activo=1 ";
        $condiciones = "";

		if($this->nombre_albaran != "") {
			$condiciones .= " and albaranes_perifericos.nombre_albaran like '%".$this->nombre_albaran."%'";
		}
		if($this->id_usuario != "") {
			$condiciones .= " and albaranes_perifericos.id_usuario=".$this->id_usuario;
		}
		if($this->fecha_creado != "") {
			// Preparamos la fecha de Brasil a la hora de España 
			// Hay que añadir un rango de busqueda dado que en el buscador no tenemos en cuenta la hora
			$condiciones .= " and albaranes_perifericos.fecha_creado >='".$this->fecha_creado_inicio."' and fecha_creado <= '".$this->fecha_creado_fin."'";
		}
		if($this->tipo_albaran != "") {
			$condiciones .= " and albaranes_perifericos.tipo_albaran='".$this->tipo_albaran."'";
		}
		if($this->id_centro_logistico != ""){
			$condiciones .= " and albaranes_perifericos.id_centro_logistico=".$this->id_centro_logistico;
		}	
		if($this->motivo != ""){
			$condiciones .= " and albaranes_perifericos.motivo='".$this->motivo."'";
		}
		if($this->id_almacen != ""){
			$condiciones .= " and albaranes_perifericos.id_almacen=".$this->id_almacen;
		}
        else {
            $condiciones .= " and albaranes_perifericos.id_almacen in (select id_almacen from almacenes where id_sede=".$this->id_sede.")";
        }
		if($this->num_serie != ""){
			$condiciones .= " and albaranes_perifericos.id_albaran in 
								(select id_albaran from albaranes_perifericos_movimientos where id_periferico in 
									(select id_periferico from perifericos where numero_serie='".$this->num_serie."'))";
		}

		$ordenado = " order by albaranes_perifericos.fecha_creado DESC, albaranes_perifericos.id_albaran DESC";
					
		$this->consultaSql = $campos.$condiciones.$ordenado.$this->paginacion;
	}	

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->albaranes = $this->getResultados();
	}
}
?>