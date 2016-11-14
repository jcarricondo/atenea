<?php
// Este fichero crea una nueva referencia
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/fabricante.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/listado_fabricantes.class.php");
include("../classes/basicos/listado_proveedores.class.php");
include("../classes/log/basicos/log_basicos_referencias.class.php");
permiso(2);

$db = new MySQL();
$bbdd = new MySQL;
$referencias = new Referencia();
$validacion = new Funciones();
$fab = new Fabricante();
$prov = new Proveedor();
$nf = new listadoFabricantes();
$np = new listadoProveedores();
$log = new LogBasicosReferencias();

if(isset($_POST["guardandoReferencia"]) and $_POST["guardandoReferencia"] == 1) {
	// Se reciben los datos
	$nombre = $_POST["nombre"];
	$fabricante = $_POST["fabricante"];
	$proveedor = $_POST["proveedor"];
	$nombre_pieza = $_POST["nombre_pieza"];
	$tipo_pieza = $_POST["tipo_pieza"];
	$ref_proveedor_pieza = $_POST["ref_proveedor_pieza"];
	$ref_fabricante_pieza = $_POST["ref_fabricante_pieza"];
	$part_value_name = $_POST["part_value_name"];
	$part_value_qty = $_POST["part_value_qty"];
	$part_value_name_2 = $_POST["part_value_name_2"];
	$part_value_qty_2 = $_POST["part_value_qty_2"];
	$part_value_name_3 = $_POST["part_value_name_3"];
	$part_value_qty_3 = $_POST["part_value_qty_3"];
	$part_value_name_4 = $_POST["part_value_name_4"];
	$part_value_qty_4 = $_POST["part_value_qty_4"];
	$part_value_name_5 = $_POST["part_value_name_5"];
	$part_value_qty_5 = $_POST["part_value_qty_5"];
	$pack_precio = $_POST["pack_precio"];
	$unidades = $_POST["unidades"];
	$comentarios = $_POST["comentarios"];
	$error=false;

	if (($ref_proveedor_pieza == '') or ($ref_fabricante_pieza == '') or ($pack_precio == '') or ($unidades == '') ){
		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
		$error = true;
	}
	elseif (!$validacion->verificarSoloNumeros($pack_precio) && $pack_precio != 0) {
		echo '<script type="text/javascript">alert("El precio del pack introducido no es correcto")</script>';
	}
	elseif (!$validacion->verificarSoloDigitos($unidades)) {
		echo '<script type="text/javascript">alert("El valor de unidades introducido no es correcto")</script>';
	}
	else {
		if ($nombre == '') $nombre = '-';
		if ($fabricante == '') $fabricante = 0;
		if ($proveedor == '') $proveedor = 0;
		if ($nombre_pieza == '') $nombre_pieza = '-';
		if ($tipo_pieza == '') $tipo_pieza = '-';

		$i=0;
		$error_archivo = 0;
		$archivos_adjuntos = false;
		if (isset ($_FILES["archivos"])) {
			while (($i<count($_FILES["archivos"]["name"])) and ($error_archivo == 0)) {
				if ($_FILES["archivos"]["name"][$i] != NULL) {
					$uploaddir = "uploads/";
					$nombre_archivo[] = rand(0,100).rand(0,100).'_'.basename($_FILES['archivos']['name'][$i]);
					$uploadfile = $uploaddir . $nombre_archivo[$i];
					$error_archivo = $_FILES['archivos']['error'][$i];
					$subido = false;
					if($error_archivo == UPLOAD_ERR_OK) {
						$subido = copy($_FILES['archivos']['tmp_name'][$i], $uploadfile);
					}
				}
				$i++;
			}
		}
		if ((!$archivos_adjuntos) or (($subido) and ($error_archivo == 0))) {
			$referencias->datosNuevaReferencia(NULL,$nombre,$fabricante,$proveedor,$nombre_pieza,$tipo_pieza,$ref_proveedor_pieza,$ref_fabricante_pieza,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades,$nombre_archivo,$comentarios);
			$resultado = $referencias->guardarCambios();
			if($resultado == 1) {
				// Guardamos el log de la operación
				$id_referencia = $referencias->getUltimoID();
				$referencias->cargaDatosReferenciaId($id_referencia);
				$fecha_creado = $referencias->fecha_creado;

				$id_usuario = $_SESSION["AT_id_usuario"];
				$proceso = "CREACION REFERENCIA";
				$descripcion = "-";
				$referencia_creada = "SI";
				$referencia_heredada = "NO";
				$referencia_compatible = "NO";
				$error = "NO";
				$codigo_error = "OK!";

				$log->setValores($id_usuario,$proceso,$id_referencia,$nombre,$proveedor,$fabricante,$tipo_pieza,$nombre_pieza,$ref_fabricante_pieza,$ref_proveedor_pieza,
								$descripcion,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,
								$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades,NULL,$comentarios,$fecha_creado,$fecha_modificacion,$referencia_creada,
								$referencia_heredada,$referencia_compatible,$error,$codigo_error);

				$res_log = $log->guardarLog();
				if ($res_log == 0) echo '<script>alert("Se ha producido un error al guardar el log de la operación")</script>';
				header("Location: referencias.php?ref=creado");
			}
			else {
				$mensaje_error = $referencias->getErrorMessage($resultado);

				// Guardamos el log de la operación
				$id_referencia = $referencias->getUltimoID();
				$referencias->cargaDatosReferenciaId($id_referencia);
				$fecha_creado = $referencias->fecha_creado;

				$id_usuario = $_SESSION["AT_id_usuario"];
				$proceso = "CREACION REFERENCIA";
				$descripcion = "-";
				$referencia_creada = "SI";
				$referencia_heredada = "NO";
				$referencia_compatible = "NO";
				$error = "SI";
				$codigo_error = $mensaje_error;

				$log->setValores($id_usuario,$proceso,$id_referencia,$nombre,$proveedor,$fabricante,$tipo_pieza,$nombre_pieza,$ref_fabricante_pieza,$ref_proveedor_pieza,
						$descripcion,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,
						$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades,NULL,$comentarios,$fecha_creado,$fecha_modificacion,$referencia_creada,
						$referencia_heredada,$referencia_compatible,$error,$codigo_error);

				$res_log = $log->guardarLog();
				if ($res_log == 0) echo '<script>alert("Se ha producido un error al guardar el log de la operación")</script>';
			}
		}
	}
}
else {
	$nombre = "";
	$fabricante = "";
	$proveedor = "";
	$nombre_pieza = "";
	$tipo_pieza = "";
	$ref_proveedor_pieza = "";
	$ref_fabricante_pieza = "";
	$part_value_name = "";
	$part_value_qty = "";
	$pack_precio = "";
	$unidades = "";
}

