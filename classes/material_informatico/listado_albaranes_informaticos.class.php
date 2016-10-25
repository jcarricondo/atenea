<?php
class listadoAlbaranesInformaticos extends MySQL {
	
	// Variables de la clase
	var $nombre_albaran = "";
	var $tipo_albaran = "";
	var $origen_destino = "";
	var $id_usuario = "";
	var $fecha_creado = "";
	var $num_serie = "";
	var $motivo = "";
	var $albaranes = "";
	var $consultaSql = "";

	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($nombre_albaran,$tipo_albaran,$origen_destino,$id_usuario,$num_serie,$motivo,$fecha_creado) {
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->origen_destino = $origen_destino;
		$this->id_usuario = $id_usuario;
		$this->num_serie = $num_serie;
		$this->motivo = $motivo;
		$this->fecha_creado = $fecha_creado;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select distinct id_albaran from albaranes_informaticos where id_albaran is not null and activo=1 ";

		if($this->nombre_albaran != "") {
			$condiciones .= " and nombre_albaran like '%".$this->nombre_albaran."%'";
		}
		if($this->id_usuario != "") {
			$condiciones .= " and id_usuario=".$this->id_usuario;
		}
		if($this->tipo_albaran != "") {
			$condiciones .= " and tipo_albaran='".$this->tipo_albaran."'";
		}
		if($this->origen_destino != ""){
			$condiciones .= " and origen_destino=".$this->origen;
		}	
		if($this->motivo != ""){
			$condiciones .= " and motivo='".$this->motivo."'";
		}
		if($this->fecha_creado != "") {
			$condiciones .= " and albaranes_informaticos.fecha_creado='".$this->fecha_creado."'";
		}
		if($this->num_serie != ""){
			$condiciones .= " and albaranes_informaticos.id_albaran in 
								(select id_albaran from albaranes_informaticos_movimientos where id_material in 
									(select id_material from material_informatico where num_serie='".$this->num_serie."'))";
		}

		$ordenado = " order by albaranes_informaticos.fecha_creado DESC, albaranes_informaticos.id_albaran DESC";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}	

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->albaranes = $this->getResultados();
	}
}
?>