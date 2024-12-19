<?php
include_once('connectionCajita.php');

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['montoAhorro'],$_POST['nombres'],$_POST['porcentajes'],$_POST['telefonos'],$_POST['domicilios'])){
        $montoAhorro = $_POST['montoAhorro'];
        $nomina = $_SESSION['nomina'];

        $nombres = explode(', ', $_POST['nombres']);
        $porcentajes = explode(', ', $_POST['porcentajes']);
        $telefonos = explode(', ', $_POST['telefonos']);
        $domicilios = explode(', ', $_POST['domicilios']);


        $respuesta = guardarAhorro($nomina, $montoAhorro, $nombres, $porcentajes, $telefonos,$domicilios );
    }
    else{
        $respuesta = array("status" => 'error', "message" => "Faltan datos en el formulario.");
    }
}else{
    $respuesta = array("status" => 'error', "message" => "Se esperaba REQUEST_METHOD");
}

echo json_encode($respuesta);

function guardarAhorro($nomina, $monto, $nombres, $porcentajes, $telefonos,$domicilios) {
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $conex->begin_transaction();

    try {
        $fechaSolicitud = date("Y-m-d");

        $insertAhorro = $conex->prepare("INSERT INTO CajaAhorro (nomina, montoAhorro, fechaSolicitud) VALUES ( ?, ?, ?)");
        $insertAhorro->bind_param("sss", $nomina, $monto, $fechaSolicitud);
        $resultado = $insertAhorro->execute();

        if (!$resultado) {
            throw new Exception("Error al guardar el registro.");
        }else{
            $rGuardarObjetos = true;
            // Obtener el ID generado automáticamente
            $idSolicitud = $conex->insert_id;

            //Registrar Beneficiarios
            for ($i = 0; $i < count($nombres); $i++) {
                $nombre = $nombres[$i];
                $porcentaje = $porcentajes[$i];
                $telefono = $telefonos[$i];
                $domicilio = $domicilios[$i];

                // Inserción en la base de datos
                $insertBeneficiario = $conex->prepare("INSERT INTO `Beneficiarios` (`idCaja`, `nombre`, `direccion`, `telefono`, `porcentaje`) 
                                               VALUES (?, ?, ?, ?, ?)");
                $insertBeneficiario->bind_param("issss", $idSolicitud, $nombre, $domicilio, $telefono, $porcentaje);
                $rGuardarObjetos = $rGuardarObjetos && $insertBeneficiario->execute();
            }

            if(!$rGuardarObjetos){
                $respuesta = array('status' => 'error', 'message' => 'Error en Registrar Solicitud');
            }else{
                $respuesta = array("status" => 'success', "message" => "Tu ahorro ha sido aprobado exitosamente, lo veras reflejado proximamente en tu nómina.");
            }
        }
        $conex->commit();

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