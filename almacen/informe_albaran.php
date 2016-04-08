<?php 
// Este fichero genera un excel del albarán
include("../includes/sesion.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/centro_logistico.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/almacen/albaran.class.php");

$ref = new Referencia();
$alb = new Albaran();
$user = new Usuario();
$prov = new Proveedor();
$centro = new centroLogistico();
$op = new Orden_Produccion();
$almacen = new Almacen();

$id_albaran = $_GET["id_albaran"];
$id_almacen = $_GET["id_almacen"];

// Obtenemos los datos del albarán
$alb->cargaDatosAlbaranId($id_albaran);

$nombre_albaran = $alb->nombre_albaran;
$tipo_albaran = $alb->tipo_albaran;
$id_participante = $alb->id_participante;
$id_tipo_participante = $alb->id_tipo_participante;
$motivo = $alb->motivo;
$id_usuario = $alb->id_usuario;
$fecha_creado = $alb->fecha_creado;

// Obtenemos el nombre del participante
if($id_tipo_participante == 1){
	$prov->cargaDatosProveedorId($id_participante);
	$nombre_participante = $prov->nombre;
}
else {
	$centro->cargaDatosCentroLogisticoId($id_participante);
	$nombre_participante = $centro->nombre;
}

// Obtenemos el nombre del usuario
$user->cargaDatosUsuarioId($id_usuario);
$nombre_usuario = $user->usuario;

$almacen->cargaDatosAlmacenId($id_almacen);
$nombre_almacen = utf8_decode($almacen->nombre);

$table = '<table>
		<tr>
			<th style="text-align:left;">Almacen</th>
	    	<th style="text-align:left;">Usuario</th>
	        <th style="text-align:center;">Fecha</th>
			<th style="text-align:center;">Tipo Albaran</th>
			<th style="text-align:left;">Nombre Albaran</th>  
			<th style="text-align:left;">Origen / Destino</th>
	        <th style="text-align:left;">Motivo</th>

			<th style="text-align:center;">ID_REF</th>
			<th style="text-align:left;">Nombre Referencia</th>
			<th style="text-align:left;">Proveedor</th>
			<th style="text-align:left;">Referencia Proveedor</th>
			<th style="text-align:left;">Nombre Pieza</th>
			<th style="text-align:right;">Precio Pack</th>
			<th style="text-align:right;">Unidades Paquete</th>
			<th style="text-align:right;">Cantidad</th>
        </tr>';


// Obtenemos las referencias del albarán
$referencias_albaran = $alb->dameReferenciasAlbaran($id_albaran);
for($i=0;$i<count($referencias_albaran);$i++){
	// Preparamos los datos 
	$id_referencia = $referencias_albaran[$i]["id_referencia"];
	$nombre_referencia = $referencias_albaran[$i]["nombre_referencia"];
	$nombre_proveedor = $referencias_albaran[$i]["nombre_proveedor"];
	$referencia_proveedor = $referencias_albaran[$i]["referencia_proveedor"];
	$nombre_pieza = $referencias_albaran[$i]["nombre_pieza"];
	$pack_precio = $referencias_albaran[$i]["pack_precio"];
	$unidades_paquete = $referencias_albaran[$i]["unidades_paquete"];
	$cantidad = $referencias_albaran[$i]["cantidad"];

	$ref_prov = '';
	$ref_prov_codificada = utf8_decode($referencia_proveedor);
	
	// Codificamos los resultados
	for($m=0;$m<strlen($ref_prov_codificada);$m++){
		if ($ref_prov_codificada[$m] == '?'){
			$ref_prov .= '&#8364;'; 	
		}
		else {
			$ref_prov .= $ref_prov_codificada[$m]; 
		}
	}

	$salida .= '<tr>
		  	<td style="text-align:left;">'.$nombre_almacen.'</td>
		  	<td style="text-align:left;">'.$nombre_usuario.'</td>
	      	<td style="text-align:center;">'.$fecha_creado.'</td>
			<td style="text-align:center;">'.$tipo_albaran.'</td>
			<td style="text-align:left;">'.$nombre_albaran.'</td>  
			<td style="text-align:left;">'.$nombre_participante.'</td>
	        <td style="text-align:left;">'.$motivo.'</td>

			<td style="text-align:center;">'.$id_referencia.'</td>
			<td style="text-align:left;">'.$nombre_referencia.'</td>
			<td style="text-align:left;">'.$nombre_proveedor.'</td>
			<td style="text-align:left;">'.$ref_prov.'</td>
			<td style="text-align:left;">'.$nombre_pieza.'</td>
			<td style="text-align:right;">'.$pack_precio.'</td>
			<td style="text-align:right;">'.$unidades_paquete.'</td>
			<td style="text-align:right;">'.$cantidad.'</td>
		  </tr>';	

}
$table_end = '</table>';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=informeAlbaran.xls");
echo $table.$salida.$table_end; 
?>
