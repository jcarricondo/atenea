<?php
// Este fichero muestra las interfaces de un componente
include("../classes/mysql.class.php");
include("../classes/basicos/interface.class.php");

$id_componente = $_GET["id"];

$db = new MySQL();
$interfaces = new Interfaz();
$interfaces->dameIdsInterfaces($id_componente);
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1> Interfaces asociadas a <?php echo $_GET["nombre"];?></h1>
	<div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th>INTERFAZ</th>
        	<th>REFERENCIA</th>
        	<th style="text-align:center">VERSION</th>
        	<th>DESCRIPCION</th>
        </tr>
        <?php
			for($i=0;$i<count($interfaces->ids_interfaces);$i++) {
				$interfaces->cargaDatosInterfazId($interfaces->ids_interfaces[$i]["id_interfaz"]); 
		?>
		<tr>
			<td><?php echo $interfaces->interfaz; ?></td>
			<td><?php echo $interfaces->referencia; ?></td>
			<td style="text-align:center"><?php echo $interfaces->version; ?></td>
            <td><?php echo $interfaces->descripcion; ?></td>
        </tr>
 		<?php
			}
		?>
		</table>                  
	</div>
</div>