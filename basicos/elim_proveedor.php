<?php
// Este fichero elimina el proveedor de basicos
include("../includes/sesion.php");
include("../classes/basicos/proveedor.class.php");
permiso(4);

$proveedor = new Proveedor();

$proveedor->cargaDatosProveedorId($_GET["id"]);
$id_proveedor = $proveedor->id_proveedor;
$nombre = $proveedor->nombre;
$direccion = $proveedor->direccion;
$telefono = $proveedor->direccion;
$email = $proveedor->email;
$ciudad = $proveedor->ciudad;
$pais = $proveedor->pais;
$forma_pago = $proveedor->forma_pago;
$metodo_pago = $proveedor->metodo_pago;
$tiempo_suministro = $proveedor->tiempo_suministro;
$descripcion = $proveedor->descripcion;
$provincia = $proveedor->provincia;
$codigo_postal = $proveedor->codigo_postal;
$persona_contacto = $proveedor->persona_contacto;
	
$proveedor->datosNuevoProveedor($id_proveedor,$nombre,$descripcion,$direccion,$ciudad,$pais,$telefono,$email,$forma_pago,$tiempo_suministro,$metodo_pago,$provincia,$codigo_postal,$persona_contacto);
$resultado = $proveedor->eliminar();
if($resultado == 6) {
	header("Location: proveedores.php?prov=eliminado");
} 
else {
	$mensaje_error = $proveedor->getErrorMessage($resultado);
}
?>
