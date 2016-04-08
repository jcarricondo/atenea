// JavaScript Document
// Fichero que contiene las funciones JavaScript de Pedidos

// Funcion que pregunta si se desean eliminar las Ordenes de Produccion
function validarEliminacion(id_pedido) {
	if (confirm ("¿Desea eliminar el pedido?")) {
			window.location='eliminar_pedido.php?id=' + id_pedido;
	}
}