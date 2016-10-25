<?php
class listadoIncluirReferenciaLibre extends MySQL {
	
	// Variables de la clase
	var $referencia = "";
	var $cantidad = "";
	var $proveedor = "";
	var $fabricante = "";
	var $ref_proveedor = "";
	var $ref_fabricante = "";
	var $nombre_pieza = "";
	var $tipo_pieza = "";
	var $part_value_name = "";
	var $part_value_qty = "";
	var $precio_pack = "";
	var $busqueda_magica = "";
	var $ordenar_referencias = "";
	var $id_referencia = "";
		
	var $consultaSql = "";
	var $referencias = NULL;
	
	var $id = '';
	
	
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($referencia,$cantidad,$proveedor,$fabricante,$ref_proveedor,$ref_fabricante,$nombre_pieza,$tipo_pieza,$part_value_name,$part_value_qty,$precio_pack,$busqueda_magica,$ordenar_referencias,$id_referencia) {
		$this->referencia = $referencia;
		$this->cantidad = $cantidad;
		$this->proveedor = $proveedor;
		$this->fabricante = $fabricante;
		$this->ref_proveedor = $ref_proveedor;
		$this->ref_fabricante = $ref_fabricante;
		$this->nombre_pieza = $nombre_pieza;
		$this->tipo_pieza = $tipo_pieza;
		$this->part_value_name = $part_value_name;
		$this->part_value_qty = $part_value_qty;
		$this->precio_pack = $precio_pack;
		$this->busqueda_magica = $busqueda_magica;
		$this->ordenar_referencias = $ordenar_referencias;
		$this->id_referencia = $id_referencia;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select referencias.id_referencia from referencias inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) inner join fabricantes on (fabricantes.id_fabricante=referencias.id_fabricante) where referencias.id_referencia is not null and referencias.activo=1 "; 
		
		if($this->busqueda_magica != "") {
			$condiciones .= "and referencias.referencia like '%".$this->busqueda_magica."%' or proveedores.nombre_prov like '%".$this->busqueda_magica."%' or referencias.part_proveedor_referencia like '%".$this->busqueda_magica."%' or referencias.pack_precio like '%".$this->busqueda_magica."%' or fabricantes.nombre_fab like '%".$this->busqueda_magica."%' or referencias.part_fabricante_referencia like '%".$this->busqueda_magica."%' or referencias.part_tipo like '%".$this->busqueda_magica."%' or referencias.part_valor_nombre like '%".$this->busqueda_magica."%' or referencias.part_valor_nombre_2 like '%".$this->busqueda_magica."%' or referencias.part_valor_nombre_3 like '%".$this->busqueda_magica."%' or referencias.part_valor_nombre_4 like '%".$this->busqueda_magica."%' or referencias.part_valor_nombre_5 like '%".$this->busqueda_magica."%' or referencias.unidades like '%".$this->busqueda_magica."%' or referencias.part_nombre like '%".$this->busqueda_magica."%' or referencias.part_valor_cantidad like '%".$this->busqueda_magica."%' or referencias.part_valor_cantidad_2 like '%".$this->busqueda_magica."%' or referencias.part_valor_cantidad_3 like '%".$this->busqueda_magica."%' or referencias.part_valor_cantidad_4 like '%".$this->busqueda_magica."%' or referencias.part_valor_cantidad_5 like '%".$this->busqueda_magica."%' or (referencias.id_referencia like '%".$this->busqueda_magica."%' and referencias.activo=1)  ";
		}
		else { 
			if($this->referencia != "") {
				$condiciones .= "and referencia like '%".$this->referencia."%'";
			}
			if($this->cantidad != "") {
				$condiciones .= "and unidades like '%".$this->cantidad."%'";
			}
			if($this->proveedor != "") {
				$condiciones .= "and nombre_prov like '%".$this->proveedor."%'";
			}
			if($this->fabricante != "") {
				$condiciones .= "and nombre_fab like '%".$this->fabricante."%'";
			}
			if($this->ref_proveedor != "") {
				$condiciones .= "and part_proveedor_referencia like '%".$this->ref_proveedor."%'";
			}
			if($this->ref_fabricante != "") {
				$condiciones .= "and part_fabricante_referencia like '%".$this->ref_fabricante."%'";
			}
			if($this->nombre_pieza != "") {
				$condiciones .= "and part_nombre like '%".$this->nombre_pieza."%'";
			}
			if($this->tipo_pieza != "") {
				$condiciones .= "and part_tipo like '%".$this->tipo_pieza."%'";
			}
			if($this->part_value_name != "") {
				$condiciones .= "and referencias.part_valor_nombre like '%".$this->part_value_name."%' or referencias.part_valor_nombre_2 like '%".$this->part_value_name."%' or referencias.part_valor_nombre_3 like '%".$this->part_value_name."%' or referencias.part_valor_nombre_4 like '%".$this->part_value_name."%' or referencias.part_valor_nombre_5 like '%".$this->part_value_name."%'" ;
			}
			if($this->part_value_qty != ""){
				$condiciones .= "and referencias.part_valor_cantidad like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_2 like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_3 like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_4 like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_5 like '%".$this->part_value_qty."%'";	
			}
			if($this->precio_pack != "") {
				$condiciones .= "and referencias.pack_precio like '%".$this->precio_pack."%'";
			}
			if($this->id_referencia != ""){
				$condiciones .= "and referencias.id_referencia=".$this->id_referencia." ";	
			}
		}

		if ($this->ordenar_referencias == 1) $ordenado = " order by referencias.pack_precio, referencias.referencia ";
		else if ($this->ordenar_referencias == 2) $ordenado = " order by proveedores.nombre_prov, referencias.referencia ";
		else if ($this->ordenar_referencias == 3) $ordenado = " order by fabricantes.nombre_fab, referencias.referencia ";
		else if ($this->ordenar_referencias == 4) $ordenado = " order by referencias.part_nombre, referencias.referencia ";
		else if ($this->ordenar_referencias == 5) $ordenado = " order by referencias.part_tipo, referencias.referencia ";
		else if ($this->ordenar_referencias == 6) $ordenado = " order by referencias.unidades, referencias.referencia ";
		else if ($this->ordenar_referencias == 7) $ordenado = " order by referencias.part_proveedor_referencia, referencias.referencia ";
		else if ($this->ordenar_referencias == 8) $ordenado = " order by referencias.part_fabricante_referencia, referencias.referencia ";
		else if ($this->ordenar_referencias == 9) $ordenado = " order by referencias.id_referencia, referencias.referencia ";					
		else $ordenado = " order by referencias.referencia ";
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}
	
			
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias = $this->getResultados();
	}
	
}
?>