<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'listado_perifericos':
                  if(permisoMenu(29)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/recepcion_perifericos.php">Entrada</a>';
                  }
                  if(permisoMenu(30)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/desrecepcion_perifericos.php">Salida</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/albaranes_perifericos.php">Albaranes</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_perifericos/listado_perifericos.php">Listado</a>';
                  }
              break;
              case 'listado_albaranes':
                  if(permisoMenu(29)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/recepcion_perifericos.php">Entrada</a>';
                  }
                  if(permisoMenu(30)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/desrecepcion_perifericos.php">Salida</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_perifericos.php">Perifericos</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_perifericos/albaranes_perifericos.php">Listado</a>';
                  }
              break;
              case 'listado_movimientos':
                  if(permisoMenu(29)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/recepcion_perifericos.php">Entrada</a>';
                  }
                  if(permisoMenu(30)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/desrecepcion_perifericos.php">Salida</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_perifericos.php">Perifericos</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/albaranes_perifericos.php">Albaranes</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_perifericos/listado_movimientos.php">Listado</a>';
                  }
              break;
              case 'recepcion_perifericos':
                  if(permisoMenu(30)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/desrecepcion_perifericos.php">Salida</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_perifericos.php">Perifericos</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/albaranes_perifericos.php">Albaranes</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(29)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_perifericos/recepcion_perifericos.php">Entrada</a>';
                  }
              break;
              case 'desrecepcion_perifericos':
                  if(permisoMenu(29)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/recepcion_perifericos.php">Entrada</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_perifericos.php">Perifericos</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/albaranes_perifericos.php">Albaranes</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(30)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_perifericos/desrecepcion_perifericos.php">Salida</a>';
                  }
              break;
              case 'ficha_periferico':
                  if(permisoMenu(29)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/recepcion_perifericos.php">Entrada</a>';
                  }
                  if(permisoMenu(30)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/desrecepcion_perifericos.php">Salida</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_perifericos.php">Perifericos</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/albaranes_perifericos.php">Albaranes</a>';
                  }
                  if(permisoMenu(31)){ 
                      echo '<a class="BotonMenu" href="../almacen_perifericos/listado_movimientos.php">Movimientos</a>';
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