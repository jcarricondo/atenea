<!-- AJAX -->
// Funcion para poder utilizar AJAX
function objetoAJAX() {
	var ajaxs = ["Msxml2.XMLHTTP","Msxml2.XMLHTTP.4.0","Msxml2.XMLH TTP.5.0","Msxml2.XMLHTTP.3.0","Microsoft.XMLHTTP"];
    var ajax = false;
    for(var i=0 ; !ajax && i<ajaxs.length ; i++){
        try{ 
            ajax = new ActiveXObject(ajaxs[i]);   // Internet Explorer
        }
        catch(e) { 
            ajax = false; 
        }
    }
    if(!ajax && typeof XMLHttpRequest!='undefined') {
        ajax = new XMLHttpRequest();  // Firefox, Opera 8.0+, Safari
    }
    return ajax;
}

// Funcion que comprueba si estan seleccionadas todas los registros del listado
// y marca o desmarca todas los checkbox en funcion del resultado
function todosCheckbox(){
	if (document.getElementById('todos_Checkbox').checked == false){
		desSeleccionarTodosCheckbox();			
	}
	else{
		seleccionarTodosCheckbox();
	}
}
	
// Selecciona todas los checkbox
function seleccionarTodosCheckbox(){
	// Guardamos en checkboxs todos los ids del Listado
	var checkbox = document.getElementsByName('chkbox[]');
	
	for (i=0;i<checkbox.length;i++){
		checkbox.item(i).checked = 'checked';
	}
}
	
// Deselecciona todos los checkbox del listado
function desSeleccionarTodosCheckbox(){
	// Guardamos en checkboxs todos los ids del listado
	var checkbox = document.getElementsByName('chkbox[]');
		
	for (i=0;i<checkbox.length;i++){
		checkbox.item(i).checked = 0;
	}
}

// Funcion que solo permite escribir numeros
function soloNumeros (e) { 
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla==8) return true; // 3
    patron =/\d/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

// Funciones de validacion de input para enteros y decimales
function extractNumber(obj, decimalPlaces, allowNegative){
	var temp = obj.value;
	
	// avoid changing things if already formatted correctly
	var reg0Str = '[0-9]*';
	if (decimalPlaces > 0) {
		reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
	} else if (decimalPlaces < 0) {
		reg0Str += '\\.?[0-9]*';
	}
	reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
	reg0Str = reg0Str + '$';
	var reg0 = new RegExp(reg0Str);
	if (reg0.test(temp)) return true;

	// first replace all non numbers
	var reg1Str = '[^0-9' + (decimalPlaces != 0 ? '.' : '') + (allowNegative ? '-' : '') + ']';
	var reg1 = new RegExp(reg1Str, 'g');
	temp = temp.replace(reg1, '');

	if (allowNegative) {
		// replace extra negative
		var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
		var reg2 = /-/g;
		temp = temp.replace(reg2, '');
		if (hasNegative) temp = '-' + temp;
	}
	
	if (decimalPlaces != 0) {
		var reg3 = /\./g;
		var reg3Array = reg3.exec(temp);
		if (reg3Array != null) {
			// keep only first occurrence of .
			//  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
			var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
			reg3Right = reg3Right.replace(reg3, '');
			reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
			temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
		}
	}
	obj.value = temp;
}

function blockNonNumbers(obj, e, allowDecimal, allowNegative){
	var key;
	var isCtrl = false;
	var keychar;
	var reg;
		
	if(window.event) {
		key = e.keyCode;
		isCtrl = window.event.ctrlKey
	}
	else if(e.which) {
		key = e.which;
		isCtrl = e.ctrlKey;
	}
	
	if (isNaN(key)) return true;
	
	keychar = String.fromCharCode(key);
	
	// check for backspace or delete, or if Ctrl was pressed
	if (key == 8 || isCtrl)
	{
		return true;
	}

	reg = /\d/;
	var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
	var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;
	
	return isFirstN || isFirstD || reg.test(keychar);
}

// Función que carga la referencia al pulsar intro
function cargaReferenciaIntro(e){
	if (e.keyCode == 13) {
		enviarFormulario();
	}
}

function enviarFormulario(){
	var formulario = document.getElementById("nombreFormulario");
	document.forms[formulario.value].submit();
}

