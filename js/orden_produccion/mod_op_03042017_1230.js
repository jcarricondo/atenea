// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el proceso de modificación de una Orden de Producción

// PERIFERICOS

// Función que muestra todos los periféricos creados
function MostrarTodosPerifericos(){
	// Obtenemos el botón de todos los periféricos y lo eliminamos
	var capaBotones = document.getElementById("CapaBotonPerifericos");
	var botonTodosPerifericos = document.getElementById("BotonTodosPerifericos");
	botonTodosPerifericos.parentNode.removeChild(botonTodosPerifericos);
	// Creamos el nuevo boton de perifericos de producción
	var boton_perifericos = document.createElement("input");
	boton_perifericos.type = "button";
	boton_perifericos.id = "BotonPerProduccion";
	boton_perifericos.name = "BotonPerProduccion";
	boton_perifericos.className = "BotonTodosComponentes";
	boton_perifericos.value = "Mostrar periféricos en producción";
	boton_perifericos.setAttribute('onclick', 'MostrarPerProduccion()');
	capaBotones.appendChild(boton_perifericos);

	// Obtenemos el buscador para que busque en todos los periféricos
	var input_buscador = document.getElementById("BuscadorPerModOP");
	input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('todos','BuscadorPerModOP','perifericos_no_asignados[]')");

	var selectPerifericos = document.getElementById("perifericos_no_asignados[]");
	for(i=0;i<selectPerifericos.length;i++) {
		var option = selectPerifericos.options[i];
		option.style.display = "block";
		if(option.id == "") option.selected = false;
	}
	BuscadorDinamicoComponentes('todos','BuscadorPerModOP','perifericos_no_asignados[]');
}

// Función que muestra sólo los periféricos en estado PRODUCCION
function MostrarPerProduccion(){
	// Obtenemos el botón de periféricos de producción y lo eliminamos
	var capaBotones = document.getElementById("CapaBotonPerifericos");
	var botonPerProduccion = document.getElementById("BotonPerProduccion");
	botonPerProduccion.parentNode.removeChild(botonPerProduccion);
	// Creamos el nuevo botón de todos los periféricos
	var boton_perifericos = document.createElement("input");
	boton_perifericos.type = "button";
	boton_perifericos.id = "BotonTodosPerifericos";
	boton_perifericos.name = "BotonTodosPerifericos";
	boton_perifericos.className = "BotonTodosComponentes";
	boton_perifericos.value = "Mostrar todos los periféricos";
	boton_perifericos.setAttribute('onclick', 'MostrarTodosPerifericos()');
	capaBotones.appendChild(boton_perifericos);

	// Obtenemos el buscador para que busque sólo los periféricos de producción
	var input_buscador = document.getElementById("BuscadorPerModOP");
	input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('produccion','BuscadorPerModOP','perifericos_no_asignados[]')");

	selectPerifericos = document.getElementById("perifericos_no_asignados[]");
	for (i = 0; i < selectPerifericos.length; i++) {
		var option = selectPerifericos.options[i];
		if (option.id == "") option.style.display = "none";
	}
	BuscadorDinamicoComponentes('produccion','BuscadorPerModOP','perifericos_no_asignados[]');
}

// Añadir elemento a la segunda lista
function AddToSecondList(){
	var fl = document.getElementById('perifericos_no_asignados[]');
	var sl = document.getElementById('perifericos[]');

	for (i = 0; i < fl.options.length; i++){
		if(fl.options[i].selected){
			if(fl.options[i].style.display === "block"){
				// Añadimos la opcion a la lista 1
				var option = document.createElement("option");
				option.value = fl[i].value;
				option.text = fl[i].text;
				fl.add(option,i);
				sl.add(fl.options[i],null);
			}
		}
	}
	return true;
}

// Eliminar elemento de la lista
function DeleteSecondListItem(){
	var sl = document.getElementById('perifericos[]');

	for (i=0; i<sl.options.length;i++){
		if(sl.options[i].selected){
			sl.remove(sl.selectedIndex);
			i--;
		}
	}
	return true;
}

// Seleccionar periféricos para POST
function SeleccionarPerifericos(){
	var lista = document.getElementById("perifericos[]");
	for	(i=0; i<lista.options.length; i++){
		lista[i].selected = "selected";
	}
}


// KITS

