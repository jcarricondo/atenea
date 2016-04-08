<?php 
//Este fichero genera un excel con los materiales informáticos del almacen
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/material_informatico/material_informatico.class.php");
include("../classes/material_informatico/listado_material_informatico.class.php");

$funciones = new Funciones();
$almacen = new Almacen();
$materialInformatico = new MaterialInformatico();
$listadoMaterialInformatico = new ListadoMaterialInformatico();

$id_tipo = $_SESSION["tipo_material_xls_material_informatico"];
$id_subtipo = $_SESSION["subtipo_material_xls_material_informatico"];
$num_serie = $_SESSION["num_serie_xls_material_informatico"];
$descripcion = $_SESSION["descripcion_xls_material_informatico"];
$precio = $_SESSION["precio_xls_material_informatico"];
$asignado_a = $_SESSION["asignado_a_xls_material_informatico"];
$estado = $_SESSION["estado_xls_material_informatico"];
$observaciones = $_SESSION["observaciones_xls_material_informatico"];
$fecha_desde = $_SESSION["fecha_desde_xls_material_informatico"];
$fecha_hasta = $_SESSION["fecha_hasta_xls_material_informatico"];

$listadoMaterialInformatico->setValores($id_tipo,$id_subtipo,$num_serie,$descripcion,$precio,$asignado_a,$estado,$observaciones,$fecha_desde,$fecha_hasta,$paginacion);
$listadoMaterialInformatico->realizarConsulta();
$resultadosBusqueda = $listadoMaterialInformatico->materiales_informaticos;

$table .= '<table>
		<tr>
            <th style="text-align:center;">NUM. SERIE</th>
            <th style="text-align:left;">TIPO MATERIAL</th>
            <th style="text-align:left;">SUBTIPO</th>
            <th style="text-align:left;">DESCRIPCI&Oacute;N</th>
            <th style="text-align:right;">PRECIO</th>
            <th style="text-align:left;">ASIGNADO</th>
            <th style="text-align:left;">ESTADO</th>
            <th style="text-align:left;">OBSERVACIONES</th>
            <th style="text-align:left;">F. CREADO</th>
        </tr>';

// Se cargan los datos de los materiales de la busqueda según su identificador
for($i=0;$i<count($resultadosBusqueda);$i++) {
    $id_material = $resultadosBusqueda[$i]["id_material"];

    $materialInformatico->cargaDatosMaterialId($id_material);
    $num_serie = $materialInformatico->num_serie;
    $id_tipo = $materialInformatico->id_tipo;
    $id_subtipo = $materialInformatico->id_subtipo;
    $descripcion = $materialInformatico->descripcion;
    $precio = $materialInformatico->precio;
    $asignado_a = $materialInformatico->asignado_a;
    $estado = $materialInformatico->estado;
    $observaciones = $materialInformatico->observaciones;
    $fecha_creacion = $funciones->cFechaNormal($materialInformatico->fecha_creado);

    $res_tipo_material = $materialInformatico->dameTipoMaterial($id_tipo);
    $nombre_tipo_material = utf8_encode($res_tipo_material[0]["nombre"]);
    $res_subtipo_material = $materialInformatico->dameSubtipoMaterial($id_subtipo);
    $nombre_subtipo_material = utf8_encode($res_subtipo_material[0]["subtipo"]);
    if(empty($nombre_subtipo_material)) $nombre_subtipo_material = "-";

    if(!empty($unidades_creadas) && $unidades_creadas != 0 && $dif==0) $color = "green";
    else $color = "black";

    if($precio > 200){
        $color_precio = "peru";
    }
    else $color_precio = "black";

    $salida .= '<tr>
                    <td style="text-align:center;">'.$num_serie.'</td>
                    <td style="">'.$nombre_tipo_material.'</td>
                    <td style="">'.$nombre_subtipo_material.'</td>
                    <td style="">'.utf8_decode($descripcion).'</td>
                    <td style="text-align:right;">'.$precio.'</td>
                    <td style="">'.utf8_decode($asignado_a).'</td>                    
                    <td style="">'.$estado.'</td>
                    <td style="">'.utf8_decode($observaciones).'</td>
                    <td style="text-align:left; color:'.$color.'">'.$fecha_creacion.'</td>
    	        </tr>';
}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeMateriales.xls");
echo $table.$salida.$table_end; 
?>
