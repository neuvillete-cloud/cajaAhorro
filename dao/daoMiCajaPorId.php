<?php

include_once('connectionCajita.php');
session_start();
$id_solicitud = $_GET['ret'];
resumenCajaPorId($id_solicitud);

function resumenCajaPorId($id_solicitud){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datosPrueba =  mysqli_query($conex,
        "SELECT
                    s.idCaja,
                    s.nomina,
                    s.fechaSolicitud,
                    s.montoAhorro,
                    b.nombre,
                    b.direccion,
                    b.telefono,
                    b.porcentaje
                FROM
                    CajaAhorro s, Beneficiarios b
                WHERE s.idCaja = b.idCaja
                  AND s.idCaja = '$id_solicitud'
                ");

    $resultado= mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));

}

?>