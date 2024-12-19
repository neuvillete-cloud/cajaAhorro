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
                    s.idRetiro,
                    s.idCaja,
                    c.nomina,
                    s.fechaSolicitud,
                    s.estatusRetiro
                FROM
                    RetiroAhorro s, CajaAhorro c 
                WHERE
                    c.nomina = '$id_solicitante'
                AND
                    s.idCaja = c.idCaja
                ORDER BY
                    s.fechaSolicitud DESC;
                ");

    $resultado= mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));

}

?>