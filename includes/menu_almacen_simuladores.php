<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'listado_simuladores':
                  if(permisoMenu(40)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/recepcion_simuladores.php">Entrada</a>';
                  }
                  if(permisoMenu(41)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/desrecepcion_simuladores.php">Salida</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/albaranes_simuladores.php">Albaranes</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_simuladores/listado_simuladores.php">Listado</a>';
                  }
              break;
              case 'listado_albaranes':
                  if(permisoMenu(40)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/recepcion_simuladores.php">Entrada</a>';
                  }
                  if(permisoMenu(41)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/desrecepcion_simuladores.php">Salida</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_simuladores.php">Simuladores</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_simuladores/albaranes_simuladores.php">Listado</a>';
                  }
              break;
              case 'listado_movimientos':
                  if(permisoMenu(40)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/recepcion_simuladores.php">Entrada</a>';
                  }
                  if(permisoMenu(41)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/desrecepcion_simuladores.php">Salida</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_simuladores.php">Simuladores</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/albaranes_simuladores.php">Albaranes</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_simuladores/listado_simuladores.php">Listado</a>';
                  }
              break;
              case 'recepcion_simuladores':
                  if(permisoMenu(41)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/desrecepcion_simuladores.php">Salida</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_simuladores.php">Simuladores</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/albaranes_simuladores.php">Albaranes</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(40)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_simuladores/recepcion_simuladores.php">Entrada</a>';
                  }
              break;
              case 'desrecepcion_simuladores':
                  if(permisoMenu(40)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/recepcion_simuladores.php">Entrada</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_simuladores.php">Simuladores</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/albaranes_simuladores.php">Albaranes</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(41)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen_simuladores/desrecepcion_simuladores.php">Salida</a>';
                  }
              break;
              case 'ficha_simulador':
                  if(permisoMenu(40)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/recepcion_simuladores.php">Entrada</a>';
                  }
                  if(permisoMenu(41)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/desrecepcion_simuladores.php">Salida</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_simuladores.php">Simuladores</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/albaranes_simuladores.php">Albaranes</a>';
                  }
                  if(permisoMenu(42)){ 
                      echo '<a class="BotonMenu" href="../almacen_simuladores/listado_movimientos.php">Movimientos</a>';
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