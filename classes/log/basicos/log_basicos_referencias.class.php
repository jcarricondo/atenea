<?php
class LogBasicosReferencias extends MySQL {
	
	// Variables de la clase
	var $id_usuario;						// ID del usuario que realiza la operación
	var $proceso;							// Proceso de creación, modificación o eliminación de la referencia
	var $id_referencia;						// ID de la referencia
	var $referencia;						// Nombre de la referencia
	var $id_proveedor;						// ID del proveedor de la referencia
	var $id_fabricante;						// ID del fabricante de la referencia
	var $part_tipo;							// Tipo de la pieza
	var $part_nombre;						// Nombre de la pieza
	var $part_fabricante_referencia;		// Referencia del fabricante de la pieza
	var $part_proveedor_referencia;			// Referencia del proveedor de la pieza
	var $part_descripcion;					// Descripción de la pieza
	var $part_valor_nombre;					// Campo descriptivo número 1 de la pieza
	var $part_valor_cantidad; 				// Campo de valor número 1 de la pieza
	var $part_valor_nombre_2; 				// Campo descriptivo número 2 de la pieza
	var $part_valor_cantidad_2;				// Campo de valor número 2 de la pieza
	var $part_valor_nombre_3;				// Campo descriptivo número 3 de la pieza
	var $part_valor_cantidad_3;				// Campo de valor número 3 de la pieza
	var $part_valor_nombre_4;				// Campo descriptivo número 4 de la pieza
	var $part_valor_cantidad_4;				// Campo de valor número 4 de la pieza
	var $part_valor_nombre_5;				// Campo descriptivo número 5 de la pieza
	var $part_valor_cantidad_5;				// Campo de valor número 5 de la pieza
    var $pack_precio;						// Precio del paquete de la pieza
	var $unidades;							// Número de unidades por paquete de la pieza
	var $part_precio_cantidad;				// Precio por unidad de la pieza
	var $comentarios; 						// Comentarios respecto a la pieza
	var $fecha_creado;						// Fecha de creación de la pieza
	var $fecha_modificacion;				// Fecha de modificacion de la pieza
    var $referencia_creada;					// Campo que indica si se creó la referencia
	var $referencia_heredada;				// Campo que indica si la referencia es heredada
	var $referencia_compatible;				// Campo que indica si la referencia es compatible
	var $error;								// Indica si hubo error en el proceso
	var $codigo_error; 						// Indica un mensaje del error producido



	function setValores($id_usuario,$proceso,$id_referencia,$referencia,$id_proveedor,$id_fabricante,$part_tipo,$part_nombre,$part_fabricante_referencia,$part_proveedor_referencia,$part_descripcion,
						$part_valor_nombre,$part_valor_cantidad,$part_valor_nombre_2,$part_valor_cantidad_2,$part_valor_nombre_3,$part_valor_cantidad_3,$part_valor_nombre_4,$part_valor_cantidad_4,
						$part_valor_nombre_5,$part_valor_cantidad_5,$pack_precio,$unidades,$part_precio_cantidad,$comentarios,$fecha_creado,$fecha_modificacion,$referencia_creada,$referencia_heredada,
						$referencia_compatible,$error,$codigo_error) {

		$this->id_usuario = $id_usuario;
		$this->proceso = $proceso;
		$this->id_referencia = $id_referencia;
		$this->referencia = $referencia;
		$this->id_proveedor = $id_proveedor;
		$this->id_fabricante = $id_fabricante;
		$this->part_tipo = $part_tipo;
		$this->part_nombre = $part_nombre;
		$this->part_fabricante_referencia = $part_fabricante_referencia;
		$this->part_proveedor_referencia = $part_proveedor_referencia;
		$this->part_descripcion = $part_descripcion;
		$this->part_valor_nombre = $part_valor_nombre;
		$this->part_valor_cantidad = $part_valor_cantidad;
		$this->part_valor_nombre_2 = $part_valor_nombre_2;
		$this->part_valor_cantidad_2 = $part_valor_cantidad_2;
		$this->part_valor_nombre_3 = $part_valor_nombre_3;
		$this->part_valor_cantidad_3 = $part_valor_cantidad_3;
		$this->part_valor_nombre_4 = $part_valor_nombre_4;
		$this->part_valor_cantidad_4 = $part_valor_cantidad_4;
		$this->part_valor_nombre_5 = $part_valor_nombre_5;
		$this->part_valor_cantidad_5 = $part_valor_cantidad_5;
		$this->pack_precio = $pack_precio;
		$this->unidades = $unidades;
		$this->part_precio_cantidad = $part_precio_cantidad;
		$this->comentarios = $comentarios;
		$this->fecha_creado = $fecha_creado;
		$this->fecha_modificacion = $fecha_modificacion;
		$this->referencia_creada = $referencia_creada;
		$this->referencia_heredada = $referencia_heredada;
		$this->referencia_compatible = $referencia_compatible;
		$this->error = $error;
		$this->codigo_error = $codigo_error;
	}

