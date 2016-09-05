<?php
// Muestra los software de la OP
include("../classes/mysql.class.php");
include("../classes/basicos/software.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");

$db = new MySQL();
$orden_produccion = new Orden_Produccion();
$soft = new Software();

$id_produccion = $_GET["id_produccion"];

$ids_softwares = $orden_produccion->dameIdsSoftwares($id_produccion);
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Software asociado al producto </h1>
    <h2> <?php echo $_GET["producto"];?> </h2>
    <div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th>NOMBRE</th>
       		<th>REFERENCIA</th>
        	<th style="text-align:center">VERSION</th>
        	<th>DESCRIPCION</th>
        </tr>
        <?php
			for($i=0;$i<count($ids_softwares);$i++) {
				// Se cargan los datos de los softwares según su identificador
				$datoSoftware = $ids_softwares[$i];
				$soft->cargaDatosSoftwareId($datoSoftware["id_componente"]);
		?>
		<tr>
			<td><?php echo $soft->software; ?></td>
			<td><?php echo $soft->referencia; ?></td>
			<td style="text-align:center"><?php echo $soft->version; ?></td>
			<td><?php echo $soft->descripcion; ?></td>
        </tr>
 		<?php
			}
		?>
		</table>                  
	</div>
</div>