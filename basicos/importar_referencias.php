<?php 
// Este fichero crea una nueva referencia
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/fabricante.class.php");
include("../classes/basicos/listado_proveedores.class.php");
include("../classes/basicos/listado_fabricantes.class.php");
include("../classes/kint/Kint.class.php");
permiso(3);

if(isset($_POST["importarReferencias"]) and $_POST["importarReferencias"] == 1) {
    $fabricante = $_POST["fabricante"];
    $proveedor = $_POST["proveedor"];
    $error=false;
    $error_bbdd = "NO";
	
    $validacion = new Funciones();
    $new_ref = new Referencia();

    // Obtenemos el id_usuario
    $id_usuario = $_SESSION["AT_id_usuario"];

    if($fabricante == 0 or $proveedor == 0) {
        $mensaje_error = "Se tiene que indicar un fabricante y un proveedor";
    } 
    else {
        // Se comprueba si el archivo subido es un CSV
        $csv_tp = $_FILES["archivos"]['type'];
        if(($csv_tp == "application/x-csv" || $csv_tp == "text/csv" || $csv_tp == "application/vnd.ms-excel" || $csv_tp == "text/comma-separated-values" || $csv_tp == "application/octet-stream" || $csv_tp == "application/x-octet-stream")) {
            $csv_nm    = $_FILES["archivos"]['tmp_name'];
            $file = fopen($csv_nm, "rt");

            // El excel fue guardado con formato ANSI. Tenemos que cambiar el formato a UTF-8.
            // Para ello guardamos el contenido del fichero en un string, lo formateamos y lo
            // volvemos a guardar
            $f = file_get_contents($csv_nm);
            $f = iconv("WINDOWS-1252","UTF-8", $f);
            file_put_contents($csv_nm, $f);

            $con_BOM = $validacion->comprobarArchivoConBOM($csv_nm);
            if($con_BOM){
                // Se eliminó el BOM correctamente
            }

            $error = false;
            $fila = 0;
            while ((($datos = fgetcsv($file, 1000, ";")) !== false) and (!$error)) {
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
                */

                // Comprobamos que se han rellenado los campos obligatorios de la BBDD
                $error_faltan_datos = (empty($datos[0]) or empty($datos[3]) or empty($datos[4]));
                $ref = $datos[0];

                // Establecemos valores predeterminados en caso de campo vacio para campos "no obligatorios"
                if($datos[1] == NULL) $datos[1] = "-";
                if($datos[2] == NULL) $datos[2] = "-";

                // Guardamos en una variable auxiliar el pack_precio y las unidades para guardar en el log
                $pack_precio_log = $datos[15];
                $unidades_log = $datos[16];

                if(!$error_faltan_datos){
                    if($fila != 0 && !$error) {
                        // Comprobamos el pack_precio
                        // Comprobamos si en el excel el usuario introdujo en "pack_precio", decimales con ","
                        $hay_coma = strpos($datos[15],",");    
                        if($hay_coma != false){
                            // Se encontro un pack_precio con decimal formateado con ","
                            $num_pack_precio = explode(",",$datos[15]);
                            $parte_entera = $num_pack_precio[0];
                            $parte_decimal = $num_pack_precio[1];
                            $datos[15] = $parte_entera.".".$parte_decimal;
                        }
                        if(!is_numeric($datos[15])){
                            $error = true;
                            $error_bbdd = "SI";
                            $codigo_error = "[PACK_PRECIO NO ES UN VALOR ENTERO O DECIMAL]";
                        }
                        else if(empty($datos[15])) $datos[15] = 0;

                        if(!$error){
                            // Comprobamos las unidades
                            // Comprobamos si en el excel el usuario introdujo en "unidades", decimales con ","
                            $hay_coma = strpos($datos[16],",");    
                            if($hay_coma != false){
                                $num_unidades = explode(",",$datos[16]);
                                $parte_entera = $num_unidades[0];
                                $parte_decimal = $num_unidades[1];
                                $datos[16] = $parte_entera.".".$parte_decimal;  
                            }

                            if(!is_numeric($datos[16])){
                                $error = true;
                                $error_bbdd = "SI";
                                $codigo_error = "[UNIDADES NO ES UN VALOR ENTERO O DECIMAL]";
                            }
                            else if(empty($datos[16]) or ($datos[16] == 0)) $datos[16] = 1;
                        }

                        if(!$error){
                            // Se comprueba si la referencia ya existe en el proveedor indicado
                            $nuevaReferencia = new Referencia();
                            $nuevaReferencia->datosNuevaReferencia(NULL,$datos[0],$fabricante,$proveedor,$datos[1],$datos[2],$datos[3],$datos[4],$datos[5],$datos[6],$datos[7],$datos[8],$datos[9],$datos[10],$datos[11],$datos[12],$datos[13],$datos[14],$datos[15],$datos[16],NULL,$datos[17]);
                            if($nuevaReferencia->comprobarReferenciaProveedorDuplicada()) {
                                $mensaje_error .= "<br/>No se ha podido importar la referencia: la referencia ".$datos[3]." ya existe para el proveedor indicado";
                                $error_bbdd = "SI";
                            } 
                            else {
                                // Se guarda la referencia en la base de datos
                                if($nuevaReferencia->guardarCambios()) {
                                  $mensaje_error .= "<br/><span style='color:#74b874;'>La referencia ".$datos[0]." se ha guardado correctamente. [<a href='mod_referencia.php?id=".$nuevaReferencia->id_referencia."' target='_blank'>VER</a>]</span>";
                                } 
                                else {
                                  $mensaje_error .= "<br/>".$referencias->getErrorMessage($resultado);
                                  $error_bbdd = "SI";
                                }
                            }
                        } 
                    }        
                }
                else {
                    $error = true;
                    $error_bbdd = "SI";
                }
              
                // Guardamos un log con los datos introducidos por el usuario 
                if($fila != 0) {
                    $new_ref->datosNuevaReferencia(NULL,$datos[0],$fabricante,$proveedor,$datos[1],$datos[2],$datos[3],$datos[4],$datos[5],$datos[6],$datos[7],$datos[8],
                                                            $datos[9],$datos[10],$datos[11],$datos[12],$datos[13],$datos[14],$pack_precio_log,$unidades_log,NULL,$datos[17]);
                    $res_importacion = $new_ref->guardarLogImportacionReferencia($id_usuario,$error_bbdd);
                    if($res_importacion == 1){
                        if(empty($datos[0])){
                            $mensaje_error .= "<br/><span style='color:#74b874;'>Se ha guardado el log correctamente.</span>";
                        }
                        else{
                            $error_bbdd = "NO";
                            $mensaje_error .= "<br/><span style='color:#74b874;'>Se ha guardado el log para la referencia [".$datos[0]."] correctamente.</span>";
                        }
                    }
                    else {
                        $mensaje_error .= "<br/>".$new_ref->getErrorMessage($res_importacion);
                    }
                }
                $fila++;
            }
            if($error){
                if($error_faltan_datos){
                    $mensaje_error .= "<br/>Se produjo un error porque no se rellen&oacute; alguno de los campos obligatorios ";  
                } 
                else {
                    $mensaje_error .= "<br/>Se produjo un error en la referencia [".$ref."]. Compruebe que ha rellenado los datos correctamente ".$codigo_error;
                }
            }
        } 
        else {
            $mensaje_error .= "<br/>El archivo de importación tiene un formato no valido [".$csv_tp."]";
        }  
    }
} 
else {
	$fabricante = 0;
	$proveedor = 0;
}

