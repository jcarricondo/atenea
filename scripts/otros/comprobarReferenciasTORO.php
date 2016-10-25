<?php 
// Este fichero comprueba las referencias del SIMESTRUCK de SIMUMAK 
// Carga un fichero .csv con las referencias del SIMESTRUCK y verificamos si coinciden con las referencias de TORO
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/kint/Kint.class.php");
permiso(6);
if(isset($_POST["comprobarReferencias"]) and $_POST["comprobarReferencias"] == 1) {
	$fabricante = $_POST["fabricante"];
	$proveedor = $_POST["proveedor"];
	$error=false;
  $num_refs = 0;
  $num_refs_ok = 0;
  $num_refs_pack = 0;
  $num_refs_dif_id = 0;
  $num_refs_no_exist = 0;
  $num_refs_no_exist_no_id = 0;
	
	$validacion = new Funciones();

  if($error) {
    $mensaje_error = "ERROR AL CARGAR EL ARCHIVO";
  } 
  else {
    // Se comprueba si el archivo subido es un CSV
    $csv_tp = $_FILES["archivos"]['type'];
    if(($csv_tp == "application/x-csv" || $csv_tp == "text/csv" || $csv_tp == "application/vnd.ms-excel" || $csv_tp == "text/comma-separated-values" || $csv_tp == "application/octet-stream" || $csv_tp == "application/x-octet-stream")) {
      $csv_nm    = $_FILES["archivos"]['tmp_name'];
      $file = fopen($csv_nm, "rt");
      $error = false;
      while ((($datos = fgetcsv($file, 1000, ";")) !== false) and (!$error)) {
          /*
            ESTRUCTURA DE DATOS:
            0: ID REFERENCIA
            1: NOMBRE 
            2: ID PROVEEDOR
            3: REF. PROVEEDOR
            4: PART TIPO
            5: PART NOMBRE
            6: PACK_PRECIO
            7: UDS_PAQUETE
          */
          
          /*
          var_dump($datos[0]); echo "<br/>";
          var_dump($datos[1]); echo "<br/>";
          var_dump($datos[2]); echo "<br/>";
          var_dump($datos[3]); echo "<br/>";  
          var_dump($datos[4]); echo "<br/>";  
          var_dump($datos[5]); echo "<br/>";
          var_dump($datos[6]); echo "<br/>";
          var_dump($datos[7]); echo "<br/>";
          */

          // Cargamos las referencias de TORO con los id_ref de SIMUMAK
          // para comprobar si coinciden en ambas instancias
          $ref = new Referencia();
          $ref->cargaDatosReferenciaId($datos[0]);
          
          $nombre_referencia = $ref->referencia;
          $part_proveedor_referencia = $ref->part_proveedor_referencia;
          $part_nombre = $ref->part_nombre;
          $part_tipo = $ref->part_tipo;
          $pack_precio_toro = $ref->pack_precio;
          $unidades_toro = $ref->unidades;


       // if($datos[0] == 407){
          
          // Comprobamos por id_referencia y referencia del proveedor
          $ref->datosReferencia($datos[0],$datos[1],"",$datos[2],"","",$datos[3],"","","",$datos[6],$datos[7],"");
          if($ref->comprobarReferenciaTORO()) {
              // La referencia existe con el mismo id_ref, nombre y referencia de proveedor
              $mensaje_error .= "<br/>";
              $mensaje_error .= "<br/><span style='color: green;'>LA REF [".$datos[0]."] coincide con TORO</span>";
              // Comprobamos que los datos de pack_precio, uds_paquete son los mismos  
              if($pack_precio_toro != $datos[6]){
                  $num_refs_pack++;
                  $mensaje_error .= "<br/><span style='color: black;'>El pack_precio de TORO no coincide con el pack_precio de SIMUMAK</span>"; 
                  $mensaje_error .= "<br/><span style='color: black;'>Actualizando pack_precio...</span>"; 

                  // ACTUALIZAR PACK_PRECIO
                  /*
                  $updateSql = sprintf("update referencias set pack_precio=%s where activo=1 and id_referencia=%s",
                    $db->makeValue($datos[6], "float"),
                    $db->makeValue($datos[0], "int"));
                  $db->setConsulta($updateSql);
                  if($db->ejecutarSoloConsulta()){
                      $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado el pack_precio de la referencia [".$datos[0]."] correctanente <br/>";
                  }
                  else{
                      $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar el pack_precio de la referencia [".$datos[0]."] <br/>";
                  }
                  */
              }
              if($unidades_toro != $datos[7]){
                  $mensaje_error .= "<br/><span style='color: black;'>Las unidades_paquete de TORO no coinciden con las unidades_paquete de SIMUMAK</span>";   
                  $mensaje_error .= "<br/><span style='color: black;'>Actualizando unidades_paquete...</span>"; 

                  // ACTUALIZAR UNIDADES PAQUETE
                  /*
                  $updateSql = sprintf("update referencias set unidades=%s where activo=1 and id_referencia=%s",
                    $db->makeValue($datos[7], "float"),
                    $db->makeValue($datos[0], "int"));
                  $db->setConsulta($updateSql);
                  if($db->ejecutarSoloConsulta()){
                      $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado las unidades_paquete de la referencia [".$datos[0]."] correctanente <br/>";   
                  }
                  else {
                      $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar las unidades_paquete de la referencia [".$datos[0]."] <br/>"; 
                  }
                  */
              }

              $num_refs_ok++;
          }   
          else {
              // Comprobamos si existe con otro id_ref
              $ref->datosReferencia(NULL,$datos[1],"",$datos[2],"","",$datos[3],"","","",$datos[6],$datos[7],"");      
              if($ref->comprobarReferenciaTORO()) {
                  $mensaje_error .= "<br/>";
                  $mensaje_error .= "<br/><span style='color: orange;'>LA REF [".$datos[0]."] existe en TORO con otro id_ref distinto</span>";
                  $num_refs_dif_id++;
              }   
              else{ 
                  // Puede que se trate de un problema de codificacion de nombre de la referencia 
                  // Comprobamos por part_nombre y part_tipo y part_proveedor_referencia
                  $ref->datosReferencia($datos[0],$datos[1],"",$datos[2],$datos[5],$datos[4],$datos[3],"","","","","","");
                  // Comprobamos los campos tipo pieza y nombre por si no reconoció el nombre por codificacion
                  if($ref->comprobarReferenciaPorPiezaTORO()){
                      $mensaje_error .= "<br/>";
                      if($datos[4] != "-" and $datos[5] != "-"){
                          $mensaje_error .= "<br/><span style='color: green;'>LA REF [".$datos[0]."] coincide con TORO</span><span style='color: red;'> * (codificacion) </span> ";     

                          // Comprobamos que los datos de pack_precio, uds_paquete son los mismos  
                          if($pack_precio_toro != $datos[6]){
                              $mensaje_error .= "<br/><span style='color: black;'>El pack_precio de TORO no coincide con el pack_precio de SIMUMAK</span>";  
                              
                              /*
                              // Actualizar pack_precio
                              $updateSql = sprintf("update referencias set pack_precio=%s where activo=1 and id_referencia=%s",
                                $db->makeValue($datos[6], "float"),
                                $db->makeValue($datos[0], "int"));
                              $db->setConsulta($updateSql);
                              if($db->ejecutarSoloConsulta()){
                                  $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado el pack_precio de la referencia [".$datos[0]."] correctanente <br/>";
                              }
                              else{
                                  $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar el pack_precio de la referencia [".$datos[0]."] <br/>";
                              }
                              */
                          }
                          if($unidades_toro != $datos[7]){
                              $mensaje_error .= "<br/><span style='color: black;'>Las unidades_paquete de TORO no coinciden con las unidades_paquete de SIMUMAK</span>";        
                              // Actualizar unidades
                              // NO SE DA ESTE CASO
                              /*
                               $updateSql = sprintf("update referencias set unidades=%s where activo=1 and id_referencia=%s",
                                  $db->makeValue($datos[7], "float"),
                                  $db->makeValue($datos[0], "int"));
                               $db->setConsulta($updateSql);
                               if($db->ejecutarSoloConsulta()){
                                   $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado las unidades_paquete de la referencia [".$datos[0]."] correctanente <br/>";   
                               }
                               else {
                                   $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar las unidades_paquete de la referencia [".$datos[0]."] <br/>"; 
                               }
                              */
                          }
                      }    
                      else {
                          // $mensaje_error .= "<br/><span style='color: orange;'>Comprobar la referencia [".$datos[0]."]</span>";      
                          $mensaje_error .= "<br/><span style='color: green;'>LA REF [".$datos[0]."] coincide con TORO *</span>";
                      }
                  }
                  else {
                      $mensaje_error .= "<br/>";
                      // Comprobamos si el id_ref que queremos insertar ya esta ocupado en ATENEA TORO
                      if($ref->comprobarId_RefTORO()){
                          $mensaje_error .= "<br/><span style='color: red;'>LA REF [".$datos[0]."] NO existe en TORO y ya esta asociado el ID_REF a otra referencia</span>";                                   
                          $num_refs_no_exist_no_id++;

                          $array_refs_error[] = $datos[0];
                      }
                      else {
                          $mensaje_error .= "<br/><span style='color: red;'>LA REF [".$datos[0]."] NO existe en TORO</span>";   

                          // Crear la referencia en TORO con el mismo ID_REF y datos que en SIMUMAK
                          




                      }
                      $num_refs_no_exist++;
                  } 
              }
          }    



        //  }



          $num_refs++;    

      }

      // Descontamos el encabezado del excel
      $num_refs = $num_refs - 1;
      $num_refs_no_exist = $num_refs_no_exist - 1;

      $mensaje_error .= "<br/>";
      $mensaje_error .= "<br/><span style='color: black;'>Numero total de referencias: ".$num_refs."</span><br/>";
      $mensaje_error .= "<br/><span style='color: black;'>Numero referencias OK: ".$num_refs_ok."</span><br/>";
      $mensaje_error .= "<br/><span style='color: black;'>Numero referencias OK (pack_precio distintos): ".$num_refs_pack."</span><br/>";
      $mensaje_error .= "<br/><span style='color: black;'>Numero referencias existen con diferentes ID_REF: ".$num_refs_dif_id."</span><br/>";
      $mensaje_error .= "<br/><span style='color: black;'>Numero referencias no existen: ".$num_refs_no_exist."</span><br/>";
      $mensaje_error .= "<br/><span style='color: black;'>Numero referencias no existen y ya existe el id_ref: ".$num_refs_no_exist_no_id."</span><br/>";

      d($array_refs_error);

      d($datos[3]);


      if($error){
          $mensaje_error = "Se produjo un error en la referencia [".$ref."].";  
      }
    } 
    else {
    	$mensaje_error = "El archivo de importación tiene un formato no valido [".$csv_tp."]";
    }  
  }
} 
else {
	$fabricante = 0;
	$proveedor = 0;
}
$titulo_pagina = "B&aacutesico > Comprobar referencias";
include ('../includes/header.php');
?>
<div class="separador"></div> 
<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
       	<?php if(permisoMenu(5)) { ?>
    	<a class="BotonMenu" href="../basicos/proveedores.php">
           		Proveedores
       	</a>
        <?php 
		} if(permisoMenu(6)) { ?>
      	<a class="BotonMenu" href="../basicos/referencias.php" >
           		Referencias
       	</a>
        <?php 
		} 
		if(permisoMenu(8)) { ?>
        <a class="BotonMenu" href="../basicos/cabinas.php" >
           		Cabinas
        </a>
        <?php 
		}
		if(permisoMenu(9)) { ?>
        <a class="BotonMenu" href="../basicos/perifericos.php" >
           		Perifericos
        </a>
        <?php 
		}
		if(permisoMenu(10)) { ?>
        <a class="BotonMenu" href="../basicos/ordenadores.php" >
           		Ordenadores
        </a>
        <?php 
		}
		if(permisoMenu(11)) { ?>
        <a class="BotonMenu" href="../basicos/softwareSimulacion.php" >
         		Software
        </a>
        <?php 
		} 
		if(permisoMenu(12)) { ?>
        <a class="BotonMenu" href="../basicos/fabricantes.php">
            	Fabricantes
        </a>
        <?php 
		}
		if(permisoMenu(13)) { ?>
        <a class="BotonMenu" href="../basicos/familias.php">
            	Familias
        </a>
        <?php
		} if(permisoMenu(14)) { ?>
        <a class="BotonMenu" href="../basicos/clientes.php" >
           		Clientes
        </a>
        <?php 
		} 
		if(permisoMenu(15)) { ?>
        <a class="BotonMenu" href="../basicos/usuarios.php" >
          		Usuarios
        </a>
        <?php } ?>
        <a class="BotonMenuActual" href="../basicos/nuevareferencia.php">Nueva</a>
        <a class="BotonMenuActual" href="../basicos/referencias.php" >
           		Listado
        </a>

    </div> 
    
	<?php include ("../includes/opciones_usuario.php"); ?>
    
</div>


<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3> Importar referencias </h3>
    <form id="FormularioCreacionBasico" name="crearReferencia" action="comprobarReferenciasTORO.php" method="post" enctype="multipart/form-data">
    	<br />
        <h5>[<a href="../documentos/plantilla_importacion.xlsx">Descargar plantilla</a>]</h5>
        
        <div id="adjuntos"> 
        	<div class="ContenedorCamposAdjuntar">
        		<input type="file" id="archivos" name="archivos" class="BotonAdjuntar"/>  
        	</div>
        </div>
               
        <div class="ContenedorBotonCreacionBasico">
           	<input type="hidden" id="comprobarReferencias" name="comprobarReferencias" value="1"/>
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
    </form>
</div>    

<!--<div class="separador"></div>-->
<?php include ("../includes/footer.php"); ?>
