// JavaScript Document
// Archivo que contiene todas las funciones de javascript del proceso de creación de nuevo usuario

function obtenerTipoUsuario(tipo_usuario,esAdminGlobalSesion){
	var almacen = false;
    var tipo_almacen = 0;
	var usuario_fabrica = false;
    var usuario_mantenimiento = false;

	switch (tipo_usuario) {
		case '1': 
			// ADMINISTRADOR SIMUMAK
			almacen = 0;
		break;
		case '2': 
			// ADMINISTRADOR DISEÑO 
			almacen = 0;
		break;
		case '3': 
			// ADMINISTRADOR FABRICA
			almacen = 1;
			usuario_fabrica = true;
            tipo_almacen = 1;
		break;
		case '4': 
			// ADMINISTRADOR MANTENIMIENTO
			almacen = 1;
            usuario_mantenimiento = true;
            tipo_almacen = 2;
		break;
		case '5': 
			// USUARIO DISEÑO 
			almacen = 0;
		break;
		case '6': 
			// USUARIO FABRICA
			almacen = 1;
			usuario_fabrica = true;
            tipo_almacen = 1;
		break;
		case '7': 
			// USUARIO MANTENIMIENTO
			almacen = 1;
            usuario_mantenimiento = true;
            tipo_almacen = 2;
		break;
		case '8': 
			// ADMINISTRADOR GESTION 
			almacen = 0;
		break;
		case '2': 
			// USUARIO GESTION 
			almacen = 0;
		break;
	}

	// Cambiamos el select en el caso de que se haya seleccionado otra opción
	if((!usuario_fabrica && !usuario_mantenimiento) || esAdminGlobalSesion == 1){
		// Obtenemos el select con los almacenes en función del tipo de usuario
		var ajax = objetoAJAX();
		ajax.open("GET","../ajax/basicos/usuarios.php?func=obtenerAlmacen&almacen=" + almacen + "&tipo_almacen=" + tipo_almacen,"true");
		ajax.onreadystatechange=function() {
			if(ajax.readyState==4 && ajax.status==200) {
				document.getElementById("capaAlmacen").innerHTML=ajax.responseText;
			}
		}
		ajax.send(null);
	}
}

// Función para obtener el select de almacenes del buscador de usuarios mediante llamada AJAX
function cargaAlmacen(id_tipo_usuario){
	var almacen = false;
	var tipo_almacen = 0;

	switch (id_tipo_usuario) {
		case '1': 
			// ADMINISTRADOR SIMUMAK
			almacen = 0;
		break;
		case '2': 
			// ADMINISTRADOR DISEÑO 
			almacen = 0;
		break;
		case '3': 
			// ADMINISTRADOR FABRICA
			almacen = 1;
			tipo_almacen = 1;
		break;
		case '4': 
			// ADMINISTRADOR MANTENIMIENTO
			almacen = 1;
			tipo_almacen = 2;
		break;
		case '5': 
			// USUARIO DISEÑO 
			almacen = 0;
		break;
		case '6': 
			// USUARIO FABRICA
			almacen = 1;
			tipo_almacen = 1;
		break;
		case '7': 
			// USUARIO MANTENIMIENTO
			almacen = 1;
			tipo_almacen = 2;
		break;
		case '8': 
			// ADMINISTRADOR GESTION 
			almacen = 0;
		break;
		case '9': 
			// USUARIO GESTION
			almacen = 0;
		break;
		default: 
			almacen = 0;
	}

	// Obtenemos el select con los almacenes en función del tipo de usuario
	var ajax = objetoAJAX();
	ajax.open("GET","../ajax/basicos/usuarios.php?func=cargaAlmacen&almacen=" + almacen + "&tipo_almacen=" + tipo_almacen,"true");
	ajax.onreadystatechange=function() {
		if(ajax.readyState==4 && ajax.status==200) {
			document.getElementById("capaAlmacen").innerHTML=ajax.responseText;
		}
	}
	ajax.send(null);
}

// Función para resetear los campos del formulario de creación de usuarios
function cleanInputs(){
    var form_new_users = document.getElementById('FormularioCreacionBasico');
    var input_name = document.getElementById('user');
    var input_pass = document.getElementById('pass');

    input_name.innerHTML="";
    input_pass.innerHTML="";
    form_new_users.reset();
}

