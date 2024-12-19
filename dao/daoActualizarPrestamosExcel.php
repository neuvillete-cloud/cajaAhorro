<?php
include_once('connectionCajita.php');
include_once('funcionesGenerales.php');

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (isset($inputData['prestamos']) && is_array($inputData['prestamos'])) {
        $todosExitosos = true;
        $errores = [];
        $fechaActual = date("Y-m-d");

        foreach ($inputData['prestamos'] as $prestamo) {
            $idSolicitud = isset($prestamo['idSolicitud']) ? $prestamo['idSolicitud'] : null;
            $anioConvocatoria = isset($prestamo['anioConvocatoria']) ? $prestamo['anioConvocatoria'] : null;
            $montoDeposito = isset($prestamo['montoDeposito']) ? $prestamo['montoDeposito'] : null;
            $fechaDeposito = isset($prestamo['fechaDeposito']) ? $prestamo['fechaDeposito'] : null;
            $montoAprobado = isset($prestamo['montoAprobado']) ? $prestamo['montoAprobado'] : null;
            $comentariosAdmin = trim(isset($prestamo['comentariosAdmin']) ? $prestamo['comentariosAdmin'] : '');
            $nominaAval1 = isset($prestamo['nominaAval1']) ? $prestamo['nominaAval1'] : '';
            $telAval1 = isset($prestamo['telAval1']) ? $prestamo['telAval1'] : '';
            $nominaAval2 = isset($prestamo['nominaAval2']) ? $prestamo['nominaAval2'] : '';
            $telAval2 = isset($prestamo['telAval2']) ? $prestamo['telAval2'] : '';

            if (empty($idSolicitud) || empty($anioConvocatoria)) {
                $errores[] = "Faltan datos obligatorios para la solicitud ID: $idSolicitud.";
                $todosExitosos = false;
                continue;
            }

            if (!empty($fechaDeposito)) {
                $fechaDepositoFormateada = formatearFecha($fechaDeposito);
                if (!$fechaDepositoFormateada) {
                    $errores[] = "La fecha de depósito es inválida para la solicitud Folio: $idSolicitud.";
                    $todosExitosos = false;
                    continue;
                }
                $fechaDeposito = $fechaDepositoFormateada;
            }

            // Asignar el valor de $idEstatus si se cumple la condición de montoDeposito y fechaDeposito
            $idEstatus = (!empty($montoDeposito) && !empty($fechaDeposito)) ? 4 : null;

            // Validar que $idEstatus esté en el rango de 1 a 5
            if ($idEstatus !== null && ($idEstatus < 1 || $idEstatus > 5)) {
                $errores[] = "El valor de idEstatus debe estar entre 1 y 5.";
                $todosExitosos = false;
                continue;
            }

            // Validar montoAprobado si montoDeposito no está vacío
            if (!empty($montoDeposito) && (empty($montoAprobado) || $montoAprobado == 0)) {
                $montoAprobado = $montoDeposito;
            }


            if (empty($comentariosAdmin)) {
                $comentariosAdmin = 'Sin comentarios.';
            }

            $resultado = actualizarSolicitud(
                $idSolicitud,
                $anioConvocatoria,
                $idEstatus,
                $montoAprobado,
                $montoDeposito,
                $fechaDeposito,
                $comentariosAdmin,
                $fechaActual,
                $nominaAval1,
                $telAval1,
                $nominaAval2,
                $telAval2
            );

            if ($resultado['status'] !== 'success') {
                $errores[] = $resultado['message'];
                $todosExitosos = false;
            }
        }

        $respuesta = $todosExitosos
            ? ["status" => 'success', "message" => "Todas las solicitudes fueron actualizadas exitosamente."]
            : ["status" => 'error', "message" => "Errores al actualizar algunas solicitudes.", "detalles" => $errores];
    } else {
        $respuesta = ["status" => 'error', "message" => "Datos no válidos."];
    }
} else {
    $respuesta = ["status" => 'error', "message" => "Método no permitido, se esperaba POST."];
}

echo json_encode($respuesta);

function actualizarSolicitud($idSolicitud, $anioConvocatoria, $idEstatus, $montoAprobado, $montoDeposito, $fechaDeposito, $comentarios, $fechaRespuesta, $nominaAval1, $telAval1, $nominaAval2, $telAval2) {
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    try {
        $conex->begin_transaction();

        $campos = [
            "idEstatus = ?" => $idEstatus,
            "montoAprobado = ?" => $montoAprobado,
            "comentariosAdmin = ?" => $comentarios,
            "fechaRespuesta = ?" => $fechaRespuesta,
            "nominaAval1 = ?" => $nominaAval1,
            "telAval1 = ?" => $telAval1,
            "nominaAval2 = ?" => $nominaAval2,
            "telAval2 = ?" => $telAval2
        ];

        if (!empty($montoDeposito) && !empty($fechaDeposito)) {
            $campos["montoDepositado = ?"] = $montoDeposito;
            $campos["fechaDeposito = ?"] = $fechaDeposito;
        }

        if (empty($campos)) {
            throw new Exception("No hay campos para actualizar.");
        }

        $query = "UPDATE Prestamo SET " . implode(", ", array_keys($campos)) . " WHERE idSolicitud = ? AND anioConvocatoria = ?";
        $stmt = $conex->prepare($query);

        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $conex->error);
        }

        $parametros = array_values($campos);
        $parametros[] = $idSolicitud;
        $parametros[] = $anioConvocatoria;

        $tipos = "";
        foreach ($parametros as $parametro) {
            $tipos .= is_int($parametro) ? "i" : (is_float($parametro) ? "d" : "s");
        }

        $stmt->bind_param($tipos, ...$parametros);

        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }

        $nomina = $_SESSION["nomina"];
        $descripcion = "Actualización de préstamo Folio: $idSolicitud.";
        if (!actualizarBitacoraCambios($nomina, $fechaRespuesta, $descripcion, $conex)) {
            throw new Exception("Error al registrar en bitácora para la solicitud Folio: $idSolicitud.");
        }

        $conex->commit();
        return ["status" => 'success', "message" => "Solicitud Folio: $idSolicitud actualizada exitosamente."];
    } catch (Exception $e) {
        $conex->rollback();
        error_log("Error en actualizarSolicitud: " . $e->getMessage());
        return ["status" => 'error', "message" => $e->getMessage()];
    } finally {
        $conex->close();
    }
}

?>