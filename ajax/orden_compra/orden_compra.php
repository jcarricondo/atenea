<?php
// Fichero con las funciones de comprobacion para AJAX de las OC
include("../../classes/mysql.class.php");
include("../../classes/basicos/referencia.class.php");
include("../../classes/basicos/proveedor.class.php");
include("../../classes/basicos/listado_proveedores.class.php");
include("../../classes/orden_produccion/orden_produccion.class.php");
include("../../classes/orden_compra/orden_compra.class.php");
include("../../classes/orden_compra/listado_ordenes_compra.class.php");
include("../../classes/sede/sede.class.php");

$db = new MySQL();
$ref = new Referencia();
$op = new Orden_Produccion();
$listadoOP = new listadoOrdenesCompra();
$listado_orden_compra = new listadoOrdenesCompra();
$prov = new Proveedor();
$np = new listadoProveedores();
$sede = new Sede();

if (isset($_GET["comp"])){
	switch($_GET["comp"]){
		case "cargaOpsPorSede":
        	$id_sede = $_GET["id_sede"];

        	$respuesta .= '<td>
								<div class="Label">Orden de Producción</div>
           						<select multiple="multiple" id="orden_produccion[]" name="orden_produccion[]" class="BuscadorOCEstadosOP" size="4">';
           		
			// Sacar el listado de todas las OP. 
			$listadoOP->prepararOP($id_sede);
			$listadoOP->realizarConsultaOP();
			$resultados_op = $listadoOP->orden_produccion; 
			
			// Si una OP tiene alias != NULL mostrar alias
			for($i=-1; $i<count($resultados_op); $i++){
				if($i == -1) $respuesta .= '<option value=""></option>';
				else{
					$op->cargaDatosProduccionId($resultados_op[$i]["id_produccion"]);	
					if ($op->alias_op != NULL){
						$respuesta .= '<option value="'.$op->id_produccion.'"';
						for($j=0;$j<count($_SESSION["orden_produccion_orden_compra"]);$j++){
							if ($_SESSION["orden_produccion_orden_compra"][$j] == $op->id_produccion) { $respuesta .= ' selected="selected"'; }
						}
						$respuesta .= '>'.$op->alias_op.'</option>';	
					}
					else {
						$respuesta .= '<option value="'.$op->id_produccion.'"';
						for($j=0;$j<count($_SESSION["orden_produccion_orden_compra"]);$j++){
							if ($_SESSION["orden_produccion_orden_compra"][$j] == $op->id_produccion) { $respuesta .= ' selected="selected"'; }
						}
						$respuesta .= '>'.$op->codigo.'</option>';	
					}
				}
			}

			$respuesta .= '</select>
							</td>
							<td>
        						<div class="Label">Proveedor</div>
        						<select multiple="multiple" id="proveedor[]" name="proveedor[]" class="BuscadorOCEstadosOP" size="4">';
       	 	
       	 	// Sacamos el listado de los proveedores
			$np->prepararConsulta();
			$np->realizarConsulta();
			$resultado_proveedores = $np->proveedores;

			for($i=-1;$i<count($resultado_proveedores);$i++) {
				if($i == -1) $respuesta .= '<option value=""></option>';
				else {
					$datoProveedor = $resultado_proveedores[$i];
					$prov->cargaDatosProveedorId($datoProveedor["id_proveedor"]);
					$respuesta .= '<option value="'.$prov->nombre.'"';
					for($j=0;$j<count($_SESSION["proveedor_orden_compra"]);$j++){
						if($_SESSION["proveedor_orden_compra"][$j] == $prov->nombre) { $respuesta .= ' selected="selected"'; }
					}
					$respuesta .= '>'.$prov->nombre.'</option>';	
				}
			}
			$respuesta .= '</select></td><td></td>';
			echo $respuesta;
        break;
        default:

        break;
	}
}
?>