$pagina = "importar_referencias";
$titulo_pagina = "B&aacutesico > Importar referencias";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3> Importar referencias </h3>
    <form id="FormularioCreacionBasico" name="crearReferencia" action="importar_referencias.php" method="post" enctype="multipart/form-data">
    	<br />
        <h5>Indique el fabricante y el proveedor para realizar la importaci&oacuten masiva [<a href="../documentos/plantilla_importacion.xlsx">Descargar plantilla</a>]</h5>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Fabricante *</div>
           	<select id="fabricante" name="fabricante"  class="CreacionBasicoInput">
              <option value=0>Selecciona</option>
            	<?php 
					       $bbdd = new MySQL;
					       $nf = new listadoFabricantes();
					       $nf->prepararConsulta();
					       $nf->realizarConsulta();
					       $resultado_fabricantes = $nf->fabricantes;

					       for($i=0;$i<count($resultado_fabricantes);$i++) {
						        $fab = new Fabricante();
						        $datoFabricante = $resultado_fabricantes[$i];
						        $fab->cargaDatosFabricanteId($datoFabricante["id_fabricante"]);
						        echo '<option value="'.$fab->id_fabricante.'"';if ($fab->id_fabricante == $fabricante) { echo 'selected="selected"'; } echo '>'.$fab->nombre.'</option>';
					       }
				      ?>
            </select>
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Proveedor *</div>
           	<select id="proveedor" name="proveedor"  class="CreacionBasicoInput">
              <option value="0">Selecciona</option>
            	<?php 
					       $np = new listadoProveedores();
					       $np->prepararConsulta();
					       $np->realizarConsulta();
					       $resultado_proveedores = $np->proveedores;

					       for($i=0;$i<count($resultado_proveedores);$i++) {
						        $prov = new Proveedor();
						        $datoProveedor = $resultado_proveedores[$i];
						        $prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]);
						        echo '<option value="'.$prov->id_proveedor.'"';if ($prov->id_proveedor == $proveedor) { echo 'selected="selected"'; } echo '>'.$prov->nombre.'</option>';
					       }
				      ?>
            </select>
        </div>
        <div id="adjuntos"> 
        	<div class="ContenedorCamposAdjuntar">
        		<input type="file" id="archivos" name="archivos" class="BotonAdjuntar"/>  
        	</div>
        </div>
               
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()"/> 
            <input type="hidden" id="importarReferencias" name="importarReferencias" value="1"/>
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
        <h5>NOTAS PARA REALIZAR LA IMPORTACI&Oacute;N MASIVA DE REFERENCIAS</h5>
        <h5>Antes de realizar la importaci&oacute;n compruebe que no existen las referencias en la BBDD</h5>
        <h5>Descargue el archivo excel desde el enlace "Descargar Plantilla"</h5>
        <h5>Rellene los campos del excel. Cada fila corresponde con una referencia</h5>
        <h5>Los campos marcados en <span style="color:red">ROJO</span> son obligatorios y no pueden dejarse en blanco</h5>
        <h5>El campo "PACK_PRECIO" tiene que ser un n&uacute;mero entero o decimal</h5>
        <h5>No introduzca el simbolo "€" en el campo "PACK_PRECIO"</h5>
        <h5>El campo "UNIDADES" tiene que ser un n&uacute;mero entero o decimal</h5>
        <h5>El campo "UNIDADES" no puede ser 0</h5>
        <h5>Guarde el fichero con extension .csv - Tipo:(CSV (delimitado por comas)(*.csv))</h5>
        <h5>Pulsar "SI" si aparece la pregunta "¿Desea mantener formato de libro?"</h5>
        <h5>Suba el archivo .csv desde el boton "Examinar"</h5>
        <h5>Comprueba que el proveedor y el fabricante son los correctos para las referencias introducidas</h5>
        <h5>Pulse "Continuar"</h5>
        <h5>Esta operacion quedar&aacute; registrada</h5>
    </form>
</div>    

<!--<div class="separador"></div>-->

<?php include ("../includes/footer.php"); ?>
