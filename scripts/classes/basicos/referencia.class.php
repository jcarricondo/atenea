<?php
class Referencia extends MySQL {

	var $id_referencia;
	var $referencia;
	var $proveedor;
	var $fabricante;
	var $part_tipo;
	var $part_nombre;
	var $part_fabricante_referencia;
	var $part_proveedor_referencia;
	var $part_descripcion;
	var $part_valor_nombre;
	var $part_valor_cantidad;
	var $part_valor_nombre_2;
	var $part_valor_cantidad_2;
	var $part_valor_nombre_3;
	var $part_valor_cantidad_3;
	var $part_valor_nombre_4;
	var $part_valor_cantidad_4;
	var $part_valor_nombre_5;
	var $part_valor_cantidad_5;
	var $pack_precio;
	var $part_precio_cantidad;
	var $unidades;
	var $comentarios;
	var $nombre_proveedor;
	var $nombre_fabricante;
	var $unidades_stock;
	var $unidades_entrada;

	var $id_proveedor;
	var $id_fabricante;

	var $nombre_archivo;
	var $id_archivo;
	var $fecha_subida;
	// $ids_archivos guarda los ids de la tabla de referencias_archivos de una referencia en particular.
	var $ids_archivos;
	var $nombres_archivos; // array de nombres que tiene una referencia

	var $resultados_componentes;
	var $total_paquetes;


	function cargarDatos($id_referencia,$referencia,$fabricante,$proveedor,$part_nombre,$part_tipo,$part_proveedor_referencia,$part_fabricante_referencia,$part_valor_nombre,$part_valor_cantidad,$part_valor_nombre_2,$part_valor_cantidad_2,$part_valor_nombre_3,$part_valor_cantidad_3,$part_valor_nombre_4,$part_valor_cantidad_4,$part_valor_nombre_5,$part_valor_cantidad_5,$pack_precio,$unidades,$part_descripcion,$comentarios,$pack_precio_cantidad,$nombre_proveedor,$nombre_fab,$unidades_stock,$unidades_entrada) {
		$this->id_referencia = $id_referencia;
		$this->referencia = $referencia;
		$this->fabricante = $fabricante;
		$this->proveedor = $proveedor;
		$this->part_nombre = $part_nombre;
		$this->part_tipo = $part_tipo;
		$this->part_proveedor_referencia = $part_proveedor_referencia;
		$this->part_fabricante_referencia = $part_fabricante_referencia;
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
		$this->unidades_stock = round($unidades_stock);
		$this->unidades_entrada = round($unidades_entrada);

		$this->part_descripcion = $part_descripcion;
		$this->comentarios = $comentarios;
		$this->part_precio_cantidad = $part_precio_cantidad;

		$this->nombre_proveedor = $nombre_proveedor;
		$this->nombre_fabricante = $nombre_fab;
	}

	function cargaDatosReferenciaId($id_referencia) {
		$consultaSql = sprintf("select referencias.*,proveedores.nombre_prov,fabricantes.nombre_fab,(select sum(piezas) from stock where id_referencia=referencias.id_referencia) as unidades_stock,(select sum((total_piezas-piezas_recibidas)) as unidades_entrada from orden_compra_referencias where id_referencia=referencias.id_referencia and activo=1 group by id_referencia) as unidades_entrada from referencias inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) inner join fabricantes on (fabricantes.id_fabricante=referencias.id_fabricante) where referencias.id_referencia=%s",
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_referencia"],
			$resultados["referencia"],
			$resultados["id_fabricante"],
			$resultados["id_proveedor"],
			$resultados["part_nombre"],
			$resultados["part_tipo"],
			$resultados["part_proveedor_referencia"],
			$resultados["part_fabricante_referencia"],
			$resultados["part_valor_nombre"],
			$resultados["part_valor_cantidad"],
			$resultados["part_valor_nombre_2"],
			$resultados["part_valor_cantidad_2"],
			$resultados["part_valor_nombre_3"],
			$resultados["part_valor_cantidad_3"],
			$resultados["part_valor_nombre_4"],
			$resultados["part_valor_cantidad_4"],
			$resultados["part_valor_nombre_5"],
			$resultados["part_valor_cantidad_5"],
			$resultados["pack_precio"],
			$resultados["unidades"],
			$resultados["part_descripcion"],
			$resultados["comentarios"],
			$resultados["part_precio_cantidad"],
			$resultados["nombre_prov"],
			$resultados["nombre_fab"],
			$resultados["unidades_stock"],
			$resultados["unidades_entrada"]
		);
	}

