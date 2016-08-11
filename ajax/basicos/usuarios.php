<?php
// Fichero con las funciones de comprobación para AJAX del módulo usuarios
include("../../classes/mysql.class.php");
include("../../classes/almacen/almacen.class.php");
include("../../classes/control_usuario.class.php");

$db = new MySQL();
$almacen = new Almacen();
$control_usuario = new Control_Usuario();

if(isset($_GET["func"])){
	switch($_GET["func"]){
		case "obtenerAlmacen":
			$es_almacen = $_GET["almacen"];
            $tipo_almacen = $_GET["tipo_almacen"];

			if($es_almacen == 1) {
				$capa_opcion = '<div class="LabelCreacionBasico">Almacen *</div>';
				$select_opcion .= '<select id="almacenes" name="almacenes" class="CreacionBasicoInput">';
                if($tipo_almacen == 1) $res_almacenes = $almacen->dameAlmacenesFabrica();
                if($tipo_almacen == 2) $res_almacenes = $almacen->dameAlmacenesMantenimiento();
				for($i=0;$i<count($res_almacenes);$i++){
					$id_almacen = $res_almacenes[$i]["id_almacen"];
					$nombre = $res_almacenes[$i]["almacen"];
					$select_opcion .= '<option value="'.$id_almacen.'">'.$nombre.'</option>';
				}
				$select_opcion .= '</select>';	
			}
			else{
				$capa_opcion = '';
				$select_opcion .= '<input type="hidden" id="almacenes" name="almacenes" value="0" />';
			}
			echo $capa_opcion.$select_opcion;
		break;	
		case "cargaAlmacen":
			$es_almacen = $_GET["almacen"];
			$tipo_almacen = $_GET["tipo_almacen"];

			if($es_almacen == 1) {
				$capa_opcion = '<div class="Label">Almacen *</div>';
				$select_opcion .= '<select id="almacenes" name="almacenes" class="CreacionBasicoInput" ><option value=""></option>';
				if($tipo_almacen == 1) $res_almacenes = $almacen->dameAlmacenesFabrica();
                if($tipo_almacen == 2) $res_almacenes = $almacen->dameAlmacenesMantenimiento();
				for($i=0;$i<count($res_almacenes);$i++){
					$id_almacen = $res_almacenes[$i]["id_almacen"];
					$nombre = $res_almacenes[$i]["almacen"];
					$select_opcion .= '<option value="'.$id_almacen.'">'.$nombre.'</option>';
				}
				$select_opcion .= '</select>';	
			}
			else{
				$capa_opcion = '';
				$select_opcion .= '<input type="hidden" id="almacenes" name="almacenes" value="" />';
			}
			echo $capa_opcion.$select_opcion;
		break;
	}
}
?>