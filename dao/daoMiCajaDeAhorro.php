<?php

include_once('connectionCajita.php');
session_start();
$id_solicitante = $_SESSION['nomina'];
resumenPrueba($id_solicitante);

function resumenPrueba($id_solicitante){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datosPrueba =  mysqli_query($conex,
        "SELECT
                    s.idCaja,
                    s.nomina,
                    s.fechaSolicitud,
                    s.montoAhorro
                FROM
                    CajaAhorro s
                WHERE
                    s.nomina = '$id_solicitante'
                ORDER BY
                    s.fechaSolicitud DESC;
                ");

    $resultado= mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));

}

?>