<?php 

class Funciones extends MySQL{
	
	var $validacion;
	var $ids_interfaces;	
		
	// Funcion para validar que se introducen solo digitos
	function verificarSoloDigitos($cadena){ 
  		return (ctype_digit($cadena) or ($cadena == "-"));
    }
	
	// Funcion para validar que se introducen solo letras
	function verificarSoloLetras($cadena){ 
  		return (ctype_alpha($cadena) or ($cadena == "-"));
    }
	
	// Funcion validacion de email
	function verificarEmail($email){ 
  		if (filter_var($email , FILTER_VALIDATE_EMAIL ) or ($email == "-")){
      		return true; 
  		}
		else { 
       		return false; 
		} 
	}
	
	// Funcion validar solo numeros integer o float
	function verificarSoloNumeros($numero){
		if ((filter_var($numero , FILTER_VALIDATE_INT )) || (filter_var($numero, FILTER_VALIDATE_FLOAT)) ){
			return true;
		}
		else {
			return false;	
		}
	}
	
	// Funcion para validar fechas
	function validarFecha ($fecha) {
 		$fecha = explode("/",$fecha);
 		if(sizeof($fecha) != 3) return false;
 		if(checkdate($fecha[1],$fecha[0],$fecha[2])) return true;
 		else return false;
	}
	
	// Funcion para convertir fecha dd/mm/aaaa a formato SQL
	function cFechaMy ($fecha) {
 		$f = explode("/",$fecha);
 		return $f[2]."-".$f[1]."-".$f[0];
	}
	
	// Funcion para convertir fecha SQL  a formato dd/mm/aaaa
	function cFechaNormal($fecha) {
 		$f = explode("-",$fecha);
 		if(count($f) > 1) {
  			return $f[2]."/".$f[1]."/".$f[0];
 		} else {
  			return $fecha;
		 }
	}
	
	function cFechaMyEsp ($fecha) {
 		$f = explode("/",$fecha);
 		return $f[1]."/".$f[0]."/".$f[2];
	}

    // Funcion para tranformar la hora actual de Madrid a la de Brasil
    function fechaHoraBrasil($date){
        date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->sub(new DateInterval('PT5H'));
        $nuevafecha = $fecha->format('d/m/Y H:i');
        return $nuevafecha;
    }

    // Funcion para tranformar la hora actual a la de Madrid
    function fechaHoraSpain($date){
        date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->format('d/m/Y H:i');
        return $nuevafecha;
    }

    // Funcion para tranformar la hora de Madrid a la de Brasil
    function fechaBrasil($date){
        date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->sub(new DateInterval('PT5H'));
        $nuevafecha = $fecha->format('d/m/Y');
        return $nuevafecha;
    }

    // Funcion para tranformar la hora a la de Madrid
    function fechaSpain($date){
        date_default_timezone_set('Europe/Madrid');
        $fecha = new DateTime($date);
        $nuevafecha = $fecha->format('d/m/Y');
        return $nuevafecha;
    }


    function comprobarFechaPedido($fecha_creado_semana,$fecha_pedido){
		return ($fecha_creado_semana == $fecha_pedido);			
	}	

	// Elimina recursivamente los archivos creados en el directorio
    function eliminarDir($carpeta) {
    	foreach(glob($carpeta . "/*") as $archivos_carpeta) {
    		//echo $archivos_carpeta;
    		if (is_dir($archivos_carpeta)) {
    			$this->eliminarDir($archivos_carpeta);
    		}
    		else {
    			unlink($archivos_carpeta);
    		}
    	}
    	rmdir($carpeta);
    }

    // Funcion que comprueba si faltan datos por rellenar en el excel
    function comprobarDatosExcel($archivo){
    	$csv_tp = $archivo['type'];

		if(($csv_tp == "text/csv" || $csv_tp == "application/vnd.ms-excel" || $csv_tp == "text/comma-separated-values" || $csv_tp == "application/octet-stream" || $csv_tp == "application/x-octet-stream")) {
   			$csv_nm = $archivo['tmp_name'];
   			$file = fopen($csv_nm, "rt");
   			$fila = 0;
   			$error = false;
        	while(($datos = fgetcsv($file, 1000, ";")) !== false && !$error) {
            	++$fila;
            	if($datos[0] != "NOMBRE") {
					/*
					ESTRUCTURA DE DATOS:
			        0: Nombre Referencia
			        1: Nombre Pieza
			        2: Tipo Pieza
			        3: Referencia Proveedor
			        4: Referencia Fabricante
			        5: Nombre_01
			        6: Valor_01
			        7: Nombre_02
			        8: Valor_02
			        9: Nombre_03
			        10: Valor_03
			        11: Nombre_04
			        12: Valor_04
			        13: Nombre_05
			        14: Valor_05
			        15: Pack_Precio
			        16: Unidades 
			        17: Comentarios
			        18: Piezas utilizadas en el componente
			        19: Nombre del proveedor
			        20: Nombre del fabricante
			    	*/

			        $error = (empty($datos[0]) or empty($datos[3]) or empty($datos[4]));	
			        if(!$error){
			        	// EXCEL OK
			        	return 1;
			        }	
			        else {
			        	// ERROR: FALTAN DATOS DEL EXCEL POR RELLENAR
			        	return 3;
			        }
		    	}
			}  
		}
		else {
			// ERROR: EL DOCUMENTO NO ES VALIDO
			return 2;
		}
    }

    function comprobarArchivoConBOM($filename) {
        $bom = pack('CCC', 0xEF, 0xBB, 0xBF);
        $file = fopen($filename, "rb");
        $hasBOM = fread($file, 3) === $bom;
        fclose($file);

        if($hasBOM) {
            $contents = file_get_contents($filename);
            file_put_contents($filename, substr($contents, 3));
        }
        return $hasBOM;
    }
}
?>