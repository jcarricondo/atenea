// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el listado de Ordenes de Compra

// Función que comprueba si estan seleccionadas todas las Ordenes de Compra y marca o desmarca todas las OC en función del resultado
function TodasOC(){
	if (document.getElementById('todas_oc').checked == false){
		desSeleccionarTodasOC();			
	}
	else{
		seleccionarTodasOC();
	}
}
	
// Selecciona todas las Ordenes de Compra
function seleccionarTodasOC(){
	// Guardamos en checkboxs todos los ids de las órdenes de Compra
	var checkbox = document.getElementsByName('chkbox[]');
	for (i=0;i<checkbox.length;i++){
		checkbox.item(i).checked = 'checked';
	}
}
	
// Deselecciona todas las Ordenes de Compra
function desSeleccionarTodasOC(){
	// Guardamos en checkboxs todos los ids de las órdenes de Producción
	var checkbox = document.getElementsByName('chkbox[]');
	for (i=0;i<checkbox.length;i++){
		checkbox.item(i).checked = 0;
	}
}
	
// Función para descargar el XLS de varias Ordenes de Compra
function descargar_XLS_OC() {
	var checkbox = document.getElementsByName('chkbox[]');
	var ids_compra = new Array();
	var j=0;
	for (i=0;i<checkbox.length;i++){
		if (checkbox.item(i).checked == true){
			ids_compra[j] = checkbox.item(i).value; 
			j++;
		}
	}
	window.location="informe_oc_combinadas.php?ids_compra=" + ids_compra;
}

// Obtiene los ids de las Ordenes de Compra de la descarga multiple
function descarga_multiple() {
	var checkbox = document.getElementsByName('chkbox[]');
	var ids_compra = new Array();
	var j=0;
	for (i=0;i<checkbox.length;i++){
		if (checkbox.item(i).checked == true){
			ids_compra[j] = checkbox.item(i).value; 
			j++;
		}
	}

	// Limitamos el numero de ordenes de compra a descargar
	if (ids_compra.length > 50){
		alert("ERROR: El numero de órdenes de compra seleccionadas debe ser inferior a 50");
	}
	else if (ids_compra.length > 25){
		alert("Esta operación puede tardar varios minutos. Por favor, manténgase a la espera");
		window.location="ordenes_compra.php?ids_compra=" + ids_compra + "&OCompra=multiple";
	}
	else{	
		window.location="ordenes_compra.php?ids_compra=" + ids_compra + "&OCompra=multiple";
	}
}


// Función para el envío de emails al proveedor
function enviarEmailPedidos() {
	var checkbox = document.getElementsByName('chkbox[]');
	var ids_compra = new Array();
	var j=0;
	for (i=0;i<checkbox.length;i++){
		if (checkbox.item(i).checked == true){
			ids_compra[j] = checkbox.item(i).value; 
			j++;
		}
	}
	if(ids_compra.length == 0) {
		alert("No se ha seleccionado ninguna orden de compra");
	} else {
		if(confirm("¿Quieres proceder al envío de emails de pedido?")) {
			window.location="envio_pedidos.php?ids_compra=" + ids_compra;
		}
	}
}
	
// Función para cambiar el estado de varias órdenes de compra seleccionadas
function cambiar_estado_OC() {
	// Creamos los arrays para guardar las órdenes de compra seleccionadas
	// Obtenemos todos los inputs de las órdenes de compra
	var ids_compra = new Array();
	var fecha_entrega_vuelo = new Array();
	var checkbox = document.getElementsByName('chkbox[]');
	var estado_OC = document.getElementById('estado_OC').value;		
	var fecha_entrega = document.getElementsByName('fecha_entrega_vuelo[]');

	// Guardamos en un array los ids de las órdenes de compra seleccionadas y en otro las fechas de entrega 	
	var j=0;
	for (i=0;i<checkbox.length;i++){
		if (checkbox.item(i).checked == true){
			ids_compra[j] = checkbox.item(i).value;
			fecha_entrega_vuelo[j] = fecha_entrega.item(i).value;
			j++;
		}
	}
	window.location="ordenes_compra.php?OCompra=cambiar_estado&ids_compra=" + ids_compra + "&estado_modificado=" + estado_OC + "&fecha_entrega_vuelo=" + fecha_entrega_vuelo;
}

// Funcion para mostrar un mensaje informativo con las Ordenes de Compra que no han sido modificadas en cambio masivo de OC.
// Estas ordenes compra corresponden a las que pasan de transicion de GEN a REC o de REC a GEN
function muestraOCnoModificadas(){
	
	var nombres = new Array();
	var mensaje = 'Las Ordenes de Compra:\n\n'
	
	nombres_oc = document.getElementsByName('oc_no_modificadas[]');
	for (i=0;i<nombres_oc.length;i++){
		nombres[i] = nombres_oc.item(i).value;
		mensaje = mensaje + nombres[i] + '\n';
	}
	mensaje = mensaje + '\n no han sido actualizadas';
	alert(mensaje);	
}


// Controlar el numero de caracteres maximo del textarea 
function maximaLongitud(texto,maxlong) {
	var tecla, in_value, out_value;

	if (texto.value.length > maxlong) {
		in_value = texto.value;
		out_value = in_value.substring(0,maxlong);
		texto.value = out_value;
		return false;
	}
	return true;
}

// Funcion que carga las OPs al cambiar de Sede el ADMIN GLOBAL
function cargaOPsPorSede(id_sede){
	// Hacemos la llamada a la funcion AJAX para la carga de las OPs segun la sede
	var ajax = objetoAJAX();
	
	ajax.open("GET","../ajax/orden_compra/orden_compra.php?comp=cargaOpsPorSede&id_sede=" + id_sede,"true");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4 && ajax.status==200) {
			document.getElementById("fila_cambios_sede").innerHTML=ajax.responseText;
		}
	}
	ajax.send(null);
}

