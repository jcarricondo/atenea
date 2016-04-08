<?php
class listadoMovimientos extends MySQL {
	
	// Variables de la clase
	var $nombre_albaran = "";
	var $tipo_albaran = "";
	var $id_tipo_participante = "";
	var $id_participante = "";
	var $tipo_motivo = "";
	var $id_usuario = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $id_ref = "";
	var $paginacion = "";
	var $id_almacen = "";
    var $id_sede = "";
	
	var $movimientos = "";
	var $consultaSql = "";

	// Establecemos en la clase los valores del buscador
	function setValores($nombre_albaran,$tipo_albaran,$id_tipo_participante,$id_participante,$tipo_motivo,$id_usuario,$id_ref,$fecha_desde,$fecha_hasta,$paginacion,$id_almacen,$id_sede) {
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_tipo_participante = $id_tipo_participante;
		$this->id_participante = $id_participante;
		$this->tipo_motivo = $tipo_motivo;
		$this->id_usuario = $id_usuario;
		$this->id_ref = $id_ref;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->paginacion = $paginacion;
		$this->id_almacen = $id_almacen;
        $this->id_sede = $id_sede;

		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select id,id_albaran,id_referencia,nombre_referencia,nombre_proveedor,referencia_proveedor,cantidad,metodo,id_usuario,id_almacen,fecha_creado from almacenes_albaranes_referencias where almacenes_albaranes_referencias.activo=1";

        // Vemos si el usuario ha filtrado por algun campo relacionado con un albaran
        $tipo_albaran_normal = $this->tipo_albaran == "ENTRADA" || $this->tipo_albaran == "SALIDA";
        $filtro_albaran = ($this->nombre_albaran != "") || ($this->id_tipo_participante != 3) || ($this->tipo_motivo != "") || ($tipo_albaran_normal);

        $condiciones = "";
        $condiciones_ajuste = "";

        // Si no se relleno ningún campo relacionado con los albaranes mostramos tambien los movimientos de ajuste
        if($filtro_albaran){
            $condiciones .= " and id_albaran in (select id_albaran from almacenes_albaranes where almacenes_albaranes.id_albaran is not null and almacenes_albaranes.activo=1";

            if($this->nombre_albaran != "") {
                $condiciones .= " and almacenes_albaranes.nombre_albaran like '%".$this->nombre_albaran."%'";
            }
            if($this->id_usuario != "") {
                $condiciones .= " and almacenes_albaranes.id_usuario=".$this->id_usuario;
            }
            if($this->fecha_desde != "") {
                $condiciones .= " and almacenes_albaranes.fecha_creado >= '".$this->fecha_desde."' ";
            }
            if($this->fecha_hasta != ""){
                $condiciones .= " and almacenes_albaranes.fecha_creado <= '".$this->fecha_hasta."' ";
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
                $condiciones .= " and almacenes_albaranes.motivo='".$this->tipo_motivo."'";
            }
            if($this->id_almacen != ""){
                $condiciones .= " and almacenes_albaranes.id_almacen=".$this->id_almacen;
            }
            else {
                $condiciones .= " and almacenes_albaranes.id_almacen in (select id_almacen from almacenes where id_sede=".$this->id_sede.")";
            }
            $condiciones .= ")";
            if($this->id_ref != ""){
                $condiciones .= " and almacenes_albaranes_referencias.id_referencia=".$this->id_ref;
            }
        }
        else {
            if($this->id_usuario != "") {
                $condiciones_ajuste .= " and almacenes_albaranes_referencias.id_usuario=".$this->id_usuario;
            }
            if($this->id_almacen != ""){
                $condiciones_ajuste .= " and almacenes_albaranes_referencias.id_almacen=".$this->id_almacen;
            }
            else {
                $condiciones_ajuste .= " and almacenes_albaranes_referencias.id_almacen in (select id_almacen from almacenes where id_sede=".$this->id_sede.")";
            }
            if($this->fecha_desde != "") {
                $condiciones_ajuste .= " and almacenes_albaranes_referencias.fecha_creado >= '".$this->fecha_desde."' ";
            }
            if($this->fecha_hasta != ""){
                $condiciones_ajuste .= " and almacenes_albaranes_referencias.fecha_creado <= '".$this->fecha_hasta."' ";
            }
            if($this->id_ref != ""){
                $condiciones_ajuste .= " and almacenes_albaranes_referencias.id_referencia=".$this->id_ref;
            }
            if($this->tipo_albaran != ""){
                if($this->tipo_albaran == "AJUSTE ENTRADA") {
                    $condiciones_ajuste .= " and almacenes_albaranes_referencias.metodo='AJUSTE RECEPCIONAR'";
                }
                else if($this->tipo_albaran == "AJUSTE SALIDA"){
                    $condiciones_ajuste .= " and almacenes_albaranes_referencias.metodo='AJUSTE DESRECEPCIONAR'";
                }
            }
        }

		$ordenado = " order by almacenes_albaranes_referencias.id DESC ";
					
		$this->consultaSql = $campos.$condiciones.$condiciones_ajuste.$ordenado.$this->paginacion;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->movimientos = $this->getResultados();
	}
}
?>