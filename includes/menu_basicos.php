<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
        <?php
            if(permisoMenu(1)) { ?>
                <a class="BotonMenu" href="../basicos/proveedores.php">Proveedores</a>
                <a class="BotonMenu" href="../basicos/referencias.php">Referencias</a>
                <a class="BotonMenu" href="../basicos/cabinas.php">Cabinas</a>
                <a class="BotonMenu" href="../basicos/perifericos.php">Perifericos</a>
                <a class="BotonMenu" href="../basicos/kits.php">Kits</a>
                <a class="BotonMenu" href="../basicos/fabricantes.php">Fabricantes</a>
                <a class="BotonMenu" href="../basicos/familias.php">Familias</a>
                <a class="BotonMenu" href="../basicos/clientes.php">Clientes</a>
                <a class="BotonMenu" href="../basicos/usuarios.php">Usuarios</a>
        <?php
            }
            switch($pagina) {
                case 'proveedores':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_proveedor.php">Nuevo</a>';
                    break;
                case 'new_proveedor':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/proveedores.php">Listado</a>';
                    break;
                case 'mod_proveedor':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/proveedores.php">Listado</a>';
                    break;
                case 'referencias':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nueva_referencia.php">Nueva</a>';
                    if(permisoMenu(3)) echo '<a class="BotonMenuActual" href="../basicos/importar_referencias.php">Importar</a>';
                    if(permisoMenu(3)) echo '<a class="BotonMenuActual" href="../basicos/actualizar_precio_referencias.php">Act. Precio</a>';
                    break;
                case 'new_referencia':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/referencias.php">Listado</a>';
                    break;
                case 'mod_referencia':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/referencias.php">Listado</a>';
                    break;
                case 'nombres_de_productos':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_nombre_producto.php">Nuevo</a>';
                    break;
                case 'new_nombre_producto':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/nombres_de_productos.php">Listado</a>';
                    break;
                case 'mod_nombre_producto':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/nombres_de_productos.php">Listado</a>';
                    break;
                case 'cabinas':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nueva_cabina.php">Nueva</a>';
                    break;
                case 'new_cabina':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/cabinas.php">Listado</a>';
                    break;
                case 'mod_cabina':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/cabinas.php">Listado</a>';
                    break;
                case 'perifericos':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_periferico.php">Nuevo</a>';
                    break;
                case 'new_periferico':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/perifericos.php">Listado</a>';
                    break;
                case 'mod_periferico':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/perifericos.php">Listado</a>';
                    break;
                case 'kits':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_kit.php">Nuevo</a>';
                    break;
                case 'new_kit':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/kits.php">Listado</a>';
                    break;
                case 'mod_kit':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/kits.php">Listado</a>';
                    break;
                /* case 'softwares':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_software_simulacion.php">Nuevo</a>';
                    break;
                case 'new_software':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/software_simulacion.php">Listado</a>';
                    break;
                case 'mod_software':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/software_simulacion.php">Listado</a>';
                    break; */
                case 'fabricantes':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_fabricante.php">Nuevo</a>';
                    break;
                case 'new_fabricante':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/fabricantes.php">Listado</a>';
                    break;
                case 'mod_fabricante':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/fabricantes.php">Listado</a>';
                    break;
                case 'familias':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nueva_familia.php">Nueva</a>';
                    break;
                case 'new_familia':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/familias.php">Listado</a>';
                    break;
                case 'mod_familia':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/familias.php">Listado</a>';
                    break;
                case 'clientes':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_cliente.php">Nuevo</a>';
                    break;
                case 'new_cliente':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/clientes.php">Listado</a>';
                    break;
                case 'mod_cliente':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/clientes.php">Listado</a>';
                    break;
                case 'usuarios':
                    if(permisoMenu(5)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_usuario.php">Nuevo</a>';
                    break;
                case 'new_usuario':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/usuarios.php">Listado</a>';
                    break;
                case 'mod_usuario':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/usuarios.php">Listado</a>';
                    break;
                case 'direcciones':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nueva_direccion.php">Nueva</a>';
                    break;
                case 'new_direccion':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/direcciones.php">Listado</a>';
                    break;
                case 'mod_direccion':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/direcciones.php">Listado</a>';
                    break;
                case 'centros_logisticos':
                    if(permisoMenu(35)) echo '<a class="BotonMenuActual" href="../basicos/nuevo_centro_logistico.php">Nuevo</a>';
                    break;
                case 'new_centro_logistico':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/centros_logisticos.php">Listado</a>';
                    break;
                case 'mod_centro_logistico':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/centros_logisticos.php">Listado</a>';
                    break;
                case 'importar_referencias':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/referencias.php">Listado</a>';
                    break;
                case 'importar_referencias_componentes':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/referencias.php">Listado</a>';
                    break;
                case 'actualizar_precio_referencias':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/referencias.php">Listado</a>';
                    break;
                case 'plantillas_de_productos':
                    if(permisoMenu(2)) echo '<a class="BotonMenuActual" href="../basicos/nueva_plantilla_producto.php">Nuevo</a>';
                    break;
                case 'new_plantilla_producto':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/plantillas_de_productos.php">Listado</a>';
                    break;
                case 'mod_plantilla_producto':
                    if(permisoMenu(1)) echo '<a class="BotonMenuActual" href="../basicos/plantillas_de_productos.php">Listado</a>';
                    break;
                default:
                    # code...
                    break;
            }
        ?>
    </div>
    <?php include ("../includes/opciones_usuario.php"); ?>
</div>