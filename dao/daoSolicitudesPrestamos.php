<?php

include_once('connectionCajita.php');
include_once('connection.php');

$anio = $_GET["anio"];
$prestamos = todosLosPrestamos($anio);
$prestamosConNombres = ObtenerNombres($prestamos);

echo json_encode(array("data" => $prestamosConNombres));

function ObtenerNombres($prestamos)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    // Crear un array para almacenar los nombres obtenidos
    $prestamosConNombres = [];

    // Extraer los IdUser de cada registro de préstamo y realizar la consulta
    foreach ($prestamos as $prestamo) {
        $nominaSolicitante = $prestamo['nominaSolicitante'];

        // Consulta para obtener el NomUser correspondiente
        $result = mysqli_query($conex, "SELECT NomUser FROM Empleados WHERE IdUser = '$nominaSolicitante'");

        if ($row = mysqli_fetch_assoc($result)) {
            // Agregar el nombre al arreglo de préstamo
            $prestamo['NomUser'] = $row['NomUser'];
        } else {
            $prestamo['NomUser'] = null; // En caso de que no se encuentre el usuario
        }

        $prestamosConNombres[] = $prestamo;
    }

    return $prestamosConNombres;
}

function todosLosPrestamos($anio)
{
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datosPrueba = mysqli_query($conex, "
        SELECT
            s.idSolicitud,
            s.anioConvocatoria,
            s.nominaSolicitante,
            s.fechaSolicitud,
            s.montoSolicitado,
            s.telefono,
            s.idEstatus,
            CASE
                WHEN s.idEstatus = 1 
                    THEN CONCAT('<span class=\"badge bg-warning text-dark\" title=\"', e.detalles, '\">', e.descripcion, '</span>')
                WHEN s.idEstatus = 2 
                    THEN CONCAT('<span class=\"badge bg-danger\" title=\"', e.detalles, '\">', e.descripcion, '</span>')
                WHEN s.idEstatus = 3 
                    THEN CONCAT('<span class=\"badge bg-success\" title=\"', e.detalles, '\">', e.descripcion, '</span>')
                WHEN s.idEstatus = 4 
                    THEN CONCAT('<span class=\"badge bg-dark\" title=\"', e.detalles, '\">', e.descripcion, '</span>')
                WHEN s.idEstatus = 5
                    THEN CONCAT('<span class=\"badge bg-secondary\" title=\"', e.detalles, '\">', e.descripcion, '</span>')
            END AS estatusVisual,
            e.descripcion,
            nominaAval1,
            telAval1,
            nominaAval2,
            telAval2,
            fechaRespuesta,
            montoAprobado,
            s.fechaDeposito,
            s.montoDepositado,
            comentariosAdmin
        FROM
            Prestamo s
            LEFT JOIN EstatusPrestamo e ON s.idEstatus = e.idEstatus
        WHERE YEAR(s.fechaSolicitud) LIKE '$anio'
        ORDER BY
            s.idSolicitud DESC
    ");

    return mysqli_fetch_all($datosPrueba, MYSQLI_ASSOC);
}

?>
