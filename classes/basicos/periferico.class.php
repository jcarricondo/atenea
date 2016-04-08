<?php
class Periferico extends MySQL{

	var $id_componente;
	var $periferico;
	var $referencia;
	var $version;
	var $descripcion;
	var $estado;
	var $prototipo;
	var $referencias;
	var $piezas;
	var $total_paquetes;
	var $precio_pack;
	var $id_tipo;
	var $nombre_archivo;
	var $ids_archivos;
	var $nombres_archivos;
	var $interfaces;
	var $kits;

	function cargarDatos($id_componente,$periferico,$referencia,$version,$descripcion,$id_tipo,$estado,$prototipo) {
		$this->id_componente = $id_componente;
		$this->periferico = $periferico;
		$this->referencia = $referencia;
		$this->version = $version;
		$this->descripcion = $descripcion;
		$this->estado = $estado;
		$this->prototipo = $prototipo;
		$this->id_tipo = $id_tipo;
	}

	function cargaDatosPerifericoId($id_componente) {
		$consultaSql = sprintf("select * from componentes where id_componente=%s ",
		$this->makeValue($id_componente, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_componente"],
			$resultados["nombre"],
			$resultados["referencia"],
			$resultados["version"],
			$resultados["descripcion"],
			$resultados["id_tipo"],
			$resultados["estado"],
			$resultados["prototipo"]
		);
	}

	// Se comprueba si el componente existe en la base de datos
	function getExisteComponente($nombre,$version,$tipo) {
		$consultaSql = sprintf("select id_componente from componentes where nombre=%s and version=%s and id_tipo=%s and activo=1",
			$this->makeValue($nombre, "text"),
			$this->makeValue($version, "float"),
			$this->makeValue($tipo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id_componente"];
	}

	// Se crea el componente desde la carga de referencias de perifericos
	function crearComponenteImport($nombre,$version,$tipo) {
		$consultaSql = sprintf("insert into componentes (nombre,referencia,version,id_tipo,estado,fecha_creacion,activo) values (%s,%s,%s,%s,'BORRADOR',current_timestamp,1)",
			$this->makeValue($nombre, "text"),
			$this->makeValue($nombre, "text"),
			$this->makeValue($version, "float"),
			$this->makeValue($tipo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $this->getUltimoID();
	}

	// Se comprueba si ya existe la referencia en el componente
	function buscaReferenciaComponente($id_componente,$id_referencia) {
		$consultaSql = sprintf("select id from componentes_referencias where id_componente=%s and id_referencia=%s and activo=1",
			$this->makeValue($id_componente, "int"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id"];
	}

	// Se inserta la referencia en el componente
	function insertaReferenciaComponenteImport($id_componente,$id_referencia,$cantidad,$precio) {
		$consultaSql = sprintf("insert into componentes_referencias (id_componente,id_referencia,uds_paquete,piezas,total_paquetes,pack_precio,fecha_creado,activo) values (%s,%s,'1',%s,'1',%s,current_timestamp,1)",
			$this->makeValue($id_componente, "int"),
			$this->makeValue($id_referencia, "int"),
			$this->makeValue($cantidad, "float"),
			$this->makeValue($precio, "float"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $this->getUltimoID();
	}

	// Se actualiza la referencia en el componente
	function actualizaReferenciaComponenteImport($id,$id_componente,$id_referencia,$cantidad,$precio) {
		$consultaSql = sprintf("update componentes_referencias set piezas=%s, pack_precio=%s where id=%s",
			$this->makeValue($cantidad, "float"),
			$this->makeValue($precio, "float"),
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $id;
	}

	function cargarDatosArchivosPeriferico($id_archivo,$id_tipo,$id_componente,$nombre_archivo,$fecha_subida) {
		$this->id_archivo = $id_archivo;
		$this->id_tipo = $id_tipo;
		$this->id_componente = $id_componente;
		$this->nombre_archivo = $nombre_archivo;
		$this->fecha_subida = $fecha_subida;
	}


	function cargaDatosArchivosPerifericoId($id) {
		$consultaSql = sprintf("select * from componentes_archivos where id_archivo=%s ",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatosArchivosPeriferico(
			$resultados["id_archivo"],
			$resultados["id_tipo"],
			$resultados["id_componente"],
			$resultados["nombre_archivo"],
			$resultados["fecha_subida"]
		);
	}

	// Se hace la carga de datos  del nuevo periferico
	function datosNuevoPeriferico($id_componente = NULL,$nombre,$referencia,$descripcion,$version,$referencias,$piezas,$id_tipo = 2,$nombre_archivo,$interfaces,$estado,$prototipo,$kits) {
		$this->id_componente = $id_componente;
		$this->nombre = $nombre;
		$this->referencia = $referencia;
		$this->descripcion = $descripcion;
		$this->version = $version;
		$this->referencias = $referencias;
		$this->id_tipo = $id_tipo;
		$this->piezas = $piezas;
		$this->nombre_archivo = $nombre_archivo;
		$this->interfaces = $interfaces;
		$this->estado = $estado;
		$this->prototipo = $prototipo;
		$this->kits = $kits;
	}

	// Guarda los cambios realizados en el periferico
	function guardarCambios() {
		// Si el id_componente es NULL lo toma como un nuevo periferico
		if($this->id_componente == NULL) {
			// Comprueba si hay otro periferico con el mismo nombre
			if(!$this->comprobarPerifericoDuplicado()) {
				$consulta = sprintf("insert into componentes (nombre,referencia,descripcion,version,id_tipo,estado,prototipo,fecha_creacion,activo) value (%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->referencia, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->version, "float"),
					$this->makeValue($this->id_tipo, "int"),
					$this->makeValue($this->estado, "text"),
					$this->makeValue($this->prototipo, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					$this->id_componente = $this->getUltimoID();

					if($this->id_componente != NULL){
						for($i=0;$i<count($this->referencias);$i++){
							// Primero hacemos una consulta sobre el campo unidades de la referencia
							$consulta_cantidad_referencia = sprintf("select referencias.unidades from referencias where referencias.id_referencia=%s ",
								$this->makeValue($this->referencias[$i], "int"));
							$this->setConsulta($consulta_cantidad_referencia);
							$this->ejecutarConsulta();
							$resultados_referencia = $this->getPrimerResultado();

							// Obtenemos el contenido del array
							$cantidad_referencia = current ($resultados_referencia);
							if($cantidad_referencia == NULL) { $cantidad_referencia = 1; }
							$this->calculaTotalPaquetes($cantidad_referencia,$this->piezas[$i]);

							// Ahora hacemos una consulta sobre el campo pack_precio de la referencia
							$consulta_pack_precio = sprintf("select referencias.pack_precio from referencias where referencias.id_referencia=%s ",
								$this->makeValue($this->referencias[$i], "int"));
							$this->setConsulta($consulta_pack_precio);
							$this->ejecutarConsulta();
							$resultados_pack_precio = $this->getPrimerResultado();

							$precio_pack = current ($resultados_pack_precio);
							$this->precio_pack = $precio_pack;

							// Insertamos en componentes_referencia las referencias asociadas a ese id_componente
							$consulta = sprintf("insert into componentes_referencias (componentes_referencias.id_componente,componentes_referencias.id_referencia,componentes_referencias.uds_paquete,componentes_referencias.piezas,componentes_referencias.total_paquetes,componentes_referencias.pack_precio,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,current_timestamp,1)",
								$this->makeValue($this->id_componente, "int"),
								$this->makeValue($this->referencias[$i], "int"),
								$this->makeValue($cantidad_referencia, "int"),
								$this->makeValue($this->piezas[$i], "float"),
								$this->makeValue($this->total_paquetes, "int"),
								$this->makeValue($this->precio_pack,"float"));
							$this->setConsulta($consulta);
							$this->ejecutarSoloConsulta();
						}

						// Insertamos las interfaces que tenga asociado el periferico
						for($i=0;$i<count($this->interfaces);$i++){
							$consulta = sprintf("insert into componentes_interfaces(id_tipo_componente,id_componente,id_interfaz,fecha_creado,activo) value(2,%s,%s,current_timestamp,1)",
								$this->makeValue($this->id_componente, "int"),
								$this->makeValue($this->interfaces[$i], "int"));
							$this->setConsulta($consulta);
							$this->ejecutarSoloConsulta($consulta);
						}

						// Insertamos los kits que tenga asociado el periferico
						for($i=0;$i<count($this->kits);$i++){
							$consulta = sprintf("insert into componentes_kits(id_tipo_componente,id_componente,id_kit,fecha_creado,activo) value(2,%s,%s,current_timestamp,1)",
								$this->makeValue($this->id_componente, "int"),
								$this->makeValue($this->kits[$i], "int"));
							$this->setConsulta($consulta);
							$this->ejecutarSoloConsulta($consulta);
						}

						// Ahora insertamos los datos de los archivos en la tabla componentes_archivos
						$i=0;
						$fallo = false;
						while($i<count($this->nombre_archivo) and (!$fallo) ) {
							$consulta_archivos = sprintf("insert into componentes_archivos (id_tipo, id_componente, nombre_archivo, fecha_subida, activo) value (2,%s,%s,current_timestamp,1)",
    							$this->makeValue($this->id_componente, "int"),
    							$this->makeValue($this->nombre_archivo[$i], "text"));
							$this->setConsulta($consulta_archivos);
							if($this->ejecutarSoloConsulta()) {
								$i++;
							}
							else $fallo = true;
						}
						if(!$fallo) return 1;
						else return 9;
					}
				} 
				else {
					return 3;
				}
			} 
			else {
				return 2;
			}
		}
		else {
			if(!$this->comprobarPerifericoDuplicado()) {
				$consulta = sprintf("update componentes set nombre=%s, referencia=%s, descripcion=%s, version=%s, id_tipo=%s, estado=%s, prototipo=%s where id_componente=%s ",
					$this->makeValue($this->nombre, "text"),
					$this->makeValue($this->referencia, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->version, "float"),
					$this->makeValue($this->id_tipo, "int"),
					$this->makeValue($this->estado, "text"),
					$this->makeValue($this->prototipo, "int"),
					$this->makeValue($this->id_componente, "int"));
				$this->setConsulta($consulta);
				if($this->ejecutarSoloConsulta()) {
					// Eliminar todas las referencias de componentes_referencias asociadas a ese componente.
					$consultaBorrarReferencias = sprintf("update componentes_referencias set activo=0 where id_componente=%s",
						$this->makeValue($this->id_componente, "int"));
					$this->setConsulta($consultaBorrarReferencias);
					$this->ejecutarSoloConsulta();

					// Insertar al final de la tabla de componentes_referencias las nuevas referencias asociadas a ese componente.
					for($i=0;$i<count($this->referencias);$i++){
						// Primero hacemos una consulta sobre el campo unidades de la referencia
						$consulta_cantidad_referencia = sprintf("select referencias.unidades from referencias where referencias.id_referencia=%s",
							$this->makeValue($this->referencias[$i], "int"));
						$this->setConsulta($consulta_cantidad_referencia);
						$this->ejecutarConsulta();
						$resultados_referencia = $this->getPrimerResultado();

						// Obtenemos el contenido del array
						$cantidad_referencia = current ($resultados_referencia);
						if($cantidad_referencia == NULL) { $cantidad_referencia = 1; }
						$this->calculaTotalPaquetes($cantidad_referencia,$this->piezas[$i]);

						// Ahora hacemos una consulta sobre el campo pack_precio de la referencia
						$consulta_pack_precio = sprintf("select referencias.pack_precio from referencias where referencias.id_referencia=%s",
							$this->makeValue($this->referencias[$i], "int"));

						$this->setConsulta($consulta_pack_precio);
						$this->ejecutarConsulta();

						$resultados_pack_precio = $this->getPrimerResultado();
						$precio_pack = current ($resultados_pack_precio);
						$this->precio_pack = $precio_pack;

						// Insertamos en componentes_referencia las referencias asociadas a ese id_componente
						$consulta = sprintf("insert into componentes_referencias (id_componente,id_referencia,uds_paquete,piezas,total_paquetes,pack_precio,fecha_creado,activo) value (%s,%s,%s,%s,%s,%s,current_timestamp,1)",
							$this->makeValue($this->id_componente, "int"),
							$this->makeValue($this->referencias[$i], "int"),
							$this->makeValue($cantidad_referencia, "int"),
							$this->makeValue($this->piezas[$i], "float"),
							$this->makeValue($this->total_paquetes, "int"),
							$this->makeValue($this->precio_pack, "float"));
						$this->setConsulta($consulta);
						$this->ejecutarSoloConsulta($consulta);
					}

					// Eliminar las interfaces asociadas a esa componente
					$consultaBorrarInterfaces = sprintf("update componentes_interfaces set activo=0 where componentes_interfaces.id_componente=%s ",
							$this->makeValue($this->id_componente, "int"));
						$this->setConsulta($consultaBorrarInterfaces);
						$this->ejecutarSoloConsulta();

					// Insertamos las interfaces que tenga asociada el periferico
					for($i=0;$i<count($this->interfaces);$i++){
						$consulta = sprintf("insert into componentes_interfaces(id_tipo_componente,id_componente,id_interfaz,fecha_creado,activo) value(2,%s,%s,current_timestamp,1)",
							$this->makeValue($this->id_componente, "int"),
							$this->makeValue($this->interfaces[$i], "int"));
						$this->setConsulta($consulta);
						$this->ejecutarSoloConsulta($consulta);
					}

					// Eliminar los kits asociados a ese componente
					$consultaBorrar = sprintf("update componentes_kits set activo=0 where componentes_kits.id_componente=%s ",
							$this->makeValue($this->id_componente, "int"));
						$this->setConsulta($consultaBorrar);
						$this->ejecutarSoloConsulta();

					// Insertamos los kits que tenga asociado el periferico
					for($i=0;$i<count($this->kits);$i++){
						$consulta = sprintf("insert into componentes_kits(id_tipo_componente,id_componente,id_kit,fecha_creado,activo) value(2,%s,%s,current_timestamp,1)",
							$this->makeValue($this->id_componente, "int"),
							$this->makeValue($this->kits[$i], "int"));
						$this->setConsulta($consulta);
						$this->ejecutarSoloConsulta($consulta);
					}

					// Insertamos en componentes_archivos los archivos asociados a ese id_componente
					// Si hay archivos que insertar
					if(!empty($this->nombre_archivo)) {
						$i=0;
						$fallo = false;
						while ($i<count($this->nombre_archivo) and (!$fallo) ) {
							$consulta_archivos = sprintf("insert into componentes_archivos (id_tipo, id_componente, nombre_archivo, fecha_subida, activo) value (2,%s,%s,current_timestamp,1)",
    							$this->makeValue($this->id_componente, "int"),
    							$this->makeValue($this->nombre_archivo[$i], "text"));
							$this->setConsulta($consulta_archivos);
							if ($this->ejecutarSoloConsulta()) {
    							$i++;
							}
							else $fallo = true;
						}
						if (!$fallo) return 1;
						else return 9;
					}
					else return 1;
				return 1;
				}
				else {
					return 4;
				}
			} 
			else {
				return 2;
			}
		}
	}

	// Comprueba si hay otro periferico con el mismo nombre
	// Devuelve true si hay perifericos duplicados
	function comprobarPerifericoDuplicado() {
		if($this->id_componente == NULL) {
			$consulta = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=2",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->version, "float"));
		} else {
			$consulta = sprintf("select id_componente from componentes where nombre=%s and version=%s and activo=1 and id_tipo=2 and id_componente<>%s",
				$this->makeValue($this->nombre, "text"),
				$this->makeValue($this->version, "float"),
				$this->makeValue($this->id_componente, "int"));
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
		$consulta = sprintf("update componentes set componentes.activo=0 where id_tipo=2 and id_componente=%s",
			$this->makeValue($this->id_componente, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 6;
		} 
		else {
			return 5;
		}
	}

	function eliminarReferenciasPeriferico(){
		// Eliminar todas las referencias de componentes_referencias asociadas a ese componente.
		$consulta_refs_borrar = sprintf ("select count(id) as Total from componentes_referencias where componentes_referencias.id_componente=%s",
			$this->makeValue($this->id_componente, "int"));
		$this->setConsulta($consulta_refs_borrar);
		$this->ejecutarConsulta();
		$num_ref_borrar = current ($this->getPrimerResultado());
		if($num_ref_borrar != 0){
			for($i=0;$i< $num_ref_borrar;$i++){
				$consultaBorrarReferencias = sprintf("update componentes_referencias set componentes_referencias.activo=0 where componentes_referencias.id_componente=%s",
					$this->makeValue($this->id_componente, "int"));
				$this->setConsulta($consultaBorrarReferencias);
				if($this->ejecutarSoloConsulta()) {
					return 7;
				} 
				else {
					return 8;
				}
			}
		} 
		else return 7;
	}

	function calculaTotalPaquetes($cantidad_referencia,$num_pieza){
		if ($num_pieza<$cantidad_referencia) {
			$this->total_paquetes = 1;
		}
		else {
			$resto = fmod($num_pieza,$cantidad_referencia);
			$tot_paquetes = floor($num_pieza/$cantidad_referencia);
			if ($resto == 0) $this->total_paquetes = $tot_paquetes;
			else $this->total_paquetes = $tot_paquetes + 1;
		}
	}

	function dameId_archivo($id_componente) {
		$consulta = sprintf("select id_archivo from componentes_archivos where componentes_archivos.id_componente=%s and componentes_archivos.activo=1 ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_archivos = $this->getResultados();
	}

	function dameNombres_archivos($id_componente) {
		$consulta = sprintf("select nombre_archivo from componentes_archivos where componentes_archivos.id_componente=%s and componentes_archivos.activo=1 ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->nombres_archivos = $this->getResultados();
	}

	// Funcion que elimina un archivo cuando se quita de la tabla de archivos de modificacion periferico
	function quitarArchivo($nom_archivo,$id_componente) {
		$consulta = sprintf("update componentes_archivos set activo=0 where componentes_archivos.id_componente=%s and componentes_archivos.nombre_archivo=%s ",
			$this->makeValue($id_componente, "int"),
			$this->makeValue($nom_archivo, "text"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 10;
	}

	// Funcion que elimina los archivos cuando se elimina un periferico
	function quitarArchivoPeriferico($id_componente) {
		$consulta = sprintf("update componentes_archivos set activo=0 where componentes_archivos.id_componente=%s ",
			$this->makeValue($id_componente, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 10;
	}

	// Funcion que comprueba si se han modificado la tabla de archivos
	function compruebaTablaArchivos($id_componente,$archivos_tabla){
		// Consulta para comprobar que los nombres de la tabla son los mismos que los de la base de datos
		// Comprobamos que los nombres de la base de datos estan en archivos_tabla. Si alguno no esta ponemos su activo a cero.
		$this->dameNombres_archivos($id_componente);
		$nombres = $this->nombres_archivos;
		// Los guardamos en un array simple
		for($i=0;$i<count($nombres);$i++) {
			$nombres_bbdd[]= $nombres[$i]["nombre_archivo"];
		}
		// Comprobamos que los archivos de la base de datos estan en archivos tabla. Si alguno no esta, ponemos su activo a cero.
		for($i=0;$i<count($nombres_bbdd);$i++) {
			$encontrado = false;
			$j=0;
			while(($j<count($archivos_tabla)) and (!$encontrado)) {
				$encontrado = $archivos_tabla[$j] == $nombres_bbdd[$i];
				$j++;
			}
			// Si no esta en la tabla es que se ha eliminado y por tanto procedemos a poner su activo a 0;
			if(!$encontrado) {
				$resultado = $this->quitarArchivo($nombres_bbdd[$i],$id_componente);
				if($resultado != 1) {
					return $resultado;
				}
			}
		} 
		if($nombres_bbdd == NULL || $encontrado) $resultado = 1;
		return $resultado;	
	}


	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe un periferico con ese nombre y esa versión en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar el nuevo periferico<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos del periferico<br/>';
			break;
			case 5;
				return 'Se produjo un error al eliminar el periferico<br/>';
			break;
			case 8;
				return 'Se produjo un error al eliminar las referencias del periferico<br/>';
			break;
			case 9;
				return 'Se produjo un error al insertar los archivos de mecanica del periferico<br/>';
			break;
			case 10:
				return 'Se produjo un error al desactivar un archivo adjunto del periferico<br/>';
			break;
		}
	}
}
?>