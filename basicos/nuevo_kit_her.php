<?php 
set_time_limit(10000);
// Este fichero crea un nuevo kit
include("../includes/sesion.php");
include("../classes/basicos/kit.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_componente.class.php");
include("../classes/basicos/referencia_heredada.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/fabricante.class.php");
include("../classes/basicos/log_importacion_referencias_componente.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/kint/Kint.class.php");
permiso(2);

$kt = new Kit();
$ref = new Referencia();
$ref_componente = new Referencia_Componente();
$ref_heredada = new Referencia_Heredada();
$funciones = new Funciones();
$proveedor = new Proveedor();
$fabricante = new Fabricante();
$log = new LogImportacionReferenciasComponente();

if(isset($_POST["guardandoKit"]) and $_POST["guardandoKit"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$referencia = $_POST["referencia"];
	$descripcion = $_POST["descripcion"];
	$version = $_POST["version"];
	$referencias = $_POST["REFS"];
	$piezas = $_POST["piezas"];
	$prototipo = $_POST["prototipo"];
	$estado = $_POST["estado"];
	$metodo = $_POST["metodo"]; 
	$seguir = true;	

	if($prototipo == NULL) $prototipo = "0";
	if(($nombre == '') or ($referencia == '') or ($descripcion == '') or ($version == '') ){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
	}
	else if(!is_numeric($version)){
		echo '<script type="text/javascript">alert("La versión debe ser un numero entero o un decimal con punto")</script>';
	}
	else {
		$fallo = false;   // Campo vacio 
		$fallo2 = false;  // Valor no numerico
		// Comprobamos si se realiza la importacion normal o masiva
		if($metodo == "normal") {
			if(count($piezas) != 0) {
				$i=0;
				while($i<count($piezas) and $seguir) {
					if(empty($piezas[$i])) {
						$seguir = false;
						echo '<script type="text/javascript">alert("Introduzca numero de unidades para todas las referencias")</script>';
					}
					elseif(!is_numeric($piezas[$i])) {
						$seguir = false;
						echo '<script type="text/javascript">alert("El campo PIEZAS debe ser un entero o un decimal con punto")</script>';
					}
					$i++;
				}
			}
		}
		else {
			// Comprobamos que las referencias del excel son correctas 
			if(isset($_FILES["archivo_importacion"])){
				$resultado = $funciones->comprobarDatosExcel($_FILES["archivo_importacion"]);
				switch ($resultado) {
					case 1:
						// OK
					break;
					case 2:
						// ERROR EXCEL NO VALIDO
						$seguir = false;
						echo '<script type="text/javascript">alert("El archivo excel no tiene un formato valido")</script>';	
					break;
					case 3:
						// ERROR CAMPOS SIN RELLENAR
						$seguir = false;
						echo '<script type="text/javascript">alert("Los campos en rojo son obligatorios para todas las referencias")</script>';	
					break;	
					default:
					# code...
					break;
				}
			}
		}

		if($seguir){
			$i=0;
			$error_archivo = 0;
			$archivos_adjuntos = false;
			if(isset ($_FILES["archivos"])) {
				while(($i<count($_FILES["archivos"]["name"])) and ($error_archivo == 0)) {
					if($_FILES["archivos"]["name"][$i] != NULL) {				
						$uploaddir = "mecanica/"; 
						$num_rand = rand(0,10000).rand(0,10000);
						$nombre_archivo[] = $num_rand.'_'.basename($_FILES['archivos']['name'][$i]);
						$uploadfile = $uploaddir . $num_rand.'_'.basename($_FILES['archivos']['name'][$i]); 
						$error_archivo = $_FILES['archivos']['error'][$i]; 
						$subido = false;
						if($error_archivo == UPLOAD_ERR_OK) {
							$subido = copy($_FILES['archivos']['tmp_name'][$i], $uploadfile);
						}
					}
					$i++;
				}
			}	
			
			if((!$archivos_adjuntos) or (($subido) and ($error_archivo == 0))) { 		
				if($metodo == "masivo"){
					unset($referencias);
					unset($piezas);
					include("../basicos/preparar_referencias_importacion.php");
				}

				// Si hay referencias
				if($referencias != NULL){
					$refs_piezas = $ref->agruparReferencias($referencias,$piezas);
					$referencias = $refs_piezas["referencias"];
					$piezas = $refs_piezas["piezas"];

					// Obtenemos las referencias heredadas
					for($i=0;$i<count($referencias);$i++) $referencias_componente[$i]["id_referencia"] = $referencias[$i];
					for($i=0;$i<count($piezas);$i++) $referencias_componente[$i]["piezas"] = $piezas[$i];
					$referencias_componente_final = $ref_heredada->obtenerHeredadas($referencias_componente);
					unset($referencias);
					unset($piezas);

					for($i=0;$i<count($referencias_componente_final);$i++){
						$referencias[] = $referencias_componente_final[$i]["id_referencia"];
						$piezas[] = $referencias_componente_final[$i]["piezas"];
					}
				}

				$kt->datosNuevoKit(NULL,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,5,$nombre_archivo,$estado,$prototipo);
				$resultado = $kt->guardarCambios();
				if($resultado == 1) {
					// Guardamos un log en caso de importacion masiva
					if($metodo == "masivo"){
						$id_usuario = $_SESSION["AT_id_usuario"];
						$id_componente = $kt->getExisteComponente($nombre,$version,5);
						$tipo_componente = "KIT";
						$proceso = "NUEVO KIT";
						include("../basicos/log_importacion_referencias_componente.php");
						header("Location: kits.php?operacion_kit=creado");
					}
					else {
						header("Location: kits.php?operacion_kit=creado");
					}	
				} 
				else {
					$mensaje_error = $kt->getErrorMessage($resultado);
				}
			}
		}
	}
} 
else {
	$nombre = "";
	$referencia = "";
	$descripcion = "";
	$version = "";
	$referencias = "";
}

$componente = "kit";
$titulo_pagina = "Básico > Nuevo kit";
$pagina = "new_kit";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/basicos/nuevo_kit_08032017_1050.js"></script>';
echo '<script type="text/javascript" src="../js/basicos/nuevo_kit_adjuntos.js"></script>';
echo '<script type="text/javascript" src="../js/funciones.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
      
    <h3> Creaci&oacute;n de un nuevo kit </h3>
    <form id="FormularioCreacionBasico" name="crearKit" action="nuevo_kit_her.php" method="post" enctype="multipart/form-data">
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de un nuevo kit </h5>
    	<div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>" />
        </div>    
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencia *</div>
           	<input type="text" id="referencia" name="referencia" class="CreacionBasicoInput" value="<?php echo $referencia;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Descripcion *</div>
          	<input type="text" id="descripcion" name="descripcion" class="CreacionBasicoInput" value="<?php echo $descripcion;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Version *</div>
          	<input type="text" id="version" name="version" class="CreacionBasicoInput" value="<?php echo $version;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Estado *</div>
            <select id="estado" name="estado" class="CreacionBasicoInput">
            	<?php
					$num_estados = 3;
					$estados = array ("BORRADOR","PRODUCCIÓN","LEGACY");
					for($i=0;$i<$num_estados;$i++) {
						echo '<option value="'.$estados[$i].'">'.$estados[$i].'</option>';
					}
				?>
            </select>
            <div class="LabelPrototipo">Prototipo</div>
          	<input type="checkbox" id="prototipo" name="prototipo" class="BotonEliminar" style="margin: 5px 0px 5px 0px;" value="1" />
        </div>
        <br/>
        <br/>
        <div id="capa_metodo" class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico"></div>
           	<div id="capa_boton_metodo" class="ContenedorCamposAdjuntar">
        		<input type="button" id="importar_excel" name="importar_excel" class="BotonEliminar" value="IMPORTAR DESDE EXCEL" onclick="cargaArchivoImportacion();" />  
        		<input type="hidden" id="metodo" name="metodo" value="normal">
        	</div>	
        </div>
        <div id="capa_referencias" class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Referencias</div>
           		<div class="CajaReferencias">
            		<div id="CapaTablaIframe">
    					<table id="mitabla">
    					<th style="text-align:center">ID REF</th>
        				<th>NOMBRE</th>
        				<th>PROVEEDOR</th>
        				<th>REF. PROVEEDOR</th>
                        <th>NOMBRE PIEZA</th>
        				<th style="text-align:center">PIEZAS</th>
        				<th style="text-align:center">PACK PRECIO</th>
                        <th style="text-align:center">UDS/P</th> 
                        <th style="text-align:center">PRECIO UNIDAD</th>
                        <th style="text-align:center">PRECIO</th>  
                        <th style="text-align:center">ELIMINAR</th>       
                        </table>
                    </div>
			    </div> 
            	<input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="Abrir_ventana('buscador_referencias.php?componente=<?php echo $componente;?>')"/> 																																																																																																																																			                <input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRow(mitabla)"  />
        </div>
        <div class="ContenedorCamposCreacionBasico" id="capa_opciones" style="display:none;">
        	<div class="LabelCreacionBasico">Referencias</div>
        	<div class="ContenedorCamposImportacion">
        		<a class="EnlaceDescarga" href="../documentos/importacion_masiva.zip"><span class="fuenteSimumakLittle">Descargar plantilla</span></a>
        	</div>
        	<div id="importar_archivo"> 
	        	<div class="ContenedorCamposAdjuntar">
	        		<input type="file" id="archivo_importacion" name="archivo_importacion" class="BotonAdjuntar" style="margin-left: 0px;" />  
	        	</div>
        	</div>
        </div>
        <br/>
        <div id="coste_componente" class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Kit</div>
            <div id="CosteTotalComponente"><span class="fuenteSimumakNegrita"><?php echo number_format($precio_total, 2, ',', '.'); ?> €</span></div>
        </div>
        <br/>
        <br/>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Información mecánica</div>
            <div id="AñadirMasArchivos"><a href="#" onClick="addCampo()"><span class="fuenteSimumakLittle">Subir otro archivo</span></a></div>
        </div> 
        <div id="adjuntos"> 
        	<div class="ContenedorCamposAdjuntar">
        		<input type="file" id="archivos[]" name="archivos[]" class="BotonAdjuntar"/>  
        	</div>
        </div>
        <br/>
        <br/>
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="VOLVER" class="BotonEliminar" onclick="window.history.back()"/>
            <input type="hidden" id="guardandoKit" name="guardandoKit" value="1"/>
            <input type="submit" id="continuar" name="continuar" value="CONTINUAR" class="BotonEliminar" />
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
<?php include ("../includes/footer.php"); ?>