// Funciones JavaScript correspondientes al módulo de ALMACEN

// Función para abrir una ventana modal
function Abrir_ventana(pagina){
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=1220, height=500, top=100, left=350";
	window.open(pagina,"",opciones);
}

// Función que carga los almacenes de una sede
function cargaAlmacenes(id_sede){
    // Reseteamos los campos del formulario
    resetearCamposFormulario();

    // Hacemos la llamada a la función AJAX para la carga de las OPs según la sede
    var ajax = objetoAJAX();
    ajax.open("GET","../ajax/almacen/almacen.php?comp=cargaAlmacenes&id_sede=" + id_sede,"true");
    ajax.onreadystatechange=function() {
        if (ajax.readyState==4 && ajax.status==200) {
            document.getElementById("capaAlmacenes").innerHTML=ajax.responseText;
        }
    }
    ajax.send(null);
}

// Función que carga las OPs al cambiar de Sede
function cargaOPsPorSede(id_sede){
    // Cargamos primero los almacenes según la sede
    cargaAlmacenes(id_sede);

    // Realizamos la petición al servidor para obtener la fila del buscador del almacen
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen/almacen.php?comp=cargaOpsPorSede",
            data: "id_sede=" + id_sede,
            type: "GET",
            async: false,
            success: function (data) {
                respuesta = data;
            }
        });
        return respuesta;
    })();

    // Obtenemos los campos del buscador que se van a actualizar
    var celda_op = document.getElementById('celda_op');
    var capa_orden_compra = document.getElementById('capaOrdenCompra');

    var salida = '';

    // Cargamos las órdenes de producción iniciadas de esa sede
    celda_op.innerHTML = "";
    salida = '<div class="Label">Orden Producción</div>';
    salida += '<div id="capaOrdenProduccion">';
    salida += '<select multiple="multiple" id="orden_produccion[]" name="orden_produccion[]" class="BuscadorOCEstadosOP" size="8" onchange="javascript:cargarOrdenesCompraVariasOP()">';
    salida += '<option></option>';

    // Cargamos las op iniciadas obtenidas en la variable JSON
    for(var i in respuesta.ops){
        salida += '<option value="' + respuesta.ops[i].id_produccion + '">' + respuesta.ops[i].alias_op + '</option>';
    }
    salida += '<option value="0">STOCK</option>';
    salida += '</select>';
    salida += '</div>';
    celda_op.innerHTML = salida;

    // Cargamos el input de las órdenes de compra
    var uri = '<input type="text" id="input_oc" name="orden_compra" class="BuscadorInputAlmacen" maxlength="50" value=""/>';
    capa_orden_compra.innerHTML = uri;
}

// Función que obtiene las Ordenes de Compra al seleccionar varias OP
function cargarOrdenesCompraVariasOP(id_sede){
	var ids_op = document.getElementById("orden_produccion[]");
	var ids_produccion = new Array();
	var id_produccion = new Array();

	var j = 0;
	// Si la primera esta seleccionada carga todas menos la primera y STOCK
	if(ids_op.item(0).selected){
		for(i=1;i<ids_op.length-1;i++){		
			id_produccion[j] = ids_op.item(i).value;
			j++;
		}	
	}	
	else{
		for(i=1;i<ids_op.length-1;i++){		
			if(ids_op.item(i).selected){
				id_produccion[j] = ids_op.item(i).value;
				j++;
			}	
		}
	}

    var proveedor = document.getElementById("proveedor").value;
    var ajax = objetoAJAX();
	ajax.open("GET","../ajax/almacen/almacen.php?comp=cargarOrdenesCompra&id_produccion=" + id_produccion + "&proveedor=" + proveedor + "&id_sede=" + id_sede,"true");
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4 && ajax.status==200) {
		   document.getElementById("capaOrdenCompra").innerHTML=ajax.responseText;
   		}
	}
	ajax.send(null);
}

