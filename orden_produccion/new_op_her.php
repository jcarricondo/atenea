<?php
// Primer paso para la creación de una Orden de Producción 
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/control_usuario.class.php");
include("../classes/sede/sede.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/listado_perifericos.class.php");
include("../classes/basicos/listado_nombre_producto.class.php");
include("../classes/almacen/almacen.class.php");
permiso(9);

$control_usuario = new Control_Usuario();
$sede_class = new Sede();
$nombre_prod = new Nombre_Producto();
$per = new Periferico();
$listado_per = new listadoPerifericos();
$nom_prods = new listadoNombreProducto();
$almacen = new Almacen();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];

$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdminGes = $control_usuario->esAdministradorGes($id_tipo_usuario);

if(isset($_POST["guardandoOP"]) and $_POST["guardandoOP"] == 1) {
	// Se reciben los datos
	$alias_op = $_POST["alias_op"];
	$unidades = $_POST["unidades"];
	$producto = $_POST["producto"];
	$perifericos = $_POST["perifericos"];
	$ref_libres = $_POST["REFS"];
	$piezas = $_POST["piezas"];
	$fecha_inicio_construccion = $_POST["fecha_inicio_construccion"];
	$sede = $_POST["sede"];
}
else {
	$alias_op = "";
	$unidades = "";
	$producto = "";
	$perifericos = "";
	$ref_libres = "";
	$fecha_inicio_construccion = "";
	$sede = "";
	$Campos_no_rellenados = false;
}
$titulo_pagina = "Órdenes de Producción > Nueva Orden de Producción";
$pagina = "new_orden_produccion";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/funciones.js"></script>';
echo '<script type="text/javascript" src="../js/orden_produccion/new_op_03042017_1230.js"></script>';
?>

<div class="separador"></div>
<?php include("../includes/menu_op.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>
      
    <h3> Creación de una nueva orden de producción </h3>
    <form id="FormularioCreacionBasico" name="crearOrdenProduccion" onsubmit="return validarFormulario()" action="confirm_new_op_her.php" method="post">
	<br />
    <h5> Rellene los siguientes campos para la creación de una nueva orden de producción </h5>
    <?php
        if($esAdminGlobal || $esAdminGes){
    	    // ADMINISTRADOR SIMUMAK. Elige la sede de la OP
            $res_sedes = $sede_class->dameSedesFabrica(); ?>
            <div class="ContenedorCamposCreacionBasico">
		        <div class="LabelCreacionBasico">Sede</div>
		        <select id="sede" name="sede"  class="CreacionBasicoInput">
                <?php
                    for($i=0;$i<count($res_sedes);$i++) {
                        $id_sede_bus = $res_sedes[$i]["id_sede"];
                        $nombre_sede = $res_sedes[$i]["sede"]; ?>
                        <option value="<?php echo $id_sede_bus; ?>"><?php echo $nombre_sede; ?></option>
                <?php
                    }
                ?>
				</select>
		    </div>
	<?php
	    }
		else {
		    // Obtenemos la sede a la que pertenece el usuario
			$id_sede = $almacen->dameSedeAlmacen($id_almacen);
			$id_sede = $id_sede["id_sede"]; ?>
			<input type="hidden" id="sede" name="sede" value="<?php echo $id_sede;?>"/>
	<?php
	    }
	?>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Alias</div>
        <input type="text" id="alias_op" name="alias_op" class="CreacionBasicoInput" value="<?php echo $alias_op;?>" onblur="comprobarAliasCorrecto()"/>
        <div id="alias_correcto">
            <input type="hidden" id="alias_validado" name="alias_validado" value="-1" />
        </div>
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Unidades *</div>
        <input type="text" id="unidades" name="unidades" class="CreacionBasicoInput" value="<?php echo $unidades;?>" onkeypress="return soloNumeros(event)" />
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Fecha Inicio Construcci&oacute;n</div>
        <input type="text" id="fecha_inicio_construccion" class="fechaCal" name="fecha_inicio_construccion" readonly="readonly" value="<?php echo $fecha_inicio_construccion;?>"  />
    </div>
    <div class="ContenedorCamposCreacionBasico">
        <div class="LabelCreacionBasico">Producto *</div>
        <select id="producto" name="producto"  class="CreacionBasicoInput" onchange="cargaPlantillasProducto(this.value)">
            <option value="0">Seleccione un nombre de producto</option>
            <?php
			    $nom_prods->prepararConsulta();
				$nom_prods->realizarConsulta();
				$resultado_nombres_producto = $nom_prods->nombre_productos;

				for($i=0;$i<count($resultado_nombres_producto);$i++) {
				    $datoNombreProducto = $resultado_nombres_producto[$i];
					$nombre_prod->cargaDatosNombreProductoId($datoNombreProducto["id_nombre_producto"]);
					echo '<option value="'.$nombre_prod->id_nombre_producto.'">'.$nombre_prod->nombre.'_'.$nombre_prod->version.'</option>';
				}
			?>
        </select>
    </div>
    <div id="CapaPlantillaProducto" class="ContenedorCamposCreacionBasico" style="display: none;">
        <div id="PlantillaProducto" style="display: none;">

        </div>
    </div>
    <div id="CapaContenedorComponentes" style="display: block;">
        <?php include("new_op_add_perifericos.php") ?>

        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Referencias Libres </div>
            <div class="CajaReferencias">
                <div id="CapaTablaIframe">
                    <table id="mitablaRefsLibres">
                    <tr>
                        <th style="text-align:center">ID</th>
                        <th>NOMBRE</th>
                        <th>PROVEEDOR</th>
                        <th>REF. PROVEEDOR</th>
                        <th>NOMBRE PIEZA</th>
                        <th style="text-align:center">PIEZAS</th>
                        <th style="text-align:center">PACK PRECIO</th>
                        <th style="text-align:center">UDS/P</th>
                        <th style="text-align:center">TOTAL PAQS</th>
                        <th style="text-align:center">PRECIO UNIDAD</th>
                        <th style="text-align:center">PRECIO</th>
                        <th style="text-align:center">ELIMINAR</th>
                    </tr>
                    </table>
                </div>
            </div>
            <?php
                // Hay que hacer un seguimiento de las filas para cuando se añadan referencias. Si se modifica el campo piezas de una referencia
                // añadida, necesitaremos saber que fila de la tabla se esta modificando. Al principio el numero de filas es cero
                $fila = 0;
            ?>
            <input type="hidden" name="fila" id="fila" value="<?php echo $fila;?>"/>
            <input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="Abrir_ventana('buscador_referencias_libres.php')"/>
            <input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="removeRow(mitablaRefsLibres)"  />
        </div>
        <br/>

        <div class="ContenedorCamposCreacionBasico">
            <div class="LabelCreacionBasico">Coste Referencias Libres </div>
            <label id="precio_refs_libres" class="LabelPrecio"><?php echo number_format(0.00, 2, ',', '.').'€';?></label>
        </div>
    </div>
        
    <div class="ContenedorBotonCreacionBasico">
        <input type="button" id="volver" name="volver" value="Volver" onclick="window.history.back()"/>
        <input type="hidden" id="guardandoOP" name="guardandoOP" value="1"/>
        <input type="submit" id="continuar" name="continuar" value="Continuar" />
    </div>
    <div class="mensajeCamposObligatorios">* Campos obligatorios</div>
	<?php
        if($mensaje_error != "") echo '<div class="mensaje_error">'.$mensaje_error.'</div>';
	?>
    <br />
    </form>
</div>    
<?php include ("../includes/footer.php");?>