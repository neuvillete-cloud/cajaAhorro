<?php
include_once('connectionCajita.php');

if (!empty($_POST["idSolicitud"]) && !empty($_POST["anio"]) && !empty($_POST["nom1"]) && !empty($_POST["nom2"]) && !empty($_POST["tel1"]) && !empty($_POST["tel2"])){
    $solicitud = $_POST["idSolicitud"];
    $anio = $_POST["anio"];
    $nomina1 = $_POST["nom1"];
    $nomina2 = $_POST["nom2"];
    $tel1 = $_POST["tel1"];
    $tel2 = $_POST["tel2"];

    $response = guardarAvalesDB($solicitud, $anio, $nomina1, $tel1, $nomina2, $tel2);
}else{
    $response = (array("data" => "Faltan datos en el formulario"));
}
echo json_encode($response);

function guardarAvalesDB($solicitud, $anio, $Nomina1, $tel1, $Nomina2, $tel2){
    $con = new LocalConectorCajita();
    $conexion=$con->conectar();

    // Verificar el estatus de la solicitud
    $checkStatus = $conexion->prepare("SELECT idEstatus FROM Prestamo WHERE IdSolicitud = ? AND anioConvocatoria = ?");
    $checkStatus->bind_param("ii", $solicitud, $anio);
    $checkStatus->execute();
    $result = $checkStatus->get_result();
    $status = $result->fetch_assoc();

    if (!$status || $status['idEstatus'] != 3) {
        $conexion->close();
        return array("status" => "error", "message" => "No se puede actualizar los avales porque el estatus no es válido.");
    }

    $updateAvales = $conexion->prepare("UPDATE Prestamo 
                                                 SET nominaAval1 = ?, telAval1 = ?, nominaAval2 = ?, telAval2 = ?
                                               WHERE IdSolicitud = ?
                                                 AND anioConvocatoria = ?");
    $updateAvales->bind_param("ssssii", $Nomina1, $tel1, $Nomina2, $tel2, $solicitud, $anio);
    $resultado = $updateAvales->execute();

    $conexion->close();


    if(!$resultado){
        $response = array("status"=>"error", "message"=>"Error al actualizar los avales.");
    }else{
        $response = array ("status"=>"success", "message"=>"Los avales con nóminas $Nomina1 y $Nomina2 se han registrado correctamente para la solicitud $solicitud.");
    }

    return $response;
}
?>
