<?php 
// Este fichero guarda un log con la operacion de importacion de referencias en un componente

$csv_tp = $_FILES["archivo_importacion"]['type'];
if(($csv_tp == "text/csv" || $csv_tp == "application/vnd.ms-excel" || $csv_tp == "text/comma-separated-values" || $csv_tp == "application/octet-stream" || $csv_tp == "application/x-octet-stream")) {
	$csv_nm = $_FILES["archivo_importacion"]['tmp_name'];
	$file = fopen($csv_nm, "rt");
	$fila = 0;
	$cont = 0;
	while(($datos = fgetcsv($file, 1000, ";")) !== false) {
		++$fila;
		if($datos[0] != "NOMBRE") {
			$id_proveedor = "";
			$id_fabricante = "";
			$id_referencia = "";
						                
			/*
			ESTRUCTURA DE DATOS:
			0: Nombre Referencia
			1: Nombre Pieza
			2: Tipo Pieza
			3: Referencia Proveedor
			4: Referencia Fabricante
			5: Nombre_01
			6: Valor_01
			7: Nombre_02
			8: Valor_02
			9: Nombre_03
			10: Valor_03
			11: Nombre_04
			12: Valor_04
			13: Nombre_05
			14: Valor_05
			15: Pack_Precio
			16: Unidades 
			17: Comentarios
			18: Piezas utilizadas en el componente
			19: Nombre del proveedor
			20: Nombre del fabricante
			*/

			$nombre_referencia = $datos[0];
            $nombre_pieza = $datos[1];
            $tipo_pieza = $datos[2];
            $ref_proveedor = $datos[3];
            $ref_fabricante = $datos[4];
            $nombre1 = $datos[5];
            $valor1 = $datos[6];
            $nombre2 = $datos[7];
            $valor2 = $datos[8];
            $nombre3 = $datos[9];
            $valor3 = $datos[10];
            $nombre4 = $datos[11];
            $valor4 = $datos[12];
            $nombre5 = $datos[13];
            $valor5 = $datos[14];
            $pack_precio = $datos[15];
            $unidades_paquete = $datos[16];
            $comentarios = $datos[17];
            $piezas_ref = $datos[18];
            $nombre_proveedor = $datos[19];
            $nombre_fabricante = $datos[20];

            $id_referencia = $referencias_id[$cont];
            $id_proveedor = $proveedores_id[$cont];
            $id_fabricante = $fabricantes_id[$cont];
            $proveedor_creado = $proveedores_nuevos[$cont];
            $fabricante_creado = $fabricantes_nuevos[$cont];
            $referencia_creada = $referencias_nuevas[$cont];

            // Set datos 
            $log->setValores($id_usuario,$id_referencia,$id_proveedor,$id_fabricante,$nombre_referencia,$nombre_pieza,$tipo_pieza,$ref_proveedor,$ref_fabricante,$nombre1,$valor1,$nombre2,$valor2,$nombre3,
							$valor3,$nombre4,$valor4,$nombre5,$valor5,$pack_precio,$unidades_paquete,$comentarios,$piezas_ref,$nombre_proveedor,$nombre_fabricante,
    						$proveedor_creado,$fabricante_creado,$referencia_creada,$id_componente,$tipo_componente,$proceso);

            // Llamada a guardar log
            $res = $log->guardarLog();
            if($res == 0){
            	echo '<script type="text/javascript">alert("Se produjo un error al guardar el log de la importacion masiva de referencias")</script>';
            }
            $cont++;
		}       
	}
}
?>
