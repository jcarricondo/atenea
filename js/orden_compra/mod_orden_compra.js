// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en la modificación de una orden de compra

// Función para que guarda las direcciones de entrega y facturacion y llama al proceso de generación de la fra_request
function abrirFactura(id_compra,nombre_factura,fecha,telefono_entrega,telefono_facturacion,cif_entrega,cif_facturacion,localidad_entrega,localidad_facturacion,cp_entrega,cp_facturacion,provincia_entrega,provincia_facturacion,precio_total) {
	if(document.getElementById("con_precios").checked) {
		mostrar_precios = 1;
	} else {
		mostrar_precios = 0;
	}
	
	var i_opt_entrega = document.getElementById("direccion_entrega").selectedIndex;
	var i_opt_facturacion = document.getElementById("direccion_facturacion").selectedIndex;
	var id_dir_entrega = document.getElementById("direccion_entrega").options[i_opt_entrega].value;
	var id_dir_facturacion = document.getElementById("direccion_facturacion").options[i_opt_facturacion].value;
	
	direccion_entrega = document.getElementById("direccion_entrega").value;
	direccion_facturacion = document.getElementById("direccion_facturacion").value;
	
	// Se genera la factura y se guardan las direcciones seleccionadas en la base de datos 
	window.location.href='fra_request.php?id_compra='+id_compra+'&mostrar_precios='+mostrar_precios+'&nombre_factura='+nombre_factura+'&fecha='+fecha+'&direccion_entrega='+direccion_entrega+'&direccion_facturacion='+direccion_facturacion+'&telefono_entrega='+telefono_entrega+'&telefono_facturacion='+telefono_facturacion+'&cif_entrega='+cif_entrega+'&localidad_entrega='+localidad_entrega+'&localidad_facturacion='+localidad_facturacion+'&cp_entrega='+cp_entrega+'&cp_facturacion='+cp_facturacion+'&provincia_entrega='+provincia_entrega+'&provincia_facturacion='+provincia_facturacion+'&precio_total='+precio_total;
}


// Funcion para cambiar la coma de un numero decimal por un punto para su validacion
function cambiarComaPorPunto(precio){
	tamaño_float = precio.length;
	i=0;
	cadena = "";
	while (i<tamaño_float) {
		if(precio[i] == ","){
			cadena = cadena + ".";
		}
		else {
			cadena = cadena + precio[i];
		}
		i++;	
	}
	precio = cadena;
	return precio;
}	
	
// Funcion para validar que el campo tasas sea un number
function validarHayCaracter() {
	var precio_tasas = document.getElementById('precio_tasas').value;
	var j = 0;
	var error = false;
	var digito = 0;
	var primer_caracter = false;
	var punto_reconocido = false;
	if (precio_tasas.length == 0) error = true;
	while (j<precio_tasas.length && !error){
		primer_caracter = parseInt(precio_tasas[0]);
		if (isNaN(primer_caracter)) error = true;
		else {
			digito = parseInt(precio_tasas[j]);
			if (isNaN(digito) && precio_tasas[j] != ".") error = true;
			else if ((precio_tasas[j] == "." && punto_reconocido)) error = true;
			if (precio_tasas[j] == ".") punto_reconocido = true;
 		}
		j++;
	}
	if (!error){
		costeTotal = calculaCosteTotal(precio_tasas);
		actualizarCoste(costeTotal);
	}
	else {
		alert("El campo TASAS tiene que ser un valor entero o un decimal con punto");	
	}
}
	
// Funcion que calcula el coste total de las referencias de la tabla cabina (sin las interfaces)
function calculaCosteTotal(precio_tasas){
	try {
		var precio = document.getElementById('precio').value;
		precio = cambiarComaPorPunto(precio);
		precio = parseFloat(precio);
		precio = precio * 100;
		precio = Math.round(precio) / 100;
		precio_tasas = parseFloat(precio_tasas);
		precio_tasas = precio_tasas * 100;
		precio_tasas = Math.round(precio_tasas) / 100;
		var costeTotal = precio + precio_tasas;
		return costeTotal;
	}
	catch(e) {
		alert(e);
	}
}

