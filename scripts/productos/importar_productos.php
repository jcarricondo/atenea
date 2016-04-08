<?php 
set_time_limit(10000);
// Script parar importar los productos de TORO
include("../../classes/mysql.class.php");
include("../../classes/basicos/nombre_producto.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/productos/producto.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$op = new Orden_Produccion();
$producto = new Producto();
$nom = new Nombre_Producto();
$log = new Log_Unificacion();

echo '<br/>PRODUCTOS de TORO<br/>';
// Importamos los PRODUCTOS de TORO que no existan en SMK
$consulta = "select * from productos_toro where activo=1";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res_toro = $db->getResultados();

for($i=0;$i<count($res_toro);$i++){
	$id_produccion = $res_toro[$i]["id_produccion"];
	$id_nombre_producto = $res_toro[$i]["id_nombre_producto"];
	$num_serie = $res_toro[$i]["num_serie"];

	$estado = $res_toro[$i]["estado"];
	$fecha_entrega = $res_toro[$i]["fecha_entrega"];
	$fecha_entrega_prevista = $res_toro[$i]["fecha_entrega_prevista"];

	// Comprobamos cual es la Orden de Produccion equivalente de TORO
	$consulta_op = sprintf("select * from orden_produccion_toro where activo=1 and id_produccion=%s",
		$db->makeValue($id_produccion, "int"));
	$db->setConsulta($consulta_op);
	$db->ejecutarConsulta();
	$res_op = $db->getPrimerResultado();
	$alias_toro = $res_op["alias"];

	// Comprobamos si existen productos para esa OP actualizada
	if($res_op != NULL){

		//d($consulta_op);
		//d($alias_toro);

		// Buscamos el nuevo id_produccion del OP con ese alias
		$consulta_op = sprintf("select * from orden_produccion where activo=1 and alias=%s",
			$db->makeValue($alias_toro,"text"));
		$db->setConsulta($consulta_op);
		$db->ejecutarConsulta();
		$res_op = $db->getPrimerResultado();
		$id_produccion_new = $res_op["id_produccion"];

		// d($id_produccion_new);

		// Comprobamos cual es el Nombre de Producto equivalente de TORO
		$consulta_np = sprintf("select * from nombre_producto_toro where activo=1 and id_nombre_producto=%s",
			$db->makeValue($id_nombre_producto, "int"));
		$db->setConsulta($consulta_np);
		$db->ejecutarConsulta();
		$res_np = $db->getPrimerResultado();
		$nombre_producto_toro = $res_np["nombre"];

		// Buscamos el nuevo id_nombre_producto 
		$consulta_np = sprintf("select * from nombre_producto where activo=1 and nombre=%s",
			$db->makeValue($nombre_producto_toro,"text"));
		$db->setConsulta($consulta_np);
		$db->ejecutarConsulta();
		$res_np = $db->getPrimerResultado();
		$id_nombre_producto_new = $res_np["id_nombre_producto"];

		if($id_nombre_producto != $id_nombre_producto_new){
			d($id_nombre_producto);
			d($id_nombre_producto_new);
		}

		
		// Actualizamos el num_serie
		// Utilizamos una variable auxiliar para contabilizar las id_produccion y resetear el contador_producto
		if($id_produccion_new != $id_produccion_aux) {
			$contador_producto = 150;
		}
		else {
			$contador_producto++;
		}


		// Cargamos los datos del nombre de producto para generar el numero de serie del producto
		$nom->cargaDatosNombreProductoId($id_nombre_producto_new);
		$codigo_nombre_producto = $nom->codigo;
		$num_serie = $codigo_nombre_producto.'_'.$id_produccion_new.'_'.$contador_producto;

		$id_produccion_aux = $id_produccion_new;

		// Insertamos los productos actualizados
		$consulta = sprintf("insert into productos (id_produccion,id_nombre_producto,id_cliente,num_serie,num_ordenadores,estado,fecha_entrega,fecha_entrega_prevista,fecha_creado,activo) 
								value (%s,%s,NULL,%s,0,%s,%s,%s,current_timestamp,1) ",
			$db->makeValue($id_produccion_new, "int"),
			$db->makeValue($id_nombre_producto_new, "int"),
			$db->makeValue($num_serie, "text"),
			$db->makeValue($estado, "text"),
			$db->makeValue($fecha_entrega, "text"),
			$db->makeValue($fecha_entrega_prevista, "text"));
		$db->setConsulta($consulta);
		if($db->ejecutarSoloConsulta()) {
			// Insertamos el log
			$mensaje_log = '<span style="color:green">El producto ['.$num_serie.'] se ha importado correctamente</span><br/><br/>';
			$log->datosNuevoLog(NULL,"IMPORTAR_PRODUCTOS (TORO)",$mensaje_log,$fecha);
			$res_log = $log->guardarLog();
			if($res_log == 1){
				echo $mensaje_log;
			}
			else echo 'Se produjo un error al guardar el LOG';
		}
		else {
			echo 'Se ha producido un error al importar los productos de TORO';
		}
	}
}
?>

