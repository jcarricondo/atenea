<?php
// Este fichero elimina el centro logistico de basicos
include("../includes/sesion.php");
include("../classes/basicos/centro_logistico.class.php");
permiso(4);

$centroLogistico = new CentroLogistico();

$centroLogistico->cargaDatosCentroLogisticoId($_GET["id"]);
$id_centro_logistico = $centroLogistico->id_centro_logistico;
$nombre = $centroLogistico->nombre;
$direccion = $centroLogistico->direccion;
$telefono = $centroLogistico->telefono;
$email = $centroLogistico->email;
$ciudad = $centroLogistico->ciudad;
$pais = $centroLogistico->pais;
$forma_pago = $centroLogistico->forma_pago;
$metodo_pago = $centroLogistico->metodo_pago;
$tiempo_suministro = $centroLogistico->tiempo_suministro;
$descripcion = $centroLogistico->descripcion;
$provincia = $centroLogistico->provincia;
$codigo_postal = $centroLogistico->codigo_postal;
$persona_contacto = $centroLogistico->persona_contacto;
	
$centroLogistico->datosNuevoCentroLogistico($id_centro_logistico,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto);
$resultado = $centroLogistico->eliminar();
if($resultado == 6) {
	header("Location: centros_logisticos.php?centro_logistico=eliminado");
} 
else {
	$mensaje_error = $centroLogistico->getErrorMessage($resultado);
}
?>
