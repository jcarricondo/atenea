<?php
// Este fichero elimina el fabricante de basicos
include("../includes/sesion.php");
include("../classes/basicos/fabricante.class.php");
permiso(4);

$fabricante = new Fabricante();

$fabricante->cargaDatosFabricanteId($_GET["id"]);
$id_fabricante = $fabricante->id_fabricante;
$nombre = $fabricante->nombre;
$direccion = $fabricante->direccion;
$telefono = $fabricante->direccion;
$email = $fabricante->email;
$ciudad = $fabricante->ciudad;
$pais = $fabricante->pais;
$descripcion = $fabricante->descripcion;
	
$fabricante->datosNuevoFabricante($id_fabricante,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email);
$resultado = $fabricante->eliminar();
if($resultado == 6) {
	header("Location: fabricantes.php?fab=eliminado");
}
else {
	$mensaje_error = $fabricante->getErrorMessage($resultado);
}
?>