// Funcion que actualiza el coste total de la orden de compra en funcion de las tasas 
function actualizarCoste(costeTotal){
	try{
		costeTotal = costeTotal * 100;
		costeTotal = Math.round(costeTotal) / 100;
		costeTotal = costeTotal.toFixed(2);
		precio_total = document.getElementById('precio_total');
		precio_total.innerHTML = costeTotal + " €";
	}
	catch(e) {
		alert(e);
	}
}	

// Quita las facturas de la tabla
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
	}catch(e) {
		alert(e);
	}
}

// Quita los adjuntos de la tabla
function removeRowAdjuntos(tableID) {
	try {
		table = document.getElementById('mitablaAdjuntos');
		var rowCount = table.rows.length;
		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[2].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				table.deleteRow(i);
				rowCount--;
				i--;
			}
		}
	}catch(e) {
		alert(e);
	}
}


// Esta es una variable de control para mantener nombres diferentes de cada campo creado dinamicamente.
var numero = 0; 
// Esta funcion nos devuelve el tipo de evento disparado
evento = function (evt) { 
	return (!evt) ? event : evt;
}
// Esta funcion crea dinamicamente los nuevos campos file
addCampo = function () { 
	//Creamos un nuevo div para que contenga el nuevo campo
   	nDiv = document.createElement('div');
	//con esto se establece la clase de la div
   	nDiv.className = 'ContenedorCamposAdjuntarFacturas';
	//este es el id de la div, aqui la utilidad de la variable numero
	//nos permite darle un id unico
  	nDiv.id = 'file' + (++numero);
	//creamos el input para el formulario:
   	nCampo = document.createElement('input');
	//le damos un nombre, es importante que lo nombren como vector, pues todos los campos
	//compartiran el nombre en un arreglo, asi es mas facil procesar posteriormente con php
   	nCampo.name = 'archivos[]';
	//Establecemos el tipo de campo
   	nCampo.type = 'file';
	//Establecemos la clase del campo
   	nCampo.className = 'BotonAdjuntar';

	//creamos el label:
   	nLabel = document.createElement('label');
	//Establecemos la clase del label
   	nLabel.className = 'LabelImporte';
	nLabel.innerHTML = 'IMPORTE';

	//creamos el input para el formulario:
   	nCampo1 = document.createElement('input');
	//le damos un nombre, es importante que lo nombren como vector, pues todos los campos
	//compartiran el nombre en un array, asi es mas facil procesar posteriormente con php
   	nCampo1.name = 'importe[]';
	//Establecemos el tipo de campo
   	nCampo1.type = 'text';
	//Establecemos la clase del campo
   	nCampo1.className = 'CreacionBasicoInput';	
		
	//Ahora creamos un link para poder eliminar un campo que ya no deseemos
   	a = document.createElement('a');
	//El link debe tener el mismo nombre de la div padre, para efectos de localizarla y eliminarla
   	a.name = nDiv.id;
	//Este link no debe ir a ningun lado
   	a.href = '#';
	//Establecemos que dispare esta funcion en click
   	a.onclick = elimCamp;
	//Con esto ponemos el texto del link
   	a.innerHTML = 'ELIMINAR';
    a.className = 'EliminarAdjuntar';
   
	//Bien es el momento de integrar lo que hemos creado al documento,
	//primero usamos la función appendChild para adicionar el campo file nuevo
   	nDiv.appendChild(nCampo);
	//segundo usamos la función appendChild para adicionar el label nuevo
   	nDiv.appendChild(nLabel);
	//tercero usamos la función appendChild para adicionar el campo file nuevo
   	nDiv.appendChild(nCampo1);	
	//Adicionamos el Link
   	nDiv.appendChild(a);
	//Ahora si recuerdan, en el html hay una div cuyo id es 'adjuntos', bien
	//con esta función obtenemos una referencia a ella para usar de nuevo appendChild
	//y adicionar la div que hemos creado, la cual contiene el campo file con su link de eliminación:
   	container = document.getElementById('adjuntos');
   	container.appendChild(nDiv);
}

