<?php
// Este fichero elimina la familia de basicos
include("../includes/sesion.php");
include("../classes/basicos/familia.class.php");
permiso(4);

$familia = new Familia();

$familia->cargaDatosFamiliaId($_GET["id"]);
$id_familia = $familia->id_familia;
$nombre = $familia->nombre;
	
$familia->datosNuevaFamilia($id_familia,$nombre);
$resultado = $familia->eliminar();
if($resultado == 6) {
	header("Location: familias.php?familia=eliminado");
} 
else {
	$mensaje_error = $nombre_producto->getErrorMessage($resultado);
}
?>
