// Fichero con las funciones de javascript del módulo TALLER SIMULADORES
function Abrir_ventana(pagina) {
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=500, top=100, left=350";
	window.open(pagina,"",opciones);
}

// Función para comprobar si el número de serie del simulador tiene un formato correcto
function comprobacionSimulador(num_serie){
	// FORMATO: SMTGLD01-SMK-001-14-00028
	var ExpReg = /^[A-Z0-9]{8}-[A-Z0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{5}$/;

	if((num_serie == num_serie.match(ExpReg)) && (num_serie != '')){
		return true;
	}
	else{
		return false;
	}
}

// Función que comprueba si existe ya el simulador en la tabla LOG
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

// Función que carga el simulador a recepcionar
function cargaSimulador(){
	var num_serie = document.getElementById("num_serie").value;
	var metodo = document.getElementById('metodo').value;
	var id_taller = document.getElementById('id_taller').value;

	var codigo = "";
	var codigo_length = 3;

	// Comprobamos si el numero de serie introducido tiene un formato correcto
	if(comprobacionSimulador(num_serie)){
		// Comprobamos si existe el simulador con ese numero de serie
		if(num_serie.length != 0){
			var ajax = objetoAJAX();
			ajax.open("GET","../ajax/taller_simuladores/taller_simuladores.php?func=comprobarNumSerie&num_serie=" + num_serie + "&metodo=" + metodo + "&id_taller=" + id_taller,"true");
			ajax.onreadystatechange=function() {
				if(ajax.readyState==4 && ajax.status==200) {
					document.getElementById("cargaSimulador").innerHTML=ajax.responseText;
				}
			}
			ajax.send(null);
		}
		else {
			alert("Introduzca el número de serie del simulador");
		}
	}
	else {
		alert("El formato del simulador no es correcto.\n\nIntroduzca un simulador con formato (EJ) SMTGLD01-SMK-001-14-00028\n\n");
	}
}

// Función para recepcionar los simuladores uno por uno.
// Se guarda el simulador y su estado dependiendo de si esta averiado
function recepcionarSimulador(num_serie,id_taller){
	// Comprobamos que no se haya añadido el simuladores
	var error = comprobarNumSerie(num_serie);

	if(!error){
		// Obtenemos los elementos de la pagina
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');	
		var id_albaran = document.getElementById('id_albaran_global').value;
		var id_simulador = document.getElementById('id_simulador_hidden').value;
		var averiado = document.getElementById('averiado');
		var id_estado_antiguo = document.getElementById('id_estado_hidden').value;
		
		if(averiado.checked){
			esta_averiado = 'SI';
		}
		else{
			esta_averiado = 'NO';
		}

        // Realizamos la petición al servidor para realizar la recepción
        var respuesta = (function () {
            var respuesta = null;
            $.ajax({
                dataType: "json",
                url: "../ajax/taller_simuladores/taller_simuladores.php?func=recepcionar",
                data: "num_serie=" + num_serie + "&id_simulador=" + id_simulador + "&esta_averiado=" + esta_averiado + "&id_taller=" + id_taller,
                type: "GET",
                async: false,
                success: function (data) {
                    respuesta = data;
                }
            });
            return respuesta;
        })();

        var hubo_error = respuesta.mov.error == true;
        var error_recepcion = respuesta.mov.error_des;

        if(!hubo_error) {
            // Preparamos la tabla log para insertar un nuevo registro
            var pos = table.rows.length;
            var row = table.insertRow(pos);
            var fila = pos;

            var cell_0 = row.insertCell(0);
            var cell_1 = row.insertCell(1);
            var cell_2 = row.insertCell(2);
            var cell_3 = row.insertCell(3);

            cell_1.setAttribute("style", "text-align:center");
            cell_2.setAttribute("style", "text-align:center");
            cell_3.setAttribute("style", "text-align:center");

            // Insertamos el simulador
            table.rows[fila].cells[0].innerHTML = num_serie;
            table.rows[fila].cells[1].innerHTML = esta_averiado
            table.rows[fila].cells[2].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir(' + "'" + 'popup_log.php?id_albaran=' + id_albaran + '&num_serie=&quot;' + num_serie + '&quot;&metodo=&quot;RECEPCIONAR&quot;&averiado=&quot;' + esta_averiado + '&quot;' + "'" + ') " />';
            table.rows[fila].cells[3].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerRecepcion(this,' + id_simulador + ',' + id_estado_antiguo + ')" />';

            // Eliminamos el simulador del buscador
            table_buscador.rows[1].cells[0].innerHTML = "";
            table_buscador.rows[1].cells[1].innerHTML = "";
            table_buscador.rows[1].cells[2].innerHTML = "";
            table_buscador.rows[1].cells[3].innerHTML = "";

            table_buscador.rows[1].cells[0].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[1].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[2].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[3].setAttribute("style", "height: 35px;");
        }
        else {
            alert(error_recepcion);
        }
	}
	else {
		alert("Ya se realizó una operación con ese simulador. Elimine el movimiento del simulador y vuelva a realizar la operación de nuevo");
	}
}

// Funcion que deshace la recepcion 
function deshacerRecepcion(r, id_simulador, id_estado){
	var id_albaran = document.getElementById('id_albaran_global').value;

    // Realizamos la petición al servidor para deshacer la recepción
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/taller_simuladores/taller_simuladores.php?func=deshacerRecepcion",
            data: "id_simulador=" + id_simulador + "&id_estado=" + id_estado + "&id_albaran=" + id_albaran,
            type: "GET",
            async: false,
            success: function (data) {
                respuesta = data;
            }
        });
        return respuesta;
    })();

    var hubo_error = respuesta.mov.error == true;
    var error_deshacer = respuesta.mov.error_des;

    if(!hubo_error){
        // Eliminamos la fila de la tabla
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("tabla_log").deleteRow(i);
    }
    else {
        alert(error_deshacer);
    }
}

