<?php
class listadoReferenciasAlmacen extends MySQL {

	// Variables de la clase
	var $busqueda_magica = "";
	var $orden_produccion;
	var $orden_compra;
	var $proveedor;
	var $paginacion;
	var $id_ref;
	var $id_almacen;
    var $id_sede;

	var $consultaSql = "";
	var $referencias = NULL;


	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($busqueda_magica,$orden_produccion,$orden_compra,$proveedor,$id_ref,$paginacion,$id_almacen,$id_sede) {
		$this->busqueda_magica = $busqueda_magica;
		$this->orden_produccion = $orden_produccion;
		$this->orden_compra = $orden_compra;
		$this->proveedor = $proveedor;
		$this->paginacion = $paginacion;
		$this->id_ref = $id_ref;
		$this->id_almacen = $id_almacen;
        $this->id_sede = $id_sede;
		
		$this->prepararConsulta();
	}

	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		if($this->busqueda_magica != ""){
			$condiciones =	" and ((referencias.referencia like '%".$this->busqueda_magica."%' and referencias.activo=1)
				or (proveedores.nombre_prov like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_proveedor_referencia like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.pack_precio like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (fabricantes.nombre_fab like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_fabricante_referencia like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_tipo like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_nombre like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_nombre_2 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_nombre_3 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_nombre_4 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_nombre_5 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.unidades like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_nombre like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_cantidad like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_cantidad_2 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_cantidad_3 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_cantidad_4 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.part_valor_cantidad_5 like '%".$this->busqueda_magica."%' and referencias.activo=1) 
				or (referencias.id_referencia like '%".$this->busqueda_magica."%' and referencias.activo=1)) ";
		}
		if($this->id_ref != "") {
			$condiciones .= " and referencias.id_referencia=".$this->id_ref;
		}
		if($this->proveedor != "") {
			$condiciones .= " and proveedores.nombre_prov='".$this->proveedor."'";
		}

		if($this->orden_produccion != NULL and $this->orden_produccion[0] != ""){
			// Si sólo se seleccionó STOCK
			if(count($this->orden_produccion) == 1 and $this->orden_produccion[0] == 0){
                if($this->id_almacen != "") {
                    $condiciones .= " and referencias.id_referencia in (select distinct id_referencia from stock_almacenes where piezas <> 0 and id_almacen=" . $this->id_almacen . ")";
                }
                else {
                    $condiciones .= " and referencias.id_referencia in (select distinct id_referencia from stock_almacenes where piezas <> 0 and id_almacen in
                                        (select id_almacen from almacenes where activo=1 and id_sede=".$this->id_sede."))";
                }
			}
			else{
				$tablas = " inner join orden_compra_referencias as ocr on (referencias.id_referencia=ocr.id_referencia)
							inner join orden_compra as oc on (oc.id_orden_compra=ocr.id_orden)
							inner join orden_produccion as op on (op.id_produccion=oc.id_produccion) ";	

				// Dependiendo de las órdenes de producción seleccionadas modificaremos la consulta
				$condiciones .= " and (";
				for($i=0;$i<count($this->orden_produccion);$i++){
					// Si llegamos al último
					if($i == count($this->orden_produccion)-1){
						if($this->orden_produccion[$i] != 0){
							$condiciones .= " op.id_produccion=".$this->orden_produccion[$i].")";	
						}
						else{
                            if($this->id_almacen != "") {
                                $condiciones .= " referencias.id_referencia in (select distinct id_referencia from stock_almacenes where piezas <> 0 and id_almacen=" . $this->id_almacen . "))";
                            }
                            else {
                                $condiciones .= " referencias.id_referencia in (select distinct id_referencia from stock_almacenes where piezas <> 0 and id_almacen in
                                        (select id_almacen from almacenes where activo=1 and id_sede=".$this->id_sede.")))";
                            }
						}
					}
					else{
						$condiciones .= " op.id_produccion=".$this->orden_produccion[$i]. " or ";	
					}
				}
			}	
		}
		if($this->orden_compra != ""){
			if($this->orden_produccion == NULL or $this->orden_produccion[0] == "" or (count($this->orden_produccion) == 1 and $this->orden_produccion[0] == 0)){
				$tablas .= " inner join orden_compra_referencias as ocr on (referencias.id_referencia=ocr.id_referencia)
								inner join orden_compra as oc on (oc.id_orden_compra=ocr.id_orden) ";
			}
			$condiciones .= " and oc.numero_pedido like '%".$this->orden_compra."%'";
		}
			
		$consulta = "select referencias.id_referencia from referencias".$tablas." 
						inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) 
						inner join fabricantes on (fabricantes.id_fabricante=referencias.id_fabricante) 
							where referencias.id_referencia is not null and referencias.activo=1".$condiciones;
								
		$agrupado = " group by referencias.id_referencia ";
		$ordenado = "order by referencias.id_referencia";							

		$this->consultaSql = $consulta.$agrupado.$ordenado.$this->paginacion;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->referencias = $this->getResultados();
	}
}
?>