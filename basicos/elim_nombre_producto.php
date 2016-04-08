<?php
// Este fichero elimina el nombre de producto de basicos
include("../includes/sesion.php");
include("../classes/basicos/nombre_producto.class.php");
permiso(4);

$nombre_producto = new Nombre_Producto();

$nombre_producto->cargaDatosNombreProductoId($_GET["id"]);
$id_nombre_producto = $nombre_producto->id_nombre_producto;
$nombre = $nombre_producto->nombre;
$codigo = $nombre_producto->codigo;
$version = $nombre_producto->version;
$familia = $nombre_producto->familia;
	
$nombre_producto->datosNuevoProducto($id_nombre_producto,$nombre,$codigo,$version,$familia);
$resultado = $nombre_producto->eliminar();
if($resultado == 6) {
	header("Location: nombres_de_productos.php?producto=eliminado");
}
else {
	$mensaje_error = $nombre_producto->getErrorMessage($resultado);
}
?>
