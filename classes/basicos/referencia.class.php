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
	var $ids_archivos;
	var $nombres_archivos; 										// array de nombres que tiene una referencia
	var $resultados_componentes;
	var $total_paquetes;
    var $coste;
	var $id_proceso;
	var $activo;
	var $fecha_creado;


	function cargarDatos($id_referencia,$referencia,$fabricante,$proveedor,$part_nombre,$part_tipo,$part_proveedor_referencia,$part_fabricante_referencia,$part_valor_nombre,$part_valor_cantidad,$part_valor_nombre_2,$part_valor_cantidad_2,$part_valor_nombre_3,$part_valor_cantidad_3,$part_valor_nombre_4,$part_valor_cantidad_4,$part_valor_nombre_5,$part_valor_cantidad_5,$pack_precio,$unidades,$part_descripcion,$comentarios,
						 $part_precio_cantidad,$nombre_proveedor,$nombre_fab,$unidades_stock,$unidades_entrada,$fecha_creado) {
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
		$this->fecha_creado = $fecha_creado;
	}

	function cargaDatosReferenciaId($id_referencia) {
		$consultaSql = sprintf("select referencias.*,proveedores.nombre_prov,fabricantes.nombre_fab,(select sum(piezas) from stock_almacenes where id_referencia=referencias.id_referencia) as unidades_stock,(select sum((total_piezas-piezas_recibidas)) as unidades_entrada from orden_compra_referencias where id_referencia=referencias.id_referencia and activo=1 group by id_referencia) as unidades_entrada from referencias inner join proveedores on (proveedores.id_proveedor=referencias.id_proveedor) inner join fabricantes on (fabricantes.id_fabricante=referencias.id_fabricante) where referencias.id_referencia=%s",
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
			$resultados["unidades_entrada"],
			$resultados["fecha_creado"]
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

	// Se comprueba si la referencia existe en la base de datos según su id_ref
	function getExisteReferenciaPorId($id_referencia) {
		$consultaSql = sprintf("select id_referencia from referencias where id_referencia=%s and activo=1",
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
	
		if($this->getNumeroFilas() == 0) {
			return false;
		} 
		else {
			return true;
		}
	}

	// Se crea la referencia desde la carga de referencias de periféricos
	function crearReferenciaImport($nombre_referencia,$id_proveedor,$id_fabricante,$tipo_pieza,$nombre_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,
									$nombre3,$valor3,$nombre4,$valor4,$nombre5,$valor5,$pack_precio,$unidades_paquete,$comentarios) {
		$consultaSql = sprintf("insert into referencias (referencia,id_proveedor,id_fabricante,part_tipo,part_nombre,part_fabricante_referencia,part_proveedor_referencia,
								part_valor_nombre,part_valor_cantidad,part_valor_nombre_2,part_valor_cantidad_2,part_valor_nombre_3,part_valor_cantidad_3,part_valor_nombre_4,
								part_valor_cantidad_4,part_valor_nombre_5,part_valor_cantidad_5,pack_precio,unidades,comentarios,fecha_creado,activo) 
								values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
						$this->makeValue($nombre_referencia, "text"),
						$this->makeValue($id_proveedor, "int"),
						$this->makeValue($id_fabricante, "int"),
						$this->makeValue($tipo_pieza, "text"),
						$this->makeValue($nombre_pieza, "text"),
						$this->makeValue($ref_fabricante, "text"),
						$this->makeValue($ref_proveedor, "text"),
						$this->makeValue($nombre1, "text"),
						$this->makeValue($valor1, "text"),
						$this->makeValue($nombre2, "text"),
						$this->makeValue($valor2, "text"),
						$this->makeValue($nombre3, "text"),
						$this->makeValue($valor3, "text"),
						$this->makeValue($nombre4, "text"),
						$this->makeValue($valor4, "text"),
						$this->makeValue($nombre5, "text"),
						$this->makeValue($valor5, "text"),
						$this->makeValue($pack_precio, "float"),
						$this->makeValue($unidades_paquete, "int"),
						$this->makeValue($comentarios, "text"));
		$this->setConsulta($consultaSql);
		$this->ejecutarSoloConsulta();
		return $this->getUltimoID();
	}

	// Se actualiza la referencia desde la importación de referencias de un componente
	function actualizarReferenciaImport($id_referencia,$nombre_referencia,$id_proveedor,$id_fabricante,$tipo_pieza,$nombre_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,
											$nombre3,$valor3,$nombre4,$valor4,$nombre5,$valor5,$pack_precio,$unidades_paquete,$comentarios) {
		
		$updateSql = sprintf("update referencias set referencia=%s,part_tipo=%s,part_nombre=%s,part_fabricante_referencia=%s,part_proveedor_referencia=%s,part_valor_nombre=%s,
									part_valor_cantidad=%s,part_valor_nombre_2=%s,part_valor_cantidad_2=%s,part_valor_nombre_3=%s,part_valor_cantidad_3=%s,part_valor_nombre_4=%s,
									part_valor_cantidad_4=%s,part_valor_nombre_5=%s,part_valor_cantidad_5=%s,pack_precio=%s,unidades=%s,comentarios=%s 
									where id_referencia=%s",
						$this->makeValue($nombre_referencia, "text"),
						$this->makeValue($tipo_pieza, "text"),
						$this->makeValue($nombre_pieza, "text"),
						$this->makeValue($ref_fabricante, "text"),
						$this->makeValue($ref_proveedor, "text"),
						$this->makeValue($nombre1, "text"),
						$this->makeValue($valor1, "text"),
						$this->makeValue($nombre2, "text"),
						$this->makeValue($valor2, "text"),
						$this->makeValue($nombre3, "text"),
						$this->makeValue($valor3, "text"),
						$this->makeValue($nombre4, "text"),
						$this->makeValue($valor4, "text"),
						$this->makeValue($nombre5, "text"),
						$this->makeValue($valor5, "text"),
						$this->makeValue($pack_precio, "float"),
						$this->makeValue($unidades_paquete, "int"),
						$this->makeValue($comentarios, "text"),
						$this->makeValue($id_referencia,"int")); 
		$this->setConsulta($updateSql);
		$this->ejecutarSoloConsulta();
		return $id_referencia;
	}

	// Se actualiza la referencia menos el precio desde la importación de referencias de un componente
	function actualizarReferenciaImportSinPrecio($id_referencia,$nombre_referencia,$id_proveedor,$id_fabricante,$tipo_pieza,$nombre_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,
											$nombre3,$valor3,$nombre4,$valor4,$nombre5,$valor5,$unidades_paquete,$comentarios) {
		
		$updateSql = sprintf("update referencias set referencia=%s,part_tipo=%s,part_nombre=%s,part_fabricante_referencia=%s,part_proveedor_referencia=%s,part_valor_nombre=%s,
									part_valor_cantidad=%s,part_valor_nombre_2=%s,part_valor_cantidad_2=%s,part_valor_nombre_3=%s,part_valor_cantidad_3=%s,part_valor_nombre_4=%s,
									part_valor_cantidad_4=%s,part_valor_nombre_5=%s,part_valor_cantidad_5=%s,unidades=%s,comentarios=%s 
									where id_referencia=%s",
						$this->makeValue($nombre_referencia, "text"),
						$this->makeValue($tipo_pieza, "text"),
						$this->makeValue($nombre_pieza, "text"),
						$this->makeValue($ref_fabricante, "text"),
						$this->makeValue($ref_proveedor, "text"),
						$this->makeValue($nombre1, "text"),
						$this->makeValue($valor1, "text"),
						$this->makeValue($nombre2, "text"),
						$this->makeValue($valor2, "text"),
						$this->makeValue($nombre3, "text"),
						$this->makeValue($valor3, "text"),
						$this->makeValue($nombre4, "text"),
						$this->makeValue($valor4, "text"),
						$this->makeValue($nombre5, "text"),
						$this->makeValue($valor5, "text"),
						$this->makeValue($unidades_paquete, "int"),
						$this->makeValue($comentarios, "text"),
						$this->makeValue($id_referencia,"int")); 
		$this->setConsulta($updateSql);
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

    // Función auxiliar para obtener los componentes que tenga una referencia
    function dameComponentesConReferenciaId($id_referencia){
        $consulta = sprintf("select cr.id_componente from componentes_referencias as cr inner join componentes as c on (c.id_componente = cr.id_componente)
                                where cr.activo=1 and cr.id_referencia=%s and c.activo=1 order by cr.fecha_creado, c.version",
                        $this->makeValue($id_referencia, "int"));
        $this->setConsulta($consulta);
        $this->ejecutarConsulta();
        $this->resultados_componentes = $this->getResultados();
    }

	function calculaTotalPaquetes($uds_paquete,$num_pieza){
        if($num_pieza<$uds_paquete) {
			$this->total_paquetes = 1;
		}
		else {
			$resto = fmod($num_pieza,$uds_paquete);
			if ($uds_paquete != 0) {
				$tot_paquetes = floor($num_pieza/$uds_paquete);
			}
			else {
				$tot_paquetes = 0;
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
				// Ahora tenemos que borrar los archivos adjuntos de las referencias
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

	// Función que obtiene todos los datos de la tabla componentes_referencias que tenga la referencias
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

	// Actualizar componentes_referencias por ID menos su precio
	function actualizarComponentesReferenciasSinPrecio($id,$unidades_paquete){
		$consulta = sprintf("update componentes_referencias set uds_paquete=%s, fecha_creado=current_timestamp where id=%s",
			$this->makeValue($unidades_paquete, "int"),
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else return 14;
	}

	// Función que obtiene las piezas de un componente según su ID y su id_referencia
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

	// Función que vincula enlace a página del proveedor de la referencia
	function vincularReferenciaProveedor(){
		$max_caracteres = 50;
		if(strlen($this->part_proveedor_referencia) > $max_caracteres){
			if($this->proveedor == 1){
				echo '<a href="http://es.rs-online.com/web/c/?searchTerm='.$this->part_proveedor_referencia.'" target="_blank">'.substr($this->part_proveedor_referencia,0,50).'...'.'</a>';
			}
			elseif($this->proveedor == 2){
				echo '<a href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st='.$this->part_proveedor_referencia.'" target="_blank">'.substr($this->part_proveedor_referencia,0,50).'...'.'</a>';
			}	
			else {
				echo substr($this->part_proveedor_referencia,0,50).'...';	
			}
		}
		else {
			if($this->proveedor == 1){
				echo '<a href="http://es.rs-online.com/web/c/?searchTerm='.$this->part_proveedor_referencia.'" target="_blank">'.$this->part_proveedor_referencia.'</a>';
			}
			elseif($this->proveedor == 2){
				echo '<a href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st='.$this->part_proveedor_referencia.'" target="_blank">'.$this->part_proveedor_referencia.'</a>';
			}
			else {
				echo $this->part_proveedor_referencia;
			}
		}
	}

	// Función que vincula enlace a página del proveedor de la referencia y lo devuelve en una variable
	function vincularReferenciaProveedorVar(){
		$max_caracteres = 50;
		if(strlen($this->part_proveedor_referencia) > $max_caracteres){
			if($this->proveedor == 1){
				$cadena =  '<a href="http://es.rs-online.com/web/c/?searchTerm='.$this->part_proveedor_referencia.'" target="_blank">'.substr($this->part_proveedor_referencia,0,50).'...'.'</a>';
			}
			elseif($this->proveedor == 2){
				$cadena =  '<a href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st='.$this->part_proveedor_referencia.'" target="_blank">'.substr($this->part_proveedor_referencia,0,50).'...'.'</a>';
			}	
			else {
				$cadena =  substr($this->part_proveedor_referencia,0,50).'...';	
			}
		}
		else {
			if($this->proveedor == 1){
				$cadena =  '<a href="http://es.rs-online.com/web/c/?searchTerm='.$this->part_proveedor_referencia.'" target="_blank">'.$this->part_proveedor_referencia.'</a>';
			}
			elseif($this->proveedor == 2){
				$cadena =  '<a href="http://es.farnell.com/webapp/wcs/stores/servlet/Search?catalogId=15001&langId=-5&storeId=10176&gs=true&st='.$this->part_proveedor_referencia.'" target="_blank">'.$this->part_proveedor_referencia.'</a>';
			}
			else {
				$cadena =  $this->part_proveedor_referencia;
			}
		}
		return $cadena;
	}


	// Devuelve las referencias activas
	function dameReferenciasActivas(){
		$consulta = "select * from referencias where activo=1";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que guarda el log de la importación de una referencia desde el excel
	function guardarLogImportacionReferencia($id_usuario,$error){
		$insertSql = sprintf("insert into log_importacion_referencia (id_usuario,referencia,id_proveedor,id_fabricante,part_tipo,part_nombre,part_fabricante_referencia,
								part_proveedor_referencia,part_valor_nombre,part_valor_cantidad,part_valor_nombre_2,part_valor_cantidad_2,part_valor_nombre_3,part_valor_cantidad_3,
								part_valor_nombre_4,part_valor_cantidad_4,part_valor_nombre_5,part_valor_cantidad_5,pack_precio,unidades,comentarios,fecha_creado,error)
							values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,%s)",
			$this->makeValue($id_usuario, "int"),
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
			$this->makeValue($this->pack_precio, "text"),
			$this->makeValue($this->unidades, "text"),
			$this->makeValue($this->comentarios, "text"),
			$this->makeValue($error, "text"));		
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}	
		else {
			return 16; 
		}
	}

	// Función que agrupa las piezas de una referencia que este repetida en un composite
	function agruparReferencias($referencias,$piezas){
		// Calculamos las repeticiones de las referencias
		$array_repeticiones_referencias = array_count_values($referencias);
		// Obtenemos los ids de las referencias (claves) sin repetir a partir del array con el numero de repeticiones por referencia
		$id_refs_unicas = array_keys($array_repeticiones_referencias);

		// Guardamos en un array las claves de las referencias repetidas del array de referencias
		for($i=0;$i<count($id_refs_unicas);$i++){
			$claves_repetidas_todas_refs[$id_refs_unicas[$i]] = array_keys($referencias,$id_refs_unicas[$i]);
		}

		// Ahora recalculamos las piezas en el caso de que haya referencias repetidas
		for($i=0;$i<count($claves_repetidas_todas_refs);$i++){
			$piezas_por_referencia = 0;
			$claves_repetidas_referencia = $claves_repetidas_todas_refs[$id_refs_unicas[$i]];
			for($j=0;$j<count($claves_repetidas_referencia);$j++){
				$clave_pieza = $claves_repetidas_referencia[$j];
				$piezas_por_referencia = $piezas_por_referencia + $piezas[$clave_pieza];
			}
			// Guardamos en un nuevo array la suma de las piezas de las referencias repetidas
			$piezas_final[] = $piezas_por_referencia;
		}
		
		// Guardamos en un nuevo array las referencias sin repeteciones
		$referencias_final = array_unique($referencias);
		$referencias_final = array_merge($referencias_final);
		// Reseteamos los arrays y copiamos los obtenidos
		unset($referencias);
		unset($piezas);
		$referencias = $referencias_final;
		$piezas = $piezas_final;

		$refs_piezas["referencias"] = $referencias;
		$refs_piezas["piezas"] = $piezas;

		return $refs_piezas;
	}

	// Se actualiza el pack_precio de la referencia desde la carga de referencias
	function actualizarPackPrecioImport($id_referencia,$pack_precio) {
		$consultaSql = sprintf("update referencias set pack_precio=%s where id_referencia=%s",
			$this->makeValue($pack_precio, "float"),
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consultaSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else return 17;
	}

	// Función que guarda el log de la actualización de pack_precio mediante importación de referencias desde el excel
	function guardarLogImportacionPrecioReferencia($id_proceso,$id_usuario,$error,$codigo_error){
		$insertSql = sprintf("insert into log_importacion_precio_referencia (id_proceso,id_usuario,id_referencia,pack_precio,fecha_creado,error,codigo_error)
								values (%s,%s,%s,%s,current_timestamp,%s,%s)",
			$this->makeValue($id_proceso, "int"),								
			$this->makeValue($id_usuario, "int"),
			$this->makeValue($this->id_referencia, "text"),
			$this->makeValue($this->pack_precio, "text"),
			$this->makeValue($error, "text"),
			$this->makeValue($codigo_error, "text"));		
		$this->setConsulta($insertSql);
		if($this->ejecutarSoloConsulta()){
			return 1;
		}	
		else {
			return 18; 
		}
	}

	// Función que devuelve el último id_proceso de la tabla log_importacion_precio_referencias
	function dameUltimoProcesoLogIPR(){
		$consulta = "select max(id_proceso) as ultimo_id_proceso from log_importacion_precio_referencia";
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$res_id_proceso = $this->getPrimerResultado();
		$ultimo_id_proceso = $res_id_proceso["ultimo_id_proceso"]; 
		return $ultimo_id_proceso;
	}

	// Función que devuelve el digito de activo de una referencia
	function dameDigitoActivoReferencia($id_referencia){
		$consulta = sprintf("select activo from referencias where id_referencia=%s",
			$this->makeValue($id_referencia, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		$res_activo = $this->getPrimerResultado();
		$activo = $res_activo["activo"];
		return $activo;
	}

    // Función que calcula el coste de la referencia en función del total de paquetes
    function calculaCosteReferencia(){
        $this->coste = $this->pack_precio * $this->total_paquetes;
        return $this->coste;
    }

    // Codificación de referencias
    function prepararCodificacionReferencia(){
        $nombre_referencia_aux = '';
        $nombre_referencia_cod = utf8_decode($this->referencia);
        for($i=0;$i<strlen($nombre_referencia_cod);$i++){
            if($nombre_referencia_cod[$i] == '?'){
                $nombre_referencia_aux .= '&#8364;';
            }
            else {
                $nombre_referencia_aux .= $nombre_referencia_cod[$i];
            }
        }
        $this->referencia = $nombre_referencia_aux;

        $part_proveedor_referencia_aux = '';
        $part_proveedor_referencia_cod = utf8_decode($this->part_proveedor_referencia);
        for($i=0;$i<strlen($part_proveedor_referencia_cod);$i++){
            if($part_proveedor_referencia_cod[$i] == '?'){
                $part_proveedor_referencia_aux .= '&#8364;';
            }
            else {
                $part_proveedor_referencia_aux .= $part_proveedor_referencia_cod[$i];
            }
        }
        $this->part_proveedor_referencia = $part_proveedor_referencia_aux;

        $nombre_proveedor_aux = '';
        $nombre_proveedor_cod = utf8_decode($this->nombre_proveedor);
        for($i=0;$i<strlen($nombre_proveedor_cod);$i++){
            if($nombre_proveedor_cod[$i] == '?'){
                $nombre_proveedor_aux .= '&#8364;';
            }
            else {
                $nombre_proveedor_aux .= $nombre_proveedor_cod[$i];
            }
        }
        $this->nombre_proveedor = $nombre_proveedor_aux;

        $tipo_pieza_aux = '';
        $tipo_pieza_cod = utf8_decode($this->part_tipo);
        for($i=0;$i<strlen($tipo_pieza_cod);$i++){
            if($tipo_pieza_cod[$i] == '?'){
                $tipo_pieza_aux .= '&#8364;';
            }
            else {
                $tipo_pieza_aux .= $tipo_pieza_cod[$i];
            }
        }
        $this->part_tipo = $tipo_pieza_aux;

        $nombre_pieza_aux = '';
        $nombre_pieza_cod = utf8_decode($this->part_nombre);
        for($i=0;$i<strlen($nombre_pieza_cod);$i++){
            if($nombre_pieza_cod[$i] == '?'){
                $nombre_pieza_aux .= '&#8364;';
            }
            else {
                $nombre_pieza_aux .= $nombre_pieza_cod[$i];
            }
        }
        $this->part_nombre = $nombre_pieza_aux;

        $nombre_fabricante_aux = '';
        $nombre_fabricante_cod = utf8_decode($this->nombre_fabricante);
        for($i=0;$i<strlen($nombre_fabricante_cod);$i++){
            if($nombre_fabricante_cod[$i] == '?'){
                $nombre_fabricante_aux .= '&#8364;';
            }
            else {
                $nombre_fabricante_aux .= $nombre_fabricante_cod[$i];
            }
        }
        $this->nombre_fabricante = $nombre_pieza_aux;

        $part_fabricante_referencia_aux = '';
        $part_fabricante_referencia_cod = utf8_decode($this->part_fabricante_referencia);
        for($i=0;$i<strlen($part_fabricante_referencia_cod);$i++){
            if($part_fabricante_referencia_cod[$i] == '?'){
                $part_fabricante_referencia_aux .= '&#8364;';
            }
            else {
                $part_fabricante_referencia_aux .= $part_fabricante_referencia_cod[$i];
            }
        }
        $this->part_fabricante_referencia = $part_fabricante_referencia_aux;

        $descripcion_aux = '';
        $descripcion_cod = utf8_decode($this->part_descripcion);
        for($i=0;$i<strlen($descripcion_cod);$i++){
            if($descripcion_cod[$i] == '?'){
                $descripcion_aux .= '&#8364;';
            }
            else {
                $descripcion_aux .= $descripcion_cod[$i];
            }
        }
        $this->part_descripcion = $descripcion_aux;

        $part_valor_nombre_aux = '';
        $part_valor_nombre_cod = utf8_decode($this->part_valor_nombre);
        for($i=0;$i<strlen($part_valor_nombre_cod);$i++){
            if($part_valor_nombre_cod[$i] == '?'){
                $part_valor_nombre_aux .= '&#8364;';
            }
            else {
                $part_valor_nombre_aux .= $part_valor_nombre_cod[$i];
            }
        }
        $this->part_valor_nombre = $part_valor_nombre_aux;

        $part_valor_nombre_2_aux = '';
        $part_valor_nombre_2_cod = utf8_decode($this->part_valor_nombre_2);
        for($i=0;$i<strlen($part_valor_nombre_2_cod);$i++){
            if($part_valor_nombre_2_cod[$i] == '?'){
                $part_valor_nombre_2_aux .= '&#8364;';
            }
            else {
                $part_valor_nombre_2_aux .= $part_valor_nombre_2_cod[$i];
            }
        }
        $this->part_valor_nombre_2 = $part_valor_nombre_2_aux;

        $part_valor_nombre_3_aux = '';
        $part_valor_nombre_3_cod = utf8_decode($this->part_valor_nombre_3);
        for($i=0;$i<strlen($part_valor_nombre_3_cod);$i++){
            if($part_valor_nombre_3_cod[$i] == '?'){
                $part_valor_nombre_3_aux .= '&#8364;';
            }
            else {
                $part_valor_nombre_3_aux .= $part_valor_nombre_3_cod[$i];
            }
        }
        $this->part_valor_nombre_3 = $part_valor_nombre_3_aux;

        $part_valor_nombre_4_aux = '';
        $part_valor_nombre_4_cod = utf8_decode($this->part_valor_nombre_4);
        for($i=0;$i<strlen($part_valor_nombre_4_cod);$i++){
            if($part_valor_nombre_4_cod[$i] == '?'){
                $part_valor_nombre_4_aux .= '&#8364;';
            }
            else {
                $part_valor_nombre_4_aux .= $part_valor_nombre_4_cod[$i];
            }
        }
        $this->part_valor_nombre_4 = $part_valor_nombre_4_aux;

        $part_valor_cantidad_aux = '';
        $part_valor_cantidad_cod = utf8_decode($this->part_valor_cantidad);
        for($i=0;$i<strlen($part_valor_cantidad_cod);$i++){
            if($part_valor_cantidad_cod[$i] == '?'){
                $part_valor_cantidad_aux .= '&#8364;';
            }
            else {
                $part_valor_cantidad_aux .= $part_valor_cantidad_cod[$i];
            }
        }
        $this->part_valor_cantidad = $part_valor_cantidad_aux;

        $part_valor_cantidad_2_aux = '';
        $part_valor_cantidad_2_cod = utf8_decode($this->part_valor_cantidad_2);
        for($i=0;$i<strlen($part_valor_cantidad_2_cod);$i++){
            if($part_valor_cantidad_2_cod[$i] == '?'){
                $part_valor_cantidad_2_aux .= '&#8364;';
            }
            else {
                $part_valor_cantidad_2_aux .= $part_valor_cantidad_2_cod[$i];
            }
        }
        $this->part_valor_cantidad_2 = $part_valor_cantidad_2_aux;

        $part_valor_cantidad_3_aux = '';
        $part_valor_cantidad_3_cod = utf8_decode($this->part_valor_cantidad_3);
        for($i=0;$i<strlen($part_valor_cantidad_3_cod);$i++){
            if($part_valor_cantidad_3_cod[$i] == '?'){
                $part_valor_cantidad_3_aux .= '&#8364;';
            }
            else {
                $part_valor_cantidad_3_aux .= $part_valor_cantidad_3_cod[$i];
            }
        }
        $this->part_valor_cantidad_3 = $part_valor_cantidad_3_aux;

        $part_valor_cantidad_4_aux = '';
        $part_valor_cantidad_4_cod = utf8_decode($this->part_valor_cantidad_4);
        for($i=0;$i<strlen($part_valor_cantidad_4_cod);$i++){
            if($part_valor_cantidad_4_cod[$i] == '?'){
                $part_valor_cantidad_4_aux .= '&#8364;';
            }
            else {
                $part_valor_cantidad_4_aux .= $part_valor_cantidad_4_cod[$i];
            }
        }
        $this->part_valor_cantidad_4 = $part_valor_cantidad_4_aux;

        $comentarios_aux = '';
        $comentarios_cod = utf8_decode($this->comentarios);
        for($i=0;$i<strlen($comentarios_cod);$i++){
            if($comentarios_cod[$i] == '?'){
                $comentarios_aux .= '&#8364;';
            }
            else {
                $comentarios_aux .= $comentarios_cod[$i];
            }
        }
        $this->comentarios = $comentarios_aux;
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
			case 16:
				return 'Se produjo un error al guardar el log de la importacion de referencias<br/>';
			break;
			case 17:
				return 'Se produjo un error al actualizar el precio en la importacion masiva de referencias<br/>';
			break;
			case 18:
				return 'Se produjo un error al guardar el log de la actualizacion de precios de referencias mediante excel<br/>';
			break;
		}
	}
}
?>