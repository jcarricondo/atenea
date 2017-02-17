// Fichero con las funciones de javascript del módulo ALMACEN PERIFÉRICOS
function Abrir_ventana(pagina) {
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=500, top=100, left=350";
	window.open(pagina,"",opciones);
}

// Función que carga los almacenes de una sede
function cargaAlmacenes(id_sede){
    // Reseteamos los campos del formulario
    // resetearCamposFormulario();

    // Hacemos la llamada a la función AJAX para la carga de las OPs según la sede
    var ajax = objetoAJAX();
    ajax.open("GET","../ajax/almacen_perifericos/almacen_perifericos.php?func=cargaAlmacenes&id_sede=" + id_sede,"true");
    ajax.onreadystatechange=function() {
        if (ajax.readyState==4 && ajax.status==200) {
            document.getElementById("capaAlmacenes").innerHTML=ajax.responseText;
        }
    }
    ajax.send(null);
}

// Función para comprobar si el número de serie del periférico tiene un formato correcto
function comprobacionPeriferico(num_serie){
	// FORMATO: 000000-000000000000000
	var ExpReg = /^[0-9]{6}-[0-9]{15}$/;
	if ((num_serie == num_serie.match(ExpReg)) && (num_serie != '')){
		return true;
	}
	else{
		return false;
	}
}

// Función que comprueba si existe ya el periférico en la tabla LOG
function comprobarNumSerie(num_serie){
	var tabla = document.getElementById("tabla_log");
	var i = 0; 
	var error = false;
	var num_filas = tabla.rows.length; 

	while ((i<num_filas) && (!error)){
		var num_serie_tabla = tabla.rows[i].cells.item(0).innerHTML;
		error = (num_serie_tabla == num_serie);
		i++;
	}
	return error;
}

// Función que carga el periférico a recepcionar
function cargaPeriferico(){
	var num_serie = document.getElementById("num_serie").value;
	var metodo = document.getElementById('metodo').value;
	var id_almacen = document.getElementById('id_almacen').value;

	var codigo = "";
	var codigo_length = 3;

	// Comprobamos si el numero de serie introducido tiene un formato correcto
	if(comprobacionPeriferico(num_serie)){
		// Comprobamos si existe el periferico con ese numero de serie
		if(num_serie.length != 0){
			var ajax = objetoAJAX();
			ajax.open("GET","../ajax/almacen_perifericos/almacen_perifericos.php?func=comprobarNumSerie&num_serie=" + num_serie + "&metodo=" + metodo + "&id_almacen=" + id_almacen,"true");
			ajax.onreadystatechange=function() {
				if(ajax.readyState==4 && ajax.status==200) {
					document.getElementById("cargaPeriferico").innerHTML=ajax.responseText;
				}
			}
			ajax.send(null);
		}
		else {
			alert("Introduzca el número de serie del periférico");
		}
	}
	else {
		alert("El formato del periferico no es correcto.\n\nIntroduzca un periferico con formato 000000-000000000000000\n\nNOTA: 6 DIGITOS '-' 15 DIGITOS");
	}
}

