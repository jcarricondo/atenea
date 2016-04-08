<?php 

class MaterialInformatico extends MySql{

	var $id_material;
	var $id_tipo;
	var $id_subtipo;
	var $num_serie;
	var $descripcion;
	var $id_almacen;
	var $precio;
	var $asignado_a;
	var $estado;
	var $observaciones; 
	var $fecha_creado;
	var $activo;


	// Carga de datos de un material informático ya existente en la base de datos
	function cargarDatos($id_material,$id_tipo,$id_subtipo,$num_serie,$descripcion,$id_almacen,$precio,$asignado_a,$estado,$observaciones,$fecha_creado,$activo) {
		$this->id_material = $id_material;
		$this->id_tipo = $id_tipo;
		$this->id_subtipo = $id_subtipo;
		$this->num_serie = $num_serie;
		$this->descripcion = $descripcion;
		$this->id_almacen = $id_almacen;
		$this->precio = $precio;
		$this->asignado_a = $asignado_a;
		$this->estado = $estado;
		$this->observaciones = $observaciones;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	// Se obtienen los datos del material informático de la oficina en base a su ID
	function cargaDatosMaterialId($id_material) {
		$consultaSql = sprintf("select * from material_informatico where id_material=%s",
			$this->makeValue($id_material, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$resultados = $this->getPrimerResultado();
		$this->cargarDatos(
			$resultados["id_material"],
			$resultados["id_tipo"],
			$resultados["id_subtipo"],
			$resultados["num_serie"],
			$resultados["descripcion"],
			$resultados["id_almacen"],
			$resultados["precio"],
			$resultados["asignado_a"],
			$resultados["estado"],
			$resultados["observaciones"],
			$resultados["fecha_creado"],
			$resultados["activo"]
		);
	}

	// Función que establece los atributos del material informático
	function datosMaterial($id_material,$id_tipo,$id_subtipo,$num_serie,$descripcion,$id_almacen,$precio,$asignado_a,$estado,$observaciones,$fecha_creado,$activo){
		$this->id_material = $id_material;
		$this->id_tipo = $id_tipo;
		$this->id_subtipo = $id_subtipo;
		$this->num_serie = $num_serie;
		$this->descripcion = $descripcion; 
		$this->id_almacen = $id_almacen;
		$this->precio = $precio;
		$this->asignado_a = $asignado_a;
		$this->estado = $estado;
		$this->observaciones = $observaciones;
		$this->fecha_creado = $fecha_creado;
		$this->activo = $activo;
	}

	function setNumSerie($num_serie){
		$this->num_serie = $num_serie;
	}

	// Función que obtiene los datos del tipo de material informático 
	function dameTipoMaterial($id_tipo){
		$consultaSql = sprintf("select nombre,codigo from material_informatico_tipo where activo=1 and id_tipo=%s",
			$this->makeValue($id_tipo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que obtiene los datos del subtipo de material informático 
	function dameSubtipoMaterial($id_subtipo){
		$consultaSql = sprintf("select * from material_informatico_subtipo where activo=1 and id_subtipo=%s",
			$this->makeValue($id_subtipo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que obtiene todos los tipos de material informático 
	function dameTiposMateriales(){
		$consultaSql = "select id_tipo,nombre,codigo from material_informatico_tipo where activo=1";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que obtiene todos los subtipos de material informático 
	function dameSubtiposMateriales(){
		$consultaSql = "select * from material_informatico_subtipo where activo=1 order by id_tipo,id_subtipo";
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();
	}

	// Función que devuelve los subtipos de un tipo de material
	function dameSubtiposSegunTipo($id_tipo){
		$consultaSql = sprintf("select * from material_informatico_subtipo where activo=1 and id_tipo=%s order by id_tipo,id_subtipo",
							$this->makeValue($id_tipo, "int"));
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		return $this->getResultados();	
	}

	// Función que genera un numero de serie en función del código del tipo de material
	function generaNumSerie($codigo_tipo){
		$numero = rand(1,9999999);
 		$numero_string = str_pad((String)$numero,7,"0",STR_PAD_LEFT); 
		$num_serie = $codigo_tipo."-".$numero_string; 
		return $num_serie;
	}

	// Función que guarda un nuevo material 
	function guardaCambios(){
		if($this->id_material == NULL) {
			// Comprueba si hay otro material con el mismo número de serie
			if(!$this->comprobarMaterialDuplicado()) {
				$insertSql = sprintf("insert into material_informatico (id_tipo,id_subtipo,num_serie,descripcion,id_almacen,precio,asignado_a,estado,observaciones,fecha_creado,activo)
								values (%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
					$this->makeValue($this->id_tipo, "int"),
					$this->makeValue($this->id_subtipo, "int"),
					$this->makeValue($this->num_serie, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->id_almacen, "int"),
					$this->makeValue($this->precio, "float"),
					$this->makeValue($this->asignado_a, "text"),
					$this->makeValue($this->estado, "text"),
					$this->makeValue($this->observaciones, "text"));		
				$this->setConsulta($insertSql); 
				if($this->ejecutarSoloConsulta()){
					// Guardamos el estado del material informático
					$this->id_material = $this->getUltimoId();
					$res = $this->guardarEstadoMaterial();
					if($res == 1){
						return 1;
					}
					else{
						return 4;
					}
				}
				else {
					return 2;
				}
			}
			else {
				return 3;
			}
		}
		else {
			if(!$this->comprobarMaterialDuplicado()) {
				$updateSql = sprintf("update material_informatico set id_tipo=%s,id_subtipo=%s,num_serie=%s,descripcion=%s,id_almacen=%s,precio=%s,asignado_a=%s,estado=%s,observaciones=%s where id_material=%s",
					$this->makeValue($this->id_tipo, "int"),
					$this->makeValue($this->id_subtipo, "int"),
					$this->makeValue($this->num_serie, "text"),
					$this->makeValue($this->descripcion, "text"),
					$this->makeValue($this->id_almacen, "int"),
					$this->makeValue($this->precio, "float"),
					$this->makeValue($this->asignado_a, "text"),
					$this->makeValue($this->estado, "text"),
					$this->makeValue($this->observaciones, "text"),
					$this->makeValue($this->id_material, "int"));
				$this->setConsulta($updateSql);
				if($this->ejecutarSoloConsulta()){
					// Desactivamos el estado del material informático
					$res_desactivar = $this->desactivarMaterialInformatico();
					if($res_desactivar == 1){
						$res = $this->guardarEstadoMaterial();
						if($res == 1){
							return 1;
						}
						else {
							return 4;
						}
					}
					else {
						return 5;
					}
				}
				else {
					return 6;
				}
			}
			else {
				return 3;
			}
		}
	}

	// Función que guarda el estado del material informático 
	function guardarEstadoMaterial(){
		$insertSql = sprintf("insert into material_informatico_estados (id_material,estado,fecha_creado,activo) values (%s,%s,current_timestamp,1)",
			$this->makeValue($this->id_material, "int"),
			$this->makeValue($this->estado, "text"));		
		$this->setConsulta($insertSql); 
		if($this->ejecutarSoloConsulta()){
			return 1;
		}
		else {
			return 4;
		}
	}

	// Función que desactiva el estado del material informático 
	function desactivarMaterialInformatico(){
		$updateSql = sprintf("update material_informatico_estados set activo=0 where id_material=%s and activo=1",
			$this->makeValue($this->id_material, "int"));
		$this->setConsulta($updateSql);
		if($this->ejecutarSoloConsulta()) { 
			return 1;
		}
		else {
			return 5;
		}	
	}

	// Función que actualiza el estado del material informático
	function actualizaEstadoMaterial($id_material,$estado){
		$consulta = sprintf("update material_informatico set estado=%s, activo=1 where id_material=%s",
			$this->makeValue($estado, "text"),
			$this->makeValue($id_material, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 7;
		}
	}

	// Función que guarda el nuevo estado del material en el historial de estados de material informático
	function guardarLogEstadoMaterial($id_material,$estado){
		$consulta = sprintf("insert into material_informatico_estados (id_material,estado,fecha_creado,activo) value (%s,%s,current_timestamp,1)",
			$this->makeValue($id_material, "int"),
			$this->makeValue($estado, "text"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 4;
		}	
	}

	// Función que obtiene el último log de estado activo de un material
	function desactivarLogEstadoMaterial($id_material){
		$consulta = sprintf("update material_informatico_estados set activo=0 where id_material=%s and activo=1",
			$this->makeValue($id_material, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) { 
			return 1;		
		}	
		else {
			return 5;
		}
	}

	// Elimina el estado de un material informático cuando se deshace la operación 
	function eliminarEstadoMaterial($id){
		$consulta = sprintf("delete from material_informatico_estados where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		}
		else {
			return 8;
		}
	}

	// Vuelve a activar el estado del material informático al deshacerse la operación 
	function reactivarEstadoMaterial($id){
		$consulta = sprintf("update material_informatico_estados set activo=1 where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		if($this->ejecutarSoloConsulta()) {
			return 1;
		} 
		else {
			return 9;
		}
	}

	// Función que verifica si existe un material a partir de su número de serie y de su almacen
	function existeMaterial($num_serie,$id_almacen){
		$consulta = sprintf("select * from material_informatico where num_serie=%s and id_almacen=%s",
			$this->makeValue($num_serie, "text"),
			$this->makeValue($id_almacen, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Devuelve el ID de la tabla material_informatico_estados del material informático
	function dameIDMaterialEstados($id_material){
		$consulta = sprintf("select id from material_informatico_estados where id_material=%s and activo=1",
			$this->makeValue($id_material, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Devuelve el estado de la tabla material_informatico_estados según el ID 
	function dameEstadoMaterialLog($id){
		$consulta = sprintf("select estado from material_informatico_estados where id=%s",
			$this->makeValue($id, "int"));
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}

	// Función que devuelve el id_material de un material informático según su num_serie
	function dameIdMaterialPorNumSerie($num_serie){
		$consulta = 'select id_material from material_informatico where num_serie='.$num_serie.' and activo=1';
		$this->setConsulta($consulta);
		$this->ejecutarConsulta();
		return $this->getPrimerResultado();
	}	

	// Funcion que devuelve el numero de unidades de un subtipo de material en STOCK
	function dameUnidadesStock($id_tipo,$id_subtipo){
		if($id_subtipo != 0){
			$consultaSql = sprintf("select count(id_material) as unidades_stock from material_informatico where id_subtipo=%s",
				$this->makeValue($id_subtipo, "int"));					
		}
		else {
			$consultaSql = sprintf("select count(id_material) as unidades_stock from material_informatico where id_tipo=%s",
				$this->makeValue($id_tipo, "int"));
		}
		$this->setConsulta($consultaSql);
		$this->ejecutarConsulta();
		$res_unidades = $this->getPrimerResultado(); 
		$res_unidades = $res_unidades["unidades_stock"];
		return $res_unidades;
	}

	// Comprueba si hay material informático con el mismo número de serie
	function comprobarMaterialDuplicado() {
		if($this->id_material == NULL){
			$consulta = sprintf("select id_material from material_informatico where num_serie=%s and activo=1",
				$this->makeValue($this->num_serie, "text"));
		}
		else {
			$consulta = sprintf("select id_material from material_informatico where num_serie=%s and activo=1 and id_material<>%s",
				$this->makeValue($this->num_serie, "text"),
				$this->makeValue($this->id_material, "int"));	
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

    function eliminar(){
        $consulta = sprintf("update material_informatico set activo=0 where id_material=%s",
            $this->makeValue($this->id_material, "int"));
        $this->setConsulta($consulta);
        if($this->ejecutarSoloConsulta()) {
            return 1;
        } else {
            return 10;
        }
    }

	// Función que devuelve el HTML con el código de error
	function dameHTMLconMensajeError($mensaje_error){
		return 	$codigo = '<div id="cargaMaterial">
                    			<div class="ContenedorCamposCreacionBasico">
                        			<div class="LabelCreacionBasico">NUM. SERIE *</div>
                        			<input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="" />
                        			<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaMaterial()" />
                    			</div>
                    			<div class="ContenedorCamposCreacionBasico">
                        			<div id="error_codigo" style="height: 30px;"><span style="color: red; font:bold 10px Verdana,Arial; padding: 5px;">'.$mensaje_error.'</span></div>
                    			</div>
                    			<div id="capa_material_buscador" class="ContenedorCamposCreacionBasico">
                        			<table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
                            		<tr style="height: 30px;">
                                		<th style="width:10%;">NUM. SERIE</th>
                                		<th style="width:20%;">TIPO</th>
                                		<th style="width:20%;">ESTADO</th>	
                                		<th style="width:10%; text-align: center;">AVERIADO</th>
                                		<th style="width:20%; text-align: center;"></th>
                                		<th style="width:20%; text-align: center;"></th>
                            		</tr>
                            		<tr style="height: 35px;">
                                		<td style="width:10%;"></td>
                                		<td style="width:20%;"></td>
                                		<td style="width:20%;"></td>
                                		<td style="width:10%; text-align: center;"></td>
                                		<td style="width:20%; text-align: center;"></td>
                                		<td style="width:20%; text-align: center;"></td>
                            		</tr>
                        			</table>
                        			<div id="datos_material"></div>
                    			</div>
                			</div>';
	}

	

	// Devuelve la cadena de un error según su identificador
	function getErrorMessage($error_num) {
		switch($error_num) {
			case 2:
				return 'Se produjo un error al guardar el material informático<br/>';
			break;
			case 3:
				return 'Se generó un número de serie ya existente en otro material informático. Repita de nuevo la operación para generar un nuevo código<br/>';
			break;
			case 4:
				return 'Se produjo un error al guardar el estado del material informático<br/>';
			break;
			case 5:
				return 'Se produjo un error al desactivar el estado del material informático<br/>';
			break; 
			case 6:
				return 'Se produjo un error al actualizar el material informático<br/>';
			break;
			case 7:
				return 'Se produjo un error al actualizar el estado del material informático<br/>';
			break;
			case 8:
				return 'Se produjo un error al eliminar el estado del material informático<br/>';
			break;
			case 9:
				return 'Se produjo un error al reactivar el estado del material informático<br/>';
			break;
            case 10:
                return 'Se produjo un error al eliminar el material informático<br/>';
                break;
			default:

			break;
		}
	}
}
?>
