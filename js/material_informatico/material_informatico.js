// Fichero con las funciones de javascript del módulo MATERIAL INFORMATICO
function Abrir_ventana(pagina) {
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=500, top=100, left=350";
	window.open(pagina,"",opciones);
}

// Función que carga el select subtipo del material
function cargaSubtipo(id_tipo){
	var ajax = objetoAJAX();
	ajax.open("GET","../ajax/material_informatico/material_informatico.php?func=cargaSuptipo&id_tipo=" + id_tipo,"true");
	ajax.onreadystatechange=function() {
		if(ajax.readyState==4 && ajax.status==200) {
			document.getElementById("capa_subtipo").setAttribute("style","display:auto");
			document.getElementById("capa_subtipo").innerHTML=ajax.responseText;
		}
	}
	ajax.send(null);	
}

// Función para comprobar si el numero de serie del material informático tiene un formato correcto
function comprobacionMaterial(num_serie){
	// FORMATO: 000-0000000
	var ExpReg = /^[0-9]{3}-[0-9]{7}$/;
	if((num_serie == num_serie.match(ExpReg)) && (num_serie != '')){
		return true;
	}
	else{
		return false;
	}
}

// Función que comprueba si existe ya el material en la tabla LOG
function comprobarNumSerie(num_serie){
	var tabla = document.getElementById("tabla_log");
	var i = 0; 
	var error = false;
	var num_filas = tabla.rows.length; 

	while((i<num_filas) && (!error)){
		var num_serie_tabla = tabla.rows[i].cells.item(0).innerHTML;
		error = (num_serie_tabla == num_serie);
		i++;
	}
	return error;
}

// Función que carga el material informático a recepcionar
function cargaMaterial(){
	var num_serie = document.getElementById("num_serie").value;
	var metodo = document.getElementById('metodo').value;
	var id_almacen = document.getElementById('id_almacen').value;

	// Comprobamos si el número de serie introducido tiene un formato correcto
	if(comprobacionMaterial(num_serie)){
		// Comprobamos si existe el material con ese número de serie
		if(num_serie.length != 0){
			var ajax = objetoAJAX();
			ajax.open("GET","../ajax/material_informatico/material_informatico.php?func=comprobarNumSerie&num_serie=" + num_serie + "&metodo=" + metodo + "&id_almacen=" + id_almacen,"true");
			ajax.onreadystatechange=function() {
				if(ajax.readyState==4 && ajax.status==200) {
					document.getElementById("cargaMaterial").innerHTML=ajax.responseText;
				}
			}
			ajax.send(null);
		}
		else {
			alert("Introduzca el número de serie del material informático");
		}
		
	}
	else {
		alert("El formato del material informático no es correcto.\n\nIntroduzca uno con formato 000-0000000 \n\nNOTA: 3 DIGITOS  '-'  7 DIGITOS\n\n");
	}
}

