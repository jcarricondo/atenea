<?php
// Fichero con las funciones de comprobación para AJAX del almacen simuladores
include("../../classes/mysql.class.php");
include("../../classes/sede/sede.class.php");
include("../../classes/basicos/usuario.class.php");
include("../../classes/almacen_simuladores/simulador_almacen.class.php");
include("../../classes/almacen_simuladores/albaran_simulador.class.php");
include("../../classes/log/log_almacen.class.php");

$db = new MySQL();
$sede = new Sede();
$user = new Usuario();
$simuladorAlmacen = new SimuladorAlmacen();
$albaranSimulador = new AlbaranSimulador();
$log = new Log_Almacen();

$json = array();
$error = false;
if(isset($_GET["func"])){
	switch($_GET["func"]){
		// Comprobación si existe el simulador con cierto número de serie
		case "comprobarNumSerie":
			$num_serie = $_GET["num_serie"];
			$metodo = $_GET["metodo"];
			$id_almacen = $_GET["id_almacen"];

			$num_serie_string = "'".$num_serie."'";  

			if($metodo == "RECEPCIONAR") {
				$boton_proceso = '<input type="button" class="BotonEliminar" value="RECEPCIONAR" onclick="recepcionarSimulador('.$num_serie_string.','.$id_almacen.');" />';
			}
			else{
				$boton_proceso = '<input type="button" class="BotonEliminar" value="DESRECEPCIONAR" onclick="desrecepcionarSimulador('.$num_serie_string.','.$id_almacen.');" />';	
			}

			// Comprobamos si existe el simulador en el almacen correspondiente
			$resultados = $simuladorAlmacen->existeSimulador($num_serie,$id_almacen);
			if($resultados != NULL){
				// Cargamos los datos del simulador
				$id_simulador = $resultados["id_simulador"];
				$error_estado = false;

				if($metodo == "DESRECEPCIONAR"){
					// Tenemos que comprobar que el simulador se encuentre en estado OPERATIVO
					$resultado_estado = $simuladorAlmacen->dameEstadoActualSimulador($id_simulador);		
					$estado = $resultado_estado["estado"];

					$error_estado = $estado != "OPERATIVO";
				}

				// Si no se da el caso de que se quiera DESRECEPCIONAR un simulador en estado diferente a OPERATIVO
				if(!$error_estado){
					// Obtenemos el ID del estado del simulador
					$datos_id_estado_simulador = $simuladorAlmacen->dameIDSimuladoresEstados($id_simulador);
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
					$codigo = $simuladorAlmacen->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
			else {
				// El simulador no existe.
				if($metodo == "RECEPCIONAR") {
					// Creamos el simulador
					$simuladorAlmacen->datosNuevoSimulador(NULL,$num_serie,"OPERATIVO",$id_almacen,"",$fecha_creado,$activo);
					$resultado = $simuladorAlmacen->guardarSimulador();
					// Obtenemos el ultimo id_simulador creado perteneciente al almacen del usuario
					$ultimo_simulador = $simuladorAlmacen->dameUltimoSimulador($id_almacen);
					$id_simulador = $ultimo_simulador["id_simulador"];

					if($resultado == 1){
						// Guardamos el estado en el historial de estados de los simuladores
						$resultado = $simuladorAlmacen->guardarLogEstadoSimulador($id_simulador,"OPERATIVO");
						if($resultado == 1){
							// Obtenemos el ultimo id del estado de simuladores
							$ultimo_id_estado = $simuladorAlmacen->dameUltimoIdEstado($id_simulador);
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
								$mensaje_error = $simuladorAlmacen->getErrorMessage($resultado);
								$codigo = $simuladorAlmacen->dameHTMLconMensajeError($mensaje_error);
								echo $codigo;		
							}
						}
				}
				else {
					// No existe el simulador para DESRECEPCIONAR. ERROR
					$mensaje_error = 'NO EXISTE EL SIMULADOR EN LA BBDD';
					$codigo = $simuladorAlmacen->dameHTMLconMensajeError($mensaje_error);
					echo $codigo;
				}
			}
		break;	
		// Recepción de un simulador
		case "recepcionar":
			$num_serie = $_GET["num_serie"];
			$id_simulador = $_GET["id_simulador"];
			$esta_averiado = $_GET["esta_averiado"];
			$id_almacen = $_GET["id_almacen"];

			if($esta_averiado == 'SI'){
				$estado = "AVERIADO";
			}
			else {
				$estado = "OPERATIVO";
			}

    		// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranSimulador->dameUltimoAlbaran($id_almacen);
			$id_albaran = $id_albaran["id_albaran"];

		    // Obtenemos el id de estado del simulador que esta activo 
			$datos_simulador_estado = $simuladorAlmacen->dameIDSimuladoresEstados($id_simulador);
			$id_simulador_estado = $datos_simulador_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del simulador
			$resultado = $simuladorAlmacen->desactivarLogEstadoSimulador($id_simulador);
			if($resultado == 1){
				// Guardamos el movimiento de ese simulador asociado al albaran
			    $resultado = $albaranSimulador->guardarMovimientoSimulador($id_albaran,$id_simulador,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranSimulador->guardarLogSimulador($id_albaran,$num_serie,$esta_averiado,$id_simulador);
			    	if($resultado == 1){
			    		// Actualizamos el estado del simulador
			    		$resultado = $simuladorAlmacen->actualizaEstadoSimulador($id_simulador,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de simuladores_estados		
			    			$resultado = $simuladorAlmacen->guardarLogEstadoSimulador($id_simulador,$estado);
			    			if($resultado == 1){
			    				$error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $simuladorAlmacen->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$$error_des = $simuladorAlmacen->getErrorMessage($resultado);
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
				$error_des = $simuladorAlmacen->getErrorMessage($resultado);
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
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		    		$datos_ultimo_estado = $simuladorAlmacen->dameIDSimuladoresEstados($id_simulador);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del simulador correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $simuladorAlmacen->eliminarEstadoSimulador($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del simulador 
		    			$resultado = $simuladorAlmacen->reactivarEstadoSimulador($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del simulador
		    				$datos_estado = $simuladorAlmacen->dameEstadoSimuladorLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del simulador 
		  					if($estado != "ENVIADO"){
			    				$resultado = $simuladorAlmacen->actualizaEstadoSimulador($id_simulador,$estado);
			    				if($resultado == 1){
			    					$error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL SIMULADOR
                                    $error = true;
			    					$error_des = $simuladorAlmacen->getErrorMessage($resultado);
			    				}
			    			}	
			    			else {
			    				$resultado = $simuladorAlmacen->actualizaEstadoSimuladorDesactivandolo($id_simulador,$estado);
			    				if($resultado == 1){
                                    $error_des = "OK!";
			    				}
			    				else {
			    					// ERROR AL ACTUALIZAR EL ESTADO DEL SIMULADOR
                                    $error = true;
			    					$error_des = $simuladorAlmacen->getErrorMessage($resultado);
			    				}
			    			}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL SIMULADOR
                            $error = true;
		    				$error_des = $simuladorAlmacen->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL SIMULADOR
                        $error = true;
		    			$error_des = $simuladorAlmacen->getErrorMessage($resultado);
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
            $id_almacen = $albaranSimulador->id_almacen;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER ENTRADA SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $simuladorAlmacen->cargaDatosSimuladorId($id_simulador);
            $num_serie = $simuladorAlmacen->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
			$id_almacen = $_GET["id_almacen"];

			// Obtenemos el albaran al que pertenece la operacion
    		$id_albaran = $albaranSimulador->dameUltimoAlbaran($id_almacen);
			$id_albaran = $id_albaran["id_albaran"];

			// Obtenemos el id de estado del simulador que esta activo 
			$datos_simulador_estado = $simuladorAlmacen->dameIDSimuladoresEstados($id_simulador);
			$id_simulador_estado = $datos_simulador_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del simulador
			$resultado = $simuladorAlmacen->desactivarLogEstadoSimulador($id_simulador);
			if($resultado == 1){
				// Guardamos el movimiento de ese simulador asociado al albaran
			    $resultado = $albaranSimulador->guardarMovimientoSimulador($id_albaran,$id_simulador,$estado);	
			    if($resultado == 1){
			    	// Guardamos un log con la operacion que realizo el usuario
			    	$resultado = $albaranSimulador->guardarLogSimulador($id_albaran,$num_serie,$esta_averiado,$id_simulador);
			    	if($resultado == 1){
			    		// Actualizamos el estado del simulador
			    		$resultado = $simuladorAlmacen->actualizaEstadoSimuladorDesactivandolo($id_simulador,$estado);
			    		if($resultado == 1){
			    			// Guardamos el log de simuladores_estados		
			    			$resultado = $simuladorAlmacen->guardarLogEstadoSimulador($id_simulador,$estado);
			    			if($resultado == 1){
                                $error_des = "OK!";
			    			}
			    			else{
                                $error = true;
			    				$error_des = $simuladorAlmacen->getErrorMessage($resultado);
			    			}
			    		}
			    		else {
                            $error = true;
			    			$error_des = $simuladorAlmacen->getErrorMessage($resultado);
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
				$error_des = $simuladorAlmacen->getErrorMessage($resultado);
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
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
		    		$datos_ultimo_estado = $simuladorAlmacen->dameIDSimuladoresEstados($id_simulador);
		    		$id_estado = $datos_ultimo_estado["id"];

		    		// Eliminamos el estado del simulador correspondiente a la operacion que se ha deshecho
		    		$resultado_estado = $simuladorAlmacen->eliminarEstadoSimulador($id_estado);
		    		if($resultado_estado == 1){
		    			// Reactivamos el estado del simulador
		    			$resultado = $simuladorAlmacen->reactivarEstadoSimulador($id_estado_restaurar);
		    			if($resultado == 1){
		    				// Obtenemos el estado anterior del simulador
		    				$datos_estado = $simuladorAlmacen->dameEstadoSimuladorLog($id_estado_restaurar);
		    				$estado = $datos_estado["estado"];

		    				// Por ultimo cambiamos el estado del simulador 
		    				$resultado = $simuladorAlmacen->actualizaEstadoSimulador($id_simulador,$estado);
		    				if($resultado == 1){
		    					$error_des = "OK!";
		    				}
		    				else {
		    					// ERROR AL ACTUALIZAR EL ESTADO DEL SIMULADOR
                                $error = true;
                                $error_des = $simuladorAlmacen->getErrorMessage($resultado);
		    				}
		    			}
		    			else {
		    				// ERROR AL REACTIVAR EL ESTADO DEL SIMULADOR
                            $error = true;
		    				$error_des = $simuladorAlmacen->getErrorMessage($resultado);
		    			}
		    		}
		    		else{
		    			// ERROR AL ELIMINAR EL ESTADO DEL SIMULADOR
                        $error = true;
		    			$error_des = $simuladorAlmacen->getErrorMessage($resultado_estado);
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
            $id_almacen = $albaranSimulador->id_almacen;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "DESHACER SALIDA SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";
            $simuladorAlmacen->cargaDatosSimuladorId($id_simulador);
            $num_serie = $simuladorAlmacen->numero_serie;

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,$id_albaran,0,"-","SIMULADOR",$num_serie,$estado,$hubo_error,$error_des,NULL);
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
			$datos_simulador_estado = $simuladorAlmacen->dameIDSimuladoresEstados($id_simulador);
			$id_simulador_estado = $datos_simulador_estado["id"];

			// Al cambiar de estado, desactivamos el estado anterior del simulador
			$resultado = $simuladorAlmacen->desactivarLogEstadoSimulador($id_simulador);
			if($resultado == 1){
				// Actualizamos el estado del simulador
			    $resultado = $simuladorAlmacen->actualizaEstadoSimulador($id_simulador,$estado_siguiente);
	    		if($resultado == 1){
	    			// Guardamos el log de simuladores_estados		
	    			$resultado = $simuladorAlmacen->guardarLogEstadoSimulador($id_simulador,$estado_siguiente);
	    			if($resultado == 1){
	    				$error_des = "OK!";
	    			}
	    			else{
                        $error = true;
                        $error_des = $simuladorAlmacen->getErrorMessage($resultado);
	    			}
	    		}
	    		else {
                    $error = true;
	    			$error_des = $simuladorAlmacen->getErrorMessage($resultado);
		   		}				
			}
			else {
                $error = true;
				$error_des = $simuladorAlmacen->getErrorMessage($resultado);
			}

            // Preparamos los datos del log
            $simuladorAlmacen->cargaDatosSimuladorId($id_simulador);
            $num_serie = $simuladorAlmacen->numero_serie;
            $id_almacen = $simuladorAlmacen->id_almacen;
            $user->cargaDatosUsuarioId($id_usuario);
            $usuario = $user->usuario;
            $id_tipo_usuario = $user->id_tipo;
            $res_tipo_usuario = $user->dameNombreTipoUsuario($id_tipo_usuario);
            $tipo_usuario = $res_tipo_usuario["tipo"];
            $proceso = "CAMBIO ESTADO SIMULADOR";
            if($error) $hubo_error = "SI";
            else $hubo_error = "NO";

            // Guardamos log de la operación
            $log->cargarDatos($id = NULL,$id_usuario,$usuario,$tipo_usuario,$proceso,$id_almacen,0,0,"-","SIMULADOR",$num_serie,$estado_siguiente,$hubo_error,$error_des,NULL);
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

			if($tipo_albaran == "ENTRADA") $res_motivos = $albaranSimulador->dameMotivosAlbaranEntradaSimuladores($id_almacen);
			else if($tipo_albaran == "SALIDA") $res_motivos = $albaranSimulador->dameMotivosAlbaranSalidaSimuladores($id_almacen);
			else $res_motivos = $albaranSimulador->dameMotivosAlbaranSimuladores($id_almacen);

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
			$res_motivos = $sede->dameMotivosAlbaranSimuladoresSede($id_sede);
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