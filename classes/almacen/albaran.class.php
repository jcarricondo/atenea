<?php

class Albaran extends MySQL {

	var $id_albaran;
	var $nombre_albaran;
	var $tipo_albaran;
	var $id_participante;
	var $id_tipo_participante;
	var $motivo;
    var $metodo;
	var $id_usuario;
	var $id_almacen;
	var $fecha_creado;
	var $activo;

	var $id_referencia;
	var $nombre_referencia;
	var $nombre_proveedor;
	var $referencia_proveedor;
	var $nombre_pieza;
	var $pack_precio;
	var $unidades_paquete;
	var $cantidad;



	// Carga de datos de un albarán ya existente en la base de datos
	function cargarDatos($id_albaran,$nombre_albaran,$tipo_albaran,$id_participante,$id_tipo_participante,$motivo,$id_usuario,$id_almacen,$fecha_creado,$activo) {
		$this->id_albaran = $id_albaran;
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_participante = $id_participante;
		$this->id_tipo_participante = $id_tipo_participante;
		$this->motivo = $motivo;
		$this->id_usuario = $id_usuario;
		$this->id_almacen = $id_almacen;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del albarán en base a su ID
	function cargaDatosAlbaranId($id_albaran) {
		$consultaSql = sprintf("select * from almacenes_albaranes where id_albaran=%s",
			$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_albaran"],
			$resultados["nombre_albaran"],
			$resultados["tipo_albaran"],
			$resultados["id_participante"],
			$resultados["id_tipo_participante"],
			$resultados["motivo"],
			$resultados["id_usuario"],
			$resultados["id_almacen"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Función que establece los atributos del albarán
	function datosNuevoAlbaran($id_albaran = NULL,$nombre_albaran,$tipo_albaran,$id_participante,$id_tipo_participante,$motivo,$id_usuario,$id_almacen,$fecha_creado,$activo){
		$this->id_albaran = $id_albaran;
		$this->nombre_albaran = $nombre_albaran;
		$this->tipo_albaran = $tipo_albaran;
		$this->id_participante = $id_participante;
		$this->id_tipo_participante = $id_tipo_participante;
		$this->motivo = $motivo;
		$this->id_usuario = $id_usuario;
		$this->id_almacen = $id_almacen;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Función que establece los atributos de las referencias del albarán
	function datosNuevaReferenciaAlbaran($id_albaran,$id_referencia,$nombre_referencia,$nombre_proveedor,$referencia_proveedor,$nombre_pieza,$pack_precio,$unidades_paquete,$cantidad,$metodo,$id_usuario,$id_almacen,$activo){
		$this->id_albaran = $id_albaran;
		$this->id_referencia = $id_referencia;
		$this->nombre_referencia = $nombre_referencia;
		$this->nombre_proveedor = $nombre_proveedor;
		$this->referencia_proveedor = $referencia_proveedor;
		$this->nombre_pieza = $nombre_pieza;
		$this->pack_precio = $pack_precio;
		$this->unidades_paquete = $unidades_paquete;
		$this->cantidad = $cantidad;
        $this->metodo = $metodo;
        $this->id_usuario = $id_usuario;
        $this->id_almacen = $id_almacen;
        $this->activo = $activo;
	}

	// Función que comprueba el tipo de participante en el albarán
	function dameTipoParticipante($nombre_participante){
		// Comprueba si es un proveedor
		$consultaSql = sprintf("select id_proveedor from proveedores where nombre_prov=%s",
			$this->makeValue($nombre_participante, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			// Comprueba si es un centro logistico
			$consultaSql = sprintf("select id_centro_logistico from centros_logisticos where centro_logistico=%s",
				$this->makeValue($nombre_participante, "text"));
			$this->setConsulta($consultaSql);
			$this->ejecutarConsulta();	
			if($this->getNumeroFilas() == 0) {
				// ERROR
				// No es ni un centro ni un proveedor
				return 3;
			} 
			else {
                // CENTRO LOGISTICO
				return 2;
			}
		} 
		else {
            // PROVEEDOR
			return 1;
		}
	}

	// Guarda el albarán
	function guardarAlbaran() {
		if($this->albaran == NULL) {
			// Comprueba si ya existe el albarán en la BBDD
			if(!$this->comprobarAlbaranDuplicado()){
				$consulta = sprintf("insert into almacenes_albaranes (nombre_albaran,tipo_albaran,id_participante,id_tipo_participante,motivo,id_usuario,id_almacen,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre_albaran, "text"),
					$this->makeValue($this->tipo_albaran, "text"),
					$this->makeValue($this->id_participante, "int"),
					$this->makeValue($this->id_tipo_participante, "int"),
					$this->makeValue($this->motivo, "text"),
					$this->makeValue($this->id_usuario, "int"),
					$this->makeValue($this->id_almacen, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_albaran = $this->getUltimoID();
					return 1;
				} 
				else {
					return 2;
				}
			}
			else return 4;	
		}
	}

	// Guarda las referencias del albarán
	function guardarReferenciasAlbaran(){
		$consulta = sprintf("insert into almacenes_albaranes_referencias (id_albaran,id_referencia,nombre_referencia,nombre_proveedor,referencia_proveedor,nombre_pieza,pack_precio,unidades_paquete,cantidad,metodo,id_usuario,id_almacen,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,CURRENT_TIMESTAMP,1)",
			$this->makeValue($this->id_albaran, "int"),
			$this->makeValue($this->id_referencia, "int"),
			$this->makeValue($this->nombre_referencia, "text"),
			$this->makeValue($this->nombre_proveedor, "text"),
			$this->makeValue($this->referencia_proveedor, "text"),
			$this->makeValue($this->nombre_pieza, "text"),
			$this->makeValue($this->pack_precio, "float"),
			$this->makeValue($this->unidades_paquete, "int"),
			$this->makeValue($this->cantidad, "float"),
            $this->makeValue($this->metodo, "text"),
            $this->makeValue($this->id_usuario, "int"),
            $this->makeValue($this->id_almacen, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			$this->id = $this->getUltimoID();
			return 1;
		} 
		else {
			return 5;
		}
	}

	// Función que guarda un log de la Operación del Albarán
	function guardarLogReferencia($id_albaran,$id_referencia,$id_produccion,$piezas,$metodo,$id_usuario,$id_almacen){
		$consulta = sprintf("insert into almacenes_albaranes_log (id_albaran,id_referencia,id_produccion,piezas,metodo,id_usuario,id_almacen,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
			$this->makeValue($id_albaran, "int"),
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($id_produccion,"int"),
			$this->makeValue($piezas, "float"),
			$this->makeValue($metodo, "text"),
            $this->makeValue($id_usuario, "int"),
            $this->makeValue($id_almacen, "int"));
		$this->setConsulta($consulta);	
		if ($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 6;
		}			
	}

	// Desactivamos el log del Albaran para esa referencia
	function desactivarLogReferencia($id_albaran,$id_referencia,$id_produccion){
		$consulta = sprintf("update almacenes_albaranes_log set activo=0 where id_albaran=%s and id_referencia=%s and id_produccion=%s",
			$this->makeValue($id_albaran,"int"),
			$this->makeValue($id_referencia,"int"),
			$this->makeValue($id_produccion,"int"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 7;
		}
	}

	// Desactivamos la referencia del albarán
	function desactivarReferencia($id_albaran,$id_referencia){
		$consulta = sprintf("update almacenes_albaranes_referencias set activo=0 where id_albaran=%s and id_referencia=%s",
			$this->makeValue($id_albaran,"int"),
			$this->makeValue($id_referencia,"int"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 8;
		}
	}

	// Función que desactiva un albarán vacío
	function desactivarAlbaran($id_albaran){
		$consulta = sprintf("update almacenes_albaranes set activo=0 where id_albaran=%s",
			$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 9;
		}
	}

    // Función que activa un albarán no vacío
    function activarAlbaran($id_albaran){
        $consulta = sprintf("update almacenes_albaranes set activo=1 where id_albaran=%s",
            $this->makeValue($id_albaran, "int"));
        $this->setConsulta($consulta);
        if($this->ejecutarSoloConsulta()) {
            return 1;
        }
        else {
            return 10;
        }
    }

	// Función que devuelve el id del participante en función de su tipo
	function dameIdParticipante($id_tipo_participante,$nombre_participante){
		if ($id_tipo_participante == 1){
			// ES UN PROVEEDOR
			$consultaSql = sprintf("select id_proveedor from proveedores where nombre_prov=%s",
				$this->makeValue($nombre_participante, "text"));
			$this->setConsulta($consultaSql);
			$this->ejecutarConsulta();
		}
		else {
			// ES UN CENTRO LOGISTICO
			$consultaSql = sprintf("select id_centro_logistico from centros_logisticos where centro_logistico=%s",
				$this->makeValue($nombre_participante, "text"));
			$this->setConsulta($consultaSql);
			$this->ejecutarConsulta();	
		}	
		$this->id_participante = $this->getPrimerResultado();
	}

	// Comprueba si ya existe un albarán con ese nombre en la BBDD
	function comprobarAlbaranDuplicado() {
		if($this->id_albaran == NULL) {
			$consulta = sprintf("select id_albaran from almacenes_albaranes where nombre_albaran=%s and activo=1",
				$this->makeValue($this->nombre_albaran, "text"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		} else {
			return true;
		}
	}

	// Devuelve el ultimo albarán registrado en la BBDD
	function dameUltimoAlbaran($id_almacen) {
		$consulta = sprintf("select max(id_albaran) as id_albaran from almacenes_albaranes where activo=1 and id_almacen=%s",
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Función que devuelve las referencias de un albaran
	function dameReferenciasAlbaran($id_albaran){
		$consulta = sprintf("select * from almacenes_albaranes_referencias where id_albaran=%s and activo=1 order by almacenes_albaranes_referencias.id_referencia ",
			$this->makeValue($id_albaran, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que devuelve los datos de albarán_referencias según el ID
	function dameDatosAlbaranReferenciasPorId($id){
		$consulta = sprintf("select * from almacenes_albaranes_referencias where id=%s and activo=1 ",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

	// Función que devuelve los datos del log dado un albarán y una referencia
	function dameDatosLogReferencia($id_albaran,$id_referencia){
		$consulta = sprintf("select * from almacenes_albaranes_log where id_albaran=%s and id_referencia=%s and activo=1 ",
			$this->makeValue($id_albaran,"int"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

	// Función que devuelve los datos del log dado un albarán
	function dameLogAlbaran($id_albaran){
		$consulta = sprintf("select * from almacenes_albaranes_log where id_albaran=%s and activo=1 ",
			$this->makeValue($id_albaran,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

	// Función que devuelve los id de los albaranes activos
	function dameAlbaranesActivos(){
		$consulta = "select id_albaran from almacenes_albaranes where activo=1 ";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

    // Función que devuelve los albaranes vacíos
    function dameAlbaranesVacios(){
        $consulta = "select id_albaran from almacenes_albaranes where activo=1
	                    and id_albaran not in (select id_albaran from almacenes_albaranes_referencias where activo=1)
	                    and id_albaran not in (select id_albaran from almacenes_albaranes_log where activo=1)";
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

    // Función que desactiva todos los albaranes vacíos
    function desactivarAlbaranesVacios(){
        // Comprobamos si existen albaranes vacios
        $res_alb = $this->dameAlbaranesVacios();
        for($i=0;$i<count($res_alb);$i++){
            $id_albaran = $res_alb[$i]["id_albaran"];
            $res = $this->desactivarAlbaran($id_albaran);
            if($res != 1){
                $i = count($res_alb);
            }
        }
        return res;
    }

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar el albarán<br/>';
			break;
			case 3:
				return 'El origen seleccionado no es correcto<br/>';
			break;
			case 4:
				return 'Ya existe un albaran con ese nombre en la base de datos<br/>';
			break;
			case 5:
				return 'Se produjo un error al guardar las referencias del albarán<br/>';
			break;
			case 6:
				return 'Se produjo un error al guardar el log de la referencia del albarán<br/>';
			break;
			case 7:
				return 'Se produjo un error al desactivar el log de la referencia del albarán<br/>';
			break;
			case 8:
				return 'Se produjo un error al desactivar la referencia del albarán<br/>';
			break;
			case 9:
				return 'Se produjo un error al desactivar el albaran vacio';
			break;
            case 10:
                return 'Se produjo un error al desactivar el albaran vacio';
            break;
		}
	}
}
?>