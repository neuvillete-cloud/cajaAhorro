<?php

include_once('connectionCajita.php');
$idSolicitud = $_GET["sol"];
$anioConv = $_GET["a"];
obtenerPrestamoPorId($idSolicitud, $anioConv);

function obtenerPrestamoPorId($idSolicitud, $anioConv){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datosPrueba =  mysqli_query($conex,
        "SELECT
                    s.idSolicitud,
                    s.anioConvocatoria,
                    s.nominaSolicitante,
                    s.fechaSolicitud,
                    s.montoSolicitado,
                    s.fechaDeposito,
                    s.montoDepositado,
                    s.idEstatus,
                    s.telefono,
                    s.montoAprobado,
                    s.comentariosAdmin,
                    s.nominaAval1,
                    s.telAval1,
                    s.nominaAval2,
                    s.telAval2
                FROM
                    Prestamo s
                WHERE idSolicitud = '$idSolicitud'
                  AND anioConvocatoria = '$anioConv'
                ");

    $resultado= mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));

}

?>