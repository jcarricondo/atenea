<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php 
            switch ($pagina) {
              case 'pedidos':
                  if(permisoMenu(19)){ 
                      echo '<a class="BotonMenuActualOP" href="../pedidos/nuevo_pedido.php">Nuevo</a>';
                  }
                  if(permisoMenu(18)){ 
                      echo '<a class="BotonMenu" href="../pedidos/pedidos.php">Listado</a>';
                  }
              break;
              case 'new_pedido':
                  if(permisoMenu(19)){ 
                      echo '<a class="BotonMenu" href="../pedidos/nuevo_pedido.php">Nuevo</a>';
                  }
                  if(permisoMenu(18)){ 
                      echo '<a class="BotonMenuActualOP" href="../pedidos/pedidos.php">Listado</a>';
                  }
              break;
              case 'mod_pedido':
                  if(permisoMenu(19)){ 
                      echo '<a class="BotonMenu" href="../pedidos/nuevo_pedido.php">Nuevo</a>';
                  }
                  if(permisoMenu(18)){ 
                      echo '<a class="BotonMenuActualOP" href="../pedidos/pedidos.php">Listado</a>';
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