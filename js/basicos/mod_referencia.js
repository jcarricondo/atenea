// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el proceso de modificación de una referencia

// Función que elimina de la tabla ficheros adjuntos
function removeRow(tableID) {
	try {
		table = document.getElementById('mitabla');
		var rowCount = table.rows.length;
		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[3].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				table.deleteRow(i);
				rowCount--;
				i--;
			}
		}
	}
	catch(e) {
		alert(e);
	}
}

var numero = 0; // Esta es una variable de control para mantener nombres diferentes de cada campo creado dinamicamente.

evento = function (evt) { // esta funcion nos devuelve el tipo de evento disparado
			return (!evt) ? event : evt;
		}

// Esta función crea dinámicamente los nuevos campos file
addCampo = function () { 
// Creamos un nuevo div para que contenga el nuevo campo
nDiv = document.createElement('div');
// Establecemos la clase del div
nDiv.className = 'ContenedorCamposAdjuntar';
// Damos un id único al div
nDiv.id = 'file' + (++numero);
// Creamos el input para el formulario:
nCampo = document.createElement('input');
// Damos un nombre como un vector para que todos los campos compartan el nombre como array
// Asi es más facil procesar los datos con php
nCampo.name = 'archivos[]';
// Establecemos el tipo de campo como archivo
nCampo.type = 'file';
// Establecemos la clase del campo
nCampo.className = 'BotonAdjuntar';
// Ahora creamos un link para poder eliminar un campo que ya no deseemos
a = document.createElement('a');
// El link debe tener el mismo nombre del div padre para poder localizarla
a.name = nDiv.id;
// Este link no debe ir a ningun lado
a.href = '#';
// Establecemos que dispare esta función en click
a.onclick = elimCamp;
// Con esto ponemos el texto del link
a.innerHTML = 'ELIMINAR';
a.className = 'EliminarAdjuntar';
// Añadimos el campo file nuevo
nDiv.appendChild(nCampo);
// Añadimos el link
nDiv.appendChild(a);
// Obtenemos el div "adjuntos" y añadimos el nuevo div creado que contiene el campo file con el link de eliminación
container = document.getElementById('adjuntos');
container.appendChild(nDiv);
}

// Función para eliminar el campo adjunto cuando se presiona el link de eliminación
elimCamp = function (evt){
	evt = evento(evt);
   	nCampo = rObj(evt);
   	div = document.getElementById(nCampo.name);
   	div.parentNode.removeChild(div);
}

// Función para recuperar la instancia del objeto que disparó el evento
rObj = function (evt) { 
	return evt.srcElement ?  evt.srcElement : evt.target;
}

// Función para rellenar el campo "tipo pieza" en el caso de que sea un ordenador
function rellenarTipoPieza(){
	// Si esta seleccionado rellenamos el campo "tipo pieza" con "ORDENADOR"
	var checkbox = document.getElementById('es_ordenador');
	var tipo_pieza = document.getElementById('tipo_pieza');
	var tipo_pieza_ant = document.getElementById('tipo_pieza_ant').value;
	if (checkbox.checked){
		// Insertamos el texto ORDENADOR
		tipo_pieza.value = "ORDENADOR";
		tipo_pieza.setAttribute("readonly","readonly");
	}
	else{
		// Eliminamos el texto ORDENADOR y ponemos el que estaba cargado en un principio. 
		tipo_pieza.value = tipo_pieza_ant;
		tipo_pieza.removeAttribute("readonly");
	}
}


// Función para añadir una referencia heredada
function addRowHeredada(tableId,id_referencia){
	// Comprobamos que no se esta intentando añadir una referencia ancestro
	var error_ancestro = validarAncestro(id_referencia);
	if(!error_ancestro) {
		// Comprobamos que no se esta intentado añadir una referencia repetida
		var error_heredera_repetida = validarHeredadasRepetida(id_referencia);
		if(!error_heredera_repetida) {
			var table = document.getElementById(tableId);
			// Guardamos en una variable la cantidad de filas que tiene la tabla.
			// Esta variable también nos servirá para indicar que la fila se tiene
			// que insertar al final de la tabla.
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

			var piezas = new Array();
			var num_piezas = num_uds;

			cell_0.setAttribute("style", "text-align:center");
			cell_1.setAttribute("id", "enlaceComposites");
			cell_5.setAttribute("style", "text-align:center");
			cell_5.setAttribute("id", "celda_campo_piezas-" + fila);
			cell_6.setAttribute("style", "text-align:center");
			cell_7.setAttribute("style", "text-align:center");
			cell_8.setAttribute("style", "text-align:center");
			cell_9.setAttribute("style", "text-align:center");
			cell_10.setAttribute("style", "text-align:center");

			cell_0.innerHTML = id_ref;
			cell_1.innerHTML = ref;
			cell_2.innerHTML = prov;
			cell_3.innerHTML = ref_prov;
			cell_4.innerHTML = nom_pieza;
			cell_5.innerHTML = '<input type="text" id="piezas[]" name="piezas[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarHayCaracter(' + fila + ')"/>';
			cell_6.innerHTML = pack_precio.toFixed(2);
			cell_7.innerHTML = cant.toFixed(2);
			cell_8.innerHTML = precio_unidad.toFixed(2);
			cell_9.innerHTML = precio_referencia.toFixed(2);
			cell_10.innerHTML = '<input type="checkbox" name="chkbox" value"' + id_ref + '"/>';
		}
		else{
			alert("ERROR: Ya se ha añadido la referencia heredada a la tabla")
		}
	}
	else {
		alert("ERROR: No se puede insertar una referencia como heredera que es a la vez referencia antecesor de la referencia principal.");
	}
}

