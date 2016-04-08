<?php
class listadoMovimientosInformaticos extends MySQL {
	
	// Variables de la clase
	var $nombre_albaran = "";
	var $tipo_albaran = "";
	var $origen_destino = "";
	var $id_usuario = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $num_serie = "";
	var $averiado = "";
	var $tipo_material = "";
	var $tipo_motivo = "";
	var $movimientos = "";
	var $consultaSql = "";

	// Establecemos en la clase los valores del buscador
	function setValores($nombre_albaran,$tipo_albaran,$origen_destino,$id_usuario,$num_serie,$averiado,$tipo_material,$tipo_motivo,$fecha_desde,$fecha_hasta){
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->origen_destino = $origen_destino;
		$this->id_usuario = $id_usuario;
		$this->num_serie = $num_serie;
		$this->averiado = $averiado;
		$this->tipo_material = $tipo_material;
		$this->tipo_motivo = $tipo_motivo;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select * from albaranes_informaticos_log where activo=1";

		if($this->num_serie != ""){
			$condiciones .= " and num_serie='".$this->num_serie."'";
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
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_informaticos where nombre_albaran='".$this->nombre_albaran."')";
		}
		if($this->tipo_albaran != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_informaticos where tipo_albaran='".$this->tipo_albaran."')";
		}
		if($this->id_centro_logistico != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_informaticos where origen_destino=".$this->origen_destino.")";
		}
		if($this->id_usuario != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_informaticos where id_usuario=".$this->id_usuario.")";
		}
		if($this->tipo_motivo != ""){
			$condiciones .= " and id_albaran in (select id_albaran from albaranes_informaticos where motivo='".$this->tipo_motivo."')";
		}
		if($this->tipo_material != ""){
			$condiciones .= " and id_material in (select id_material from material_informatico where id_tipo in 
													(select id_tipo from material_informatico_tipo where nombre=".$this->tipo_material."))";
		}

		$ordenado = " order by albaranes_informaticos_log.id DESC ";
					
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}	

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->movimientos = $this->getResultados();
	}
}
?>