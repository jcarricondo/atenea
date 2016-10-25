<?php
// Primer paso para la creación de una Orden de Producción de Mantenimiento 
// Carga de clases y funciones JavaScript
include("../includes/sesion.php");
include("../classes/almacen/almacen.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");
permiso(15);

$almacen = new Almacen();
$control_usuario = new Control_Usuario();

// Obtenemos el tipo de usuario para conocer su sede
$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];

if(isset($_POST["guardandoOP"]) and $_POST["guardandoOP"] == 1) {
	// Se reciben los datos
	$alias_op = $_POST["alias_op"];
	$ref_libres = $_POST["REFS"];
	$piezas = $_POST["piezas"];
}
else {
	$alias_op = "";
	$ref_libres = "";
	$Campos_no_rellenados = false;
}

$titulo_pagina = "Orden Compra > Nueva Orden de Producción para Mantenimiento";
$pagina = "new_orden_produccion_mantenimiento";
include ('../includes/header.php');
echo '<script type="text/javascript" src="../js/orden_compra/nueva_op_mantenimiento.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_oc.php");?> 

<div id="ContenedorCentral">
	<div id="ContenedorSidebar"><?php include ("../includes/sidebar.php"); ?></div>
      
    <h3>Creación de una nueva orden de producción para mantenimiento</h3>
    <form id="FormularioCreacionBasico" name="crearOrdenProduccion" onsubmit="return validarFormulario()" action="confirm_nueva_op_mantenimiento.php" method="post">
		<br />
        <h5>Rellene los siguientes campos para la creación de una nueva orden de producción para mantenimiento</h5>
        <?php 
            if($id_tipo_usuario == 1 || $id_tipo_usuario == 8 || $id_tipo_usuario == 9){
                // ADMINISTRADOR GLOBAL. Elige la sede de la OP ?>
                <div class="ContenedorCamposCreacionBasico">
                    <div class="LabelCreacionBasico">Sede</div>
                    <select id="sede" name="sede"  class="CreacionBasicoInput">
                        <option value="1">SIMUMAK</option>';    
                        <option value="2">TORO</option>';       
                    </select>
                </div>
        <?php
            }
            else { 
                // Obtenemos la sede a la que pertenece el usuario 
                $id_sede = $almacen->dameSedeAlmacen($id_almacen);
                $id_sede = $id_sede["id_sede"];?>
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
           	<div class="LabelCreacionBasico">Referencias Libres </div>
           	<div class="CajaReferenciasMantenimiento">
            	<div id="CapaTablaIframe">
    				<table id="mitablaRefsLibres">
        			<tr>
                        <th style="text-align:center">ID REF</th>
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
            <input type="button" id="mas" name="mas" class="BotonMas"  value="+" onclick="javascript:Abrir_ventana('../orden_produccion/buscador_referencias_libres.php')"/> 																																																																																																																																			
           	<input type="button" id="menos" name="menos" class="BotonMenos" value="-" onclick="javascript:removeRow(mitablaRefsLibres)"  /> 
        </div>
        <br/>
        
        <div class="ContenedorCamposCreacionBasico">
           	<div class="LabelCreacionBasico">Coste Referencias Libres </div>
            <label id="precio_refs_libres" class="LabelPrecio"><?php echo number_format(0.00, 2, ',', '.').'€';?></label>
        </div>
        
        <div class="ContenedorBotonCreacionBasico">
           	<input type="button" id="volver" name="volver" value="Volver" onclick="javascript:window.history.back()"/> 
            <input type="hidden" id="guardandoOP" name="guardandoOP" value="1"/>            
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