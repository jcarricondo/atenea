<?php 
// Este fichero interpreta los datos del excel importado. 
// Interpretamos el excel 
// Si no existe el provedor para la referencia lo creamos 
// Si no existe el fabricante para la referencia lo creamos 
// Si no existe la referencia para el proveedor y fabricante dados la creamos 
// Si existe la referencia para el proveedor y fabricante dados la actualizamos y actualizamos todos los componentes con esa referencia

$csv_tp = $_FILES["archivo_importacion"]['type'];
if(($csv_tp == "text/csv" || $csv_tp == "application/vnd.ms-excel" || $csv_tp == "text/comma-separated-values" || $csv_tp == "application/octet-stream" || $csv_tp == "application/x-octet-stream")) {
	$csv_nm = $_FILES["archivo_importacion"]['tmp_name'];
	$file = fopen($csv_nm, "rt");

    // El excel fue guardado con formato ANSI. Tenemos que cambiar el formato a UTF-8.
    // Para ello guardamos el contenido del fichero en un string, lo formateamos y lo
    // volvemos a guardar
    $f = file_get_contents($csv_nm);
    $f = iconv("WINDOWS-1252","UTF-8", $f);
    file_put_contents($csv_nm, $f);

    $con_BOM = $funciones->comprobarArchivoConBOM($csv_nm);
    if($con_BOM){
        // Se eliminó el BOM correctamente
    }

	$fila = 0;
	$cont = 0;
	while(($datos = fgetcsv($file, 1000, ";")) !== false) {
		++$fila;
		if($datos[0] != "NOMBRE") {
			$id_proveedor = "";
			$id_fabricante = "";
			$id_referencia = "";
						                
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

			$nombre_referencia = $datos[0];
            $nombre_pieza = $datos[1];
            $tipo_pieza = $datos[2];
            $ref_proveedor = $datos[3];
            $ref_fabricante = $datos[4];
            $nombre1 = $datos[5];
            $valor1 = $datos[6];
            $nombre2 = $datos[7];
            $valor2 = $datos[8];
            $nombre3 = $datos[9];
            $valor3 = $datos[10];
            $nombre4 = $datos[11];
            $valor4 = $datos[12];
            $nombre5 = $datos[13];
            $valor5 = $datos[14];
            $pack_precio = $datos[15];
            $pack_precio_aux = $datos[15];
            $unidades_paquete = $datos[16];
            $comentarios = $datos[17];
            $piezas_ref = $datos[18];
            $nombre_proveedor = $datos[19];
            $nombre_fabricante = $datos[20];

			// VERIFICACIONES DEL PROVEEDOR
			if($nombre_proveedor == "") {
				// Si el proveedor está en blanco se le asigna automáticamente el proveedor "0 - SIN ESPECIFICAR";
				$id_proveedor = 86;
			} 
			else {
				// Se consulta si existe el proveedor
				$id_proveedor = $proveedor->getExisteProveedor($nombre_proveedor);
				if($id_proveedor == NULL) {
					// Se crea el proveedor
					$id_proveedor = $proveedor->crearProveedorImport($nombre_proveedor);
					$mensaje .= '<div class="mensaje">Se ha creado el proveedor '.$nombre_proveedor.'</div>';
                    $proveedores_nuevos[$cont] = "SI";
				}
				else {
					$proveedores_nuevos[$cont] = "NO";
				}
    		}
            $proveedores_id[$cont] = $id_proveedor;

			// VERIFICACIONES DEL FABRICANTE
            if($nombre_fabricante == "") {
            	// Si el fabricante está en blanco se le asigna automáticamente el fabricante "0 - SIN ESPECIFICAR";
                $id_fabricante = 30;
            } 
            else {
            	// Se consulta si existe el fabricante
                $id_fabricante = $fabricante->getExisteFabricante($nombre_fabricante);
                if($id_fabricante == NULL) {
                	// Se crea el fabricante
                    $id_fabricante = $fabricante->crearFabricanteImport($nombre_fabricante);
                    $mensaje .= '<div class="mensaje">Se ha creado el fabricante '.$nombre_fabricante.'</div>';
                    $fabricantes_nuevos[$cont] = "SI";
                }
                else {
                	$fabricantes_nuevos[$cont] = "NO";
                }
            }
            $fabricantes_id[$cont] = $id_fabricante;

            // VERIFICACIONES DE LA REFERENCIA
            if($nombre_referencia == "" || $ref_proveedor == "" || $ref_fabricante == "") {
				$mensaje .= '<div class="mensaje_error">La fila '.$fila.' no se puede procesar, no dispone de números de referencia</div>';
            } 
            else {
            	// Se comprueba si existe la referencia para el proveedor indicado
                $id_referencia = $ref->getExisteReferencia($ref_proveedor,$id_proveedor);
                  
                // Si el nombre pieza esta en blanco ponemos el de la referencia de proveedor
                if($nombre_pieza == "") $nombre_pieza = $ref_proveedor;
                if($tipo_pieza == "") $tipo_pieza = $ref_proveedor;
                    
                // Comprobamos si en el excel el usuario introdujo en pack_precio decimales con ","
                $pos = strpos($pack_precio,",");      
                // Si precio esta en blanco le ponemos 0
                if($pack_precio == "") $pack_precio = 0;
                else if($pos != false){
                	// Se introdujo en el excel un pack_precio con decimales con ","
                    $num_pack_precio = explode(",",$pack_precio);
                    $parte_entera = $num_pack_precio[0];
                    $parte_decimal = $num_pack_precio[1];
                    $pack_precio = $parte_entera.".".$parte_decimal;
                }
                else if(!is_numeric($pack_precio)) $pack_precio = 0;

                // Comprobamos las unidades_paquete
                if($unidades_paquete == NULL || $unidades_paquete == 0) $unidades_paquete = 1;
                else $unidades_paquete = intval($unidades_paquete);

                // Comprobamos si en el excel el usuario introdujo en piezas decimales con ","
                $pos_p = strpos($piezas_ref,",");          
                // Si las piezas usadas en el componente es blanco le asignamos 1
                if(($piezas_ref == "") || ($piezas_ref == 0)) $piezas_ref = 1;
                else if($pos_p != false){
                	// Se introdujo en el excel piezas con decimales con ","
                    $num_piezas = explode(",",$piezas_ref);
                    $parte_entera_piezas = $num_piezas[0];
                    $parte_decimal_piezas = $num_piezas[1];
                    $piezas_ref = $parte_entera_piezas.".".$parte_decimal_piezas;    
                }

                if($id_referencia == NULL) {
                	// Se crea la referencia
                    $id_referencia = $ref->crearReferenciaImport($nombre_referencia,$id_proveedor,$id_fabricante,$tipo_pieza,$nombre_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,
												$nombre3,$valor3,$nombre4,$valor4,$nombre5,$valor5,$pack_precio,$unidades_paquete,$comentarios);

                    $referencias_nuevas[$cont] = "SI";
                }
                else {
                	// Se actualiza la referencia
                    // Comprobamos si se debe actualizar el pack_precio
                    $ref->cargaDatosReferenciaId($id_referencia);
                    $pack_precio_bbdd = $ref->pack_precio;

                    if($pack_precio_aux == ""){
                        // NO ACTUALIZAMOS EL PACK_PRECIO 
                        $id_referencia = $ref->actualizarReferenciaImportSinPrecio($id_referencia,$nombre_referencia,$id_proveedor,$id_fabricante,$tipo_pieza,$nombre_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,
                                            $nombre3,$valor3,$nombre4,$valor4,$nombre5,$valor5,$unidades_paquete,$comentarios);
                    }
                    else {
                        // ACTUALIZAMOS EL PACK_PRECIO
                        $id_referencia = $ref->actualizarReferenciaImport($id_referencia,$nombre_referencia,$id_proveedor,$id_fabricante,$tipo_pieza,$nombre_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,
											$nombre3,$valor3,$nombre4,$valor4,$nombre5,$valor5,$pack_precio,$unidades_paquete,$comentarios);
                    }

                    // Como hemos modificado la referencia debemos actualizar la tabla componentes_referencias que contengan esa referencia
                    // Obtenemos los datos de la tabla componentes_referencias que tengan esa referencia
                    $datos_componentes_referencias = $ref->dameComponentesReferencias($id_referencia);

                    for($i=0;$i<count($datos_componentes_referencias);$i++){
                    	$id = $datos_componentes_referencias[$i]["id"];
   
                        if($pack_precio_aux == ""){
                            // Actualizamos (segun el ID) las unidades_paquete, las piezas y la fecha de creacion de la tabla componentes_referencias
                            $resultado = $ref->actualizarComponentesReferenciasSinPrecio($id,$unidades_paquete); 
                        }
                        else {
                            // Actualizamos (segun el ID) el pack_precio, las unidades_paquete, las piezas y la fecha de creacion de la tabla componentes_referencias
                            $resultado = $ref->actualizarComponentesReferencias($id,$unidades_paquete,$pack_precio); 
                        }

                        // Obtenemos las piezas de los componentes con esa referencia para recalcular los paquetes
                        if($resultado == 1){
                            $piezas_componente = $ref->damePiezasComponenteId($id);
                            $piezas_componente = $piezas_componente[0]["piezas"];

                            // Recalculamos los paquetes 
                            $ref->calculaTotalPaquetes($unidades_paquete,$piezas_componente);
                            $total_paquetes = $ref->total_paquetes;

                            // Actualizamos los paquetes
                            $resultado = $ref->actualizaPaquetesReferenciaComponente($id,$total_paquetes);
                            if($resultado != 1){
                                $mensaje .= '<div class="mensaje_error">'.$ref->getErrorMessage($resultado).'</div>';
                            }
                            else{
                                $mensaje .= '<div class="mensaje">Se ha actualizado el total_paquetes de la referencia '.$ref_proveedor.' con id ['.$id.']</div>';
                            }    
                        }
                        else{    
                            $i = count($datos_componentes_referencias); 
                            $mensaje .= '<div class="mensaje_error">'.$ref->getErrorMessage($resultado).'</div>';
                        }
                    }
                    $referencias_nuevas[$cont] = "NO";
                }
                $referencias_id[$cont] = $id_referencia;
            }

            // Preparamos los arrays de referencias y piezas 
            $referencias[$cont] = intval($id_referencia);
            $piezas[$cont] = floatval($piezas_ref); 
            $cont++;
        }
    }   
}     

?>
