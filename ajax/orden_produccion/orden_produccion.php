<?php
// Fichero con las funciones de comprobacion para AJAX de las OP
include("../../classes/mysql.class.php");
include("../../classes/sede/sede.class.php");
include("../../classes/basicos/plantilla_producto.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/taller/taller.class.php");

$db = new MySQL();
$op = new Orden_Produccion();
$taller = new Taller();
$sede = new Sede();
$plant = new Plantilla_Producto();

if(isset($_GET["comp"])){
	switch($_GET["comp"]){
		// CARGA LA REFERENCIA DESDE ENTRADA/SALIDA DE MATERIAL DE TALLER
		case "cargaAlias":
			$id_sede = $_GET["id_sede"];
			$res_alias = $sede->dameAliasOPSede($id_sede);

			$select_alias .= '<select id="alias_op" name="alias_op" class="BuscadorInput"><option></option>';
	     	for($i=0;$i<count($res_alias);$i++) { 
				$select_alias .= '<option value="'.$res_alias[$i]["alias"].'">'.$res_alias[$i]["alias"].'</option>';
			}
			$select_alias .= '</select>';
			echo $select_alias;
            break;
        // CARGA LAS PLANTILLAS EN FUNCION DE UN NOMBRE DE PRODUCTO
        case "carga_plantillas" :
            $id_nombre_producto = $_GET["id_nombre_producto"];
            $res_plantillas = $plant->damePlantillasNombreProducto($id_nombre_producto);

            if($res_plantillas != NULL){
                $salida = '<div id="PlantillaProducto" style="display: block;">
                                <div class="LabelCreacionBasico">Plantilla *</div>
                                <select id="select_plantilla" name="select_plantilla" class="CreacionBasicoInput" >';
                for($i=0;$i<count($res_plantillas);$i++){
                    $id_plantilla = $res_plantillas[$i]["id_plantilla"];
                    $plant->cargaDatosPlantillaProductoId($id_plantilla);
                    $nombre_plantilla = $plant->nombre;
                    $salida .= '<option value="'.$id_plantilla.'">'.$nombre_plantilla.'</option>';
                }
                $salida .= '</select></div>';
            }
            else {
                $salida=0;
            }
            echo $salida;
            break;
	}
}
?>

