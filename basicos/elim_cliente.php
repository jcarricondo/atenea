<?php
// Este fichero elimina el cliente de basicos
include("../includes/sesion.php");
include("../classes/basicos/cliente.class.php");
permiso(4);

$cliente = new Cliente();

$cliente->cargaDatosClienteId($_GET["id"]);
$id_cliente = $cliente->id_cliente;
$nombre = $cliente->nombre;
$direccion = $cliente->direccion;
$cp = $cliente->cp;
$ciudad = $cliente->ciudad;
$pais = $cliente->pais;
$telefono = $cliente->telefono;
$email = $cliente->email;
	
$cliente->datosNuevoCliente($id_cliente,$nombre,$direccion,$cp,$ciudad,$pais,$telefono,$email);
$resultado = $cliente->eliminar();
if($resultado == 6) {
	header("Location: clientes.php?client=eliminado");
}
else {
	$mensaje_error = $cliente->getErrorMessage($resultado);
}
?>