// Función para eliminar una referencia heredada
function removeRowHeredada(tableID) {
	try {
		table = document.getElementById('mitablaHeredadas');
		var rowCount = table.rows.length;

		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[10].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				table.deleteRow(i);
				rowCount--;
				i--;
				if (i+1 != rowCount){
					actualizarFilaHeredada(table,i,rowCount);
				}
			}
		}
	}
	catch(e) {
		alert(e);
	}
}

// Función para actualizar las filas después de la eliminación de una fila
function actualizarFilaHeredada(table,i,rowCount){
	for (var j=i; j<rowCount-1; j++){
		var num_piezas = table.rows[j+1].cells[5].childNodes[0].value;
		table.rows[j+1].deleteCell(5);
		var td_precio = table.rows[j+1].insertCell(5);
		td_precio.setAttribute("style","text-align:center");
		td_precio.innerHTML = '<input type="text" id="piezas[]" name="piezas[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarHayCaracter(' + j + ')"/>';
	}
}

// Función para validar que el campo piezas sea un number
// Si es correcto se modifica el campo precio de la referencia
function validarHayCaracter(fila) {
	var table = document.getElementById('mitablaHeredadas');
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
	}
	else {
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
	}
}

// Función que calcula el nuevo coste cuando se modifica el campo piezas de una referencia
// Cambia el precio de la referencia modificada
function modificaPrecioReferencia(piezas,fila){
	try {
		var table = document.getElementById('mitablaHeredadas');
		fila = parseInt(fila) + 1;
		var nuevo_coste_referencia = 0;
		var row = table.rows[fila];
		piezas = cambiarComaPorPunto(piezas);
		piezas = parseFloat(piezas);
		piezas = piezas * 100;
		piezas = Math.round(piezas)/100;
		var precio_unidad = row.cells[8].childNodes[0].nodeValue;
		precio_unidad = cambiarComaPorPunto(precio_unidad);
		precio_unidad = parseFloat(precio_unidad);
		precio_unidad = precio_unidad * 100;
		precio_unidad = Math.round(precio_unidad) / 100;
		nuevo_coste_referencia = parseFloat(piezas * precio_unidad);
		nuevo_coste_referencia = nuevo_coste_referencia * 100;
		nuevo_coste_referencia = Math.round(nuevo_coste_referencia) / 100;

		// Eliminamos la celda con el precio antiguo
		table.rows[fila].deleteCell(9);

		// Creamos la celda para el precio nuevo
		var td_precio = table.rows[fila].insertCell(9);
		// Establecemos el estilo
		td_precio.setAttribute("style","text-align:center");
		td_precio.innerHTML = nuevo_coste_referencia.toFixed(2);
	}
	catch(e) {
		alert(e);
	}
}

// Función para cambiar la coma de un número decimal por un punto para su validación
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

// Función que determina si se esta intentando añadir una referencia ancestro como referencia heredera
function validarAncestro(id_referencia){
	var referencias_ancestros = document.getElementsByName("REFS_ANCESTRO[]");
	var encontrado = false;
	var i=0;
	while (i < referencias_ancestros.length && !encontrado){
		id_ref_ancestro = referencias_ancestros[i].value;
		encontrado = id_referencia == id_ref_ancestro;
		i++;
	}
	return encontrado;
}

// Función que determina si se esta intentando añadir una referencia que ya haya sido añadida a la tabla
function validarHeredadasRepetida(id_referencia){
	var referencias_herederas = document.getElementsByName("REFS[]");
	var encontrado = false;
	var i=0;
	while (i < referencias_herederas.length && !encontrado){
		id_ref_heredera = referencias_herederas[i].value;
		encontrado = id_referencia == id_ref_heredera;
		i++;
	}
	return encontrado;
}


