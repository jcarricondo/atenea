// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el proceso de creación de un kit

	function abrir(url) {
		open(url,'','top=200,left=700,width=500,height=500') ;
	}

	function Abrir_ventana(pagina) {
		var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=500, top=100, left=350";
		window.open(pagina,"",opciones);
	}
	
	function popup(url,ancho,alto) {
		var posicion_x; 
		var posicion_y; 
		posicion_x=(screen.width/2)-(ancho/2); 
		posicion_y=(screen.height/2)-(alto/2); 
		window.open(url, "width="+ancho+",height="+alto+",menubar=0,toolbar=0,directories=0,scrollbars=no,resizable=no,left="+posicion_x+",top="+posicion_y+"");
	}


	var div = document.getElementById('CapaTablaIframe');
	var table = document.createElement('tabla');
	var row = table.insertRow(0);
	var cell = row.insertCell(0);
	div.appendChild(table);
	
	// Funcion para añadir una referencia al kit
	function addRow(tableId,id_referencia) {
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

		var piezas = new Array();
		var num_piezas = num_uds;
		
		cell_0.setAttribute("style","text-align:center");
		cell_5.setAttribute("style","text-align:center");
		cell_6.setAttribute("style","text-align:center");
		cell_7.setAttribute("style","text-align:center");
		cell_8.setAttribute("style","text-align:center");
		cell_9.setAttribute("style","text-align:center");
		cell_10.setAttribute("style","text-align:center");

		cell_0.innerHTML = id_ref;
		cell_1.innerHTML = ref;
		cell_2.innerHTML = prov;
		cell_3.innerHTML = ref_prov;
		cell_4.innerHTML = nom_pieza;
		cell_5.innerHTML = '<input type="text" id="piezas[]" name="piezas[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarHayCaracter(' + fila  + ')"/>';
		cell_6.innerHTML = pack_precio.toFixed(2);
		cell_7.innerHTML = cant.toFixed(2);
		cell_8.innerHTML = precio_unidad.toFixed(2);
		cell_9.innerHTML = precio_referencia.toFixed(2);
		cell_10.innerHTML = '<input type="checkbox" name="chkbox" value"' + id_ref + '"/>';

		// Calculamos el coste de todas las referencias que haya en la tabla
		// costeTotal = calculaCoste(table); // <-- quitar ?

		// Calcula el precio total con las referencias heredadas
		costeTotal = damePrecioComponenteConHeredadas(table);
		actualizarCoste(costeTotal);
	}
	
	// Funcion para actualizar las filas despues de la eliminacion de una fila
	// para que se puedan validar las filas despues de la modificacion
	function actualizarFila(table,i,rowCount){
		for (var j=i; j<rowCount-1; j++){
			var num_piezas = table.rows[j+1].cells[5].childNodes[0].value;
			table.rows[j+1].deleteCell(5);	
			var td_precio = table.rows[j+1].insertCell(5);
			td_precio.setAttribute("style","text-align:center");
			td_precio.innerHTML = '<input type="text" id="piezas[]" name="piezas[]" class="CampoPiezasInput" value="' + num_piezas + '" onblur="javascript:validarHayCaracter(' + j + ')"/>';
		}
	}
	
	// Funcion para eliminar una referencia del kit
	function removeRow(tableID) {
		try {
			table = document.getElementById('mitabla');
			var rowCount = table.rows.length;

			for(var i=0; i<rowCount; i++) {
				// i = 0 -> Cabecera de la tabla
				var row = table.rows[i];
				var chkbox = row.cells[10].childNodes[0];
				if(null != chkbox && true == chkbox.checked) {
					table.deleteRow(i);
					rowCount--;
					i--;
					if (i+1 != rowCount){
						actualizarFila(table,i,rowCount);
					}
				}
			}
			// costeTotal = calculaCoste(table);
			costeTotal = damePrecioComponenteConHeredadas(table);
			actualizarCoste(costeTotal);
		}
		catch(e) {
			alert(e);
		}
	}
	
	// Funcion que calcula el nuevo coste cuando se modifica el campo piezas de una referencia 
	// y cambia el precio de la referencia modificada
	function modificaPrecioReferencia(piezas,fila){
		try {
			var table = document.getElementById('mitabla');
			fila = parseInt(fila) + 1;
			var nuevo_coste_referencia = 0;
			var row = table.rows[fila];
			piezas = parseFloat(piezas);
			piezas = piezas * 100;
			piezas = Math.round(piezas)/100;			
			var precio_unidad = row.cells[8].childNodes[0].nodeValue;
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
	
	// Funcion para validar que el campo piezas sea un number
	// Si es correcto se modifica el campo precio de la referencia
	function validarHayCaracter(fila) {
		var table = document.getElementById('mitabla');
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
			// costeTotal = calculaCoste(table);
			costeTotal = damePrecioComponenteConHeredadas(table);
			actualizarCoste(costeTotal);
		}
		else {
			alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");	
		}
	}	
	
	// Funcion que calcula el coste total de las referencias 
	function calculaCoste(tableId){
		try {
			table = document.getElementById('mitabla');
			var rowCount = table.rows.length;
			var coste_ref = 0;
			var costeTotal = 0;

			for(var i=1; i<rowCount; i++) {
				var row = table.rows[i];
				coste_ref = parseFloat(row.cells[9].childNodes[0].nodeValue);
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
	
	// Funcion que actualiza el coste total del componente 
	function actualizarCoste(costeTotal){
		try{
			costeTotal = parseFloat(costeTotal);
			costeTotal = costeTotal * 100;
			costeTotal = Math.round(costeTotal) / 100;
			costeTotal = costeTotal.toFixed(2);
			capa_coste = document.getElementById('CosteTotalComponente');
			capa_coste.innerHTML = '<span class="fuenteSimumakNegrita">' + costeTotal + ' € </span>';
		}
		catch(e) {
			alert(e);
		}
	}

	// Funcion que quita la tabla de carga de referencias y añade un enlace para subir un archivo
	function cargaArchivoImportacion(){
		var capa_referencias = document.getElementById('capa_referencias');
		var capa_opciones = document.getElementById('capa_opciones');
		var capa_coste_componente = document.getElementById('coste_componente');
		var capa_boton_metodo = document.getElementById('capa_boton_metodo');

		// Mostramos las opciones para la subida del excel de importacion 
		capa_referencias.style.display = "none"; 
		capa_opciones.style.display = "block"; 
		capa_coste_componente.style.display = "block";
		
		// Cambiamos el boton de metodo de importacion 
		capa_boton_metodo.innerHTML = '<input type="button" id="importar_normal" name="importar_normal" class="BotonEliminar" value="IMPORTACIÓN NORMAL" onclick="cargaNormal();" /><input type="hidden" id="metodo" name="metodo" value="masivo">';  
	}

	// Funcion que vuelve a mostrar la tabla y las opciones para la carga normal 
	function cargaNormal(){
		var capa_referencias = document.getElementById('capa_referencias');
		var capa_opciones = document.getElementById('capa_opciones');
		var capa_coste_componente = document.getElementById('coste_componente');
		var capa_boton_metodo = document.getElementById('capa_boton_metodo');	

		// Mostramos de nuevo la tabla
		capa_opciones.style.display = "none";
		capa_referencias.style.display = "block";
		capa_coste_componente.style.display = "block";
		
		// Cambiamos el boton para la importacion masiva
		capa_boton_metodo.innerHTML = '<input type="button" id="importar_excel" name="importar_excel" class="BotonEliminar" value="IMPORTAR DESDE EXCEL" onclick="cargaArchivoImportacion();" /><input type="hidden" id="metodo" name="metodo" value="normal">';
	}
	