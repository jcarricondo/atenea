<?php 
// Este fichero actualiza los pack precios de referencias segun su id_ref
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/kint/Kint.class.php");
permiso(3);

if(isset($_POST["actualizarPrecios"]) and $_POST["actualizarPrecios"] == 1) {
    $error = false;
    $error_bbdd = "NO";
	
    $validacion = new Funciones();
    $ref = new Referencia();

    // Obtenemos el id_usuario
    $id_usuario = $_SESSION["AT_id_usuario"];

    // Se comprueba si el archivo subido es un CSV
    $csv_tp = $_FILES["archivos"]['type'];
    if(($csv_tp == "application/x-csv" || $csv_tp == "text/csv" || $csv_tp == "application/vnd.ms-excel" || $csv_tp == "text/comma-separated-values" || $csv_tp == "application/octet-stream" || $csv_tp == "application/x-octet-stream")) {
        $csv_nm = $_FILES["archivos"]['tmp_name'];
        $file = fopen($csv_nm, "rt");
        $error = false;
        $fila = 0;
        $ultimo_es_titulo = true;

        // Obtenemos el ultimo proceso
        $id_proceso = $ref->dameUltimoProcesoLogIPR();
        if($id_proceso == NULL) $id_proceso = 1;
        else $id_proceso++;

        while ((($datos = fgetcsv($file, 1000, ";")) !== false) and (!$error)) {
            /*
                ESTRUCTURA DE DATOS:
                0: ID_REF
                1: Pack_Precio
            */

            $id_referencia_excel = $datos[0];
            $pack_precio_excel = $datos[1];   

            // Comprobamos que se han rellenado los campos obligatorios de la BBDD
            $error_faltan_datos = ($id_referencia_excel == "" || $pack_precio_excel == "");

            if(!$error_faltan_datos){   
                if($fila != 0 && !$error) {
                    $ultimo_es_titulo = false;
                    // Comprobamos que el id_ref sea numerico
                    if(is_numeric($id_referencia_excel)){
                        // Comprobamos si existe la referencia
                        if($ref->getExisteReferenciaPorId($id_referencia_excel)){
                            // Comprobamos el pack_precio
                            // Comprobamos si en el excel el usuario introdujo en "pack_precio", decimales con ","
                            $hay_coma = strpos($pack_precio_excel,",");    
                            if($hay_coma != false){
                                // Se encontro un pack_precio con decimal formateado con ","
                                $num_pack_precio = explode(",",$pack_precio_excel);
                                $parte_entera = $num_pack_precio[0];
                                $parte_decimal = $num_pack_precio[1];
                                $pack_precio = $parte_entera.".".$parte_decimal;
                            }
                            else {
                                // No hay coma
                                if(empty($pack_precio_excel)) $pack_precio = 0;  
                                else $pack_precio = $pack_precio_excel; 
                            }

                            // Comprobamos que el pack_precio sea un number
                            if(!is_numeric($pack_precio)){
                                $error = true;
                                $error_bbdd = "SI";
                                $mensaje_error .= "<br/>Pack_precio no es un valor entero o decimal";
                                $codigo_error = "Pack_precio no es un valor entero o decimal";
                            }
                            
                            if(!$error){
                                // Obtenemos el pack_precio de la referencia que se va a actualizar
                                $ref->cargaDatosReferenciaId($id_referencia_excel);
                                $pack_precio_antiguo = $ref->pack_precio;

                                // Se actualiza el pack_precio de la referencia
                                $resultado = $ref->actualizarPackPrecioImport($id_referencia_excel,$pack_precio);
                                if($resultado == 1){
                                    $mensaje_error .= "<br/><span style='color:#74b874;'>La referencia [".$id_referencia_excel."] con pack_precio [".$pack_precio_antiguo."] se ha actualizado a [".$pack_precio."] correctamente. [<a href='mod_referencia.php?id=".$id_referencia_excel."' target='_blank'>VER</a>]</span>";
                                    $codigo_error = "[OK]";
                                }
                                else {
                                    // ERROR AL ACTUALIZAR EL PRECIO DE UNA REFERENCIA
                                    $error = true;  
                                    $error_bbdd = "SI";
                                    $mensaje_error .= "<br/>".$ref->getErrorMessage($resultado);
                                    $codigo_error = $ref->getErrorMessage($resultado);
                                }
                            } 
                        }
                        else {
                            // ERROR NO EXISTE LA REFERENCIA 
                            $error = true;  
                            $error_bbdd = "SI";
                            $mensaje_error .= "<br/>No existe la referencia con ID_REF [".$id_referencia_excel."]. Verifique los ID_Ref introducidos";
                            $codigo_error = "No existe la referencia con ID_REF [".$id_referencia_excel."]. Verifique los ID_Ref introducidos";
                        } 
                    }   
                    else {
                        $error = true;  
                        $error_bbdd = "SI";
                        $mensaje_error .= "<br/>El id_ref no es un numero entero";
                        $codigo_error = "El id_ref no es un numero entero";
                    }
                }  
            }
            else {
                // ERROR NO SE RELLENARON TODOS LOS CAMPOS 
                $ultimo_es_titulo = false;
                $error = true;  
                $error_bbdd = "SI";
                $mensaje_error .= "<br/>No se rellenaron todos los campos del excel";
                $codigo_error .= "No se rellenaron todos los campos del excel";
            }

            // Guardamos un log con los datos introducidos por el usuario 
            if($fila != 0) {
                $ref->datosReferencia($id_referencia_excel,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$pack_precio_excel,NULL,NULL);
                
                $res_importacion = $ref->guardarLogImportacionPrecioReferencia($id_proceso,$id_usuario,$error_bbdd,$codigo_error);
                if($res_importacion == 1){
                    if($error){
                        $mensaje_error .= "<br/><span style='color:#74b874;'>Se ha guardado el log correctamente.</span>";
                    }
                    else {
                        $error_bbdd = "NO";
                        $mensaje_error .= "<br/><span style='color:#74b874;'>Se ha guardado el log para la referencia [".utf8_encode($id_referencia_excel)."] correctamente.</span>";
                    }
                }
                else {
                    $mensaje_error .= "<br/>".$ref->getErrorMessage($res_importacion);
                }
            }
            $fila++;
        }
        if($ultimo_es_titulo){
            $mensaje_error .= "<br/>El archivo de importación esta vacio. Rellene los campos obligatorios";  
        }
    } 
    else {
        $mensaje_error .= "<br/>El archivo de importación tiene un formato no valido [".$csv_tp."]";
    }  
} 

