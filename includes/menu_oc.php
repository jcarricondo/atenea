<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'ordenes_compra':
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
                  if(permisoMenu(14)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_compra/opciones.php">Opciones</a>';
                  }
              break;
              case 'new_orden_produccion_mantenimiento':
                  if(permisoMenu(15)){ 
                      echo '<a class="BotonMenu" href="../orden_compra/nueva_op_mantenimiento.php">Nueva OPM</a>';
                  }
                  if(permisoMenu(13)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_compra/ordenes_compra.php">Listado</a>';
                  }
              break;
              case 'confirm_new_orden_produccion_mantenimiento':
                  if(permisoMenu(15)){ 
                      echo '<a class="BotonMenu" href="../orden_compra/nueva_op_mantenimiento.php">Nueva OPM</a>';
                  }
                  if(permisoMenu(13)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_compra/ordenes_compra.php">Listado</a>';
                  }
              break;
              case 'mod_orden_compra':
                  if(permisoMenu(15)){ 
                      echo '<a class="BotonMenu" href="../orden_compra/nueva_op_mantenimiento.php">Nueva OPM</a>';
                  }
                  if(permisoMenu(13)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_compra/ordenes_compra.php">Listado</a>';
                  }
              break;
              case 'opciones':
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
                  if(permisoMenu(14)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_compra/ordenes_compra.php">Listado</a>';
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