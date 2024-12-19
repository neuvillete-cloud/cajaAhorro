<?php
include_once('connectionCajita.php');
include_once('funcionesGenerales.php');

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['telefono'],$_POST['montoSolicitado'])){
        $telefono = $_POST['telefono'];
        $montoSolicitado = $_POST['montoSolicitado'];
        $nomina = $_SESSION['nomina'];

        $respuesta = guardarPrestamo($nomina,$montoSolicitado,$telefono);
    }
    else{
        $respuesta = array("status" => 'error', "message" => "Faltan datos en el formulario.");
    }
}else{
    $respuesta = array("status" => 'error', "message" => "Se esperaba REQUEST_METHOD");
}

echo json_encode($respuesta);

function guardarPrestamo($nomina, $montoSolicitado, $telefono) {
    // Configurar la zona horaria
    date_default_timezone_set('America/Mexico_City');

    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    // Iniciar transacción
    $conex->begin_transaction();

    try {
        // Obtener fecha y hora actuales
        $fechaSolicitud = date("Y-m-d");
        $horaSolicitud = date("H:i:s");
        $anioActual = date('Y'); // Año actual

        $convocatoria = validarConvocatoria($conex, $anioActual);

        if($convocatoria["status"] === 'error'){
            throw new Exception($convocatoria["message"]);
        }
        // Obtener los datos de la convocatoria
        $fechaInicio = $convocatoria["data"]["fechaInicio"];
        $horaInicio = $convocatoria["data"]["horaInicio"];
        $fechaFin = $convocatoria["data"]["fechaFin"];
        $horaFin = $convocatoria["data"]["horaFin"];

        // Construir datetime para comparar fecha y hora
        $fechaHoraInicioDB = new DateTime($fechaInicio . ' ' . $horaInicio);
        $fechaHoraCierreDB = new DateTime($fechaFin . ' ' . $horaFin);
        $fechaHoraSolicitud = new DateTime("$fechaSolicitud $horaSolicitud");

        // Comparar fecha y hora actuales con las de la base de datos
        if ($fechaHoraSolicitud >= $fechaHoraInicioDB && $fechaHoraSolicitud <= $fechaHoraCierreDB) {

            // Validar si ya existe una solicitud en proceso en el periodo actual
            $existeSolActiva = validarSolicitud($conex, $nomina, "$fechaSolicitud $horaSolicitud");
            $existeSolRechazada = validarSolicitudRechazada($conex, $nomina, "$fechaSolicitud $horaSolicitud");

            if ($existeSolActiva === 0 && $existeSolRechazada <= 1) {

                $insertPrestamo = $conex->prepare("INSERT INTO Prestamo (anioConvocatoria, nominaSolicitante, montoSolicitado, telefono, fechaSolicitud) 
                                                                VALUES (?, ?, ?, ?, ?)");
                $insertPrestamo->bind_param("issss", $anioActual, $nomina, $montoSolicitado, $telefono, $fechaSolicitud);
                $resultado = $insertPrestamo->execute();

                // Verificar si la inserción fue exitosa
                if (!$resultado) {
                    throw new Exception("Error al guardar el préstamo.");
                }

                // Obtener el ID generado por el trigger
                $idSolicitudQuery = $conex->prepare("SELECT idSolicitud FROM Prestamo WHERE anioConvocatoria = ? AND nominaSolicitante = ? ORDER BY idSolicitud DESC LIMIT 1");
                $idSolicitudQuery->bind_param("is", $anioActual, $nomina);
                $idSolicitudQuery->execute();
                $idSolicitudQuery->bind_result($idSolicitud);
                $idSolicitudQuery->fetch();
                $idSolicitudQuery->close();

                $conex->commit();

                $respuesta = array("status" => 'success', "message" => "Folio de solicitud: " . $idSolicitud);

            }else if($existeSolActiva > 0){
                $respuesta = array("status" => 'error',"message" => "Ya existe una solicitud activa en el periodo actual ".$anioActual);
            }else if($existeSolRechazada > 1) {
                $respuesta = array("status" => 'error', "message" => "Se ha solicitado el máximo de préstamos en el periodo actual " . $anioActual);
            }else {
                $respuesta = $existeSolActiva;
            }

        } else {
            $mensaje = "";
            if ($fechaHoraSolicitud <= $fechaHoraInicioDB) {
                $inicioConvocatoria = formatearFechaHora($fechaInicio, $horaInicio);
                $mensaje = "Por el momento no es posible atender tu solicitud. Las solicitudes se estarán recibiendo a partir del día $inicioConvocatoria horas.";
            } else if ($fechaHoraSolicitud >= $fechaHoraCierreDB) {
                $finConvocatoria = formatearFechaHora($fechaFin, $horaFin);
                $mensaje = "No es posible atender tu solicitud. La recepción de solicitudes terminó el día $finConvocatoria horas.";
            }
            $respuesta = array("status" => 'error', "message" => $mensaje);
        }

    } catch (Exception $e) {
        // Si ocurre un error, hacer rollback y mostrar el mensaje de error
        $conex->rollback();
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    }finally {
        $conex->close();
    }

    return $respuesta;
}

function validarConvocatoria($conex, $anio)
{
    $selectFechaAut = $conex->prepare("SELECT fechaInicio, horaInicio, fechaFin, horaFin FROM Convocatoria WHERE anio = ?");
    $selectFechaAut->bind_param("i", $anio);
    $selectFechaAut->execute();
    $resultado = $selectFechaAut->get_result();

    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $respuesta = array(
            "status" => 'success',
            "data" => $row
        );
    } else {
        $respuesta = array(
            "status" => 'error',
            "message" => "No se encontraron fechas registradas para el año actual.",
        );
    }

    return $respuesta;
}


function validarSolicitud($conex, $nomina, $fechaHoraSolicitud) {
    try {
        $anioSolicitud = (new DateTime($fechaHoraSolicitud))->format('Y');

        // Verificar si ya existe una solicitud en proceso para este año
        $queryValidacion = $conex->prepare(
            "SELECT COUNT(*) AS total FROM Prestamo 
             WHERE nominaSolicitante = ? 
             AND YEAR(fechaSolicitud) = ? 
             AND idEstatus IN (1, 3)"
        );
        $queryValidacion->bind_param("si", $nomina, $anioSolicitud);
        $queryValidacion->execute();
        $resultadoValidacion = $queryValidacion->get_result();
        $row = $resultadoValidacion->fetch_assoc();
        $respuesta = $row["total"];

    } catch (Exception $e) {
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    }

    return $respuesta;
}

/*Valida cuantos prestamos rechazados existen en el mismo año*/
function validarSolicitudRechazada($conex, $nomina, $fechaHoraSolicitud) {
    try {
        $anioSolicitud = (new DateTime($fechaHoraSolicitud))->format('Y');

        // Verificar si ya existe una solicitud en proceso para este año
        $queryValidacion = $conex->prepare(
            "SELECT COUNT(*) AS total FROM Prestamo 
             WHERE nominaSolicitante = ? 
             AND YEAR(fechaSolicitud) = ?"
        );
        $queryValidacion->bind_param("si", $nomina, $anioSolicitud);
        $queryValidacion->execute();
        $resultadoValidacion = $queryValidacion->get_result();
        $row = $resultadoValidacion->fetch_assoc();
        $respuesta = $row["total"];

    } catch (Exception $e) {
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    }

    return $respuesta;
}

?>