<?php
class listadoPerifericosAlmacen extends MySQL {

	// Variables de la clase
	var $num_serie;
	var $tipo_periferico;
	var $estado;
	var $paginacion;
	var $ajuste;
	var $id_almacen;
    var $id_sede;
	
	var $consultaSql = "";
	var $perifericos = NULL;


	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($num_serie,$tipo_periferico,$estado,$paginacion,$ajuste,$id_almacen,$id_sede) {
		$this->num_serie = $num_serie;
		$this->tipo_periferico = $tipo_periferico;
		$this->estado = $estado;
		$this->paginacion = $paginacion;
		$this->ajuste = $ajuste;
		$this->id_almacen = $id_almacen;
        $this->id_sede = $id_sede;

		$this->prepararConsulta();
	}

	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$consulta = "select id_periferico from perifericos where perifericos.id_periferico is not null and perifericos.activo=1";

		if($this->num_serie != ""){
			$consulta .= " and numero_serie='".$this->num_serie."'";
		}
		if($this->tipo_periferico != ""){
			$consulta .= " and tipo_periferico=".$this->tipo_periferico;
		}
		if($this->id_almacen != ""){
			$consulta .= " and id_almacen=".$this->id_almacen;
		}
        else {
            $consulta .= " and id_almacen in (select id_almacen from almacenes where id_sede=".$this->id_sede.")";
        }
		if($this->ajuste == 0){
			if($this->estado != ""){
				$consulta .= " and id_periferico in 
								(select id_periferico from perifericos_estados where estado='".$this->estado."' and activo=1)";
			}
		}
		else {
			if($this->estado != ""){
				$consulta .= " and id_periferico in 
								(select id_periferico from perifericos_estados where estado='".$this->estado."' and activo=1)";
			}
			else{
				$consulta .= " and id_periferico in 
								(select id_periferico from perifericos_estados where activo=1 and (estado='AVERIADO' or estado='EN REPARACION'))";		
			}	
		}

        $utilizado_alguna_vez = " and id_periferico in (select id_periferico from perifericos_estados group by id_periferico having count(id_periferico) > 1)";

		$ordenado = " order by perifericos.fecha_creado DESC";							

		$this->consultaSql = $consulta.$utilizado_alguna_vez.$ordenado.$this->paginacion;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->perifericos = $this->getResultados();
	}
}
?>