// Función que muestra todos los kits creados
function MostrarTodosKits(){
	// Obtenemos el botón de todos los kits y lo eliminamos
	var capaBotones = document.getElementById("CapaBotonKits");
	var botonTodosKits = document.getElementById("BotonTodosKits");
	botonTodosKits.parentNode.removeChild(botonTodosKits);
	// Creamos el nuevo botón de kits de producción
	var boton_kits = document.createElement("input");
	boton_kits.type = "button";
	boton_kits.id = "BotonKitProduccion";
	boton_kits.name = "BotonKitProduccion";
	boton_kits.className = "BotonTodosComponentes";
	boton_kits.value = "Mostrar kits en producción";
	boton_kits.setAttribute('onclick', 'MostrarKitProduccion()');
	capaBotones.appendChild(boton_kits);
	// Obtenemos el buscador para que busque en todos los kits
	var input_buscador = document.getElementById("BuscadorKitModOP");
	input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('todos','BuscadorKitModOP','kits_no_asignados[]')");

	var selectKits = document.getElementById("kits_no_asignados[]");
	for(i=0;i<selectKits.length;i++) {
		var option = selectKits.options[i];
		option.style.display = "block";
		if(option.id == "") option.selected = false;
	}
	BuscadorDinamicoComponentes('todos','BuscadorKitModOP','kits_no_asignados[]');
}

// Función que muestra sólo los kits en estado PRODUCCION
function MostrarKitProduccion(){
	// Obtenemos el botón de kits de producción y lo eliminamos
	var capaBotones = document.getElementById("CapaBotonKits");
	var botonKitProduccion = document.getElementById("BotonKitProduccion");
	botonKitProduccion.parentNode.removeChild(botonKitProduccion);
	// Creamos el nuevo botón de todos los kits
	var boton_kits = document.createElement("input");
	boton_kits.type = "button";
	boton_kits.id = "BotonTodosKits";
	boton_kits.name = "BotonTodosKits";
	boton_kits.className = "BotonTodosComponentes";
	boton_kits.value = "Mostrar todos los kits";
	boton_kits.setAttribute('onclick', 'MostrarTodosKits()');
	capaBotones.appendChild(boton_kits);
	// Obtenemos el buscador para que busque sólo los kits de producción
	var input_buscador = document.getElementById("BuscadorKitModOP");
	input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('produccion','BuscadorKitModOP','kits_no_asignados[]')");

	selectKits = document.getElementById("kits_no_asignados[]");
	for (i = 0; i < selectKits.length; i++) {
		var option = selectKits.options[i];
		if (option.id == "") option.style.display = "none";
	}
	BuscadorDinamicoComponentes('produccion','BuscadorKitModOP','kits_no_asignados[]');
}

// Añadir elemento a la segunda lista
function AddKitsToSecondList(){
	var fl = document.getElementById('kits_no_asignados[]');
	var sl = document.getElementById('kits[]');

	for(i=0;i < fl.options.length;i++){
		if(fl.options[i].selected){
			if(fl.options[i].style.display === "block"){
				// Añadimos la opcion a la lista 1
				var option = document.createElement("option");
				option.value = fl[i].value;
				option.text = fl[i].text;
				fl.add(option,i);
				sl.add(fl.options[i],null);
			}
		}
	}
	return true;
}

// Eliminar elemento de la lista
function DeleteKitsSecondListItem(){
	var sl = document.getElementById('kits[]');

	for (i=0; i<sl.options.length;i++){
		if(sl.options[i].selected){
			sl.remove(sl.selectedIndex);
			i--;
		}
	}
	return true;
}

// Seleccionar kits para POST
function SeleccionarKits(){
	var lista = document.getElementById("kits[]");
	for(i = 0; i<lista.options.length; i++){
		lista[i].selected = "selected";
	}
}




// REFERENCIAS LIBRES

// Función para abrir el buscador de Referencias Libres
function Abrir_ventana(pagina) {
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=500, top=100, left=350";
	window.open(pagina,"",opciones);
}

var div = document.getElementById('CapaTablaIframe');
var table = document.createElement('tabla');
var row = table.insertRow(0);
var cell = row.insertCell(0);
div.appendChild(table);

