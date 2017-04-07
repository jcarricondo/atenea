// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el listado de perifericos

// Función que redirige la página para generar la documentación
function descargar_documentacion(id_componente){
   window.location="descargar_documentacion.php?op=PER" + "&id=" + id_componente;
}