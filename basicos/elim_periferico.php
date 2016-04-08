<?php
// Este fichero elimina el periferico de basicos
include("../includes/sesion.php");
include("../classes/basicos/periferico.class.php");
permiso(4);

$perifericos = new Periferico();

$perifericos->cargaDatosPerifericoId($_GET["id"]);
$id_componente = $perifericos->id_componente;
$nombre = $perifericos->periferico;
$referencia = $perifericos->referencia;
$descripcion = $perifericos->descripcion;
$version = $perifericos->version;
$estado = $cabina->estado;
$prototipo = $cabina->prototipo;
$referencias = $perifericos->referencias;
	
$perifericos->datosNuevoPeriferico($id_componente,$nombre,$referencia,$descripcion,$version,$referencias,"",$id_tipo,"",NULL,$estado,$prototipo,NULL);	
$resultado = $perifericos->eliminarReferenciasPeriferico();
if ($resultado == 7) {
	// Una vez eliminadas las referencias de perifericos de la tabla componentes_referencias eliminamos el periferico
	$resultado = $perifericos->eliminar();	
	if($resultado == 6) {
		$resultado = $perifericos->quitarArchivoPeriferico($id_componente);
		if ($resultado == 1) {
			header("Location: perifericos.php?per=eliminado");
		}
		else {
			$mensaje_error = $perifericos->getErrorMessage($resultado);
		}
	} 
	else {
		$mensaje_error = $perifericos->getErrorMessage($resultado);
	}
}
else {
	$mensaje_error = $perifericos->getErrorMessage($resultado);
}
?>
