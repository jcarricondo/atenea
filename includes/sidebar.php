<div id="MenuSidebar">
    <ul>
        <?php if(permisoMenu(8)){ ?>
        <li>
            &Oacute;RDENES DE PRODUCCI&Oacute;N
            <ul>
                <?php if(permisoMenu(8)) { ?><li><a href="../orden_produccion/ordenes_produccion.php">Listado</a></li><?php } ?>
                <?php if(permisoMenu(9)) { ?><li><a href="../orden_produccion/new_op.php">Nueva</a></li><?php } ?>
                <?php if(permisoMenu(12)) { ?><li><a href="../orden_produccion/gestionar_produccion_optimizado.php">Gestionar Producci&oacute;n</a></li><?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(permisoMenu(13)){ ?>
        <li>
            &Oacute;RDENES DE COMPRA
            <ul>
                <?php if(permisoMenu(13)) { ?><li><a href="../orden_compra/ordenes_compra.php">Listado</a></li><?php } ?>
                <?php if(permisoMenu(15)) { ?><li><a href="../orden_compra/nueva_op_mantenimiento.php">Mantenimiento</a></li><?php } ?>                
            </ul>
        </li>
        <?php } ?>
        <?php if(permisoMenu(16)){ ?>
        <li>
            FLOTA DE PRODUCTOS
            <ul>
                <?php if(permisoMenu(16)) { ?><li><a href="../productos/productos.php">Listado</a></li><?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(permisoMenu(18)){ ?>
        <li>
            PEDIDOS
            <ul>
                <?php if(permisoMenu(18)) { ?><li><a href="../pedidos/pedidos.php">Listado</a></li><?php } ?>
                <?php if(permisoMenu(19)) { ?><li><a href="../pedidos/nuevo_pedido.php">Nuevo</a></li><?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(permisoMenu(20)){ ?>
        <li>
            PRODUCCI&Oacute;N
            <ul>
                <?php if(permisoMenu(20)) { ?><li><a href="../producciones/escandallo_por_componentes.php">Ordenar Producci&oacute;n</a></li><?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(permisoMenu(21)){ ?>
        <li>
            ALMACEN
            <ul>
                <?php if(permisoMenu(21)) { ?><li><a href="../almacen/listado_material.php">Listado Material</a></li><?php } ?>
                <?php if(permisoMenu(22)) { ?><li><a href="../almacen/recepcion_material.php">Entrada Material</a></li><?php } ?>
                <?php if(permisoMenu(23)) { ?><li><a href="../almacen/desrecepcion_material.php">Salida Material</a></li><?php } ?>
                <?php if(permisoMenu(21)) { ?><li><a href="../almacen/albaranes.php">Albaranes</a></li><?php } ?>
                <?php if(permisoMenu(24)) { ?><li><a href="../almacen/ajuste_material.php">Ajuste Material</a></li><?php } ?>
                <?php if(permisoMenu(21)) { ?><li><a href="../almacen/listado_movimientos.php">Listado Movimientos</a></li><?php } ?>
            </ul>
        </li>
        <?php } ?>
        <?php if(permisoMenu(31)){ ?>
        <li>
            ALMACEN PERIF&Eacute;RICOS
            <ul>
                <?php if(permisoMenu(31)) { ?><li><a href="../almacen_perifericos/listado_perifericos.php">Listado Perif&eacute;ricos</a></li><?php } ?>
                <?php if(permisoMenu(29)) { ?><li><a href="../almacen_perifericos/recepcion_perifericos.php">Entrada Perif&eacute;ricos</a></li><?php } ?>
                <?php if(permisoMenu(30)) { ?><li><a href="../almacen_perifericos/desrecepcion_perifericos.php">Salida Perif&eacute;ricos</a></li><?php } ?>
                <?php if(permisoMenu(31)) { ?><li><a href="../almacen_perifericos/albaranes_perifericos.php">Albaranes</a></li><?php } ?>
                <?php if(permisoMenu(31)) { ?><li><a href="../almacen_perifericos/listado_movimientos.php">Listado Movimientos</a></li><?php } ?>
            </ul>  
        </li>
        <?php } ?>
        <?php if(permisoMenu(42)){ ?>
            <li>
                ALMACEN SIMULADORES
                <ul>
                    <?php if(permisoMenu(42)) { ?><li><a href="../almacen_simuladores/listado_simuladores.php">Listado Simuladores</a></li><?php } ?>
                    <?php if(permisoMenu(40)) { ?><li><a href="../almacen_simuladores/recepcion_simuladores.php">Entrada Simuladores</a></li><?php } ?>
                    <?php if(permisoMenu(41)) { ?><li><a href="../almacen_simuladores/desrecepcion_simuladores.php">Salida Simuladores</a></li><?php } ?>
                    <?php if(permisoMenu(42)) { ?><li><a href="../almacen_simuladores/albaranes_simuladores.php">Albaranes</a></li><?php } ?>
                    <?php if(permisoMenu(42)) { ?><li><a href="../almacen_simuladores/listado_movimientos.php">Listado Movimientos</a></li><?php } ?>
                </ul>
            </li>
        <?php } ?>
        <li>
            B&Aacute;SICOS
            <ul>
                <?php 
                    if(permisoMenu(1)) { ?>
                        <li><a href="../basicos/proveedores.php">Proveedores</a></li>
                        <li><a href="../basicos/referencias.php">Referencias</a></li>
                        <li><a href="../basicos/nombres_de_productos.php">Nombres de productos</a></li>
                        <li><a href="../basicos/plantillas_de_productos.php">Plantillas de productos</a></li>
                        <!--<li><a href="../basicos/cabinas.php">Cabinas</a></li>-->
                        <li><a href="../basicos/perifericos.php">Perif&eacute;ricos</a></li>
                        <li><a href="../basicos/kits.php">Kits</a></li>
                        <!--<li><a href="../basicos/software_simulacion.php">Software Simulaci&oacute;n</a>-->
                        <li><a href="../basicos/fabricantes.php">Fabricantes</a></li>
                        <li><a href="../basicos/familias.php">Familias</a></li>
                        <li><a href="../basicos/clientes.php">Clientes</a></li>
                        <li><a href="../basicos/usuarios.php">Usuarios</a></li>
                        <li><a href="../basicos/direcciones.php">Direcciones</a></li>
                        <li><a href="../basicos/centros_logisticos.php">Centros Log&iacute;sticos</a></li>
                <?php
                    }
                ?>
            </ul>
        </li>
        <?php //if(permisoMenu(33)) { ?><!--<li><a href="../orden_produccion/imputacion.php">IMPUTACIÓN DE HORAS</a></li>--><?php //} ?>
        <?php if(permisoMenu(38)) { ?>
        <li>
            MATERIAL INFORM&Aacute;TICO
            <ul>
                <?php if(permisoMenu(38)) { ?><li><a href="../material_informatico/listado_informatica.php">Listado Inform&aacute;tica</a></li><?php } ?>
                <?php if(permisoMenu(39)) { ?><li><a href="../material_informatico/entrada_informatica.php">Entrada Inform&aacute;tica</a></li><?php } ?>
                <?php if(permisoMenu(39)) { ?><li><a href="../material_informatico/salida_informatica.php">Salida Inform&aacute;tica</a></li><?php } ?>
                <?php if(permisoMenu(38)) { ?><li><a href="../material_informatico/albaranes_informatica.php">Albaranes</a></li><?php } ?>
                <?php if(permisoMenu(38)) { ?><li><a href="../material_informatico/listado_movimientos.php">Listado Movimientos</a></li><?php } ?>
                <?php if(permisoMenu(38)) { ?><li><a href="../material_informatico/stock_informatica.php">Stock</a></li><?php } ?>
            </ul>        
        </li>
        <?php } ?>
    </ul>
</div>
    