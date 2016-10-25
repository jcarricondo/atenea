<?php
// Fichero con las funciones de comprobación para AJAX del taller periféricos
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/taller_perifericos/periferico_taller.class.php");
include("../../classes/taller_perifericos/albaran_periferico.class.php");
include("../../classes/log/log_taller.class.php");

$db = new MySQL();
$user = new Usuario();
$perifericoTaller = new PerifericoTaller();
$albaranPeriferico = new AlbaranPeriferico();
$log = new Log_Taller();

$json = array();
$error = false;
if(isset($_GET["func"])){
	switch($_GET["func"]){
		// Comprobacion si existe el periferico con cierto numero de serie
		case "comprobarNumSerie":
			$num_serie = $_GET["num_serie"];
			$metodo = $_GET["metodo"];
			$id_taller = $_GET["id_taller"];

			$num_serie_string = "'".$num_serie."'";  

			if($metodo == "RECEPCIONAR") {
				$boton_proceso = '<input type="button" class="BotonEliminar" value="RECEPCIONAR" onclick="recepcionarPeriferico('.$num_serie_string.','.$id_taller.');" />';
			}
			else{
				$boton_proceso = '<input type="button" class="BotonEliminar" value="DESRECEPCIONAR" onclick="desrecepcionarPeriferico('.$num_serie_string.','.$id_taller.');" />';
			}

			// Comprobamos si existe el periferico en el taller correspondiente
			$resultados = $perifericoTaller->existePeriferico($num_serie,$id_taller);
			if($resultados != NULL){
				// Cargamos los datos del periferico
				$id_periferico = $resultados["id_periferico"];
				$tipo_periferico = $resultados["tipo_periferico"];
				$error_estado = false;

				if($metodo == "DESRECEPCIONAR"){
					// Tenemos que comprobar que el periferico se encuentre en estado OPERATIVO
					$resultado_estado = $perifericoTaller->dameEstadoActualPeriferico($id_periferico);		
					$estado = $resultado_estado["estado"];

					$error_estado = $estado != "OPERATIVO";
				}

				// Si no se da el caso de que se quiera DESRECEPCIONAR un periferico en estado diferente a OPERATIVO
				if(!$error_estado){
					// Obtenemos el nombre del tipo de periferico
					$nombre_tipo = $perifericoTaller->dameNombreTipoPeriferico($tipo_periferico);
					$nombre_tipo = $nombre_tipo["nombre"];

					// Obtenemos el ID del estado del periferico 
					$datos_id_estado_periferico = $perifericoTaller->dameIDPerifericosEstados($id_periferico);
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
					$codigo = $perifericoTaller->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
			else {
				// El periferico no existe.
				if($metodo == "RECEPCIONAR") {
					// El codigo del periferico viene definido por los 3 primeros digitos del numero de serie
					$codigo = substr($num_serie,0,3);

					$resultados = $perifericoTaller->dameDatosPerifericosTipoPorCodigo($codigo);	
					if($resultados != NULL){
						// Existe el codigo por lo que procedemos a crear el periferico con ese codigo
						$tipo_periferico = $resultados["id"];
						// Obtenemos el nombre del tipo de periferico
						$nombre_tipo = $perifericoTaller->dameNombreTipoPeriferico($tipo_periferico);
						$nombre_tipo = $nombre_tipo["nombre"];

						// Creamos el periferico asociado a ese codigo
						$perifericoTaller->datosNuevoPeriferico(NULL,$num_serie,$tipo_periferico,"OPERATIVO",$id_taller,"",$fecha_creado,$activo);
						$resultado = $perifericoTaller->guardarPeriferico();
						// Obtenemos el ultimo id_periferico creado perteneciente al taller del usuario
						$ultimo_periferico = $perifericoTaller->dameUltimoPeriferico($id_taller);
						$id_periferico = $ultimo_periferico["id_periferico"];

						if($resultado == 1){
							// Guardamos el estado en el historial de estados de los perifericos
							$resultado = $perifericoTaller->guardarLogEstadoPeriferico($id_periferico,"OPERATIVO");
							if($resultado == 1){
								// Obtenemos el ultimo id del estado de perifericos
								$ultimo_id_estado = $perifericoTaller->dameUltimoIdEstado($id_periferico);
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
								$mensaje_error = $perifericoTaller->getErrorMessage($resultado);
								$codigo = $perifericoTaller->dameHTMLconMensajeError($mensaje_error);
								echo $codigo;		
							}
						}
						else {
							// ERROR AL CREAR EL PERIFERICO
							$mensaje_error = $perifericoTaller->getErrorMessage($resultado);
							$codigo = $perifericoTaller->dameHTMLconMensajeError($mensaje_error);
							echo $codigo;
						}
					}
					else {
						// No existe el codigo en la BBDD. ERROR
						$mensaje_error = 'EL CODIGO DE PERIFERICO ASOCIADO AL NUMERO DE SERIE NO EXISTE';
						$codigo = $perifericoTaller->dameHTMLconMensajeError($mensaje_error);
						echo $codigo;
					}
				}
				else {
					// No existe el periferico para DESRECEPCIONAR. ERROR
					$mensaje_error = 'NO EXISTE EL PERIFERICO EN LA BBDD';
					$codigo = $perifericoTaller->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
		    break;
		// Recepcion de un periferico
		case "recepcionar":
			$num_serie = $_GET["num_serie"];
			$id_periferico = $_GET["id_periferico"];
			$esta_averiado = $_GET["esta_averiado"];
			$id_taller = $_GET["id_taller"];

			if($esta_averiado == 'SI'){
				$estado = "AVERIADO";
			}
			else {
				$estado = "OPERATIVO";
			}

    		// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranPeriferico->dameUltimoAlbaran($id_taller);
			$id_albaran = $id_albaran["id_albaran"];

		    // Obtenemos el id de estado del periferico que esta activo 
			$datos_periferico_estado = $perifericoTaller->dameIDPerifericosEstados($id_periferico);
			$id_periferico_estado = $datos_periferico_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del periferico
			$resultado = $perifericoTaller->desactivarLogEstadoPeriferico($id_periferico);
			if($resultado == 1){
				// Guardamos el movimiento de ese periferico asociado al albaran
			    $resultado = $albaranPeriferico->guardarMovimientoPeriferico($id_albaran,$id_periferico,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranPeriferico->guardarLogPeriferico($id_albaran,$num_serie,$esta_averiado,$id_periferico);
			    	if($resultado == 1){
			    		// Actualizamos el estado del periferico
			    		$resultado = $perifericoTaller->actualizaEstadoPeriferico($id_periferico,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de perifericos_estados		
			    			$resultado = $perifericoTaller->guardarLogEstadoPeriferico($id_periferico,$estado);
			    			if($resultado == 1){
			    				// Ok 
                                $error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $perifericoTaller->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$error_des = $perifericoTaller->getErrorMessage($resultado);
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
                $error_des = $perifericoTaller->getErrorMessage($resultado);
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
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		    		$datos_ultimo_estado = $perifericoTaller->dameIDPerifericosEstados($id_periferico);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del periferico correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $perifericoTaller->eliminarEstadoPeriferico($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del periferico 
		    			$resultado = $perifericoTaller->reactivarEstadoPeriferico($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del periferico
		    				$datos_estado = $perifericoTaller->dameEstadoPerifericoLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del periferico 
		  					if($estado != "ENVIADO"){
			    				$resultado = $perifericoTaller->actualizaEstadoPeriferico($id_periferico,$estado);
			    				if($resultado == 1){
			    					$error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL PERIFERICO
                                    $error = true;
			    					$error_des = $perifericoTaller->getErrorMessage($resultado);
			    				}
			    			}	
			    			else {
			    				$resultado = $perifericoTaller->actualizaEstadoPerifericoDesactivandolo($id_periferico,$estado);
			    				if($resultado == 1){
                                    $error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL PERIFERICO
                                    $error = true;
                                    $error_des = $perifericoTaller->getErrorMessage($resultado);
			    				}
			    			}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL PERIFERICO
                            $error = true;
		    				$error_des = $perifericoTaller->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL PERIFERICO
                        $error = true;
		    			$error_des = $perifericoTaller->getErrorMessage($resultado_estado);
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
            $id_taller = $albaranPeriferico->id_taller;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER ENTRADA PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $perifericoTaller->cargaDatosPerifericoId($id_periferico);
            $num_serie = $perifericoTaller->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
			$id_taller = $_GET["id_taller"];

			// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranPeriferico->dameUltimoAlbaran($id_taller);
			$id_albaran = $id_albaran["id_albaran"];

			// Obtenemos el id de estado del periferico que esta activo 
			$datos_periferico_estado = $perifericoTaller->dameIDPerifericosEstados($id_periferico);
			$id_periferico_estado = $datos_periferico_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del periferico
			$resultado = $perifericoTaller->desactivarLogEstadoPeriferico($id_periferico);
			if($resultado == 1){
				// Guardamos el movimiento de ese periferico asociado al albaran
			    $resultado = $albaranPeriferico->guardarMovimientoPeriferico($id_albaran,$id_periferico,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranPeriferico->guardarLogPeriferico($id_albaran,$num_serie,$esta_averiado,$id_periferico);
			    	if($resultado == 1){
			    		// Actualizamos el estado del periferico
			    		$resultado = $perifericoTaller->actualizaEstadoPerifericoDesactivandolo($id_periferico,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de perifericos_estados		
			    			$resultado = $perifericoTaller->guardarLogEstadoPeriferico($id_periferico,$estado);
			    			if($resultado == 1){
                                $error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $perifericoTaller->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$error_des = $perifericoTaller->getErrorMessage($resultado);
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
				$error_des = $perifericoTaller->getErrorMessage($resultado);
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
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		    		$datos_ultimo_estado = $perifericoTaller->dameIDPerifericosEstados($id_periferico);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del periferico correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $perifericoTaller->eliminarEstadoPeriferico($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del periferico 
		    			$resultado = $perifericoTaller->reactivarEstadoPeriferico($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del periferico
		    				$datos_estado = $perifericoTaller->dameEstadoPerifericoLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del periferico 
		    				$resultado = $perifericoTaller->actualizaEstadoPeriferico($id_periferico,$estado);
		    				if($resultado == 1){
                                $error_des = "OK!";
		    				}
		    				else {
		    					// ERROR AL ACTUALIZAR EL ESTADO DEL PERIFERICO
                                $error = true;
		    					$error_des = $perifericoTaller->getErrorMessage($resultado);
		    				}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL PERIFERICO
                            $error = true;
                            $error_des = $perifericoTaller->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL PERIFERICO
                        $error = true;
                        $error_des = $perifericoTaller->getErrorMessage($resultado_estado);
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
            $id_taller = $albaranPeriferico->id_taller;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER SALIDA PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $perifericoTaller->cargaDatosPerifericoId($id_periferico);
            $num_serie = $perifericoTaller->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","PERIFERICO",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
			$datos_periferico_estado = $perifericoTaller->dameIDPerifericosEstados($id_periferico);
			$id_periferico_estado = $datos_periferico_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del periferico
			$resultado = $perifericoTaller->desactivarLogEstadoPeriferico($id_periferico);
			if($resultado == 1){
				// Actualizamos el estado del periferico
			    $resultado = $perifericoTaller->actualizaEstadoPeriferico($id_periferico,$estado_siguiente);
	    		if($resultado == 1){
	    			// Guardamos el log de perifericos_estados		
	    			$resultado = $perifericoTaller->guardarLogEstadoPeriferico($id_periferico,$estado_siguiente);
	    			if($resultado == 1){
	    				$error_des = "OK!";
	    			}
	    			else{
                        $error = true;
	    				$error_des = $perifericoTaller->getErrorMessage($resultado);
	    			}
	    		}
	    		else {
                    $error = true;
	    			$error_des = $perifericoTaller->getErrorMessage($resultado);
		   		}				
			}
			else {
                $error = true;
				$error_des = $perifericoTaller->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $perifericoTaller->cargaDatosPerifericoId($id_periferico);
            $num_serie = $perifericoTaller->numero_serie;
            $id_taller = $perifericoTaller->id_taller;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "CAMBIO ESTADO PERIFERICO";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,0,0,"-","PERIFERICO",$num_serie,$estado_siguiente,$hubo_error,$error_des,NULL);
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
	}
}
?>