// Función para recepcionar los materiales uno por uno.  
function recepcionarMaterial(num_serie,id_almacen){
	// Comprobamos que no se haya añadido el material
	var error = comprobarNumSerie(num_serie);

	if (!error){
		// Obtenemos los elementos de la pagina
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');	
		var id_albaran = document.getElementById('id_albaran_global').value;
		var id_material = document.getElementById('id_material_hidden').value;
		var nombre_tipo = document.getElementById('nombre_tipo_hidden').value;
		var averiado = document.getElementById('averiado');
		var id_estado_antiguo = document.getElementById('id_estado_hidden').value;
		var estado;
		
		if(averiado.checked){
			esta_averiado = 'SI';
			estado = 'AVERIADO';
		}
		else{
			esta_averiado = 'NO';
			estado = 'STOCK';
		}

		// Hacemos la llamada a la función AJAX para la recepción del material informático
		var ajax = objetoAJAX();
		ajax.open("GET","../ajax/material_informatico/material_informatico.php?func=recepcionar&num_serie=" + num_serie + "&id_material=" + id_material + "&esta_averiado=" + esta_averiado + "&id_almacen=" + id_almacen,"true");
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4 && ajax.status==200) {
				document.getElementById("datos_log").innerHTML=ajax.responseText;
		   	}
		}
		ajax.send(null);

		// Preparamos la tabla log para insertar un nuevo registro
		var pos = table.rows.length; 
		var row = table.insertRow(pos);
		var fila = pos;
					
		var cell_0 = row.insertCell(0); 
		var cell_1 = row.insertCell(1); 
		var cell_2 = row.insertCell(2);
		var cell_3 = row.insertCell(3);
		var cell_4 = row.insertCell(4);
		var cell_5 = row.insertCell(5);
				
		cell_3.setAttribute("style","text-align:center");
		cell_4.setAttribute("style","text-align:center");
		cell_5.setAttribute("style","text-align:center");
		
		// Insertamos el material
		table.rows[fila].cells[0].innerHTML = num_serie;
		table.rows[fila].cells[1].innerHTML = nombre_tipo;
		table.rows[fila].cells[2].innerHTML = estado;
		table.rows[fila].cells[3].innerHTML = esta_averiado;
		table.rows[fila].cells[4].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir('+"'"+'popup_log.php?id_albaran='+id_albaran+'&num_serie=&quot;'+num_serie+'&quot;&metodo=&quot;RECEPCIONAR&quot;&averiado=&quot;'+esta_averiado+'&quot;'+"'"+') " />';
		table.rows[fila].cells[5].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerRecepcion(this,' + id_material + ',' + id_estado_antiguo +')" />';

		// Eliminamos el material del buscador 
		table_buscador.rows[1].cells[0].innerHTML = "";
		table_buscador.rows[1].cells[1].innerHTML = "";
		table_buscador.rows[1].cells[2].innerHTML = "";
		table_buscador.rows[1].cells[3].innerHTML = "";
		table_buscador.rows[1].cells[4].innerHTML = "";
		table_buscador.rows[1].cells[5].innerHTML = "";

		table_buscador.rows[1].cells[0].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[1].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[2].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[3].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[4].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[5].setAttribute("style","height: 35px;");
	}
	else {
		alert("Ya se realizó una operación con ese material. Elimine el movimiento del material y vuelva a realizar la operación de nuevo");
	}
}

// Función que deshace la recepción 
function deshacerRecepcion(r, id_material, id_estado){
	var id_albaran = document.getElementById('id_albaran_global').value;

	// Eliminamos la fila de la tabla 
	var i = r.parentNode.parentNode.rowIndex;
	document.getElementById("tabla_log").deleteRow(i);

	var ajax = objetoAJAX();
	ajax.open("GET","../ajax/material_informatico/material_informatico.php?func=deshacerRecepcion&id_material=" + id_material + "&id_estado=" + id_estado + "&id_albaran=" + id_albaran,"true");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4 && ajax.status==200) {

		}
	}
	ajax.send(null);
}

