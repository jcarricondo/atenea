<?php 
// Script parar convertir las cabinas existentes en perifericos
set_time_limit(10000);

$entorno_local = 'C:\xampp\htdocs\proyectos\git\atenea';

if(realpath($_SERVER["DOCUMENT_ROOT"]) == $entorno_local) {
	$dir_raiz = $_SERVER["DOCUMENT_ROOT"];
}
else {
	$dir_raiz = $_SERVER["DOCUMENT_ROOT"]."/atenea";
}

$dir_classes = $dir_raiz."/classes";

include($dir_classes."/mysql.class.php");
include($dir_classes."/basicos/componente.class.php");

$db = new MySQL();
$comp = new Componente();

// Todos los componentes con id_tipo=1 pasarlo a id_tipo=2
echo "-----------------------------------------------------------"; echo "<br/>";
echo "CONVERTIR CABINAS EN PERIFERICOS"; echo "<br/>";
echo "-----------------------------------------------------------"; echo "<br/>";


// Convertir todas las cabinas en perifericos
$consulta = "update componentes set id_tipo=2 where id_tipo=1";
$db->setConsulta($consulta);
if($db->ejecutarSoloConsulta()) {
	echo "<span style='color: green;'>SE HAN CONVERTIDO TODAS LAS CABINAS EN PERIFERICOS</span><br/>";
	// Asociar todos los ficheros de las cabinas a perifericos
	$consulta_archivos = "update componentes_archivos set id_tipo=2 where id_tipo=1";
	$db->setConsulta($consulta_archivos);
	if($db->ejecutarSoloConsulta()) {
		echo "<span style='color: green;'>SE HAN CONVERTIDO TODOS LOS ARCHIVOS DE LAS CABINAS EN ARCHIVOS DE PERIFERICOS</span><br/>";
		// Asociar todos los kits de las cabinas a perifericos
		$consulta_kits = "update componentes_kits set id_tipo_componente=2 where id_tipo_componente=1";
		$db->setConsulta($consulta_kits);
		if($db->ejecutarSoloConsulta()) {
			echo "<span style='color: green;'>SE HAN CONVERTIDO TODOS LOS KITS DE LAS CABINAS EN KITS DE PERIFERICOS</span><br/>";
			// Convertir id_tipo de las OPR que correspondan con cabinas
			$consulta_opr = "update orden_produccion_referencias set id_tipo_componente=2 where id_tipo_componente=1";
			$db->setConsulta($consulta_opr);
			if($db->ejecutarSoloConsulta()){
				echo "<span style='color: green;'>SE HAN CONVERTIDO TODAS LAS REFERENCIAS DE LAS OP DE CABINAS EN REFERENCIAS DE OP DE PERIFERICOS</span><br/>";
				// Convertir el tipo de los componentes de las Plantillas de producto que correspondan con cabinas
				$consulta_plantillas = "update plantilla_producto_componentes set id_tipo_componente=2 where id_tipo_componente=1";
				$db->setConsulta($consulta_plantillas);
				if($db->ejecutarSoloConsulta()){
					echo "<span style='color: green;'>SE HAN CONVERTIDO TODOS LOS COMPONENTES DE PLANTILLAS DE CABINAS EN COMPONENTES DE PLANTILLAS DE PERIFERICOS</span><br/>";
				}
				else {
					echo "<span style='color: red;'>SE PRODUJO UN ERROR AL CONVERTIR LOS COMPONENTES DE LAS PLANTILLAS DE CABINAS EN COMPONENTES DE PLANTILLAS DE PERIFERICOS</span><br/>";
				}
			}
			else {
				echo "<span style='color: red;'>SE PRODUJO UN ERROR AL CONVERTIR LAS REFERENCIAS DE LAS OP DE LAS CABINAS EN REFERENCIAS DE OP DE PERIFERICOS</span><br/>";
			}
		}
		else {
			echo "<span style='color: red;'>SE PRODUJO UN ERROR AL CONVERTIR LOS KITS DE LAS CABINAS EN KITS DE PERIFERICOS</span><br/>";
		}
	}
	else {
		echo "<span style='color: red;'>SE PRODUJO UN ERROR AL CONVERTIR LOS ARCHIVOS DE LAS CABINAS EN ARCHIVOS DE PERIFERICOS</span><br/>";
	}
}
else {
	echo "<span style='color: red;'>SE PRODUJO UN ERROR AL CONVERTIR LAS CABINAS EN PERIFERICOS</span><br/>";
}







?>
