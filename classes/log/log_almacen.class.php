<?php
class Log_Almacen extends MySql{
	
	var $id;
    var $id_usuario;
    var $usuario;
    var $tipo_usuario;
    var $proceso;
    var $id_almacen;
    var $id_albaran;
    var $id_referencia;
    var $cantidad_introducida;
    var $tipo_elemento;
    var $num_serie;
    var $estado;
    var $error;
    var $error_des;
    var $fecha_creado;


    function cargarDatos($id,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,$id_referencia,$cantidad_introducida,$tipo_elemento,$num_serie,$estado,$error,$error_des,$fecha_creado) {
		$this->id = $id;
		$this->id_usuario = $id_usuario;
        $this->usuario = $usuario;
        $this->tipo_usuario = $tipo_usuario;
        $this->proceso = $proceso;
        $this->id_almacen = $id_almacen;
        $this->id_albaran = $id_albaran;
        $this->id_referencia = $id_referencia;
        $this->cantidad_introducida = $cantidad_introducida;
        $this->tipo_elemento = $tipo_elemento;
        $this->num_serie = $num_serie;
        $this->estado = $estado;
        $this->error = $error;
        $this->error_des = $error_des;
		$this->fecha_creado = $fecha_creado;
	}
	
	function cargaDatosLogAlmacenId($id) {
		$consultaSql = sprintf("select * from log_almacen where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id"],
            $resultados["id_usuario"],
			$resultados["usuario"],
            $resultados["tipo_usuario"],
            $resultados["proceso"],
            $resultados["id_almacen"],
            $resultados["id_albaran"],
            $resultados["id_referencia"],
            $resultados["cantidad_introducida"],
            $resultados["tipo_elemento"],
            $resultados["num_serie"],
            $resultados["estado"],
            $resultados["error"],
            $resultados["error_des"],
			$resultados["fecha_creado"]
		);
	}

    // Guarda el log de la operación realizada en el módulo ALMACEN
	function guardarLog() {
        $consulta = sprintf("insert into log_almacen (id_usuario,usuario,tipo_usuario,proceso,id_almacen,id_albaran,id_referencia,cantidad_introducida,
                                                      tipo_elemento,num_serie,estado,error,error_des,fecha_creado)
                                values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,CURRENT_TIMESTAMP)",
                        $this->makeValue($this->id_usuario, "int"),
                        $this->makeValue($this->usuario, "text"),
                        $this->makeValue($this->tipo_usuario, "text"),
                        $this->makeValue($this->proceso, "text"),
                        $this->makeValue($this->id_almacen, "int"),
                        $this->makeValue($this->id_albaran, "int"),
                        $this->makeValue($this->id_referencia, "int"),
                        $this->makeValue($this->cantidad_introducida, "text"),
                        $this->makeValue($this->tipo_elemento, "text"),
                        $this->makeValue($this->num_serie, "text"),
                        $this->makeValue($this->estado, "text"),
                        $this->makeValue($this->error, "text"),
                        $this->makeValue($this->error_des, "text"));
        $this->setConsulta($consulta);
        if($this->ejecutarSoloConsulta()){
            return 1;
        }
        else return 2;
	}
	

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
                $mensaje_error = "Se produjo un error al guardar el log del usuario en la operación de ALMACEN";
			break;
		}
        return $mensaje_error;
	}			
}
?>