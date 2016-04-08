// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el proceso de confirmación de la modificación de una Orden de Producción

function Abrir_ventana(pagina){
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=500, top=100, left=350";
	window.open(pagina,"",opciones);
}

// Funcion para añadir referencias libres. 
// Las de las cabinas y perifericos estan en MuestraCabinaModOP - MuestraPerifericoModOP
function addRowRefLibre(tableId,id_referencia){ 
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
	cell_5.innerHTML = '<input type="text" id="piezas_ref_libres[]" name="piezas_ref_libres[]" class="CampoPiezasInput" value="' + num_uds + '" onblur="javascript:validarPiezasCorrectasRefsLibres(' + fila + ')"/>';
	cell_6.innerHTML = pack_precio;
	cell_7.innerHTML = cant;  
	cell_8.innerHTML = total_paquetes.toFixed(2);
	cell_9.innerHTML = precio_unidad.toFixed(2);
	cell_10.innerHTML = precio_referencia.toFixed(2);
	cell_11.innerHTML = '<input type="checkbox" name="chkbox" value"' + id_ref + '/>';
	
	// Calculamos el coste de todas las referencias que haya en la tabla
	costeTotal = calculaCosteRefLibres(table);
	actualizarCosteRefLibres(costeTotal);
}

// Funcion para eliminar Referencias Libres
function removeRowRefLibres(tableID) {
	try {
		table = document.getElementById('mitablaRefLibres');
		var rowCount = table.rows.length;

		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[11].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				table.deleteRow(i);
				rowCount--;
				i--;
				if (i+1 != rowCount){
					actualizarFilaRefLibres(table,i,rowCount);
				}
			}
		}
		costeTotal = calculaCosteRefLibres(table);
		actualizarCosteRefLibres(costeTotal);
	}catch(e) {
		alert(e);
	}
}

// Funcion para validar que el campo piezas sea un number
// Si es correcto se modifica el campo precio de la referencia
function validarPiezasCorrectasRefsLibres(fila) {
	var table = document.getElementById('mitablaRefLibres');
	var piezas = document.getElementsByName("piezas_ref_libres[]");
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
		modificaPrecioReferenciaRefLibres(num_piezas,fila);
		costeTotal = calculaCosteRefLibres(table);
		actualizarCosteRefLibres(costeTotal);
	}
	else {
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");	
	}
}
	
// Funcion para actualizar las filas despues de la eliminacion de una fila
// para que se puedan validar las filas despues de la modificacion
function actualizarFilaRefLibres(table,i,rowCount){
	for (var j=i; j<rowCount-1; j++){
		var num_piezas = table.rows[j+1].cells[5].childNodes[0].value;
		table.rows[j+1].deleteCell(5);	
		
		var td_precio = table.rows[j+1].insertCell(5);
		td_precio.setAttribute("style","text-align:center");
		td_precio.innerHTML = '<input type="text" id="piezas_ref_libres[]" name="piezas_ref_libres[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarPiezasCorrectasRefsLibres(' + j + ')"/>';
	
	}
}	

