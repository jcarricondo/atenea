<?php 
// Este es el fichero del buscador de referencias libres de una orden de producción
include("../classes/mysql.class.php");
include("../classes/orden_produccion/incluir_referencia_libre.class.php");
include("../classes/orden_produccion/listado_incluir_referencia_libre.class.php");

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
	
	// Se carga la clase para la base de datos y el listado de referencias libres
	$db = new MySQL();
	$referencias = new listadoIncluirReferenciaLibre();
	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$referencias->setValores($referencia,$cantidad,$proveedor,$fabricante,$ref_proveedor,$ref_fabricante,$nombre_pieza,$tipo_pieza,$part_value_name,$part_value_qty,$precio_pack,$busqueda_magica,$ordenar_referencias,$id_ref);
	$referencias->realizarConsulta();
	$resultadosBusqueda = $referencias->referencias;
	$num_resultados = count($resultadosBusqueda);

	if ($busqueda_magica != "") {
		$referencia = "";
		$cantidad = "";
		$proveedor = "";
		$fabricante = "";
		$ref_proveedor = "";
		$ref_fabricante = "";
		$nombre_pieza = "";
		$tipo_pieza = "";
		$part_value_name = "";
		$part_value_qty = "";
		$precio_pack = "";
		$id_ref = "";
	}
	
	// Mostramos los valores iniciales de busqueda
	$busqueda_magica = $busqueda_magica_ant;
}
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<script type="text/javascript" src="../js/funciones.js"></script>

