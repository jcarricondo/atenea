<?php
// Este fichero crea una nuevo material informático
include("../includes/sesion.php");
include("../classes/sede/sede.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/material_informatico/material_informatico.class.php");
permiso(39);

$sede = new Sede();
$almacen = new Almacen();
$validacion = new Funciones();
$materialInformatico = new MaterialInformatico();

// Obtenemos el nombre del almacen 
$id_almacen = 22;
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = $almacen->nombre;
$unidades = 1;

if(isset($_POST["guardandoMaterial"]) and $_POST["guardandoMaterial"] == 1) {
    // Se reciben los datos
    $id_tipo = $_POST["id_tipo"];
    $id_subtipo = $_POST["id_subtipo"];
    $descripcion = $_POST["descripcion"];
    $estado = $_POST["estado"];
    $precio = $_POST["precio"];
    $asignado_a = $_POST["asignado_a"];
    $observaciones = $_POST["observaciones"];
    $unidades = $_POST["unidades"];
    
    if($descripcion == ''){
  		echo '<script type="text/javascript">alert("Rellene los campos obligatorios")</script>';
  	}
    else if($unidades == 0 || empty($unidades)){
        echo '<script type="text/javascript">alert("El numero de unidades tiene que ser mayor que 0")</script>';
    }
    else {
        $i=0;
        $error = false;
        while($i<$unidades && !$error){ 
            // Generamos el número de serie [CODIGO_TIPO][-][NNNNNNN] [012-3456789]
            $res_tipo = $materialInformatico->dameTipoMaterial($id_tipo);
            $codigo_tipo = $res_tipo[0]["codigo"]; 
            $num_serie = $materialInformatico->generaNumSerie($codigo_tipo);
            if($asignado_a == "") $asignado_a = "-";
            if($observaciones == "") $observaciones = "-";
            if($precio == "") $precio = 0;
            if($id_subtipo == "") $id_subtipo = 0;

            $materialInformatico->datosMaterial($id_material,$id_tipo,$id_subtipo,$num_serie,$descripcion,$id_almacen,$precio,$asignado_a,$estado,$observaciones,$fecha_creado,$activo);
            $resultado = $materialInformatico->guardaCambios();
            $error = $resultado != 1;
            $i++;
        }
        if(!$error){
            $fecha_hoy = date('Y-m-d');
            $fecha_hoy = $validacion->cFechaNormal($fecha_hoy);
            header("Location: listado_informatica.php?matInf=creado&realizandoBusqueda=1&unidades=".$unidades."&tipo_material=".$id_tipo."&subtipo_material=".$id_subtipo."&fecha_desde=".$fecha_hoy."&fecha_hasta=".$fecha_hoy);
        }
        else {
          // ERROR 
          $mensaje_error = $materialInformatico->getErrorMessage($resultado);
        }
	}
}
else {
	 $id_tipo = "";
     $id_subtipo = "";
	 $descripcion = "-";
	 $precio = "";
}

$titulo_pagina = "Material Informático > Nuevo material";
$pagina = "nuevo_material";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/funciones_24052017_1515.js"></script>';
echo '<script type="text/javascript" src="../js/material_informatico/material_informatico.js"></script>'; 
?>

<div class="separador"></div>
<?php include("../includes/menu_material_informatico.php");?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
      <?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Creaci&oacute;n de un nuevo material inform&aacute;tico</h3>
    <form id="FormularioCreacionBasico" name="crearMaterialInformatico" action="nuevo_material.php" method="post" enctype="multipart/form-data">
 	  <br/>
    <h5>Rellene los siguientes campos para la creaci&oacute;n de una nuevo material inform&aacute;tico</h5>
  	<div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Almacen</div>
        <label id="nombre_almacen" class="LabelInfoOP"><?php echo $nombre_almacen;?></label>
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Tipo *</div>
        <select id="id_tipo" name="id_tipo" class="CreacionBasicoInput" onchange="cargaSubtipo(this.value);">
        <?php 
            // Obtenemos los tipos de materiales informáticos
            $res_materiales = $materialInformatico->dameTiposMateriales();
            for($i=0;$i<count($res_materiales);$i++){
              $id_tipo_material = $res_materiales[$i]["id_tipo"];
              $nombre = $res_materiales[$i]["nombre"];
              $codigo = $res_materiales[$i]["codigo"]; ?>
              <option value="<?php echo $id_tipo_material;?>"><?php echo $codigo.' - '.utf8_encode($nombre);?></option> 
        <?php     
            }
        ?>
        </select>
    </div>  
    <div id="capa_subtipo" class="ContenedorCamposCreacionBasico" style="display:auto;">

    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Unidades *</div>
        <input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" style="width:50px;" value="<?php echo $unidades;?>" onkeypress="return soloNumeros(event);" />
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Descripci&oacute;n *</div>
        <textarea type="text" id="descripcion" name="descripcion" rows="10" class="textareaInput" style="resize: none;"><?php echo $descripcion;?></textarea>
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Precio *</div>
        <input type="text" id="precio" name="precio" class="CreacionBasicoInput" value="<?php echo $precio;?>" onkeypress="return blockNonNumbers(this, event, true, true);" />
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Estado *</div>
        <select id="estado" name="estado" class="CreacionBasicoInput">
            <option value="STOCK">STOCK</option>
            <option value="AVERIADO">AVERIADO</option>
            <option value="EN REPARACION">EN REPARACI&Oacute;N</option>
            <option value="EN USO">EN USO</option>
        </select>
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Asignado a</div>
        <input type="text" id="asignado_a" name="asignado_a" class="CreacionBasicoInput" value="<?php echo $asignado_a;?>"/>
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Observaciones</div>
        <textarea type="text" id="observaciones" name="observaciones" rows="10" class="textareaInput" style="resize: none;"><?php echo $observaciones;?></textarea>
    </div>
    <br/>
    <div class="ContenedorBotonCreacionBasico">
        <input type="button" id="volver" name="volver" value="VOLVER" class="BotonEliminar" onclick="javascript:history.back()"/>
        <input type="hidden" id="guardandoMaterial" name="guardandoMaterial" value="1"/>
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
    <br/> 
    </form>
</div>
<?php include ("../includes/footer.php"); ?>
