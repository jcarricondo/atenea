<?php
// Este fichero elimina la referencia de basicos
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/log/basicos/log_basicos_referencias.class.php");
permiso(4);

$referencias = new Referencia();
$log = new LogBasicosReferencias();

$referencias->cargaDatosReferenciaId($_GET["id"]);
$id_referencia = $referencias->id_referencia;
$nombre = $referencias->referencia;
$proveedor = $referencias->proveedor;
$fabricante = $referencias->fabricante;
$ref_fab_pieza = $referencias->part_fabricante_referencia;
$ref_prov_pieza = $referencias->part_proveedor_referencia;
$nombre_pieza = $referencias->part_nombre;
$tipo_pieza = $referencias->part_tipo;
$part_value_name = $referencias->part_valor_nombre;
$part_value_qty = $referencias->part_valor_cantidad;
$part_value_name_2 = $referencias->part_valor_nombre_2;
$part_value_qty_2 = $referencias->part_valor_cantidad_2;
$part_value_name_3 = $referencias->part_valor_nombre_3;
$part_value_qty_3 = $referencias->part_valor_cantidad_3;
$part_value_name_4 = $referencias->part_valor_nombre_4;
$part_value_qty_4 = $referencias->part_valor_cantidad_4;
$part_value_name_5 = $referencias->part_valor_nombre_5;
$part_value_qty_5 = $referencias->part_valor_cantidad_5;
$pack_precio = $referencias->pack_precio;
$unidades_paquete = $referencias->unidades;
$precio_pack_qty = $referencias->part_precio_cantidad;
$nombre_proveedor = $referencias->nombre_proveedor;
$comentarios = $referencias->comentarios;
$fecha_creado = $referencias->fecha_creado;
$id_motivo_compatibilidad = $referencias->id_motivo_compatibilidad;

$referencias->datosNuevaReferencia($id_referencia,$nombre,$fabricante,$proveedor,$nombre_pieza,$tipo_pieza,$ref_proveedor_pieza,$ref_fabricante_pieza,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades,$nombre_archivo,$comentarios,$id_motivo_compatibilidad);
$resultado = $referencias->eliminar();
if($resultado == 6) {
	// Obtenemos las referencias del buscador de referencias 
	// Comprobamos si se filtro por busqueda magica
	if($_GET["busqueda_magica_buscador"] != NULL){
		$referencia_buscador = "";
		$proveedor_buscador = "";
		$ref_prov_pieza_buscador = "";
		$precio_pack_buscador = "";
		$fabricante_buscador = "";
		$ref_fab_pieza_buscador = "";
		$tipo_pieza_buscador = "";
		$part_value_name_buscador = "";
		$unidades_paquete_buscador = "";
		$nombre_pieza_buscador = "";
		$part_value_qty_buscador = "";
		$busqueda_magica_buscador = $_GET["busqueda_magica_buscador"];
		$ordenar_referencias_buscador = "";
		$fecha_desde_buscador = "";
		$fecha_hasta_buscador = "";
		$id_referencia_buscador = "";
	}
	else{
		$referencia_buscador = $_GET["referencia_buscador"];
		$proveedor_buscador = $_GET["proveedor_buscador"];
		$ref_prov_pieza_buscador = $_GET["ref_prov_pieza_buscador"];
		$precio_pack_buscador = $_GET["precio_pack_buscador"];
		$fabricante_buscador = $_GET["fabricante_buscador"];
		$ref_fab_pieza_buscador = $_GET["ref_fab_pieza_buscador"];
		$tipo_pieza_buscador = $_GET["tipo_pieza_buscador"];
		$part_value_name_buscador = $_GET["part_value_name_buscador"];
		$unidades_paquete_buscador = $_GET["unidades_paquete_buscador"];
		$nombre_pieza_buscador = $_GET["nombre_pieza_buscador"];
		$part_value_qty_buscador = $_GET["part_value_qty_buscador"];
		$busqueda_magica_buscador = $_GET["busqueda_magica_buscador"];
		$ordenar_referencias_buscador = $_GET["ordenar_referencias_buscador"];
		$fecha_desde_buscador = $_GET["fecha_desde_buscador"];
		$fecha_hasta_buscador = $_GET["fecha_hasta_buscador"];
		$id_referencia_buscador = $_GET["id_referencia_buscador"];
	}

	// Guardamos el log de la operación
	$id_usuario = $_SESSION["AT_id_usuario"];
	$proceso = "ELIMINACION REFERENCIA";
	$descripcion = "-";
	$referencia_creada = "NO";
	$referencia_heredada = "NO";
	$referencia_compatible = "NO";
	$error = "NO";
	$codigo_error = "OK!";

	$log->setValores($id_usuario,$proceso,$id_referencia,$nombre,$proveedor,$fabricante,$tipo_pieza,$nombre_pieza,$ref_fab_pieza,$ref_prov_pieza,
			$descripcion,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,
			$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades_paquete,NULL,$comentarios,$fecha_creado,$fecha_modificacion,$referencia_creada,
			$referencia_heredada,$referencia_compatible,$error,$codigo_error);

	$res_log = $log->guardarLog();
	if ($res_log == 0) echo '<script>alert("Se ha producido un error al guardar el log de la operación")</script>';
	header("Location: referencias.php?ref=eliminado&referencia=".$referencia_buscador."&proveedor=".$proveedor_buscador."&ref_prov_pieza=".$ref_prov_pieza_buscador."&precio_pack=".$precio_pack_buscador."&fabricante=".$fabricante_buscador."&ref_fab_pieza=".$ref_fab_pieza_buscador."&tipo_pieza=".$tipo_pieza_buscador."&part_value_name=".$part_value_name_buscador."&unidades_paquete=".$unidades_paquete_buscador."&nombre_pieza=".$nombre_pieza_buscador."&part_value_qty=".$part_value_qty_buscador."&busqueda_magica=".$busqueda_magica_buscador."&ordenar_referencias=".$ordenar_referencias_buscador."&fecha_desde=".$fecha_desde_buscador."&fecha_hasta=".$fecha_hasta_buscador."&id_ref=".$id_referencia_buscador);
} 
else {
	$mensaje_error = $referencias->getErrorMessage($resultado);

	// Guardamos el log de la operación
	$id_usuario = $_SESSION["AT_id_usuario"];
	$proceso = "ELIMINACION REFERENCIA";
	$descripcion = "-";
	$referencia_creada = "NO";
	$referencia_heredada = "NO";
	$referencia_compatible = "NO";
	$error = "SI";
	$codigo_error = $mensaje_error;

	$log->setValores($id_usuario,$proceso,$id_referencia,$nombre,$proveedor,$fabricante,$tipo_pieza,$nombre_pieza,$ref_fab_pieza,$ref_prov_pieza,
			$descripcion,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,
			$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades_paquete,NULL,$comentarios,$fecha_creado,$fecha_modificacion,$referencia_creada,
			$referencia_heredada,$referencia_compatible,$error,$codigo_error);

	$res_log = $log->guardarLog();
	if ($res_log == 0) echo '<script>alert("Se ha producido un error al guardar el log de la operación")</script>';
}
?>