// Obtiene las referencias de una tabla y calcula el precio incluyendo las heredadas
function damePrecioComponenteConHeredadas(table){
	var array_ids = new Array();
	var array_piezas = new Array();

	var rowCount = table.rows.length;
	var id_ref;
	var piezas;

	// Empezamos en la primera fila obviando la fila de encabezado
	for(var i=1; i<rowCount; i++) {
		var j=i-1;
		var row = table.rows[i];
		id_ref = row.cells[0].childNodes[0].nodeValue;
		piezas = row.cells[5].childNodes[0].value;

		array_ids[j] = id_ref;
		array_piezas[j] = piezas;
	}

	// Llamada asincrona para obtener el coste total del componente con sus ref. heredadas
	var respuesta = (function () {
		var respuesta = null;
		$.ajax({
			dataType: "json",
			url: "../ajax/basicos/referencias.php?comp=calcularCosteReferenciasHeredadas",
			data: "ids=" + array_ids + "&piezas=" + array_piezas,
			type: "GET",
			async: false,
			success: function (data) {
				respuesta = data;
			}
		});
		return respuesta;
	})();

	var precio = respuesta;
	return precio;
}

// Obtiene las referencias de una tabla y calcula el precio incluyendo las heredadas
function damePrecioKitConHeredadas(table){
	var array_ids = new Array();
	var array_piezas = new Array();

	var rowCount = table.rows.length;
	var id_ref;
	var piezas;

	// Empezamos en la primera fila obviando la fila de encabezado
	for(var i=1; i<rowCount; i++) {
		var j=i-1;
		var row = table.rows[i];
		id_ref = row.cells[0].childNodes[0].nodeValue;
		piezas = row.cells[5].childNodes[0].nodeValue;

		array_ids[j] = id_ref;
		array_piezas[j] = piezas;
	}

	// Llamada asincrona para obtener el coste total del componente con sus ref. heredadas
	var respuesta = (function () {
		var respuesta = null;
		$.ajax({
			dataType: "json",
			url: "../ajax/basicos/referencias.php?comp=calcularCosteReferenciasHeredadas",
			data: "ids=" + array_ids + "&piezas=" + array_piezas,
			type: "GET",
			async: false,
			success: function (data) {
				respuesta = data;
			}
		});
		return respuesta;
	})();

	var precio = respuesta;
	return precio;
}

// Actualizamos el precio del kit
function actualizarPrecioKit(span_coste_kit,coste_input_kit,precio_kit){
	precio_kit = parseFloat(precio_kit);
	precio_kit = precio_kit * 100;
	precio_kit = Math.round(precio_kit) / 100;
	var precio_kit_string = precio_kit.toFixed(2);

	// Actualizamos el precio del kit
	document.getElementById(span_coste_kit).innerHTML = precio_kit_string.replace('.',',') + '€';
	// Guardamos en un array oculto el precio del kit
	document.getElementById(coste_input_kit).value = precio_kit;

	// Actualizamos el coste total de kits
	var costeKits = parseFloat(document.getElementById('costeKits').value);
	costeKits = costeKits + precio_kit;
	document.getElementById('costeKits').setAttribute('value',costeKits);
}

// Función auxiliar de BuscadorDinamicoComponentes que busca el patrón de búsqueda
function buscaPatronBusqueda(nombre_componente,palabra_busqueda,caracter_nombre){
	var sigo_buscando = true;
	var caracter_busqueda = 0;
	while(sigo_buscando && caracter_busqueda<palabra_busqueda.length){
		sigo_buscando = palabra_busqueda.charAt(caracter_busqueda) == nombre_componente.charAt(caracter_nombre);
		caracter_busqueda++;
		caracter_nombre++;
	}
	return sigo_buscando && (caracter_busqueda == palabra_busqueda.length);
}

// Función que filtra componentes cuando el usuario escribe en el buscador seleccionado
function BuscadorDinamicoComponentes(opcion_busqueda,input_buscador,lista_no_asignados){
	var input_buscador = document.getElementById(input_buscador);
	var palabra_busqueda = input_buscador.value;
	var contador_letras = palabra_busqueda.length;
	var empiezo_buscar = contador_letras >= 3;
	var lista_componentes = document.getElementById(lista_no_asignados);

	for(i=0; i<lista_componentes.length; i++){
		var option_componente = lista_componentes.item(i);
		var nombre_componente = lista_componentes.item(i).innerHTML;
		var proceso_filtrado = opcion_busqueda === "todos" || (opcion_busqueda === "produccion" && option_componente.id != "");

		if(proceso_filtrado){
			if(empiezo_buscar){
				var caracter_nombre = 0;
				var encontrado = false;
				while(!encontrado && caracter_nombre<nombre_componente.length){
					// Entramos si coincide el carácter del nombre con la primera letra del patrón de búsqueda
					if(nombre_componente.charAt(caracter_nombre) == palabra_busqueda.charAt(0)){
						encontrado = buscaPatronBusqueda(nombre_componente,palabra_busqueda,caracter_nombre);
					}
					caracter_nombre++;
				}
				if(encontrado) option_componente.style.display = "block";
				else option_componente.style.display = "none";
			}
			else option_componente.style.display = "block";
		}
	}
}