// Funcion para desrecepcionar los simuladores uno por uno 
function desrecepcionarSimulador(num_serie,id_taller){
	// Comprobamos que no se haya añadido el simulador
	var error = comprobarNumSerie(num_serie);

	if(!error){
		// Obtenemos los elementos de la pagina
		var id_simulador = document.getElementById('id_simulador_hidden').value;
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');	
		var id_albaran = document.getElementById('id_albaran_global').value;
		var id_estado_antiguo = document.getElementById('id_estado_hidden').value;

        // Realizamos la petición al servidor para realizar la desrecepción
        var respuesta = (function () {
            var respuesta = null;
            $.ajax({
                dataType: "json",
                url: "../ajax/taller_simuladores/taller_simuladores.php?func=desrecepcionar",
                data: "num_serie=" + num_serie + "&id_simulador=" + id_simulador + "&id_taller=" + id_taller,
                type: "GET",
                async: false,
                success: function (data) {
                    respuesta = data;
                }
            });
            return respuesta;
        })();

        var hubo_error = respuesta.mov.error == true;
        var error_desrecepcion = respuesta.mov.error_des;

        if(!hubo_error) {
            // Preparamos la tabla log para insertar un nuevo registro
            var pos = table.rows.length;
            var row = table.insertRow(pos);
            var fila = pos;

            var cell_0 = row.insertCell(0);
            var cell_1 = row.insertCell(1);
            var cell_2 = row.insertCell(2);
            var cell_3 = row.insertCell(3);

            cell_1.setAttribute("style", "text-align:center");
            cell_2.setAttribute("style", "text-align:center");
            cell_3.setAttribute("style", "text-align:center");

            // Insertamos el simulador
            table.rows[fila].cells[0].innerHTML = num_serie;
            table.rows[fila].cells[1].innerHTML = "-";
            table.rows[fila].cells[2].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir(' + "'" + 'popup_log.php?id_albaran=' + id_albaran + '&num_serie=&quot;' + num_serie + '&quot;&metodo=&quot;DESRECEPCIONAR&quot;&averiado=NO' + "'" + ') " />';
            table.rows[fila].cells[3].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerDesRecepcion(this,' + id_simulador + ',' + id_estado_antiguo + ')" />';

            // Eliminamos el simulador del buscador
            table_buscador.rows[1].cells[0].innerHTML = "";
            table_buscador.rows[1].cells[1].innerHTML = "";
            table_buscador.rows[1].cells[2].innerHTML = "";
            table_buscador.rows[1].cells[3].innerHTML = "";

            table_buscador.rows[1].cells[0].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[1].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[2].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[3].setAttribute("style", "height: 35px;");
        }
        else {
            alert(error_desrecepcion);
        }
	}
	else {
		alert("Ya se realizó una operación con ese simulador. Elimine el movimiento del simulador y vuelva a realizar la operación de nuevo");
	}
}

