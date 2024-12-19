<?php
require_once "connectionCajita.php";
session_start();

cargarUltimoPrestamo();

function cargarUltimoPrestamo()
{
    $con = new LocalConectorCajita();
    $conexion = $con -> conectar();

    $nomina = $_SESSION["nomina"];

    $consultaPrestamo = "SELECT telefono, montoSolicitado 
                           FROM Prestamo 
                          WHERE nominaSolicitante = '$nomina'
                       ORDER BY idSolicitud DESC 
                          LIMIT 1";

    $resultadoConsulta = mysqli_query($conexion, $consultaPrestamo);
    mysqli_close($conexion);
    $resultado = mysqli_fetch_all($resultadoConsulta, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}

?>