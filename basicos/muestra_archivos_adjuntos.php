<?php
// Este fichero muestra los archivos de una referencia
$id_referencia = $_GET["id"];
include("../classes/mysql.class.php");
include("../classes/basicos/referencia.class.php");   
include("../classes/basicos/listado_referencias_archivos.class.php");

$db = new MySQL();
$ref_archivos = new listadoReferenciasArchivos();
$ref_archivos->setValores($id_referencia);
$ref_archivos->realizarConsulta();
$resultadosBusqueda = $ref_archivos->referencias_archivos;
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1>Archivos adjuntos a la referencia: <?php echo $_GET["nombre"];?></h1>
    
	<div id="CapaTablaReferencias">
    	<table>
        <tr>
        	<th>NOMBRE ARCHIVO</th>
        	<th>FECHA SUBIDA</th>
        	<th>DESCARGAR</th>
            <th>ELIMINAR</th>
        </tr>
        <?php
			for($i=0;$i<count($resultadosBusqueda);$i++) {
				$ref_arch = new Referencia();
				$datoRef_Arch = $resultadosBusqueda[$i];
				$ref_arch->cargaDatosArchivosReferenciaId(($datoRef_Arch["id_archivo"]));  
		?>
		<tr>
			<td><?php echo $ref_arch->nombre_archivo; ?></td>
			<td><?php echo $ref_arch->fecha_subida; ?></td>
			<td><input type="button" id="descargar" name="descargar" class="BotonEliminar"  value="DESCARGAR" onclick="javascript:Abrir_ventana('BuscadorReferenciasCabina.php')"/></td>
			<td><input type="checkbox" name="chkbox" value="<?php echo $ref_arch->id_archivo;?>" /></td>
        </tr>
        <?php
			}
		?>
		</table>                  
	</div>
</div>