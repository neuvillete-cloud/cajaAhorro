<?php
include_once('connection.php');


if (isset ($_POST["nom1"],$_POST["nom2"])){
    $nomina1 = $_POST["nom1"];
    $nomina2 = $_POST["nom2"];

    consultarNombreAval($nomina1, $nomina2);
}else{
    echo json_encode(array("data" => "Faltan datos en el formulario"));
}

function consultarNombreAval($Nomina1, $Nomina2){
    $con = new LocalConector();
    $conexion=$con->conectar();

    $consP="SELECT IdUser, NomUser FROM Empleados WHERE IdUser = '$Nomina1' OR IdUser = '$Nomina2' ";
    $rsconsPro=mysqli_query($conexion,$consP);

    mysqli_close($conexion);

    $resultado= mysqli_fetch_all($rsconsPro, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}
?>