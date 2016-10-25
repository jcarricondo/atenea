<?php
// Fichero con las funciones de comprobación para AJAX del material informático
include("../../classes/mysql.class.php");
include("../../classes/material_informatico/material_informatico.class.php");
include("../../classes/material_informatico/albaran_informatico.class.php");

$db = new MySQL();
$materialInformatico = new MaterialInformatico();
$albaranInformatico = new AlbaranInformatico();

if(isset($_GET["func"])){
	switch($_GET["func"]){
		// Comprobación si existe el material informático con cierto número de serie
		case "comprobarNumSerie":
			$num_serie = $_GET["num_serie"];
			$metodo = $_GET["metodo"];
			$id_almacen = $_GET["id_almacen"];
			$num_serie_string = "'".$num_serie."'";  

			// Comprobamos si existe el material en el almacen correspondiente
			$resultados = $materialInformatico->existeMaterial($num_serie,$id_almacen);
			if($resultados != NULL){
				// Cargamos los datos del material
				$id_material = $resultados["id_material"];
				$id_tipo = $resultados["id_tipo"];
				$estado = $resultados["estado"]; 
				$error_estado = false;
				
				if($metodo == "RECEPCIONAR"){
					// El material debe estar en estado EN REPARACIÓN o EN USO
					$error_estado = ($estado != "EN REPARACION" && $estado != "EN USO"); 
					$mensaje_error = 'EL MATERIAL INFORMÁTICO NO SE ENCUENTRA EN ESTADO "EN REPARACIÓN" O "EN USO" Y NO SE PUEDE RECEPCIONAR';
					$boton_proceso = '<input type="button" class="BotonEliminar" value="RECEPCIONAR" onclick="recepcionarMaterial('.$num_serie_string.','.$id_almacen.');" />';
				}
				if($metodo == "DESRECEPCIONAR"){
					// El material debe estar en estado STOCK o AVERIADO
					$error_estado = ($estado != "STOCK" && $estado != "AVERIADO"); 
					$mensaje_error = 'EL MATERIAL INFORMÁTICO NO SE ENCUENTRA EN ESTADO "STOCK" O "AVERIADO" Y NO SE PUEDE DESRECEPCIONAR';
					$boton_proceso = '<input type="button" class="BotonEliminar" value="DESRECEPCIONAR" onclick="desrecepcionarMaterial('.$num_serie_string.','.$id_almacen.');" />';	
				}

				if(!$error_estado){
					// Obtenemos el nombre del tipo de material
					$nombre_tipo = $materialInformatico->dameTipoMaterial($id_tipo); 
					$nombre_tipo = $nombre_tipo[0]["nombre"]; 

					// Obtenemos el ID del estado del material informático 
					$datos_id_estado_material = $materialInformatico->dameIDMaterialEstados($id_material);
					$id_estado = $datos_id_estado_material["id"]; 

					// Preparamos la tabla de respuesta con los datos cargados
					$mensaje_error = ''; 
                	echo '<div class="ContenedorCamposCreacionBasico">
                    	    <div class="LabelCreacionBasico">NUM. SERIE *</div>
                        	<input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="" />
                        	<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaMaterial()" />
                    	</div>
                    	<div class="ContenedorCamposCreacionBasico">
                    		<div id="error_codigo" style="height: 30px;"></div>
						</div>
                    	<div id="capa_periferico_buscador" class="ContenedorCamposCreacionBasico">
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
                            	<td style="width:10%;">'.$num_serie.'</td>
	                            <td style="width:20%;">'.utf8_encode($nombre_tipo).'</td>
	                            <td style="width:20%;">'.utf8_encode($estado).'</td>';

	                        if($metodo == "RECEPCIONAR") {
                            	if($estado == "EN REPARACION"){
                            		echo '<td style="width:10%; text-align: center;"><input type="checkbox" id="averiado" disabled/></td>';
                            	}
                            	else {
                            		// EN USO
                            		echo '<td style="width:10%; text-align: center;"><input type="checkbox" id="averiado"/></td>';	
                            	}
                            }
                            else {
                            	// DESRECEPCIONAR
                            	if($estado == "STOCK"){
                            		echo '<td style="width:10%; text-align: center;"><input type="checkbox" id="averiado" disabled/></td>';	
                            	}
                            	else {
                            		// AVERIADO 
                            		echo '<td style="width:10%; text-align: center;"><input type="checkbox" id="averiado" checked disabled/></td>';		
                            	}
                            }	
					
					echo '<td style="width:20%; text-align: center;"></td>
							<td style="width:20%; text-align: center;">'.$boton_proceso.'</td>
                        </tr>
                   		</table>
                        <div id="datos_material">
                        	<input type="hidden" id="num_serie_hidden" value="'.$num_serie.'" />
                        	<input type="hidden" id="id_material_hidden" value="'.$id_material.'" />
                        	<input type="hidden" id="nombre_tipo_hidden" value="'.utf8_encode($nombre_tipo).'" />
                        	<input type="hidden" id="id_estado_hidden" value="'.$id_estado.'" />
                        </div>
                    </div>';
				}
				else {
					$codigo = $materialInformatico->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
			else {
				// No existe el material informático. ERROR
				$mensaje_error = 'NO EXISTE EL MATERIAL INFORMÁTICO EN LA BBDD';
				$codigo = $materialInformatico->dameHTMLconMensajeError($mensaje_error);
				echo $codigo;
			}
		break;	
		// Recepción de un material
		case "recepcionar":
			$num_serie = $_GET["num_serie"];
			$id_material = $_GET["id_material"];
			$esta_averiado = $_GET["esta_averiado"];
			$id_almacen = $_GET["id_almacen"];

			if($esta_averiado == 'SI'){
				$estado = "AVERIADO";
			}
			else {
				$estado = "STOCK";
			}

    		// Obtenemos el albarán al que pertenece la operación
    		$id_albaran = $albaranInformatico->dameUltimoAlbaran($id_almacen);
			$id_albaran = $id_albaran["id_albaran"];

		    // Obtenemos el id de estado del material que esta activo 
			$datos_material_estado = $materialInformatico->dameIDMaterialEstados($id_material);
			$id_material_estado = $datos_material_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del material
			$resultado = $materialInformatico->desactivarLogEstadoMaterial($id_material);
			if($resultado == 1){
				// Guardamos el movimiento de ese material asociado al albarán
			    $resultado = $albaranInformatico->guardarMovimientoMaterial($id_albaran,$id_material,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operación que realizó el usuario
			    	$resultado = $albaranInformatico->guardarLogMaterial($id_albaran,$num_serie,$esta_averiado,$id_material);
			    	if($resultado == 1){
			    		// Actualizamos el estado del material
			    		$resultado = $materialInformatico->actualizaEstadoMaterial($id_material,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de material_informatico_estados		
			    			$resultado = $materialInformatico->guardarLogEstadoMaterial($id_material,$estado);
			    			if($resultado == 1){
			    				// Ok 
			    				// Regresamos a la llamada de Javascript		
			    			}
			    			else{
			    				$mensaje_error_log = $materialInformatico->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
			    			$mensaje_error_log = $materialInformatico->getErrorMessage($resultado);
			    		}
			    	}
			    	else{
			    		$mensaje_error_log = $albaranInformatico->getErrorMessage($resultado);
			    	}
			    }
			    else{
			    	$mensaje_error_log = $albaranInformatico->getErrorMessage($resultado);		
			    }	
			}
			else {
				$mensaje_error_log = $materialInformatico->getErrorMessage($resultado);
			}
		break;
		// Deshacer recepción del material
		case "deshacerRecepcion":
			$id_material = $_GET["id_material"];
			$id_estado_restaurar = $_GET["id_estado"];
			$id_albaran = $_GET["id_albaran"];

			// Desactivamos el último movimiento realizado
			// Primero obtenemos los datos del último movimiento de albarán de ese material
		    $datos_ultimo_movimiento = $albaranInformatico->dameUltimoMovimientoMaterial($id_material);
		    $id_movimiento = $datos_ultimo_movimiento["id"];

	    	// Eliminamos el movimiento del albarán del material correspondiente a la operación que se ha deshecho
	    	$resultado_movimiento = $albaranInformatico->eliminarEstadoMaterial($id_movimiento);
			if($resultado_movimiento == 1){
				// Desactivamos el log del albarán de ese material correspondiente a la operación que se ha deshecho
		    	$resultado = $albaranInformatico->desactivarLogMaterial($id_albaran,$id_material);
		    	if($resultado == 1){
		    		// Obtenemos el id del estado del material de la operación que se ha deshecho
		    		$datos_ultimo_estado = $materialInformatico->dameIDMaterialEstados($id_material);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del material correspondiente a la operación que se ha deshecho
		    		$resultado_estado = $materialInformatico->eliminarEstadoMaterial($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del material
		    			$resultado = $materialInformatico->reactivarEstadoMaterial($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del material
		    				$datos_estado = $materialInformatico->dameEstadoMaterialLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];
		    				
		    				// Por último cambiamos el estado del material
		  					$resultado = $materialInformatico->actualizaEstadoMaterial($id_material,$estado);
		    				if($resultado == 1){
		    					// OK
		    					// Volvemos a la llamada de Javascript
		    				}
		    				else {
		    					// ERROR AL ACTUALIZAR EL ESTADO DEL MATERIAL
		    					$materialInformatico->getErrorMessage($resultado);
		    				}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL MATERIAL
		    				$materialInformatico->getErrorMessage($resultado);	
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL MATERIAL
		    			$materialInformatico->getErrorMessage($resultado);
		    		}
		    	}
		    	else {
		    		// ERROR AL DESACTIVAR EL LOG DEL ALBARAN DEL MATERIAL
		    		$mensaje_error_log = $albaranInformatico->getErrorMessage($resultado);
		    	}
			}  
		break;
		// Desrecepción de un material
		case "desrecepcionar":
			$num_serie = $_GET["num_serie"];
			$id_material = $_GET["id_material"];
			$esta_averiado = $_GET["esta_averiado"];
			$id_almacen = $_GET["id_almacen"];

			if($esta_averiado == 'SI'){
				$estado = "EN REPARACION";
			}
			else {
				$estado = "EN USO";
			}

    		// Obtenemos el albarán al que pertenece la operación
    		$id_albaran = $albaranInformatico->dameUltimoAlbaran($id_almacen);
			$id_albaran = $id_albaran["id_albaran"];

		    // Obtenemos el id de estado del material que esta activo 
			$datos_material_estado = $materialInformatico->dameIDMaterialEstados($id_material);
			$id_material_estado = $datos_material_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del material
			$resultado = $materialInformatico->desactivarLogEstadoMaterial($id_material);
			if($resultado == 1){
				// Guardamos el movimiento de ese material asociado al albarán
			    $resultado = $albaranInformatico->guardarMovimientoMaterial($id_albaran,$id_material,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operación que realizó el usuario
			    	$resultado = $albaranInformatico->guardarLogMaterial($id_albaran,$num_serie,$esta_averiado,$id_material);
			    	if($resultado == 1){
			    		// Actualizamos el estado del material
			    		$resultado = $materialInformatico->actualizaEstadoMaterial($id_material,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de material_informatico_estados		
			    			$resultado = $materialInformatico->guardarLogEstadoMaterial($id_material,$estado);
			    			if($resultado == 1){
			    				// Ok 
			    				// Regresamos a la llamada de Javascript		
			    			}
			    			else{
			    				$mensaje_error_log = $materialInformatico->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
			    			$mensaje_error_log = $materialInformatico->getErrorMessage($resultado);
			    		}
			    	}
			    	else{
			    		$mensaje_error_log = $albaranInformatico->getErrorMessage($resultado);
			    	}
			    }
			    else{
			    	$mensaje_error_log = $albaranInformatico->getErrorMessage($resultado);		
			    }	
			}
			else {
				$mensaje_error_log = $materialInformatico->getErrorMessage($resultado);
			}	
		break;
		// Deshacer desrecepción del material
		case "deshacerDesRecepcion":
			$id_material = $_GET["id_material"];
			$id_estado_restaurar = $_GET["id_estado"];
			$id_albaran = $_GET["id_albaran"];

			// Desactivamos el último movimiento realizado
			// Primero obtenemos los datos del último movimiento de albarán de ese material
		    $datos_ultimo_movimiento = $albaranInformatico->dameUltimoMovimientoMaterial($id_material);
		    $id_movimiento = $datos_ultimo_movimiento["id"];

	    	// Eliminamos el movimiento del albarán del material correspondiente a la operación que se ha deshecho
	    	$resultado_movimiento = $albaranInformatico->eliminarEstadoMaterial($id_movimiento);
			if($resultado_movimiento == 1){
				// Desactivamos el log del albarán de ese material correspondiente a la operación que se ha deshecho
		    	$resultado = $albaranInformatico->desactivarLogMaterial($id_albaran,$id_material);
		    	if($resultado == 1){
		    		// Obtenemos el id del estado del material de la operación que se ha deshecho
		    		$datos_ultimo_estado = $materialInformatico->dameIDMaterialEstados($id_material);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del material correspondiente a la operación que se ha deshecho
		    		$resultado_estado = $materialInformatico->eliminarEstadoMaterial($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del material
		    			$resultado = $materialInformatico->reactivarEstadoMaterial($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del material
		    				$datos_estado = $materialInformatico->dameEstadoMaterialLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];
		    				
		    				// Por último cambiamos el estado del material
		  					$resultado = $materialInformatico->actualizaEstadoMaterial($id_material,$estado);
		    				if($resultado == 1){
		    					// OK
		    					// Volvemos a la llamada de Javascript
		    				}
		    				else {
		    					// ERROR AL ACTUALIZAR EL ESTADO DEL MATERIAL
		    					$materialInformatico->getErrorMessage($resultado);
		    				}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL MATERIAL
		    				$materialInformatico->getErrorMessage($resultado);	
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL MATERIAL
		    			$materialInformatico->getErrorMessage($resultado);
		    		}
		    	}
		    	else {
		    		// ERROR AL DESACTIVAR EL LOG DEL ALBARAN DEL MATERIAL
		    		$mensaje_error_log = $albaranInformatico->getErrorMessage($resultado);
		    	}
			}
		break;
		case 'cargaSuptipo':
			// Cargamos el select de subtipo según el tipo de material
			$label_subtipos = '<div class="LabelCreacionBasico">Subtipo *</div>';
			$select_subtipos = '<select id="id_subtipo" name="id_subtipo" class="CreacionBasicoInput">';
			$id_tipo = $_GET["id_tipo"];
			$res_subtipos = $materialInformatico->dameSubtiposSegunTipo($id_tipo);
			if(!empty($res_subtipos[0])){
				for($i=0;$i<count($res_subtipos);$i++){
					$id_subtipo = $res_subtipos[$i]["id_subtipo"];
					$subtipo = $res_subtipos[$i]["subtipo"];
					$select_subtipos .= '<option value='.$id_subtipo.' >'.utf8_encode($subtipo).'</option>';	
				}
				$select_subtipos .= '<option value="0">OTRO</option></select>';
				echo $label_subtipos.$select_subtipos;	
			}
			else {
				echo $input_subtipo = '<input type="hidden" name="id_subtipo" value=0 />';
			}
		break;
	}
}
?>