	function guardarLog(){

		$insertSql = sprintf("insert into log_basicos_referencias (id_usuario,proceso,id_referencia,referencia,id_proveedor,id_fabricante,part_tipo,part_nombre,
						part_fabricante_referencia,part_proveedor_referencia,part_descripcion,part_valor_nombre,part_valor_cantidad,part_valor_nombre_2,
						part_valor_cantidad_2,part_valor_nombre_3,part_valor_cantidad_3,part_valor_nombre_4,part_valor_cantidad_4,part_valor_nombre_5,
						part_valor_cantidad_5,pack_precio,unidades,part_precio_cantidad,comentarios,fecha_creado,fecha_modificacion,referencia_creada,
						referencia_heredada,referencia_compatible,error,codigo_error) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,
						%s,%s,%s,%s,%s,%s,%s,current_timestamp,%s,%s,%s,%s,%s)",

						$this->makeValue($this->id_usuario, "int"),
						$this->makeValue($this->proceso, "text"),
						$this->makeValue($this->id_referencia, "int"),
						$this->makeValue($this->referencia, "text"),
						$this->makeValue($this->id_proveedor, "int"),
						$this->makeValue($this->id_fabricante, "int"),
						$this->makeValue($this->part_tipo, "text"),
						$this->makeValue($this->part_nombre, "text"),
						$this->makeValue($this->part_fabricante_referencia, "text"),
						$this->makeValue($this->part_proveedor_referencia, "text"),
						$this->makeValue($this->part_descripcion, "text"),
						$this->makeValue($this->part_valor_nombre, "text"),
						$this->makeValue($this->part_valor_cantidad, "text"),
						$this->makeValue($this->part_valor_nombre_2, "text"),
						$this->makeValue($this->part_valor_cantidad_2, "text"),
						$this->makeValue($this->part_valor_nombre_3, "text"),
						$this->makeValue($this->part_valor_cantidad_3, "text"),
						$this->makeValue($this->part_valor_nombre_4, "text"),
						$this->makeValue($this->part_valor_cantidad_4, "text"),
						$this->makeValue($this->part_valor_nombre_5, "text"),
						$this->makeValue($this->part_valor_cantidad_5, "text"),
						$this->makeValue($this->pack_precio, "text"),
						$this->makeValue($this->unidades, "text"),
						$this->makeValue($this->part_precio_cantidad, "text"),
						$this->makeValue($this->comentarios, "text"),
						$this->makeValue($this->fecha_creado, "date"),
						$this->makeValue($this->referencia_creada, "text"),
						$this->makeValue($this->referencia_heredada, "text"),
						$this->makeValue($this->referencia_compatible, "text"),
						$this->makeValue($this->error, "text"),
						$this->makeValue($this->codigo_error, "text"));

		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else {
			return 0;
		}	
	}
}
?>