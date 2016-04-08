<?php
session_start();
// Se guarda la desconexión del usuario
session_start();
session_unset();
session_destroy();
header("Location:../");
?>