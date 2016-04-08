<?php 
set_time_limit(10000);
include("../includes/sesion.php");
include("../classes/basicos/referencia.class.php");

$ref = new Referencia();

if(isset($_POST["compararReferencias"]) && $_POST["compararReferencias"] == 1){
  // Obtenemos las referencias activas de TORO
  $referencias = $ref->dameReferenciasActivas();
  for($i=0;$i<count($referencias);$i++){
    $id_referencia = $referencias[$i]["id_referencia"];
    $nombre_referencia = $referencias[$i]["referencia"];
    $id_proveedor = $referencias[$i]["id_proveedor"];
    $part_proveedor_referencia = $referencias[$i]["part_proveedor_referencia"];
    $pack_precio = $referencias[$i]["pack_precio"];
    $unidades = $referencias[$i]["unidades"];

    // Comprobamos que no hay ningun pack_precio, ni unidades paquete a NULL
    if($pack_precio == NULL) $pack_precio = 0;
    if($unidades == NULL) $unidades = 1;

    






    // Cargamos las referencias de TORO con los id_ref de SIMUMAK
        // para comprobar si coinciden en ambas instancias
        $ref->cargaDatosReferenciaId($id_referencia_simumak);
        $id_referencia_toro = $ref->id_referencia;
        $nombre_referencia_toro = $ref->referencia;
        $part_proveedor_referencia_toro = $ref->part_proveedor_referencia;
        $pack_precio_toro = $ref->pack_precio;
        $unidades_toro = $ref->unidades;




  }
  




}






$titulo_pagina = "Scripts > Comparar referencias SMK / TORO";
include ('../includes/header.php');
?>

<div class="separador"></div> 
<?php include ("../includes/menu_basicos.php"); ?>

<div id="ContenedorCentral">
    <div id="ContenedorSidebar">
        <?php include ("../includes/sidebar.php"); ?>
    </div>
    
    <h3> Comprobar referencias </h3>
    <form id="FormularioCreacionBasico" name="compararReferenciasToro" action="comparar_referencias_TORO.php" method="post">
    	<br />
      <div class="ContenedorBotonCreacionBasico">
          <input type="hidden" id="compararReferencias" name="compararReferencias" value="1"/>
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

<?php include ("../includes/footer.php"); ?>