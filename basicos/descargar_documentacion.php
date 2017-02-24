<?php 
// Este fichero permite la opcion de descargar la documentación de perifericos o plantillas
include("../includes/sesion.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/plantilla_producto.class.php");

include("../classes/kint/Kint.class.php");

permiso(2);

$prov = new Proveedor();
$ref = new Referencia();
$per = new Periferico();
$plant = new Plantilla_Producto();

$op = $_GET["op"];
$id = $_GET["id"];

if($op == "PER") {
	$comp = "Periférico";
	$per->cargaDatosPerifericoId($id);
	$ver_comp = $per->version;
	$nombre_comp = $per->periferico."_v".$ver_comp;
}
else if($op == "PLANT") {
	$comp = "Plantilla de Producto";
	$plant->cargaDatosPlantillaProductoId($id);
	$nombre_comp = $plant->nombre;
}
else $comp = "ERROR!";

if(isset($_POST["generarDocumentacion"]) and $_POST["generarDocumentacion"] == 1){
	// Obtenemos los datos del formulario
	$op = $_POST["op"];
	$id = $_POST["id"];
	$id_proveedor = $_POST["proveedor"];

	d($op);
	d($id);
	d($id_proveedor);


} 
else {

}

$titulo_pagina = "Básicos > Descarga de documentación";
$pagina = "descargar_documentacion";
include ("../includes/header.php");	
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
 	</div>
    
    <h3>Descarga de documentaci&oacute;n</h3>
    
    <form id="FormularioCreacionBasico" name="descargarDocumentacion" action="descargar_documentacion.php" method="post" >
    	<br />
        <h5>Seleccione el proveedor para preparar la descarga de documentaci&oacute;n</h5>
    	<div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico"><?php echo $comp;?></div>
			<label id="nombre_componente" class="LabelPrecio"><?php echo $nombre_comp;?></label>
        </div>
        <div class="ContenedorCamposCreacionBasico">
        	<div class="LabelCreacionBasico">Proveedor *</div>
            <select id="proveedor" name="proveedor" class="CreacionBasicoInput">
				<option value="0">TODOS</option>
			<?php
				// Cargamos todos los proveedores activos
				$res_proveedores = $prov->dameProveedores();
				for($i=0;$i<count($res_proveedores);$i++){
					$id_proveedor = $res_proveedores[$i]["id_proveedor"];
					$nombre_proveedor = $res_proveedores[$i]["nombre_prov"]; ?>
					<option value="<?php echo $id_proveedor;?>"><?php echo $nombre_proveedor;?></option>
			<?php
				}
			?>
            </select>
        </div>
        <div class="ContenedorBotonCreacionBasico">
          	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()" /> 
            <input type="hidden" id="generarDocumentacion" name="generarDocumentacion" value="1" />
			<input type="hidden" id="op" name="op" value="<?php echo $op; ?>" />
			<input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />
            <input type="submit" id="descargar" name="descargar" value="Descargar" />
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
<?php include ("../includes/footer.php");?>