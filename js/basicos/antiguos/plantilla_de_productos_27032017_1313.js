// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el listado de plantillas de productos

// Función que redirige la página para generar la documentación
function descargar_documentacion(id_plantilla){
   window.location="descargar_documentacion.php?op=PLANT" + "&id=" + id_plantilla;
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

