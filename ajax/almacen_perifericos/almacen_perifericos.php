<?php
// Fichero con las funciones de comprobación para AJAX del almacen periféricos
include("../../classes/mysql.class.php");
include("../../classes/sede/sede.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/almacen_perifericos/periferico_almacen.class.php");
include("../../classes/almacen_perifericos/albaran_periferico.class.php");
include("../../classes/log/log_almacen.class.php");

$db = new MySQL();
$sede = new Sede();
$user = new Usuario();
$perifericoAlmacen = new PerifericoAlmacen();
$albaranPeriferico = new AlbaranPeriferico();
$log = new Log_Almacen();

$json = array();
$error = false;
if(isset($_GET["func"])){
	switch($_GET["func"]){
		// Comprobacion si existe el periferico con cierto numero de serie
		case "comprobarNumSerie":
			$num_serie = $_GET["num_serie"];
			$metodo = $_GET["metodo"];
			$id_almacen = $_GET["id_almacen"];

			$num_serie_string = "'".$num_serie."'";  

			if($metodo == "RECEPCIONAR") {
				$boton_proceso = '<input type="button" class="BotonEliminar" value="RECEPCIONAR" onclick="recepcionarPeriferico('.$num_serie_string.','.$id_almacen.');" />';
			}
			else{
				$boton_proceso = '<input type="button" class="BotonEliminar" value="DESRECEPCIONAR" onclick="desrecepcionarPeriferico('.$num_serie_string.','.$id_almacen.');" />';
			}

			// Comprobamos si existe el periferico en el almacen correspondiente
			$resultados = $perifericoAlmacen->existePeriferico($num_serie,$id_almacen);
			if($resultados != NULL){
				// Cargamos los datos del periferico
				$id_periferico = $resultados["id_periferico"];
				$tipo_periferico = $resultados["tipo_periferico"];
				$error_estado = false;

				if($metodo == "DESRECEPCIONAR"){
					// Tenemos que comprobar que el periferico se encuentre en estado OPERATIVO
					$resultado_estado = $perifericoAlmacen->dameEstadoActualPeriferico($id_periferico);		
					$estado = $resultado_estado["estado"];

					$error_estado = $estado != "OPERATIVO";
				}

				// Si no se da el caso de que se quiera DESRECEPCIONAR un periferico en estado diferente a OPERATIVO
				if(!$error_estado){
					// Obtenemos el nombre del tipo de periferico
					$nombre_tipo = $perifericoAlmacen->dameNombreTipoPeriferico($tipo_periferico);
					$nombre_tipo = $nombre_tipo["nombre"];

					// Obtenemos el ID del estado del periferico 
					$datos_id_estado_periferico = $perifericoAlmacen->dameIDPerifericosEstados($id_periferico);
					$id_estado = $datos_id_estado_periferico["id"];

					// Preparamos la tabla de respuesta con los datos cargados
					$mensaje_error = ''; 
                	echo '<div class="ContenedorCamposCreacionBasico">
                    	    <div class="LabelCreacionBasico">NUM. SERIE *</div>
                        	<input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="" />
                        	<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaPeriferico()" />
                    	</div>
                    	<div class="ContenedorCamposCreacionBasico">
                    		<div id="error_codigo" style="height: 30px;"></div>
						</div>
                    	<br/>
                    	<div id="capa_periferico_buscador" class="ContenedorCamposCreacionBasico">
                        	<table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
                        	<tr style="height: 30px;">
                            	<th style="width:20%;">NUM. SERIE</th>
                            	<th style="width:20%;">TIPO</th>
                            	<th style="width:20%; text-align: center;">AVERIADO</th>
                            	<th style="width:20%; text-align: center;"></th>
                            	<th style="width:20%; text-align: center;"></th>
                        	</tr>
                        	<tr style="height: 35px;">
                            	<td style="width:20%;">'.$num_serie.'</td>
	                            <td style="width:20%;">'.$nombre_tipo.'</td>';

                            if($metodo == "RECEPCIONAR") {
                            	echo '<td style="width:20%; text-align: center;"><input type="checkbox" id="averiado"/></td>';
                            }
                            else {
                            	echo '<td style="width:20%; text-align: center;"><input type="checkbox" id="averiado" disabled/></td>';	
                            }	
					
					echo '<td style="width:20%; text-align: center;">'.$boton_proceso.'</td>
                         <td style="width:20%; text-align: center;"></td>
                        </tr>
                   		</table>
                        <div id="datos_periferico">
                        	<input type="hidden" id="num_serie_hidden" value="'.$num_serie.'" />
                        	<input type="hidden" id="id_periferico_hidden" value="'.$id_periferico.'" />
                        	<input type="hidden" id="nombre_tipo_hidden" value="'.$nombre_tipo.'" />
                        	<input type="hidden" id="id_estado_hidden" value="'.$id_estado.'" />
                        </div>
                    </div>';
				}
				else {
					// El periferico que se quiere DESRECEPCIONAR no esta en estado OPERATIVO. ERROR
					$mensaje_error = 'EL PERIFERICO NO SE ENCUENTRA EN ESTADO "OPERATIVO" Y NO SE PUEDE DESRECEPCIONAR';
					$codigo = $perifericoAlmacen->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
			else {
				// El periferico no existe.
				if($metodo == "RECEPCIONAR") {
					// El codigo del periferico viene definido por los 3 primeros digitos del numero de serie
					$codigo = substr($num_serie,0,3);

					$resultados = $perifericoAlmacen->dameDatosPerifericosTipoPorCodigo($codigo);	
					if($resultados != NULL){
						// Existe el codigo por lo que procedemos a crear el periferico con ese codigo
						$tipo_periferico = $resultados["id"];
						// Obtenemos el nombre del tipo de periferico
						$nombre_tipo = $perifericoAlmacen->dameNombreTipoPeriferico($tipo_periferico);
						$nombre_tipo = $nombre_tipo["nombre"];

						// Creamos el periferico asociado a ese codigo
						$perifericoAlmacen->datosNuevoPeriferico(NULL,$num_serie,$tipo_periferico,"OPERATIVO",$id_almacen,"",$fecha_creado,$activo);
						$resultado = $perifericoAlmacen->guardarPeriferico();
						// Obtenemos el ultimo id_periferico creado perteneciente al almacen del usuario
						$ultimo_periferico = $perifericoAlmacen->dameUltimoPeriferico($id_almacen);
						$id_periferico = $ultimo_periferico["id_periferico"];

						if($resultado == 1){
							// Guardamos el estado en el historial de estados de los perifericos
							$resultado = $perifericoAlmacen->guardarLogEstadoPeriferico($id_periferico,"OPERATIVO");
							if($resultado == 1){
								// Obtenemos el ultimo id del estado de perifericos
								$ultimo_id_estado = $perifericoAlmacen->dameUltimoIdEstado($id_periferico);
								$id_estado = $ultimo_id_estado["id"];

				                echo '<div class="ContenedorCamposCreacionBasico">
				                        <div class="LabelCreacionBasico">NUM. SERIE *</div>
				                        <input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="" />
				                        <input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaPeriferico()" />
				                    </div>
				                    <div class="ContenedorCamposCreacionBasico">
				                    	<div id="error_codigo" style="height: 30px;"></div>
									</div>
				                    <br/>
				                    <div id="capa_periferico_buscador" class="ContenedorCamposCreacionBasico">
				                        <table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
				                        <tr style="height: 30px;">
				                            <th style="width:20%;">NUM. SERIE</th>
				                            <th style="width:20%;">TIPO</th>
				                            <th style="width:20%; text-align: center;">AVERIADO</th>
				                            <th style="width:20%; text-align: center;"></th>
				                            <th style="width:20%; text-align: center;"></th>
				                        </tr>
				                        <tr style="height: 35px;">
				                            <td style="width:20%;">'.$num_serie.'</td>
				                            <td style="width:20%;">'.$nombre_tipo.'</td>
				                            <td style="width:20%; text-align: center;"><input type="checkbox" id="averiado"/></td>
				                            <td style="width:20%; text-align: center;">'.$boton_proceso.'</td>
				                            <td style="width:20%; text-align: center;"></td>
				                        </tr>
				                   		</table>
				                        <div id="datos_periferico">
			   	                        	<input type="hidden" id="num_serie_hidden" value="'.$num_serie.'" />
			   	                        	<input type="hidden" id="id_periferico_hidden" value="'.$id_periferico.'" />
		                        			<input type="hidden" id="nombre_tipo_hidden" value="'.$nombre_tipo.'" />
		                        			<input type="hidden" id="id_estado_hidden" value="'.$id_estado.'" />
				                        </div>
				                    </div>';
							}
							else{
								$mensaje_error = $perifericoAlmacen->getErrorMessage($resultado);
								$codigo = $perifericoAlmacen->dameHTMLconMensajeError($mensaje_error);
								echo $codigo;		
							}
						}
						else {
							// ERROR AL CREAR EL PERIFERICO
							$mensaje_error = $perifericoAlmacen->getErrorMessage($resultado);
							$codigo = $perifericoAlmacen->dameHTMLconMensajeError($mensaje_error);
							echo $codigo;
						}
					}
					else {
						// No existe el codigo en la BBDD. ERROR
						$mensaje_error = 'EL CODIGO DE PERIFERICO ASOCIADO AL NUMERO DE SERIE NO EXISTE';
						$codigo = $perifericoAlmacen->dameHTMLconMensajeError($mensaje_error);
						echo $codigo;
					}
				}
				else {
					// No existe el periferico para DESRECEPCIONAR. ERROR
					$mensaje_error = 'NO EXISTE EL PERIFERICO EN LA BBDD';
					$codigo = $perifericoAlmacen->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
		    break;
		// Recepcion de un periferico
		case "recepcionar":
			$num_serie = $_GET["num_serie"];
			$id_periferico = $_GET["id_periferico"];
			$esta_averiado = $_GET["esta_averiado"];
			$id_almacen = $_GET["id_almacen"];

			if($esta_averiado == 'SI'){
				$estado = "AVERIADO";
			}
			else {
				$estado = "OPERATIVO";
			}

    		// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranPeriferico->dameUltimoAlbaran($id_almacen);
			$id_albaran = $id_albaran["id_albaran"];

		    // Obtenemos el id de estado del periferico que esta activo 
			$datos_periferico_estado = $perifericoAlmacen->dameIDPerifericosEstados($id_periferico);
			$id_periferico_estado = $datos_periferico_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del periferico
			$resultado = $perifericoAlmacen->desactivarLogEstadoPeriferico($id_periferico);
			if($resultado == 1){
				// Guardamos el movimiento de ese periferico asociado al albaran
			    $resultado = $albaranPeriferico->guardarMovimientoPeriferico($id_albaran,$id_periferico,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranPeriferico->guardarLogPeriferico($id_albaran,$num_serie,$esta_averiado,$id_periferico);
			    	if($resultado == 1){
			    		// Actualizamos el estado del periferico
			    		$resultado = $perifericoAlmacen->actualizaEstadoPeriferico($id_periferico,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de perifericos_estados		
			    			$resultado = $perifericoAlmacen->guardarLogEstadoPeriferico($id_periferico,$estado);
			    			if($resultado == 1){
			    				// Ok 
                                $error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $perifericoAlmacen->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$error_des = $perifericoAlmacen->getErrorMessage($resultado);
			    		}
			    	}
			    	else{
                        $error = true;
			    		$error_des = $albaranPeriferico->getErrorMessage($resultado);
			    	}
			    }
			    else{
                    $error = true;
			    	$error_des = $albaranPeriferico->getErrorMessage($resultado);
			    }	
			}
			else {
                $error = true;
                $error_des = $perifericoAlmacen->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $albaranPeriferico->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranPeriferico->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "ENTRADA PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
		    break;
		// Deshacer recepcion del periferico
		case "deshacerRecepcion":
			$id_periferico = $_GET["id_periferico"];
			$id_estado_restaurar = $_GET["id_estado"];
			$id_albaran = $_GET["id_albaran"];

			// Desactivamos el último movimiento realizado
			// Primero obtenemos los datos del último movimiento de albarán de ese periférico
		    $datos_ultimo_movimiento = $albaranPeriferico->dameUltimoMovimientoPeriferico($id_periferico);
		    $id_movimiento = $datos_ultimo_movimiento["id"];

	    	// Eliminamos el movimiento del albaran del periferico correspondiente a la operacion que se ha deshecho
	    	$resultado_movimiento = $albaranPeriferico->eliminarEstadoPeriferico($id_movimiento);
			if($resultado_movimiento == 1){
				// Desactivamos el log del albaran de ese periferico correspondiente a la operacion que se ha deshecho
		    	$resultado = $albaranPeriferico->desactivarLogPeriferico($id_albaran,$id_periferico);
		    	if($resultado == 1){
		    		// Obtenemos el id del estado del periferico de la operacion que se ha deshecho
		    		$datos_ultimo_estado = $perifericoAlmacen->dameIDPerifericosEstados($id_periferico);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del periferico correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $perifericoAlmacen->eliminarEstadoPeriferico($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del periferico 
		    			$resultado = $perifericoAlmacen->reactivarEstadoPeriferico($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del periferico
		    				$datos_estado = $perifericoAlmacen->dameEstadoPerifericoLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del periferico 
		  					if($estado != "ENVIADO"){
			    				$resultado = $perifericoAlmacen->actualizaEstadoPeriferico($id_periferico,$estado);
			    				if($resultado == 1){
			    					$error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL PERIFERICO
                                    $error = true;
			    					$error_des = $perifericoAlmacen->getErrorMessage($resultado);
			    				}
			    			}	
			    			else {
			    				$resultado = $perifericoAlmacen->actualizaEstadoPerifericoDesactivandolo($id_periferico,$estado);
			    				if($resultado == 1){
                                    $error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL PERIFERICO
                                    $error = true;
                                    $error_des = $perifericoAlmacen->getErrorMessage($resultado);
			    				}
			    			}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL PERIFERICO
                            $error = true;
		    				$error_des = $perifericoAlmacen->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL PERIFERICO
                        $error = true;
		    			$error_des = $perifericoAlmacen->getErrorMessage($resultado_estado);
		    		}
		    	}
		    	else {
		    		// ERROR AL DESACTIVAR EL LOG DEL ALBARAN DEL PERIFERICO
                    $error = true;
		    		$error_des = $albaranPeriferico->getErrorMessage($resultado);
		    	}
			}
            else {
                $error = true;
                $error_des = $albaranPeriferico->getErrorMessage($resultado_movimiento);
            }

            // Preparamos los datos del log
            $albaranPeriferico->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranPeriferico->id_usuario;
            $id_almacen = $albaranPeriferico->id_almacen;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER ENTRADA PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $perifericoAlmacen->cargaDatosPerifericoId($id_periferico);
            $num_serie = $perifericoAlmacen->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
		    break;
		// Desrecepción de un periférico
		case "desrecepcionar":
			$num_serie = $_GET["num_serie"];
			$id_periferico = $_GET["id_periferico"];
			$estado = "ENVIADO";
			$esta_averiado = "NO";
			$id_almacen = $_GET["id_almacen"];

			// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranPeriferico->dameUltimoAlbaran($id_almacen);
			$id_albaran = $id_albaran["id_albaran"];

			// Obtenemos el id de estado del periferico que esta activo 
			$datos_periferico_estado = $perifericoAlmacen->dameIDPerifericosEstados($id_periferico);
			$id_periferico_estado = $datos_periferico_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del periferico
			$resultado = $perifericoAlmacen->desactivarLogEstadoPeriferico($id_periferico);
			if($resultado == 1){
				// Guardamos el movimiento de ese periferico asociado al albaran
			    $resultado = $albaranPeriferico->guardarMovimientoPeriferico($id_albaran,$id_periferico,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranPeriferico->guardarLogPeriferico($id_albaran,$num_serie,$esta_averiado,$id_periferico);
			    	if($resultado == 1){
			    		// Actualizamos el estado del periferico
			    		$resultado = $perifericoAlmacen->actualizaEstadoPerifericoDesactivandolo($id_periferico,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de perifericos_estados		
			    			$resultado = $perifericoAlmacen->guardarLogEstadoPeriferico($id_periferico,$estado);
			    			if($resultado == 1){
                                $error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $perifericoAlmacen->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$error_des = $perifericoAlmacen->getErrorMessage($resultado);
			    		}
			    	}
			    	else{
                        $error = true;
			    		$error_des = $albaranPeriferico->getErrorMessage($resultado);
			    	}
			    }
			    else{
                    $error = true;
			    	$error_des = $albaranPeriferico->getErrorMessage($resultado);
			    }	
			}
			else {
                $error = true;
				$error_des = $perifericoAlmacen->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $albaranPeriferico->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranPeriferico->id_usuario;

            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "SALIDA PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
		    break;
		// Deshacer desrecepcion del periferico
		case "deshacerDesRecepcion":
			$id_periferico = $_GET["id_periferico"];
			$id_estado_restaurar = $_GET["id_estado"];
			$id_albaran = $_GET["id_albaran"];

			// Desactivamos el ultimo movimiento realizado
			// Primero obtenemos los datos del ultimo movimiento de albaran de ese periferico
		    $datos_ultimo_movimiento = $albaranPeriferico->dameUltimoMovimientoPeriferico($id_periferico);
		    $id_movimiento = $datos_ultimo_movimiento["id"];

	    	// Eliminamos el movimiento del albaran del periferico correspondiente a la operacion que se ha deshecho
	    	$resultado_movimiento = $albaranPeriferico->eliminarEstadoPeriferico($id_movimiento);
			if($resultado_movimiento == 1){
				// Desactivamos el log del albaran de ese periferico correspondiente a la operacion que se ha deshecho
		    	$resultado = $albaranPeriferico->desactivarLogPeriferico($id_albaran,$id_periferico);
		    	if($resultado == 1){
		    		// Obtenemos el id del estado del periferico de la operacion que se ha deshecho
		    		$datos_ultimo_estado = $perifericoAlmacen->dameIDPerifericosEstados($id_periferico);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del periferico correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $perifericoAlmacen->eliminarEstadoPeriferico($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del periferico 
		    			$resultado = $perifericoAlmacen->reactivarEstadoPeriferico($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del periferico
		    				$datos_estado = $perifericoAlmacen->dameEstadoPerifericoLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del periferico 
		    				$resultado = $perifericoAlmacen->actualizaEstadoPeriferico($id_periferico,$estado);
		    				if($resultado == 1){
                                $error_des = "OK!";
		    				}
		    				else {
		    					// ERROR AL ACTUALIZAR EL ESTADO DEL PERIFERICO
                                $error = true;
		    					$error_des = $perifericoAlmacen->getErrorMessage($resultado);
		    				}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL PERIFERICO
                            $error = true;
                            $error_des = $perifericoAlmacen->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL PERIFERICO
                        $error = true;
                        $error_des = $perifericoAlmacen->getErrorMessage($resultado_estado);
		    		}
		    	}
		    	else {
		    		// ERROR AL DESACTIVAR EL LOG DEL ALBARAN DEL PERIFERICO
                    $error = true;
		    		$error_des = $albaranPeriferico->getErrorMessage($resultado);
		    	}
			}
            else {
                $error = true;
                $error_des = $albaranPeriferico->getErrorMessage($resultado_movimiento);
            }

            // Preparamos los datos del log
            $albaranPeriferico->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranPeriferico->id_usuario;
            $id_almacen = $albaranPeriferico->id_almacen;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER SALIDA PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $perifericoAlmacen->cargaDatosPerifericoId($id_periferico);
            $num_serie = $perifericoAlmacen->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);

            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
		    break;
		// Cambiar estado de un periferico
		case "cambiarEstado":
			$id_periferico = $_GET["id_periferico"];
			$estado = $_GET["estado"];
            $id_usuario = $_GET["id_usuario"];

			if($estado == "AVERIADO"){
				$estado_siguiente = "EN REPARACION";
			}
			else if($estado == "EN REPARACION") {
				$estado_siguiente = "OPERATIVO";
			}
			else {
				// $estado == OPERATIVO
				$estado_siguiente = "AVERIADO";
			}

			// Obtenemos el id de estado del periferico que esta activo 
			$datos_periferico_estado = $perifericoAlmacen->dameIDPerifericosEstados($id_periferico);
			$id_periferico_estado = $datos_periferico_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del periferico
			$resultado = $perifericoAlmacen->desactivarLogEstadoPeriferico($id_periferico);
			if($resultado == 1){
				// Actualizamos el estado del periferico
			    $resultado = $perifericoAlmacen->actualizaEstadoPeriferico($id_periferico,$estado_siguiente);
	    		if($resultado == 1){
	    			// Guardamos el log de perifericos_estados		
	    			$resultado = $perifericoAlmacen->guardarLogEstadoPeriferico($id_periferico,$estado_siguiente);
	    			if($resultado == 1){
	    				$error_des = "OK!";
	    			}
	    			else{
                        $error = true;
	    				$error_des = $perifericoAlmacen->getErrorMessage($resultado);
	    			}
	    		}
	    		else {
                    $error = true;
	    			$error_des = $perifericoAlmacen->getErrorMessage($resultado);
		   		}				
			}
			else {
                $error = true;
				$error_des = $perifericoAlmacen->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $perifericoAlmacen->cargaDatosPerifericoId($id_periferico);
            $num_serie = $perifericoAlmacen->numero_serie;
            $id_almacen = $perifericoAlmacen->id_almacen;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "CAMBIO ESTADO PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,0,0,"-","PERIFERICO",$num_serie,$estado_siguiente,$hubo_error,$error_des,NULL);
            $res_log = $log->guardarLog();
            if($res_log != 1){
                $error = true;
                $error_des = $log->getErrorMessage($res_log);
            }

            // Preparamos el array json para devolver los datos de la operación
            $array_movimiento = array(
                "error" => $error,
                "error_des" => $error_des);
            $json = array("mov" => $array_movimiento);
            echo json_encode($json, JSON_FORCE_OBJECT);
    		break;
        case "cargaAlmacenes":
            $id_sede = $_GET["id_sede"];
            $respuesta .= '<select id="almacenes" name="almacenes" class="BuscadorInputAlmacen">';
            $respuesta .= '<option value="">Seleccionar</option>';

            // Obtenemos los almacenes de esa sede
            $res_almacenes = $sede->dameAlmacenesMantenimientoSede($id_sede);
            for($i=0;$i<count($res_almacenes);$i++){
                $id_almacen = $res_almacenes[$i]["id_almacen"];
                $nombre = $res_almacenes[$i]["almacen"];
                $respuesta .= '<option value="'.$id_almacen.'">'.$nombre.'</option>';
            }

            $respuesta .= '</select>';
            echo $respuesta;
            break;
		case "cargaMotivos":
			$json = array();
			// Obtenemos el almacen y el tipo de albarán
			$id_almacen = $_GET["id_almacen"];
			$tipo_albaran = $_GET["tipo_albaran"];

			if($tipo_albaran == "ENTRADA") $res_motivos = $albaranPeriferico->dameMotivosAlbaranEntradaPerifericos($id_almacen);
			else if($tipo_albaran == "SALIDA") $res_motivos = $albaranPeriferico->dameMotivosAlbaranSalidaPerifericos($id_almacen);
			else $res_motivos = $albaranPeriferico->dameMotivosAlbaranPerifericos($id_almacen);

			// Cargamos los motivos y los guardamos en un array para incluirlo en la variable JSON
			for($i=0;$i<count($res_motivos);$i++) $array_motivos[] = $res_motivos[$i]["motivo"];

			$json = $array_motivos;
			echo json_encode($json, JSON_FORCE_OBJECT);
			break;
		case "cargaMotivosBuscador":
			$json = array();
			// Obtenemos la sede
			$id_sede = $_GET["id_sede"];
			// Obtenemos los motivos según la sede
			$res_motivos = $sede->dameMotivosAlbaranPerifericosSede($id_sede);
			// Cargamos los motivos y los guardamos en un array para incluirlo en la variable JSON
			for($i=0;$i<count($res_motivos);$i++) $array_motivos[] = $res_motivos[$i]["motivo"];

			$json = $array_motivos;
			echo json_encode($json, JSON_FORCE_OBJECT);
			break;
		default:

		break;
	}
}
?>