<?php
set_time_limit(10000);
// Script para importar los boxes de TORO y SMK
include("../../classes/mysql.class.php");
include("../../classes/funciones/log_unificacion.class.php");

$db = new MySql();
$log = new Log_Unificacion();

$insertSql = "INSERT INTO boxes (id_box,nombre,id_sede,activo) VALUES
				(1, 'INT 1', 2, 1),
				(2, 'INT 2', 2, 1),
				(3, 'INT 3', 2, 1),
				(4, 'INT 4', 2, 1),
				(5, 'INT 5', 2, 1),
				(6, 'INT 6', 2, 1),
				(7, 'INT 7', 2, 1),
				(8, 'INT 8', 2, 0),
				(9, 'INT 9', 2, 1),
				(10, 'INT 10', 2, 1),
				(11, 'INT 11', 2, 1),
				(12, 'INT 12', 2, 1),
				(13, 'INT 13', 2, 1),
				(14, 'INT 14', 2, 1),
				(15, 'INT 15', 2, 1),
				(16, 'INT 16', 2, 1),
				(17, 'INT 17', 2, 1),
				(18, 'INT 18', 2, 1),
				(19, 'INT 19', 2, 1),
				(20, 'INT 20', 2, 1),
				(21, 'BOX 1', 2, 1),
				(22, 'BOX 2', 2, 1),
				(23, 'BOX 3', 2, 1),
				(24, 'BOX 4', 2, 1),
				(25, 'BOX 5', 2, 1),
				(26, 'BOX 6', 2, 1),
				(27, 'BOX 7', 2, 1),
				(28, 'BOX 8', 2, 1),
				(29, 'BOX 9', 2, 1),
				(30, 'BOX 10', 2, 1),
				(31, 'BOX 11', 2, 1),
				(32, 'BOX 12', 2, 1),
				(33, 'BOX 13', 2, 1),
				(34, 'BOX 14', 2, 1),
				(35, 'BOX 15', 2, 1),
				(36, 'BOX 16', 2, 1),
				(37, 'BOX 17', 2, 1),
				(38, 'BOX 18', 2, 1),
				(39, 'BOX 19', 2, 1),
				(40, 'BOX 20', 2, 1),
				(41, 'BOX 21', 2, 1),
				(42, 'BOX 22', 2, 1),
				(43, 'BOX 23', 2, 1),
				(44, 'BOX 24', 2, 1),
				(45, 'BOX 25', 2, 1),
				(46, 'BOX 26', 2, 1),
				(47, 'BOX 27', 2, 1),
				(48, 'BOX 28', 2, 1),
				(49, 'BOX 29', 2, 1),
				(50, 'BOX 30', 2, 1),
				(51, 'BOX 31', 2, 1),
				(52, 'BOX 32', 2, 1),
				(53, 'BOX 33', 2, 1),
				(54, 'BOX 34', 2, 1),
				(55, 'BOX 35', 2, 1),
				(56, 'BOX 36', 2, 1),
				(57, 'BOX 37', 2, 1),
				(58, 'BOX 38', 2, 0),
				(59, 'BOX 39', 2, 1),
				(60, 'BOX 40', 2, 0),
				(61, 'BOX 41', 2, 1),
				(62, 'BOX 42', 2, 1),
				(63, 'BOX 43', 2, 1),
				(64, 'BOX 44', 2, 1),
				(65, 'INTEGRACIONES', 1, 1),
				(66, 'BOX 1', 1, 1),
				(67, 'BOX 2', 1, 1),
				(68, 'BOX 3', 1, 1),
				(69, 'BOX 4', 1, 1),
				(70, 'BOX 5', 1, 1),
				(71, 'BOX 6', 1, 1),
				(72, 'BOX 7', 1, 1),
				(73, 'BOX 8', 1, 1),
				(74, 'BOX 9', 1, 1),
				(75, 'BOX 10', 1, 1),
				(76, 'BOX 11', 1, 1),
				(77, 'BOX 12', 1, 1),
				(78, 'BOX 13', 1, 1)";

$db->setConsulta($insertSql);
if($db->ejecutarSoloConsulta()){
	// Insertamos el log
	$mensaje_log = '<span style="color: green;">Se han insertado los boxes</span><br/><br/>';
	$log->datosNuevoLog(NULL,"IMPORTAR_BOXES",$mensaje_log,$fecha);
	$res_log = $log->guardarLog();
	if($res_log == 1){
		echo $mensaje_log;
	}
	else echo 'Se produjo un error al guardar el LOG';
}
else{
	echo '<span style="color: red;">Se produjo un error al insertar los boxes</span><br/>';	
}

?>
