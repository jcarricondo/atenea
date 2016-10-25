<?php
class listadoStockInformatico extends MySQL {

	// Variables de la clase
	var $id_tipo;
	var $id_subtipo;
	var $unidades;
	var $min_unidades;
	var $unidades_pedido;
    var $paginacion;

	var $consultaSql = "";
	var $materiales_informaticos = NULL;

	// Se Pasan los valores de las variables del buscador a las de la clase
	function setValores($id_tipo,$id_subtipo,$unidades,$min_unidades,$unidades_pedido,$paginacion){
		$this->id_tipo = $id_tipo;
		$this->id_subtipo = $id_subtipo;
		$this->unidades = $unidades;
		$this->min_unidades = $min_unidades;
		$this->unidades_pedido = $unidades_pedido;
        $this->paginacion = $paginacion;

		$this->prepararConsulta();
	}

	// Prepara la consulta a la base de datos
	function prepararConsulta() {
		$consulta = "select mi.id_material, count(id_material) as unidades_stock from material_informatico as mi
                        inner join material_informatico_subtipo as mis on (mi.id_subtipo = mis.id_subtipo)
						where mi.id_material is not null and mi.activo=1 and mi.estado='STOCK'";

		if($this->id_tipo != ""){
			$consulta .= " and mi.id_tipo=".$this->id_tipo;
		}
		if($this->id_subtipo != ""){
			$consulta .= " and mi.id_subtipo=".$this->id_subtipo;
		}
		if($this->unidades != ""){
			$having .= " having unidades_stock=".$this->unidades;
		}
		if($this->min_unidades != ""){
			$consulta .= " and mis.min_unidades=".$this->min_unidades;
		}
		if($this->unidades_pedido != ""){
            // if(empty($having)) $having = " having unidades_stock-material_informatico_subtipo.min_unidades=".$this->unidades_pedido;
            // else $having .= " and unidades_stock-material_informatico_subtipo.min_unidades=".$this->unidades_pedido;
		}
		
		$agrupado = " group by mi.id_tipo, mi.id_subtipo ";

		$ordenado = " order by mi.id_tipo ";		

		$this->consultaSql = $consulta.$agrupado.$having.$ordenado.$this->paginacion;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->materiales_informaticos = $this->getResultados();
	}
}
?>