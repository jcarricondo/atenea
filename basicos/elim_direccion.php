<?php
// Este fichero elimina la direccion de basicos
include("../includes/sesion.php");
include("../classes/basicos/direccion.class.php");
permiso(4);

$dir = new Direccion();

$dir->cargaDatosDireccionId($_GET["id"]);
$id_direccion = $dir->id_direccion;
$nombre_empresa = $dir->nombre_empresa;
$cif = $dir->cif;
$direccion = $dir->direccion;
$codigo_postal = $dir->codigo_postal;
$localidad = $dir->localidad;
$provincia = $dir->provincia;
$pais = $dir->pais;
$telefono = $dir->telefono;
$tipo = $dir->tipo;
	
$dir->datosNuevaDireccion($id_direccion,$nombre_empresa,$cif,$direccion,$codigo_postal,$localidad,$provincia,$telefono,$persona_contacto,$comentarios,$tipo);
$resultado = $dir->eliminar();
if($resultado == 6) {
	header("Location: direcciones.php?dir=eliminado");
} 
else {
	$mensaje_error = $dir->getErrorMessage($resultado);
}
?>
