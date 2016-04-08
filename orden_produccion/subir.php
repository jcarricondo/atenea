<?php 
	$uploaddir = "uploads/"; 
	$uploadfile = $uploaddir . basename($_FILES['archivo']['name']); 
	$error = $_FILES['archivo']['error']; 
	$subido = false; 
	if(isset($_POST['boton']) && $error==UPLOAD_ERR_OK) { 
		$subido = copy($_FILES['archivo']['tmp_name'], $uploadfile); 
	} 
	if($subido) { 
		echo "El archivo subio con exito"; 
	}
	else { 
		echo "Se ha producido un error: ".$error; 
	} 
?>
