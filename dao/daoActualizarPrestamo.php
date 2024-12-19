<?php
include_once('connectionCajita.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['idPrestamo'],$_POST['telefono'],$_POST['montoSolicitado'])){
        $solicitud = $_POST['idPrestamo'];
        $telefono = $_POST['telefono'];
        $montoSolicitado = $_POST['montoSolicitado'];

        $respuesta = actualizarPrestamo($solicitud,$montoSolicitado,$telefono);
    }
    else{
        $respuesta = array("status" => 'error', "message" => "Faltan datos en el formulario.");
    }
}else{
    $respuesta = array("status" => 'error', "message" => "Se esperaba REQUEST_METHOD");
}

echo json_encode($respuesta);

function actualizarPrestamo($solicitud, $montoSolicitado, $telefono)
{
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    // Iniciar transacción
    $conex->begin_transaction();

    $anioActual = date('Y'); // Año actual

    try {
        // Preparar la consulta de actualización
        $updateSol = $conex->prepare("UPDATE Prestamo 
                                      SET montoSolicitado = ?, 
                                          telefono = ? 
                                      WHERE idSolicitud = ?
                                        AND anioConvocatoria = ?");
        if (!$updateSol) {
            throw new Exception("Error al preparar la consulta: " . $conex->error);
        }

        // Vincular parámetros
        $updateSol->bind_param("ssii", $montoSolicitado, $telefono, $solicitud, $anioActual);

        // Ejecutar la consulta
        if (!$updateSol->execute()) {
            throw new Exception("Error al ejecutar la consulta: " . $updateSol->error);
        }

        // Confirmar la transacción
        $conex->commit();
        $respuesta = array("status" => 'success', "message" => "Actualización exitosa");

    } catch (Exception $e) {
        // Si ocurre un error, hacer rollback
        $conex->rollback();
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    } finally {
        // Cerrar el statement y la conexión
        if (isset($updateSol)) {
            $updateSol->close();
        }
        $conex->close();
    }

    return $respuesta;
}
?>