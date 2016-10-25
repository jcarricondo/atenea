<?php
class RecepcionMaterial extends MySql{

	var $id_referencia = NULL;
	var $ordenes_compra;
	var $orden_produccion;
	var $orden_compra;
	var $id;
	var $pedidas;
	var $recibidas; // Piezas recibidas, proceso de entrada
	var $piezas_recibidas; // Piezas recibidas, guardadas en la base de datos
	var $asignadas;
	var $unidades_restantes;
	var $numero_pedido;
	var $piezas_por_entrar;
	var $id_almacen;
	var $mostrarEntradaStock = false;
	var $error = false;


	function setReferencia($id_referencia,$orden_produccion,$orden_compra) {
		$this->id_referencia = $id_referencia;
		$this->orden_produccion = $orden_produccion;
		$this->orden_compra = $orden_compra;
	}

	function getOrdenesReferenciaPendientes() {
		$addSql = "";
		if($this->orden_produccion != "") {
			$addSql .= " and oc.id_produccion=".$this->orden_produccion;
		}
		if($this->orden_compra != "") {
			$addSql .= " and oc.numero_pedido like '%".$this->orden_compra."%'";
		}
		$consulta = "select oc.id_orden_compra from orden_compra_referencias as ocr inner join orden_compra as oc on (oc.id_orden_compra=ocr.id_orden) inner join orden_produccion as op on (op.id_produccion=oc.id_produccion) where ocr.id_referencia=".$this->id_referencia." and ocr.activo=1 and (ocr.total_piezas-ocr.piezas_recibidas)!=0 and op.estado='INICIADO' and op.activo=1".$addSql." group by oc.id_orden_compra order by ocr.fecha_creado asc";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ordenes_compra = $this->getResultados();
	}

	function setDatosEntrada($id,$id_referencia,$pedidas,$recibidas,$asignadas) {
		$this->id = $id;
		$this->id_referencia = $id_referencia;
		$this->pedidas = $pedidas;
		$this->recibidas = $recibidas;
		$this->asignadas = $asignadas;

		$this->getDatosBaseDatos();
	}

	function getDatosBaseDatos() {
		$consultaSql = sprintf("select oc.numero_pedido,ocr.piezas_recibidas from orden_compra_referencias as ocr inner join orden_compra as oc on (oc.id_orden_compra=ocr.id_orden) where ocr.id=%s and ocr.activo=1",
			$this->makeValue($this->id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$datos = $this->getPrimerResultado();
		$this->numero_pedido = $datos["numero_pedido"];
		$this->piezas_recibidas = $datos["piezas_recibidas"];
	}

	function procesar() {
		$total_recibidas = $this->piezas_recibidas + $this->asignadas;
		$this->recibidas = $total_recibidas;
		$this->piezas_por_entrar = $this->pedidas - $this->recibidas;
		$this->piezas_por_entrar = round($this->piezas_por_entrar,15);
		if($this->piezas_por_entrar < 0) {
			$this->error = true;
		} else {
			if(is_float($this->recibidas)) {
				$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
					$this->makeValue($this->recibidas, "double"),
					$this->makeValue($this->id, "int"));
			} else {
				$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
					$this->makeValue($this->recibidas, "int"),
					$this->makeValue($this->id, "int"));
			}
			$this->setConsulta($updateSql);
			if($this->ejecutarSoloConsulta()) {
				return 1;
			} else {
				return 0;
			}
		}
	}


	// Función que realiza la función inversa a procesar
	function descontar() {
		$total_recibidas = $this->piezas_recibidas - $this->asignadas;
		$this->recibidas = $total_recibidas;
		$this->piezas_por_entrar = $this->pedidas - $this->recibidas;
		$this->piezas_por_entrar = round($this->piezas_por_entrar,15);
		if($this->piezas_por_entrar < 0) {
			$this->error = true;
		} 
		else {
			if(is_float($this->recibidas)) {
				$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
					$this->makeValue($this->recibidas, "double"),
					$this->makeValue($this->id, "int"));
			} else {
				$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
					$this->makeValue($this->recibidas, "int"),
					$this->makeValue($this->id, "int"));
			}
			$this->setConsulta($updateSql);
			if($this->ejecutarSoloConsulta()) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	function guardarStock($id_referencia,$unidades_restantes,$id_almacen) {
		$consultaSql = sprintf("select id_referencia from stock_almacenes where id_referencia=%s and id_almacen=%s",
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		if($this->getNumeroFilas() == 0) {
			$updateSql = sprintf("insert into stock_almacenes (id_referencia,piezas,id_almacen) values (%s,%s,%s)",
				$this->makeValue($id_referencia, "int"),
				$this->makeValue($unidades_restantes, "float"),
				$this->makeValue($id_almacen, "int"));
		} else {
			$updateSql = sprintf("update stock_almacenes set piezas=(piezas+(%s)) where id_referencia=%s and id_almacen=%s",
				$this->makeValue($unidades_restantes, "float"),
				$this->makeValue($id_referencia, "int"),
				$this->makeValue($id_almacen, "int"));
		}
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} else {
			return 0;
		}
	}

