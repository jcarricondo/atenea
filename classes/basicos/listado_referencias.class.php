<?php
class listadoReferencias extends MySQL {
	
	// Variables de la clase
	var $referencia = "";
	var $proveedor = "";
	var $ref_prov_pieza = "";
	var $precio_pack = "";
	var $fabricante = "";
	var $ref_fab_pieza = "";
	var $tipo_pieza = "";
	var $part_value_name = "";
	var $unidades_paquete = "";
	var $nombre_pieza = "";
	var $part_value_qty = "";
	var $nombre_proveedor = "";
	var $busqueda_magica = "";
	var $ordenar_referencias = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $id_referencia = "";
	var $paginacion = "";

	var $consultaSql = "";
	var $referencias = NULL;
	

	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($referencia,$proveedor,$ref_prov_pieza,$precio_pack,$fabricante,$ref_fab_pieza,$tipo_pieza,$part_value_name,$unidades_paquete,$nombre_pieza,$part_value_qty,$busqueda_magica,$ordenar_referencias,$fecha_desde,$fecha_hasta,$id_referencia,$paginacion) {
		$this->referencia = $referencia;
		$this->proveedor = $proveedor;
		$this->ref_prov_pieza = $ref_prov_pieza;
		$this->precio_pack = $precio_pack;
		$this->fabricante = $fabricante;
		$this->ref_fab_pieza = $ref_fab_pieza;
		$this->tipo_pieza = $tipo_pieza;
		$this->part_value_name = $part_value_name;
		$this->unidades_paquete = $unidades_paquete;
		$this->nombre_pieza = $nombre_pieza;
		$this->part_value_qty = $part_value_qty;
		$this->busqueda_magica = $busqueda_magica;
		$this->ordenar_referencias = $ordenar_referencias;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->id_referencia = $id_referencia;
		$this->paginacion = $paginacion;
		
		$this->prepararConsulta();
	}
	
	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$campos = "select referencias.id_referencia from referencias inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) inner join fabricantes on (fabricantes.id_fabricante=referencias.id_fabricante) where referencias.id_referencia is not null and referencias.activo=1 ";
			
		if($this->busqueda_magica != ""){
			$condiciones .= "and (referencias.referencia like '%".$this->busqueda_magica."%' and referencias.activo=1) or (proveedores.nombre_prov like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_proveedor_referencia like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.pack_precio like '%".$this->busqueda_magica."%' and referencias.activo=1) or (fabricantes.nombre_fab like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_fabricante_referencia like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_tipo like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_nombre like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_nombre_2 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_nombre_3 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_nombre_4 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_nombre_5 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.unidades like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_nombre like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_cantidad like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_cantidad_2 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_cantidad_3 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_cantidad_4 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.part_valor_cantidad_5 like '%".$this->busqueda_magica."%' and referencias.activo=1) or (referencias.id_referencia like '%".$this->busqueda_magica."%' and referencias.activo=1) ";
		}
		else {
		
			if($this->referencia != "") {
				$condiciones .= " and referencias.referencia like '%".$this->referencia."%'";
			}
			if($this->proveedor != "") {
				$condiciones .= " and proveedores.nombre_prov like '%".$this->proveedor."%'";
			}
			if($this->ref_prov_pieza != "") {
				$condiciones .= " and referencias.part_proveedor_referencia like '%".$this->ref_prov_pieza."%'";
			}
			if($this->fabricante != "") {
				$condiciones .= " and fabricantes.nombre_fab like '%".$this->fabricante."%'";
			}
			if($this->ref_fab_pieza != "") {
				$condiciones .= " and referencias.part_fabricante_referencia like '%".$this->ref_fab_pieza."%'";
			}
			if($this->precio_pack != "") {
				$condiciones .= " and referencias.pack_precio=".$this->precio_pack." ";
			}
			if($this->tipo_pieza != "") {
				$condiciones .= " and referencias.part_tipo like '%".$this->tipo_pieza."%'";
			}
			if($this->part_value_name != "") {
				$condiciones .= " and (referencias.part_valor_nombre like '%".$this->part_value_name."%' or referencias.part_valor_nombre_2 like '%".$this->part_value_name."%' or referencias.part_valor_nombre_3 like '%".$this->part_value_name."%' or referencias.part_valor_nombre_4 like '%".$this->part_value_name."%' or referencias.part_valor_nombre_5 like '%".$this->part_value_name."%')" ;
			}
			if($this->unidades_paquete != ""){
				$condiciones .= " and referencias.unidades=".$this->unidades_paquete." ";	
			}
			if($this->nombre_pieza != ""){
				$condiciones .= " and referencias.part_nombre like '%".$this->nombre_pieza."%'";	
			}
			if($this->part_value_qty != ""){
				$condiciones .= " and (referencias.part_valor_cantidad like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_2 like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_3 like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_4 like '%".$this->part_value_qty."%' or referencias.part_valor_cantidad_5 like '%".$this->part_value_qty."%')";	
			}
			if($this->fecha_desde != ""){
				$condiciones .= " and referencias.fecha_creado >= '".$this->fecha_desde."' ";	
			}
			if($this->fecha_hasta != ""){
				$condiciones .= "and referencias.fecha_creado <= '".$this->fecha_hasta."' ";	
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

		$this->consultaSql = $campos.$condiciones.$ordenado.$this->paginacion;
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias = $this->getResultados();
	}

}
?>