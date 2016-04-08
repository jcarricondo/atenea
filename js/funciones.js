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



// Función para enviar datos de formulario mediante AJAX
/*function peticionForm(url, parametros, div) {
	http_request = objetoAJAX();      
	http_request.open('POST', url, true);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.setRequestHeader("Content-length", parametros.length);
	http_request.setRequestHeader("Connection", "close");
	http_request.onreadystatechange = function() {
		if (http_request.readyState == 4) {
			if (http_request.status == 200) {
				document.getElementById(div).innerHTML = http_request.responseText;
			}
		}
	}
	http_request.send(parametros);
	$("#loading").css({visibility: "visible",display: "none"}).slideUp(600);
}
// Calcula un número aleatorio
function numAleatorio(){
    numPosibilidades = 9999999999999999 - 0
    aleat = Math.random() * numPosibilidades
    aleat = Math.round(aleat)
    return parseInt(0) + aleat
}
// Valida NIF, NIE, CIF
/* Devuelve:
1 = NIF ok, 
2 = CIF ok, 
3 = NIE ok, 
-1 = NIF error, 
-2 = CIF error, 
-3 = NIE error, 
0 = ??? error
*-/
function valida_nif_cif_nie(a) 
{
	var temp=a.toUpperCase();
	var cadenadni="TRWAGMYFPDXBNJZSQVHLCKE";
 
	if (temp!==''){
		//si no tiene un formato valido devuelve error
		if ((!/^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$/.test(temp) && !/^[T]{1}[A-Z0-9]{8}$/.test(temp)) && !/^[0-9]{8}[A-Z]{1}$/.test(temp))
		{
			return 0;
		}
 
		//comprobacion de NIFs estandar
		if (/^[0-9]{8}[A-Z]{1}$/.test(temp))
		{
			posicion = a.substring(8,0) % 23;
			letra = cadenadni.charAt(posicion);
			var letradni=temp.charAt(8);
			if (letra == letradni)
			{
			   	return 1;
			}
			else
			{
				return -1;
			}
		}
 
		//algoritmo para comprobacion de codigos tipo CIF
		suma = parseInt(a[2])+parseInt(a[4])+parseInt(a[6]);
		for (i = 1; i < 8; i += 2)
		{
			temp1 = 2 * parseInt(a[i]);
			temp1 += '';
			temp1 = temp1.substring(0,1);
			temp2 = 2 * parseInt(a[i]);
			temp2 += '';
			temp2 = temp2.substring(1,2);
			if (temp2 == '')
			{
				temp2 = '0';
			}
 
			suma += (parseInt(temp1) + parseInt(temp2));
		}
		suma += '';
		n = 10 - parseInt(suma.substring(suma.length-1, suma.length));
 
		//comprobacion de NIFs especiales (se calculan como CIFs)
		if (/^[KLM]{1}/.test(temp))
		{
			if (a[8] == String.fromCharCode(64 + n))
			{
				return 1;
			}
			else
			{
				return -1;
			}
		}
 
		//comprobacion de CIFs
		if (/^[ABCDEFGHJNPQRSUVW]{1}/.test(temp))
		{
			temp = n + '';
			if (a[8] == String.fromCharCode(64 + n) || a[8] == parseInt(temp.substring(temp.length-1, temp.length)))
			{
				return 2;
			}
			else
			{
				return -2;
			}
		}
 
		//comprobacion de NIEs
		//T
		if (/^[T]{1}/.test(temp))
		{
			if (a[8] == /^[T]{1}[A-Z0-9]{8}$/.test(temp))
			{
				return 3;
			}
			else
			{
				return -3;
			}
		}
 
		//XYZ
		if (/^[XYZ]{1}/.test(temp))
		{
			pos = str_replace(['X', 'Y', 'Z'], ['0','1','2'], temp).substring(0, 8) % 23;
			if (a[8] == cadenadni.substring(pos, pos + 1))
			{
				return 3;
			}
			else
			{
				return -3;
			}
		}
	}
 
	return 0;
}
// Funcion srt_replace de PHP para Javascript
function str_replace(search, replace, subject) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Gabriel Paderni
    // +   improved by: Philip Peterson
    // +   improved by: Simon Willison (http://simonwillison.net)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   bugfixed by: Anton Ongson
    // +      input by: Onno Marsman
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    tweaked by: Onno Marsman
    // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    // *     returns 1: 'Kevin.van.Zonneveld'
    // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
    // *     returns 2: 'hemmo, mars'
 
    var f = search, r = replace, s = subject;
    var ra = r instanceof Array, sa = s instanceof Array, f = [].concat(f), r = [].concat(r), i = (s = [].concat(s)).length;
 
    while (j = 0, i--) {
        if (s[i]) {
            while (s[i] = s[i].split(f[j]).join(ra ? r[j] || "" : r[0]), ++j in f){};
        }
    };
 
    return sa ? s : s[0];
}
// Validar CIF
function validarCIF(texto){
        
        var pares = 0;
        var impares = 0;
        var suma;
        var ultima;
        var unumero;
        var uletra = new Array("J", "A", "B", "C", "D", "E", "F", "G", "H", "I");
        var xxx;
        
        texto = texto.toUpperCase();
        
        var regular = new RegExp(/^[ABCDEFGHJKLMNPQS]\d\d\d\d\d\d\d[0-9,A-J]$/g);
         if (!regular.exec(texto)) return false;
             
         ultima = texto.substr(8,1);

         for (var cont = 1 ; cont < 7 ; cont ++){
             xxx = (2 * parseInt(texto.substr(cont++,1))).toString() + "0";
             impares += parseInt(xxx.substr(0,1)) + parseInt(xxx.substr(1,1));
             pares += parseInt(texto.substr(cont,1));
         }
         xxx = (2 * parseInt(texto.substr(cont,1))).toString() + "0";
         impares += parseInt(xxx.substr(0,1)) + parseInt(xxx.substr(1,1));
         
         suma = (pares + impares).toString();
         unumero = parseInt(suma.substr(suma.length - 1, 1));
         unumero = (10 - unumero).toString();
         if(unumero == 10) unumero = 0;
         
         if ((ultima == unumero) || (ultima == uletra[unumero]))
             return true;
         else
             return false;

    } 
function enviarObjetivo() {
	$("#loading").css({visibility: "visible",display: "none"}).slideDown(200);
	ajax = objetoAJAX();
	ajax.open("GET", "../lib/comercial.php?mod=guardarObjetivo&time=" + numAleatorio() + "&id_periodo=" + document.getElementById("id_periodo").value + "&id_consultor=" + document.getElementById("id_consultor").value + "&objetivo=" + document.getElementById("objetivo").value);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			document.getElementById("capa_objetivo").innerHTML = ajax.responseText;
			$("#loading").css({visibility: "visible",display: "none"}).slideUp(200);
		}
	}
	ajax.send(null);
}
function verAnuncio(id_anuncio) {
	var contenido = "contenidoAnuncio-" + id_anuncio;
	if(document.getElementById(contenido).title == "0") {
		document.getElementById(contenido).title = "Noticia";
		$("#"+contenido).css({visibility: "visible",display: "none"}).toggle(400);
	} else {
		document.getElementById(contenido).title = "0";
		$("#"+contenido).css({visibility: "visible",display: "none"}).slideUp(400);;
	}
}
function marcarAnuncioLeido(id_consultor,id_anuncio) {
	$("#loading").css({visibility: "visible",display: "none"}).slideDown(200);
	ajax = objetoAJAX();
	ajax.open("GET", "../lib/comercial.php?mod=marcarAnuncioLeido&time=" + numAleatorio() + "&id_consultor=" + id_consultor + "&id_anuncio=" + id_anuncio);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			setTimeout($.unblockUI, 0);
			$("#loading").css({visibility: "visible",display: "none"}).slideUp(200);
		}
	}
	ajax.send(null);
}*/