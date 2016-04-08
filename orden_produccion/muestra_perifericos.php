<?php
// Muestra en un popup los periféricos asociados a esa Orden de Producción
include("../classes/mysql.class.php");
include("../classes/basicos/periferico.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");

$id_produccion = $_GET["id_produccion"];
$db = new MySQL();
$per = new Periferico();

$orden_produccion = new Orden_Produccion();

$ids_perifericos = $orden_produccion->dameIdsPerifericos($id_produccion)
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Periféricos asociados al producto </h1>
    <h2> <?php echo $_GET["producto"];?> </h2>
    <div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th>NOMBRE</th>
       		<th>REFERENCIA</th>
        	<th style="text-align:center">VERSIÓN</th>
        	<th>DESCRIPCIÓN</th>
        </tr>
        <?php
			for($i=0;$i<count($ids_perifericos);$i++) {
				// Se cargan los datos de los perifericos según su identificador
				$datoPeriferico = $ids_perifericos[$i];
				$per->cargaDatosPerifericoId($datoPeriferico["id_componente"]);
		?>
		<tr>
			<td><?php echo $per->periferico."_v".$per->version;?></td>
			<td><?php echo $per->referencia;?></td>
			<td style="text-align:center"><?php echo $per->version;?></td>
			<td><?php echo $per->descripcion;?></td>
        </tr>
 		<?php
			}
		?>
		</table>                  
	</div>
</div>
