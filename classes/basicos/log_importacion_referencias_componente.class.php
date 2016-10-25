<?php
class LogImportacionReferenciasComponente extends MySQL {
	
	// Variables de la clase
	var $id_usuario;
	var $id_referencia;
	var $id_proveedor;
	var $id_fabricante;
	var $nombre_referencia;
	var $nombre_pieza;
	var $tipo_pieza;
    var $ref_proveedor;
    var $ref_fabricante;
	var $nombre1;
	var $valor1;
    var $nombre2;
    var $valor2;
    var $nombre3;
	var $valor3;
    var $nombre4;
    var $valor4;
    var $nombre5;
    var $valor5;
    var $pack_precio;
    var $unidades_paquete;
    var $comentarios;
    var $piezas_ref;
    var $nombre_proveedor;
    var $nombre_fabricante;
    var $proveedor_creado;
    var $fabricante_creado;
    var $referencia_creada;
    var $id_componente; 
    var $tipo_componente;
    var $proceso;
    var $fecha_creado;

	function setValores($id_usuario,$id_referencia,$id_proveedor,$id_fabricante,$nombre_referencia,$nombre_pieza,$tipo_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,$nombre3,
							$valor3,$nombre4,$valor4,$nombre5,$valor5,$pack_precio,$unidades_paquete,$comentarios,$piezas_ref,$nombre_proveedor,$nombre_fabricante,
    						$proveedor_creado,$fabricante_creado,$referencia_creada,$id_componente,$tipo_componente,$proceso) {

		$this->id_usuario = $id_usuario;
		$this->id_referencia = $id_referencia;
		$this->id_proveedor = $id_proveedor;
		$this->id_fabricante = $id_fabricante;
		$this->nombre_referencia = $nombre_referencia;
		$this->nombre_pieza = $nombre_pieza;
		$this->tipo_pieza = $tipo_pieza;
	    $this->ref_proveedor = $ref_proveedor;
	    $this->ref_fabricante = $ref_fabricante;
		$this->nombre1 = $nombre1;
		$this->valor1 = $valor1;
	    $this->nombre2 = $nombre2;
	    $this->valor2 = $valor2;
	    $this->nombre3 = $nombre3;
		$this->valor3 = $valor3;
	    $this->nombre4 = $nombre4;
	    $this->valor4 = $valor4;
	    $this->nombre5 = $nombre5;
	    $this->valor5 =  $valor5;
	    $this->pack_precio = $pack_precio;
	    $this->unidades_paquete = $unidades_paquete;
	    $this->comentarios = $comentarios;
	    $this->piezas_ref = $piezas_ref;
	    $this->nombre_proveedor = $nombre_proveedor;
	    $this->nombre_fabricante = $nombre_fabricante;
	    $this->proveedor_creado = $proveedor_creado;
	    $this->fabricante_creado =  $fabricante_creado;
	    $this->referencia_creada = $referencia_creada;
	    $this->id_componente = $id_componente; 
	    $this->tipo_componente = $tipo_componente;
	    $this->proceso = $proceso;
	}

	function guardarLog(){

		$insertSql = sprintf("insert into log_importacion_referencias_componente (id_usuario,id_referencia,id_proveedor,id_fabricante,nombre_referencia,nombre_pieza,
								tipo_pieza,ref_proveedor,ref_fabricante,nombre1,valor1,nombre2,valor2,nombre3,valor3,nombre4,valor4,nombre5,valor5,pack_precio,
								unidades_paquete,comentarios,piezas_ref,nombre_proveedor,nombre_fabricante,proveedor_creado,fabricante_creado,referencia_creada,
								id_componente,tipo_componente,proceso,fecha_creado) 
								values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp)",
						
						$this->makeValue($this->id_usuario, "int"),
						$this->makeValue($this->id_referencia, "int"),
						$this->makeValue($this->id_proveedor, "int"),
						$this->makeValue($this->id_fabricante, "int"),
						$this->makeValue($this->nombre_referencia, "text"),
						$this->makeValue($this->nombre_pieza, "text"),
						$this->makeValue($this->tipo_pieza, "text"),
	    				$this->makeValue($this->ref_proveedor, "text"),
	    				$this->makeValue($this->ref_fabricante, "text"),
						$this->makeValue($this->nombre1, "text"),
						$this->makeValue($this->valor1, "text"),
	    				$this->makeValue($this->nombre2, "text"),
	    				$this->makeValue($this->valor2, "text"),
	    				$this->makeValue($this->nombre3, "text"),
						$this->makeValue($this->valor3, "text"),
	    				$this->makeValue($this->nombre4, "text"),
	    				$this->makeValue($this->valor4, "text"),
	    				$this->makeValue($this->nombre5, "text"),
	    				$this->makeValue($this->valor5, "text"),
	    			    $this->makeValue($this->pack_precio, "text"),
	    				$this->makeValue($this->unidades_paquete, "text"),
	    				$this->makeValue($this->comentarios, "text"),
	    				$this->makeValue($this->piezas_ref, "text"),
	    				$this->makeValue($this->nombre_proveedor, "text"),
	    				$this->makeValue($this->nombre_fabricante, "text"),
	      				$this->makeValue($this->proveedor_creado, "text"),
	    				$this->makeValue($this->fabricante_creado, "text"),
	    				$this->makeValue($this->referencia_creada, "text"), 
	    				$this->makeValue($this->id_componente, "int"), 
	    				$this->makeValue($this->tipo_componente, "text"),
	    				$this->makeValue($this->proceso, "text"));		
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else {
			return 0;
		}	
	}
}
?>