<?php
// Este fichero muestra un popup con los codigo de los escandallos generados
include("../includes/sesion.php");
include("../classes/basicos/usuario.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/producciones/produccion.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");

$db = new MySQL();
$produccion = new Produccion();
$usuario = new Usuario();
$almacen = new Almacen();
$orden_produccion = new Orden_Produccion();
$control_usuario = new Control_Usuario();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen = $_SESSION["AT_id_almacen"];
// Comprobamos si es Administrador Global
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esAdministradorGes = $control_usuario->esAdministradorGes($id_tipo_usuario);
// Obtenemos la sede a la que pertenece el usuario 
$id_sede = $almacen->dameSedeAlmacen($id_almacen);
$id_sede = $id_sede["id_sede"];

// Cargamos todos los codigos del log
if($esAdminGlobal || $esAdministradorGes) $produccion->dameTodosCodigos();
else {
    $produccion->dameTodosCodigosPorSede($id_sede);
}

$resultados_codigos = $produccion->resultados;

if($_GET["recuperar"] == 1){
    $codigo = $_GET["codigo"];
    echo '<script type="text/javascript">opener.location.href="escandallo_por_componentes.php?escandallo=recuperar&codigo='.$codigo.'"; window.close()</script>';
}    
?>

<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraReferencias">
	<h1>Código de escandallos</h1>
	<h2>Pulse sobre el código para recuperar el escandallo</h2>
	<div id="CapaTablaReferencias">
    	<table>
        	<tr>
        		<th style="text-align:center">CÓDIGO</th>
        		<th style="text-align:center">ID PRODUCCIÓN</th>
                <th>ORDEN PRODUCCIÓN</th>
                <?php 
                    if($esAdminGlobal || $esAdministradorGes){ ?>
                        <th>SEDE</th>
                <?php 
                    } 
                ?>
                <th style="text-align:center">NUM. TÉCNICOS</th>
        		<th style="text-align:center">USUARIO</th>
        		<th style="text-align:center">FECHA</th>
        	</tr>
<?php
	for($i=0;$i<count($resultados_codigos);$i++){
		// Cargamos los datos de ese escandallo
		$produccion->cargaDatosEscandalloCodigo($resultados_codigos[$i]["codigo"]);
		$usuario->cargaDatosUsuarioId($produccion->id_usuario);
        // Cargamos los datos de la orden de produccion
        $orden_produccion->cargaDatosProduccionId($produccion->id_produccion);
?>
			<tr>
				<td style="text-align:center">
                    <?php echo '<a href="recuperar_escandallo_por_componentes.php?codigo='.$produccion->codigo.'&recuperar=1">'.$produccion->codigo.'</a>';?>
                </td>
				<td style="text-align:center"><?php echo $produccion->id_produccion;?></td>		
                <td><?php echo $orden_produccion->codigo;?></td>
                <?php 
                    if($esAdminGlobal || $esAdministradorGes){ ?>
                        <td>
                        <?php 
                            if($orden_produccion->id_sede == 1) echo "SIMUMAK";
                            else if($orden_produccion->id_sede == 2) echo "TORO";
                        ?>
                        </td>
                <?php 
                    }
                ?>
                <td style="text-align:center"><?php echo $produccion->numero_tecnicos;?>
            	<td style="text-align:center"><?php echo $usuario->usuario;?></td>
            	<td style="text-align:center"><?php echo $usuario->FechaHoraSpain($produccion->fecha_creacion);?></td>
        	</tr>    	
<?php
	}
?>        	
        </table>
    </div>
</div>    
