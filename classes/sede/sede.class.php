<?php
class Sede extends MySql{
	
	var $id_sede; 
	var $nombre;
    var $id_tipo;
    var $activo;

	function cargarDatos($id_sede,$nombre,$id_tipo) {
		$this->id_sede = $id_sede;
		$this->nombre = $nombre;
        $this->id_tipo = $id_tipo;
	}
	
	function cargaDatosSedeId($id_sede) {
		$consultaSql = sprintf("select * from sedes where id_sede=%s",
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_sede"],
			$resultados["sede"],
            $resultados["id_tipo"]
		);
	}

	// Función que devuelve todas las sedes existentes
	function dameSedes(){
		$consultaSql = "select * from sedes";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que devuelve los almacenes de una sede
	function dameAlmacenesSede($id_sede){
		$consultaSql = sprintf("select * from almacenes where activo=1 and id_sede=%s",
			$this->makeValue($id_sede, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

    // Función que devuelve los almacenes de fábrica de una sede
    function dameAlmacenesFabricaSede($id_sede){
        $consultaSql = sprintf("select * from almacenes where activo=1 and id_sede=%s
                                  and (id_tipo=0 or id_tipo=1)",
            $this->makeValue($id_sede, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que devuelve los almacenes de mantenimiento de una sede
    function dameAlmacenesMantenimientoSede($id_sede){
        $consultaSql = sprintf("select * from almacenes where activo=1 and id_sede=%s
                                  and (id_tipo=0 or id_tipo=2)",
            $this->makeValue($id_sede, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

	// Función que devuelve los alias de las OP de una sede
	function dameAliasOPSede($id_sede){
		if($id_sede != 0){
			$consultaSql = sprintf("select alias from orden_produccion where activo=1 and alias is not null and id_sede=%s and orden_produccion.activo=1 order by fecha_creado desc",
				$this->makeValue($id_sede, "int"));
		}
		else {
			$consultaSql = "select alias from orden_produccion where activo=1 and alias is not null and orden_produccion.activo=1 order by fecha_creado desc";
		} 
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

    // Función que devuelve las sedes del módulo de Fábrica
    function dameSedesFabrica(){
        $consultaSql = "select * from sedes where activo=1 and id_sede in
                          (select id_sede from almacenes where activo=1 and (id_tipo=0 or id_tipo=1))";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que devuelve las sedes del modulo de Mantenimiento
    function dameSedesMantenimiento(){
        $consultaSql = "select * from sedes where activo=1 and id_sede in
                          (select id_sede from almacenes where activo=1 and (id_tipo=0 or id_tipo=2))";
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que devuelve los motivos de albaran segun la sede
    function dameMotivosAlbaranSede($id_sede){
        $consultaSql = sprintf("select distinct motivo from almacenes_albaranes_motivos where activo=1 and id_sede=%s",
                            $this->makeValue($id_sede,"int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

	// Función que devuelve los motivos de albaran de periféricos según la sede
	function dameMotivosAlbaranPerifericosSede($id_sede){
		$consultaSql = sprintf("select distinct motivo from albaranes_perifericos_motivos where activo=1 and id_sede=%s",
				$this->makeValue($id_sede,"int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que devuelve los motivos de albaran de simuladores según la sede
	function dameMotivosAlbaranSimuladoresSede($id_sede){
		$consultaSql = sprintf("select distinct motivo from albaranes_simuladores_motivos where activo=1 and id_sede=%s",
				$this->makeValue($id_sede,"int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

    // Función que devuelve los usuarios de almacen segun su sede
    function dameUsuariosAlmacenSede($id_sede){
        $consultaSql = sprintf("select * from usuarios where id_almacen in (select id_almacen from almacenes where id_sede=%s)",
            $this->makeValue($id_sede,"int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }



}
?>