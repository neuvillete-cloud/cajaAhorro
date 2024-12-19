<?php
include_once('connectionCajita.php');

consultarFechas();
function consultarFechas(){
    $con = new LocalConectorCajita();
    $conexion=$con->conectar();

    $anioActual = intval(date('Y'));

    $consP="SELECT fechaInicio, fechaFin, horaInicio, horaFin FROM Convocatoria WHERE anio = '$anioActual'";
    $rsconsPro=mysqli_query($conexion,$consP);

    mysqli_close($conexion);

    $resultado= mysqli_fetch_all($rsconsPro, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}
?>