// Funcion para añadir referencias libres a la tabla		
function addRow(tableId,id_referencia) 	{ 
	var table = document.getElementById(tableId); 
	//Guardamos en una variable la cantidad de filas que tiene la tabla. 
	//esta variable tambien nos servira para indicar que la fila se tiene 
	//que insertar al final de la tabla.Es una ventaja que las posiciones 
	//empiecen en cero.
	var pos = table.rows.length; 
	var row = table.insertRow(pos);
	var fila = pos - 1; 
						
	var cell_0 = row.insertCell(0); 
	var cell_1 = row.insertCell(1);
	var cell_2 = row.insertCell(2);
	var cell_3 = row.insertCell(3);
	var cell_4 = row.insertCell(4);
	var cell_5 = row.insertCell(5);
	var cell_6 = row.insertCell(6);
	var cell_7 = row.insertCell(7);
	var cell_8 = row.insertCell(8);
	var cell_9 = row.insertCell(9);
	var cell_10 = row.insertCell(10);
	var cell_11 = row.insertCell(11);
	var piezas = new Array();
	var num_piezas = num_uds;
	
	cell_0.setAttribute("style","text-align:center");
	cell_5.setAttribute("style","text-align:center");
	cell_6.setAttribute("style","text-align:center");
	cell_7.setAttribute("style","text-align:center");
	cell_8.setAttribute("style","text-align:center");
	cell_9.setAttribute("style","text-align:center");
	cell_10.setAttribute("style","text-align:center");
	cell_11.setAttribute("style","text-align:center");
	
	cell_0.innerHTML = id_ref;
	cell_1.innerHTML = ref;
	cell_2.innerHTML = prov;
	cell_3.innerHTML = ref_prov;
	cell_4.innerHTML = nom_pieza;
	cell_5.innerHTML = '<input type="text" id="piezas[]" name="piezas[]" class="CampoPiezasInput" value="' + num_uds + '" onblur="javascript:validarPiezasCorrectas(' + fila + ')"/>';
	cell_6.innerHTML = pack_precio;
	cell_7.innerHTML = cant;
	cell_8.innerHTML = total_paquetes.toFixed(2);
	cell_9.innerHTML = precio_unidad.toFixed(2);
	cell_10.innerHTML = precio_referencia.toFixed(2);
	cell_11.innerHTML = '<input type="checkbox" name="chkbox" value"' + id_ref + '/>';
	
	// Calculamos el coste de todas las referencias que haya en la tabla
	costeTotal = calculaCoste(table);
	actualizarCoste(costeTotal);
}
	
// Funcion para eliminar referencias libres a la tabla	
function removeRow(tableID) {
	try {
		table = document.getElementById('mitablaRefsLibres');
		var rowCount = table.rows.length;

		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[11].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				table.deleteRow(i);
				rowCount--;
				i--;
				if (i+1 != rowCount){
					actualizarFila(table,i,rowCount);
				}
			}
		}
		costeTotal = calculaCoste(table);
		actualizarCoste(costeTotal);
	}
	catch(e) {
		alert(e);
	}
}
	
// Funcion para actualizar las filas despues de la eliminacion de una fila
// para que se puedan validar las filas despues de la modificacion
function actualizarFila(table,i,rowCount){
	for (var j=i; j<rowCount-1; j++){
		var num_piezas = table.rows[j+1].cells[5].childNodes[0].value;
		table.rows[j+1].deleteCell(5);	
		
		var td_precio = table.rows[j+1].insertCell(5);
		td_precio.setAttribute("style","text-align:center");
		td_precio.innerHTML = '<input type="text" id="piezas[]" name="piezas[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarPiezasCorrectas(' + j + ')"/>';
	}
}

// Funcion para comprobar que el valor introducido en "piezas" es un entero o decimal con punto.
// Se utiliza en la funcion validarFormulario y se le pasa la posicion de la letra de la palabra
// y la palabra a validar
function validarHayCaracter(i,a) {
	var j = 0;
	var error = false;
	var digito = 0;
	var primer_caracter = false;
	var punto_reconocido = false;
	while (j<a[i].value.length && !error){
		primer_caracter = parseInt(a[i].value[0]);
		if (isNaN(primer_caracter)) error = true;
		else {
			digito = parseInt(a[i].value[j]);
			if (isNaN(digito) && a[i].value[j] != ".") error = true;
			else if ((a[i].value[j] == "." && punto_reconocido)) error = true;
			if (a[i].value[j] == ".") punto_reconocido = true;
		}
		j++;
	}
	return error;
}

