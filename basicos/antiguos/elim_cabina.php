<?php
// Este fichero elimina la cabina de basicos
include("../includes/sesion.php");
include("../classes/basicos/cabina.class.php");
permiso(4);

$cabina = new Cabina();

$cabina->cargaDatosCabinaId($_GET["id"]);
$id_componente = $cabina->id_componente;
$nombre = $cabina->cabina;
$referencia = $cabina->referencia;
$descripcion = $cabina->descripcion;
$version = $cabina->version;
$estado = $cabina->estado;
$prototipo = $cabina->prototipo;
$referencias = $cabina->referencias;
	
$cabina->datosNuevoCabina($id_componente,$nombre,$referencia,$descripcion,$version,$referencias,"",$id_tipo,"",NULL,$estado,$prototipo,NULL);
$resultado = $cabina->eliminarReferenciasCabina();
if ($resultado == 7) {
	// Una vez eliminadas las referencias de cabinas de la tabla componentes_referencias eliminamos la cabina
	$resultado = $cabina->eliminar();	
	if($resultado == 6) {
		$resultado = $cabina->quitarArchivoCabina($id_componente);
		if($resultado == 1) {
			header("Location: cabinas.php?cab=eliminado");
		}
		else {
			$mensaje_error = $cabina->getErrorMessage($resultado);
		}
	} 
	else {
		$mensaje_error = $cabina->getErrorMessage($resultado);
	}
} 
else {
	$mensaje_error = $cabina->getErrorMessage($resultado);
}
?>
