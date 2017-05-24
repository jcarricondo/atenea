<?php
// Ficha del material informático con la posibilidad de modificar su estado
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/sede/sede.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/material_informatico/material_informatico.class.php");
include("../classes/kint/Kint.class.php");
permiso(39);

$sede = new Sede();
$almacen = new Almacen();
$validacion = new Funciones();
$materialInformatico = new MaterialInformatico();

// Obtenemos el nombre del almacen 
$id_almacen = 22;
$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = $almacen->nombre;
$mod_num_serie = false;

if(isset($_POST["guardandoMaterial"]) and $_POST["guardandoMaterial"] == 1) {
    $id_material = $_POST["id_material"];
    $num_serie = $_POST["num_serie"];
    $id_tipo = $_POST["id_tipo"];
    $id_tipo_ant = $_POST["id_tipo_ant"];
    $id_subtipo = $_POST["id_subtipo"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $estado = $_POST["estado"];
    $asignado_a = $_POST["asignado_a"];
    $observaciones = $_POST["observaciones"];

    $res_material = $materialInformatico->dameTipoMaterial($id_tipo);
    $codigo = $res_material[0]["codigo"];

    if($asignado_a == "") $asignado_a = "-";
    if($observaciones == "") $observaciones = "-";

    if($id_tipo != $id_tipo_ant){
        // Modificamos el número de serie
        $array_num_serie = explode("-", $num_serie);
        $digitos = $array_num_serie[1];
        $num_serie = $codigo.'-'.$digitos;
        $mod_num_serie = true;
    }

    $materialInformatico->datosMaterial($id_material,$id_tipo,$id_subtipo,$num_serie,$descripcion,$id_almacen,$precio,$asignado_a,$estado,$observaciones,$fecha_creado,$activo);
    $resultado = $materialInformatico->guardaCambios(); 
    if($resultado == 1){  
        if($mod_num_serie){
            header("Location: listado_informatica.php?matInf=modificado&realizandoBusqueda=1&num_serie=".$num_serie."&tipo_material=".$id_tipo."&subtipo_material=".$id_subtipo);
        }
        else {
            header("Location: listado_informatica.php?matInf=modificado&realizandoBusqueda=1&tipo_material=".$id_tipo."&subtipo_material=".$id_subtipo);
        }
    }
    else {
        $mensaje_error = $materialInformatico->getErrorMessage($resultado);
    }
}


$id_material = $_GET["id_material"];

// Cargamos los datos del Material Informático
$materialInformatico->cargaDatosMaterialId($id_material);
$id_tipo = $materialInformatico->id_tipo;
$id_subtipo = $materialInformatico->id_subtipo;
$num_serie = $materialInformatico->num_serie;
$descripcion = $materialInformatico->descripcion;
$precio = $materialInformatico->precio;
$estado = $materialInformatico->estado;
$asignado_a = $materialInformatico->asignado_a;
$observaciones = $materialInformatico->observaciones;

$titulo_pagina = "Material Informático > Modificación Material";
$pagina = "mod_material";
include("../includes/header.php");
echo '<script type="text/javascript" src="../js/funciones_24052017_1515.js"></script>';
echo '<script type="text/javascript" src="../js/material_informatico/material_informatico.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_material_informatico.php"); ?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div>

    <h3>Modificaci&oacute;n del Material Inform&aacute;tico</h3>

    <form id="FormularioCreacionBasico" name="modificacionMaterial" action="mod_material.php" method="post">
    <br/>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Almacen</div>
        <label id="nombre_almacen" class="LabelInfoOP"><?php echo $nombre_almacen;?></label>
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Num. serie</div>
        <label id="label_num_serie" class="LabelInfoOP"><?php echo $num_serie;?></label>
        <input type="hidden" id="num_serie" name="num_serie" value="<?php echo $num_serie; ?>" />
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
                <option <?php if($id_tipo_material == $id_tipo) { ?> selected="selected" <?php } ?> value="<?php echo $id_tipo_material;?>"><?php echo $codigo.' - '.utf8_encode($nombre);?></option> 
        <?php     
            }
        ?>
        </select>
        <input type="hidden" id="id_tipo_ant" name="id_tipo_ant" value="<?php echo $id_tipo; ?>">
    </div>  
    <div id="capa_subtipo" class="ContenedorCamposCreacionBasico">
        <?php
            // Obtenemos el subtipo según el tipo de material
            $res_subtipos = $materialInformatico->dameSubtiposSegunTipo($id_tipo);
            if(!empty($res_subtipos)){ ?>
                <div class="LabelCreacionBasico">Subtipo *</div>
                <select id="id_subtipo" name="id_subtipo" class="CreacionBasicoInput">
                <?php
                    for($i=0;$i<count($res_subtipos);$i++) {
                        $id_subtipo_material = $res_subtipos[$i]["id_subtipo"];
                        $nombre = $res_subtipos[$i]["subtipo"]; ?>
                        <option <?php if ($id_subtipo_material == $id_subtipo) { ?> selected="selected" <?php } ?> value="<?php echo $id_subtipo_material; ?>"><?php echo utf8_encode($nombre);?></option>
                <?php
                    }
                ?>
                    <option value="0" <?php if ($id_subtipo == "0") { ?> selected="selected" <?php } ?>>OTRO</option>
                </select>
        <?php
            }
        ?>
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
        <?php 
            switch ($estado) {
                case 'STOCK': ?>
                    <option value="STOCK" selected="selected">STOCK</option>
                    <option value="AVERIADO">AVERIADO</option>
                    <option value="EN USO">EN USO</option>
        <?php 
                break;
                case 'AVERIADO': ?>
                    <option value="AVERIADO" selected="selected">AVERIADO</option>
                    <option value="EN REPARACION">EN REPARACI&Oacute;N</option>
        <?php 
                break; 
                case 'EN REPARACION': ?>
                    <option value="EN REPARACION" selected="selected">EN REPARACI&Oacute;N</option>
                    <option value="STOCK">STOCK</option>
        <?php 
                break;
                case 'EN USO': ?>
                    <option value="EN USO" selected="selected">EN USO</option>
                    <option value="STOCK">STOCK</option>
                    <option value="AVERIADO">AVERIADO</option>
        <?php 
                break;
            }
        ?>
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
    <br/>
    <br/>
    <div class="ContenedorBotonCreacionBasico">
        <input type="button" id="volver" name="volver" value="VOLVER" class="BotonEliminar" onclick="javascript:history.back()"/>
        <input type="hidden" id="guardandoMaterial" name="guardandoMaterial" value="1"/>
        <input type="hidden" id="id_material" name="id_material" value="<?php echo $id_material;?>" />
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
