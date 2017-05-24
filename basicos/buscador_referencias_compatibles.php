<?php
// Este fichero realiza la busqueda de referencias. Es el buscador de referencias compatibles de basicos
include("../classes/mysql.class.php");
include("../classes/basicos/listado_referencias.class.php");
include("../classes/basicos/incluir_referencia.class.php");
include("../classes/basicos/referencia.class.php");
include("../classes/basicos/referencia_compatible.class.php");
include("../classes/basicos/usuario.class.php");

// Se carga la clase para la base de datos
$db = new MySQL();
$ref = new Referencia();
$ref_comp = new Referencia_Compatible();
$referencias = new listadoReferencias();
$usuario = new Usuario();

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

	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$referencias->setValores($referencia,$proveedor,$ref_proveedor,$precio_pack,$fabricante,$ref_fabricante,$tipo_pieza,$part_value_name,$cantidad,$nombre_pieza,$part_value_qty,$busqueda_magica,$ordenar_referencias,$fecha_desde,$fecha_hasta,$id_ref,'');
	$referencias->realizarConsulta();
	$resultadosBusqueda = $referencias->referencias;
	$num_resultados = count($resultadosBusqueda); 
	
	if($busqueda_magica != "") {
		$referencia = "";
		$proveedor = "";
		$ref_prov_pieza = "";
		$precio_pack = "";
		$fabricante = "";
		$ref_fab_pieza = "";
		$tipo_pieza = "";
		$part_value_name = "";
		$unidades_paquete = "";
		$nombre_pieza = "";
		$part_value_qty = "";
		$id_ref = "";
	}
	
	// Mostramos los valores iniciales de busqueda
	$busqueda_magica = $busqueda_magica_ant;
	$ref_proveedor = $ref_proveedor_ant;
	$ref_fabricante = $ref_fabricante_ant;
}
$id_referencia_principal = $_GET["id_ref"];
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<script type="text/javascript" src="../js/funciones_24052017_1515.js"></script>

<script type="text/javascript">
// Llama a la funcion addRow para insertar la referencia en la tabla seleccionada
function add_referencia(id_referencia){
	id_grupo = document.getElementById("id_grupo-" + id_referencia).value;
	fecha_grupo = document.getElementById("fecha_grupo-" + id_referencia).value;
	referencia = document.getElementById("referencia-" + id_referencia).value;
	proveedor = document.getElementById("proveedor-" + id_referencia).value;
	nombre_pieza = document.getElementById("nombre_pieza-" + id_referencia).value;
	ref_proveedor = document.getElementById("ref_proveedor-" + id_referencia).value;
	cantidad = document.getElementById("unidades-" + id_referencia).value;
	p_precio = document.getElementById("p_precio-" + id_referencia).value;
	id_referencia_principal = document.getElementById("id_referencia_principal").value;

	var p_unitario;
	var p_referencia;

	p_precio = cambiarComaPorPunto(p_precio);
	p_precio = parseFloat(p_precio);
	p_precio = p_precio * 100;
	p_precio = Math.round(p_precio)/100;
		
	cantidad = parseInt(cantidad);

	if (isNaN(cantidad)){
		cantidad = 0;
		p_unitario = 0;
	}
	else {
		p_unitario = parseFloat(p_precio / cantidad);
	}

	p_unitario = p_unitario * 100;
	p_unitario = Math.round(p_unitario)/100;
	p_referencia = p_unitario;

	enlace = '<a href="mod_referencia.php?id=' + id_referencia + '" target="blank" style="color: #0000EE;"/>'
	fin_enlace = '</a>';
	cadena_identificador = '<input type="hidden" name="REFS_COMP[]" id="REFS_COMP[]" value="' + id_referencia + '" />';
	identificador = id_referencia;
	boton = '<input type="button" id="menos" name="menos" value="-" onclick="javascript:removeRowCompatible()"  />'

	if (referencia.length > 50){
		referencia = referencia.substring(0,50) + '...';
	}

	// Preparamos el enlacen de la referencia del proveedor
	if (ref_proveedor.length > 35){
		if(proveedor == 'RS AMIDATA'){
			ref_proveedor = '<a href="http://es.rs-online.com/web/c/?searchTerm=' + ref_proveedor + '" target="_blank">' + ref_proveedor.substring(0,35) + '...' + '</a>';
		}
		else if(proveedor == 'FARNELL'){
			ref_proveedor = '<a href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st=' + ref_proveedor +'" target="_blank">' + ref_proveedor.substring(0,35) + '...' + '</a>';
		}
		else {
			ref_proveedor = ref_proveedor.substring(0,35) + '...';
		}
	}
	else {
		if(proveedor == 'RS AMIDATA'){
			ref_proveedor = '<a href="http://es.rs-online.com/web/c/?searchTerm=' + ref_proveedor + '" target="_blank">' + ref_proveedor + '</a>';
		}
		else if(proveedor == 'FARNELL'){
			ref_proveedor = '<a href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st=' + ref_proveedor +'" target="_blank">' + ref_proveedor + '</a>';
		}
	}

	// window.opener.id_grupo = id_grupo;
	// window.opener.fecha_grupo = fecha_grupo;
	window.opener.ref = enlace + referencia + fin_enlace + cadena_identificador;
	window.opener.prov = proveedor;
	window.opener.ref_prov = ref_proveedor;
	window.opener.nom_pieza = nombre_pieza;
	window.opener.pack_precio = p_precio;
	window.opener.cant = cantidad;
	window.opener.precio_unidad = p_unitario;
	window.opener.precio_referencia = p_referencia;
		
	window.opener.boton = boton;
	
	window.opener.id_ref= identificador;

	window.opener.id_referencia_principal = id_referencia_principal;
	
	window.opener.addRowCompatible('mitablaCompatibles',id_referencia);
}

