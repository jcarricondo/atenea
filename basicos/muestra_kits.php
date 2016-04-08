<?php
// Este fichero muestra las interfaces de un componente
include("../classes/mysql.class.php");
include("../classes/basicos/kit.class.php");

$db = new MySQL();
$kits = new Kit();

$id_componente = $_GET["id"];
$kits->dameIdsKits($id_componente);
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Kits asociados a <?php echo $_GET["nombre"];?></h1>
	<div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th>NOMBRE KIT</th>
        	<th>REFERENCIA</th>
        	<th style="text-align:center">VERSION</th>
        	<th>DESCRIPCION</th>
        </tr>
        <?php
			for($i=0;$i<count($kits->ids_kits);$i++) {
				$kits->cargaDatosKitId($kits->ids_kits[$i]["id_kit"]); 
		?>
		<tr>
			<td><?php echo $kits->kit; ?></td>
			<td><?php echo $kits->referencia; ?></td>
			<td style="text-align:center"><?php echo $kits->version; ?></td>
            <td><?php echo $kits->descripcion; ?></td>
        </tr>
 		<?php
			}
		?>
		</table>                  
	</div>
</div>