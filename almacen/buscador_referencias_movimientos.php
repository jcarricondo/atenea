<?php
// Este fichero realiza la busqueda de referencias. Es el buscador de referencias de movimientos
// Devuelve unicamente el id_ref de la referencia seleccionada
include_once("../classes/mysql.class.php");
include_once("../classes/basicos/listado_referencias.class.php");
include_once("../classes/basicos/incluir_referencia.class.php");

$metodo = $_GET["metodo"];

// Se obtienen los datos del formulario
if(isset($_POST["realizandoBusqueda"]) and $_POST["realizandoBusqueda"] == 1) {
	$mostrar_tabla = true;
	$referencia = addslashes($_POST["referencia"]);
	$cantidad = addslashes($_POST["cantidad"]);
	$proveedor = addslashes($_POST["proveedor"]);
	$fabricante = addslashes($_POST["fabricante"]);
	$ref_proveedor = addslashes($_POST["ref_proveedor"]);
	$ref_fabricante = addslashes($_POST["ref_fabricante"]);
	$nombre_pieza = addslashes($_POST["nombre_pieza"]);
	$tipo_pieza = addslashes($_POST["tipo_pieza"]);
	$part_value_name = addslashes($_POST["part_value_name"]);
	$part_value_qty = addslashes($_POST["part_value_qty"]);
	$precio_pack = addslashes($_POST["precio_pack"]);
	$busqueda_magica = addslashes($_POST["busqueda_magica"]);
	$ordenar_referencias = $_POST["ordenar_referencias"];
	$id_ref = $_POST["id_ref"];

	// Si los valores de cantidad y precio_pack no son numericos omite los campos
	if(!is_numeric($cantidad)) $cantidad = NULL;
	if(!is_numeric($precio_pack)) $precio_pack = NULL;
	if(!is_numeric($id_ref)) $id_ref = NULL;
	
	// Guardamos en una variable los campos para mostrarlos despues de la busqueda
	$busqueda_magica_ant = $busqueda_magica;
	$ref_proveedor_ant = $ref_proveedor;
	$ref_fabricante_ant = $ref_fabricante;	
	
	// Quitar guiones y espacios del campo de busqueda magica y referencias de proveedor y fabricante
	for($i=0;$i<strlen($busqueda_magica);$i++){
		if (($busqueda_magica[$i] == '-') or ($busqueda_magica[$i] == ' ')) $busqueda_magica[$i] = '%'; 
	}
	
	for($i=0;$i<strlen($ref_proveedor);$i++){
		if (($ref_proveedor[$i] == '-') or ($ref_proveedor[$i] == ' ')) $ref_proveedor[$i] = '%'; 	
	}
	
	for($i=0;$i<strlen($ref_fabricante);$i++){
		if (($ref_fabricante[$i] == '-') or ($ref_fabricante[$i] == ' ')) $ref_fabricante[$i] = '%'; 	
	}
		
	// Se carga la clase para la base de datos
	$db = new MySQL();
	$referencias = new listadoReferencias();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$referencias->setValores($referencia,$proveedor,$ref_proveedor,$precio_pack,$fabricante,$ref_fabricante,$tipo_pieza,$part_value_name,$cantidad,$nombre_pieza,$part_value_qty,$busqueda_magica,$ordenar_referencias,$fecha_desde,$fecha_hasta,$id_ref,'');
	$referencias->realizarConsulta();
	$resultadosBusqueda = $referencias->referencias;
	$num_resultados = count($resultadosBusqueda);
	
	if ($busqueda_magica != "") {
		$referencia = "";
		$proveedor = "";
		$ref_proveedor = "";
		$precio_pack = "";
		$fabricante = "";
		$ref_fabricante = "";
		$tipo_pieza = "";
		$part_value_name = "";
		$unidades_paquete = "";
		$nombre_pieza = "";
		$part_value_qty = "";
		$id_ref = "";
	}
	
	// Mostramos los valores iniciales de busqueda
	$busqueda_magica = $busqueda_magica_ant;
}
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<script type="text/javascript" src="../js/funciones_24052017_1515.js"></script>

