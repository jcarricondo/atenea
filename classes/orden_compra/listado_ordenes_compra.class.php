<?php
class listadoOrdenesCompra extends MySQL {
	
	// Variables de la clase
	var $proveedor = "";
	var $fecha_pedido = "";
	var $dir_entrega = "";
	var $orden_produccion = "";
	var $fecha_requerida = "";
	var $estado = "";
	var $n_pedido = "";
	var $fecha_desde = "";
	var $fecha_hasta = "";
	var $fecha_entrega = "";
	var $estado_op = "";
	var $id_sede = "";
	var $paginacion = "";
		
	var $consultaSql = "";
	var $ordenes_compra = NULL;
			
	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($proveedor,$fecha_pedido,$dir_entrega,$orden_produccion,$fecha_requerida,$estado,$n_pedido,$fecha_desde,$fecha_hasta,$fecha_entrega,$estado_op,$id_sede,$paginacion) {
		$this->proveedor = $proveedor;
		$this->fecha_pedido = $fecha_pedido;
		$this->dir_entrega = $dir_entrega;
		$this->orden_produccion = $orden_produccion;
		$this->fecha_requerida = $fecha_requerida;
		$this->estado = $estado;
		$this->n_pedido = $n_pedido;
		$this->fecha_desde = $fecha_desde;
		$this->fecha_hasta = $fecha_hasta;
		$this->fecha_entrega = $fecha_entrega;
		$this->estado_op = $estado_op;
		$this->id_sede = $id_sede;
		$this->paginacion = $paginacion;
				
		$this->prepararConsulta();
	}

	// Se realiza la búsqueda de ordenes de compra por orden de producción
	function serValoresBusquedaProvOP($id_produccion,$proveedor,$id_sede) {
		$this->orden_produccion = $id_produccion;
		$this->proveedor = $proveedor;
		$campos = "select orden_compra.*,proveedores.nombre_prov from orden_compra inner join proveedores on (proveedores.id_proveedor=orden_compra.id_proveedor) inner join orden_produccion on (orden_produccion.id_produccion=orden_compra.id_produccion) where orden_compra.id_orden_compra is not null and orden_compra.activo=1";

		if($this->orden_produccion != "") {
			if($this->orden_produccion[0] != "") {
				$condiciones = " and (";
				for($i=0;$i<count($this->orden_produccion);$i++){
					// Si llegamos al ultimo
					if($i == count($this->orden_produccion)-1){
						$condiciones .= " orden_compra.id_produccion=".$this->orden_produccion[$i].")";
					}
					else{
						$condiciones .= " orden_compra.id_produccion=".$this->orden_produccion[$i]. " or ";	
					}
				}
			}
            else {
                // Cargamos solo las op iniciadas de la sede
                $condiciones = " and orden_compra.id_produccion in (select id_produccion from orden_produccion where activo=1 and estado='INICIADO' and id_sede=".$id_sede.") ";
            }
		}

		if($this->proveedor != "") {
			$condiciones .= " and proveedores.nombre_prov like '%".$this->proveedor."%'";
		}
		
		$ordenado = " order by orden_compra.id_produccion desc, proveedores.nombre_prov "; 
		
		$this->consultaSql = $campos.$condiciones.$ordenado;
	}

	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		if($this->id_sede != 0){
			$campos = "select orden_compra.*,proveedores.nombre_prov from orden_compra inner join proveedores on (proveedores.id_proveedor=orden_compra.id_proveedor) inner join orden_produccion on (orden_produccion.id_produccion=orden_compra.id_produccion) where orden_compra.id_orden_compra is not null and orden_compra.activo=1 and orden_produccion.activo=1 
							and orden_produccion.id_sede=".$this->id_sede." ";
		}
		else {
			$campos = "select orden_compra.*,proveedores.nombre_prov from orden_compra inner join proveedores on (proveedores.id_proveedor=orden_compra.id_proveedor) inner join orden_produccion on (orden_produccion.id_produccion=orden_compra.id_produccion) where orden_compra.id_orden_compra is not null and orden_compra.activo=1 and orden_produccion.activo=1";	
		}	
		// Comprobamos que se ha seleccionado alguna proveedor
		if($this->proveedor != "") {
			//$condiciones .= " and proveedores.nombre_prov like '%".$this->proveedor."%'";
			if (!(count($this->proveedor == 1) and $this->proveedor[0] == "")){
				// Dependiendo de los proveedores seleccionados modificaremos la consulta
				$condiciones .= " and (";	
				for ($i=0;$i<count($this->proveedor);$i++){	
					// Si llegamos al ultimo
					if ($i == count($this->proveedor)-1){
						$condiciones .= " proveedores.nombre_prov like '%".$this->proveedor[$i]."%')";	
					}
					else{
						$condiciones .= " proveedores.nombre_prov like '%".$this->proveedor[$i]."%' or ";	
					}	
				}
			}		
		}
		// Comprobamos que se ha seleccionado alguna OP
		if($this->orden_produccion != "") {
			if (!(count($this->orden_produccion == 1) and $this->orden_produccion[0] == "")){
				// Dependiendo de las ordenes de produccion seleccionadas modificaremos la consulta
				$condiciones .= " and (";
				for ($i=0;$i<count($this->orden_produccion);$i++){
					// Si llegamos al ultimo
					if ($i == count($this->orden_produccion)-1){
						$condiciones .= " orden_compra.id_produccion=".$this->orden_produccion[$i].")";	
					}
					else{
						$condiciones .= " orden_compra.id_produccion=".$this->orden_produccion[$i]. " or ";	
					}
				}
			}
		}
		if($this->fecha_pedido != "") {
			$condiciones .= " and fecha_pedido like '%".$this->fecha_pedido."%'";
		}
		if($this->dir_entrega != "") {
			$condiciones .= " and direccion_entrega like '%".$this->dir_entrega."%'";
		}
		if($this->estado != "") {
			$condiciones .= " and orden_compra.estado like '%".$this->estado."%'";
		}
		if($this->n_pedido != "") {
			$condiciones .= " and numero_pedido='".$this->n_pedido."'";
		}
		if($this->fecha_desde != "") {
			$condiciones .= " and orden_compra.fecha_creado >= '".$this->fecha_desde."' ";
		}
		if($this->fecha_hasta != ""){
			$condiciones .= " and orden_compra.fecha_creado <= '".$this->fecha_hasta."' ";	
		}
		if($this->fecha_entrega != ""){
			$condiciones .= " and orden_compra.fecha_entrega = '".$this->fecha_entrega."' ";	
		}
		if($this->estado_op != ""){
			$condiciones .= " and orden_produccion.estado = '".$this->estado_op.="' and orden_produccion.activo=1 ";	
		}
		$ordenado = " order by orden_compra.id_produccion desc, proveedores.nombre_prov "; 

