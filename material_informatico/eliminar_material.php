<?php
// Este fichero elimina el material informático seleccionado
include("../includes/sesion.php");
include("../classes/material_informatico/material_informatico.class.php");
permiso(39);

$materialInformatico = new MaterialInformatico();

$materialInformatico->cargaDatosMaterialId($_GET["id"]);
$id_material = $materialInformatico->id_material;
$id_tipo = $materialInformatico->id_tipo;
$id_subtipo = $materialInformatico->id_subtipo;
$num_serie = $materialInformatico->num_serie;
$descripcion = $materialInformatico->descripcion;
$id_almacen = $materialInformatico->id_almacen;
$precio = $materialInformatico->precio;
$asignado_a = $materialInformatico->asignado_a;
$estado = $materialInformatico->estado;
$observaciones = $materialInformatico->observaciones;
$fecha_creado = $materialInformatico->fecha_creado;
$activo = $materialInformatico->activo;

$materialInformatico->datosMaterial($id_material,$id_tipo,$id_subtipo,$num_serie,$descripcion,$id_almacen,$precio,$asignado_a,$estado,$observaciones,$fecha_creado,$activo);
$resultado = $materialInformatico->eliminar();
if($resultado == 1) {
	header("Location: listado_informatica.php?matInf=eliminado&realizandoBusqueda=1");
}
else {
	$mensaje_error = $materialInformatico->getErrorMessage($resultado);
}
?>