$titulo_pagina = "Básico > Nueva referencia";
$pagina = "new_referencia";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/basicos/nueva_referencia.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3> Creaci&oacute;n de una nueva referencia </h3>
    <form id="FormularioCreacionBasico" name="crearReferencia" action="nueva_referencia.php" method="post" enctype="multipart/form-data">
    	<br />
        <h5> Rellene los siguientes campos para la creaci&oacute;n de una nueva referencia </h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Nombre *</div>
            <input type="text" id="nombre" name="nombre" class="CreacionBasicoInput" value="<?php echo $nombre;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Fabricante *</div>
           	<select id="fabricante" name="fabricante"  class="CreacionBasicoInput">
            	<?php
   					$nf->prepararConsulta();
   					$nf->realizarConsulta();
   					$resultado_fabricantes = $nf->fabricantes;

   					for($i=0;$i<count($resultado_fabricantes);$i++) {
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
            	<?php
   					$np->prepararConsulta();
   					$np->realizarConsulta();
   					$resultado_proveedores = $np->proveedores;

   					for($i=0;$i<count($resultado_proveedores);$i++) {
   						$datoProveedor = $resultado_proveedores[$i];
   						$prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]);
		                if($prov->nombre != "0 - SIN ESPECIFICAR"){
      						echo '<option value="'.$prov->id_proveedor.'"';if ($prov->id_proveedor == $proveedor) { echo 'selected="selected"'; } echo '>'.$prov->nombre.'</option>';
                  		}
      				}
      			?>
            </select>
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Nombre de pieza </div>
          	<input type="text" id="nombre_pieza" name="nombre_pieza" class="CreacionBasicoInput" value="<?php echo $nombre_pieza;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Tipo de pieza </div>
          	<input type="text" id="tipo_pieza" name="tipo_pieza" class="CreacionBasicoInput" value="<?php echo $tipo_pieza;?>"/>
            <div class="LabelCreacionBasicoPCRef">PC</div>
            <input type="checkbox" id="es_ordenador" name="es_ordenador" class="CreacionBasicoInputPCRef" onclick="rellenarTipoPieza()" />
         </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Ref. proveedor pieza *</div>
           	<input type="text" id="ref_proveedor_pieza" name="ref_proveedor_pieza" class="CreacionBasicoInput" value="<?php echo $ref_proveedor_pieza;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Ref. fabricante pieza *</div>
           	<input type="text" id="ref_fabricante_pieza" name="ref_fabricante_pieza" class="CreacionBasicoInput" value="<?php echo $ref_fabricante_pieza;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Nombre</div>
        <input type="text" id="part_value_name" name="part_value_name" class="CreacionBasicoInput" value="<?php echo $part_value_name;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Valor</div>
          	<input type="text" id="part_value_qty" name="part_value_qty" class="CreacionBasicoInput" value="<?php echo $part_value_qty;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Nombre 2</div>
        <input type="text" id="part_value_name_2" name="part_value_name_2" class="CreacionBasicoInput" value="<?php echo $part_value_name_2;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Valor 2</div>
          	<input type="text" id="part_value_qty_2" name="part_value_qty_2" class="CreacionBasicoInput" value="<?php echo $part_value_qty_2;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Nombre 3</div>
        <input type="text" id="part_value_name_3" name="part_value_name_3" class="CreacionBasicoInput" value="<?php echo $part_value_name_3;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Valor 3 </div>
          	<input type="text" id="part_value_qty_3" name="part_value_qty_3" class="CreacionBasicoInput" value="<?php echo $part_value_qty_3;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Nombre 4</div>
        <input type="text" id="part_value_name_4" name="part_value_name_4" class="CreacionBasicoInput" value="<?php echo $part_value_name_4;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Valor 4</div>
          	<input type="text" id="part_value_qty_4" name="part_value_qty_4" class="CreacionBasicoInput" value="<?php echo $part_value_qty_4;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Nombre 5</div>
        <input type="text" id="part_value_name_5" name="part_value_name_5" class="CreacionBasicoInput" value="<?php echo $part_value_name_5;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Esp. Valor 5</div>
          	<input type="text" id="part_value_qty_5" name="part_value_qty_5" class="CreacionBasicoInput" value="<?php echo $part_value_qty_5;?>"/>
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Precio Pack *</div>
            <input type="text" id="pack_precio" name="pack_precio" class="CreacionBasicoInput" value="<?php echo $pack_precio;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
          	<div class="LabelCreacionBasico">Unidades por paquete *</div>
           	<input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" value="<?php echo $unidades;?>" />
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Comentarios</div>
          	<textarea type="text" id="comentarios" name="comentarios" rows="10" class="textareaInput"><?php echo $comentarios; ?></textarea>
        </div>
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Archivos adjuntos</div>
            <div id="AñadirMasArchivos"><a href="#" onClick="addCampo()">Subir otro archivo</a></div>
        </div>
        <div id="adjuntos">
        	<div class="ContenedorCamposAdjuntar">
        		<input type="file" id="archivos[]" name="archivos[]" class="BotonAdjuntar"/>
        	</div>
        </div>

        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:history.back()"/>
            <input type="hidden" id="guardandoReferencia" name="guardandoReferencia" value="1"/>
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

<?php include ("../includes/footer.php"); ?>
