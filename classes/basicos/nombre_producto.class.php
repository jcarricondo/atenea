<?php
class Nombre_Producto extends MySql{
	
	var $id_nombre_producto;
	var $nombre; 
	var $codigo; 
	var $version;
	var $familia;

	var $fecha_creado;
	var $activo;
		
	function cargarDatos($id_nombre_producto,$nombre,$codigo,$version,$familia) {
		$this->id_nombre_producto = $id_nombre_producto;
		$this->nombre = $nombre;
		$this->codigo = $codigo;
		$this->version = $version;
		$this->familia = $familia;
	}
	
	function cargaDatosNombreProductoId($id_nombre_producto) {
		$consultaSql = sprintf("select *,familias.nombre_familia from nombre_producto inner join familias on (familias.id_familia=nombre_producto.id_familia) where id_nombre_producto=%s",
			$this->makeValue($id_nombre_producto, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_nombre_producto"],
			$resultados["nombre"],
			$resultados["codigo"],
			$resultados["version"],
			$resultados["nombre_familia"]
		);
	}
	
	// Se hace la carga de datos  del nuevo producto
	function datosNuevoProducto($id_nombre_producto = NULL,$nombre,$codigo,$version,$familia) {
		$this->id_nombre_producto = $id_nombre_producto;
		$this->nombre = $nombre;
		$this->codigo = $codigo;
		$this->version = $version;
		$this->familia = $familia;
	}

	// Guarda los cambios realizados en el producto
	function guardarCambios() {
		// Si el id_nombre_producto es NULL lo toma como un nuevo producto
		if($this->id_nombre_producto == NULL) {
			// Comprueba si hay otro producto con el mismo nombre
			if(!$this->comprobarProductoDuplicado()) {
				$consulta = sprintf("insert into nombre_producto (nombre,codigo,version,id_familia,fecha_creado,activo) value (%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->codigo, "text"),
					$this->makeValue($this->version, "text"),
					$this->makeValue($this->familia, "int"));

				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_nombre_producto = $this->getUltimoID();
					return 1;
				} else {
					return 3;
				}
			} else {
				return 2;
			}
		} else {
			if(!$this->comprobarProductoDuplicado()) {
				$consulta = sprintf("update nombre_producto set nombre=%s, codigo=%s, version=%s, id_familia=%s where id_nombre_producto=%s",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->codigo, "text"),
					$this->makeValue($this->version, "double"),
					$this->makeValue($this->familia, "int"),
					$this->makeValue($this->id_nombre_producto, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					return 1;
				} else {
					return 4;
				}
			} else {
				return 2;
			}
		}
	}
	
	// Comprueba si hay otro producto con el mismo nombre
	// Devuelve true si hay productos duplicados
	function comprobarProductoDuplicado() {
		if($this->id_nombre_producto == NULL) {
			$consulta = sprintf("select id_nombre_producto from nombre_producto where nombre=%s and version=%s and activo=1",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->version, "double"));
		} else {	
			$consulta = sprintf("select id_nombre_producto from nombre_producto where nombre=%s and version=%s and id_nombre_producto<>%s and activo=1",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->version, "double"),
				$this->makeValue($this->id_nombre_producto, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;	
		} else {
			return true;
		}
	}
	
	function eliminar(){
		//ELIMINAR
		$consulta = sprintf("update nombre_producto set nombre_producto.activo=0 where id_nombre_producto=%s",	
			$this->makeValue($this->id_nombre_producto, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} else {
			return 5;
		}
	}
	
	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe un producto con ese nombre y esa version en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo producto<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del producto<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar el nombre de producto</br>';
			break;	
		}
	}			
}
?>