// Funcion que calcula el nuevo coste cuando se modifica el campo piezas de una referencia 
// y cambia el precio de la referencia modificada
function modificaPrecioReferenciaRefLibres(piezas,fila){
	try {
		var table = document.getElementById('mitablaRefLibres');
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
	
function validarPeriferico (piezas_periferico) {
	var i = 0;
	var no_hay_piezas_periferico = false;
	var no_es_numero = false;
	var fallo = false;
		
	while ((i < piezas_periferico.length) && (!no_hay_piezas_periferico) && (!no_es_numero)){
		// Si piezas esta vacio
		if (piezas_periferico[i].value.length == 0) no_hay_piezas_periferico = true;
		// Comprobamos que el valor introducido en piezas es correcto 
		else if (validarHayCaracter(i,piezas_periferico)) no_es_numero = true;
		i++;
	}
	if (no_hay_piezas_periferico) {
		alert ("Rellene el campo PIEZAS de todas las referencias de los perifericos");
		fallo = true;
	}
	else if (no_es_numero) {
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
		fallo = true;	
	}
	else fallo = false;
	return fallo;
}
	
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
	
// Funcion para validar que el campo PIEZAS de todas las referencias de todos los componentes
// y las referencias libre sean correctos
function validarFormulario() {
	var piezas_cabina = document.getElementsByName("piezas_cabina[]");
	var no_hay_piezas_cabina = false;
	var no_hay_piezas_periferico = false;
	var no_es_numero = false;
	var pieza = 0;
		
	if (piezas_cabina.length != 0) {
		var i = 0;
		while ((i < piezas_cabina.length) && (!no_hay_piezas_cabina) && (!no_es_numero)){
			// Si piezas esta vacio
			if (piezas_cabina[i].value.length == 0) no_hay_piezas_cabina = true;
			// Comprobamos que el valor introducido en piezas es correcto 
			else if (validarHayCaracter(i,piezas_cabina)) no_es_numero = true;
		i++;
		}
	}
	if (no_hay_piezas_cabina){
		alert ("Rellene el campo PIEZAS de todas las referencias de la cabina");	
		return false;
	}
	else if (no_es_numero) {
		alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
		return false;	
	}
	else {
		var num_perifericos = document.getElementsByName("IDS_PERS[]").length;
		// Si hay perifericos comprueba si el valor de piezas introducido es correcto
		if (num_perifericos != 0) {
			// Comprobar que no hay ningun campo piezas vacio y que sean piezas
			var j = 0;
			var fallo = false;
			while ((j < num_perifericos) && (!fallo)){
				var piezas_periferico = document.getElementsByName("piezas_perifericos_" + j + "[]");
				fallo = validarPeriferico(piezas_periferico);
				j++;
			}
			if (!fallo) {
				var piezas_ref_libres = document.getElementsByName("piezas_ref_libres[]");
				var no_hay_piezas_ref_libres = false;
				if (piezas_ref_libres.length != 0) {
					var k = 0;
					while ((k < piezas_ref_libres.length) && (!no_hay_piezas_ref_libres) && (!no_es_numero)){
						// Si piezas esta vacio
						if (piezas_ref_libres[k].value.length == 0) no_hay_piezas_ref_libres = true;
						// Comprobamos que el valor introducido en piezas es correcto 
						else if (validarHayCaracter(k,piezas_ref_libres)) no_es_numero = true;
						k++;
					}
					if (no_hay_piezas_ref_libres){
						alert ("Rellene el campo PIEZAS de todas las referencias libres");	
						return false;
					}
					else if (no_es_numero) {
						alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
						return false;	
					}
					else return true;
				}
			}
			else return false;
		}
	}
}
	
// Devuelve el coste de las interfaces de la cabina
function getCosteInterfacesCabina(){
	coste_interfaces = document.getElementById("costeInterfacesCabina").value;
	return coste_interfaces;
}

// Devuelve el coste de los kits de la cabina
function getCosteKitsCabina(){
	coste_kits = document.getElementById("costeKitsCabina").value;
	return coste_kits;
}
	
// Devuelve el coste de las interfaces del perifericos
function getCosteInterfacesPerifericos(periferico){
	coste_interfaces = document.getElementById('costeInterfacesPeriferico_' + periferico).value;
	return coste_interfaces;
}

// Devuelve el coste de los kits del perifericos
function getCosteKitsPerifericos(periferico){
	coste_kits = document.getElementById('costeKitsPeriferico_' + periferico).value;
	return coste_kits;
}
	
// Obtiene los precios de la cabina mas el precio de sus interfaces y los suma
function actualizarCosteTotalCabina(costeTotal){
	try{
		// Obtenemos el td donde esta guardado el input hidden
		var precio_total_cabina = document.getElementById("precio_total_cabina");
	
		// Obtener el coste total de las interfaces de la cabina
		coste_interfaces = getCosteInterfacesCabina();
		if (coste_interfaces != ""){
			coste_interfaces = parseFloat(coste_interfaces);
			coste_interfaces = coste_interfaces * 100;
			coste_interfaces = Math.round(coste_interfaces) / 100; 
		}
		else {
			coste_interfaces = 0;	
		}
		
		// Obtener el coste total de los kits de la cabina
		coste_kits = getCosteKitsCabina();
		if (coste_kits != ""){
			coste_kits = parseFloat(coste_kits);
			coste_kits = coste_kits * 100;
			coste_kits = Math.round(coste_kits) / 100; 
		}
		else {
			coste_kits = 0;	
		}

		costeTotal = costeTotal + coste_interfaces + coste_kits;
		coste_total_cabina = costeTotal;
		costeTotal = costeTotal.toFixed(2);
		precio_total_cabina.innerHTML = '<span class="tituloComp">' + costeTotal + "€" + '</span><input type="hidden" id="costeInterfacesCabina" name="costeInterfacesCabina" value="' + coste_interfaces + '"/><input type="hidden" id="costeKitsCabina" name="costeKitsCabina" value="' + coste_kits + '"/><input type="hidden" id="coste_total_cabina" name="coste_total_cabina" value="' + coste_total_cabina + '"/>'; 
		actualizarCosteTotalProducto();
	}
	catch(e) {
		alert(e);
	}
}
	
// Obtiene el coste total del periferico, actualiza el coste de todos los perifericos y el del producto.
function actualizarCosteTotalPeriferico(costeTotal,periferico){
	try{
		// Obtenemos el td donde esta guardado el input hidden
		var precio_total_periferico = document.getElementById('precio_total_periferico_' + periferico);
		// Obtener el coste total de las interfaces del periferico
		coste_interfaces = getCosteInterfacesPerifericos(periferico);
		if (coste_interfaces != ''){
			coste_interfaces = parseFloat(coste_interfaces);
			coste_interfaces = coste_interfaces * 100;
			coste_interfaces = Math.round(coste_interfaces) / 100; 
		}
		else {
			coste_interfaces = 0;	
		}
		
		// Obtener el coste total de los kits del periferico
		coste_kits = getCosteKitsPerifericos(periferico);
		if (coste_kits != ''){
			coste_kits = parseFloat(coste_kits);
			coste_kits = coste_kits * 100;
			coste_kits = Math.round(coste_kits) / 100; 
		}
		else {
			coste_kits = 0;	
		}
			
		costeTotal = costeTotal + coste_interfaces + coste_kits;
		costeTotal = costeTotal.toFixed(2);
			
		precio_total_periferico.innerHTML = '<span class="tituloComp">' + costeTotal + "€" + '</span><input type="hidden" id="costeInterfacesPeriferico_' + periferico + '" name="costeInterfacesPeriferico_' + periferico + '" value="' + coste_interfaces + '"/><input type="hidden" id="costeKitsPeriferico_' + periferico + '" name="costeKitsPeriferico_' + periferico + '" value="' + coste_kits + '"/><input type="hidden" id="precio_tot_periferico_' + periferico + '" name="precio_tot_periferico_' + periferico + '" value="' + costeTotal + '"/>';
			
		actualizarCosteTotalProducto();
	}
	catch(e) {
		alert(e);
	}
}
	
// Devuelve el coste de la cabina
function dameCosteTotalCabina(){
	try{	
		if (document.getElementById('coste_total_cabina') == null){
			coste_total_cabina = 0;	
		}
		else{
 			coste_total_cabina = document.getElementById('coste_total_cabina').value;
			coste_total_cabina = parseFloat(coste_total_cabina);
			coste_total_cabina = coste_total_cabina * 100;
			coste_total_cabina = Math.round(coste_total_cabina) / 100;
		}
		return coste_total_cabina;
	}		
	catch(e) {
		alert(e);
	}
}
	
// Calcula el coste total de todos los perifericos cuando se ha producido alguna modificacion en alguno de ellos
// Añadir referencia, eliminar referencia o cambiar numero de piezas
function dameCosteTotalPerifericos(){
	try{	
		// Obtenemos el td donde esta guardado el input hidden
		var num_perifericos = document.getElementsByName("IDS_PERS[]").length;
		precio_total_perifericos = 0;
		for (i=0; i<num_perifericos; i++){
			// Obtenemos la celda donde se guardan los precios totales de los perifericos
			var celda_total_periferico = document.getElementById('precio_total_periferico_' + i);
			// Obtiene los precios totales de cada periferico
			var precio_tot_periferico = document.getElementById('precio_tot_periferico_' + i).value;
			// Obtiene la capa donde se guarda el precio de todos los perifericos
			var capa_final_perifericos = document.getElementById('capa_final_perifericos');
			precio_tot_periferico = parseFloat(precio_tot_periferico);
			precio_tot_periferico = precio_tot_periferico * 100;
			precio_tot_periferico = Math.round(precio_tot_periferico) / 100;
			precio_total_perifericos = precio_total_perifericos + precio_tot_periferico;
		}
		return precio_total_perifericos;
	}		
	catch(e) {
		alert(e);
	}
}
	
// Devuelve el coste de la cabina
function dameCosteTotalRefsLibres(){
	try{	
		coste_total_refs_libres = document.getElementById('coste_total_refs_libres').value;	
		coste_total_refs_libres = parseFloat(coste_total_refs_libres);
		coste_total_refs_libres = coste_total_refs_libres * 100;
		coste_total_refs_libres = Math.round(coste_total_refs_libres) / 100;
		return coste_total_refs_libres;
	}		
	catch(e) {
		alert(e);
	}
}
	
// Funcion que calcula el coste total de las referencias de la tabla
function calculaCosteRefLibres(table){
	try {
		table = document.getElementById('mitablaRefLibres');
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
function actualizarCosteRefLibres(costeTotal){
	try{
		costeTotal = parseFloat(costeTotal);
		costeTotal = costeTotal * 100;
		costeTotal = Math.round(costeTotal) / 100;
		costeTotal = costeTotal.toFixed(2);
		label_precio = document.getElementById('coste_ref_libres');
		label_precio.innerHTML = '<span class="tituloComp">' + costeTotal + "€" + '</span><input type="hidden" id="coste_total_refs_libres" name="coste_total_refs_libres" value="' + costeTotal + '"/>';
		
		actualizarCosteTotalProducto();
	}
	catch(e) {
		alert(e);
	}
}
	
// Actualiza el coste total del producto cuando se produce alguna modificacion en alguno de sus componentes
function actualizarCosteTotalProducto(){
	// Obntenemos el coste final de la cabina 
	precio_final_cabina = dameCosteTotalCabina();
	precio_final_todos_perifericos = dameCosteTotalPerifericos();
	precio_final_refs_libres = dameCosteTotalRefsLibres();

	precio_producto = precio_final_cabina + precio_final_todos_perifericos + precio_final_refs_libres;
	precio_producto_string = precio_producto.toFixed(2);
				
	// Obtenemos la celda de total producto
	celda_total_producto = document.getElementById('celda_total_producto');
	celda_total_producto.innerHTML = '<span class="tituloComp">' + precio_producto_string + '€' + '</span>';
		
	actualizarCosteTotalOrdenProduccion(precio_producto);
}
	
// Actualiza el coste total de la Orden de Produccion
function actualizarCosteTotalOrdenProduccion(precio_producto){
	// Obtenemos el numero de unidades 
	numero_unidades = document.getElementById('numero_unidades').value;
	numero_unidades = parseInt(numero_unidades);
		
	coste_op = precio_producto * numero_unidades;
	
	coste_op = parseFloat(coste_op);
	coste_op = coste_op * 100;
	coste_op = Math.round(coste_op) / 100;
	coste_op = coste_op.toFixed(2);

	// Obtenemos la celda donde esta escrito el resultado 		
	celda_coste_op = document.getElementById('celda_coste_op');
	celda_coste_op.innerHTML = '<span class="tituloComp">' + coste_op + '€' + '</span>'; 
}
