<?php 
// Script para importar el stock del taller de TORO
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$log = new Log_Unificacion();

// Obtenemos todas las piezas de stock de TORO
$consultaSql = "select * from stock_talleres_toro";
$db->setConsulta($consultaSql);
$db->ejecutarConsulta();
$res = $db->getResultados();

// d($res);
echo "<br/>TALLERES_STOCK de TORO<br/>";

for($i=0;$i<count($res);$i++){
	$id_referencia = $res[$i]["id_referencia"];
	$piezas = $res[$i]["piezas"];

	// Ajustamos las referencias
	if($id_referencia == 1821) {
		$id_referencia = 1806; 
	}
	else if($id_referencia == 1823){
		$id_referencia = 1808;
	}

	// Insertamos las piezas en el STOCK de TALLERES con el taller de TORO
	$insertSql = sprintf("insert into stock_talleres (id_referencia,piezas,id_taller) values(%s,%s,2)",
		$db->makeValue($id_referencia, "int"),
		$db->makeValue($piezas, "float"));
	$db->setConsulta($insertSql);
	if($db->ejecutarSoloConsulta()){
		echo '<span style="color: green";> Se ha insertado la referencia ['.$id_referencia.'] con '.$piezas.' piezas en el TALLERES_STOCK de TORO</span><br/>';
	}
	else{
		echo '<span style="color: red";> Se ha producido un error al insertar la referencia ['.$id_referencia.'] con '.$piezas.' piezas en el TALLERES_STOCK de TORO</span><br/>';
	}
}

// Recorremos las referencias duplicadas y actualizamos en el caso de que alguna referencia este duplicada en stock 
$refDuplicadasSql = "select * from referencias_duplicadas";
$db->setConsulta($refDuplicadasSql);
$db->ejecutarConsulta();	
$res_duplicadas = $db->getResultados();

// Ahora actualizamos la tabla stock donde estan guardadas las piezas de taller
for($i=0;$i<count($res_duplicadas);$i++){
	$id_referencia = $res_duplicadas[$i]["id_referencia"];
	$id_referencia_duplicada = $res_duplicadas[$i]["id_referencia_duplicada"];

	// d($id_referencia);
	// d($id_referencia_duplicada);

	// Si alguna pieza duplicada esta en el stock del taller la actualizamos 
	$refStockSql = sprintf("select * from stock_talleres where id_referencia=%s",
		$db->makeValue($id_referencia_duplicada,"int"));
	$db->setConsulta($refStockSql);
	$db->ejecutarConsulta();
	$res_stock = $db->getResultados();

	// d($res_ref_componentes);

	if($res_stock != NULL){
		for($j=0;$j<count($res_stock);$j++){
			$id = $res_stock[$j]["id"];
			$id_referencia = $res_stock[$j]["id_referencia"];
			$id_taller = $res_stock[$j]["id_taller"];
			
			// Actualizamos la referencia desactivada del stock
			$updateSql = sprintf("update stock_talleres set id_referencia=%s where id=%s",
				$db->makeValue($id_referencia, "int"),
				$db->makeValue($id, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green;">Se ha actualizado la referencia ['.$id_referencia_duplicada.'] => ['.$id_referencia.'] del taller ['.$id_taller.'] </span><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR STOCK TALLER - Actualizar referencias duplicadas",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}
			else {
				echo 'Se ha producido un error al actualizar la referencia del componente';
			}
		}
	}		
}



?>