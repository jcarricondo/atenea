// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el proceso de creación de un periferico

function abrir(url) {
	open(url,'','top=200,left=700,width=500,height=500') ;
}

function Abrir_ventana(pagina) {
    /* width=820px height=500px */
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=600, top=100, left=350";
    window.open(pagina,"",opciones);
}
	
	var div = document.getElementById('CapaTablaIframe');
	var table = document.createElement('tabla');
	var row = table.insertRow(0);
	var cell = row.insertCell(0);
	div.appendChild(table);

	// Funcion para añadir una referencia al periférico
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

		// Calculamos el coste de todas las referencias que hubiera en la tabla
		// costeTotal = calculaCoste(table);
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

	// Funcion para eliminar una referencia del periferico
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

	
	// Funcion que calcula el coste total de las referencias de la tabla periferico
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

	// Función que actualiza el coste total del componente mas el coste de los kits
	function actualizarCoste(costeTotal){
		try{
            // Coste del periferico
            costeTotal = parseFloat(costeTotal);
            // Actualizamos el precio del periferico
            document.getElementById('coste_periferico').setAttribute('value',costeTotal);

            // Coste de los kits
            var input_coste_kits = document.getElementById('costeKits');
            var coste_kits = input_coste_kits.value;
            coste_kits = parseFloat(coste_kits);

            var costeTotalPeriferico = costeTotal + coste_kits;
            document.getElementById('coste_total').setAttribute('value',costeTotalPeriferico);

            costeTotalPeriferico = costeTotalPeriferico * 100;
            costeTotalPeriferico = Math.round(costeTotalPeriferico) / 100;
            costeTotalPeriferico = costeTotalPeriferico.toFixed(2);
            costeTotal = costeTotal.toFixed(2);
            var capa_coste = document.getElementById('CosteTotalComponente');
            capa_coste.innerHTML = '<span class="fuenteSimumakNegrita">' + costeTotalPeriferico.replace('.',',') + '€ </span>';
            document.getElementById('capa_coste_periferico').innerHTML = costeTotal.replace('.',',') + '€';
		}
		catch(e) {
			alert(e);
		}
	}

	// Añadir kit a la segunda lista
	function AddKitToSecondList(){
		var fl = document.getElementById('kits_no_asignados[]');
		var sl = document.getElementById('kit[]');
        var contador_kits = sl.options.length;
	
		for(i=0;i<fl.options.length;i++){
			if(fl.options[i].selected){
				if(fl.options[i].style.display === "block"){
					// Añadimos la opcion a la lista 1
					var option = document.createElement("option");
					option.value = fl[i].value;
					option.text = fl[i].text;
					fl.add(option,i);
					sl.add(fl.options[i],null);
					// Deseleccionar campo origen
					fl.options[i].selected = false;
					var id_componente = option.value;

					// Realizamos la petición al servidor para obtener los datos y referencias del componente
					var respuesta = (function () {
						var respuesta = null;
						$.ajax({
							dataType: "json",
							url: "../ajax/basicos/nuevo_periferico.php?func=loadComp",
							data: "id=" + id_componente,
							type: "GET",
							async: false,
							success: function (data) {
								respuesta = data;
							}
						});
						return respuesta;
					})();

					// Obtenemos la capa contenedora para añadir los kits
					var capa_contenedora = document.getElementById('capa_kits');
					var id_capa_kit = 'kit-' + contador_kits;
					var coste_input_kit = 'coste_kit-' + contador_kits;
					var kit_repetido = document.getElementById(id_capa_kit) != null ;
					var salida = '';

					// Si ya se insertó ese kit le asignamos otro id
					while(kit_repetido){
						contador_kits++;
						id_capa_kit = 'kit-' + contador_kits;
						kit_repetido = document.getElementById(id_capa_kit) != null ;
					}

					// Preparamos el HTML con las referencias
					salida = '<div id="' + id_capa_kit + '" class="ContenedorCamposCreacionBasico" style="display: block;">';
					salida += '<div class="LabelCreacionBasico">Referencias Kit</div>';
					salida += '<div class="tituloComponente">' + respuesta.nombre + '</div>';
					salida += '<div class="CajaReferencias"><div id="CapaTablaIframe"><table id="mitabla-' + id_capa_kit + '">';
					salida += '<tr><th style="text-align:center;">ID REF</th><th>NOMBRE</th><th>PROVEEDOR</th><th>REF. PROVEEDOR</th><th>NOMBRE PIEZA</th><th style="text-align:center;">PIEZAS</th><th style="text-align:center;">PACK PRECIO</th><th style="text-align:center;">UDS/P</th><th style="text-align:center;">PRECIO UNIDAD</th><th style="text-align:center;">PRECIO</th></tr>';

					var precio_kit = 0;
					// Cargamos cada fila de la tabla correspondiente a una referencia
					for(var j in respuesta.referencias){
						salida += '<tr>';
						salida += '<td style="text-align:center;">' + respuesta.referencias[j].id_referencia + '</td>';
						salida += '<td>' + respuesta.referencias[j].nombre + '</td>';
						salida += '<td>' + respuesta.referencias[j].nombre_proveedor + '</td>';
						salida += '<td>' + respuesta.referencias[j].ref_proveedor + '</td>';
						salida += '<td>' + respuesta.referencias[j].nombre_pieza +'</td>';
						salida += '<td style="text-align:center;">' + respuesta.referencias[j].piezas + '</td>';
						salida += '<td style="text-align:center;">' + respuesta.referencias[j].pack_precio + '</td>';
						salida += '<td style="text-align:center;">' + respuesta.referencias[j].uds_paquete + '</td>';
						salida += '<td style="text-align:center;">' + respuesta.referencias[j].precio_unidad + '</td>';
						salida += '<td style="text-align:center;">' + respuesta.referencias[j].precio + '</td>';
						salida += '</tr>';
						precio_kit = precio_kit + parseFloat(respuesta.referencias[j].precio);
					}
					precio_kit = precio_kit * 100;
					precio_kit = Math.round(precio_kit) / 100;
					var precio_kit_string = precio_kit.toFixed(2);
					precio_kit_string = precio_kit_string.replace('.',',');

					salida += '</table></div></div>';
					salida += '<div class="LabelCreacionBasico" style="margin-top:5px;">Coste Kit</div>';
					salida += '<div class="tituloComponente">';
					salida += '<table id="tablaTituloPrototipo">';
					salida += '<tr>';
					salida += '<td style="text-align:left; background:#fff; vertical-align:top; padding:5px 5px 0px 0px;">';

					salida += '<span id="coste-' + id_capa_kit + '" class="tituloComp">' + precio_kit_string + '€</span>';
					salida += '</td></tr></table>';
					salida += '<input type="hidden" id="' + coste_input_kit + '" value="' + precio_kit + '" />';
					salida += '</div><br/></div>';

					// Actualizamos el coste total de kits
					/* var costeKits = parseFloat(document.getElementById('costeKits').value);
					 costeKits = costeKits + precio_kit;
					 document.getElementById('costeKits').setAttribute('value',costeKits);*/

					// Mostramos la salida por pantalla
					capa_contenedora.innerHTML = capa_contenedora.innerHTML + salida;

					// Una vez se ha añadido el kit calculamos el coste con sus referencias heredadas
					var mitabla = document.getElementById("mitabla-" + id_capa_kit);
					var span_coste_kit = "coste-" + id_capa_kit;
					precio_kit = damePrecioKitConHeredadas(mitabla);
					actualizarPrecioKit(span_coste_kit,coste_input_kit,precio_kit);

					contador_kits++;
					// Actualizamos el precio total del periferico
					sumaPrecioComponentePeriferico(precio_kit);
				}
			}
		}
		return true;
	}

	// Eliminar kit de la lista
	function DeleteKitSecondListItem(){
		var sl = document.getElementById('kit[]');
        var j=0;
	
		for(i=0;i<sl.options.length;i++){
			if(sl.options[i].selected){
                // Borramos el elemento del select multiple
                sl.remove(sl.selectedIndex);
                var num_total_kits = sl.options.length;

                // Borramos la capa del kit seleccionado
                var id_capa_kit = 'kit-' + i;
                var id_capa_kit_ant = id_capa_kit;
                var id_input_coste_kit = 'coste_kit-' + i;
                var id_input_coste_kit_ant = id_input_coste_kit;
                var precio_kit = document.getElementById(id_input_coste_kit).value;
                var capa_kit_remove = document.getElementById(id_capa_kit);
                if(capa_kit_remove.parentNode){
                    capa_kit_remove.parentNode.removeChild(capa_kit_remove);
                }

                // Modificamos los ids de las capas de los kits
                for(j=i;j<num_total_kits;j++){
                    var aux = j+1;
                    id_capa_kit = 'kit-' + aux;
                    id_input_coste_kit = 'coste_kit-' + aux;
                    document.getElementById(id_capa_kit).setAttribute('id',id_capa_kit_ant);
                    id_capa_kit_ant = 'kit-' + aux;
                    document.getElementById(id_input_coste_kit).setAttribute('id',id_input_coste_kit_ant);
                    id_input_coste_kit_ant = 'coste_kit-' + aux;
                }

                // Actualizamos el coste total de kits
                var costeKits = parseFloat(document.getElementById('costeKits').value);
                costeKits = costeKits - precio_kit;
                if(costeKits < 0) costeKits = 0;
                costeKits = costeKits * 100;
                costeKits = Math.round(costeKits) / 100;
                document.getElementById('costeKits').setAttribute('value',costeKits);

                // Actualizamos el precio total del periferico
                restaPrecioComponentePeriferico(precio_kit);
                i--;
			}
		}
		return true;
	}

	// Seleccionar kits para POST
	function SeleccionarKits(){
		var lista = document.getElementById("kit[]");
		for	(i = 0; i<lista.options.length; i++){				
			lista[i].selected = "selected";
		}
	}

	// Selecciona los kits del periferico
	function SeleccionarComponentes() {
		SeleccionarKits();
		return true;	
	}	

	// Función que quita la tabla de carga de referencias y añade un enlace para subir un archivo
	function cargaArchivoImportacion(){
		var capa_referencias = document.getElementById('capa_referencias');
		var capa_opciones = document.getElementById('capa_opciones');
		var capa_coste_componente = document.getElementById('coste_componente');
		var capa_boton_metodo = document.getElementById('capa_boton_metodo');
        var capa_coste_solo_componente = document.getElementById('coste_solo_componente');

		// Mostramos las opciones para la subida del excel de importacion 
		capa_referencias.style.display = "none"; 
		capa_opciones.style.display = "block"; 
		capa_coste_componente.style.display = "none";
        capa_coste_solo_componente.style.display = "none";
		
		// Cambiamos el boton de metodo de importacion 
		capa_boton_metodo.innerHTML = '<input type="button" id="importar_normal" name="importar_normal" class="BotonEliminar" value="IMPORTACIÓN NORMAL" onclick="cargaNormal();" /><input type="hidden" id="metodo" name="metodo" value="masivo">';  
	}

	// Funcion que vuelve a mostrar la tabla y las opciones para la carga normal 
	function cargaNormal(){
		var capa_referencias = document.getElementById('capa_referencias');
		var capa_opciones = document.getElementById('capa_opciones');
		var capa_coste_componente = document.getElementById('coste_componente');
		var capa_boton_metodo = document.getElementById('capa_boton_metodo');
        var capa_coste_solo_componente = document.getElementById('coste_solo_componente');

		// Mostramos de nuevo la tabla
		capa_opciones.style.display = "none";
		capa_referencias.style.display = "block";
		capa_coste_componente.style.display = "block";
        capa_coste_solo_componente.style.display = "block";
		
		// Cambiamos el boton para la importacion masiva
		capa_boton_metodo.innerHTML = '<input type="button" id="importar_excel" name="importar_excel" class="BotonEliminar" value="IMPORTAR DESDE EXCEL" onclick="cargaArchivoImportacion();" /><input type="hidden" id="metodo" name="metodo" value="normal">';
	}

    // Función que suma el precio de un kit al precio total del periférico
    function sumaPrecioComponentePeriferico(precio_componente){
        // Obtenemos el precio total del periferico
        var capa_coste_total = document.getElementById('CosteTotalComponente');
        var input_coste_total = document.getElementById('coste_total');
        var coste_total = input_coste_total.value;
        // Convertimos el coste total para poder operar con él
        coste_total = parseFloat(coste_total);
        coste_total = coste_total * 100;
        coste_total = Math.round(coste_total) / 100;
        // Convertimos el precio del componente para poder operar con él
        precio_componente = parseFloat(precio_componente);
        precio_componente = precio_componente * 100;
        precio_componente = Math.round(precio_componente) / 100;
        // Sumamos al coste total el precio del componente añadido
        coste_total = coste_total + precio_componente;
        coste_total = coste_total * 100;
        coste_total = Math.round(coste_total) / 100;
        var coste_total_string = coste_total.toFixed(2);
        coste_total_string = coste_total_string.replace('.',',');
        // Actualiamos en pantalla el coste total
        input_coste_total.setAttribute('value', coste_total);
        capa_coste_total.innerHTML = '<span class="fuenteSimumakNegrita">' + coste_total_string + '€</span>';
    }

    // Función que resta el precio de un kit al precio total del periferico
    function restaPrecioComponentePeriferico(precio_componente){
        // Obtenemos el precio total del periferico
        var capa_coste_total = document.getElementById('CosteTotalComponente');
        var input_coste_total = document.getElementById('coste_total');
        var coste_total = input_coste_total.value;
        // Convertimos el coste total para poder operar con él
        coste_total = parseFloat(coste_total);
        coste_total = coste_total * 100;
        coste_total = Math.round(coste_total) / 100;
        // Convertimos el precio del componente para poder operar con él
        precio_componente = parseFloat(precio_componente);
        precio_componente = precio_componente * 100;
        precio_componente = Math.round(precio_componente) / 100;
        // Restamos al coste total el precio del componente añadido
        coste_total = coste_total - precio_componente;
        coste_total = coste_total * 100;
        coste_total = Math.round(coste_total) / 100;
        if(coste_total <= 0) coste_total = 0;
        var coste_total_string = coste_total.toFixed(2);
        coste_total_string = coste_total_string.replace('.',',');
        // Actualiamos en pantalla el coste total
        input_coste_total.setAttribute('value', coste_total);
        capa_coste_total.innerHTML = '<span class="fuenteSimumakNegrita">' + coste_total_string + '€</span>';
    }

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
		var input_buscador = document.getElementById("BuscadorKitNewPeriferico");
		input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('todos','BuscadorKitNewPeriferico','kits_no_asignados[]')");

		var selectKits = document.getElementById("kits_no_asignados[]");
		for(i=0;i<selectKits.length;i++) {
			var option = selectKits.options[i];
			option.style.display = "block";
			if(option.id == "") option.selected = false;
		}
		BuscadorDinamicoComponentes('todos','BuscadorKitNewPeriferico','kits_no_asignados[]');
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
		var input_buscador = document.getElementById("BuscadorKitNewPeriferico");
		input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('produccion','BuscadorKitNewPeriferico','kits_no_asignados[]')");

		selectKits = document.getElementById("kits_no_asignados[]");
		for (i = 0; i < selectKits.length; i++) {
			var option = selectKits.options[i];
			if (option.id == "") option.style.display = "none";
		}
		BuscadorDinamicoComponentes('produccion','BuscadorKitNewPeriferico','kits_no_asignados[]');
	}
