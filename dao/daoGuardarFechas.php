<?php
include_once('connectionCajita.php');

if (!empty($_POST["fechaInicio"]) && !empty($_POST["fechaCierre"]) && !empty($_POST["anio"]) && !empty($_POST["horaInicio"]) && !empty($_POST["horaCierre"]) ) {
    $fechaInicio = $_POST["fechaInicio"];
    $fechaCierre = $_POST["fechaCierre"];
    $horaInicio = $_POST["horaInicio"];
    $horaCierre = $_POST["horaCierre"];

    $anio = intval($_POST["anio"]);

    // Validar que las fechas sean válidas
    $anioInicio = intval(date('Y', strtotime($fechaInicio)));
    $anioCierre = intval(date('Y', strtotime($fechaCierre)));
    $anioActual = intval(date('Y'));

    // Validar que ambas fechas pertenezcan al mismo año
    if ($anioInicio !== $anioCierre) {
        $response = array("status" => 'error', "message" => "Ambas fechas deben pertenecer al mismo año.");
    }
    // Validar que el año coincida con el año actual
    elseif ($anioInicio !== $anioActual) {
        $response = array("status" => 'error', "message" => "Las fechas deben corresponder al año actual ($anioActual).");
    }
    // Validar que el año proporcionado coincida con el año de las fechas
    elseif ($anioInicio !== $anio) {
        $response = array("status" => 'error', "message" => "El año ingresado no coincide con las fechas seleccionadas.");
    }
    // Validar que la fecha de inicio sea anterior a la fecha de cierre
    elseif ($fechaInicio >= $fechaCierre) {
        $response = array("status" => 'error', "message" => "La fecha de inicio debe ser anterior a la fecha de cierre.");
    }
    // Guardar las fechas si todas las validaciones son correctas
    else {
        $response = guardarFechas($fechaInicio, $fechaCierre, $anio, $horaInicio, $horaCierre);
    }
} else {
    $response = array("status" => 'error', "message" => "Faltan datos en el formulario.");
}

echo json_encode($response);


function guardarFechas($fechaInicio, $fechaCierre, $anio, $horaInicio, $horaCierre){
    $con = new LocalConectorCajita();
    $conexion = $con->conectar();

    // Buscar en la base de datos si ya existe el registro
    $selectFechas = $conexion->prepare("SELECT id FROM Convocatoria WHERE anio = ?");
    $selectFechas->bind_param("i", $anio);
    $selectFechas->execute();
    $selectFechas->store_result(); // Almacena el resultado

    // Si ya hay un registro con ese año, se actualiza
    if ($selectFechas->num_rows > 0) {
        $updateFechas = $conexion->prepare("UPDATE Convocatoria SET fechaInicio = ?, fechaFin = ?, horaInicio = ?, horaFin = ? WHERE anio = ?");
        $updateFechas->bind_param("ssssi", $fechaInicio, $fechaCierre, $horaInicio, $horaCierre, $anio);
        $resultado = $updateFechas->execute();
    } else {
        $insertFechas = $conexion->prepare("INSERT INTO Convocatoria (anio, fechaInicio, fechaFin, horaInicio, horaFin) VALUES (?, ?, ?, ?, ?)");
        $insertFechas->bind_param("issss", $anio, $fechaInicio, $fechaCierre, $horaInicio, $horaCierre);
        $resultado = $insertFechas->execute();
    }
    $conexion->close();

    if (!$resultado) {
        $response = array("status" => "error", "message" => "Error al actualizar las fechas.");
    } else {
        $response = array("status" => "success", "message" => "Fechas actualizadas exitosamente");
    }

    return $response;
}
?>