// Funcion que calcula el nuevo coste cuando se modifica el campo piezas de una referencia
// y cambia el precio de la referencia modificada
function modificaPrecioReferencia(piezas,fila){
	try {
		var table = document.getElementById('mitablaRefsLibres');
		fila = parseInt(fila) + 1;
		var nuevo_coste_referencia = 0;
		var row = table.rows[fila];
		piezas = cambiarComaPorPunto(piezas);
		piezas = parseFloat(piezas);
		piezas = piezas * 100;
		piezas = Math.round(piezas)/100;
		var cant = row.cells[7].childNodes[0].nodeValue;
		var precio_unidad = row.cells[9].childNodes[0].nodeValue;

		if (piezas < cant) {
			total_paquetes = 1;
		}
		else {
			resto = piezas % cant;
			total_paquetes = Math.floor((piezas / cant));
			if (resto != 0) {
				total_paquetes = total_paquetes + 1;
			}
		}

		precio_unidad = cambiarComaPorPunto(precio_unidad);
		precio_unidad = parseFloat(precio_unidad);
		precio_unidad = precio_unidad * 100;
		precio_unidad = Math.round(precio_unidad) / 100;
		nuevo_coste_referencia = parseFloat(piezas * precio_unidad);
		nuevo_coste_referencia = nuevo_coste_referencia * 100;
		nuevo_coste_referencia = Math.round(nuevo_coste_referencia) / 100;

		// Eliminamos la celda con el total paquetes antiguo
		table.rows[fila].deleteCell(8);

		// Creamos la celda para el precio nuevo
		var td_tot_paquetes = table.rows[fila].insertCell(8)
		td_tot_paquetes.setAttribute("style","text-align:center");
		td_tot_paquetes.innerHTML = total_paquetes.toFixed(2);

		// Eliminamos la celda con el precio antiguo
		table.rows[fila].deleteCell(10);

		var td_precio = table.rows[fila].insertCell(10);
		td_precio.setAttribute("style","text-align:center");
		td_precio.innerHTML = nuevo_coste_referencia.toFixed(2);
	}
	catch(e) {
		alert(e);
	}
}

// Funcion que calcula el coste total de las referencias de la tabla
function calculaCoste(tableId){
	try {
		table = document.getElementById('mitablaRefsLibres');
		var rowCount = table.rows.length;
		var coste_ref = 0;
		var costeTotal = 0;

		for(var i=1; i<rowCount; i++) {
			var row = table.rows[i];
			coste_ref = parseFloat(row.cells[10].childNodes[0].nodeValue);
			coste_ref = coste_ref * 100;
			coste_ref = Math.round(coste_ref) / 100;
			costeTotal = costeTotal + coste_ref;
		}
		return costeTotal;
	}
	catch(e) {
		alert(e);
	}
}

// Funcion que actualiza el coste total de la tabla
function actualizarCoste(costeTotal){
	try{
		costeTotal = parseFloat(costeTotal);
		costeTotal = costeTotal * 100;
		costeTotal = Math.round(costeTotal) / 100;
		costeTotal = costeTotal.toFixed(2);
		label_precio = document.getElementById('precio_refs_libres');
		label_precio.innerHTML = costeTotal + "€";
	}
	catch(e) {
		alert(e);
	}
}

// Funcion para validar que el campo piezas sea un number
// Si es correcto se modifica el campo precio de la referencia
function validarPiezasCorrectas(fila) {
	var table = document.getElementById('mitablaRefsLibres');
	var piezas = document.getElementsByName("piezas[]");
	var num_piezas = piezas[fila].value;
	var nuevo_coste = 0;
	var costeTotal = 0;

	var j = 0;
	var error = false;
	var digito = 0;
	var primer_caracter = false;
	var punto_reconocido = false;
	if (num_piezas.length == 0) error = true;
	while (j<num_piezas.length && !error){
		primer_caracter = parseInt(num_piezas[0]);
		if (isNaN(primer_caracter)) error = true;
		else {
			digito = parseInt(num_piezas[j]);
			if (isNaN(digito) && num_piezas[j] != ".") error = true;
			else if ((num_piezas[j] == "." && punto_reconocido)) error = true;
			if (num_piezas[j] == ".") punto_reconocido = true;
		}
		j++;
	}
	if (!error){
		modificaPrecioReferencia(num_piezas,fila);
		costeTotal = calculaCoste(table);
		actualizarCoste(costeTotal);
	}
	else {
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
	}
}


