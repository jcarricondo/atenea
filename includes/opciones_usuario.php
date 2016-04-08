<div class="CapaBotonCerrarSesion">
   	<div class="TituloUsuario">
       	Usuario:
    </div>
    <div id="UsuarioSesion">
      	<a href="../usuario/perfil.php?id=<?php echo $ateneaUser->id_usuario?>">
           	<?php echo $ateneaUser->usuario;?>
        </a>
    </div>
    <div id="BotonCerrarSesion">
      	<a href="../usuario/logout.php" >
       		Cerrar sesion
       	</a>
    </div>
</div>