$pagina = "actualizar_precio_referencias";
$titulo_pagina = "B&aacutesico > Actualizar precio referencias";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3>Actualizar precio referencias</h3>
    <form id="FormularioCreacionBasico" name="actualizarPreciosReferencia" action="actualizar_precio_referencias.php" method="post" enctype="multipart/form-data">
    	<br />
        <h5>Descargue la plantilla para realizar la actualización de precios de referencias [<a href="../documentos/plantilla_actualizacion_precios.xlsx">Descargar plantilla</a>]</h5>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Importar archivo csv *</div>
            <div id="adjuntos"> 
                <div class="ContenedorCamposAdjuntar">
                    <input type="file" id="archivos" name="archivos" class="BotonAdjuntar"/>  
                </div>
            </div>
        </div>
        
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()"/> 
            <input type="hidden" id="actualizarPrecios" name="actualizarPrecios" value="1"/>
            <input type="submit" id="continuar" name="continuar" value="Continuar" />
        </div>
	    <div class="mensajeCamposObligatorios">
        	* Campos obligatorios
        </div>       
        <?php
            if($mensaje_error != "") {
                echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
			}
		?>
        <br />
        <h5>NOTAS PARA REALIZAR LA ACTUALIZACI&Oacute;N MASIVA DE PRECIOS DE REFERENCIAS</h5>
        <h5>En este proceso se actualizar&aacute;n los pack_precio de referencias segun su ID_REF</h5>
        <h5>Descargue el archivo excel desde el enlace "Descargar Plantilla"</h5>
        <h5>Rellene los campos del excel. Cada fila corresponde con una referencia</h5>
        <h5>Los campos marcados en <span style="color:red">ROJO</span> son obligatorios y no pueden dejarse en blanco</h5>
        <h5>El campo "PACK_PRECIO" tiene que ser un n&uacute;mero entero o decimal</h5>
        <h5>No introduzca el simbolo "€" en el campo "PACK_PRECIO"</h5>
        <h5>Guarde el fichero con extension .csv - Tipo:(CSV (delimitado por comas)(*.csv))</h5>
        <h5>Pulsar "SI" si aparece la pregunta "¿Desea mantener formato de libro?"</h5>
        <h5>Suba el archivo .csv desde el boton "Examinar"</h5>
        <h5>Pulse "Continuar"</h5>
        <br/>
        <h5><span style="color:red">NOTA:</span></h5>
        <h5><span style="color:red">Antes de realizar la importaci&oacute;n COMPRUEBE FEHACIENTEMENTE LOS ID_REF de las referencias que se van a actualizar</span></h5>
        <h5><span style="color:red">Tenga en cuenta que si introduce referencias distintas a las que se desea modificar su precio, se actualizar&aacute;n igualmente</span></h5>
        <h5><span style="color:red">Esta operaci&oacute;n quedar&aacute; registrada</span></h5>
        <br/>
    </form>
</div>    

<?php include ("../includes/footer.php"); ?>
