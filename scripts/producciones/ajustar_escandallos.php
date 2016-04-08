<?php 
set_time_limit(10000);
// Script para ajustar los escandallos de toro
include("../../classes/mysql.class.php");
include("../../classes/kint/Kint.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

echo '<br/>AJUSTE DEL LOG DE LOS ESCANDALLOS DE TORO';

// Ajuste de USUARIOS
// Ajuste de ID_PRODUCCION
// Ajuste de ID_COMPONENTE

// Obtenemos todo el log de los escandallos de TORO
$consulta = "select * from escandallo_log";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

for($i=0;$i<count($res);$i++){
	$id_usuario = $res[$i]["id_usuario"];
	$id_produccion = $res[$i]["id_produccion"];
	$id_componente = $res[$i]["id_componente"];

	$consulta_usuario = sprintf("select * from usuarios_toro where id_usuario=%s",
		$db->makeValue($id_usuario,"int"));
	$db->setConsulta($consulta_usuario);
	$db->ejecutarConsulta();
	$res_usuario = $db->getPrimerResultado();

	if($res_usuario != NULL){
		$nombre_usuario = $res_usuario["usuario"];

		// Comprobamos que el usuario existe y tiene el mismo id
		$c_usuario_atenea = sprintf("select * from usuarios where usuario=%s",
			$db->makeValue($nombre_usuario,"text"));
		$db->setConsulta($c_usuario_atenea);
		$db->ejecutarConsulta();
		$res_usuario_atenea = $db->getPrimerResultado();

		$id_usuario_atenea = $res_usuario_atenea["id_usuario"];

		// Comprobamos el id_produccion
		$consulta_alias = sprintf("select * from orden_produccion_toro where id_produccion=%s",
			$db->makeValue($id_produccion, "int"));
		$db->setConsulta($consulta_alias);
		$db->ejecutarConsulta();
		$res_alias = $db->getPrimerResultado();
		$alias = $res_alias["alias"];

		//d($alias);

		// Obtenemos el id_produccion equivalente 
		$c_datos_op = sprintf("select id_produccion from orden_produccion where activo=1 and id_sede=2 and alias=%s",
			$db->makeValue($alias, "text"));
		$db->setConsulta($c_datos_op);
		$db->ejecutarConsulta();
		$id_produccion_atenea = $db->getResultados();	
		$id_produccion_atenea = $id_produccion_atenea[0]["id_produccion"];

		if($id_produccion != NULL){
			//d($id_produccion);

			// Ajuste de componente
			// Comprobamos cual es el nuevo componente de la OP equivalente a TORO
			$consulta_comp = sprintf("select * from componentes_toro where activo=1 and id_componente=%s",
				$db->makeValue($id_componente, "int"));
			$db->setConsulta($consulta_comp);
			$db->ejecutarConsulta();
			$res_comp = $db->getPrimerResultado();
			
			$nombre_componente_toro = $res_comp["nombre"];
			$version_componente_toro = $res_comp["version"];
			$id_tipo_componente_toro = $res_comp["id_tipo"];

			// Buscamos el componente actualizado 
			$consulta_comp = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=%s",
				$db->makeValue($nombre_componente_toro, "text"),
				$db->makeValue($version_componente_toro, "float"),
				$db->makeValue($id_tipo_componente_toro, "int"));
			$db->setConsulta($consulta_comp);
			$db->ejecutarConsulta();
			$res_comp = $db->getPrimerResultado();
			$id_componente_atenea = $res_comp["id_componente"];

			// Actualizamos el log del escandallo
			$updateSql = sprintf("update escandallo_log set id_usuario=%s,id_produccion=%s,id_componente=%s",
				$db->makeValue($id_usuario_atenea, "int"),
				$db->makeValue($id_produccion_atenea, "int"),
				$db->makeValue($id_componente_atenea, "int"));
			$db->setConsulta($updateSql);
			if($db->ejecutarSoloConsulta()){
				// Insertamos el log
				$mensaje_log = '<span style="color:green">Se ha actualizado el log del escandallo ID_USER['.$id_usuario_atenea.'] ID_OP['.$id_produccion_atenea.'] ID_COMP['.$id_componente_atenea.']</span><br/><br/>';
				$log->datosNuevoLog(NULL,"AJUSTAR_ESCANDALLOS",$mensaje_log,$fecha);
				$res_log = $log->guardarLog();
				if($res_log == 1){
					echo $mensaje_log;
				}
				else echo 'Se produjo un error al guardar el LOG';
			}
			else {
				echo '<span style="color:red">Error al actualizar el log del escandallo</span><br/>';
			}
		}
	}
}	
?>

