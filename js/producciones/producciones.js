// JavaScript Document
// Fichero que contiene las funciones JavaScript utilizadas en el modulo de Producciones

// Funcion que cargas las Ordenes de Produccion del escandallo en funcion de la sede
function cargaSedesEscandallo(id_sede){
	window.location="escandallo_por_componentes.php?id_sede=" + id_sede;
}


// Funcion que carga el escandallo correspondiente a la Orden de Produccion seleccionada
function cargaEscandalloPorComponentes(id_produccion){
	window.location="escandallo_por_componentes.php?id_produccion=" + id_produccion + "&cambio_op=1";
}

// Funcion que obtiene los ids de los componentes de los checkbox seleccionados y llama al fichero
// que generara el excel con los componentes del escandallo
function validarDescargaEscandalloPorComponentes() {
	var id_produccion = document.getElementById('id_op').value;
	var unidades_op = document.getElementById('unidades_op').value;
	var num_tecnicos = document.getElementById('num_tecnicos').value;
	var checkbox = document.getElementsByName('chkbox[]');
	var unidades = document.getElementsByName('unidades_componente[]');

	var ids_componentes = new Array();
	var unidades_componentes = new Array();
	var error_tecnicos = false; 
	var mas_unidades = false;
	var continuar = true;
	var unidades_fabrican;

	if(num_tecnicos == null || num_tecnicos == 0){
		error_tecnicos = true;
	}
	else{	
		var i=0;
		var j=0;
		var error = false;
		var error_unidades_seleccion = false;
		var error_seleccion = true;
		while((i<checkbox.length) && (!error)){
			if(checkbox.item(i).checked == true){
				// Si el campo unidades esta vacio
				if(unidades.item(i).value.length == 0){
					error = true;
				}
				else{
					ids_componentes[j] = checkbox.item(i).value;
					unidades_componentes[j] = unidades.item(i).value;	
					unidades_fabrican = unidades.item(i).value;	

					// Avisar al usuario cuando las unidades a fabricar de un componente supera a las de las previstas en la OP
					// Convertimos las unidades en integer para poder compararlas
					unidades_op = parseInt(unidades_op);
					unidades_fabrican = parseInt(unidades_fabrican);

					if(unidades_fabrican > unidades_op){
						mas_unidades = true;
					}
					j++;	
					
				}
				error_seleccion = false;
			}
			else {
				// Si un componente no esta seleccionado y tiene unidades
				if(unidades.item(i).value.length != 0){
					error_unidades_seleccion = true;
				}
			}
			i++;	
		}
	}
	// Numero de tecnicos 0 o vacio
	if(error_tecnicos){
		alert("Introduzca un numero de tecnicos valido");
	}
	// No se selecciono ningun componente
	else if (error_seleccion){
		alert("Seleccione algun componente");
	}
	// Campo unidades de un componente seleccionado esta vacio
	else if (error) {
		alert("Compruebe que ha rellenado todas las unidades de los componentes seleccionados");
	}
	else if (error_unidades_seleccion){
		alert("Compruebe que ha seleccionado aquellos componentes que tienen unidades introducidas");
	}
	// OK
	else {
		if(mas_unidades){
			if (!confirm("Ha introducido más componentes a fabricar de los previstos en la Orden de Producción.\n\nTenga en cuenta que si continua con el proceso, se reflejará en el escandallo, que el número de piezas utilizadas será mayor que el número de piezas recibidas.\n\n¿Desea continuar con la operación?")){
				continuar = false;
			}				
		}

		if(continuar){
			codigo = randomString();
			if (confirm("Se va a generar un informe y se descontarán las piezas correspondientes a los componentes seleccionados.\n\nCompruebe el número de unidades seleccionadas para cada componente.\n\nSu código de operación es " + codigo + "\n\nApunte este código para recuperar su informe en el futuro\n\n¿Está seguro de continuar con la operación?")){
				window.location="escandallo_por_componentes.php?escandallo=descargar&id_produccion="+ id_produccion + "&ids_componentes=" + ids_componentes + "&unidades_componentes=" + unidades_componentes + "&codigo=" + codigo + "&num_tecnicos=" + num_tecnicos;
			}
			else{
				// No se realiza la operacion
			}
		}		
	}
}





/*
// Funcion que comprueba si el numero de tecnicos es correcto y llama al fichero que generara el excel con con los boxes del escandallo
function validarDescargaEscandallo() {
	var checkbox = document.getElementsByName('chkbox[]');
	var unidades = document.getElementsByName('unidades_componente[]');
	var unidades_op = document.getElementById('unidades_op').value;
	var id_produccion = document.getElementById('id_op').value;
	var num_tecnicos = document.getElementById('num_tecnicos').value;

	var ids_componentes = new Array();
	var unidades_componentes = new Array();
	var error_tecnicos = false; 

	if(num_tecnicos == null || num_tecnicos == 0){
		error_tecnicos = true;
	}
	// Numero de tecnicos 0 o vacio
	if(error_tecnicos){
		alert("Introduzca un numero de tecnicos valido");
	}
	// OK
	else {
		codigo = randomString();
		if (confirm("Se va a generar un informe y se descontarán las piezas correspondientes a los componentes seleccionados.\n\nCompruebe el número de unidades seleccionadas para cada componente.\n\nSu código de operación es " + codigo + "\n\nApunte este código para recuperar su informe en el futuro\n\n¿Está seguro de continuar con la operación?")){
			window.location="escandallo.php?escandallo=descargar&id_produccion="+ id_produccion + "&ids_componentes=" + ids_componentes + "&unidades_componentes=" + unidades_componentes + "&unidades_op=" + unidades_op + "&codigo=" + codigo + "&num_tecnicos=" + num_tecnicos;
		}
		else{
			// No se realiza la operacion
		}	
	}
}
*/



// Funcion que rellena los demas input text del listado con el numero introducido en el input generico
function rellenaUnidades(){
	var todas_unidades = document.getElementById('todas_unidades').value;
	var unidades = document.getElementsByName('unidades_componente[]');

	if ((todas_unidades.length != 0) && (todas_unidades != 0)){
		if (todas_unidades.value != 0){
			for(i=0; i<unidades.length; i++){
				unidades.item(i).value = todas_unidades;
			}
			var todosCheckbox = document.getElementById('todos_Checkbox');
			todosCheckbox.checked = true;
			seleccionarTodosCheckbox();
		}
	}	
	else {
		for(i=0; i<unidades.length; i++){
			unidades.item(i).value = null;
		}
		var todosCheckbox = document.getElementById('todos_Checkbox');
		todosCheckbox.checked = false;
		desSeleccionarTodosCheckbox();
	}
}

/*
// Funcion que carga el escandallo correspondiente a la Orden de Produccion seleccionada
function cargaEscandallo(id_produccion){
	window.location="escandallo.php?id_produccion=" + id_produccion;
}
*/



// Funcion para generar codigo aleatorio
function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZ";
	var string_length = 10;
	var randomstring = '';
	for(var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
}
