// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el proceso de confirmación de la modificación de una Orden de Producción de la cabina

var div = document.getElementById('CapaTablaIframe');
var table = document.createElement('tabla');
var row = table.insertRow(0);
var cell = row.insertCell(0);
div.appendChild(table);
	
// Funcion para añadir referencias a la cabina
function addRow(tableId,id_referencia){ 
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
	cell_5.innerHTML = '<input type="text" id="piezas_cabina[]" name="piezas_cabina[]" class="CampoPiezasInput" value="' + num_uds + '" onblur="javascript:validarPiezasCorrectas(' + fila + ')"/>';
	cell_6.innerHTML = pack_precio;
	cell_7.innerHTML = cant;  
	cell_8.innerHTML = total_paquetes.toFixed(2);
	cell_9.innerHTML = precio_unidad.toFixed(2);
	cell_10.innerHTML = precio_referencia.toFixed(2);
	cell_11.innerHTML = '<input type="checkbox" name="chkbox" value"' + id_ref + '/>';
		
	// Calculamos el coste de todas las referencias que haya en la tabla
	costeTotal = calculaCoste(table);
	// Actualiza el coste de la cabina
	actualizarCoste(costeTotal);
	/*
	// Actualiza el coste de la cabina sumando sus kits
	// actualizarCosteTotalCabina(costeTotal);
	*/
}
	
// Funcion para eliminar referencias de la cabina
function removeRow(tableID) {
	try {
		table = document.getElementById('mitablaCabina');
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
		// Actualiza el coste de la cabina
		actualizarCoste(costeTotal);
		/*
		// Actualiza el coste de la cabina sumando sus kits
		actualizarCosteTotalCabina(costeTotal);
		*/
	}catch(e) {
		alert(e);
	}
}
	
// Funcion para validar que el campo piezas sea un number
// Si es correcto se modifica el campo precio de la referencia
function validarPiezasCorrectas(fila) {
	var table = document.getElementById('mitablaCabina');
	var piezas = document.getElementsByName("piezas_cabina[]");
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
		// Actualiza el coste de la cabina
		actualizarCoste(costeTotal);
		// Actualiza el coste de la cabina sumando sus kits
		actualizarCosteTotalCabina(costeTotal);
	}
	else {
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");	
	}
}
	
// Funcion para actualizar las filas despues de la eliminacion de una fila
// para que se puedan validar las filas despues de la modificacion
function actualizarFila(table,i,rowCount){
	for (var j=i; j<rowCount-1; j++){
		var num_piezas = table.rows[j+1].cells[5].childNodes[0].value;
		table.rows[j+1].deleteCell(5);	
		//table.rows[j+1].insertCell(4).innerHTML = '<input type="text" id="piezas_cabina[]" name="piezas_cabina[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarPiezasCorrectas(' + j + ')"/>';
		
		var td_precio = table.rows[j+1].insertCell(5);
		td_precio.setAttribute("style","text-align:center");
		td_precio.innerHTML = '<input type="text" id="piezas_cabina[]" name="piezas_cabina[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarPiezasCorrectas(' + j + ')"/>';
	}
}	
	
// Funcion que calcula el nuevo coste cuando se modifica el campo piezas de una referencia 
// y cambia el precio de la referencia modificada
function modificaPrecioReferencia(piezas,fila){
	try {
		var table = document.getElementById('mitablaCabina');
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
		//table.rows[fila].insertCell(7).innerHTML = total_paquetes.toFixed(2);
		
		// Creamos la celda para el precio nuevo
		var td_tot_paquetes = table.rows[fila].insertCell(8)
		td_tot_paquetes.setAttribute("style","text-align:center");
		td_tot_paquetes.innerHTML = total_paquetes.toFixed(2);
		
		// Eliminamos la celda con el precio antiguo
		table.rows[fila].deleteCell(10); 
		//table.rows[fila].insertCell(9).innerHTML = nuevo_coste_referencia.toFixed(2);
		
		var td_precio = table.rows[fila].insertCell(10);
		td_precio.setAttribute("style","text-align:center");
		td_precio.innerHTML = nuevo_coste_referencia.toFixed(2);
	}
	catch(e) {
		alert(e);
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
	
// Funcion que calcula el coste total de las referencias de la tabla
function calculaCoste(tableId){
	try {
		table = document.getElementById('mitablaCabina');
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
		label_precio = document.getElementById('precio_cabina');
		label_precio.innerHTML = '<span class="tituloComp">' + costeTotal + "€" + '</span>';
	}
	catch(e) {
		alert(e);
	}
}