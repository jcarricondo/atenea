<?php
class listadoMovimientosSimulador extends MySQL {
	
	// Variables de la clase
	var $nombre_albaran = "";
	var $tipo_albaran = "";
	var $id_centro_logistico = "";
	var $id_usuario = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $numero_serie = "";
	var $averiado = "";
	var $tipo_motivo = "";
	var $paginacion = "";
	var $id_almacen = "";
    var $id_sede = "";
	var $fecha_creado_inicio = "";
	var $fecha_creado_fin = "";
	
	var $movimientos = "";
	var $consultaSql = "";

	// Establecemos en la clase los valores del buscador
	function setValores($nombre_albaran,$tipo_albaran,$id_centro_logistico,$id_usuario,$numero_serie,$averiado,$tipo_motivo,$fecha_desde,$fecha_hasta,$paginacion,$id_almacen,$id_sede) {
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_centro_logistico = $id_centro_logistico;
		$this->id_usuario = $id_usuario;
		$this->numero_serie = $numero_serie;
		$this->averiado = $averiado;
		$this->tipo_motivo = $tipo_motivo;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->paginacion = $paginacion;
		$this->id_almacen = $id_almacen;
        $this->id_sede = $id_sede;

		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select * from albaranes_simuladores_log where activo=1";

		if($this->numero_serie != ""){
			$condiciones = " and numero_serie='".$this->numero_serie."'";
		}	
		if($this->averiado != ""){
			$condiciones .= " and averiado='".$this->averiado."'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and fecha_creado <= '".$this->fecha_hasta."' ";
		}
		if($this->nombre_albaran != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_simuladores where nombre_albaran='".$this->nombre_albaran."')";
		}
		if($this->tipo_albaran != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_simuladores where tipo_albaran='".$this->tipo_albaran."')";
		}
		if($this->id_centro_logistico != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_simuladores where id_centro_logistico=".$this->id_centro_logistico.")";
		}
		if($this->id_usuario != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_simuladores where id_usuario=".$this->id_usuario.")";
		}
		if($this->tipo_motivo != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_simuladores where motivo='".$this->tipo_motivo."')";
		}
		if($this->id_almacen != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_simuladores where id_almacen=".$this->id_almacen.")";
		}
        else {
            $condiciones .= " and id_albaran in (select id_albaran from albaranes_simuladores where id_almacen in
                                                    (select id_almacen from almacenes where activo=1 and id_sede=".$this->id_sede."))";
        }
		
		$ordenado = " order by albaranes_simuladores_log.id DESC ";
					
		$this->consultaSql = $campos.$condiciones.$ordenado.$this->paginacion;
	}	

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->movimientos = $this->getResultados();
	}
}
?>