<?php
// Este fichero elimina la interfaz de basicos
include("../includes/sesion.php");
include("../classes/basicos/interface.class.php");
permiso(4);

$interfaces = new Interfaz();

$interfaces->cargaDatosInterfazId($_GET["id"]);
$id_componente = $interfaces->id_componente;
$nombre = $interfaces->interfaz;
$referencia = $interfaces->referencia;
$descripcion = $interfaces->descripcion;
$version = $interfaces->version;
$referencias = $interfaces->referencias;
	
$interfaces->datosNuevoInterfaz($id_componente,$nombre,$referencia,$descripcion,$version,$referencias,"",$id_tipo,"",$estado,$prototipo);
$resultado = $interfaces->eliminarReferenciasInterfaz();
if ($resultado == 7) {
	// Una vez eliminadas las referencias de la interfaz de la tabla componentes_referencias eliminamos la interfaz
	$resultado = $interfaces->eliminar();	
	if($resultado == 6) {
		$resultado = $interfaces->quitarArchivoInterfaz($id_componente);
		if($resultado == 1) {
			header("Location: interfaces.php?interface=eliminado");
		}
		else {
			$mensaje_error = $interfaces->getErrorMessage($resultado);
		}
	} 
	else {
		$mensaje_error = $interfaces->getErrorMessage($resultado);
	}
} 
else {
	$mensaje_error = $interfaces->getErrorMessage($resultado);
}
?>
