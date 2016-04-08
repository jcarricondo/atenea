<?php
// Este fichero inicia la Orden de Produccion
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/proveedor.class.php");
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/listado_clientes.class.php");
include("../classes/orden_produccion/orden_produccion.class.php");
include("../classes/orden_compra/orden_compra.class.php");
include("../classes/productos/producto.class.php");
include("../classes/sede/sede.class.php");
include("../classes/control_usuario.class.php");
include("../classes/kint/Kint.class.php");
permiso(11);

$id_producto = $_GET["id_producto"];
$unidades = $_GET["unidades"];
$id_produccion = $_GET["id_produccion"];

$bbdd = new MySQL;
$clientes = new listadoClientes();
$orden_produccion = new Orden_Produccion();
$producto = new Producto();
$client = new Cliente();
$sede = new Sede();
$control_usuario = new Control_Usuario();
$funciones = new Funciones();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$esAdminGlobal = $control_usuario->esAdministradorGlobal($id_tipo_usuario);
$esUsuarioGes = $control_usuario->esUsuarioGes($id_tipo_usuario);

$orden_produccion->cargaDatosProduccionId($id_produccion);
$alias = $orden_produccion->alias_op;
$unidades = $orden_produccion->unidades;
$fic = $funciones->cFechaNormal($orden_produccion->fecha_inicio_construccion);
$estado = $orden_produccion->estado;

$id_sede = $orden_produccion->id_sede;
$sede->cargaDatosSedeId($id_sede);
$nombre_sede = $sede->nombre;

if(isset($_POST["iniciarOP"]) and $_POST["iniciarOP"] == 1) {
	$id_producto = $_GET["id_producto"];
	$id_produccion = $_GET["id_produccion"];
	$unidades = $_GET["unidades"];
	$fecha_inicio = $_POST["fecha_inicio"];
	$fecha_entrega = $_POST["fecha_entrega"];
	$fecha_entrega_deseada = $_POST["fecha_entrega_deseada"];
	$cliente = $_POST["cliente"];
	$ids_clientes = $_POST["IDS_CLIENTES"];

	if($esAdminGlobal || $esUsuarioGes) $id_almacen = $_POST["almacenes"];

	$orden_produccion->dameIdsProductoOP($id_produccion); 
	$ids_productos_op = $orden_produccion->ids_productos;
	for($i=0;$i<$unidades;$i++) {
		$array_ids_productos[$i] = $ids_productos_op[$i]["id_producto"];
	}
	
	if(($orden_produccion->validarFecha($fecha_inicio)) and ($orden_produccion->validarFecha($fecha_entrega)) and ($orden_produccion->validarFecha($fecha_entrega_deseada))) {
		$fecha_inicio = $orden_produccion->cFechaMy($fecha_inicio); 
		$fecha_entrega = $orden_produccion->cFechaMy($fecha_entrega);
		$fecha_entrega_deseada = $orden_produccion->cFechaMy($fecha_entrega_deseada); 
				
		// Pasa a estado INICIADO la Orden de Produccion
		$resultado = $orden_produccion->iniciarOrdenProduccion($id_produccion,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada);
		if($resultado == 1) {
			$ids_productos_op = $orden_produccion->dameIdsProductoOP($id_produccion); 

			$fallo = false;
			$i=0;
			// Asigna el cliente al producto y pasa el estado del Producto a EN CONSTRUCCION
			while ($i<$unidades and !$fallo) {
				$resultado = $producto->iniciarProducto($array_ids_productos[$i],$ids_clientes[$i]);
				$i++;
				$fallo = $resultado != 1;
			}
			if (!$fallo) {
				echo '<script type="text/javascript">opener.location.href="ordenes_produccion.php?OProduccion=iniciado&id_produccion='.$id_produccion.'&sedes='.$id_sede.'&almacenes='.$id_almacen.'";window.close()</script>';
			}
			else {
				// Se produjo un error al iniciar el estado del producto
				// Pasamos la Orden de Produccion a estado BORRADOR
				$orden_produccion->estadoBorradorOrdenProduccion($id_produccion,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada);
				if ($resultado == 1) {
					// Pasamos los productos de la Orden de Produccion a estado BORRADOR
					$orden_produccion->estadoBorradorProducto($id_produccion);
					if ($resultado == 1) {
						$mensaje_error = $producto->getErrorMessage($resultado);	
					}
					else {
						$mensaje_error = $orden_produccion->getErrorMessage($resultado);	
					}
				}
				else {
					$mensaje_error = $orden_produccion->getErrorMessage($resultado);
				}
			}
		} 
		else {
			// Se produjo un error al iniciar la Orden de Produccion
			$orden_produccion->estadoBorradorOrdenProduccion($id_produccion,$fecha_inicio,$fecha_entrega,$fecha_entrega_deseada);
			$mensaje_error = $orden_produccion->getErrorMessage($resultado);
		} 
	}
	else {
		// Inserte una fecha correcta
		$resultado = 7;
		$mensaje_error = $orden_produccion->getErrorMessage($resultado);	
	}
}
?>
<script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
<script src="http://code.jquery.com/jquery-1.8.2.js"></script>
<script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
<script>
	
	jQuery(function($){
        $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: '&#x3c;Ant',
                nextText: 'Sig&#x3e;',
                currentText: 'Hoy',
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                'Jul','Ago','Sep','Oct','Nov','Dic'],
                dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
                dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['es']);
	});
	
	$(function() {
        $( ".fechaCal" ).datepicker();
    });
	
