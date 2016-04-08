<?php
// Fichero con las funciones de comprobación para AJAX del módulo usuarios
include("../../classes/mysql.class.php");
include("../../classes/taller/taller.class.php");
include("../../classes/control_usuario.class.php");

$db = new MySQL();
$taller = new Taller();
$control_usuario = new Control_Usuario();

if(isset($_GET["func"])){
	switch($_GET["func"]){
		case "obtenerTaller":
			$es_taller = $_GET["taller"];

			if($es_taller == 1) {
				$capa_opcion = '<div class="LabelCreacionBasico">Taller *</div>';
				$select_opcion .= '<select id="talleres" name="talleres" class="CreacionBasicoInput">';
				$res_talleres = $taller->dameTalleres();
				for($i=0;$i<count($res_talleres);$i++){
					$id_taller = $res_talleres[$i]["id_taller"];
					$nombre = $res_talleres[$i]["taller"];
					$select_opcion .= '<option value="'.$id_taller.'">'.$nombre.'</option>';
				}
				$select_opcion .= '</select>';	
			}
			else{
				$capa_opcion = '';
				$select_opcion .= '<input type="hidden" id="talleres" name="talleres" value="0" />';
			}
			echo $capa_opcion.$select_opcion;
		break;	
		case "cargaTaller":
			$es_taller = $_GET["taller"];

			if($es_taller == 1) {
				$capa_opcion = '<div class="Label">Taller *</div>';
				$select_opcion .= '<select id="talleres" name="talleres" class="CreacionBasicoInput" ><option value=""></option>';
				$res_talleres = $taller->dameTalleres();
				for($i=0;$i<count($res_talleres);$i++){
					$id_taller = $res_talleres[$i]["id_taller"];
					$nombre = $res_talleres[$i]["taller"];
					$select_opcion .= '<option value="'.$id_taller.'">'.$nombre.'</option>';
				}
				$select_opcion .= '</select>';	
			}
			else{
				$capa_opcion = '';
				$select_opcion .= '<input type="hidden" id="talleres" name="talleres" value="" />';
			}
			echo $capa_opcion.$select_opcion;
		break;
	}
}
?>