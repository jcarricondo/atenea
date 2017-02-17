<?php
class Almacen extends MySql{
	
	var $id_almacen;
	var $nombre;
	var $id_sede;

	function cargarDatos($id_almacen,$nombre,$id_sede) {
		$this->id_almacen = $id_almacen;
		$this->nombre = $nombre;
		$this->id_sede = $id_sede;
	}
	
	function cargaDatosAlmacenId($id_almacen) {
		$consultaSql = sprintf("select * from almacenes where id_almacen=%s",
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_almacen"],
			$resultados["almacen"],
			$resultados["id_sede"]
		);
	}

	// Función que devuelve todos los almacenes existentes
	function dameAlmacenes(){
		$consultaSql = "select * from almacenes where activo=1";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

    // Función que devuelve todos los almacenes de fábrica existentes
    function dameAlmacenesFabrica(){
        $consultaSql = "select * from almacenes where activo=1 and (id_tipo=0 or id_tipo=1)";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que devuelve todos los almacenes de mantenimiento existentes
    function dameAlmacenesMantenimiento(){
        $consultaSql = "select * from almacenes where activo=1 and (id_tipo=0 or id_tipo=2)";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que devuelve el primer almacen de mantenimiento de todas las sedes de Mantenimiento
    function damePrimerAlmacenMantenimiento(){
        $consultaSql = "select id_almacen from almacenes where activo=1 and (id_tipo=0 or id_tipo=2) limit 0,1";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        $res_id_almacen = $this->getPrimerResultado();
        $res_id_almacen = $res_id_almacen["id_almacen"];
        return $res_id_almacen;
    }

	// Función que devuelve la sede a la que pertenece el almacen
	function dameSedeAlmacen($id_almacen){
		$consultaSql = sprintf("select id_sede from almacenes where id_almacen=%s",
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

    // Función que devuelve los motivos de un albarán de entrada
    function dameMotivosAlbaranEntrada($id_almacen){
        $consultaSql = sprintf("select motivo from almacenes_albaranes_motivos where activo=1 and tipo_motivo='ENTRADA' and id_sede in
                                  (select id_sede from almacenes where activo=1 and id_almacen=%s)",
            $this->makeValue($id_almacen, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que devuelve los motivos de un albarán de salida
    function dameMotivosAlbaranSalida($id_almacen){
        $consultaSql = sprintf("select motivo from almacenes_albaranes_motivos where activo=1 and tipo_motivo='SALIDA' and id_sede in
                                  (select id_sede from almacenes where activo=1 and id_almacen=%s)",
            $this->makeValue($id_almacen, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que devuelve los motivos de un albarán de un almacen sin tener en cuenta el tipo
    function dameMotivosAlbaran($id_almacen){
        $consultaSql = sprintf("select distinct motivo from almacenes_albaranes_motivos where activo=1 and id_sede in
                                  (select id_sede from almacenes where activo=1 and id_almacen=%s) order by tipo_motivo,id_motivo",
            $this->makeValue($id_almacen, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que determinar si el almacen pertence a Brasil
    function esAlmacenBrasil($id_almacen){
        $consultaSql = sprintf("select id_sede from almacenes where id_almacen=%s",
            $this->makeValue($id_almacen, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        $res = $this->getPrimerResultado();
        return $res["id_sede"] == 3;
    }

}
?>