// Función para recepcionar los periféricos uno por uno.
// Se guardan el periférico y su estado dependiento de si esta averiado
function recepcionarPeriferico(num_serie,id_almacen){
	// Comprobamos que no se haya añadido el periferico
	var error = comprobarNumSerie(num_serie);

	if(!error){
		// Obtenemos los elementos de la pagina
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');	
		var id_albaran = document.getElementById('id_albaran_global').value;
		var id_periferico = document.getElementById('id_periferico_hidden').value;
		var nombre_tipo = document.getElementById('nombre_tipo_hidden').value;
		var averiado = document.getElementById('averiado');
		var id_estado_antiguo = document.getElementById('id_estado_hidden').value;
        var esta_averiado;
		
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
                url: "../ajax/almacen_perifericos/almacen_perifericos.php?func=recepcionar",
                data: "num_serie=" + num_serie + "&id_periferico=" + id_periferico + "&esta_averiado=" + esta_averiado + "&id_almacen=" + id_almacen,
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
            var cell_4 = row.insertCell(3);

            cell_2.setAttribute("style", "text-align:center");
            cell_3.setAttribute("style", "text-align:center");
            cell_4.setAttribute("style", "text-align:center");

            // Insertamos el periferico
            table.rows[fila].cells[0].innerHTML = num_serie;
            table.rows[fila].cells[1].innerHTML = nombre_tipo;
            table.rows[fila].cells[2].innerHTML = esta_averiado
            table.rows[fila].cells[3].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir(' + "'" + 'popup_log.php?id_albaran=' + id_albaran + '&num_serie=&quot;' + num_serie + '&quot;&metodo=&quot;RECEPCIONAR&quot;&averiado=&quot;' + esta_averiado + '&quot;' + "'" + ') " />';
            table.rows[fila].cells[4].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerRecepcion(this,' + id_periferico + ',' + id_estado_antiguo + ')" />';

            // Eliminamos el periferico del buscador
            table_buscador.rows[1].cells[0].innerHTML = "";
            table_buscador.rows[1].cells[1].innerHTML = "";
            table_buscador.rows[1].cells[2].innerHTML = "";
            table_buscador.rows[1].cells[3].innerHTML = "";
            table_buscador.rows[1].cells[4].innerHTML = "";

            table_buscador.rows[1].cells[0].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[1].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[2].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[3].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[4].setAttribute("style", "height: 35px;");
        }
        else {
            alert(error_recepcion);
        }
	}
	else {
		alert("Ya se realizó una operación con ese periferico. Elimine el movimiento del periferico y vuelva a realizar la operación de nuevo")
	}
}

