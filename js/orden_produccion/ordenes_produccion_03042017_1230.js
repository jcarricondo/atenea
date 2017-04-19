// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el listado de Ordenes de Produccion

function Abrir_ventana(pagina) {
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=820, height=500, top=200, left=500";
	window.open(pagina,"",opciones);
}

function abrir_iniciar(url) {
	open(url,'','top=100,left=450,width=1000,height=750') ;
}
	
// Funcion que comprueba si se puede modificar la Orden de Produccion. Si alguna de las Ordenes de Compra estan en estado PEDIDA/RECIBIDA
// no se podrá modificar 
function validarModificacion(id_produccion,id_producto) {
	/*var error_estado = document.getElementById("estado_pedida-" + id_produccion).value;
	if (error_estado == 1) {
		alert("No se puede modificar la Orden de Produccion porque las ordenes de compra asociadas estan en estado: PEDIDA/RECIBIDA");
		window.location="OrdenesProduccion.php?realizarBusqueda=1";
	}
	else window.location='mod_OrdenProduccion.php?id_produccion=' + id_produccion + '&id_producto=' + id_producto;*/
	window.location='mod_op.php?id_produccion=' + id_produccion + '&id_producto=' + id_producto;
}

// Funcion para abrir un popup pequeño
function abrirLittlePopup(url) {
	open(url,'','top=200,left=650,width=600,height=500') ;
}

	
// Funcion que pregunta si se desean eliminar las Ordenes de Produccion
function validarEliminacion(id_produccion) {
	var error_hay_facturas = document.getElementById("hay_facturas-" + id_produccion).value;
	var error_estado = document.getElementById("estado_pedida-" + id_produccion).value;
		
	if ((error_hay_facturas == 1) || (error_estado == 1)) {
		if (confirm ("Esta Orden de Produccion tiene asociada facturas o alguna de sus ordenes de compra estan en estado PEDIDA/RECIBIDA. ¿Desea eliminar la Orden de Produccion y las Ordenes de Compra asociadas?")) {
			window.location='elim_op.php?id=' + id_produccion;
		}
		else {
			window.location="ordenes_produccion.php?realizarBusqueda=1";
		}
	}
	else if (confirm ("¿Desea eliminar la Orden de Produccion?")) {
			window.location='elim_op.php?id=' + id_produccion;
	}
	else { 
	 	window.location="ordenes_produccion.php?realizarBusqueda=1";
	}
}
	
// Funcion que pregunta si se desea actualizar el estado de la Orden de Produccion
function validarCambioEstado(id_produccion,estado) {
	if (confirm ("¿Está seguro de actualizar el estado de la Orden de Producción?")) {
		cambiarEstadoOP(id_produccion,estado)
	}
}

// Funcion para descargar el XLS de varias Ordenes de Produccion
function descargar_XLS_OP() {
	var checkbox = document.getElementsByName('chkbox[]');
	var ids_produccion = new Array();
	var j=0;
	for (i=0;i<checkbox.length;i++){
		if (checkbox.item(i).checked == true){
			ids_produccion[j] = checkbox.item(i).value; 
			j++;
		}
	}
	window.location="informe_referencias_op_combinadas.php?ids_produccion=" + ids_produccion;
}
	
// Funcion para cambiar el estado de la Orden de Produccion
function cambiarEstadoOP(id_produccion,estado) {
	window.location='ordenes_produccion.php?id_produccion=' + id_produccion + '&realizarBusqueda=1&cambiar_estado=true&estado_anterior=' + estado;	
}
	
// Funcion que comprueba si estan seleccionadas todas las Ordenes de Produccion
// y marca o desmarca todas las OP en funcion del resultado
function TodasOP(){
	if (document.getElementById('todas_op').checked == false){
		desSeleccionarTodasOP();			
	}
	else{
		seleccionarTodasOP();
	}
}
	
// Selecciona todas las Ordenes de Produccion
function seleccionarTodasOP(){
	// Guardamos en checkboxs todos los ids de las ordenes de Produccion
	var checkbox = document.getElementsByName('chkbox[]');
	
	for (i=0;i<checkbox.length;i++){
		checkbox.item(i).checked = 'checked';
	}
}
	
// Deselecciona todas las Ordenes de Produccion
function desSeleccionarTodasOP(){
	// Guardamos en checkboxs todos los ids de las ordenes de Produccion
	var checkbox = document.getElementsByName('chkbox[]');
		
	for (i=0;i<checkbox.length;i++){
		checkbox.item(i).checked = 0;
	}
}

function cargaAlias(id_sede){
	var ajax = objetoAJAX();
	ajax.open("GET","../ajax/orden_produccion/orden_produccion.php?comp=cargaAlias&id_sede=" + id_sede,"true");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4 && ajax.status==200) {
	   		document.getElementById("capa_alias").innerHTML=ajax.responseText;
		}
	}
	ajax.send(null);
}

// Funcion para redirigir a la pagina de Iniciar Produccion
function validarInicio(id_produccion,unidades,id_producto){
    window.location='iniciar_orden_produccion.php?id_produccion=' + id_produccion + '&unidades=' + unidades + '&id_producto=' + id_producto;
}