		$this->consultaSql = $campos.$condiciones.$ordenado.$this->paginacion; 
	}
	
	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->ordenes_compra = $this->getResultados();
	}

	// Función que comprueba si la fecha introducida está en formato correcto
	function validarFecha ($fecha) {
 		$fecha = explode("/",$fecha);
 		if(sizeof($fecha) != 3) return false;
 		if(checkdate($fecha[1],$fecha[0],$fecha[2])) return true;
 		else return false;
	}
	
	// Convierte la fecha del listado a fecha MySQL
	function cFechaMy ($fecha) {
 		$f = explode("/",$fecha);
 		return $f[2]."-".$f[1]."-".$f[0];
	}
	
	// Convierte la fecha MySQL a formato listado
	function cFechaNormal($fecha) {
 		$f = explode("-",$fecha);
 		if(count($f) > 1) {
  			return $f[2]."/".$f[1]."/".$f[0];
 		} else {
  			return $fecha;
		 }
	}
	
	function prepararOP($id_sede) {
		if($id_sede == 0){
			// ADMIN GLOBAL
			$campos = "select orden_produccion.id_produccion from orden_produccion where orden_produccion.id_produccion is not null and activo=1 order by id_produccion asc";
		}
		else {	
			$campos = sprintf("select orden_produccion.id_produccion from orden_produccion where orden_produccion.id_produccion is not null and id_sede=%s and activo=1 order by id_produccion asc",
			$this->makeValue($id_sede,"int"));
		}
		$this->consultaSql = $campos;
	}

	function prepararOPIniciadas($id_sede) {
		if($id_sede == 0){
			$campos = "select orden_produccion.id_produccion from orden_produccion where orden_produccion.id_produccion is not null and estado='INICIADO' and activo=1 order by id_produccion asc";
		}
		else {
			$campos = sprintf("select orden_produccion.id_produccion from orden_produccion where orden_produccion.id_produccion is not null and estado='INICIADO' and id_sede=%s and activo=1 order by id_produccion asc",
				$this->makeValue($id_sede, "int"));
		}
			
		$this->consultaSql = $campos;
	}	

	function prepararProveedorOP($proveedor) {
		$campos = "select orden_produccion.id_produccion from orden_produccion inner join orden_compra on (orden_compra.id_produccion=orden_produccion.id_produccion) inner join proveedores on (proveedores.id_proveedor=orden_compra.id_proveedor) where orden_produccion.id_produccion is not null and orden_produccion.activo=1 and proveedores.nombre_prov='".$proveedor."' group by orden_produccion.id_produccion order by orden_produccion.fecha_creado desc";
		$this->consultaSql = $campos;
	}
	
	function realizarConsultaOP() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->orden_produccion = $this->getResultados();
	}
	
	function prepararFechaRequerida() {
		$campos = "select distinct orden_compra.fecha_requerida from orden_compra where orden_compra.fecha_requerida is not null and orden_compra.activo=1 ";
		$ordenado = "order by orden_compra.fecha_requerida";
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaFechaRequerida() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->fecha_requerida = $this->getResultados();
	}
	
	function prepararFechaPedido() {
		$campos = "select distinct orden_compra.fecha_pedido from orden_compra where orden_compra.fecha_pedido is not null and orden_compra.activo=1 ";
		$ordenado = "order by orden_compra.fecha_pedido"; 
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaFechaPedido() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->fecha_pedido = $this->getResultados();
	}
	
	function prepararDirEntrega() {
		$campos = "select distinct orden_compra.direccion_entrega from orden_compra where orden_compra.direccion_entrega is not null and orden_compra.activo=1 ";
		$ordenado = "order by orden_compra.direccion_entrega";
		$this->consultaSql = $campos.$ordenado;
	}
	
	function realizarConsultaDirEntrega() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->dir_entrega = $this->getResultados();
	}
}
?>