// Función que deshace la recepción
function deshacerRecepcion(r, id_periferico, id_estado){
	var id_albaran = document.getElementById('id_albaran_global').value;
	
	// Realizamos la petición al servidor para deshacer la recepción
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen_perifericos/almacen_perifericos.php?func=deshacerRecepcion",
            data: "id_periferico=" + id_periferico + "&id_estado=" + id_estado + "&id_albaran=" + id_albaran,
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

// Funcion para desrecepcionar los perifericos uno por uno 
function desrecepcionarPeriferico(num_serie,id_almacen){
	// Comprobamos que no se haya añadido el periferico
	var error = comprobarNumSerie(num_serie);

	if(!error){
		// Obtenemos los elementos de la pagina
		var id_periferico = document.getElementById('id_periferico_hidden').value;
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');	
		var id_albaran = document.getElementById('id_albaran_global').value;
		var nombre_tipo = document.getElementById('nombre_tipo_hidden').value;
		var id_estado_antiguo = document.getElementById('id_estado_hidden').value;

        // Realizamos la petición al servidor para realizar la desrecepción
        var respuesta = (function () {
            var respuesta = null;
            $.ajax({
                dataType: "json",
                url: "../ajax/almacen_perifericos/almacen_perifericos.php?func=desrecepcionar",
                data: "num_serie=" + num_serie + "&id_periferico=" + id_periferico + "&id_almacen=" + id_almacen,
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
            var cell_4 = row.insertCell(3);

            cell_2.setAttribute("style", "text-align:center");
            cell_3.setAttribute("style", "text-align:center");
            cell_4.setAttribute("style", "text-align:center");

            // Insertamos el periferico
            table.rows[fila].cells[0].innerHTML = num_serie;
            table.rows[fila].cells[1].innerHTML = nombre_tipo;
            table.rows[fila].cells[2].innerHTML = "-";
            table.rows[fila].cells[3].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir(' + "'" + 'popup_log.php?id_albaran=' + id_albaran + '&num_serie=&quot;' + num_serie + '&quot;&metodo=&quot;DESRECEPCIONAR&quot;&averiado=NO' + "'" + ') " />';
            table.rows[fila].cells[4].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerDesRecepcion(this,' + id_periferico + ',' + id_estado_antiguo + ')" />';

            // Eliminamos el periferico del buscador
            table_buscador.rows[1].cells[0].innerHTML = "";
            table_buscador.rows[1].cells[1].innerHTML = "";
            table_buscador.rows[1].cells[2].innerHTML = "";
            table_buscador.rows[1].cells[3].innerHTML = "";
            table_buscador.rows[1].cells[4].innerHTML = "";

            table_buscador.rows[1].cells[0].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[1].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[2].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[3].setAttribute("style", "height: 35px;");
            table_buscador.rows[1].cells[4].setAttribute("style", "height: 35px;");
        }
        else {
            alert(error_desrecepcion);
        }
	}
	else {
		alert("Ya se realizó una operación con ese periferico. Elimine el movimiento del periferico y vuelva a realizar la operación de nuevo")
	}
}

// Funcion que deshace la desrecepcion 
function deshacerDesRecepcion(r, id_periferico, id_estado){
	var id_albaran = document.getElementById('id_albaran_global').value;

    // Realizamos la petición al servidor para deshacer la recepción
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen_perifericos/almacen_perifericos.php?func=deshacerDesRecepcion",
            data: "id_periferico=" + id_periferico + "&id_estado=" + id_estado + "&id_albaran=" + id_albaran,
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

    if(!hubo_error) {
        // Eliminamos la fila de la tabla
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("tabla_log").deleteRow(i);
    }
    else {
        alert(error_deshacer);
    }
}

// Función que cambia el estado de un periférico "AVERIADO" o "EN REPARACIÓN"
function cambiarEstado(id_periferico,estado){
    var id_usuario = document.getElementById("id_usuario_hidden").value;
	if(confirm("¿Está seguro de realizar el cambio de estado del periférico?"))	{
        // Realizamos la petición al servidor para hacer el cambio de estado
        var respuesta = (function () {
            var respuesta = null;
            $.ajax({
                dataType: "json",
                url: "../ajax/almacen_perifericos/almacen_perifericos.php?func=cambiarEstado",
                data: "id_periferico=" + id_periferico + "&estado=" + estado + "&id_usuario=" + id_usuario,
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
            if(estado == "AVERIADO") {
                var celda_estado = document.getElementById('estado');
                celda_estado.innerHTML = '<span style="color: green;">EN REPARACION</span>';
                var celda_boton_estado = document.getElementById('boton_estado');
                celda_boton_estado.innerHTML = '<input type="button" class="BotonEliminar" style="margin-left: 10px;" value="OPERATIVO" onclick="cambiarEstado(' + id_periferico + ',' + '&quot;EN REPARACION&quot;)">';
            }
            else if(estado == "EN REPARACION") {
                var celda_estado = document.getElementById('estado');
                celda_estado.innerHTML = '<span style="color: green;">OPERATIVO</span>';
                var celda_boton_estado = document.getElementById('boton_estado');
                celda_boton_estado.innerHTML = '<input type="button" class="BotonEliminar" style="margin-left: 10px;" value="AVERIADO" onclick="cambiarEstado(' + id_periferico + ',' + '&quot;OPERATIVO&quot;)">';
            }
            else {
                // estado == OPERATIVO
                var celda_estado = document.getElementById('estado');
                celda_estado.innerHTML = '<span style="color: green;">AVERIADO</span>';
                var celda_boton_estado = document.getElementById('boton_estado');
                celda_boton_estado.innerHTML = '<input type="button" class="BotonEliminar" style="margin-left: 10px;" value="EN REPARACION" onclick="cambiarEstado(' + id_periferico + ',' + '&quot;AVERIADO&quot;)">';
            }
        }
        else {
            alert(error_cambio);
        }
	}
}

// Función que carga los tipos de motivos de los albaranes del almacen de perifericos
function cargaMotivos(id_almacen,tipo_albaran){
    // Realizamos la petición al servidor para obtener los motivos del albaran
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen_perifericos/almacen_perifericos.php?func=cargaMotivos",
            data: "id_almacen=" + id_almacen + "&tipo_albaran=" + tipo_albaran,
            type: "GET",
            async: false,
            success: function (data) {
                respuesta = data;
            }
        });
        return respuesta;
    })();

    var capa_motivo = document.getElementById('capa_motivo');
    var salida = '';

    if(respuesta == null) {
        capa_motivo.innerHTML = "";
        salida = '<input type="hidden" id="motivo" name="motivo" value=""/>';
        capa_motivo.innerHTML = salida;
    }
    else {
        salida += '<div class="LabelCreacionBasico">Motivo * </div>';
        salida += '<select id="motivo" name="motivo" class="CreacionBasicoInput">';
        for(var i in respuesta) salida += '<option value="' + respuesta[i] + '">' + respuesta[i] + '</option>';
        salida += '</select>';
        capa_motivo.innerHTML = salida;
    }
}

// Función que carga los tipos de motivos de los albaranes de periféricos para el buscador
function cargaMotivosBuscador(id_sede){
    // Realizamos la petición al servidor para obtener los motivos del albaran
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen_perifericos/almacen_perifericos.php?func=cargaMotivosBuscador",
            data: "id_sede=" + id_sede,
            type: "GET",
            async: false,
            success: function (data) {
                respuesta = data;
            }
        });
        return respuesta;
    })();

    var select_motivo = document.getElementById('tipo_motivo');
    var salida = "";
    salida += '<option></option>';

    for(var i in respuesta) salida += '<option>' + respuesta[i] + '</option>';
    select_motivo.innerHTML = salida;
}