// Función para añadir una referencia compatible
function addRowCompatible(tableId,id_referencia){
	// Comprobamos que no se esta intentado añadir una referencia repetida
	var error_compatible_repetida = validarCompatiblesRepetida(id_referencia);
	if(!error_compatible_repetida) {
		var table = document.getElementById(tableId);
		// Guardamos en una variable la cantidad de filas que tiene la tabla.
		// Esta variable también nos servirá para indicar que la fila se tiene
		// que insertar al final de la tabla.
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

		cell_0.setAttribute("style", "text-align:center");
		cell_1.setAttribute("style", "text-align:center");
		cell_2.setAttribute("style", "text-align:center");
		cell_7.setAttribute("style", "text-align:center");
		cell_8.setAttribute("style", "text-align:center");
		cell_9.setAttribute("style", "text-align:center");
		cell_10.setAttribute("style", "text-align:center");
		cell_11.setAttribute("style", "text-align:center");

		cell_0.innerHTML = id_grupo;
		cell_1.innerHTML = fecha_grupo;
		cell_2.innerHTML = id_ref;
		cell_3.innerHTML = ref;
		cell_4.innerHTML = prov;
		cell_5.innerHTML = ref_prov;
		cell_6.innerHTML = nom_pieza;
		cell_7.innerHTML = pack_precio.toFixed(2);
		cell_8.innerHTML = cant.toFixed(2);
		cell_9.innerHTML = precio_unidad.toFixed(2);
		cell_10.innerHTML = precio_referencia.toFixed(2);
		cell_11.innerHTML = '<input type="checkbox" name="chkbox_comp" value"' + id_ref + '"/>';
	}
	else{
		alert("ERROR: Ya se ha añadido la referencia compatible a la tabla")
	}
}

// Función para eliminar una referencia compatible
function removeRowCompatible(tableID) {
	try {
		table = document.getElementById('mitablaCompatibles');
		var rowCount = table.rows.length;

		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[11].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				table.deleteRow(i);
				rowCount--;
				i--;
				if (i+1 != rowCount){
					// actualizarFilaHeredada(table,i,rowCount);
				}
			}
		}
	}
	catch(e) {
		alert(e);
	}

}

// Función que determina si se esta intentando añadir una referencia que ya haya sido añadida a la tabla
function validarCompatiblesRepetida(id_referencia){
	var referencias_compatibles = document.getElementsByName("REFS_COMP[]");
	var encontrado = false;
	var i=0;
	while (i < referencias_compatibles.length && !encontrado){
		id_ref_compatible = referencias_compatibles[i].value;
		encontrado = id_referencia == id_ref_compatible;
		i++;
	}
	return encontrado;
}

// Función que limpia toda la tabla de referencias heredadas de la referencia
function quitarHeredadas(tablaHeredadas){
	try {
		var rowCount = tablaHeredadas.rows.length;
		if(rowCount != 1) {
			if (confirm("Se eliminarán todas las referencias de la tabla. ¿Desea continuar?")) {
				for (var i = 1; i < rowCount; i++) {
					var row = tablaHeredadas.rows[i];
					tablaHeredadas.deleteRow(i);
					rowCount--;
					i--;
				}
			}
		}
	}
	catch(e) {
		alert(e);
	}
}

// Función que limpia toda la tabla de referencias compatibles con la referencia
function quitarCompatibilidad(tablaComp){
	try {
		var rowCount = tablaComp.rows.length;
		if(rowCount != 1) {
			if(confirm("Se eliminarán todas las referencias de la tabla. ¿Desea continuar?")){
				for (var i = 1; i < rowCount; i++) {
					var row = tablaComp.rows[i];
					tablaComp.deleteRow(i);
					rowCount--;
					i--;
				}
			}
		}
	}
	catch (e) {
		alert(e);
	}
}

// Función para comprobar que el valor introducido en "piezas" es un entero o decimal con punto.
// Se utiliza en la funcion validarFormulario y se le pasa la posición de la letra de la palabra
// y la palabra a validar
function validarPiezasHeredadas(i,a) {
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


// Comprueba el formulario antes de pasar al siguiente punto
function validarFormulario() {

	var error_no_hay_piezas = false;		// Piezas por introducir
	var error_tipo_piezas = false;	      	// Piezas no es tipo number

	var a = document.getElementsByName("piezas[]");
	var fallo = false;
	var no_es_numero = false;
	var pieza = 0;
	if (a.length != 0) {
		var i = 0;
		while ((i < a.length) && (!fallo) && (!no_es_numero)) {
			if (a[i].value.length == 0) fallo = true;
			else if (validarPiezasHeredadas(i,a)) no_es_numero = true;
			else {
				pieza = parseFloat(a[i].value);
				if (isNaN(pieza)) no_es_numero = true;
			}
			i++;
		}
	}
	if (fallo) error_no_hay_piezas = true;
	if (no_es_numero) error_tipo_piezas = true;

	if (error_no_hay_piezas) {
		alert("Rellene el campo PIEZAS de todas las referencias heredadas");
		return false;
	}
	else if (error_tipo_piezas){
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
		return false;
	}
	else {
		return true;
	}
}
