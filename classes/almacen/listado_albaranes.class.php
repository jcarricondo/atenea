<?php
class listadoAlbaranes extends MySQL {
	
	// Variables de la clase
	var $nombre_albaran = "";
	var $tipo_albaran = "";
	var $id_tipo_participante = "";
	var $id_participante = "";
	var $tipo_motivo = "";
	var $id_usuario = "";
	var $fecha_creado = "";
	var $id_ref = "";
	var $id_almacen = "";
    var $id_sede = "";
    var $fecha_creado_inicio = "";
    var $fecha_creado_fin = "";
	var $paginacion = "";

	var $albaranes = "";
	var $consultaSql = "";

	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($nombre_albaran,$tipo_albaran,$id_tipo_participante,$id_participante,$tipo_motivo,$id_usuario,$fecha_creado,$id_ref,$id_almacen,$id_sede,$fecha_creado_inicio,$fecha_creado_fin,$paginacion) {
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_tipo_participante = $id_tipo_participante;
		$this->id_participante = $id_participante;
		$this->tipo_motivo = $tipo_motivo;
		$this->id_usuario = $id_usuario;
		$this->fecha_creado = $fecha_creado;
		$this->id_ref = $id_ref;
		$this->id_almacen = $id_almacen;
        $this->id_sede = $id_sede;
        $this->fecha_creado_inicio = $fecha_creado_inicio;
        $this->fecha_creado_fin = $fecha_creado_fin;
		$this->paginacion = $paginacion;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select distinct almacenes_albaranes.id_albaran from almacenes_albaranes where almacenes_albaranes.id_albaran is not null and almacenes_albaranes.activo=1 ";
        $condiciones = "";

		if($this->nombre_albaran != "") {
			$condiciones .= " and almacenes_albaranes.nombre_albaran like '%".$this->nombre_albaran."%'";
		}
		if($this->id_usuario != "") {
			$condiciones .= " and almacenes_albaranes.id_usuario=".$this->id_usuario;
		}
        if($this->fecha_creado != "") {
            $condiciones .= " and almacenes_albaranes.fecha_creado >='".$this->fecha_creado_inicio."' and fecha_creado <= '".$this->fecha_creado_fin."'";
        }
		if($this->tipo_albaran != "") {
			$condiciones .= " and almacenes_albaranes.tipo_albaran='".$this->tipo_albaran."'";
		}
		if($this->id_tipo_participante != ""){
			if($this->id_tipo_participante != 3){
				$condiciones .= " and almacenes_albaranes.id_tipo_participante=".$this->id_tipo_participante." and id_participante=".$this->id_participante;
			}
		}	
		if($this->tipo_motivo != ""){
			$condiciones .= " and motivo='".$this->tipo_motivo."'";
		}
		if($this->id_almacen != ""){
			$condiciones .= " and almacenes_albaranes.id_almacen=".$this->id_almacen;
		}
        else {
            $condiciones .= " and almacenes_albaranes.id_almacen in (select id_almacen from almacenes where id_sede=".$this->id_sede.")";
        }

		if($this->id_ref != ""){
			$condiciones .= " and id_albaran in 
								(select id_albaran from almacenes_albaranes_referencias as albr where albr.id_referencia=".$this->id_ref." and albr.activo=1)";
		}

		$ordenado = " order by almacenes_albaranes.fecha_creado DESC, almacenes_albaranes.id_albaran DESC";
					
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