<?php
// Fichero que muestra la tabla html del producto (numero de serie y cliente)
include("../classes/basicos/cliente.class.php");
include("../classes/basicos/listado_clientes.class.php");

$id_producto = $_GET["id_producto"];
$db = new MySQL();
?>
<link rel="stylesheet" type="text/css" media="all" href="../css/style.css" />
<tr>
    <th>NUM SERIE</th>
    <th style="text-align:center">CLIENTE</th>
</tr>
<?php 
	for ($i=0;$i<$unidades;$i++) { 
		$id_producto = $Productos_OP->ids_productos[$i]["id_producto"];
		$producto = new Producto();
		$producto->dameNumSerie($id_producto);
		$producto->dameIdCliente($id_producto);
		$id_cliente = $producto->id_cliente["id_cliente"];
		$Cliente = new Cliente();
		$Cliente->cargaDatosClienteId($id_cliente);
		$nombre_cliente = $Cliente->nombre;
?>
<tr>
	<td><?php echo $producto->num_serie["num_serie"];?></td>
    <td style="text-align:center">
		<select id="cliente[]" name="cliente[]" class="ListadoClientesOP" >
         	<?php 
				$clientes = new listadoClientes();
				$clientes->prepararConsulta();
				$clientes->realizarConsulta();
				$resultado_clientes = $clientes->clientes;

				for($j=-1;$j<count($resultado_clientes);$j++) {
					$cliente = new Cliente();
					$datoCliente = $resultado_clientes[$j];
					$cliente->cargaDatosClienteId($datoCliente["id_cliente"]);
					echo '<option value="'.$cliente->id_cliente.'" '; if ($cliente->id_cliente == $id_cliente) echo 'selected="selected"'; echo '>'.$cliente->nombre.'</option>';
				}
			?>
        </select>
  	</td>
</tr>
<?php 
	}
?>



    
    