// OTRAS FUNCIONES

// Comprueba el formulario antes de pasar al siguiente punto
function validarFormulario() {
	var error0 = false;		  // Alias no comprobado
	var error1 = false;		  // Datos por introducir
	var error2 = false;		  // Piezas por introducir
	var error3 = false;		  // Piezas no son integer
	var error4 = false;		  // Unidades no puede ser 0
	var error_alias = false;  // Alias duplicado

	var tam_unidades = document.getElementById("unidades").value.length;
	var unidades = document.getElementById("unidades").value;

	var a = document.getElementsByName("piezas[]");
	// Obtenemos el input hidden alias_validado que estan dentro de la capa alias_correcto
	var alias_validado = document.getElementById('alias_validado').value;

	// Salta este error cuando el usuario escribe el alias y sin salir del foco envía el formulario.
	if (alias_validado == -1){
		error0 = true;
	}
	else if (alias_validado == 0) {
		error_alias = true;
	}
	else if(tam_unidades == 0) {
		error1 = true;
	}
	else if(unidades == 0){
		error4 = true;
	}
	else {
		var fallo = false;
		var no_es_numero = false;
		var pieza = 0;
		if (a.length != 0) {
			var i = 0;
			while ((i < a.length) && (!fallo) && (!no_es_numero)) {
				if (a[i].value.length == 0) fallo = true;
				else if (validarHayCaracter(i,a)) no_es_numero = true;
				else {
					pieza = parseFloat(a[i].value);
					if (isNaN(pieza)) no_es_numero = true;
				}
				i++;
			}
		}
		if (fallo) error2 = true;
		if (no_es_numero) error3 = true;
	}

	if (error0){
		alert("ERROR: No se ha podido comprobar el ALIAS a la hora de enviar el formulario");
		return false;
	}
	else if (error1) {
		alert("Rellene los campos obligatorios");
		return false;
	}
	else if (error2) {
		alert("Rellene el campo PIEZAS de todas las referencias");
		return false;
	}
	else if (error3){
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
		return false;
	}
	else if (error4){
		alert("El campo no puede ser 0");
		return false;
	}
	else if (error_alias){
		alert("El alias ya existe en la base de datos");
		return false;
	}
	else {
		SeleccionarPerifericos();
		SeleccionarKits();
		return true;
	}
}

// Funcion para cambiar la coma de un numero decimal por un punto para su validacion
function cambiarComaPorPunto(p_precio){
	tamaño_float = p_precio.length;
	i=0;
	cadena = "";
	while (i<tamaño_float) {
		if(p_precio[i] == ","){
			cadena = cadena + ".";
		}
		else {
			cadena = cadena + p_precio[i];
		}
		i++;
	}
	p_precio = cadena;
	return p_precio;
}

// Funcion que solo permite insertar numeros en los input
function soloNumeros (e) {
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==8) return true; // 3
	patron =/\d/;
	te = String.fromCharCode(tecla);
	return patron.test(te);
}

<!-- AJAX -->
// Comprueba al vuelo si el alias introducido por el usuario esta guardado en la base de datos
function comprobarAliasCorrecto(){
	var alias = document.getElementById('alias_op').value;
	var id_produccion = document.getElementById('id_produccion').value;
	document.getElementById("alias_correcto").innerHTML = "Comprobando..." + '<input type="hidden" id="alias_validado" name="alias_validado" value="-1"/>';
	var ajax = objetoAJAX();
	ajax.open("GET","../funciones/comprobaciones.php?comp=mod_comprueba_alias&alias=" + alias + '&id_produccion=' + id_produccion,"true");

	ajax.onreadystatechange=function() {
		if (ajax.readyState==4 && ajax.status==200) {
			document.getElementById("alias_correcto").innerHTML=ajax.responseText;
		}
	}
	ajax.send(null);
}



/*
// Funcion para comprobar que el campo introducido es un numero	
function noEsNumero(numero) {
	var cont = 0;
	var error = false;
	var digito = 0;
	while (cont<numero.length && !error){
		digito = parseInt(numero[cont]);
		error = (isNaN(digito));
		cont++;
	}
	return error;
}
*/