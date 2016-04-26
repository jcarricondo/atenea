<?php
ini_set('session.gc_maxlifetime', '3600');
ini_set('max_execution_time', 3600);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Madrid');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
class MySQL {

	/* -----------------------------------------------------------------------------
	// PRODUCCION
	var $servidor = "localhost"; // Servidor de la base de datos
	var $user = "atenea"; // Usuario de la base de datos
	var $pass = "AteneaPrea07e0x"; // Contraseña del usuario
	var $dbname = "atenea"; // Tabla de la base de datos

	// DESARROLLO 
	var $servidor = "localhost"; // Servidor de la base de datos
	var $user = "ateneadev"; // Usuario de la base de datos
	var $pass = "AteneaPrea07e0x"; // Contraseña del usuario
	var $dbname = "ateneadev"; // Tabla de la base de datos

	// Servidor Local Oficina ATENADESLOCAL
	var $servidor = "192.168.1.40"; // Servidor de la base de datos
	var $user = "atenea"; // Usuario de la base de datos
	var $pass = "SmkAtenea087"; // Contraseña del usuario
	var $dbname = "atenea"; // Tabla de la base de datos

	// Servidor Local Casa
	var $servidor = "127.0.0.1"; // Servidor de la base de datos
	var $user = "root"; // Usuario de la base de datos
	var $pass = ""; // Contraseña del usuario
	var $dbname = "atenea"; // Tabla de la base de datos
	------------------------------------------------------------------------------ */

    var $servidor = "";
    var $user = "";
    var $pass = "";
    var $dbname = "";

	var $conexion = NULL; // Conexión
	var $errorSql = ""; // Error

	var $consulta = ""; // Consulta
	var $resultados = NULL; // Resultados
	var $query = NULL; // Última consulta ejecutada


	/* Constructor */
	function __construct() {

        switch (realpath($_SERVER["DOCUMENT_ROOT"])) {
            case 'C:\xampp\htdocs\proyectos\git\atenea':            // LOCAL OFICINA
                $this->servidor = "192.168.1.40";                   // Servidor de la base de datos
                $this->user = "atenea";                             // Usuario de la base de datos
                $this->pass = "SmkAtenea087";                       // Contraseña del usuario
                $this->dbname = "atenea";                           // Tabla de la base de datos
                break;
            case '/var/www/vhosts/ateneadev.simumak.com/httpdocs':  // DESARROLLO
                $this->servidor = "localhost";                      // Servidor de la base de datos
                $this->user = "ateneadev";                          // Usuario de la base de datos
                $this->pass = "AteneaPrea07e0x";                    // Contraseña del usuario
                $this->dbname = "ateneadev";                        // Tabla de la base de datos
                break;
            case '/var/www/vhosts/ateneapre.simumak.com/httpdocs':  // PREPRODUCCION
                $this->servidor = "localhost";                      // Servidor de la base de datos
                $this->user = "ateneapre";                          // Usuario de la base de datos
                $this->pass = "AteneaPrea07e0x";                    // Contraseña del usuario
                $this->dbname = "ateneapre";                        // Tabla de la base de datos
                break;
            case '/var/www/vhosts/atenea.simumak.com/httpdocs':     // PRODUCCION
                $this->servidor = "localhost";                      // Servidor de la base de datos
                $this->user = "atenea";                             // Usuario de la base de datos
                $this->pass = "AteneaPrea07e0x";                    // Contraseña del usuario
                $this->dbname = "atenea";                           // Tabla de la base de datos
                break;
            default:
                break;
        }

		$this->conectar();
	}


	/* Conecta a la base de datos */
	function conectar() {
		$this->conexion = mysql_pconnect($this->servidor,$this->user,$this->pass);

		if(!$this->conexion) {
			$this->errorSql = "Error al conectar al servidor: ".$this->servidor;
		} else {
			if(!mysql_select_db($this->dbname,$this->conexion)) {
				$this->errorSql = "No se puede abrir la tabla: ".$this->dbname;
			}
		}
	}

	/* Fija la consulta */
	function setConsulta($consulta) {
		$this->consulta = $consulta;
	}

	/* Devuelve la consulta fijada */
	function getConsulta() {
		return $this->consulta;
	}

	/* Ejecuta la consulta fijada */
	function ejecutarConsulta() {
		$this->query = mysql_query($this->consulta,$this->conexion) or die($this->errorSql = mysql_error());

		if($this->query) {
			$this->prepararResultados();
		}
	}

	/* Ejecuta sólo la consulta fijada, sin preparar los resultados, devuelve query para saber si la consulta fue bien */
	function ejecutarSoloConsulta() {
		$this->query = mysql_query($this->consulta,$this->conexion) or die($this->errorSql = mysql_error());
		return $this->query;
	}

	/* Prepara los resultados, los mete en un array */
	function prepararResultados() {
		$this->limpiarResultados();

		while($this->datos = mysql_fetch_assoc($this->query)) {
			$this->resultados[] = $this->datos;
		}
	}

	/* Devuelve los resultados en un array */
	function getResultados() {
		return $this->resultados;
	}

	/* Devuelve la primera fila de los resultados en un array */
	function getPrimerResultado() {
		return $this->resultados[0];
	}

	/* Devuelve la fila seleccionada de los resultados en un array */
	function getFilaSeleccionada($num) {
		return $this->resultados[$num];
	}

	/* Muestras los resultados bajo la etiqueta pre */
	function resultadosPre() {
		return '<pre>'.print_r($this->resultados).'</pre>';
	}

	/* Limpia los resultados */
	function limpiarResultados() {
		$this->resultados = NULL;
	}

	/* Cierra la conexión a la base de datos */
	function cerrarConexion() {
		if(!mysql_close($this->conexion)){
      		$this->errorSql = "No se ha podido cerrar la conexión: ". $this->cerrarConexion();
    	}
	}

	/* Devuelve el ID del último insert */
	function getUltimoID() {
		if($this->query) {
			return mysql_insert_id();
		}
	}

	/* Devuelve el número de filas en la última consulta */
	function getNumeroFilas() {
		if($this->query) {
			return mysql_num_rows($this->query);
		}
	}

	/* Devuelve el número de filas afectadas en la última consulta */
	function getFilasAfectadas() {
		if($this->query) {
			return mysql_affected_rows($this->query);
		}
	}

	/* Prepara el dato para la consulta */
	function makeValue($theValue,$theType, $theDefinedValue = "", $theNotDefinedValue = "") {
		$theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

		  switch ($theType) {
			case "text":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;
			case "long":
			  $theValue = ($theValue != "") ? $theValue : "NULL";
			  break;
			case "int":
			  $theValue = ($theValue != "") ? intval($theValue) : "NULL";
			  break;
			case "float":
			  $theValue = ($theValue != "") ? floatval($theValue) : "NULL";
			  break;
			case "double":
			  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
			  break;
			case "date":
			  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			  break;
			case "defined":
			  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			  break;
		  }
		  return $theValue;
	}

}
?>