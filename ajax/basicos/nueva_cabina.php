<?php
// Fichero AJAX para devolver los datos del proceso de nueva_cabina
include("../../classes/mysql.class.php");
include("../../classes/basicos/componente.class.php");
include("../../classes/basicos/referencia.class.php");

$db = new MySQL();
$comp = new Componente();
$ref = new Referencia();

if(isset($_GET["func"])){
	switch($_GET["func"]){
		case "loadComp":
            $json = array();
            // Obtenemos los id_componentes
            $id_componente = $_GET["id"];
            $claves = array("id","nombre","referencias");

            // Obtenemos los nombres
            $comp->cargaDatosComponenteId($id_componente);
            $nombre_componente = $comp->nombre.'_v'.$comp->version;
            $refs_componente = $comp->dameRefsYPiezasComponente($id_componente);

            // Cargamos los datos de las referencias y las guardamos en un array para incluirlo en la variable JSON
            for($i=0;$i<count($refs_componente);$i++){
                $id_referencia = $refs_componente[$i]["id_referencia"];
                $ref->cargaDatosReferenciaId($id_referencia);
                $piezas = $refs_componente[$i]["piezas"];
                $precio_unidad = number_format(($ref->pack_precio / $ref->unidades), 2, '.', '');
                $precio_referencia = number_format(($piezas * $precio_unidad), 2, '.', '');
                $array_referencias[] = array("id_referencia" => $id_referencia,
                                                "nombre" => $ref->referencia,
                                                "nombre_proveedor" => $ref->nombre_proveedor,
                                                "ref_proveedor" => $ref->part_proveedor_referencia,
                                                "nombre_pieza" => $ref->part_nombre,
                                                "piezas" => $piezas,
                                                "pack_precio" => $ref->pack_precio,
                                                "uds_paquete" => $ref->unidades,
                                                "precio_unidad" => $precio_unidad,
                                                "precio" => $precio_referencia);
            }

            $json = array("id_componente" => $id_componente, "nombre" => $nombre_componente, "referencias" => $array_referencias);
            echo json_encode($json, JSON_FORCE_OBJECT);
		break;
		default:

        break;
	}
}
?>