<script type="text/javascript">
// Cambia el atributo "value" del id_ref del buscador de movimientos por la id_ref seleccionada
function add_referencia(id_referencia){
	window.opener.document.getElementById('id_ref').setAttribute("value",id_referencia);
	window.close();
}
// Funcion que solo permite escribir numeros
function soloNumeros (e) { 
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla==8) return true; // 3
    patron =/\d/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}
</script>

<div id="ContenedorCentralReferencias">
	<h3> Añadir referencia</h3>
	<div id="ContenedorBuscadorReferencias">
		<h4>Buscar la referencia</h4>
   		<form name="BuscadorReferenciasMovimientos" id="BuscadorReferenciasMovimientos" action="buscador_referencias_movimientos.php" method="post">
    		<div class="ContenedorCamposBuscadorReferencias">
				<div class="LabelReferencias">Nombre</div>
            	<input type="text" name="referencia" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($referencia));?>"/> 
               	<div class="LabelReferencias">Unidades paquete</div>
            	<input type="text" name="cantidad" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($cantidad));?>"/>
   				<div class="LabelReferencias">Precio Pack</div>
            	<input type="text" name="precio_pack" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($precio_pack));?>"/>
            </div>
      		<div class="ContenedorCamposBuscadorReferencias">
		        <div class="LabelReferencias">Proveedor</div>
            	<input type="text" name="proveedor" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($proveedor));?>"/>
                <div class="LabelReferencias">Ref. Proveedor</div>
            	<input type="text" name="ref_proveedor" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($ref_proveedor));?>"/>
                <div class="LabelReferencias">Nombre Pieza</div>
            	<input type="text" name="nombre_pieza" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($nombre_pieza));?>"/>
            </div> 
            <div class="ContenedorCamposBuscadorReferencias">
   				<div class="LabelReferencias">Fabricante</div>
            	<input type="text" name="fabricante" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($fabricante));?>"/>
                <div class="LabelReferencias">Ref. Fabricante</div>
            	<input type="text" name="ref_fabricante" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($ref_fabricante));?>"/>
                <div class="LabelReferencias">Tipo Pieza</div>
            	<input type="text" name="tipo_pieza" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($tipo_pieza));?>"/>
            </div>    
           	<div class="ContenedorCamposBuscadorReferencias">
  				<div class="LabelReferencias">Part value name</div>
            	<input type="text" name="part_value_name" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($part_value_name));?>"/>
   				<div class="LabelReferencias">Part value qty</div>
            	<input type="text" name="part_value_qty" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($part_value_qty));?>"/>
            	<div class="LabelReferencias">ID Referencia</div>
            	<input type="text" name="id_ref" class="BuscadorInputReferencias" value="<?php echo $id_ref;?>" onkeypress="return soloNumeros(event)" onkeyup="cargaReferenciaIntro(event);" />
        	</div>
            <div class="ContenedorCamposBuscadorReferencias">

  			</div>
            
            <div class="ContenedorCamposBuscadorReferencias">
				<div class="LabelReferenciasBusqueda">BUSQUEDA MAGICA Y ORDENAR POR</div>
            </div>
            
            <div class="ContenedorCamposBuscadorReferencias">
            	<div class="LabelReferencias">Busqueda Mágica</div>
            		<input type="text" name="busqueda_magica" class="BuscadorInputReferencias" value="<?php echo stripslashes(htmlspecialchars($busqueda_magica));?>"/>
  				<div class="LabelReferencias">Ordenar por</div>
          		<select id="ordenar_referencias" name="ordenar_referencias" class="BuscadorInputReferencias"/>
            		<option value="0">Ordenar por...</option>
                	<option value="1">PRECIO</option>
                	<option value="2">PROVEEDOR</option>
                	<option value="3">FABRICANTE</option>
                	<option value="4">NOMBRE PIEZA</option>
               		<option value="5">TIPO PIEZA</option>
                	<option value="6">UNIDADES PAQUETE</option>
                	<option value="7">REF. PROVEEDOR</option>
                	<option value="8">REF. FABRICANTE</option>    
                	<option value="9">ID. REFERENCIA</option>             
            	</select>
            </div>
                
        	<div class="ContenedorBotonBuscadorReferencias">
        		<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
            	<input type="submit" id="" name="" class="" value="Buscar" />
        	</div>
        	</br>
			<input type="hidden" id="nombreFormulario" name="nombreFormulario" value="BuscadorReferenciasMovimientos" />
    	</form>
    </div>
    
    <div class="ContenedorMensajeOperacionReferencia">
    	<?php
    		if($mostrar_tabla){
				if($num_resultados == NULL or $num_resultados == 0){
	               echo '<div class="mensaje">No se encontraron referencias</div>';
	               $mostrar_tabla = false;
	            }
	            else if ($num_resultados == 1){
	            	echo '<div class="mensaje">Se encontró 1 referencia</div>';
	            }
	            else{
	            	echo '<div class="mensaje">Se encontraron '.$num_resultados.' referencias</div>';
	            }	
        	}
    	?>
    </div>
 
    <?php 
		if ($mostrar_tabla){
		?>
   			<div class="CapaTablaReferencias">
    		<table>
        	<tr>
        		<th style="text-align:center">ID</th>
        		<th>NOMBRE</th>
            	<th>PROVEEDOR</th>
            	<th>REF. PROVEEDOR</th>
                <th>NOMBRE PIEZA</th>
                <th style="text-align:center">PACK PRECIO</th>       	
                <th style="text-align:center">UDS/P</th>
                <th style="text-align:center">+</th>
         	</tr>
        	<?php
				$max_caracteres = 50;
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					// Se cargan los datos de las referencias según su identificador
					$ref = new Referencia_Componente();
					$datoRef = $resultadosBusqueda[$i];
					$ref->cargaDatosReferenciaComponenteId($datoRef["id_referencia"]);
					?>
					<tr>
						<td style="text-align: center;">
							<?php echo $ref->id_referencia; ?>	
						</td>
						<td>
							<?php  
								if (strlen($ref->referencia) > $max_caracteres){
									echo substr($ref->referencia,0,50).'...';
								}
								else {
									echo $ref->referencia;	
								}
							?>
                            <input type="hidden" name="referencia-<?php echo $ref->id_referencia;?>" id="referencia-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->referencia;?>" />
						</td>
						<td>
							<?php echo $ref->proveedor; ?>
                            <input type="hidden" name="proveedor-<?php echo $ref->id_referencia;?>" id="proveedor-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->proveedor;?>" />
						</td>
						<td>
							<?php $ref->vincularReferenciaProveedor();?>
                            <input type="hidden" name="ref_proveedor-<?php echo $ref->id_referencia;?>" id="ref_proveedor-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->ref_proveedor;?>" />
                 		</td>
                        <td>
							<?php 
								if (strlen($ref->nombre_pieza) > $max_caracteres){
									echo substr($ref->nombre_pieza,0,50).'...';
								}
								else {
									echo $ref->nombre_pieza;	
								}
							?>
                            <input type="hidden" name="nombre_pieza-<?php echo $ref->id_referencia;?>" id="nombre_pieza-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->nombre_pieza;?>" />
						</td>
                        <td style="text-align:center">
							<?php echo number_format($ref->pack_precio, 2, '.', ''); ?>
                            <input type="hidden" name="p_precio-<?php echo $ref->id_referencia;?>" id="p_precio-<?php echo $ref->id_referencia;?>" value="<?php echo number_format($ref->pack_precio, 2, '.', '');?>" />                        </td>                       
                        <td style="text-align:center">
							<?php echo $ref->cantidad; ?>
                            <input type="hidden" name="cantidad-<?php echo $ref->id_referencia;?>" id="cantidad-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->cantidad;?>" />                            
                        </td>
                        <td style="text-align:center">
                            <form name="BuscadorReferenciaComponente" id="BuscadorReferenciaComponente" action="" method="post">
                            	<input type="button" onclick="javascript:add_referencia(<?php echo $ref->id_referencia;?>);" value="+" />
                                <input type="hidden" id="guardandoReferenciaComponente" name="guardandoReferenciaComponente" />
                            </form>
                        </td>                        
					</tr> 
               		<?php
				}
				?>
				</table>                  
			</div>
		<?php
		}
		?>
</div>
