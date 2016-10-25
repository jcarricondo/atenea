<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'listado_material':
                  if(permisoMenu(22)){ 
                      echo '<a class="BotonMenu" href="../almacen/recepcion_material.php">Recepcion</a>';
                  }
                  if(permisoMenu(23)){ 
                      echo '<a class="BotonMenu" href="../almacen/desrecepcion_material.php">Desrecepcion</a>';
                  }
                  if(permisoMenu(24)){ 
                      echo '<a class="BotonMenu" href="../almacen/ajuste_material.php">Ajuste</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/albaranes.php">Albaranes</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen/listado_material.php">Listado</a>';
                  }
              break;
              case 'listado_albaranes':
                  if(permisoMenu(22)){ 
                      echo '<a class="BotonMenu" href="../almacen/recepcion_material.php">Recepcion</a>';
                  }
                  if(permisoMenu(23)){ 
                      echo '<a class="BotonMenu" href="../almacen/desrecepcion_material.php">Desrecepcion</a>';
                  }
                  if(permisoMenu(24)){ 
                      echo '<a class="BotonMenu" href="../almacen/ajuste_material.php">Ajuste</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_material.php">Material</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen/albaranes.php">Listado</a>';
                  }
              break;
              case 'listado_movimientos':
                  if(permisoMenu(22)){ 
                      echo '<a class="BotonMenu" href="../almacen/recepcion_material.php">Recepcion</a>';
                  }
                  if(permisoMenu(23)){ 
                      echo '<a class="BotonMenu" href="../almacen/desrecepcion_material.php">Desrecepcion</a>';
                  }
                  if(permisoMenu(24)){ 
                      echo '<a class="BotonMenu" href="../almacen/ajuste_material.php">Ajuste</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_material.php">Material</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/albaranes.php">Albaranes</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen/listado_movimientos.php">Listado</a>';
                  }
              break;
              case 'recepcion_material':
                  if(permisoMenu(23)){ 
                      echo '<a class="BotonMenu" href="../almacen/desrecepcion_material.php">Desrecepcion</a>';
                  }
                  if(permisoMenu(24)){ 
                      echo '<a class="BotonMenu" href="../almacen/ajuste_material.php">Ajuste</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_material.php">Material</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/albaranes.php">Albaranes</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(22)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen/recepcion_material.php">Recepcion</a>';
                  }
              break;
              case 'desrecepcion_material':
                  if(permisoMenu(22)){ 
                      echo '<a class="BotonMenu" href="../almacen/recepcion_material.php">Recepcion</a>';
                  }
                  if(permisoMenu(24)){ 
                      echo '<a class="BotonMenu" href="../almacen/ajuste_material.php">Ajuste</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_material.php">Material</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/albaranes.php">Albaranes</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(23)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen/desrecepcion_material.php">Desrecepcion</a>';
                  }
              break;
              case 'ajuste_material':
                  if(permisoMenu(22)){ 
                      echo '<a class="BotonMenu" href="../almacen/recepcion_material.php">Recepcion</a>';
                  }
                  if(permisoMenu(23)){ 
                      echo '<a class="BotonMenu" href="../almacen/desrecepcion_material.php">Desrecepcion</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_material.php">Material</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/albaranes.php">Albaranes</a>';
                  }
                  if(permisoMenu(21)){ 
                      echo '<a class="BotonMenu" href="../almacen/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(24)){ 
                      echo '<a class="BotonMenuActualOP" href="../almacen/ajuste_material.php">Ajuste</a>';
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