<?php
// Fichero con las funciones de comprobación para AJAX del taller simuladores
include("../../classes/mysql.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/taller_simuladores/simulador_taller.class.php");
include("../../classes/taller_simuladores/albaran_simulador.class.php");
include("../../classes/log/log_taller.class.php");

$db = new MySQL();
$user = new Usuario();
$simuladorTaller = new SimuladorTaller();
$albaranSimulador = new AlbaranSimulador();
$log = new Log_Taller();

$json = array();
$error = false;
if(isset($_GET["func"])){
	switch($_GET["func"]){
		// Comprobación si existe el simulador con cierto número de serie
		case "comprobarNumSerie":
			$num_serie = $_GET["num_serie"];
			$metodo = $_GET["metodo"];
			$id_taller = $_GET["id_taller"];

			$num_serie_string = "'".$num_serie."'";  

			if($metodo == "RECEPCIONAR") {
				$boton_proceso = '<input type="button" class="BotonEliminar" value="RECEPCIONAR" onclick="recepcionarSimulador('.$num_serie_string.','.$id_taller.');" />';
			}
			else{
				$boton_proceso = '<input type="button" class="BotonEliminar" value="DESRECEPCIONAR" onclick="desrecepcionarSimulador('.$num_serie_string.','.$id_taller.');" />';	
			}

			// Comprobamos si existe el simulador en el taller correspondiente
			$resultados = $simuladorTaller->existeSimulador($num_serie,$id_taller);
			if($resultados != NULL){
				// Cargamos los datos del simulador
				$id_simulador = $resultados["id_simulador"];
				$error_estado = false;

				if($metodo == "DESRECEPCIONAR"){
					// Tenemos que comprobar que el simulador se encuentre en estado OPERATIVO
					$resultado_estado = $simuladorTaller->dameEstadoActualSimulador($id_simulador);		
					$estado = $resultado_estado["estado"];

					$error_estado = $estado != "OPERATIVO";
				}

				// Si no se da el caso de que se quiera DESRECEPCIONAR un simulador en estado diferente a OPERATIVO
				if(!$error_estado){
					// Obtenemos el ID del estado del simulador
					$datos_id_estado_simulador = $simuladorTaller->dameIDSimuladoresEstados($id_simulador);
					$id_estado = $datos_id_estado_simulador["id"];

					// Preparamos la tabla de respuesta con los datos cargados
					$mensaje_error = ''; 
                	echo '<div class="ContenedorCamposCreacionBasico">
                    	    <div class="LabelCreacionBasico">NUM. SERIE *</div>
                        	<input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="" />
                        	<input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaSimulador()" />
                    	</div>
                    	<div class="ContenedorCamposCreacionBasico">
                    		<div id="error_codigo" style="height: 30px;"></div>
						</div>
                    	<br/>
                    	<div id="capa_simulador_buscador" class="ContenedorCamposCreacionBasico">
                        	<table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
                        	<tr style="height: 30px;">
                            	<th style="width:25%;">NUM. SERIE</th>
                            	<th style="width:25%; text-align: center;">AVERIADO</th>
                            	<th style="width:25%; text-align: center;"></th>
                            	<th style="width:25%; text-align: center;"></th>
                        	</tr>
                        	<tr style="height: 35px;">
                            	<td style="width:25%;">'.$num_serie.'</td>';
	                            
                            if($metodo == "RECEPCIONAR") {
                            	echo '<td style="width:25%; text-align: center;"><input type="checkbox" id="averiado"/></td>';
                            }
                            else {
                            	echo '<td style="width:25%; text-align: center;"><input type="checkbox" id="averiado" disabled/></td>';	
                            }	
					
					echo '<td style="width:25%; text-align: center;">'.$boton_proceso.'</td>
                         <td style="width:25%; text-align: center;"></td>
                        </tr>
                   		</table>
                        <div id="datos_simulador">
                        	<input type="hidden" id="num_serie_hidden" value="'.$num_serie.'" />
                        	<input type="hidden" id="id_simulador_hidden" value="'.$id_simulador.'" />
                        	<input type="hidden" id="id_estado_hidden" value="'.$id_estado.'" />
                        </div>
                    </div>';
				}
				else {
					// El simulador que se quiere DESRECEPCIONAR no esta en estado OPERATIVO. ERROR
					$mensaje_error = 'EL SIMULADOR NO SE ENCUENTRA EN ESTADO "OPERATIVO" Y NO SE PUEDE DESRECEPCIONAR';
					$codigo = $simuladorTaller->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
			else {
				// El simulador no existe.
				if($metodo == "RECEPCIONAR") {
					// Creamos el simulador
					$simuladorTaller->datosNuevoSimulador(NULL,$num_serie,"OPERATIVO",$id_taller,"",$fecha_creado,$activo);
					$resultado = $simuladorTaller->guardarSimulador();
					// Obtenemos el ultimo id_simulador creado perteneciente al taller del usuario
					$ultimo_simulador = $simuladorTaller->dameUltimoSimulador($id_taller);
					$id_simulador = $ultimo_simulador["id_simulador"];

					if($resultado == 1){
						// Guardamos el estado en el historial de estados de los simuladores
						$resultado = $simuladorTaller->guardarLogEstadoSimulador($id_simulador,"OPERATIVO");
						if($resultado == 1){
							// Obtenemos el ultimo id del estado de simuladores
							$ultimo_id_estado = $simuladorTaller->dameUltimoIdEstado($id_simulador);
							$id_estado = $ultimo_id_estado["id"];

			                echo '<div class="ContenedorCamposCreacionBasico">
			                        	<div class="LabelCreacionBasico">NUM. SERIE *</div>
				                        <input type="text" id="num_serie" name="num_serie" class="CreacionBasicoInput" value="" />
				                        <input type="button" class="BotonEliminar" style="margin: 3px 0px 0px 0px;" value="CARGAR" onclick="cargaSimulador()" />
				                    </div>
				                    <div class="ContenedorCamposCreacionBasico">
				                    	<div id="error_codigo" style="height: 30px;"></div>
									</div>
				                    <br/>
				                    <div id="capa_simulador_buscador" class="ContenedorCamposCreacionBasico">
				                        <table id="tabla_buscador" style="width: 1100px; min-width: 480px;">
				                        <tr style="height: 30px;">
				                            <th style="width:25%;">NUM. SERIE</th>
				                            <th style="width:25%; text-align: center;">AVERIADO</th>
				                            <th style="width:25%; text-align: center;"></th>
				                            <th style="width:25%; text-align: center;"></th>
				                        </tr>
				                        <tr style="height: 35px;">
				                            <td style="width:25%;">'.$num_serie.'</td>
				                            <td style="width:25%; text-align: center;"><input type="checkbox" id="averiado"/></td>
				                            <td style="width:25%; text-align: center;">'.$boton_proceso.'</td>
				                            <td style="width:25%; text-align: center;"></td>
				                        </tr>
				                   		</table>
				                        <div id="datos_simulador">
			   	                        	<input type="hidden" id="num_serie_hidden" value="'.$num_serie.'" />
			   	                        	<input type="hidden" id="id_simulador_hidden" value="'.$id_simulador.'" />
		                        			<input type="hidden" id="id_estado_hidden" value="'.$id_estado.'" />
				                        </div>
				                    </div>';
							}
							else{
								$mensaje_error = $simuladorTaller->getErrorMessage($resultado);
								$codigo = $simuladorTaller->dameHTMLconMensajeError($mensaje_error);
								echo $codigo;		
							}
						}
				}
				else {
					// No existe el simulador para DESRECEPCIONAR. ERROR
					$mensaje_error = 'NO EXISTE EL SIMULADOR EN LA BBDD';
					$codigo = $simuladorTaller->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
		break;	
		// Recepción de un simulador
		case "recepcionar":
			$num_serie = $_GET["num_serie"];
			$id_simulador = $_GET["id_simulador"];
			$esta_averiado = $_GET["esta_averiado"];
			$id_taller = $_GET["id_taller"];

			if($esta_averiado == 'SI'){
				$estado = "AVERIADO";
			}
			else {
				$estado = "OPERATIVO";
			}

    		// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranSimulador->dameUltimoAlbaran($id_taller);
			$id_albaran = $id_albaran["id_albaran"];

		    // Obtenemos el id de estado del simulador que esta activo 
			$datos_simulador_estado = $simuladorTaller->dameIDSimuladoresEstados($id_simulador);
			$id_simulador_estado = $datos_simulador_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del simulador
			$resultado = $simuladorTaller->desactivarLogEstadoSimulador($id_simulador);
			if($resultado == 1){
				// Guardamos el movimiento de ese simulador asociado al albaran
			    $resultado = $albaranSimulador->guardarMovimientoSimulador($id_albaran,$id_simulador,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranSimulador->guardarLogSimulador($id_albaran,$num_serie,$esta_averiado,$id_simulador);
			    	if($resultado == 1){
			    		// Actualizamos el estado del simulador
			    		$resultado = $simuladorTaller->actualizaEstadoSimulador($id_simulador,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de simuladores_estados		
			    			$resultado = $simuladorTaller->guardarLogEstadoSimulador($id_simulador,$estado);
			    			if($resultado == 1){
			    				$error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $simuladorTaller->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$$error_des = $simuladorTaller->getErrorMessage($resultado);
			    		}
			    	}
			    	else{
                        $error = true;
			    		$error_des = $albaranSimulador->getErrorMessage($resultado);
			    	}
			    }
			    else{
                    $error = true;
			    	$error_des = $albaranSimulador->getErrorMessage($resultado);
			    }	
			}
			else {
                $error = true;
				$error_des = $simuladorTaller->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $albaranSimulador->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranSimulador->id_usuario;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "ENTRADA SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		// Deshacer recepcion del simulador
		case "deshacerRecepcion":
			$id_simulador = $_GET["id_simulador"];
			$id_estado_restaurar = $_GET["id_estado"];
			$id_albaran = $_GET["id_albaran"];

			// Desactivamos el ultimo movimiento realizado
			// Primero obtenemos los datos del ultimo movimiento de albaran de ese simulador
		    $datos_ultimo_movimiento = $albaranSimulador->dameUltimoMovimientoSimulador($id_simulador);
		    $id_movimiento = $datos_ultimo_movimiento["id"];

	    	// Eliminamos el movimiento del albaran del simulador correspondiente a la operacion que se ha deshecho
	    	$resultado_movimiento = $albaranSimulador->eliminarEstadoSimulador($id_movimiento);
			if($resultado_movimiento == 1){
				// Desactivamos el log del albaran de ese simulador correspondiente a la operacion que se ha deshecho
		    	$resultado = $albaranSimulador->desactivarLogSimulador($id_albaran,$id_simulador);
		    	if($resultado == 1){
		    		// Obtenemos el id del estado del simulador de la operacion que se ha deshecho
		    		$datos_ultimo_estado = $simuladorTaller->dameIDSimuladoresEstados($id_simulador);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del simulador correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $simuladorTaller->eliminarEstadoSimulador($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del simulador 
		    			$resultado = $simuladorTaller->reactivarEstadoSimulador($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del simulador
		    				$datos_estado = $simuladorTaller->dameEstadoSimuladorLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del simulador 
		  					if($estado != "ENVIADO"){
			    				$resultado = $simuladorTaller->actualizaEstadoSimulador($id_simulador,$estado);
			    				if($resultado == 1){
			    					$error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL SIMULADOR
                                    $error = true;
			    					$error_des = $simuladorTaller->getErrorMessage($resultado);
			    				}
			    			}	
			    			else {
			    				$resultado = $simuladorTaller->actualizaEstadoSimuladorDesactivandolo($id_simulador,$estado);
			    				if($resultado == 1){
                                    $error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL SIMULADOR
                                    $error = true;
			    					$error_des = $simuladorTaller->getErrorMessage($resultado);
			    				}
			    			}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL SIMULADOR
                            $error = true;
		    				$error_des = $simuladorTaller->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL SIMULADOR
                        $error = true;
		    			$error_des = $simuladorTaller->getErrorMessage($resultado);
		    		}
		    	}
		    	else {
		    		// ERROR AL DESACTIVAR EL LOG DEL ALBARAN DEL SIMULADOR
		    		$error = true;
                    $error_des = $albaranSimulador->getErrorMessage($resultado);
		    	}
			}
            else {
                $error = true;
                $error_des = $albaranSimulador->getErrorMessage($resultado_movimiento);
            }

            // Preparamos los datos del log
            $albaranSimulador->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranSimulador->id_usuario;
            $id_taller = $albaranSimulador->id_taller;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER ENTRADA SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $simuladorTaller->cargaDatosSimuladorId($id_simulador);
            $num_serie = $simuladorTaller->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		// Desrecepción de un simulador
		case "desrecepcionar":
			$num_serie = $_GET["num_serie"];
			$id_simulador = $_GET["id_simulador"];
			$estado = "ENVIADO";
			$esta_averiado = "NO";
			$id_taller = $_GET["id_taller"];

			// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranSimulador->dameUltimoAlbaran($id_taller);
			$id_albaran = $id_albaran["id_albaran"];

			// Obtenemos el id de estado del simulador que esta activo 
			$datos_simulador_estado = $simuladorTaller->dameIDSimuladoresEstados($id_simulador);
			$id_simulador_estado = $datos_simulador_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del simulador
			$resultado = $simuladorTaller->desactivarLogEstadoSimulador($id_simulador);
			if($resultado == 1){
				// Guardamos el movimiento de ese simulador asociado al albaran
			    $resultado = $albaranSimulador->guardarMovimientoSimulador($id_albaran,$id_simulador,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranSimulador->guardarLogSimulador($id_albaran,$num_serie,$esta_averiado,$id_simulador);
			    	if($resultado == 1){
			    		// Actualizamos el estado del simulador
			    		$resultado = $simuladorTaller->actualizaEstadoSimuladorDesactivandolo($id_simulador,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de simuladores_estados		
			    			$resultado = $simuladorTaller->guardarLogEstadoSimulador($id_simulador,$estado);
			    			if($resultado == 1){
                                $error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $simuladorTaller->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$error_des = $simuladorTaller->getErrorMessage($resultado);
			    		}
			    	}
			    	else{
                        $error = true;
			    		$error_des = $albaranSimulador->getErrorMessage($resultado);
			    	}
			    }
			    else{
                    $error = true;
			    	$error_des = $albaranSimulador->getErrorMessage($resultado);
			    }	
			}
			else {
                $error = true;
				$error_des = $simuladorTaller->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $albaranSimulador->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranSimulador->id_usuario;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "SALIDA SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		// Deshacer desrecepcion del simulador
		case "deshacerDesRecepcion":
			$id_simulador = $_GET["id_simulador"];
			$id_estado_restaurar = $_GET["id_estado"];
			$id_albaran = $_GET["id_albaran"];

			// Desactivamos el ultimo movimiento realizado
			// Primero obtenemos los datos del ultimo movimiento de albaran de ese simulador
		    $datos_ultimo_movimiento = $albaranSimulador->dameUltimoMovimientoSimulador($id_simulador);
		    $id_movimiento = $datos_ultimo_movimiento["id"];

	    	// Eliminamos el movimiento del albaran del simulador correspondiente a la operacion que se ha deshecho
	    	$resultado_movimiento = $albaranSimulador->eliminarEstadoSimulador($id_movimiento);
			if($resultado_movimiento == 1){
				// Desactivamos el log del albaran de ese simulador correspondiente a la operacion que se ha deshecho
		    	$resultado = $albaranSimulador->desactivarLogSimulador($id_albaran,$id_simulador);
		    	if($resultado == 1){
		    		// Obtenemos el id del estado del simulador de la operacion que se ha deshecho
		    		$datos_ultimo_estado = $simuladorTaller->dameIDSimuladoresEstados($id_simulador);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del simulador correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $simuladorTaller->eliminarEstadoSimulador($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del simulador
		    			$resultado = $simuladorTaller->reactivarEstadoSimulador($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del simulador
		    				$datos_estado = $simuladorTaller->dameEstadoSimuladorLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del simulador 
		    				$resultado = $simuladorTaller->actualizaEstadoSimulador($id_simulador,$estado);
		    				if($resultado == 1){
		    					$error_des = "OK!";
		    				}
		    				else {
		    					// ERROR AL ACTUALIZAR EL ESTADO DEL SIMULADOR
                                $error = true;
                                $error_des = $simuladorTaller->getErrorMessage($resultado);
		    				}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL SIMULADOR
                            $error = true;
		    				$error_des = $simuladorTaller->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL SIMULADOR
                        $error = true;
		    			$error_des = $simuladorTaller->getErrorMessage($resultado_estado);
		    		}
		    	}
		    	else {
		    		// ERROR AL DESACTIVAR EL LOG DEL ALBARAN DEL SIMULADOR
                    $error = true;
		    		$error_des = $albaranSimulador->getErrorMessage($resultado);
		    	}
			}
            else {
                $error = true;
                $error_des = $albaranSimulador->getErrorMessage($resultado_movimiento);
            }

            // Preparamos los datos del log
            $albaranSimulador->cargaDatosAlbaranId($id_albaran);
            $id_usuario = $albaranSimulador->id_usuario;
            $id_taller = $albaranSimulador->id_taller;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER SALIDA SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $simuladorTaller->cargaDatosSimuladorId($id_simulador);
            $num_serie = $simuladorTaller->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		// Cambiar estado de un simulador
		case "cambiarEstado":
			$id_simulador = $_GET["id_simulador"];
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

			// Obtenemos el id de estado del simulador que esta activo 
			$datos_simulador_estado = $simuladorTaller->dameIDSimuladoresEstados($id_simulador);
			$id_simulador_estado = $datos_simulador_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del simulador
			$resultado = $simuladorTaller->desactivarLogEstadoSimulador($id_simulador);
			if($resultado == 1){
				// Actualizamos el estado del simulador
			    $resultado = $simuladorTaller->actualizaEstadoSimulador($id_simulador,$estado_siguiente);
	    		if($resultado == 1){
	    			// Guardamos el log de simuladores_estados		
	    			$resultado = $simuladorTaller->guardarLogEstadoSimulador($id_simulador,$estado_siguiente);
	    			if($resultado == 1){
	    				$error_des = "OK!";
	    			}
	    			else{
                        $error = true;
                        $error_des = $simuladorTaller->getErrorMessage($resultado);
	    			}
	    		}
	    		else {
                    $error = true;
	    			$error_des = $simuladorTaller->getErrorMessage($resultado);
		   		}				
			}
			else {
                $error = true;
				$error_des = $simuladorTaller->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $simuladorTaller->cargaDatosSimuladorId($id_simulador);
            $num_serie = $simuladorTaller->numero_serie;
            $id_taller = $simuladorTaller->id_taller;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "CAMBIO ESTADO SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_taller,0,0,"-","SIMULADOR",$num_serie,$estado_siguiente,$hubo_error,$error_des,NULL);
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