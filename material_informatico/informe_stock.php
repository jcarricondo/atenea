<?php 
//Este fichero genera un excel con los materiales informáticos del stock
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/material_informatico.class.php");
include("../classes/material_informatico/listado_stock_informatico.class.php");

$almacen = new Almacen();
$materialInformatico = new MaterialInformatico();
$listadoStockInformatico = new ListadoStockInformatico();

$id_tipo = $_SESSION["tipo_material_xls_stock_informatico"];
$id_subtipo = $_SESSION["subtipo_material_xls_stock_informatico"];
$unidades = $_SESSION["unidades_xls_stock_informatico"];
$min_unidades = $_SESSION["min_unidades_xls_stock_informatico"];
$unidades_pedido = $_SESSION["unidades_pedido_xls_stock_informatico"];

// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos sin paginación
$listadoStockInformatico->setValores($id_tipo,$id_subtipo,$unidades,$min_unidades,$unidades_pedido,'');
$listadoStockInformatico->realizarConsulta();
$resultadosBusqueda = $listadoStockInformatico->materiales_informaticos;

$table .= '<table>
		<tr>
	    	<th style="text-align:center;">CODIGO</th>
            <th style="text-align:left;">TIPO</th>
            <th style="text-align:left;">SUBTIPO</th>
            <th style="text-align:center;">UNID. STOCK</th>
            <th style="text-align:center;">MIN. UNID</th>
            <th style="text-align:center;">UNID. PEDIDO</th>
            <th style="text-align:center;"></th>
        </tr>';

// Se cargan los datos de los materiales de la busqueda según su identificador
for($i=0;$i<count($resultadosBusqueda);$i++) {
    $id_material = $resultadosBusqueda[$i]["id_material"];
    $unidades_stock = $resultadosBusqueda[$i]["unidades_stock"];

    $materialInformatico->cargaDatosMaterialId($id_material);
    $id_tipo = $materialInformatico->id_tipo;
    $id_subtipo = $materialInformatico->id_subtipo;

    $res_tipo_material = $materialInformatico->dameTipoMaterial($id_tipo);
    $nombre_tipo_material = utf8_encode($res_tipo_material[0]["nombre"]);
    $codigo_tipo_material = $res_tipo_material[0]["codigo"];

    $res_subtipo_material = $materialInformatico->dameSubtipoMaterial($id_subtipo);
    $nombre_subtipo_material = utf8_encode($res_subtipo_material[0]["subtipo"]);
    $min_unidades = $res_subtipo_material[0]["min_unidades"];

    // Calculamos las unidades de un subtipo de material en STOCK
    // $unidades_stock = $materialInformatico->dameUnidadesStock($id_tipo,$id_subtipo);
    $unidades_pedido = $min_unidades - $unidades_stock;
    if($unidades_pedido > 0)  {
        $color_unid_pedido = "red";
    }
    else {
        $color_unid_pedido = "black";
        $unidades_pedido = "-";
    }

    // Establecemos valores predeterminados
    if(empty($min_unidades)) $min_unidades = 1;
    if(empty($nombre_subtipo_material)) $nombre_subtipo_material = "-";

    $salida .= '<tr>
                    <td style="text-align:center;">'.$codigo_tipo_material.'</td>
                    <td style="text-align:left;">'.$nombre_tipo_material.'</td>
                    <td style="text-align:left;">'.$nombre_subtipo_material.'</td>
                    <td style="text-align:center; font-weight: bold;">'.$unidades_stock.'</td>
                    <td style="text-align:center; font-weight: bold;">'.$min_unidades.'</td>
                    <td style="text-align:center; font-weight: bold; color:'.$color_unid_pedido.'">'.$unidades_pedido.'</td>
                    <td style="text-align:center;"></td>
    	        </tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeMateriales.xls");
echo $table.$salida.$table_end; 
?>
