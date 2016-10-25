<?php
class Componente extends MySQL {

	var $id_componente;
	var $nombre;
	var $referencia;
	var $version;
	var $descripcion;
	var $estado;
	var $prototipo;
	var $id_tipo;
    var $fecha_creacion;
	
	function cargarDatos($id_componente,$nombre,$referencia,$version,$descripcion,$id_tipo,$estado,$prototipo,$fecha_creacion) {
		$this->id_componente = $id_componente;
		$this->nombre = $nombre;
		$this->referencia = $referencia;
		$this->version = $version;
		$this->descripcion = $descripcion;
		$this->estado = $estado;
		$this->prototipo = $prototipo;
		$this->id_tipo = $id_tipo;
        $this->fecha_creacion = $fecha_creacion;
	}

	function cargaDatosComponenteId($id_componente) {
		$consultaSql = sprintf("select * from componentes where id_componente=%s",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_componente"],
			$resultados["nombre"],
			$resultados["referencia"],
			$resultados["version"],
			$resultados["descripcion"],
			$resultados["id_tipo"],
			$resultados["estado"],
			$resultados["prototipo"],
            $resultados["fecha_creacion"]
		);
	}

	// Se hace la carga de datos  de un nuevo componente
	function datosComponente($id_componente,$nombre,$referencia,$version,$descripcion,$estado,$prototipo,$id_tipo) {
		$this->id_componente = $id_componente;
		$this->nombre = $nombre;
		$this->referencia = $referencia;
		$this->version = $version;
		$this->descripcion = $descripcion;
		$this->estado = $estado;
		$this->prototipo = $prototipo;
		$this->id_tipo = $id_tipo;
	}


	// Se comprueba si el componente existe en la base de datos
	function getExisteComponente($nombre,$version,$tipo) {
		$consultaSql = sprintf("select id_componente from componentes where nombre=%s and version=%s and id_tipo=%s and activo=1",
			$this->makeValue($nombre, "text"),
			$this->makeValue($version, "float"),
			$this->makeValue($tipo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id_componente"];
	}

	
	// Se comprueba si ya existe la referencia en el componente
	function buscaReferenciaComponente($id_componente,$id_referencia) {
		$consultaSql = sprintf("select id from componentes_referencias where id_componente=%s and id_referencia=%s and activo=1",
			$this->makeValue($id_componente, "int"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id"];
	}

	// Función que devuelve el tipo de componente
	function dameTipoComponente($id_componente){
		$consultaSql = sprintf("select id_tipo from componentes where activo=1 and id_componente=%s", 
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id_tipo"];
	}

    // Función que devuelve los kits asociados al componente
    function dameKitsComponente($id_componente){
        $consulta = sprintf("select id_kit from componentes_kits where activo=1 and id_componente=%s order by fecha_creado",
            $this->makeValue($id_componente,"int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }

	// Función que devuelve true si es un componente principal
	function esComponentePrincipal($id_tipo_componente){
		return $id_tipo_componente == 1 || $id_tipo_componente == 2;
	}

    /*
	// Función que determina si un componente es SOFTWARE
    function esComponenteSoftware($id_tipo_componente){
        return $id_tipo_componente == 3;
    }
    */

    // Función que obtiene las referencias y las piezas de un componente
    function dameRefsYPiezasComponente($id_componente){
        $consulta = sprintf("select id_referencia,piezas from componentes_referencias where activo=1 and id_componente=%s",
            $this->makeValue($id_componente,"int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        return $this->getResultados();
    }


    // Recorre las referencias de un componente y lo añade al array principal de referencias
    function agruparReferenciasComponentes($referencias_componente_secundario,$referencias_componente_final){
        $referencias_aux = $referencias_componente_final;
        for($i=0;$i<count($referencias_componente_secundario);$i++){
            $id_referencia = $referencias_componente_secundario[$i]["id_referencia"];
            $piezas = $referencias_componente_secundario[$i]["piezas"];
            $encontrado = false;
            $j=0;
            while(($j<count($referencias_componente_final)) and (!$encontrado)){
                // Si coinciden las referencias sumamos las piezas.
                if ($id_referencia == $referencias_componente_final[$j]["id_referencia"]){
                    $referencias_aux[$j]["piezas"] = $referencias_aux[$j]["piezas"] + $piezas;
                    $encontrado = true;
                }
                $j++;
            }
            if(!$encontrado){
                // Si no esta la referencia la insertamos al final
                array_push($referencias_aux,$referencias_componente_secundario[$i]);
            }
            // Modificamos el array de referencias del componente por el array modificado con las referencias del kit
            unset($referencias_componente_final);
            $referencias_componente_final = $referencias_aux;
        }
        unset($referencias_aux);
        return $referencias_componente_final;
    }

    function dameNombreTipoComponente($id_tipo){
        switch($id_tipo) {
            case "1":
                $tipo_componente = "CABINA";
            break;
            case "2":
                $tipo_componente = "PERIFERICO";
            break;
            case "3":
                $tipo_componente = "SOFTWARE";
            break;
            case "4":
                $tipo_componente = "INTERFAZ";
            break;
            case "5":
                $tipo_componente = "KIT";
            break;
        }
        return $tipo_componente;
    }

}
?>