<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'ordenes_produccion':
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenu" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
                  }
                  if(permisoMenu(9)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/nueva_orden_produccion.php">Nueva</a>';
                  }
              break;
              case 'new_orden_produccion':
                  if(permisoMenu(9)){ 
                      echo '<a class="BotonMenu" href="../orden_produccion/nueva_orden_produccion.php">Nueva</a>';
                  }
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
                  }
              break;
              case 'confirm_new_orden_produccion':
                  if(permisoMenu(9)){ 
                      echo '<a class="BotonMenu" href="../orden_produccion/nueva_orden_produccion.php">Nueva</a>';
                  }
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
                  }
              break;
              case 'mod_orden_produccion':
                  if(permisoMenu(9)){ 
                      echo '<a class="BotonMenu" href="../orden_produccion/nueva_orden_produccion.php">Nueva</a>';
                  }
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
                  }
              break;
              case 'confirm_mod_orden_produccion':
                  if(permisoMenu(9)){ 
                      echo '<a class="BotonMenu" href="../orden_produccion/nueva_orden_produccion.php">Nueva</a>';
                  }
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
                  }
              break;
              case 'ver_orden_produccion':
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
                  }
              break;
              case 'gestionar_produccion':
                  if(permisoMenu(8)){ 
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
                  }
              break;
              case 'iniciar_orden_produccion':
                  if(permisoMenu(9)){
                      echo '<a class="BotonMenu" href="../orden_produccion/nueva_orden_produccion.php">Nueva</a>';
                  }
                  if(permisoMenu(8)){
                      echo '<a class="BotonMenuActualOP" href="../orden_produccion/ordenes_produccion.php">Listado</a>';
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