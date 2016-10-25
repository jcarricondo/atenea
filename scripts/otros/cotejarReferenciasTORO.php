<?php 
// Este fichero comprueba las referencias del SIMESTRUCK de SIMUMAK 
// Comprobamos que las referencias de SIMUMAK y TORO son las mismas
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");
include("../classes/kint/Kint.class.php");

set_time_limit(10000);
if(isset($_POST["cotejarReferencias"]) and $_POST["cotejarReferencias"] == 1) {
    $num_refs = 0;
    $num_refs_ok = 0;
    $num_refs_pack = 0;
    $num_refs_dif_id = 0;
    $num_refs_no_exist = 0;
    $num_refs_no_exist_no_id = 0;
    $num_refs_no_act_pack = 0;
    $num_refs_no_act_uds = 0;
    $num_total_simescar = 0;

    $ref = new Referencia();
 
    // OBTENER REFERENCIAS DEL SIMESCAR DE TORO

    // Las referencias de TORO que se esten utilizando en el SIMESCAR 
    // y que tengan que utilizarse en el SIMESTRUCK NO SE ACTUALIZAN
    // Obtenemos las referencias utilizadas en el SIMESCAR
    $id_produccion_simescar = 129;

    $consulta = sprintf("select id_referencia from orden_produccion_referencias where activo=1 and id_produccion=%s group by id_referencia order by id_referencia",
        $db->makeValue($id_produccion_simescar, "int"));
    $db->setConsulta($consulta);
    $db->ejecutarConsulta();
    $referencias_simescar = $db->getResultados();
    d($referencias_simescar);

    // Preparamos el array con las referencias del SIMESCAR 
    for($i=0;$i<count($referencias_simescar);$i++){
        $id_refs_simestruck[] = $referencias_simescar[$i]["id_referencia"];
    }

    // ----------------------------------------

    // ACTUALIZAR REFERENCIAS 

    // Cargamos las referencias del Simestruck de la BBDD de Simumak
    $consulta = "select * from zzz_referencias where activo=1";
    $db->setConsulta($consulta);
    $db->ejecutarConsulta();
    $referencias_simumak = $db->getResultados();

    d($referencias_simumak);  

    // COMPROBAMOS SI CONCUERDAN LAS REFERENCIAS DE SIMUMAK CON LAS DE TORO
    // ACTUALIZAMOS AQUELLAS QUE NO PERTENEZCAN AL SIMESCAR
    for($i=0;$i<count($referencias_simumak);$i++){
        // Obtenemos las referencias de simumak utilizadas en el Simestruck
        $id_referencia_simumak = $referencias_simumak[$i]["id_referencia"];
        $nombre_referencia_simumak = $referencias_simumak[$i]["referencia"];
        $id_proveedor_simumak = $referencias_simumak[$i]["id_proveedor"];
        $part_proveedor_referencia_simumak = $referencias_simumak[$i]["part_proveedor_referencia"];
        $pack_precio_simumak = $referencias_simumak[$i]["pack_precio"];
        $unidades_simumak = $referencias_simumak[$i]["unidades"];

        // Referencias utilizadas en el simescar 
        if(in_array($id_referencia_simumak, $id_refs_simestruck)){
            $num_total_simescar++;
        }    

        // Comprobamos que no hay ningun pack_precio, ni unidades paquete a NULL
        if($pack_precio_simumak == NULL) $pack_precio_simumak = 0;
        if($unidades_simumak == NULL) $unidades_simumak = 1;

        // Cargamos las referencias de TORO con los id_ref de SIMUMAK
        // para comprobar si coinciden en ambas instancias
        $ref->cargaDatosReferenciaId($id_referencia_simumak);
        $id_referencia_toro = $ref->id_referencia;
        $nombre_referencia_toro = $ref->referencia;
        $part_proveedor_referencia_toro = $ref->part_proveedor_referencia;
        $pack_precio_toro = $ref->pack_precio;
        $unidades_toro = $ref->unidades;

        $impedir_act = false;
        // Comprobamos por id_referencia y referencia del proveedor
        $ref->datosReferencia($id_referencia_simumak,$nombre_referencia_simumak,"",$id_proveedor_simumak,"","",$part_proveedor_referencia_simumak,"","","",$pack_precio_simumak,$unidades_simumak,"");
        if($ref->comprobarReferenciaTORO()) {
            // La referencia existe con el mismo id_ref, nombre y referencia de proveedor
            $mensaje_error .= "<br/>";
            $mensaje_error .= "<br/><span style='color: green;'>LA REF [".$id_referencia_simumak."] coincide con TORO</span>";
            
            // Comprobamos que el pack_precio es el mismo en SIMUMAK y TORO
            if($pack_precio_toro != $pack_precio_simumak){
                $num_refs_pack++;
                // Si la referencia se utiliza en el SIMESCAR entonces NO ACTUALIZAMOS el pack_precio
                if(in_array($id_referencia_simumak, $id_refs_simestruck)){
                    $mensaje_error .= "<br/><span style='color: red;'>La referencia [".$id_referencia_simumak."] existe en el SIMESCAR. NO ACTUALIZADO el pack_precio</span>";     
                    $mensaje_error .= "<br/><span style='color: red;'>PACK_PRECIO_TORO: ".$pack_precio_toro."</span>";
                    $mensaje_error .= "<br/><span style='color: red;'>PACK_PRECIO_SMK: ".$pack_precio_simumak."</span>"; 
                    $num_refs_no_act_pack++; 
                    $impedir_act = true;
                }
                else {
                    $mensaje_error .= "<br/><span style='color: black;'>El pack_precio de TORO no coincide con el pack_precio de SIMUMAK</span>"; 
                    $mensaje_error .= "<br/><span style='color: black;'>PACK_PRECIO_TORO: ".$pack_precio_toro."</span>";
                    $mensaje_error .= "<br/><span style='color: black;'>PACK_PRECIO_SMK: ".$pack_precio_simumak."</span>";  
                    $mensaje_error .= "<br/><span style='color: black;'>Actualizando pack_precio...</span>"; 

                    // ACTUALIZAR PACK_PRECIO
                    $updateSql = sprintf("update referencias set pack_precio=%s where activo=1 and id_referencia=%s",
                        $db->makeValue($pack_precio_simumak, "float"),
                        $db->makeValue($id_referencia_simumak, "int"));
                    $db->setConsulta($updateSql);
                    if($db->ejecutarSoloConsulta()){
                       $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado el pack_precio de la referencia [".$id_referencia_simumak."] correctanente <br/>";
                    }
                    else{
                       $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar el pack_precio de la referencia [".$id_referencia_simumak."] <br/>";
                    }
                }
            }
            // Comprobamos que las unidades_paquete son las mismas en SIMUMAK y TORO
            if($unidades_toro != $unidades_simumak){
                if(in_array($id_referencia_simumak, $id_refs_simestruck)){
                    $mensaje_error .= "<br/><span style='color: red;'>La referencia [".$id_referencia_simumak."] existe en el SIMESCAR. NO ACTUALIZADAS las unidades</span>";   
                    $mensaje_error .= "<br/><span style='color: red;'>UNIDADES TORO: ".$unidades_toro."</span>";
                    $mensaje_error .= "<br/><span style='color: red;'>UNIDADES SMK: ".$unidades_simumak."</span>";   
                    $num_refs_no_act_uds++;
                }
                else {
                    $mensaje_error .= "<br/><span style='color: black;'>Las unidades de TORO no coinciden con las unidades de SIMUMAK</span>"; 
                    $mensaje_error .= "<br/><span style='color: black;'>UNIDADES_TORO: ".$unidades_toro."</span>";
                    $mensaje_error .= "<br/><span style='color: black;'>UNIDADES_SMK: ".$unidades_simumak."</span>";  
                    $mensaje_error .= "<br/><span style='color: black;'>Actualizando unidades...</span>"; 
                
                    // ACTUALIZAR UNIDADES PAQUETE
                    $updateSql = sprintf("update referencias set unidades=%s where activo=1 and id_referencia=%s",
                        $db->makeValue($unidades_simumak, "float"),
                        $db->makeValue($id_referencia_simumak, "int"));
                    $db->setConsulta($updateSql);
                    if($db->ejecutarSoloConsulta()){
                        $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado las unidades_paquete de la referencia [".$id_referencia_simumak."] correctanente <br/>";   
                    }
                    else {
                        $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar las unidades_paquete de la referencia [".$id_referencia_simumak."] <br/>"; 
                    }
                }  
            }
            if(in_array($id_referencia_simumak, $id_refs_simestruck) && !$impedir_act){
                $mensaje_error .= "<br/><span style='color: blue;'>La referencia [".$id_referencia_simumak."] existe en el SIMESCAR pero los pack_precio y unidades coinciden con los de SIMUMAK.</span>";   
            }
            $num_refs_ok++;
        }  
        else {
            // Comprobamos si existe con otro id_ref
            $ref->datosReferencia(NULL,$nombre_referencia_simumak,"",$id_proveedor_simumak,"","",$part_proveedor_referencia_simumak,"","","",$pack_precio_simumak,$unidades_simumak,"");      
            if($ref->comprobarReferenciaTORO()) {
                // Existe con otro id_referencia
                $mensaje_error .= "<br/>";
                $mensaje_error .= "<br/><span style='color: orange;'>LA REF [".$id_referencia_simumak."] existe en TORO con otro id_ref distinto</span>";
                $num_refs_dif_id++;

                // Crear la referencia en TORO con el mismo ID_REF y datos que en SIMUMAK
                // Obtenemos los datos de la referencia de simumak zzz_referencias
                $consulta = sprintf("select * from zzz_referencias where activo=1 and id_referencia=%s",
                    $db->makeValue($id_referencia_simumak, "int"));
                $db->setConsulta($consulta);
                $db->ejecutarConsulta();
                $res_referencias = $db->getPrimerResultado();

                $referencia = $res_referencias["referencia"];
                $fabricante = $res_referencias["id_fabricante"];
                $proveedor = $res_referencias["id_proveedor"];
                $part_nombre = $res_referencias["part_nombre"];
                $part_tipo = $res_referencias["part_tipo"];
                $part_proveedor_referencia = $res_referencias["part_proveedor_referencia"];
                $part_fabricante_referencia = $res_referencias["part_fabricante_referencia"];
                $part_valor_nombre = $res_referencias["part_valor_nombre"];
                $part_valor_cantidad = $res_referencias["part_valor_cantidad"];
                $part_valor_nombre_2 = $res_referencias["part_valor_nombre_2"];
                $part_valor_cantidad_2 = $res_referencias["part_valor_cantidad_2"];
                $part_valor_nombre_3 = $res_referencias["part_valor_nombre_3"];
                $part_valor_cantidad_3 = $res_referencias["part_valor_cantidad_3"];
                $part_valor_nombre_4 = $res_referencias["part_valor_nombre_4"];
                $part_valor_cantidad_4 = $res_referencias["part_valor_cantidad_4"];
                $part_valor_nombre_5 = $res_referencias["part_valor_nombre_5"];
                $part_valor_cantidad_5 = $res_referencias["part_valor_cantidad_5"];
                $pack_precio = $res_referencias["pack_precio"];
                $unidades = $res_referencias["unidades"];
                $nombre_archivo = $res_referencias["nombre_archivo"];
                $comentarios = $res_referencias["comentarios"];

                // Copiamos la referencia ya existente con el id_ref de simumak 
                $consulta = sprintf("insert into referencias (id_referencia,referencia,id_proveedor,id_fabricante,part_tipo,part_nombre,part_fabricante_referencia,part_proveedor_referencia,
                                part_valor_nombre,part_valor_cantidad,part_valor_nombre_2,part_valor_cantidad_2,part_valor_nombre_3,part_valor_cantidad_3,part_valor_nombre_4,
                                part_valor_cantidad_4,part_valor_nombre_5,part_valor_cantidad_5,pack_precio,unidades,comentarios,fecha_creado,activo) 
                                value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
                    $db->makeValue($id_referencia_simumak, "int"),
                    $db->makeValue($referencia, "text"),
                    $db->makeValue($proveedor, "int"),
                    $db->makeValue($fabricante, "int"),
                    $db->makeValue($part_tipo, "text"),
                    $db->makeValue($part_nombre, "text"),
                    $db->makeValue($part_fabricante_referencia, "text"),
                    $db->makeValue($part_proveedor_referencia, "text"),
                    $db->makeValue($part_valor_nombre, "text"),
                    $db->makeValue($part_valor_cantidad, "text"),
                    $db->makeValue($part_valor_nombre_2, "text"),
                    $db->makeValue($part_valor_cantidad_2, "text"),
                    $db->makeValue($part_valor_nombre_3, "text"),
                    $db->makeValue($part_valor_cantidad_3, "text"),
                    $db->makeValue($part_valor_nombre_4, "text"),
                    $db->makeValue($part_valor_cantidad_4, "text"),
                    $db->makeValue($part_valor_nombre_5, "text"),
                    $db->makeValue($part_valor_cantidad_5, "text"),
                    $db->makeValue($pack_precio, "float"),
                    $db->makeValue($unidades, "int"),
                    $db->makeValue($comentarios, "text"));
                $db->setConsulta($consulta);
                if($db->ejecutarSoloConsulta()) {
                    $mensaje_error .= "<br/><span style='color: green;'>LA REF [".$id_referencia_simumak."] se ha copiado correctamente en TORO</span>";
                }
                else {
                    $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al copiar la REF [".$id_referencia_simumak."] en TORO</span>";
                } 
            }   
            else{ 
                // No existe esa referencia en la BBDD de TORO 
                $mensaje_error .= "<br/>";
                // Comprobamos si el id_ref que queremos insertar ya esta ocupado en ATENEA TORO
                $ref->datosReferencia($id_referencia_simumak,$nombre_referencia_simumak,"",$id_proveedor_simumak,"","",$part_proveedor_referencia_simumak,"","","",$pack_precio_simumak,$unidades_simumak,"");
                if($ref->comprobarId_RefTORO()){
                    $mensaje_error .= "<br/><span style='color: red;'>LA REF [".$id_referencia_simumak."] NO existe en TORO y ya esta asociado el ID_REF a otra referencia</span>";                                   
                    $num_refs_no_exist_no_id++;

                    // Obtener datos de la referencia que ya existe con la id_ref de SIMUMAK
                    $ref_TORO = $ref->dameDatosId_RefTORO();
                    $nombre_ref_TORO = $ref_TORO["referencia"];
                    $ref_prov_ref_TORO = $ref_TORO["part_proveedor_referencia"];
                    $pack_precio_ref_TORO = $ref_TORO["pack_precio"];
                    $unidades_ref_TORO = $ref_TORO["unidades"];

                    $mensaje_error .= "<br/><span style='color: black;'>NOMBRE REF TORO: ".$nombre_ref_TORO."</span>";
                    $mensaje_error .= "<br/><span style='color: black;'>NOMBRE REF SMK: ".$nombre_referencia_simumak."</span>";
                    $mensaje_error .= "<br/><span style='color: black;'>REF PROV. TORO: ".$ref_prov_ref_TORO."</span>";
                    $mensaje_error .= "<br/><span style='color: black;'>REF PROV. SMK: ".$part_proveedor_referencia_simumak."</span>";

                    if($pack_precio_ref_TORO != $pack_precio_simumak){
                        // Si existe la referencia en el SIMESTRUCK no se actualiza el pack_precio
                        if(in_array($id_referencia_simumak, $id_refs_simestruck)){
                            $mensaje_error .= "<br/><span style='color: red;'>La referencia [".$id_referencia_simumak."] existe en el SIMESTRUCK. NO ACTUALIZADO pack_precio</span>";     
                            $mensaje_error .= "<br/><span style='color: red;'>PACK_PRECIO_TORO: ".$pack_precio_ref_TORO."</span>";
                            $mensaje_error .= "<br/><span style='color: red;'>PACK_PRECIO_SMK: ".$pack_precio_simumak."</span>"; 
                            $num_refs_no_act_pack++; 
                        }
                        else {
                            $mensaje_error .= "<br/><span style='color: black;'>El pack_precio de TORO no coincide con el pack_precio de SIMUMAK</span>"; 
                            $mensaje_error .= "<br/><span style='color: black;'>PACK_PRECIO_TORO: ".$pack_precio_ref_TORO."</span>";
                            $mensaje_error .= "<br/><span style='color: black;'>PACK_PRECIO_SMK: ".$pack_precio_simumak."</span>";  
                            $mensaje_error .= "<br/><span style='color: black;'>Actualizando pack_precio...</span>";   

                            // ACTUALIZAR PACK_PRECIO ??
                            /*
                            $updateSql = sprintf("update referencias set pack_precio=%s where activo=1 and id_referencia=%s",
                                $db->makeValue($pack_precio_simumak, "float"),
                                $db->makeValue($id_referencia_simumak, "int"));
                            $db->setConsulta($updateSql);
                            if($db->ejecutarSoloConsulta()){
                               $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado el pack_precio de la referencia [".$id_referencia_simumak."] correctanente <br/>";
                            }
                            else{
                               $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar el pack_precio de la referencia [".$id_referencia_simumak."] <br/>";
                            }
                            */
                        }
                    }    
                    if($unidades_ref_TORO != $unidades_simumak){
                        // Si existe la referencia en el SIMESTRUCK no se actualiza las unidades_paquete
                        if(in_array($id_referencia_simumak, $id_refs_simestruck)){
                            $mensaje_error .= "<br/><span style='color: red;'>La referencia [".$id_referencia_simumak."] existe en el SIMESTRUCK. NO ACTUALIZADAS unidades_paquete</span>";     
                            $mensaje_error .= "<br/><span style='color: red;'>UDS_PAQUETE REF. EXISTE TORO: ".$unidades_ref_TORO."</span>";
                            $mensaje_error .= "<br/><span style='color: red;'>UDS_PAQUETE SMK: ".$unidades_simumak."</span>"; 
                            $num_refs_no_act_pack++; 
                        }
                        else {
                            $mensaje_error .= "<br/><span style='color: black;'>Las unidades paquete de TORO no coincide con las unidades paquete de SIMUMAK</span>"; 
                            $mensaje_error .= "<br/><span style='color: black;'>UDS_PAQUETE REF. EXISTE TORO: ".$unidades_ref_TORO."</span>";
                            $mensaje_error .= "<br/><span style='color: black;'>UDS_PAQUETE SMK: ".$unidades_simumak."</span>"; 
                            $mensaje_error .= "<br/><span style='color: black;'>Actualizando unidades_paquete...</span>";  

                            // ACTUALIZAR UNIDADES PAQUETE
                            /*
                            $updateSql = sprintf("update referencias set unidades=%s where activo=1 and id_referencia=%s",
                                $db->makeValue($unidades_simumak, "float"),
                                $db->makeValue($id_referencia_simumak, "int"));
                            $db->setConsulta($updateSql);
                            if($db->ejecutarSoloConsulta()){
                                $mensaje_error .= "<br/><span style='color: green;'>Se ha actualizado las unidades_paquete de la referencia [".$id_referencia_simumak."] correctanente <br/>";   
                            }
                            else {
                                $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al actualizar las unidades_paquete de la referencia [".$id_referencia_simumak."] <br/>"; 
                            } 
                            */   
                        }
                    }   
                    $array_refs_error[] = $id_referencia_simumak;
                }
                else {
                    // No existe la referencia en TORO y no se ocupó el id_ref
                    $mensaje_error .= "<br/><span style='color: red;'>LA REF [".$id_referencia_simumak."] NO existe en TORO</span>";   

                    // Crear la referencia en TORO con el mismo ID_REF y datos que en SIMUMAK
                    // Obtenemos los datos de la referencia de simumak zzz_referencias
                    $consulta = sprintf("select * from zzz_referencias where activo=1 and id_referencia=%s",
                      $db->makeValue($id_referencia_simumak, "int"));
                    $db->setConsulta($consulta);
                    $db->ejecutarConsulta();
                    $res_referencias = $db->getPrimerResultado();

                    $referencia = $res_referencias["referencia"];
                    $fabricante = $res_referencias["id_fabricante"];
                    $proveedor = $res_referencias["id_proveedor"];
                    $part_nombre = $res_referencias["part_nombre"];
                    $part_tipo = $res_referencias["part_tipo"];
                    $part_proveedor_referencia = $res_referencias["part_proveedor_referencia"];
                    $part_fabricante_referencia = $res_referencias["part_fabricante_referencia"];
                    $part_valor_nombre = $res_referencias["part_valor_nombre"];
                    $part_valor_cantidad = $res_referencias["part_valor_cantidad"];
                    $part_valor_nombre_2 = $res_referencias["part_valor_nombre_2"];
                    $part_valor_cantidad_2 = $res_referencias["part_valor_cantidad_2"];
                    $part_valor_nombre_3 = $res_referencias["part_valor_nombre_3"];
                    $part_valor_cantidad_3 = $res_referencias["part_valor_cantidad_3"];
                    $part_valor_nombre_4 = $res_referencias["part_valor_nombre_4"];
                    $part_valor_cantidad_4 = $res_referencias["part_valor_cantidad_4"];
                    $part_valor_nombre_5 = $res_referencias["part_valor_nombre_5"];
                    $part_valor_cantidad_5 = $res_referencias["part_valor_cantidad_5"];
                    $pack_precio = $res_referencias["pack_precio"];
                    $unidades = $res_referencias["unidades"];
                    $nombre_archivo = $res_referencias["nombre_archivo"];
                    $comentarios = $res_referencias["comentarios"];

                    // Insertamos las referencias utilizadas en la tabla auxiliar
                    $consulta = sprintf("insert into referencias (id_referencia,referencia,id_proveedor,id_fabricante,part_tipo,part_nombre,part_fabricante_referencia,part_proveedor_referencia,
                                part_valor_nombre,part_valor_cantidad,part_valor_nombre_2,part_valor_cantidad_2,part_valor_nombre_3,part_valor_cantidad_3,part_valor_nombre_4,
                                part_valor_cantidad_4,part_valor_nombre_5,part_valor_cantidad_5,pack_precio,unidades,comentarios,fecha_creado,activo) 
                                value (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,current_timestamp,1)",
                      $db->makeValue($id_referencia_simumak, "int"),
                      $db->makeValue($referencia, "text"),
                      $db->makeValue($proveedor, "int"),
                      $db->makeValue($fabricante, "int"),
                      $db->makeValue($part_tipo, "text"),
                      $db->makeValue($part_nombre, "text"),
                      $db->makeValue($part_fabricante_referencia, "text"),
                      $db->makeValue($part_proveedor_referencia, "text"),
                      $db->makeValue($part_valor_nombre, "text"),
                      $db->makeValue($part_valor_cantidad, "text"),
                      $db->makeValue($part_valor_nombre_2, "text"),
                      $db->makeValue($part_valor_cantidad_2, "text"),
                      $db->makeValue($part_valor_nombre_3, "text"),
                      $db->makeValue($part_valor_cantidad_3, "text"),
                      $db->makeValue($part_valor_nombre_4, "text"),
                      $db->makeValue($part_valor_cantidad_4, "text"),
                      $db->makeValue($part_valor_nombre_5, "text"),
                      $db->makeValue($part_valor_cantidad_5, "text"),
                      $db->makeValue($pack_precio, "float"),
                      $db->makeValue($unidades, "int"),
                      $db->makeValue($comentarios, "text"));
                    $db->setConsulta($consulta);
                    if($db->ejecutarSoloConsulta()) {
                        $mensaje_error .= "<br/><span style='color: green;'>LA REF [".$id_referencia_simumak."] se ha creado correctamente en TORO</span>";
                    }
                    else {
                        $mensaje_error .= "<br/><span style='color: red;'>Se ha producido un error al crear la REF [".$id_referencia_simumak."] en TORO</span>";
                    } 
                }
                $num_refs_no_exist++;
            }
        } 
        $num_refs++;  
    }

    $mensaje_error .= "<br/>";
    $mensaje_error .= "<br/><span style='color: black;'>Numero total de referencias: ".$num_refs."</span><br/>";
    $mensaje_error .= "<br/><span style='color: black;'>Numero referencias coinciden en SMK y TORO: ".$num_refs_ok."</span><br/>";
    $mensaje_error .= "<br/><span style='color: black;'>Numero referencias coinciden en SMK y TORO (pack_precio distintos): ".$num_refs_pack."</span><br/>";
    $mensaje_error .= "<br/><span style='color: black;'>Numero referencias existen con diferentes ID_REF: ".$num_refs_dif_id."</span><br/>";
    $mensaje_error .= "<br/><span style='color: black;'>Numero referencias no existen: ".$num_refs_no_exist."</span><br/>";
    $mensaje_error .= "<br/><span style='color: black;'>Numero referencias no existen y ya existe el id_ref: ".$num_refs_no_exist_no_id."</span><br/>";
    $mensaje_error .= "<br/><span style='color: red;'>NUMERO TOTAL DE REFERENCIAS UTILIZADAS EN EL SIMESCAR: ".$num_total_simescar."</span><br/>";
    $mensaje_error .= "<br/><span style='color: red;'>Numero referencias coinciden en SMK y TORO (pack_precio distintos) pertenecen SIMESCAR: ".$num_refs_no_act_pack."</span><br/>";
    $mensaje_error .= "<br/><span style='color: red;'>Numero referencias coinciden en SMK y TORO (uds_pqt distintos) pertenecen SIMESCAR: ".$num_refs_no_act_uds."</span><br/>";

    d($array_refs_error);
} 
$titulo_pagina = "B&aacutesico > Cotejar referencias";
include ('../includes/header.php');
?>
<div class="separador"></div> 
<div id="CapaBotones">
    <div class="CapaBotonesContenedorContenido">
       	<?php if(permisoMenu(5)) { ?>
    	<a class="BotonMenu" href="../basicos/proveedores.php">
           		Proveedores
       	</a>
        <?php 
		} if(permisoMenu(6)) { ?>
      	<a class="BotonMenu" href="../basicos/referencias.php" >
           		Referencias
       	</a>
        <?php 
		} 
		if(permisoMenu(8)) { ?>
        <a class="BotonMenu" href="../basicos/cabinas.php" >
           		Cabinas
        </a>
        <?php 
		}
		if(permisoMenu(9)) { ?>
        <a class="BotonMenu" href="../basicos/perifericos.php" >
           		Perifericos
        </a>
        <?php 
		}
		if(permisoMenu(10)) { ?>
        <a class="BotonMenu" href="../basicos/ordenadores.php" >
           		Ordenadores
        </a>
        <?php 
		}
		if(permisoMenu(11)) { ?>
        <a class="BotonMenu" href="../basicos/softwareSimulacion.php" >
         		Software
        </a>
        <?php 
		} 
		if(permisoMenu(12)) { ?>
        <a class="BotonMenu" href="../basicos/fabricantes.php">
            	Fabricantes
        </a>
        <?php 
		}
		if(permisoMenu(13)) { ?>
        <a class="BotonMenu" href="../basicos/familias.php">
            	Familias
        </a>
        <?php
		} if(permisoMenu(14)) { ?>
        <a class="BotonMenu" href="../basicos/clientes.php" >
           		Clientes
        </a>
        <?php 
		} 
		if(permisoMenu(15)) { ?>
        <a class="BotonMenu" href="../basicos/usuarios.php" >
          		Usuarios
        </a>
        <?php } ?>
        <a class="BotonMenuActual" href="../basicos/nuevareferencia.php">Nueva</a>
        <a class="BotonMenuActual" href="../basicos/referencias.php" >
           		Listado
        </a>

    </div> 
    
	<?php include ("../includes/opciones_usuario.php"); ?>
    
</div>


<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3> Comprobar referencias </h3>
    <form id="FormularioCreacionBasico" name="crearReferencia" action="cotejarReferenciasTORO.php" method="post">
    	<br />
      <div class="ContenedorBotonCreacionBasico">
          <input type="hidden" id="cotejarReferencias" name="cotejarReferencias" value="1"/>
          <input type="submit" id="continuar" name="continuar" value="Continuar" />
      </div>
		  <div class="mensajeCamposObligatorios">

      </div>       
      <?php
        	if($mensaje_error != "") {
				      echo '<div class="mensaje_error">'.$mensaje_error.'</div>'; 
			    }
		  ?>
      <br />
    </form>
</div>    

<!--<div class="separador"></div>-->
<?php include ("../includes/footer.php"); ?>
