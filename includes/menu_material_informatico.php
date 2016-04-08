<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'listado_informatica':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/entrada_informatica.php">Entrada</a>';
                  }
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/salida_informatica.php">Salida</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/albaranes_informatica.php">Albaranes</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/nuevo_material.php">Nuevo</a>';
                  }
              break;
              case 'listado_albaranes':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/entrada_informatica.php">Entrada</a>';
                  }
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/salida_informatica.php">Salida</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_informatica.php">Informática</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/listado_informatica.php">Listado</a>';
                  }
              break;
              case 'listado_movimientos':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/entrada_informatica.php">Entrada</a>';
                  }
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/salida_informatica.php">Salida</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_informatica.php">Informática</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/albaranes_informatica.php">Albaranes</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/listado_informatica.php">Listado</a>';
                  }
              break;
              case 'entrada_informatica':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/salida_informatica.php">Salida</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_informatica.php">Informática</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/albaranes_informatica.php">Albaranes</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/listado_informatica.php">Listado</a>';
                  }
              break;
              case 'salida_informatica':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/entrada_informatica.php">Entrada</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_informatica.php">Informática</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/albaranes_informatica.php">Albaranes</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/listado_informatica.php">Listado</a>';
                  }
              break;
              case 'nuevo_material':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/entrada_informatica.php">Entrada</a>';
                  }
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/salida_informatica.php">Salida</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/albaranes_informatica.php">Albaranes</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/listado_informatica.php">Listado</a>';
                  }
              break;
              case 'mod_material':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/entrada_informatica.php">Entrada</a>';
                  }
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/salida_informatica.php">Salida</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/albaranes_informatica.php">Albaranes</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/listado_informatica.php">Listado</a>';
                  }
              break;
              case 'stock_informatica':
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/entrada_informatica.php">Entrada</a>';
                  }
                  if(permisoMenu(39)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/salida_informatica.php">Salida</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/albaranes_informatica.php">Albaranes</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenu" href="../material_informatico/listado_movimientos.php">Movimientos</a>';
                  }
                  if(permisoMenu(38)){ 
                      echo '<a class="BotonMenuActualOP" href="../material_informatico/listado_informatica.php">Listado</a>';
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