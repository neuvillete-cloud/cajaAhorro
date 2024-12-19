<?php
include_once('connectionCajita.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['idsolicitud'], $_POST['montoAprobado'], $_POST['estatus'], $_POST['comentarios'])) {
        $idSolicitud = trim($_POST['idsolicitud']);
        $montoAprobado = trim($_POST['montoAprobado']);
        $estatus = trim($_POST['estatus']);
        $comentarios = trim($_POST['comentarios']);

        // Validar datos
        if (empty($idSolicitud) || empty($montoAprobado) || empty($estatus)) {
            $respuesta = array("status" => 'error', "message" => "Algunos campos requeridos están vacíos.");
        } else {
            $respuesta = actualizarSolicitudPresAdmin($idSolicitud, $montoAprobado, $estatus, $comentarios);
        }
    } else {
        $respuesta = array("status" => 'error', "message" => "Faltan datos en el formulario.");
    }
} else {
    $respuesta = array("status" => 'error', "message" => "Se esperaba REQUEST_METHOD POST");
}

echo json_encode($respuesta);

function actualizarSolicitudPresAdmin($idSolicitud, $montoAprobado, $estatus, $comentarios) {
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $conex->begin_transaction();
    $anioActual = date('Y'); // Año actual

    try {
        $fechaResp = date("Y-m-d");

        $updateSol = $conex->prepare("UPDATE Prestamo 
                                      SET fechaRespuesta = ?, 
                                          idEstatus = ?, 
                                          montoAprobado = ?, 
                                          comentariosAdmin = ? 
                                      WHERE idSolicitud = ?
                                        AND anioConvocatoria = ?");
        $updateSol->bind_param("sissii", $fechaResp, $estatus, $montoAprobado, $comentarios, $idSolicitud, $anioActual);
        $resultado = $updateSol->execute();

        if (!$resultado) {
            $respuesta = array('status' => 'error', 'message' => 'Error al actualizar la solicitud.');
        }else{
            // Registro en la bitácora
            $nomina = $_SESSION["nomina"];
            $descripcion = "Actualización por admin. Estatus: $estatus, Monto aprobado: $montoAprobado, Comentarios: $comentarios";
            $insertBitacora = $conex->prepare("INSERT INTO BitacoraCambios (nomina, fecha, descripcion) VALUES(?,?,?)");
            $insertBitacora->bind_param("sss", $nomina, $fechaResp, $descripcion);
            $resultadoBitacora = $insertBitacora->execute();

            if (!$resultadoBitacora) {
                $respuesta = array('status' => 'error', 'message' => 'Error al registrar en bitácora.');
            }else {
                $conex->commit();
                $respuesta = array("status" => 'success', "message" => "Solicitud $idSolicitud actualizada exitosamente.");
            }
        }
    } catch (Exception $e) {
        // Deshacer la transacción en caso de error
        $conex->rollback();
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    } finally {
        $conex->close();
    }
    return $respuesta;
}
?>