// Función que cambia los campos del buscador de albaranes de periféricos según la sede
function cambiaCamposBuscadorAlbaran(id_sede){
    cargaAlmacenes(id_sede);
    cargaMotivosBuscador(id_sede);
}

// Función que descarga el informe del albarán
function cerrarAlbaran(id_albaran){
	if (confirm("¿Desea finalizar la operación?")){
        // Desactivamos la validación para que pueda redirigir la página
        window.onbeforeunload = null;

		// Comprobamos si el albarán esta vacío
		var tabla = document.getElementById("tabla_log");
		var num_filas = tabla.rows.length-1;

		if(num_filas == 0){
			// Albaran vacio
			window.location="../almacen_perifericos/albaranes_perifericos.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=1";
		}
		else{
			window.location="../almacen_perifericos/albaranes_perifericos.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=0";
		}	
	}
}

// Descarga el excel de periféricos de listado periféricos
function descargar_XLS_Perifericos(){
	window.location="informe_perifericos.php";
}

// Descarga el excel de movimientos de albaranes de periféricos
function descargar_XLS_Movimientos(){
	window.location="informe_movimientos.php";
}

// Función para resetear los campos de los formularios de Almacen de Periféricos
function resetearCamposFormulario(){
    var num_serie = document.getElementById("num_serie");
    var nombre_albaran = document.getElementById("nombre_albaran");
    var fecha_creacion = document.getElementById("datepicker_albaranes_perifericos_creacion");
    if(fecha_creacion != null) fecha_creacion.value = "";
    if(nombre_albaran != null) nombre_albaran.value = "";
    if(num_serie != null) num_serie.value = "";

    var id_usuario = document.getElementsByName("id_usuario");
    if(id_usuario != null){
        for(i=0;i<id_usuario.length;i++) {
            id_usuario[i].selectedIndex = 0;
        }
    }

    var id_centro_logistico = document.getElementsByName("id_centro_logistico");
    if(id_centro_logistico != null){
        for(i=0;i<id_centro_logistico.length;i++) {
            id_centro_logistico[i].selectedIndex = 0;
        }
    }

    var opciones_estado = document.getElementsByName("estado_periferico");
    if(opciones_estado != null) {
        for(i=0;i<opciones_estado.length;i++) {
            opciones_estado[i].selectedIndex = 0;
        }
    }

    var opciones_tipo = document.getElementsByName("tipo_periferico");
    if(opciones_tipo != null) {
        for(i=0;i<opciones_tipo.length;i++) {
            opciones_tipo[i].selectedIndex = 0;
        }
    }

}









