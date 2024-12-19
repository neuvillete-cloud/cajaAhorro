<?php

include_once('connectionCajita.php');
include_once('connection.php');

$anio = $_GET["anio"];
$ahorros = todosLosAhorros($anio);
$ahorrosConNombres = ObtenerNombresAhorros($ahorros);
echo json_encode(array("data" => $ahorrosConNombres));

function ObtenerNombresAhorros($ahorros)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    $ahorrosConNombres = [];

    foreach($ahorros as $ahorro){
        $nomina = $ahorro['nomina'];
        $result = mysqli_query($conex, "SELECT NomUser FROM Empleados WHERE IdUser = '$nomina'");

            if($row = mysqli_fetch_assoc($result)){
                $ahorro['NomUser'] = $row['NomUser'];
            }else{
                $ahorro['NomUser'] = null;
            }
            $ahorrosConNombres[] = $ahorro;
    }
    return $ahorrosConNombres;
}

function todosLosAhorros($anio){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datosPrueba =  mysqli_query($conex,
        "SELECT
                    idCaja,
                    nomina,
                    montoAhorro,
                    fechaSolicitud
                FROM
                    CajaAhorro 
                WHERE
                    YEAR(fechaSolicitud) like '$anio'
                ORDER BY
                    idCaja DESC;
                ");

    return mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
}

?>