<script type="text/javascript">
function add_referencia(id_referencia)
{
	referencia = document.getElementById("referencia-" + id_referencia).value;
	proveedor = document.getElementById("proveedor-" + id_referencia).value;
	ref_proveedor = document.getElementById("ref_proveedor-" + id_referencia).value;
	nombre_pieza = document.getElementById("nombre_pieza-" + id_referencia).value;
	piezas = document.getElementById("piezas-" + id_referencia).value;
	p_precio = document.getElementById("p_precio-" + id_referencia).value;
	cantidad = document.getElementById("cantidad-" + id_referencia).value;
	
	var p_unitario;
	var p_referencia;
	var error_piezas = false;
	error_piezas = validarPiezas(piezas);
	if (!error_piezas) {
		// Si no hay error en piezas entonces convertimos a float los campos p_precio, precio_unitari
		piezas = parseFloat(piezas);
		piezas = piezas * 100;
		piezas = Math.round(piezas)/100;
				
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
		
		if (piezas < cantidad) {
			tot_paquetes = 1;	
		}
		else {
			resto = piezas % cantidad;
			tot_paquetes = Math.floor((piezas / cantidad));
			if (resto != 0) {
				tot_paquetes = tot_paquetes + 1;	
			}
		}
		
		p_unitario = parseFloat(p_precio / cantidad);
		p_unitario = p_unitario * 100;
		p_unitario = Math.round(p_unitario)/100;
		
		p_referencia = parseFloat(piezas * p_unitario);
		p_referencia = p_referencia * 100;
		p_referencia = Math.round(p_referencia)/100;

	
		enlace = '<a href="../basicos/mod_referencia.php?id=' + id_referencia + '" target="_blank"/>';
		fin_enlace = '</a>';
		cadena_identificador = '<input type="hidden" name="REFS[]" id="REFS[]" value="' + id_referencia + '" />';
		identificador = id_referencia;
		boton = '<input type="button" id="menos" name="menos" value="-" onclick="javascript:removeRow()"  />'
		
		if (referencia.length > 50){
			referencia = referencia.substring(0,25) + '...';	
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

		if (nombre_pieza.length > 25){
			nombre_pieza = nombre_pieza.substring(0,25) + '...';	
		}
	
		window.opener.ref = enlace + referencia + fin_enlace + cadena_identificador;
		window.opener.prov = proveedor;
		window.opener.ref_prov = ref_proveedor;
		window.opener.nom_pieza = nombre_pieza;
		window.opener.num_uds = piezas;
		window.opener.pack_precio = p_precio;
		window.opener.cant = cantidad;
		window.opener.total_paquetes = tot_paquetes;
		window.opener.precio_unidad = p_unitario;
		window.opener.precio_referencia = p_referencia;
		
		window.opener.boton = boton;
		window.opener.id_ref = identificador;
		window.opener.addRow('mitablaRefsLibres',id_referencia);
		//window.close();
	}
	else alert("El campo PIEZAS tiene que ser un valor entero o un decimal con punto");
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

// Funcion para la validacion del campo piezas del buscador. 
// Solo se pueden introducir numeros enteros o decimales con punto.
function validarPiezas(a) {
	var j = 0;
	// Error si hay algun caracter o hay varios puntos
	var error = false;
	var digito = 0;
	var primer_caracter = false;
	var punto_reconocido = false;
	while (j<a.length && !error){
		// Si el primer caracter no es un digito entonces error = true;
		primer_caracter = parseInt(a[0]);
		if (isNaN(primer_caracter)) error = true;
		else {
			digito = parseInt(a[j]);
			if (isNaN(digito) && a[j] != ".") error = true;
			else if ((a[j] == "." && punto_reconocido)) error = true;
			if (a[j] == ".") punto_reconocido = true;
 		}
		j++;
	}
	if (a.length == 0) error = true;
	return error;
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
	<h3> Añadir referencia para la nueva Orden de Producción </h3>
	<div id="ContenedorBuscadorReferencias">
		<h4> Buscar la referencia para añadir a la nueva Orden de Produccion </h4>
   		<form name="BuscadorReferencias" id="BuscadorReferencias" action="buscador_referencias_libres.php" method="post">
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
		if ($mostrar_tabla){
		?>
   			<div class="CapaTablaReferencias">
    		<table>
        	<tr>
        		<th style="text-align:center">ID</th>
        		<th>NOMBRE</th>
            	<th>PROVEEDOR</th>
                <th>REF PROV</th>
            	<th>NOMBRE PIEZA</th>
                <th style="text-align:center">PIEZAS</th>
                <th style="text-align:center">PACK PRECIO</th>
                <th style="text-align:center">UDS/P</th>
                <th style="text-align:center">AÑADIR</th>
            </tr>
        	<?php
				$max_caracteres = 50;
				for($i=0;$i<count($resultadosBusqueda);$i++) {
					// Se cargan los datos de las referencias según su identificador
					$ref = new Referencia_Libre();
					$datoRef = $resultadosBusqueda[$i];
					$ref->cargaDatosReferenciaLibreId($datoRef["id_referencia"]);
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
							<input type="text" id="piezas-<?php echo $ref->id_referencia;?>" name="piezas-<?php echo $ref->id_referencia;?>" class="CampoPiezasInput" value="<?php echo $piezas;?>"/>
                 		</td>
                        <td style="text-align:center">
							<?php echo number_format($ref->pack_precio, 2, '.', ''); ?>
                            <input type="hidden" name="p_precio-<?php echo $ref->id_referencia;?>" id="p_precio-<?php echo $ref->id_referencia;?>" value="<?php echo number_format($ref->pack_precio, 2, '.', '');?>" />
                 		</td>
                        <td style="text-align:center">
							<?php echo $ref->cantidad; ?>
                            <input type="hidden" name="cantidad-<?php echo $ref->id_referencia;?>" id="cantidad-<?php echo $ref->id_referencia;?>" value="<?php echo $ref->cantidad;?>" />                            
                        </td>
                        <td style="text-align:center">
                            <form name="BuscadorReferenciaLibre" id="BuscadorReferenciaLibre" action="new_op.php?nombreref=<?php echo $ref->referencia;?>&id=<?php echo $ref->id_referencia;?>" method="post">
								<input type="button" onclick="add_referencia(<?php echo $ref->id_referencia;?>);" value="+" />
                                <input type="hidden" id="guardandoReferenciaLibre" name="guardandoReferenciaLibre" />
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
