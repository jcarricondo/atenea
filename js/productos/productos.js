// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el listado de Ordenes de Produccion

// Funcion para abrir popups
function abrir(url) {
	open(url,'','top=200,left=700,width=500,height=500') ;
}

// Funcion para abrir popups
function Abrir_ventana(pagina) 
{
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=820, height=500, top=200, left=500";
	window.open(pagina,"",opciones);
}
	
// Funcion para cambiar el estado de la Orden de Produccion
function cambiarEstadoOP(id_produccion,estado) {
	window.location='ordenes_produccion.php?id_produccion=' + id_produccion + '&realizarBusqueda=1&cambiar_estado=true&estado_anterior=' + estado;	
}
	
