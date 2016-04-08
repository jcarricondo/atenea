<?php
// Este fichero muestra el listado de los usuarios 
include("../includes/sesion.php");
include("../classes/funciones/funciones.class.php");
include("../classes/basicos/usuario.class.php");
include("../classes/basicos/listado_usuarios.class.php");
include("../classes/sede/sede.class.php");
include("../classes/almacen/almacen.class.php");
include("../classes/control_usuario.class.php");
permiso(1);

$controlUser = new Control_Usuario();
$user = new Usuario();
$usuarios = new listadoUsuarios();
$funciones = new Funciones();
$sede = new Sede();
$almacen = new Almacen();

$id_tipo_usuario = $_SESSION["AT_id_tipo_usuario"];
$id_almacen_usuario = $_SESSION["AT_id_almacen"];
$id_sede_usuario = $controlUser->dameSedeSegunUsuario($id_tipo_usuario,$id_almacen_usuario);

// Comprobamos si es usuario de Brasil para mostrar la hora correcta
$esUsuarioBrasil = $controlUser->esUsuarioBrasil($id_tipo_usuario,$id_sede_usuario);

// Se obtienen los datos del formulario
if($_GET["user"] == "creado" or $_GET["user"] == "modificado") {
	$realizarBusqueda = 1;
}
if(isset($_GET["realizandoBusqueda"]) and $_GET["realizandoBusqueda"] == 1 or $realizarBusqueda == 1) {
    $mostrar_tabla = true;
	$usuario = addslashes($_GET["usuario"]);
	$email = addslashes($_GET["email"]);
    $fecha_desde = $_GET["fecha_desde"];
	$fecha_hasta = $_GET["fecha_hasta"];
    $id_tipo_usuario_buscador = $_GET["tipos"];
    $id_almacen = $_GET["almacenes"];

	// Convierte la fecha a formato MySql
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaMy($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaMy($fecha_hasta);

	// Se pasan los datos del buscador a la clase del listado y se realiza la consulta a la base de datos
	$usuarios->setValores($usuario,$email,$id_tipo_usuario_buscador,$fecha_desde,$fecha_hasta,$id_almacen);
    $usuarios->realizarConsulta();
	$resultadosBusqueda = $usuarios->usuarios;
    $num_resultados = count($resultadosBusqueda);
	
	// Convierte la fecha a formato HTML
	if($fecha_desde != "") $fecha_desde = $funciones->cFechaNormal($fecha_desde);
	if($fecha_hasta != "") $fecha_hasta = $funciones->cFechaNormal($fecha_hasta);

    // Según el tipo de usuario seleccionado mostrarmos o no los almacenes
	$filtro_almacen_fabrica = ($id_tipo_usuario_buscador == 3) || ($id_tipo_usuario_buscador == 6);
    $filtro_almacen_mantenimiento = ($id_tipo_usuario_buscador == 4) || ($id_tipo_usuario_buscador == 7);
    $filtro_almacen = $filtro_almacen_fabrica || $filtro_almacen_mantenimiento;

    // Guardar las variables del formulario en variable de sesión
	$_SESSION["usuario_usuario"] = stripslashes(htmlspecialchars($usuario));
	$_SESSION["email_usuario"] = stripslashes(htmlspecialchars($email));
	$_SESSION["fecha_desde_usuario"] = $fecha_desde;
	$_SESSION["fecha_hasta_usuario"] = $fecha_hasta;
    $_SESSION["id_tipo_usuario_usuario"] = $id_tipo_usuario_buscador;
    $_SESSION["id_almacen_usuario_usuario"] = $id_almacen;
}

$titulo_pagina = "Básicos > Usuarios";
$pagina = "usuarios";
include ("../includes/header.php");
echo '<script type="text/javascript" src="../js/basicos/usuarios.js"></script>';
?>

<div class="separador"></div> 
<?php include("../includes/menu_basicos.php");?>

<div id="ContenedorCentral">
	<div id="ContenedorSidebar">
    	<?php include ("../includes/sidebar.php"); ?>
    </div> 
    
    <h3>Usuarios</h3>
    <h4>Buscar usuario</h4>
    
    <form id="BuscadorUsuario" name="buscadorUsuario" action="usuarios.php" method="get" class="Buscador">
    <table style="border:0;">
    	<tr style="border:0;">
        	<td>
            	<div class="Label">Nombre</div>
                <input type="text" id="usuario" name="usuario" class="BuscadorInput" value="<?php echo $_SESSION["usuario_usuario"]; ?>" />
            </td>
            <td>
            	<div class="Label">Email</div>
                <input type="text" id="email" name="email" class="BuscadorInput" value="<?php echo $_SESSION["email_usuario"]; ?>" />
            </td>
            <td>
                <div class="Label">Tipo</div>
                <select id="tipos" name="tipos" class="BuscadorInput" style="width: 245px;" onchange="cargaAlmacen(this.value)">
                    <option value=""></option>
                <?php 
                    $res_tipos = $user->dameTiposUsuario();
                    for($i=0;$i<count($res_tipos);$i++){ 
                        $id_tipo_user = $res_tipos[$i]["id"];
                        $tipo = utf8_encode($res_tipos[$i]["tipo"]); 

                        echo '<option value="'.$id_tipo_user.'"';
                        if($_SESSION["id_tipo_usuario_usuario"] == $id_tipo_user) echo ' selected="selected"';
                        echo '>'.$tipo.'</option>';
                    }
                ?>            
                </select>
            </td>
        </tr>
        <tr style="border:0;">
        	<td>
               	<div class="Label">Fecha desde</div>
           		<input type="text" name="fecha_desde" id="datepicker_usuarios_desde" class="fechaCal" value="<?php echo $_SESSION["fecha_desde_usuario"];?>"/>
            </td>
            <td>
            	<div class="Label">Fecha hasta</div>
           		<input type="text" name="fecha_hasta" id="datepicker_usuarios_hasta" class="fechaCal" value="<?php echo $_SESSION["fecha_hasta_usuario"];?>"/>
            </td>
            <td>
                <div id="capaAlmacen">
                    <?php 
                        // Se filtró por ALMACEN
                        if($filtro_almacen){ ?>
                            <div class="Label">Almacen *</div>
                            <select id="almacenes" name="almacenes" class="BuscadorInput" style="width: 245px;">
                                <option value=""></option>
                        <?php 
                            if($filtro_almacen_fabrica) $res_almacenes = $almacen->dameAlmacenesFabrica();
                            if($filtro_almacen_mantenimiento) $res_almacenes = $almacen->dameAlmacenesMantenimiento();
                            for($i=0;$i<count($res_almacenes);$i++){
                                $id_almacen = $res_almacenes[$i]["id_almacen"];
                                $nombre = ($res_almacenes[$i]["almacen"]); ?>
                                <option <?php if($_SESSION["id_almacen_usuario_usuario"] == $id_almacen) { ?> selected="selected" <?php } ?> value="<?php echo $id_almacen; ?>"><?php echo $nombre; ?></option>
                        <?php 
                            }        
                        ?>
                            </select>
                    <?php
                        }
                    ?>
                </div>
            </td>    
        </tr>
        <tr style="border:0;">
        	<td colspan="2">
            	<input type="hidden" id="realizandoBusqueda" name="realizandoBusqueda" value="1" />
                <input type="submit" value="Buscar" />
            </td>
        </tr>
    </table>
   	<br />
    </form>
    <div class="ContenedorBotonCrear">
		<?php
            if($_GET["user"] == "creado") {
                echo '<div class="mensaje">El usuario se ha creado correctamente</div>';
            }
	        if($_GET["user"] == "modificado") {
                if($_GET["password_changed"] == 1){
		            echo '<div class="mensaje">El usuario se ha modificado correctamente y se actualizó su password</div>';
                }
                else {
                    echo '<div class="mensaje">El usuario se ha modificado correctamente</div>';
                }
		    }
            if($mostrar_tabla){
                if($num_resultados == NULL or $num_resultados == 0){
                   echo '<div class="mensaje">No se encontraron usuarios</div>';
                   $mostrar_tabla = false;
                }
                else if ($num_resultados == 1){
                    echo '<div class="mensaje">Se encontró 1 usuario</div>';
                }
                else{
                    echo '<div class="mensaje">Se encontraron '.$num_resultados.' usuarios</div>';
                }   
            }
		?>
     </div>            
                
     <?php 
	 	if($mostrar_tabla){ ?>
       		<div class="CapaTabla">
     			<table>
     			<tr>
      				<th>NOMBRE</th>
            		<th>EMAIL</th>
                    <th>TIPO</th>
                    <th>SEDE</th>
                    <th>ALMACEN</th>
            		<th style="text-align:center">FECHA CREACION</th>
            		<th style="text-align:center">FECHA LOGIN</th>
        		</tr> 
        		
                <?php
				// Se cargan los datos de los usuarios según su identificador
				for($i=0;$i<count($resultadosBusqueda);$i++) {
                    $datoUsuario = $resultadosBusqueda[$i];
					$user->cargaDatosUsuarioId($datoUsuario["id_usuario"]);
                    $id_tipo = $user->id_tipo;
                    $id_almacen = $user->id_almacen;
                    $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo);
                    $tipo_user = $res_tipo_usuario["tipo"];

                    // Obtenemos la sede del usuario del listado
                    $id_sede = $controlUser->dameSedeSegunUsuario($id_tipo,$id_almacen);
                    $coinciden_sede = $id_sede_usuario == $id_sede;

                    $sede->cargaDatosSedeId($id_sede);
                    $nombre_sede = $sede->nombre;
                    if(empty($nombre_sede)) $nombre_sede = "-";
                    
                    if($esUsuarioBrasil){
                        $fecha_creado = $user->fechaBrasil($user->fecha_creado);
                        $fecha_login = $user->fechaHoraBrasil($user->fecha_login);
                    }
                    else{
                        $fecha_creado = $user->fechaSpain($user->fecha_creado);
                        $fecha_login = $user->fechaHoraSpain($user->fecha_login);
                    }

                    // Cargamos el nombre del almacen
                    $almacen->cargaDatosAlmacenId($id_almacen);
                    $nombre_almacen = utf8_decode($almacen->nombre);

                    if($nombre_almacen == NULL) $nombre_almacen = "-"; ?>

                    <tr>
						<td>
                            <?php
                                if(permisoMenu(6)){
                                    // Comprobamos si el usuario de la sesion tiene permiso de modificación sobre los demas usuarios
                                    if($controlUser->controlarUsuario($id_tipo_usuario,$id_tipo,$coinciden_sede)){ ?>
                                        <a href="mod_usuario.php?id=<?php echo $user->id_usuario; ?>"><?php echo $user->usuario; ?></a>    
                            <?php 
                                    }
                                    else echo $user->usuario;
                                } 
                                else echo $user->usuario; 
                            ?>
						</td>
						<td><?php echo $user->email; ?></td>
                        <td><?php echo utf8_encode($tipo_user);?></td>
                        <td><?php echo $nombre_sede;?></td>
                        <td><?php echo utf8_encode($nombre_almacen);?></td>
                        <td style="text-align:center"><?php echo $fecha_creado; ?></td>
                        <td style="text-align:center"><?php echo $fecha_login; ?></td>
					</tr> 
					<?php
				}
				?>
        	 	</table>         
	 		</div>
     <?php 
		}
	 ?>	
</div>      
<?php include ("../includes/footer.php"); ?>