	// Función que descuenta las piezas del Stock
	function quitarDelStock($id_referencia,$unidades_restantes,$id_almacen) {
		$consultaSql = sprintf("select id_referencia from stock_almacenes where id_referencia=%s and id_almacen=%s",
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		if($this->getNumeroFilas() != 0) {
			// Obtenemos el número de piezas del stock
			$consulta = sprintf("select piezas from stock_almacenes where id_referencia=%s and id_almacen=%s",
				$this->makeValue($id_referencia, "int"),
				$this->makeValue($id_almacen, "int"));
			$this->setConsulta($consulta);
			$this->ejecutarConsulta();
			$piezas = $this->getPrimerResultado();
			$piezas = $piezas["piezas"];
			$piezas = $piezas - $unidades_restantes;

			// Si quitamos todas las piezas del stock
			if($piezas < 0){
				$piezas = 0;
			}

			$updateSql = sprintf("update stock_almacenes set piezas=%s where id_referencia=%s and id_almacen=%s",
				$this->makeValue($piezas, "float"),
				$this->makeValue($id_referencia, "int"),
				$this->makeValue($id_almacen, "int"));
		}
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} else {
			return 0;
		}
	}

	// Devuelve las piezas en stock de una referencia
	function damePiezasReferenciaStock($id_referencia,$id_almacen){
		$consulta = sprintf("select piezas from stock_almacenes where id_referencia=%s and id_almacen=%s",
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($id_almacen,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$piezas = $this->getPrimerResultado();
		$piezas = $piezas["piezas"];
		return $piezas;			
	}

	// Función que devuelve el registro de la tabla orden_compra_referencias de las OP iniciadas
	function dameRegistroOCR($id_produccion,$id_referencia){
		$consulta = sprintf("select * from orden_compra_referencias as ocr 
								inner join orden_compra as oc on (oc.id_orden_compra=ocr.id_orden)
								inner join orden_produccion as op on (op.id_produccion=oc.id_produccion)
									where ocr.activo=1 and op.estado='INICIADO' and op.activo=1 and op.id_produccion=%s and ocr.id_referencia=%s",
			$this->makeValue($id_produccion,"int"),
			$this->makeValue($id_referencia,"int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
       	return $this->getPrimerResultado();
    }

    // Función que actualiza las piezas recibidas según el id
    function actualizaPiezasRecibidasPorId($id,$piezas_recibidas){
    	$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
                                $this->makeValue($piezas_recibidas, "float"),
                                $this->makeValue($id, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
            return 0;
        }
	}

	// Función que actualiza las piezas recibidas segun el id
    function recepcionarPorId($id,$id_referencia,$piezas_recibidas){
    	$updateSql = sprintf("update orden_compra_referencias set piezas_recibidas=%s where id=%s",
			$this->makeValue($piezas_recibidas, "float"),
			$this->makeValue($id, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 0;
		}
	}

	// Función que realiza la consulta de actualización de las piezas del stock según la referencia
    function actualizaPiezasStock($id_referencia,$piezas_stock,$id_almacen){
    	$updateSql = sprintf("update stock_almacenes set piezas=piezas+".$piezas_stock." where id_referencia=%s and id_almacen=%s",
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 0;
		}
	}

	// Función que realiza la consulta de inserción de piezas en stock según la referencia
    function insertaPiezasStock($id_referencia,$unidades,$id_almacen){
    	$insertSql = sprintf("insert into stock_almacenes (id_referencia,piezas,id_almacen,ubicacion) values (%s,%s,%s,NULL)",
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($unidades, "float"),
			$this->makeValue($id_almacen,"int"));
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 0;
		}
	}

	// Función que pone a 0 el número de piezas en Stock
    function retiraPiezaStock($id_referencia,$id_almacen){
    	$insertSql = sprintf("update stock_almacenes set piezas=0 where id_referencia=%s and id_almacen=%s",
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 0;
		}
	}

	// Función que descuenta las piezas del stock según la referencia
    function descontarPiezasStock($id_referencia,$piezas_stock,$id_almacen){
    	$updateSql = sprintf("update stock_almacenes set piezas=piezas-".$piezas_stock." where id_referencia=%s and id_almacen=%s",
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 0;
		}
	}

    // Función que comprueba si hay piezas recibidas en las OPs iniciadas de un almacen
    function hayPiezasRecibidasOPsIniciadas($id_referencia,$id_almacen){
        $consultaSql = sprintf("select piezas_recibidas from orden_compra_referencias where activo=1 and id_referencia=%s and id_orden in
	                              (select id_orden_compra from orden_compra where activo=1 and id_produccion in
		                            (select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede in
			                          (select id_sede from almacenes where id_almacen=%s))) and piezas_recibidas <> 0",
                            $this->makeValue($id_referencia, "int"),
                            $this->makeValue($id_almacen, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        $res_piezas = $this->getResultados();
        return !empty($res_piezas);
    }

    // Función que devuelve las piezas recibidas en las OPs iniciadas de un almacen
    function damePiezasRecibidasOPsIniciadas($id_referencia,$id_almacen){
        $consultaSql = sprintf("select sum(piezas_recibidas) as total_piezas_recibidas from orden_compra_referencias where activo=1 and id_referencia=%s and id_orden in
	                              (select id_orden_compra from orden_compra where activo=1 and id_produccion in
		                            (select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede in
			                          (select id_sede from almacenes where id_almacen=%s))) and piezas_recibidas <> 0",
            $this->makeValue($id_referencia, "int"),
            $this->makeValue($id_almacen, "int"));
        $this->setConsulta($consultaSql);
        $this->ejecutarConsulta();
        $res_piezas = $this->getPrimerResultado();
        $total_piezas_recibidas = $res_piezas["total_piezas_recibidas"];
        return $total_piezas_recibidas;
    }

}
?>
