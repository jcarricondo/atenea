<?php
// Este fichero muestra los componentes de una plantilla de producto
include("../classes/mysql.class.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/plantilla_producto.class.php");
include("../classes/basicos/nombre_producto.class.php");
include("../classes/basicos/componente.class.php");

$funciones = new Funciones();
$plant = new Plantilla_Producto();
$np = new Nombre_Producto();
$comp = new Componente();

$id_plantilla = $_GET["id_plantilla"];
$plant->cargaDatosPlantillaProductoId($id_plantilla);
$nombre_plantilla = strtoupper($plant->nombre);
$id_nombre_producto = $plant->id_nombre_producto;
$np->cargaDatosNombreProductoId($id_nombre_producto);
$nombre_producto = strtoupper($np->nombre);

$res_componentes = $plant->dameComponentesPlantillaProducto($id_plantilla);
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1>Componentes asociados a la plantilla: <?php echo $nombre_plantilla;?></h1>
    <h2>Plantilla vinculada al nombre de producto: <?php echo $nombre_producto;?></h2>
	<div id="CapaTablaReferencias">
    	<table>
            <tr>
                <th style="text-align: center;">ID. COMPONENTE</th>
                <th>COMPONENTE</th>
                <th>TIPO COMPONENTE</th>
                <th style="text-align: center;">VERSI&Oacute;N</th>
                <th style="text-align: center;">FECHA CREACI&Oacute;N</th>
            </tr>
            <?php
                for($i=0;$i<count($res_componentes);$i++) {
                    $id_componente = $res_componentes[$i]["id_componente"];
                    $id_tipo_componente = $res_componentes[$i]["id_tipo_componente"];
                    $comp->cargaDatosComponenteId($id_componente);
                    $nombre_componente = $comp->nombre;
                    $fecha_creado = $funciones->cFechaNormal($comp->fecha_creacion);

                    switch($id_tipo_componente) {
                        case '1':
                            // CABINA
                            $tipo_componente = 'CABINA';
                            $res_kits = $comp->dameKitsComponente($id_componente);
                            break;
                        case '2':
                            // PERIFERICO
                            $tipo_componente = 'PERIF&Eacute;RICO';
                            $res_kits = $comp->dameKitsComponente($id_componente);
                            break;
                        case '3':
                            // SOFTWARE
                            $tipo_componente = 'SOFTWARE';
                            break;
                        default:
                            // ERROR
                            break;
                    }  ?>

                    <tr>
                        <td style="text-align: center;"><?php echo $id_componente; ?></td>
                        <td><?php echo $nombre_componente; ?></td>
                        <td><?php echo $tipo_componente; ?></td>
                        <td style="text-align: center;"><?php echo $comp->version;?></td>
                        <td style="text-align: center;"><?php echo $fecha_creado; ?></td>
                    </tr>

                <?php
                    // Añadimos los kits del componente principal
                    for($j=0;$j<count($res_kits);$j++) {
                        $id_componente = $res_kits[$j]["id_kit"];
                        $tipo_componente = 'KIT';
                        $comp->cargaDatosComponenteId($id_componente);
                        $nombre_componente = $comp->nombre; ?>

                        <tr>
                            <td style="text-align: center;"><?php echo $id_componente; ?></td>
                            <td><?php echo $nombre_componente; ?></td>
                            <td><?php echo $tipo_componente; ?></td>
                            <td style="text-align: center;"><?php echo $comp->version;?></td>
                            <td style="text-align: center;"><?php echo $fecha_creado; ?></td>
                        </tr>
                <?php
                    }
                    unset($res_kits);
                }
            ?>
		</table>                  
	</div>
</div>