// Función que deshace la desrecepción
function deshacerDesRecepcion(r, id_simulador, id_estado){
	var id_albaran = document.getElementById('id_albaran_global').value;

    // Realizamos la petición al servidor para deshacer la desrecepción
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/taller_simuladores/taller_simuladores.php?func=deshacerDesRecepcion",
            data: "id_simulador=" + id_simulador + "&id_estado=" + id_estado + "&id_albaran=" + id_albaran,
            type: "GET",
            async: false,
            success: function (data) {
                respuesta = data;
            }
        });
        return respuesta;
    })();

    var hubo_error = respuesta.mov.error == true;
    var error_deshacer = respuesta.mov.error_des;

    if(!hubo_error){
        // Eliminamos la fila de la tabla
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("tabla_log").deleteRow(i);
    }
    else {
        alert(error_deshacer);
    }
}

// Función que cambia el estado de un simulador "AVERIADO" o "EN REPARACION"
function cambiarEstado(id_simulador,estado){
    var id_usuario = document.getElementById("id_usuario_hidden").value;
	if(confirm("¿Está seguro de realizar el cambio de estado del simulador?"))	{
        // Realizamos la petición al servidor para hacer el cambio de estado
        var respuesta = (function () {
            var respuesta = null;
            $.ajax({
                dataType: "json",
                url: "../ajax/taller_simuladores/taller_simuladores.php?func=cambiarEstado",
                data: "id_simulador=" + id_simulador + "&estado=" + estado + "&id_usuario=" + id_usuario,
                type: "GET",
                async: false,
                success: function (data) {
                    respuesta = data;
                }
            });
            return respuesta;
        })();

        var hubo_error = respuesta.mov.error == true;
        var error_cambio = respuesta.mov.error_des;

        if(!hubo_error) {
            /*
             // Hacemos la llamada a la funcion AJAX para cambiar el estado del simulador generando un albaran de ajuste
             var ajax = objetoAJAX();
             ajax.open("GET","../ajax/taller_simuladores/taller_simuladores.php?func=cambiarEstado&id_simulador=" + id_simulador + "&estado=" + estado,"true");
             ajax.onreadystatechange=function() {
             if (ajax.readyState==4 && ajax.status==200) {
             document.getElementById('mensaje_error').innerHTML = ajax.responseText;
             }
             }
             ajax.send(null);
             */

            if (estado == "AVERIADO") {
                var celda_estado = document.getElementById('estado');
                celda_estado.innerHTML = '<span style="color: green;">EN REPARACION</span>';
                var celda_boton_estado = document.getElementById('boton_estado');
                celda_boton_estado.innerHTML = '<input type="button" class="BotonEliminar" style="margin-left: 10px;" value="OPERATIVO" onclick="cambiarEstado(' + id_simulador + ',' + '&quot;EN REPARACION&quot;)">';
            }
            else if (estado == "EN REPARACION") {
                var celda_estado = document.getElementById('estado');
                celda_estado.innerHTML = '<span style="color: green;">OPERATIVO</span>';
                var celda_boton_estado = document.getElementById('boton_estado');
                celda_boton_estado.innerHTML = '<input type="button" class="BotonEliminar" style="margin-left: 10px;" value="AVERIADO" onclick="cambiarEstado(' + id_simulador + ',' + '&quot;OPERATIVO&quot;)">';
            }
            else {
                // estado == OPERATIVO
                var celda_estado = document.getElementById('estado');
                celda_estado.innerHTML = '<span style="color: green;">AVERIADO</span>';
                var celda_boton_estado = document.getElementById('boton_estado');
                celda_boton_estado.innerHTML = '<input type="button" class="BotonEliminar" style="margin-left: 10px;" value="EN REPARACION" onclick="cambiarEstado(' + id_simulador + ',' + '&quot;AVERIADO&quot;)">';
            }
        }
        else {
            alert(error_cambio);
        }
	}	
}

// Funcion que descarga el informe del albaran
function cerrarAlbaran(id_albaran){
	if (confirm("¿Desea finalizar la operación?")){
		// Comprobamos si el albaran esta vacio
		var tabla = document.getElementById("tabla_log");
		var num_filas = tabla.rows.length-1;

		if(num_filas == 0){
			// Albaran vacio
			window.location="../taller_simuladores/albaranes_simuladores.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=1";	
		}
		else{
			window.location="../taller_simuladores/albaranes_simuladores.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=0";
		}	
	}
}

// Descarga el excel de simuladores de listado simuladores
function descargar_XLS_Simuladores(){
	window.location="informe_simuladores.php";
}

// Descarga el excel de movimientos de albaranes de simuladores
function descargar_XLS_Movimientos(){
	window.location="informe_movimientos.php";
}

