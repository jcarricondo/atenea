<?php
// Script encargado de reajustar las referencias de las tres BBDD de ATENEA
set_time_limit(10000);
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/funciones/log_unificacion.class.php");
include("../../classes/kint/Kint.class.php");

$db = new MySql();
$referencia = new Referencia();
$log = new Log_Unificacion();

// Adaptaremos las referencias segun el excel de Javier Alvarez 
// En la ultima reunion se determinó que solo se mantendrían las referencias de SMK y se importarían aquellas de Brasil que no existieran
// Se desactivarán las referencias duplicadas y se dejará la que tenga id_ref mas pequeño

$consulta = "select id_referencia from referencias order by id_referencia";
$db->setConsulta($consulta);
$db->ejecutarConsulta();
$res = $db->getResultados();

for($i=0;$i<count($res);$i++){
	$id_referencia = $res[$i]["id_referencia"]; 

	$referencia->cargaDatosReferenciaId($id_referencia);	
	$nombre = $referencia->referencia;
	$part_nombre = $referencia->part_nombre;
	$part_tipo = $referencia->part_tipo;
	$part_proveedor_referencia = $referencia->part_proveedor_referencia;
	$pack_precio = $referencia->pack_precio;
	$unidades = $referencia->unidades;
	$nombre_proveedor = $referencia->nombre_proveedor;
	$activo_smk = $referencia->dameDigitoActivoReferencia($id_referencia); 

	if($activo_smk != 0){
		// Comprobamos si el nombre de la referencia coincide con el de otra referencia
		$consulta_nombre = sprintf("select id_referencia from referencias where referencia=%s and part_proveedor_referencia=%s and id_referencia>%s and activo=1 ",
			$db->makeValue($nombre, "text"),
			$db->makeValue($part_proveedor_referencia, "text"),
			$db->makeValue($id_referencia,"int"));
		$db->setConsulta($consulta_nombre);
		$db->ejecutarConsulta();
		$res_duplicadas = $db->getResultados();

		if($res_duplicadas != NULL){
			for($z=0;$z<count($res_duplicadas);$z++){
				$id_ref_rep = $res_duplicadas[$z]["id_referencia"];
		
				$referencia->cargaDatosReferenciaId($id_ref_rep);
				$nombre_rep = $referencia->referencia;
				$part_nombre_rep = $referencia->part_nombre;
				$part_tipo_rep = $referencia->part_tipo;
				$part_proveedor_referencia_rep = $referencia->part_proveedor_referencia;
				$pack_precio_rep = $referencia->pack_precio;
				$unidades_rep = $referencia->unidades;
				$nombre_proveedor_rep = $referencia->nombre_proveedor;

				// Guardamos en una tabla temporal de la BBDD las referencias repetidas
				$insertSql = sprintf("insert into referencias_duplicadas (id_referencia,id_referencia_duplicada,fecha) values (%s,%s,current_timestamp)",
					$db->makeValue($id_referencia, "int"),
					$db->makeValue($id_ref_rep,"int"));
				$db->setConsulta($insertSql);
				if($db->ejecutarSoloConsulta()){
					// Insertamos el log
					$mensaje_log = '<br/><span style="color:green;">Se ha guardado la referencia ['.$id_ref_rep.']['.$id_referencia.'] repetida correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"REAJUSTAR REFERENCIAS - Guardar Duplicadas",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
			}
		}
	}
}

echo '<br/><br/>';

// Desactivamos las referencias duplicadas
for($i=0;$i<count($res);$i++){
	$id_referencia = $res[$i]["id_referencia"]; 

	$referencia->cargaDatosReferenciaId($id_referencia);	
	$nombre = $referencia->referencia;
	$part_proveedor_referencia = $referencia->part_proveedor_referencia;
	$activo_smk = $referencia->dameDigitoActivoReferencia($id_referencia); 

	if($activo_smk != 0){
		// Comprobamos si el nombre de la referencia coincide con el de otra referencia
		$consulta_nombre = sprintf("select id_referencia from referencias where referencia=%s and part_proveedor_referencia=%s and id_referencia>%s and activo=1 ",
			$db->makeValue($nombre, "text"),
			$db->makeValue($part_proveedor_referencia, "text"),
			$db->makeValue($id_referencia,"int"));
		$db->setConsulta($consulta_nombre);
		$db->ejecutarConsulta();
		$res_duplicadas = $db->getResultados();

		if($res_duplicadas != NULL){
			for($z=0;$z<count($res_duplicadas);$z++){
				$id_ref_rep = $res_duplicadas[$z]["id_referencia"];
		
				$referencia->cargaDatosReferenciaId($id_ref_rep);
				$nombre_rep = $referencia->referencia;
				$part_nombre_rep = $referencia->part_nombre;
				$part_tipo_rep = $referencia->part_tipo;
				$part_proveedor_referencia_rep = $referencia->part_proveedor_referencia;
				$pack_precio_rep = $referencia->pack_precio;
				$unidades_rep = $referencia->unidades;
				$nombre_proveedor_rep = $referencia->nombre_proveedor;

				// Guardamos en una tabla temporal de la BBDD las referencias repetidas
				$desactivarSql = sprintf("update referencias set activo=0 where id_referencia=%s",
					$db->makeValue($id_ref_rep,"int"));
				$db->setConsulta($desactivarSql);
				if($db->ejecutarSoloConsulta()){
					// Insertamos el log
					$mensaje_log = '<br/><span style="color:green;">Se ha desactivado la referencia ['.$id_ref_rep.']['.$id_referencia.'] repetida correctamente</span><br/>';
					$log->datosNuevoLog(NULL,"REAJUSTAR REFERENCIAS - Desactivar Duplicadas",$mensaje_log,$fecha);
					$res_log = $log->guardarLog();
					if($res_log == 1){
						echo $mensaje_log;
					}
					else echo 'Se produjo un error al guardar el LOG';
				}
			}
		}
	}
}

echo '<br/><br/>';

// Creamos las referencias 1843 y 1844 de BRASIL
$consultaSql = "select * from referencias_brasil where id_referencia=1843 or id_referencia=1844";
$db->setConsulta($consultaSql);
$db->ejecutarConsulta();
$res_brasil = $db->getResultados();

for($i=0;$i<count($res_brasil);$i++){
	// Obtenemos los datos 
	$id_referencia = $res_brasil[$i]["id_referencia"];
	$nombre = $res_brasil[$i]["referencia"];
	$id_proveedor = $res_brasil[$i]["id_proveedor"];
	$id_fabricante = $res_brasil[$i]["id_fabricante"];
	$part_tipo = $res_brasil[$i]["part_tipo"];
	$part_nombre = $res_brasil[$i]["part_nombre"];
	$part_fabricante_referencia = $res_brasil[$i]["part_fabricante_referencia"];
	$part_proveedor_referencia = $res_brasil[$i]["part_proveedor_referencia"];
	$part_descripcion = $res_brasil[$i]["part_descripcion"];
	$part_valor_nombre = $res_brasil[$i]["part_valor_nombre"];
	$part_valor_cantidad = $res_brasil[$i]["part_valor_cantidad"];
	$part_valor_nombre_2 = $res_brasil[$i]["part_valor_nombre_2"];
	$part_valor_cantidad_2 = $res_brasil[$i]["part_valor_cantidad_2"];
	$part_valor_nombre_3 = $res_brasil[$i]["part_valor_nombre_3"];
	$part_valor_cantidad_3 = $res_brasil[$i]["part_valor_cantidad_3"];
	$part_valor_nombre_4 = $res_brasil[$i]["part_valor_nombre_4"];
	$part_valor_cantidad_4 = $res_brasil[$i]["part_valor_cantidad_4"];
	$part_valor_nombre_5 = $res_brasil[$i]["part_valor_nombre_5"];
	$part_valor_cantidad_5 = $res_brasil[$i]["part_valor_cantidad_5"];
	$pack_precio = $res_brasil[$i]["pack_precio"];
	$unidades = $res_brasil[$i]["unidades"];
	$comentarios = $res_brasil[$i]["comentarios"];

	// Establecemos los datos para crear la referencia 
	$referencia->datosNuevaReferencia(NULL,$nombre,$id_fabricante,$id_proveedor,$part_nombre,$part_tipo,$part_proveedor_referencia,$part_fabricante_referencia,$part_valor_nombre,
								$part_valor_cantidad,$part_valor_nombre_2,$part_valor_cantidad_2,$part_valor_nombre_3,$part_valor_cantidad_3,$part_valor_name_4,
								$part_valor_cantidad_4,$part_valor_nombre_5,$part_valor_cantidad_5,$pack_precio,$unidades,NULL,$comentarios);

	// Guardamos la referencia 
	$res = $referencia->guardarCambios();
	if($res == 1){
		// Insertamos el log
		$mensaje_log = '<br/><span style="color:green;">Se ha guardado la referencia ['.$id_referencia.'-BR] correctamente</span><br/>';
		$log->datosNuevoLog(NULL,"REAJUSTAR REFERENCIAS - Referencias Brasil",$mensaje_log,$fecha);
		$res_log = $log->guardarLog();
		if($res_log == 1){
			echo $mensaje_log;
		}
		else echo 'Se produjo un error al guardar el LOG';
	}
	else {
		echo '<br/><span style="color:red;">Se ha producido un error al guardar la referencia ['.$id_referencia.'-BR] </span><br/>';	
	}
}

?>
