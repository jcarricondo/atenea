/**
 * Created by Jacho on 30/06/2015.
 */

// PERIFERICOS

// Función que muestra todos los periféricos creados
function MostrarTodosPerifericos(){
    // Obtenemos el botón de todos los periféricos y lo eliminamos
    var capaBotones = document.getElementById("CapaBotonPerifericos");
    var botonTodosPerifericos = document.getElementById("BotonTodosPerifericos");
    botonTodosPerifericos.parentNode.removeChild(botonTodosPerifericos);
    // Creamos el nuevo botón de periféricos de producción
    var boton_perifericos = document.createElement("input");
    boton_perifericos.type = "button";
    boton_perifericos.id = "BotonPerProduccion";
    boton_perifericos.name = "BotonPerProduccion";
    boton_perifericos.className = "BotonTodosComponentes";
    boton_perifericos.value = "Mostrar periféricos en producción";
    boton_perifericos.setAttribute('onclick', 'MostrarPerProduccion()');
    capaBotones.appendChild(boton_perifericos);
    // Obtenemos el buscador para que busque en todos los periféricos
    var input_buscador = document.getElementById("BuscadorPerNewPlantilla");
    input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('todos','BuscadorPerNewPlantilla','perifericos_no_asignados[]')");

    var selectPerifericos = document.getElementById("perifericos_no_asignados[]");
    for(i=0;i<selectPerifericos.length;i++) {
        var option = selectPerifericos.options[i];
        option.style.display = "block";
        if(option.id == "") option.selected = false;
    }
    BuscadorDinamicoComponentes('todos','BuscadorPerNewPlantilla','perifericos_no_asignados[]');
}

// Función que muestra sólo los periféricos en estado PRODUCCION
function MostrarPerProduccion() {
    // Obtenemos el botón de periféricos de producción y lo eliminamos
    var capaBotones = document.getElementById("CapaBotonPerifericos");
    var botonPerProduccion = document.getElementById("BotonPerProduccion");
    botonPerProduccion.parentNode.removeChild(botonPerProduccion);
    // Creamos el nuevo botón de todos los periféricos
    var boton_perifericos = document.createElement("input");
    boton_perifericos.type = "button";
    boton_perifericos.id = "BotonTodosPerifericos";
    boton_perifericos.name = "BotonTodosPerifericos";
    boton_perifericos.className = "BotonTodosComponentes";
    boton_perifericos.value = "Mostrar todos los periféricos";
    boton_perifericos.setAttribute('onclick', 'MostrarTodosPerifericos()');
    capaBotones.appendChild(boton_perifericos);
    // Obtenemos el buscador para que busque solo los periféricos de producción
    var input_buscador = document.getElementById("BuscadorPerNewPlantilla");
    input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('produccion','BuscadorPerNewPlantilla','perifericos_no_asignados[]')");

    selectPerifericos = document.getElementById("perifericos_no_asignados[]");
    for (i = 0; i < selectPerifericos.length; i++) {
        var option = selectPerifericos.options[i];
        if (option.id == "") option.style.display = "none";
    }
    BuscadorDinamicoComponentes('produccion','BuscadorPerNewPlantilla','perifericos_no_asignados[]');
}

// Añadir elemento a la segunda lista
function AddToSecondList(){
    var fl = document.getElementById('perifericos_no_asignados[]');
    var sl = document.getElementById('perifericos[]');

    for(i=0;i < fl.options.length;i++){
        if(fl.options[i].selected){
            if(fl.options[i].style.display === "block"){
                // Añadimos la opcion a la lista 1
                var option = document.createElement("option");
                option.value = fl[i].value;
                option.text = fl[i].text;
                fl.add(option,i);
                sl.add(fl.options[i],null);
            }
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


// KITS

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
    var input_buscador = document.getElementById("BuscadorKitNewPlantilla");
    input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('todos','BuscadorKitNewPlantilla','kits_no_asignados[]')");

    var selectKits = document.getElementById("kits_no_asignados[]");
    for(i=0;i<selectKits.length;i++) {
        var option = selectKits.options[i];
        option.style.display = "block";
        if(option.id == "") option.selected = false;
    }
    BuscadorDinamicoComponentes('todos','BuscadorKitNewPlantilla','kits_no_asignados[]');
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
    var input_buscador = document.getElementById("BuscadorKitNewPlantilla");
    input_buscador.setAttribute("onkeyup","BuscadorDinamicoComponentes('produccion','BuscadorKitNewPlantilla','kits_no_asignados[]')");

    selectKits = document.getElementById("kits_no_asignados[]");
    for (i = 0; i < selectKits.length; i++) {
        var option = selectKits.options[i];
        if (option.id == "") option.style.display = "none";
    }
    BuscadorDinamicoComponentes('produccion','BuscadorKitNewPlantilla','kits_no_asignados[]');
}

// Añadir elemento a la segunda lista
function AddKitsToSecondList(){
    var fl = document.getElementById('kits_no_asignados[]');
    var sl = document.getElementById('kits[]');

    for(i=0;i < fl.options.length;i++){
        if(fl.options[i].selected){
            if(fl.options[i].style.display === "block"){
                // Añadimos la opcion a la lista 1
                var option = document.createElement("option");
                option.value = fl[i].value;
                option.text = fl[i].text;
                fl.add(option,i);
                sl.add(fl.options[i],null);
            }
        }
    }
    return true;
}

// Eliminar elemento de la lista
function DeleteKitsSecondListItem(){
    var sl = document.getElementById('kits[]');

    for (i=0; i<sl.options.length;i++){
        if(sl.options[i].selected){
            sl.remove(sl.selectedIndex);
            i--;
        }
    }
    return true;
}

// Seleccionar kits para POST
function SeleccionarKits(){
    var lista = document.getElementById("kits[]");
    for(i = 0; i<lista.options.length; i++){
        lista[i].selected = "selected";
    }
}

// VALIDACIONES

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
        SeleccionarKits();
        return true;
    }
}


