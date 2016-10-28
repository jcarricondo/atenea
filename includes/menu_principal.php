<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
      <?php 
          if(permisoMenu(8)){ ?>
            <a class="BotonMenu" href="../orden_produccion/ordenes_produccion.php">O. Producción</a>
      <?php
          }
          if(permisoMenu(13)){ ?>
            <a class="BotonMenu" href="../orden_compra/ordenes_compra.php">O. Compra</a>
      <?php 
          }
          if(permisoMenu(16)){ ?>
            <a class="BotonMenu" href="../productos/productos.php">Productos</a>
      <?php
          }
          if(permisoMenu(18)){ ?>
            <a class="BotonMenu" href="../pedidos/pedidos.php">Pedidos</a>
      <?php
          }
          if(permisoMenu(20)){ ?>
            <a class="BotonMenu" href="../producciones/escandallo_por_componentes.php">Producciones</a>
      <?php 
          }
          if(permisoMenu(21)){ ?>
            <a class="BotonMenu" href="../almacen/listado_material.php">Almacen</a>
      <?php
          }
          if(permisoMenu(31)){ ?>
            <a class="BotonMenu" href="../almacen_perifericos/listado_perifericos.php">A. Periféricos</a>
      <?php
          }
          if(permisoMenu(1)){ ?>
            <a class="BotonMenu" href="../basicos/proveedores.php">Básicos</a>
      <?php
          }
      ?>
    </div> 
    <?php include ("../includes/opciones_usuario.php"); ?>
</div>