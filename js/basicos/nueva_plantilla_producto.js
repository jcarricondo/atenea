/**
 * Created by Jacho on 30/06/2015.
 */

// Funcion que muestra todas las cabinas creadas
function MostrarTodasCabinas(){
    // Obtenemos el boton de todas las cabinas y lo eliminamos
    var capaBotones = document.getElementById("CapaBotonesCabOP");
    var botonTodasCabinas = document.getElementById("BotonTodasCabinas");
    botonTodasCabinas.parentNode.removeChild(botonTodasCabinas);
    // Creamos el nuevo boton de cabinas de produccion
    var boton_cabinas = document.createElement("input");
    boton_cabinas.type = "button";
    boton_cabinas.id = "BotonCabProduccion";
    boton_cabinas.name = "BotonCabProduccion";
    boton_cabinas.className = "BotonEliminar";
    boton_cabinas.value = "Mostrar cabinas en producción";
    boton_cabinas.setAttribute('onclick', 'MostrarCabProduccion()');
    capaBotones.appendChild(boton_cabinas);
    // Creamos el nuevo select con todas las cabinas
    var selectCabinas = document.getElementById("cabina");
    var lista_cabinas = document.getElementById("lista_cabinas");
    selectCabinas.parentNode.removeChild(selectCabinas);
    var selectCabinas = document.createElement("select");
    selectCabinas.id = "cabina";
    selectCabinas.name = "cabina";
    selectCabinas.className = "CreacionBasicoInput";
    // Obtenemos los input con los datos de todas las cabinas y los metemos en el select
    var id_todas_cabinas = document.getElementsByName("id_todas_cabinas[]");
    var nombre_todas_cabinas = document.getElementsByName("nombre_todas_cabinas[]");
    // Insertamos el select y vamos añadiendo cada una de las cabinas
    lista_cabinas.appendChild(selectCabinas);
    for(i=-1;i<id_todas_cabinas.length;i++){
        var opcion_cabina = document.createElement("option");
        if(i==-1) {
            opcion_cabina.value = -1;
            opcion_cabina.text = "Selecciona..";
        }
        else {
            opcion_cabina.value = id_todas_cabinas[i].value;
            opcion_cabina.text = nombre_todas_cabinas[i].value;
        }
        selectCabinas.add(opcion_cabina,null);
    }
}

// Funcion que muestra solo las cabinas en estado PRODUCCION
function MostrarCabProduccion(){
    // Obtenemos el boton de cabinas en produccion y lo eliminamos
    var capaBotones = document.getElementById("CapaBotonesCabOP");
    var botonCabProduccion = document.getElementById("BotonCabProduccion");
    botonCabProduccion.parentNode.removeChild(botonCabProduccion);
    // Creamos el nuevo boton de todas las cabinas
    var boton_cabinas = document.createElement("input");
    boton_cabinas.type = "button";
    boton_cabinas.id = "BotonTodasCabinas";
    boton_cabinas.name = "BotonTodasCabinas";
    boton_cabinas.className = "BotonEliminar";
    boton_cabinas.value = "Mostrar todas las cabinas";
    boton_cabinas.setAttribute('onclick', 'MostrarTodasCabinas()');
    capaBotones.appendChild(boton_cabinas);
    // Creamos el nuevo select con las cabinas en produccion
    var selectCabinas = document.getElementById("cabina");
    var lista_cabinas = document.getElementById("lista_cabinas");
    selectCabinas.parentNode.removeChild(selectCabinas);
    var selectCabinas = document.createElement("select");
    selectCabinas.id = "cabina";
    selectCabinas.name = "cabina";
    selectCabinas.className = "CreacionBasicoInput";
    // Obtenemos los input con los datos de las cabinas en produccion y los metemos en el select
    var id_cab_produccion = document.getElementsByName("id_cab_produccion[]");
    var nombre_cab_produccion = document.getElementsByName("nombre_cab_produccion[]");
    // Insertamos el select y vamos añadiendo cada una de las cabinas
    lista_cabinas.appendChild(selectCabinas);
    for(i=-1;i<id_cab_produccion.length;i++){
        var opcion_cabina = document.createElement("option");
        if(i==-1) {
            opcion_cabina.value = -1;
            opcion_cabina.text = "Selecciona..";
        }
        else {
            opcion_cabina.value = id_cab_produccion[i].value;
            opcion_cabina.text = nombre_cab_produccion[i].value;
        }
        selectCabinas.add(opcion_cabina,null);
    }
}

