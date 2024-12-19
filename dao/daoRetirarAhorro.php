<?php
include_once('connectionCajita.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nomina = $_SESSION['nomina'];

    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $conex->begin_transaction();

    try {
        $obtenerIdCaja = $conex->prepare("SELECT idCaja FROM CajaAhorro WHERE nomina = ? AND fechaSolicitud LIKE ?");
        $anio = date("Y");
        $fechaFiltro = "$anio%";
        $obtenerIdCaja->bind_param("ss", $nomina, $fechaFiltro);

        $obtenerIdCaja->execute();
        $obtenerIdCaja->store_result(); // Almacena el resultado

        if ($obtenerIdCaja->num_rows > 0) {
            $obtenerIdCaja->bind_result($idCaja); // Vincula el resultado
            $obtenerIdCaja->fetch(); // Obtiene el resultado

            $fechaSolicitud = date("Y-m-d");

            $rGuardarObjetos = true;
            $insertRetiro = $conex->prepare("INSERT INTO `RetiroAhorro` (`idCaja`, `fechaSolicitud`) VALUES (?, ?)");
            $insertRetiro->bind_param("is", $idCaja, $fechaSolicitud);
            $rGuardarObjetos = $rGuardarObjetos && $insertRetiro->execute();

            if (!$rGuardarObjetos) { // Verifica la inserción
                throw new Exception("Error al guardar el registro.");
            } else {
                $respuesta = array("status" => 'success', "message" => "Tu retiro de ahorro ha sido aprobado exitosamente, lo verás reflejado próximamente con el depósito de tu nómina.");
            }
            $conex->commit();
        } else {
            $respuesta = array('status' => 'error', 'message' => 'No existe caja de ahorro activa.');
        }

        $obtenerIdCaja->close();

    } catch (Exception $e) {
        $conex->rollback();
        $respuesta = array("status" => 'error', "message" => $e->getMessage());
    }
} else {
    $respuesta = array("status" => 'error', "message" => "Se esperaba REQUEST_METHOD");
}

echo json_encode($respuesta);
?>