// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en la gestion de prioridad de produccion

// Funcion que redirige a la pagina de gestion de la Produccion para iniciar el reajuste de recepcion
function iniciarProcesoPrioridad(id_sede){
	// window.location='gestionarProduccion.php?iniciar=YES'; 
	window.location='gestionar_produccion_optimizado.php?iniciar=YES&sedes=' + id_sede; 
}

// Funcion que carga las Ordenes de Produccion en funcion de la sede 
function cargaOrdenesProduccion(id_sede){
	window.location='gestionar_produccion_optimizado.php?sedes=' + id_sede;
}