// Funcion que muestra todos los perifericos creados
function MostrarTodosPerifericos(){
    // Obtenemos el boton de todos los perifericos y lo eliminamos
    var capaBotones = document.getElementById("CapaBotonesPerOP");
    var botonTodosPerifericos = document.getElementById("BotonTodosPerifericos");
    botonTodosPerifericos.parentNode.removeChild(botonTodosPerifericos);
    // Creamos el nuevo boton de perifericos de produccion
    var boton_perifericos = document.createElement("input");
    boton_perifericos.type = "button";
    boton_perifericos.id = "BotonPerProduccion";
    boton_perifericos.name = "BotonPerProduccion";
    boton_perifericos.className = "BotonEliminar";
    boton_perifericos.value = "Mostrar periféricos en producción";
    boton_perifericos.setAttribute('onclick', 'MostrarPerProduccion()');
    capaBotones.appendChild(boton_perifericos);
    // Creamos el nuevo select con todos los perifericos
    var selectPerifericos = document.getElementById("perifericos_no_asignados[]");
    var lista_no_asignados = document.getElementById("listas_no_asignados");
    selectPerifericos.parentNode.removeChild(selectPerifericos);
    var selectPerifericos = document.createElement("select");
    selectPerifericos.multiple = "multiple";
    selectPerifericos.id = "perifericos_no_asignados[]";
    selectPerifericos.name = "perifericos_no_asignados[]";
    selectPerifericos.className = "SelectMultiplePerOrigen";
    // Obtenemos los input con los datos de todos los componentes y los metemos en el select
    var id_todos_perifericos = document.getElementsByName("id_todos_perifericos[]");
    var nombre_todos_perifericos = document.getElementsByName("nombre_todos_perifericos[]");
    // Insertamos el select y vamos añadiendo cada uno de los perifericos
    lista_no_asignados.appendChild(selectPerifericos);
    for(i=0;i<id_todos_perifericos.length;i++){
        var opcion_periferico = document.createElement("option");
        opcion_periferico.value = id_todos_perifericos[i].value;
        opcion_periferico.text = nombre_todos_perifericos[i].value;
        selectPerifericos.add(opcion_periferico,null);
    }
}

// Funcion que muestra solo los perifericos en estado PRODUCCION
function MostrarPerProduccion(){
    // Obtenemos el boton de perifericos de produccion y lo eliminamos
    var capaBotones = document.getElementById("CapaBotonesPerOP");
    var botonPerProduccion = document.getElementById("BotonPerProduccion");
    botonPerProduccion.parentNode.removeChild(botonPerProduccion);
    // Creamos el nuevo boton de todos los perifericos
    var boton_perifericos = document.createElement("input");
    boton_perifericos.type = "button";
    boton_perifericos.id = "BotonTodosPerifericos";
    boton_perifericos.name = "BotonTodosPerifericos";
    boton_perifericos.className = "BotonEliminar";
    boton_perifericos.value = "Mostrar todos los periféricos";
    boton_perifericos.setAttribute('onclick', 'MostrarTodosPerifericos()');
    capaBotones.appendChild(boton_perifericos);
    // Creamos el nuevo select los perifericos en produccion
    selectPerifericos = document.getElementById("perifericos_no_asignados[]");
    lista_no_asignados = document.getElementById("listas_no_asignados");
    selectPerifericos.parentNode.removeChild(selectPerifericos);
    selectPerifericos = document.createElement("select");
    selectPerifericos.multiple = "multiple";
    selectPerifericos.id = "perifericos_no_asignados[]";
    selectPerifericos.name = "perifericos_no_asignados[]";
    selectPerifericos.className = "SelectMultiplePerOrigen";
    // Obtenemos los input con los datos de todos los componentes de produccion y los metemos en el select
    var id_per_produccion = document.getElementsByName("id_per_produccion[]");
    var nombre_per_produccion = document.getElementsByName("nombre_per_produccion[]");
    // Insertamos el select y vamos añadiendo cada uno de los perifericos
    lista_no_asignados.appendChild(selectPerifericos);
    for(i=0;i<id_per_produccion.length;i++){
        var opcion_periferico = document.createElement("option");
        opcion_periferico.value = id_per_produccion[i].value;
        opcion_periferico.text = nombre_per_produccion[i].value;
        selectPerifericos.add(opcion_periferico,null);
    }
}

// Añadir elemento a la segunda lista
function AddToSecondList(){
    var fl = document.getElementById('perifericos_no_asignados[]');
    var sl = document.getElementById('perifericos[]');

    for(i=0;i < fl.options.length;i++){
        if(fl.options[i].selected){
            // Añadimos la opcion a la lista 1
            var option = document.createElement("option");
            option.value = fl[i].value;
            option.text = fl[i].text;
            fl.add(option,i);
            sl.add(fl.options[i],null);
        }
    }
    return true;
}

// Eliminar elemento de la lista
function DeleteSecondListItem(){
    var sl = document.getElementById('perifericos[]');

    for (i=0; i<sl.options.length;i++){
        if(sl.options[i].selected){
            sl.remove(sl.selectedIndex);
            i--;
        }
    }
    return true;
}

// Seleccionar perifericos para POST
function SeleccionarPerifericos(){
    var lista = document.getElementById("perifericos[]");
    for(i = 0; i<lista.options.length; i++){
        lista[i].selected = "selected";
    }
}

// Comprueba el formulario antes de pasar al siguiente punto
function validarFormulario() {
    var plantilla_producto = document.getElementById('nombre').value;
    var version_producto = document.getElementById('version').value;

    if(plantilla_producto == ''){
        alert("Rellene el campo: Plantilla de Producto");
        return false;
    }
    else if(version_producto == ''){
        alert("Rellene el campo: Versión");
        return false;
    }
    else {
        SeleccionarPerifericos();
        return true;
    }
}