// Cambia las comas de un precio por punto
function cambiarComaPorPunto(p_precio){
	tamaño_float = p_precio.length;
	i=0;
	cadena = "";
	while (i<tamaño_float) {
		if(p_precio[i] == ","){
			cadena = cadena + ".";
		}
		else {
			cadena = cadena + p_precio[i];
		}
		i++;	
	}
	p_precio = cadena;
	return p_precio;
}

</script>

<div id="ContenedorCentralReferencias">
	<h3> Añadir referencia compatible </h3>
	<div id="ContenedorBuscadorReferencias">
		<h4> Buscar la referencia para añadir </h4>
   		<form name="BuscadorReferencias" id="BuscadorReferencias" action="buscador_referencias_compatibles.php?id_ref=<?php echo $id_referencia_principal;?>" method="post">
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
            	<input type="text" name="id_ref" class="BuscadorInputReferencias" value="<?php echo $id_ref;?>" onkeypress="return soloNumeros(event)" onkeyup="cargaReferenciaIntro(event);"/>
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
			<input type="hidden" id="nombreFormulario" name="nombreFormulario" value="BuscadorReferencias" />
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
		if ($mostrar_tabla)	{ ?>
   			<div class="CapaTablaReferencias">
    		<table>
        	<tr>
				<th style="text-align: center; display: none;">ID GR</th>
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
				$max_caracteres = 32;
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					// Se cargan los datos de las referencias según su identificador
					$datoRef = $resultadosBusqueda[$i];
					$ref->cargaDatosReferenciaId($datoRef["id_referencia"]);
					// Obtenemos el id_grupo de la referencia
					$res_grupo = $ref_comp->dameGrupoReferencia($datoRef["id_referencia"]);
					if(empty($res_grupo)) {
						$id_grupo = "-";
						$fecha_grupo = $usuario->fechaHoraSpain(date('Y-m-d H:i:s'));
					}
					else {
						$id_grupo = $res_grupo["id_grupo"];
						// Obtenemos la fecha del grupo
						$res_fecha_grupo = $ref_comp->dameFechaGrupo($id_grupo);
						$fecha_grupo = $usuario->fechaHoraSpain($res_fecha_grupo["fecha_creado"]);
					} ?>
					<tr>
						<td style="text-align: center; display: none;">
							<?php echo $id_grupo; ?>
							<input type="hidden" name="id_grupo-<?php echo $ref->id_referencia;?>" id="id_grupo-<?php echo $ref->id_referencia;?>" value="<?php echo $id_grupo; ?>" />
							<input type="hidden" name="fecha_grupo-<?php echo $ref->id_referencia;?>" id="fecha_grupo-<?php echo $ref->id_referencia;?>" value="<?php echo $fecha_grupo; ?>" />
						</td>
						<td style="text-align: center;"><?php echo $ref->id_referencia; ?></td>
						<td>
							<?php  
								if (strlen($ref->referencia) > $max_caracteres){
									echo substr($ref->referencia,0,32).'...';
								}
								else {
									echo $ref->referencia;	
								}
							?>
                            <input type="hidden" name="referencia-<?php echo $ref->id_referencia;?>" id="referencia-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->referencia;?>" />
						</td>
						<td>
							<?php echo $ref->nombre_proveedor; ?>
                            <input type="hidden" name="proveedor-<?php echo $ref->id_referencia;?>" id="proveedor-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->nombre_proveedor;?>" />
						</td>
						<td style="text_decoration:none;">
							<?php $ref->vincularReferenciaProveedor();?>
                            <input type="hidden" name="ref_proveedor-<?php echo $ref->id_referencia;?>" id="ref_proveedor-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->part_proveedor_referencia;?>" />
                 		</td>
                        <td>
							<?php 
								if (strlen($ref->part_nombre) > $max_caracteres){
									echo substr($ref->part_nombre,0,32).'...';
								}
								else {
									echo $ref->part_nombre;
								}
							?>
                            <input type="hidden" name="nombre_pieza-<?php echo $ref->id_referencia;?>" id="nombre_pieza-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->part_nombre;?>" />
						</td>
                        <td style="text-align:center">
							<?php echo number_format($ref->pack_precio, 2, '.', ''); ?>
                            <input type="hidden" name="p_precio-<?php echo $ref->id_referencia;?>" id="p_precio-<?php echo $ref->id_referencia;?>" value="<?php echo number_format($ref->pack_precio, 2, '.', '');?>" /></td>
                        <td style="text-align:center">
							<?php echo $ref->unidades; ?>
                            <input type="hidden" name="unidades-<?php echo $ref->id_referencia;?>" id="unidades-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->unidades;?>" />
                        </td>
                        <td style="text-align:center">
                            <form name="BuscadorReferenciaCompatible" id="BuscadorReferenciaCompatible" action="#" method="post">
								<input type="button" onclick="javascript:add_referencia(<?php echo $ref->id_referencia;?>);" value="+" />
                                <input type="hidden" id="guardandoReferenciaCompatible" name="guardandoReferenciaCompatible" />
								<input type="hidden" id="id_referencia_principal" name="id_referencia_principal" value="<?php echo $id_referencia_principal;?>" />
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
