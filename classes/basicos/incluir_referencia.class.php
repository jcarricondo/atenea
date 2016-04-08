<?php
class Referencia_Componente extends MySQL {
	
	var $id_referencia;
	var $referencia;
	var $proveedor;
	var $nombre_pieza;
	var $tipo_pieza;
	var $ref_proveedor;
	var $cantidad;
	var $pack_precio;
	var $id_proveedor;

	function cargarDatos($id_referencia,$referencia,$proveedor,$nombre_pieza,$tipo_pieza,$ref_proveedor,$cantidad,$pack_precio,$id_proveedor) {
		$this->id_referencia = $id_referencia;
		$this->referencia = $referencia;
		$this->proveedor = $proveedor;
		$this->nombre_pieza = $nombre_pieza;
		$this->tipo_pieza = $tipo_pieza;
		$this->ref_proveedor = $ref_proveedor;
		$this->cantidad = $cantidad;
		$this->pack_precio = $pack_precio;
		$this->id_proveedor = $id_proveedor;
	}
	
	function cargaDatosReferenciaComponenteId($id_referencia) {
		$consultaSql = sprintf ("select referencias.id_referencia,referencias.referencia,referencias.part_nombre,referencias.part_tipo,referencias.part_proveedor_referencia,referencias.unidades,referencias.pack_precio,proveedores.nombre_prov,referencias.id_proveedor from referencias inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) where referencias.id_referencia=%s",
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_referencia"],
			$resultados["referencia"],
			$resultados["nombre_prov"],
			$resultados["part_nombre"],
			$resultados["part_tipo"],
			$resultados["part_proveedor_referencia"],
			$resultados["unidades"],
			$resultados["pack_precio"],
			$resultados["id_proveedor"]
		);
	}

	// Funcion que vincula enlace a pagina del proveedor de la referencia
	function vincularReferenciaProveedor(){
		$max_caracteres = 50;
		if(strlen($this->ref_proveedor) > $max_caracteres){
			if($this->id_proveedor == 1){
				echo '<a style="text_decoration:none;" href="http://es.rs-online.com/web/c/?searchTerm='.$this->ref_proveedor.'" target="_blank">'.substr($this->ref_proveedor,0,50).'...'.'/a>';
			}
			elseif($this->id_proveedor == 2){
				echo '<a style="text_decoration:none;" href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st='.$this->ref_proveedor.'" target="_blank">'.substr($this->ref_proveedor,0,50).'...'.'</a>';
			}	
			else {
				echo substr($this->ref_proveedor,0,50).'...';	
			}
		}
		else {
			if($this->id_proveedor == 1){
				echo '<a style="text_decoration:none;" href="http://es.rs-online.com/web/c/?searchTerm='.$this->ref_proveedor.'" target="_blank">'.$this->ref_proveedor.'</a>';
			}
			elseif($this->id_proveedor == 2){
				echo '<a style="text_decoration:none;" href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st='.$this->ref_proveedor.'" target="_blank">'.$this->ref_proveedor.'</a>';
			}
			else {
				echo $this->ref_proveedor;
			}
		}
	}
	
}
?>