// Con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCamp = function (evt){
	evt = evento(evt);
	nCampo = rObj(evt);
	div = document.getElementById(nCampo.name);
	div.parentNode.removeChild(div);
}

// Con esta función recuperamos una instancia del objeto que disparo el evento
rObj = function (evt) { 
	return evt.srcElement ?  evt.srcElement : evt.target;
}


// ARCHIVOS ADJUNTOS
// Esta es una variable de control para mantener nombres diferentes de cada campo creado dinamicamente.
var numeroAd = 0; 
// Esta funcion nos devuelve el tipo de evento disparado
eventoAdjunto = function (evtAd) { 
	return (!evtAd) ? event : evtAd;
}
// Esta funcion crea dinamicamente los nuevos campos file
addCampoAdjunto = function () { 
	//Creamos un nuevo div para que contenga el nuevo campo
   	nDivAd = document.createElement('div');
	//con esto se establece la clase de la div
   	nDivAd.className = 'ContenedorCamposArchivosAdjuntos';
	//este es el id de la div, aqui la utilidad de la variable numero
	//nos permite darle un id unico
  	nDivAd.id = 'fileAdjunto' + (++numeroAd);
	//creamos el input para el formulario:
   	nCampoAd = document.createElement('input');
	//le damos un nombre, es importante que lo nombren como vector, pues todos los campos
	//compartiran el nombre en un array, asi es mas facil procesar posteriormente con php
   	nCampoAd.name = 'archivos_adjuntos[]';
	//Establecemos el tipo de campo
   	nCampoAd.type = 'file';
	//Establecemos la clase del campo
   	nCampoAd.className = 'BotonAdjuntarAdjuntos';
	
		
	//Ahora creamos un link para poder eliminar un campo que ya no deseemos
   	aAd = document.createElement('a');
	//El link debe tener el mismo nombre de la div padre, para efectos de localizarla y eliminarla
   	aAd.name = nDivAd.id;
	//Este link no debe ir a ningun lado
   	aAd.href = '#';
	//Establecemos que dispare esta funcion en click
   	aAd.onclick = elimCampAd;
	//Con esto ponemos el texto del link
   	aAd.innerHTML = 'ELIMINAR';
    aAd.className = 'EliminarAdjuntar';
   
	//Bien es el momento de integrar lo que hemos creado al documento,
	//primero usamos la función appendChild para adicionar el campo file nuevo
   	nDivAd.appendChild(nCampoAd);
	//Adicionamos el Link
   	nDivAd.appendChild(aAd);
	//Ahora si recuerdan, en el html hay una div cuyo id es 'ArchivosAdjuntos', bien
	//con esta función obtenemos una referencia a ella para usar de nuevo appendChild
	//y adicionar la div que hemos creado, la cual contiene el campo file con su link de eliminación:
   	containerAd = document.getElementById('ArchivosAdjuntos');
   	containerAd.appendChild(nDivAd);
}

// Con esta función eliminamos el campo cuyo link de eliminación sea presionado
elimCampAd = function (evtAd){
	evtAd = eventoAdjunto(evtAd);
	nCampoAd = rObjAd(evtAd);
	divAd = document.getElementById(nCampoAd.name);
	divAd.parentNode.removeChild(divAd);
}

// Con esta función recuperamos una instancia del objeto que disparo el evento
rObjAd = function (evtAd) { 
	return evtAd.srcElement ?  evtAd.srcElement : evtAd.target;
}