	// Se comprueba si la referencia existe en la base de datos
	function getExisteReferencia($num_ref,$id_proveedor) {
		$consultaSql = sprintf("select id_referencia from referencias where part_proveedor_referencia=%s and id_proveedor=%s and activo=1",
			$this->makeValue($num_ref, "text"),
			$this->makeValue($id_proveedor, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		return $resultados["id_referencia"];
	}

	// Se crea la referencia desde la carga de referencias de perifericos
	function crearReferenciaImport($num_ref,$nombre_pieza,$id_proveedor,$id_fabricante,$precio) {
		$consultaSql = sprintf("insert into referencias (referencia,id_proveedor,id_fabricante,part_tipo,part_nombre,part_fabricante_referencia,part_proveedor_referencia,part_descripcion,unidades,pack_precio,fecha_creado,activo) values (%s,%s,%s,'-',%s,%s,%s,'-',1,%s,current_timestamp,1)",
			$this->makeValue($num_ref, "text"),
			$this->makeValue($id_proveedor, "int"),
			$this->makeValue($id_fabricante, "int"),
			$this->makeValue($nombre_pieza, "text"),
			$this->makeValue($num_ref, "text"),
			$this->makeValue($num_ref, "text"),
			$this->makeValue($precio, "float"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $this->getUltimoID();
	}

	// Se actualiza la referencia desde la carga de referencias de perifericos
	function actualizarReferenciaImport($id_referencia,$num_ref,$nombre_pieza,$id_proveedor,$id_fabricante,$precio) {
		if($precio != "") {
			$updatePrecio = ", pack_precio='".$precio."'";
		}
		$consultaSql = sprintf("update referencias set referencia=%s, id_proveedor=%s, id_fabricante=%s, part_nombre=%s, part_fabricante_referencia=%s, part_proveedor_referencia=%s".$updatePrecio." where id_referencia=%s",
			$this->makeValue($num_ref, "text"),
			$this->makeValue($id_proveedor, "int"),
			$this->makeValue($id_fabricante, "int"),
			$this->makeValue($nombre_pieza, "text"),
			$this->makeValue($num_ref, "text"),
			$this->makeValue($num_ref, "text"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $id_referencia;
	}

	function cargarDatosArchivosReferencia($id_archivo,$id_referencia,$nombre_archivo,$fecha_subida) {
		$this->id_archivo = $id_archivo;
		$this->id_referencia = $id_referencia;
		$this->nombre_archivo = $nombre_archivo;
		$this->fecha_subida = $fecha_subida;
	}


	function cargaDatosArchivosReferenciaId($id) {
		$consultaSql = sprintf("select * from referencias_archivos where id_archivo=%s ",
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatosArchivosReferencia(
			$resultados["id_archivo"],
			$resultados["id_referencia"],
			$resultados["nombre_archivo"],
			$resultados["fecha_subida"]
		);
	}


	// Se hace la carga de datos de una nueva referencia
	function datosNuevaReferencia($id_referencia = NULL,$nombre,$fabricante,$proveedor,$nombre_pieza,$tipo_pieza,$ref_proveedor_pieza,$ref_fabricante_pieza,$part_value_name,$part_value_qty,$part_value_name_2,$part_value_qty_2,$part_value_name_3,$part_value_qty_3,$part_value_name_4,$part_value_qty_4,$part_value_name_5,$part_value_qty_5,$pack_precio,$unidades,$nombre_archivo,$comentarios) {
		$this->id_referencia = $id_referencia;
		$this->referencia = $nombre;
		$this->fabricante = $fabricante;
		$this->proveedor = $proveedor;
		$this->part_nombre = $nombre_pieza;
		$this->part_tipo = $tipo_pieza;
		$this->part_proveedor_referencia = $ref_proveedor_pieza;
		$this->part_fabricante_referencia = $ref_fabricante_pieza;
		$this->part_valor_nombre = $part_value_name;
		$this->part_valor_cantidad = $part_value_qty;
		$this->part_valor_nombre_2 = $part_value_name_2;
		$this->part_valor_cantidad_2 = $part_value_qty_2;
		$this->part_valor_nombre_3 = $part_value_name_3;
		$this->part_valor_cantidad_3 = $part_value_qty_3;
		$this->part_valor_nombre_4 = $part_value_name_4;
		$this->part_valor_cantidad_4 = $part_value_qty_4;
		$this->part_valor_nombre_5 = $part_value_name_5;
		$this->part_valor_cantidad_5 = $part_value_qty_5;
		$this->pack_precio = $pack_precio;
		$this->unidades = $unidades;
		$this->nombre_archivo = $nombre_archivo;
		$this->comentarios = $comentarios;
	}

	function datosReferencia($id_referencia,$nombre,$fabricante,$proveedor,$nombre_pieza,$tipo_pieza,$ref_proveedor_pieza,$ref_fabricante_pieza,$part_value_name,$part_value_qty,$pack_precio,$unidades,$comentarios) {
		$this->id_referencia = $id_referencia;
		$this->referencia = $nombre;
		$this->fabricante = $fabricante;
		$this->proveedor = $proveedor;
		$this->part_nombre = $nombre_pieza;
		$this->part_tipo = $tipo_pieza;
		$this->part_proveedor_referencia = $ref_proveedor_pieza;
		$this->part_fabricante_referencia = $ref_fabricante_pieza;
		$this->part_valor_nombre = $part_value_name;
		$this->part_valor_cantidad = $part_value_qty;
		$this->pack_precio = $pack_precio;
		$this->unidades = $unidades;
		$this->comentarios = $comentarios;
	}

	// Guarda los cambios realizados en la referencia
	function guardarCambios() {
		// Si el id_componente es NULL lo toma como una nueva referencia
		if($this->id_referencia == NULL) {
			// Comprueba si hay otra referencia con el mismo nombre
			if(!$this->comprobarReferenciaDuplicada()) {
				if(!$this->comprobarReferenciaProveedorDuplicada()){
					if(!$this->comprobarReferenciaFabricanteDuplicada()){
						$consulta = sprintf("insert into referencias (referencia, id_proveedor,id_fabricante,part_tipo,part_nombre,part_fabricante_referencia,part_proveedor_referencia,part_valor_nombre,part_valor_cantidad,part_valor_nombre_2,part_valor_cantidad_2,part_valor_nombre_3,part_valor_cantidad_3,part_valor_nombre_4,part_valor_cantidad_4,part_valor_nombre_5,part_valor_cantidad_5,pack_precio,unidades,comentarios,fecha_creado,activo) 																																																												value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
                    		$this->makeValue($this->referencia, "text"),
							$this->makeValue($this->proveedor, "int"),
							$this->makeValue($this->fabricante, "int"),
							$this->makeValue($this->part_tipo, "text"),
							$this->makeValue($this->part_nombre, "text"),
							$this->makeValue($this->part_fabricante_referencia, "text"),
							$this->makeValue($this->part_proveedor_referencia, "text"),
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
							$this->makeValue($this->pack_precio, "float"),
							$this->makeValue($this->unidades, "text"),
							$this->makeValue($this->comentarios, "text"));
						$this->setConsulta($consulta);
						if($this->ejecutarSoloConsulta()) {
							$this->id_referencia = $this->getUltimoID();
							// Ahora insertamos los datos de los archivos en la tabla referencias_archivos
							$i=0;
							$fallo = false;
							while ($i<count($this->nombre_archivo) and (!$fallo) ) {
								$consulta_archivos = sprintf("insert into referencias_archivos (id_referencia, nombre_archivo, fecha_subida, activo) value (%s,%s,current_timestamp,1)",
    								$this->makeValue($this->id_referencia, "int"),
    								$this->makeValue($this->nombre_archivo[$i], "text"));
								$this->setConsulta($consulta_archivos);
								if ($this->ejecutarSoloConsulta()) {
									$i++;
								}
								else $fallo = true;
							}

							if (!$fallo) return 1;
							else return 8;

						} else {
							return 3;
						}
					} else {
						return 12;
					}
				}
				else {
					return 13;
				}
			}
			else {
				return 2;
			}
		} else {
			if(!$this->comprobarReferenciaDuplicada()) {
				if(!$this->comprobarReferenciaProveedorDuplicada()){
					if(!$this->comprobarReferenciaFabricanteDuplicada()){
						$consulta = sprintf("update referencias set referencia=%s, id_proveedor=%s, id_fabricante=%s, part_tipo=%s, part_nombre=%s, part_fabricante_referencia=%s, part_proveedor_referencia=%s, part_valor_nombre=%s, part_valor_cantidad=%s, part_valor_nombre_2=%s, part_valor_cantidad_2=%s, part_valor_nombre_3=%s, part_valor_cantidad_3=%s, part_valor_nombre_4=%s, part_valor_cantidad_4=%s, part_valor_nombre_5=%s, part_valor_cantidad_5=%s,pack_precio=%s, unidades=%s, comentarios=%s where id_referencia=%s",
							$this->makeValue($this->referencia, "text"),
							$this->makeValue($this->proveedor, "int"),
							$this->makeValue($this->fabricante, "int"),
							$this->makeValue($this->part_tipo, "text"),
							$this->makeValue($this->part_nombre, "text"),
							$this->makeValue($this->part_fabricante_referencia, "text"),
							$this->makeValue($this->part_proveedor_referencia, "text"),
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
							$this->makeValue($this->pack_precio, "float"),
							$this->makeValue($this->unidades, "text"),
							$this->makeValue($this->comentarios, "text"),
							$this->makeValue($this->id_referencia, "int"));
						$this->setConsulta($consulta);
						if($this->ejecutarSoloConsulta()) {
							// Si hay archivos que insertar
							if (!empty($this->nombre_archivo)) {
								$i=0;
								$fallo = false;
								while ($i<count($this->nombre_archivo) and (!$fallo) ) {
									$consulta_archivos = sprintf("insert into referencias_archivos (id_referencia, nombre_archivo, fecha_subida, activo) value (%s,%s,current_timestamp,1)",
    									$this->makeValue($this->id_referencia, "int"),
    									$this->makeValue($this->nombre_archivo[$i], "text"));
									$this->setConsulta($consulta_archivos);
									if ($this->ejecutarSoloConsulta()) {
										$i++;
									}
									else $fallo = true;
								}
								if (!$fallo) return 1;
								else return 8;
							}
							else return 1;
						}
						else {
							return 4;
						}
					}
					else {
						return 12;
					}
				}
				else {
					return 13;
				}
			}
			else {
				return 2;
			}
		}
	}

	// Comprueba si hay otra referencia con el mismo nombre
	// Devuelve true si hay referencias duplicadas
	function comprobarReferenciaDuplicada() {
		/*
		if($this->id_referencia == NULL) {
			$consulta = sprintf("select id_referencia from referencias where referencia=%s and id_proveedor=%s and activo=1",
				$this->makeValue($this->referencia, "text"),
				$this->makeValue($this->id_proveedor, "int"));
		} else {
			$consulta = sprintf("select id_referencia from referencias where referencia=%s and id_proveedor=%s and activo=1 and id_referencia<>%s",
				$this->makeValue($this->referencia, "text"),
				$this->makeValue($this->id_referencia, "int"),
				$this->makeValue($this->id_proveedor, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		} else {
			return true;
		}
		*/
		return false;
	}

	function comprobarReferenciaProveedorDuplicada() {
		if($this->id_referencia == NULL) {
			$consulta = sprintf("select id_referencia from referencias where id_proveedor=%s and referencias.part_proveedor_referencia=%s and activo=1",
				$this->makeValue($this->proveedor,"int"),
				$this->makeValue($this->part_proveedor_referencia, "text"));
		} else {
			$consulta = sprintf("select id_referencia from referencias where id_proveedor=%s and referencias.part_proveedor_referencia=%s and activo=1 and id_referencia<>%s",
				$this->makeValue($this->proveedor,"int"),
				$this->makeValue($this->part_proveedor_referencia, "text"),
				$this->makeValue($this->id_referencia, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		} else {
			return true;
		}
	}

	// Comprobar referencias por Part Proveedor
	function comprobarReferenciaTORO() {
		if($this->id_referencia == NULL) {
			$consulta = sprintf("select id_referencia from referencias where referencias.part_proveedor_referencia=%s and referencia=%s and activo=1",
				$this->makeValue($this->part_proveedor_referencia, "text"),
				$this->makeValue($this->referencia, "text"));
		} 
		else {
			$consulta = sprintf("select id_referencia from referencias where referencias.part_proveedor_referencia=%s and activo=1 and id_referencia=%s",				
				$this->makeValue($this->part_proveedor_referencia, "text"),
				$this->makeValue($this->id_referencia, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();

		if($this->getNumeroFilas() == 0) {
			return false;
		} 
		else {
			return true;
		}
	}

	
	// Comprobar referencias por ID_REF
	function comprobarId_RefTORO() {
		$consulta = sprintf("select id_referencia from referencias where id_referencia=%s",
			$this->makeValue($this->id_referencia, "int"));
		$this->setConsulta($consulta);
		// echo $consulta; echo "<br/>";
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		} 
		else {
			return true;
		}
	}

	// Comprobar referencias por ID_REF
	function dameDatosId_RefTORO() {
		$consulta = sprintf("select * from referencias where id_referencia=%s and activo=1",
			$this->makeValue($this->id_referencia, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return NULL;
		} 
		else {
			return $this->getPrimerResultado();
		}
	}


	function comprobarReferenciaFabricanteDuplicada() {
		/*if($this->id_referencia == NULL) {
			$consulta = sprintf("select id_referencia from referencias where id_fabricante=%s and referencias.part_fabricante_referencia=%s and activo=1",
				$this->makeValue($this->fabricante,"int"),
				$this->makeValue($this->part_fabricante_referencia, "text"));
		} else {
			$consulta = sprintf("select id_referencia from referencias where id_fabricante=%s and referencias.part_fabricante_referencia=%s and activo=1 and id_referencia<>%s",
				$this->makeValue($this->fabricante,"int"),
				$this->makeValue($this->part_fabricante_referencia, "text"),
				$this->makeValue($this->id_referencia, "int"));
		}
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		if($this->getNumeroFilas() == 0) {
			return false;
		} else {
			return true;
		}*/
		return false;
	}


	function dameId_archivo($id_ref) {
		$consulta = sprintf("select id_archivo from referencias_archivos where referencias_archivos.id_referencia=%s and referencias_archivos.activo=1 ",
			$this->makeValue($id_ref, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->ids_archivos = $this->getResultados();
	}

	function dameNombres_archivos($id_ref) {
		$consulta = sprintf("select nombre_archivo from referencias_archivos where referencias_archivos.id_referencia=%s and referencias_archivos.activo=1 ",
			$this->makeValue($id_ref, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->nombres_archivos = $this->getResultados();
	}

	function quitarArchivo($nom_archivo,$id_ref) {
		$consulta = sprintf("update referencias_archivos set activo=0 where referencias_archivos.id_referencia=%s and referencias_archivos.nombre_archivo=%s ",
			$this->makeValue($id_ref, "int"),
			$this->makeValue($nom_archivo, "text"));
		$this->setConsulta($consulta);
		if ($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else return 10;
	}

	function dameComponentesPorReferencia($id_referencia){
		$consulta = sprintf("select componentes_referencias.id, componentes.* from componentes_referencias inner join componentes on (componentes_referencias.id_componente=componentes.id_componente) where componentes_referencias.id_referencia=%s and componentes_referencias.activo=1 ",
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$this->resultados_componentes = $this->getResultados();
	}

	function calculaTotalPaquetes($uds_paquete,$num_pieza){
		if ($num_pieza<$uds_paquete) {
			$this->total_paquetes = 1;
		}
		else {
			$resto = fmod($num_pieza,$uds_paquete);
			if ($uds_paquete != 0) {
				$tot_paquetes = floor($num_pieza/$uds_paquete);
			}
			else {
				$tot_paquetes == 0;
			}	
			if ($resto == 0) $this->total_paquetes = $tot_paquetes;
			else $this->total_paquetes = $tot_paquetes + 1;
		}
	}


	function eliminar(){
		$consulta = sprintf("update referencias set activo=0 where id_referencia=%s",
			$this->makeValue($this->id_referencia, "int"));
			$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			// Ahora tenemos que borrar las referencias asociadas a los componentes de la tabla componentes_referencias
			$consulta_refs = sprintf("update componentes_referencias set activo=0 where id_referencia=%s",
				$this->makeValue($this->id_referencia, "int"));
				$this->setConsulta($consulta_refs);
			if($this->ejecutarSoloConsulta()) {
				//ahora tenemos que borrar los archivos adjuntos de las referencias
				$consulta_archs = sprintf("update referencias_archivos set activo=0 where id_referencia=%s",
					$this->makeValue($this->id_referencia, "int"));
					$this->setConsulta($consulta_archs);
					if($this->ejecutarSoloConsulta()) {
						return 6;
					}
					else return 9;
			}
			else return 7;
		} else {
			return 5;
		}
	}

	// Funcion que obtiene todos los datos de la tabla componentes_referencias que tenga la referencias
	function dameComponentesReferencias($id_referencia){
		$consulta = sprintf("select * from componentes_referencias where activo=1 and id_referencia=%s",
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consulta);			
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Actualizar componentes_referencias por ID
	function actualizarComponentesReferencias($id,$unidades_paquete,$pack_precio){
		$consulta = sprintf("update componentes_referencias set uds_paquete=%s, pack_precio=%s, fecha_creado=current_timestamp where id=%s",
			$this->makeValue($unidades_paquete, "int"),
			$this->makeValue($pack_precio, "float"),
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else return 14;
	}

	// Funcion que obtiene las piezas de un componente segun su ID y su id_referencia
	function damePiezasComponenteId($id_componente_referencia){
		$consulta = sprintf("select piezas from componentes_referencias where activo=1 and id=%s",
			$this->makeValue($id_componente_referencia, "int"));
		$this->setConsulta($consulta);			
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Se actualiza la referencia en el componente
	function actualizaPaquetesReferenciaComponente($id,$total_paquetes){
		$consultaSql = sprintf("update componentes_referencias set total_paquetes=%s where id=%s",
			$this->makeValue($total_paquetes, "int"),
			$this->makeValue($id, "int"));
		$this->setConsulta($consultaSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else{
			return 15;	
		}
	}	

	// Devuelve las referencias activas
	function dameReferenciasActivas(){
		$consulta = "select * from referencias where activo=1";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve las referencias activas de TORO
	function dameReferenciasActivasToro(){
		$consulta = "select * from referencias_toro where activo=1";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	function dameReferenciaToro($id_referencia){
		$consulta = sprintf("select * from referencias_toro where activo=1 and id_referencia=%s",
			$this->makeValue($id_referencia,"int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Ya existe una referencia con ese nombre en la base de datos<br/>';
			break;
			case 3:
				return 'Se produjo un error al guardar la nueva referencia<br/>';
			break;
			case 4:
				return 'Se produjo un error al modificar los datos de la referencia<br/>';
			break;
			case 5:
				return 'Se produjo un error al eliminar la referencia<br/>';
			break;
			case 7:
				return 'Se produjo un error al eliminar las referencias asociadas a los componentes<br/>';
			break;
			case 8:
				return 'Se produjo un error al añadir los datos de los archivos adjuntos de las referencias<br/>';
			break;
			case 9:
				return 'Se produjo un error al eliminar los datos de los archivos adjuntos de las referencias<br/>';
			break;
			case 10:
				return 'Se produjo un error al desactivar un archivo adjunto de la referencia<br/>';
			break;
			case 11:
				return 'Se produjo un error al subir un archivo adjunto de la referencia<br/>';
			break;
			case 12:
				return 'Ya existe una referencia con ese fabricante y la misma referencia de fabricante<br/>';
			break;
			case 13:
				return 'Ya existe una referencia con ese proveedor y la misma referencia de proveedor<br/>';
			break;
			case 14:
				return 'Se produjo un error al actualizar los componentes que contienen esa referencia<br/>';
			break;
			case 15:
				return 'Se produjo un error al actualizar el total_paquetes de la referencia de la componente <br/>';
			break;
		}
	}
}
?>