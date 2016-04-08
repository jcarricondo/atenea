<?php
// Este fichero elimina el software de basicos
include("../includes/sesion.php");
include("../classes/basicos/software.class.php");
permiso(4);

$software = new Software();

$software->cargaDatosSoftwareId($_GET["id"]);
$id_componente = $software->id_componente;
$nombre = $software->software;
$referencia = $software->referencia;
$descripcion = $software->descripcion;
$version = $software->version;
	
$software->datosNuevoSoftware($id_componente,$nombre,$referencia,$descripcion,$version,$id_tipo);
$resultado = $software->eliminar();
if($resultado == 6) {
	header("Location: software_simulacion.php?soft=eliminado");
}
else {
	$mensaje_error = $software->getErrorMessage($resultado);
}
?>