</script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<div id="MuestraIniciar">
	<h1> Iniciar Orden de Producción </h1>
    <div id="CapaFormularioIniciarOP">
    	<form id="FormularioIniciarOP" name="iniciarOrdenProduccion" action="iniciar.php?id_producto=<?php echo $id_producto;?>&id_produccion=<?php echo $id_produccion;?>&unidades=<?php echo $unidades;?>" method="post">
       	<br />
        <h5> Rellene los siguientes campos para iniciar la orden de producción </h5>
        <?php 
        	if($esAdminGlobal || $esUsuarioGes){ ?>
        		<div class="ContenedorCamposIniciarOP">
        			<div class="LabelIniciarOP">Sede</div>
        			<label class="LabelInfoOP"><?php echo $nombre_sede; ?></label>
        		</div>
        		<div class="ContenedorCamposIniciarOP">
        			<div class="LabelIniciarOP">Almacen *</div>
        			<select id="almacenes" name="almacenes" class="CreacionBasicoInput">
        			<?php 
        				// Cargamos los almacenes disponibles
        				$res_almacenes = $sede->dameAlmacenesFabricaSede($id_sede);
        				for($i=0;$i<count($res_almacenes);$i++){ 
        					$id_almacen = $res_almacenes[$i]["id_almacen"];	
        					$nombre_almacen = $res_almacenes[$i]["almacen"]; 
        			?>
        					<option value="<?php echo $id_almacen;?>"><?php echo utf8_encode($nombre_almacen); ?></option>
        			<?php	
        				}
        			?>
        			</select>
        		</div>   
        <?php 
    		}
    	?>
    	<div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Alias</div>
        	<label class="LabelInfoOP"><?php echo $alias; ?></label>
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Unidades</div>
        	<label class="LabelInfoOP"><?php echo $unidades; ?></label>
        </div>
    	<div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Fecha Inicio *</div>
            <input type="text" id="datepicker_iniciar_fecha_inicio" name="fecha_inicio" class="fechaCal" value="<?php echo $fecha_inicio;?>" />
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Fecha Entrega *</div>
            <input type="text" id="datepicker_iniciar_fecha_entrega" name="fecha_entrega" class="fechaCal" value="<?php echo $fecha_entrega;?>" />
        </div>    
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Fecha Entrega Deseada * &nbsp;&nbsp;(DD/MM/AAAA)</div>
            <input type="text" id="datepicker_iniciar_fecha_deseada" name="fecha_entrega_deseada" class="fechaCal" value="<?php echo $fecha_entrega_deseada;?>" />
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Fecha Inicio Construcción</div>
        	<label class="LabelInfoOP"><?php echo $fic; ?></label>
        </div>
        <div class="ContenedorCamposIniciarOP">
        	<div class="LabelIniciarOP">Estado</div>
        	<label class="LabelInfoOP"><?php echo $estado; ?></label>
        </div>
        
		<br />
        <h5> Rellene los siguientes campos para iniciar los productos </h5>
               
       <?php 
			for($j=0;$j<$unidades;$j++)	{
				// Obtenemos los productos para ver si tiene un cliente asociado
				$orden_produccion->dameIdsProductoOP($id_produccion); 
				$productos_op = $orden_produccion->ids_productos;
				$producto->dameIdCliente($productos_op[$j]["id_producto"]);
				$id_cliente_producto = $producto->id_cliente["id_cliente"];
	   ?>
       			<div class="ContenedorCamposIniciarOP">
        			<div class="LabelIniciarOP">Asigne cliente al producto <?php echo $j+1;?></div>
                </div>
        		
                <div class="ContenedorCamposIniciarOP">
        			<div class="LabelIniciarOP">Cliente</div>
                    <select id="IDS_CLIENTES[]" name="IDS_CLIENTES[]"  class="IniciarOPInput">
            			<?php 
							$clientes->prepararConsulta();
							$clientes->realizarConsulta();
							$resultado_clientes = $clientes->clientes;

							for($i=-1;$i<count($resultado_clientes);$i++) {
								$datoCliente = $resultado_clientes[$i];
								$client->cargaDatosClienteId($datoCliente["id_cliente"]);
								echo '<option value="'.$client->id_cliente.'"';
								if($id_cliente_producto == $client->id_cliente) echo 'selected="selected"';	
								echo '>'.$client->nombre.'</option>';
							}
						?>
            		</select>
                </div>
        <?php
			}
		?>
        <div class="CapaBotonesIniciarOP">
          	<input type="button" id="cerrar" name="cerrar" value="Cerrar" onclick="javascript:window.close()" /> 
            <input type="hidden" id="iniciarOP" name="iniciarOP" value="1" />
            <input type="submit" id="guardar" name="guardar" value="Continuar" onclick="javascript: if (confirm('¿Desea iniciar la Orden de Producción?')) { window.location.href=iniciar.php?id_producto=<?php echo $id_producto;?>&id_produccion=<?php echo $id_produccion;?>' } else { void('') };" />
        </div>
        <div class="mensajeCamposObligatoriosIniciarOP">
        	* Campos obligatorios
        </div>
        <?php 
			if($mensaje_error != "") {
				if ($resultado != 4) {
					echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
				}
				else {
					echo '<div class="mensaje_error">'.$mensaje_error.'" Proveedor: "'.$nombre_prov.'</div>'; 
				}
			}
		?>
        <br />
     	</form>
    </div> 
</div>