// Función que carga la referencia a recepcionar
function cargaReferencia(){
    var id_referencia = document.getElementById("id_referencia").value;
    var metodo = document.getElementById('metodo').value;
    var id_almacen = document.getElementById('id_almacen').value;

    if(id_referencia != ""){
        if(id_referencia != 0){
            var ajax = objetoAJAX();
            ajax.open("GET","../ajax/almacen/almacen.php?comp=cargaReferencia&id_referencia=" + id_referencia + "&metodo=" + metodo + "&id_almacen=" + id_almacen,"true");
            ajax.onreadystatechange=function() {
                if (ajax.readyState==4 && ajax.status==200) {
                    document.getElementById("capa_ref_buscador").innerHTML=ajax.responseText;
                }
            }
            ajax.send(null);
        }
        else {
            alert("No existe la referencia en la base de datos");
        }
    }
}

// Función que carga la referencia al pulsar intro
function cargaReferenciaIntro(e){
    if (e.keyCode == 13) {
        // Carga La referencia
        cargaReferencia();
    }
}

// Función auxiliar para escapar ciertos caracteres
function escapeHtml(cadena) {
    return cadena
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

// Función para añadir la referencia a la tabla de buscador de referencia de recepción de material
function addReferencia(tableId,id_referencia,metodo){
	// Obtenemos los datos de la referencia obtenida del buscador
	var table = document.getElementById('tabla_buscador'); 
	var id_almacen = document.getElementById('id_almacen').value;

	table.rows[1].cells[0].setAttribute("style","text-align:center");
	table.rows[1].cells[5].setAttribute("style","text-align:center");
	table.rows[1].cells[6].setAttribute("style","text-align:center");
	table.rows[1].cells[7].setAttribute("style","text-align:center");
	table.rows[1].cells[8].setAttribute("style","text-align:center");
	table.rows[1].cells[9].setAttribute("style","text-align:center");

	// Mostramos los datos obtenidos del buscador
	table.rows[1].cells[0].innerHTML = id_referencia;
	table.rows[1].cells[1].innerHTML = nombre_referencia;
	table.rows[1].cells[2].innerHTML = nombre_proveedor;
	table.rows[1].cells[3].innerHTML = referencia_proveedor;
	table.rows[1].cells[4].innerHTML = nombre_pieza;
	table.rows[1].cells[5].innerHTML = pack_precio;
	table.rows[1].cells[6].innerHTML = unidades_paquete;
	table.rows[1].cells[7].innerHTML = '<input type="text" id="cantidad_referencia" style="width:50px; text-align:center;" onblur="extractNumber(this,2,true);" onkeyup="extractNumber(this,2,true);" onkeypress="return blockNonNumbers(this, event, true, true);" value="' + cantidad_referencia +  '" />';
	table.rows[1].cells[8].innerHTML = '';

	if(metodo == "RECEPCIONAR"){
		table.rows[1].cells[9].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="RECEPCIONAR" onclick="recepcionarReferencia('+ id_referencia +','+ id_almacen +')" />';
	}
	else {
		table.rows[1].cells[9].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="DESRECEPCIONAR" onclick="desrecepcionarReferencia('+ id_referencia +','+ id_almacen +')" />';	
	}

	// Guardamos los datos de la referencia
	document.getElementById("datos_referencia").innerHTML='<input type="hidden" id="id_referencia_hidden" value="'+id_referencia+'"/><input type="hidden" id="nombre_referencia_hidden" value="'+nombre_referencia+'" /><input type="hidden" id="nombre_proveedor_hidden" value="'+nombre_proveedor+'" /><input type="hidden" id="referencia_proveedor_hidden" value="'+escapeHtml(referencia_proveedor)+'" /><input type="hidden" id="nombre_pieza_hidden" value="'+nombre_pieza+'" /><input type="hidden" id="pack_precio_hidden" value="'+pack_precio+'" /><input type="hidden" id="unidades_paquete_hidden" value="'+unidades_paquete+'" /><input type="hidden" id="cantidad_referencia_hidden" value="" />';
}

// Función que comprueba si existe ya la referencia en la tabla LOG
function comprobarIdRef(id_referencia){
	var tabla = document.getElementById("tabla_log");
	var i = 0; 
	var error = false;
	var num_filas = tabla.rows.length; 

	while ((i<num_filas) && (!error)){
		var id_referencia_tabla = tabla.rows[i].cells.item(0).innerHTML;
		error = (id_referencia_tabla == id_referencia);
		i++;
	}
	return error;
}

// Función para recepcionar las referencias una por una
// Se guardan las piezas en el STOCK según su id_almacen
function recepcionarReferencia(id_referencia,id_almacen){
	// Comprobamos que no se haya añadido la referencia
	var error = comprobarIdRef(id_referencia);

	if(!error){
		var piezas = document.getElementById('cantidad_referencia').value;
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');
		var ids_op = new Array();
		var piezas_op = new Array();
		var op_recepcionadas_ref;
		var p_recepcionadas_ref;
		var id_albaran = document.getElementById('id_albaran_global').value;
        var id_usuario = document.getElementById('id_usuario_session').value;

		if(id_referencia != null){
            if(piezas != "."){
                if(piezas >= 0) {
                    if(piezas != 0){
                        // Realizamos la petición al servidor para realizar la recepción
                        var respuesta = (function () {
                            var respuesta = null;
                            $.ajax({
                                dataType: "json",
                                url: "../ajax/almacen/almacen.php?comp=recepcionar",
                                data: "id_referencia=" + id_referencia + "&piezas=" + piezas + "&id_almacen=" + id_almacen + "&id_usuario=" + id_usuario,
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

                        if(!hubo_error){
                            var pos = table.rows.length;
                            var row = table.insertRow(pos);
                            var fila = pos;

                            // Insertamos los datos del movimiento para poder deshacer la operación
                            var campos_ocultos = '<input type="hidden" id="id_albaran_hidden" value="' + id_albaran + '" />';
                            document.getElementById("datos_log").innerHTML = campos_ocultos;

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

                            cell_0.setAttribute("style","text-align:center");
                            cell_5.setAttribute("style","text-align:center");
                            cell_6.setAttribute("style","text-align:center");
                            cell_7.setAttribute("style","text-align:center");
                            cell_8.setAttribute("style","text-align:center");
                            cell_9.setAttribute("style","text-align:center");

                            // Insertamos la referencia
                            table.rows[fila].cells[0].innerHTML = id_referencia;
                            table.rows[fila].cells[1].innerHTML = respuesta.mov.nombre_referencia_hidden;
                            table.rows[fila].cells[2].innerHTML = respuesta.mov.nombre_proveedor_hidden;
                            table.rows[fila].cells[3].innerHTML = respuesta.mov.referencia_proveedor_hidden;
                            table.rows[fila].cells[4].innerHTML = respuesta.mov.nombre_pieza_hidden;
                            table.rows[fila].cells[5].innerHTML = respuesta.mov.pack_precio_hidden;
                            table.rows[fila].cells[6].innerHTML = respuesta.mov.unidades_paquete_hidden;
                            table.rows[fila].cells[7].innerHTML = piezas;
                            table.rows[fila].cells[8].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir(' + "'" + 'popup_log.php?id_albaran=' + id_albaran + '&id_referencia=' + id_referencia + '&id_almacen=' + id_almacen + "'" + ')" />';
                            table.rows[fila].cells[9].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerRecepcion(this,' + id_referencia + ','+id_almacen+')" />';

                            // Eliminamos la referencia del buscador
                            table_buscador.rows[1].cells[0].innerHTML = "";
                            table_buscador.rows[1].cells[1].innerHTML = "";
                            table_buscador.rows[1].cells[2].innerHTML = "";
                            table_buscador.rows[1].cells[3].innerHTML = "";
                            table_buscador.rows[1].cells[4].innerHTML = "";
                            table_buscador.rows[1].cells[5].innerHTML = "";
                            table_buscador.rows[1].cells[6].innerHTML = "";
                            table_buscador.rows[1].cells[7].innerHTML = "";
                            table_buscador.rows[1].cells[8].innerHTML = "";
                            table_buscador.rows[1].cells[9].innerHTML = "";

                            table_buscador.rows[1].cells[0].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[1].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[2].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[3].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[4].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[5].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[6].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[7].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[8].setAttribute("style","height: 35px;");
                            table_buscador.rows[1].cells[9].setAttribute("style","height: 35px;");
                        }
                        else {
                            alert(error_recepcion);
                        }
                    }
                    else {
                        alert("La cantidad no puede ser 0");
                    }
                }
                else {
                    alert("La cantidad no puede ser negativa");
                }
			}
			else {
                alert("Introduzca una cantidad correcta");
			}
		}
		else {
			alert("Introduzca una referencia");
		} 
	}
	else {
		alert("Ya se realizó una operación con esa referencia. Elimine la referencia y vuelva a realizar la operación de nuevo")
	}
}

// Función que deshace la recepción de una pieza de almacen
function deshacerRecepcion(r,id_referencia,id_almacen){
    var id_albaran = document.getElementById("id_albaran_hidden").value;

    // Realizamos la petición al servidor para deshacer la recepción
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen/almacen.php?comp=deshacerRecepcion",
            data: "id_referencia=" + id_referencia + "&id_albaran=" + id_albaran + "&id_almacen=" + id_almacen,
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

// Función para desrecepcionar las referencias una por una
// Se desrecepcionan las piezas en la OP iniciadas de esa referencia
function desrecepcionarReferencia(id_referencia,id_almacen){
    // Comprobamos que no se haya añadido la referencia
	var error = comprobarIdRef(id_referencia);

	if(!error){
		var piezas = document.getElementById('cantidad_referencia').value;
		var table = document.getElementById('tabla_log');
		var table_buscador = document.getElementById('tabla_buscador');
		var ids_op = new Array();
		var piezas_op = new Array();
		var op_recepcionadas_ref;
		var p_recepcionadas_ref;
		var id_albaran = document.getElementById('id_albaran_global_des').value;
        var id_usuario = document.getElementById('id_usuario_session').value;

		if(id_referencia != null){
            if(piezas != ".") {
                if(piezas >= 0) {
                    if(piezas != 0) {
                        // Realizamos la petición al servidor para realizar la desrecepcion
                        var respuesta = (function () {
                            var respuesta = null;
                            $.ajax({
                                dataType: "json",
                                url: "../ajax/almacen/almacen.php?comp=desrecepcionar",
                                data: "id_referencia=" + id_referencia + "&piezas=" + piezas + "&id_almacen=" + id_almacen + "&id_usuario=" + id_usuario,
                                type: "GET",
                                async: false,
                                success: function (data) {
                                    respuesta = data;
                                }
                            });
                            return respuesta;
                        })();

                        var hubo_error = respuesta.mov.error;
                        var error_desrecepcion = respuesta.mov.error_des;

                        if(!hubo_error){
                            var pos = table.rows.length;
                            var row = table.insertRow(pos);
                            var fila = pos;

                            // Insertamos los datos del movimiento para poder deshacer la operación
                            var campos_ocultos = '<input type="hidden" id="id_albaran_hidden" value="' + id_albaran + '" />';
                            document.getElementById("datos_log").innerHTML = campos_ocultos;

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

                            cell_0.setAttribute("style", "text-align:center");
                            cell_5.setAttribute("style", "text-align:center");
                            cell_6.setAttribute("style", "text-align:center");
                            cell_7.setAttribute("style", "text-align:center");
                            cell_8.setAttribute("style", "text-align:center");
                            cell_9.setAttribute("style", "text-align:center");

                            // Insertamos la referencia
                            table.rows[fila].cells[0].innerHTML = id_referencia;
                            table.rows[fila].cells[1].innerHTML = respuesta.mov.nombre_referencia_hidden;
                            table.rows[fila].cells[2].innerHTML = respuesta.mov.nombre_proveedor_hidden;
                            table.rows[fila].cells[3].innerHTML = respuesta.mov.referencia_proveedor_hidden;
                            table.rows[fila].cells[4].innerHTML = respuesta.mov.nombre_pieza_hidden;
                            table.rows[fila].cells[5].innerHTML = respuesta.mov.pack_precio_hidden;
                            table.rows[fila].cells[6].innerHTML = respuesta.mov.unidades_paquete_hidden;
                            table.rows[fila].cells[7].innerHTML = piezas;
                            table.rows[fila].cells[8].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="+" onclick="javascript:abrir(' + "'" + 'popup_log.php?id_albaran=' + id_albaran + '&id_referencia=' + id_referencia + '&id_almacen=' + id_almacen + "'" + ')" />';
                            table.rows[fila].cells[9].innerHTML = '<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="ELIMINAR" onclick="deshacerDesRecepcion(this,' + id_referencia + ',' + id_almacen + ')" />';

                            // Eliminamos la referencia del buscador
                            table_buscador.rows[1].cells[0].innerHTML = "";
                            table_buscador.rows[1].cells[1].innerHTML = "";
                            table_buscador.rows[1].cells[2].innerHTML = "";
                            table_buscador.rows[1].cells[3].innerHTML = "";
                            table_buscador.rows[1].cells[4].innerHTML = "";
                            table_buscador.rows[1].cells[5].innerHTML = "";
                            table_buscador.rows[1].cells[6].innerHTML = "";
                            table_buscador.rows[1].cells[7].innerHTML = "";
                            table_buscador.rows[1].cells[8].innerHTML = "";
                            table_buscador.rows[1].cells[9].innerHTML = "";

                            table_buscador.rows[1].cells[0].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[1].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[2].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[3].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[4].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[5].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[6].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[7].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[8].setAttribute("style", "height: 35px;");
                            table_buscador.rows[1].cells[9].setAttribute("style", "height: 35px;");
                        }
                        else {
                            alert(error_desrecepcion);
                        }
                    }
                    else {
                        alert("La cantidad no puede ser 0");
                    }
                }
                else {
                    alert("La cantidad no puede ser negativa");
                }
            }
            else {
                alert("Introduzca una cantidad correcta");
            }
		}
		else {
			alert("Introduzca una referencia");				
		} 
	}
	else {
		alert("Ya se realizó una operación con esa referencia. Elimine la referencia y vuelva a realizar la operación de nuevo")
	}
}

// Función que deshace la desrecepción
function deshacerDesRecepcion(r,id_referencia,id_almacen){
    var id_albaran = document.getElementById("id_albaran_hidden").value;

    // Realizamos la petición al servidor para deshacer la desrecepción
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen/almacen.php?comp=deshacerDesRecepcion",
            data: "id_referencia=" + id_referencia + "&id_albaran=" + id_albaran + "&id_almacen=" + id_almacen,
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

// Función AJAX que realiza el proceso para la recepción o desrecepción de piezas
function recepcionarDesrecepcionar(id_referencia,id_produccion,id_almacen){
	// Obtenemos las unidades que inserto el usuario
	var unidades_pendientes = 0;
	var unidades_entrada = document.getElementById("und_entrada-" + id_referencia + "-" + id_produccion + "-" + id_almacen).value;
	var mensaje = "";
    var id_usuario = document.getElementById("id_usuario_hidden").value;

	if(unidades_entrada != 0){
		mensaje = "¿Esta seguro de realizar la operación?";
		if(confirm(mensaje)){
            // Realizamos la petición al servidor para hacer el ajuste
            var respuesta = (function () {
                var respuesta = null;
                $.ajax({
                    dataType: "json",
                    url: "../ajax/almacen/almacen.php?comp=recepcionarDesrecepcionar",
                    data: "id_referencia=" + id_referencia + "&id_produccion=" + id_produccion + "&unidades_entrada=" + unidades_entrada + "&unidades_pendientes=" + unidades_pendientes + "&id_almacen=" + id_almacen + "&id_usuario=" + id_usuario,
                    type: "GET",
                    async: false,
                    success: function (data) {
                        respuesta = data;
                    }
                });
                return respuesta;
            })();

            var hubo_error = respuesta.mov.error == true;
            var error_ajuste = respuesta.mov.error_des;

            if(!hubo_error){
                // Mensaje de confirmación
                TINY.box.show({html:'Operación realizada con exito',width:250,animate:false,close:true,mask:false,boxid:'success',autohide:2});
                setTimeout(function(){
                    window.location.href=window.location.href;
                }, 1000);
            }
            else {
                alert(error_ajuste);
            }
		}
	}
	else {
		alert("El campo unidades no puede ser 0");	
	}
}

// Función que carga los tipos de motivos de los albaranes
function cargaMotivos(id_almacen,tipo_albaran){
    // Realizamos la petición al servidor para obtener los motivos del albaran
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen/almacen.php?comp=cargaMotivos",
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

// Función que carga los tipos de motivos de los albaranes para el buscador
function cargaMotivosBuscador(id_sede){
    // Realizamos la petición al servidor para obtener los motivos del albaran
    var respuesta = (function () {
        var respuesta = null;
        $.ajax({
            dataType: "json",
            url: "../ajax/almacen/almacen.php?comp=cargaMotivosBuscador",
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

// Función que cambia los campos del buscador de albaranes según la sede
function cambiaCamposBuscadorAlbaran(id_sede){
    cargaAlmacenes(id_sede);
    cargaMotivosBuscador(id_sede);
}

// Función que descarga el informe del albarán
function cerrarAlbaran(id_albaran){
	if(confirm("¿Desea finalizar la operación?")){
        // Desactivamos la validación para que pueda redirigir la página
        window.onbeforeunload = null;

		// Comprobamos si el albaran esta vacio
		var tabla = document.getElementById("tabla_log");
		var num_filas = tabla.rows.length-1;

		if(num_filas == 0){
			// Albaran vacio
			window.location="../almacen/albaranes.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=1";	
		}
		else{
			window.location="../almacen/albaranes.php?cerrarAlbaran=1&id_albaran=" + id_albaran + "&vacio=0";	
		}
	}
}

// Descarga el excel de referencias de listado material
function descargar_XLS(metodo){
    window.location="informe_referencias.php?metodo=" + metodo;
}

// Descarga el excel de movimientos de albaranes
function descargar_XLS_Movimientos(){
	window.location="informe_movimientos.php";
}

// Función para resetear los campos de los formularios de Almacen
function resetearCamposFormulario(){
    var id_ref = document.getElementById("id_ref");
    var busq_mag = document.getElementById("busqueda_magica");
    var nombre_albaran = document.getElementById("nombre_albaran");
    var fecha = document.getElementById("datepicker_albaranes_desde");
    var fecha_mov_desde = document.getElementById("datepicker_movimientos_desde");
    var fecha_mov_hasta = document.getElementById("datepicker_movimientos_hasta");

    if(id_ref != null) id_ref.value = "";
    if(busq_mag != null) busq_mag.value = "";
    if(nombre_albaran != null) nombre_albaran.value = "";
    if(fecha != null) fecha.value = "";
    if(fecha_mov_desde != null) fecha_mov_desde.value = "";
    if(fecha_mov_hasta != null) fecha_mov_hasta.value = "";

    var opciones_prov = document.getElementsByName("proveedor");
    if(opciones_prov != null) {
        for(i=0;i<opciones_prov.length;i++) {
            opciones_prov[i].selectedIndex = 0;
        }
    }

    var opciones_part = document.getElementsByName("nombre_participante");
    if(opciones_part != null) {
        for(i=0;i<opciones_part.length;i++) {
            opciones_part[i].selectedIndex = 0;
        }
    }

    var opciones_user = document.getElementsByName("id_usuario");
    if(opciones_user != null) {
        for(i=0;i<opciones_user.length;i++) {
            opciones_user[i].selectedIndex = 0;
        }
    }

    var opciones_tipo = document.getElementsByName("tipo_albaran");
    if(opciones_tipo != null) {
        for(i=0;i<opciones_tipo.length;i++) {
            opciones_tipo[i].selectedIndex = 0;
        }
    }

    var opciones_motivo = document.getElementsByName("tipo_motivo");
    if(opciones_motivo != null) {
        for(i=0;i<opciones_motivo.length;i++) {
            opciones_motivo[i].selectedIndex = 0;
        }
    }
}



