<?php
class listadoSimuladorAlmacen extends MySQL {

	// Variables de la clase
	var $num_serie;
	var $estado;
	var $paginacion;
	var $ajuste;
	var $id_almacen;
    var $id_sede;
	
	var $consultaSql = "";
	var $simuladores = NULL;


	// Pasan los valores de las variables del buscador a las de la clase
	function setValores($num_serie,$estado,$paginacion,$ajuste,$id_almacen,$id_sede) {
		$this->num_serie = $num_serie;
		$this->estado = $estado;
		$this->paginacion = $paginacion;
		$this->ajuste = $ajuste;
		$this->id_almacen = $id_almacen;
        $this->id_sede = $id_sede;

		$this->prepararConsulta();
	}

	// Prepara la cadena para la consulta a la base de datos
	function prepararConsulta() {
		$consulta = "select id_simulador from simuladores where simuladores.id_simulador is not null and simuladores.activo=1";

		if($this->num_serie != ""){
			$consulta .= " and numero_serie='".$this->num_serie."'";
		}
		if($this->id_almacen != ""){
			$consulta .= " and id_almacen=".$this->id_almacen;
		}
        else {
            if($_SESSION["AT_id_tipo_usuario"] == 1){
                // Usuario Administrador Simumak
                $consulta .= " and id_almacen in (select id_almacen from almacenes where activo=1 and id_sede=".$this->id_sede.")";
            }
            else {
                $consulta .= " and id_almacen in (select id_almacen from almacenes where activo=1 and id_sede=" . $_SESSION["AT_id_sede"] . ")";
            }
        }
		if($this->ajuste == 0){
			if($this->estado != ""){
				$consulta .= " and id_simulador in 
								(select id_simulador from simuladores_estados where estado='".$this->estado."' and activo=1)";
			}
		}
		else {
			if($this->estado != ""){
				$consulta .= " and id_simulador in 
								(select id_simulador from simuladores_estados where estado='".$this->estado."' and activo=1)";
			}
			else{
				$consulta .= " and id_simulador in 
								(select id_simulador from simuladores_estados where activo=1 and (estado='AVERIADO' or estado='EN REPARACION'))";		
			}	
		}	

		$utilizado_alguna_vez = " and id_simulador in (select id_simulador from simuladores_estados group by id_simulador having count(id_simulador) > 1)";

		$ordenado = " order by simuladores.fecha_creado DESC";							

		$this->consultaSql = $consulta.$utilizado_alguna_vez.$ordenado.$this->paginacion;
	}

	// Realiza la consulta a la base de datos con las opciones de búsqueda indicadas
	function realizarConsulta() {
		$this->setConsulta($this->consultaSql);
		$this->ejecutarConsulta();
		$this->simuladores = $this->getResultados();
	}
}
?>