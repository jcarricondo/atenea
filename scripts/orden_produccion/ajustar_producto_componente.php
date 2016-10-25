<?php 
// Script para ajustar los productos_componentes de las opr
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$op = new Orden_Produccion();
$log = new Log_Unificacion();

echo '<br/>OPR PROD_COMPONENTES de OPR de TORO<br/>';

// Obtenemos las ops de TORO
$c_datos_op = "select id_produccion from orden_produccion where activo=1 and id_sede=2";
$db->setConsulta($c_datos_op);
$db->ejecutarConsulta();
$datos_op = $db->getResultados();	
// $id_produccion = $id_produccion[0]["id_produccion"];

// d($datos_op);	

// Variable para comprobar si es un componente repetido
$id_componente_aux = NULL;

// Recorremos las OP de TORO de la BBDD DE SMK
for($i=0;$i<count($datos_op);$i++){
	$id_produccion = $datos_op[$i]["id_produccion"];

	// Obtenemos los opc de cada id_produccion
	$c_opc = sprintf("select * from orden_produccion_componentes where activo=1 and id_produccion=%s",
		$db->makeValue($id_produccion, "int"));	
	$db->setConsulta($c_opc);
	$db->ejecutarConsulta();
	$datos_opc = $db->getResultados();	

	//d($id_produccion);

	for($j=0;$j<count($datos_opc);$j++){
		$id_componente = $datos_opc[$j]["id_componente"];
		$id_produccion_componente = $datos_opc[$j]["id_produccion_componente"];
		$num_componente = 1;

		// Obviamos el componente "Software"
		if($id_componente != 2){
			/*
			if($id_componente == $id_componente_aux){
				// Sumamos uno al id_produccion_componente
				//$id_produccion_componente++;
			}

			d($id_componente); 
			d($id_produccion_componente);
			*/

			$c_refs_opr = sprintf("select * from orden_produccion_referencias where activo=1 and id_produccion=%s and id_componente=%s",
				$db->makeValue($id_produccion, "int"),
				$db->makeValue($id_componente, "int"));	
			$db->setConsulta($c_refs_opr);
			$db->ejecutarConsulta();
			$datos_refs_opr = $db->getResultados();				

			//d($datos_refs_opr);

			$id_componente_aux = $id_componente;

			//d($id_componente_aux);

			for($k=0;$k<count($datos_refs_opr);$k++){
				$id = $datos_refs_opr[$k]["id"];

				//d($id);
				// UPDATE OPR 
				$updateSql = sprintf("update orden_produccion_referencias set id_produccion_componente=%s where id=%s",
					$db->makeValue($id_produccion_componente, "int"),
					$db->makeValue($id, "int"));
				$db->setConsulta($updateSql);
				if($db->ejecutarSoloConsulta()){
					$error = false;							
				}
				else {
					$error = true;	
				}
			}
			if(!$error){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado el id_produccion_componente['.$id_produccion_componente.'] del componente['.$id_componente.'] de la OP['.$id_produccion.']</span><br/><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR_PRODUCTO_COMPONENTE - Actualizar producto componente",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';

			}
			else {
				echo '<span style="color:red;">Se produjo un error al actualizar el id_produccion_componente['.$id_produccion_componente.'] del componente['.$id_componente.'] de la OP['.$id_produccion.']<br/>';
			}
		}
		echo "<br/>";
	}
	echo "<br/>";
}
?>
