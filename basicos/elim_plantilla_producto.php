<?php
// Este fichero elimina el nombre de producto de basicos
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/plantilla_producto.class.php");
permiso(4);

$plant = new Plantilla_Producto();

$id_plantilla = $_GET["id_plantilla"];
$plant->cargaDatosPlantillaProductoId($id_plantilla);
$resultado = $plant->eliminar();
if($resultado == 1) {
    // Eliminamos los componentes asociados a la plantilla
    $resultado_comp = $plant->desactivarComponentesPlantilla($id_plantilla);
    if($resultado_comp == 1) {
        header("Location: plantillas_de_productos.php?plantilla=eliminado");
    }
    else{
        // ERROR AL ELIMINAR LOS COMPONENTES DE LA PLANTILLA DE PRODUCTO
        $mensaje_error = $plant->getErrorMessage($resultado_comp);
    }
}
else {
    // ERROR AL ELIMINAR LA PLANTILLA DE PRODUCTO
	$mensaje_error = $plant->getErrorMessage($resultado);
}
?>
