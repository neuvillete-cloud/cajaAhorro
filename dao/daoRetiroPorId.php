<?php

include_once('connectionCajita.php');
$idSolicitud = $_GET["ret"];
obtenerRetiroPorId($idSolicitud);

function obtenerRetiroPorId($idSolicitud){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datosPrueba =  mysqli_query($conex,
        "SELECT
                    r.idRetiro as folioRetiro,
                    r.idCaja as folioCaja,
                    r.fechaSolicitud as fechaSol,
                    r.montoDepositado as montoDep,
                    r.fechaDeposito as fechaDep,
                    r.estatusRetiro as estatusRet,
                    c.nomina as usuario,
                    CASE
                        WHEN r.estatusRetiro = 0 THEN 'En proceso'
                        WHEN r.estatusRetiro = 1 THEN 'Completado'
                    END AS estatusVisual
                FROM
                    RetiroAhorro r, CajaAhorro c
                WHERE r.idRetiro = '$idSolicitud'
                  AND r.idCaja = c.idCaja
                ");

    $resultado= mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));

}

?>
