<?php

include_once('connectionCajita.php');
include_once('connection.php');

$anio = $_GET["anio"];
$retiros = todosLosRetiros($anio);
$retirosConNombres = ObtenerNombresRetiros($retiros);

echo json_encode(array("data" => $retirosConNombres));

function ObtenerNombresRetiros($retiros)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    // Crear un array para almacenar los nombres obtenidos
    $retirosConNombres = [];

    // Extraer los IdUser de cada registro de préstamo y realizar la consulta
    foreach ($retiros as $retiro) {
        $nominaSolicitante = $retiro['nomina'];

        // Consulta para obtener el NomUser correspondiente
        $result = mysqli_query($conex, "SELECT NomUser FROM Empleados WHERE IdUser = '$nominaSolicitante'");

        if ($row = mysqli_fetch_assoc($result)) {
            // Agregar el nombre al arreglo de préstamo
            $retiro['NomUser'] = $row['NomUser'];
        } else {
            $retiro['NomUser'] = null; // En caso de que no se encuentre el usuario
        }

        $retirosConNombres[] = $retiro;
    }

    return $retirosConNombres;
}

function todosLosRetiros($anio){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datosPrueba =  mysqli_query($conex,
        "SELECT
                    idRetiro,
                    C.idCaja,
                    nomina,
                    R.fechaSolicitud,
                    montoDepositado,
                    fechaDeposito
                FROM
                    CajaAhorro C, RetiroAhorro R
                WHERE
                    YEAR(R.fechaSolicitud) like '$anio'
                    AND C.idCaja = R.idCaja
                ORDER BY
                    R.fechaSolicitud DESC;
                ");

    return mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
}
?>