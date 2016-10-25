<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'productos':
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenu" href="../orden_produccion/ordenes_produccion.php">O. Produccion</a>';
                  }
                  if(permisoMenu(13)){ 
                      echo '<a class="BotonMenu" href="../orden_compra/ordenes_compra.php">O. Compra</a>';
                  }
                  if(permisoMenu(16)){ 
                      echo '<a class="BotonMenu" href="../productos/productos.php">Productos</a>';
                  }
                  if(permisoMenu(1)){ 
                      echo '<a class="BotonMenu" href="../basicos/proveedores.php">Basicos</a>';
                  }
              break;
              case 'ver_producto':
                  if(permisoMenu(16)){ 
                      echo '<a class="BotonMenuActualOP" href="../productos/productos.php">Listado</a>';
                  }
              break;
              default:
                # code...
              break;
            }
        ?>   
    </div> 
    <?php include ("../includes/opciones_usuario.php"); ?>
</div>