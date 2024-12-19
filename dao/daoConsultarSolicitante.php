<?php
include_once('connection.php');

$nomina = $_GET["sol"];
consultarsolicitante($nomina);
function consultarsolicitante($Nomina){
    $con = new LocalConector();
    $conexion=$con->conectar();

    $consP="SELECT NomUser FROM Empleados WHERE IdUser = '$Nomina'";
    $rsconsPro=mysqli_query($conexion,$consP);

    mysqli_close($conexion);

    $resultado= mysqli_fetch_all($rsconsPro, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}
?>