// Función para desrecepcionar los materiales uno por uno 
function desrecepcionarMaterial(num_serie,id_almacen){
	// Comprobamos que no se haya añadido el material
	var error = comprobarNumSerie(num_serie);

	if (!error){
		// Obtenemos los elementos de la pagina
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');	
		var id_albaran = document.getElementById('id_albaran_global').value;
		var id_material = document.getElementById('id_material_hidden').value;
		var nombre_tipo = document.getElementById('nombre_tipo_hidden').value;
		var averiado = document.getElementById('averiado');
		var id_estado_antiguo = document.getElementById('id_estado_hidden').value;
		var estado;
		
		if(averiado.checked){
			esta_averiado = 'SI';
			estado = 'EN REPARACION';
		}
		else{
			esta_averiado = 'NO';
			estado = 'EN USO';
		}

		// Hacemos la llamada a la funcion AJAX para la desrecepción del material informático
		var ajax = objetoAJAX();
		ajax.open("GET","../ajax/material_informatico/material_informatico.php?func=desrecepcionar&num_serie=" + num_serie + "&id_material=" + id_material + "&esta_averiado=" + esta_averiado + "&id_almacen=" + id_almacen,"true");
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4 && ajax.status==200) {
				document.getElementById("datos_log").innerHTML=ajax.responseText;
		   	}
		}
		ajax.send(null);

		// Preparamos la tabla log para insertar un nuevo registro
		var pos = table.rows.length; 
		var row = table.insertRow(pos);
		var fila = pos;
					
		var cell_0 = row.insertCell(0); 
		var cell_1 = row.insertCell(1); 
		var cell_2 = row.insertCell(2);
		var cell_3 = row.insertCell(3);
		var cell_4 = row.insertCell(4);
		var cell_5 = row.insertCell(5);
				
		cell_3.setAttribute("style","text-align:center");
		cell_4.setAttribute("style","text-align:center");
		cell_5.setAttribute("style","text-align:center");
		
		// Insertamos el material
		table.rows[fila].cells[0].innerHTML = num_serie;
		table.rows[fila].cells[1].innerHTML = nombre_tipo;
		table.rows[fila].cells[2].innerHTML = estado;
		table.rows[fila].cells[3].innerHTML = esta_averiado;
		table.rows[fila].cells[4].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir('+"'"+'popup_log.php?id_albaran='+id_albaran+'&num_serie=&quot;'+num_serie+'&quot;&metodo=&quot;DESRECEPCIONAR&quot;&averiado=&quot;'+esta_averiado+'&quot;'+"'"+') " />';
		table.rows[fila].cells[5].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerDesRecepcion(this,' + id_material + ',' + id_estado_antiguo +')" />';

		// Eliminamos el material del buscador 
		table_buscador.rows[1].cells[0].innerHTML = "";
		table_buscador.rows[1].cells[1].innerHTML = "";
		table_buscador.rows[1].cells[2].innerHTML = "";
		table_buscador.rows[1].cells[3].innerHTML = "";
		table_buscador.rows[1].cells[4].innerHTML = "";
		table_buscador.rows[1].cells[5].innerHTML = "";

		table_buscador.rows[1].cells[0].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[1].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[2].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[3].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[4].setAttribute("style","height: 35px;");
		table_buscador.rows[1].cells[5].setAttribute("style","height: 35px;");
	}
	else {
		alert("Ya se realizó una operación con ese material. Elimine el movimiento del material y vuelva a realizar la operación de nuevo");
	}
}

// Funcion que deshace la desrecepción 
function deshacerDesRecepcion(r, id_material, id_estado){
	var id_albaran = document.getElementById('id_albaran_global').value;
	
	// Eliminamos la fila de la tabla 
	var i = r.parentNode.parentNode.rowIndex;
	document.getElementById("tabla_log").deleteRow(i);

	// Función para deshacer la operación de Desrecepción
	var ajax = objetoAJAX();
	ajax.open("GET","../ajax/material_informatico/material_informatico.php?func=deshacerDesRecepcion&id_material=" + id_material + "&id_estado=" + id_estado + "&id_albaran=" + id_albaran,"true");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4 && ajax.status==200) {

		}
	}
	ajax.send(null);
}

// Función que descarga el informe del albarán
function cerrarAlbaran(id_albaran){
	if(confirm("¿Desea finalizar la operación?")){
		// Comprobamos si el albarán esta vacío
		var tabla = document.getElementById("tabla_log");
		var num_filas = tabla.rows.length-1;

		if(num_filas == 0){
			// Albarán vacío
			window.location="../material_informatico/albaranes_informatica.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=1";	
		}
		else{
			window.location="../material_informatico/albaranes_informatica.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=0";
		}	
	}
}

// Descarga el excel de materiales de listado materiales informáticos
function descargar_XLS_Materiales(){
	window.location="informe_materiales.php";
}

// Descarga el excel de material de stock informático
function descargar_XLS_Stock(){
    window.location="informe_stock.php";
}

// Descarga el excel de movimientos de albaranes de los materiales informáticos
function descargar_XLS_Movimientos(){
	var ids_movimientos_materiales = document.getElementsByName('ids_movimientos_materiales[]');
	var ids_movimientos = new Array();

	for(i=0;i<ids_movimientos_materiales.length;i++){
		ids_movimientos[i] = ids_movimientos_materiales.item(i).value;
	}
	window.location="informe_movimientos.php?ids_movimientos=" + ids_movimientos;
}
