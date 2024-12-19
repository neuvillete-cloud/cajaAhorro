<?php
include_once('connection.php');
include_once('daoUsuario.php');
include_once('daoVerificarAdmin.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['password'], $_POST['user'])){

        $Nomina = $_POST['user'];
            if (strlen($Nomina) < 8) { // Validar los ceros (8 dígitos)
                $Nomina = str_pad($Nomina, 8, "0", STR_PAD_LEFT);
            }
        $resultado = Usuario($Nomina);

        if($resultado['success'] && $_POST['password'] == $resultado['password_bd']){
            session_start();

            $_SESSION['nombreUsuario'] = $resultado['nombreUsuario'];
            $_SESSION['nomina'] = $resultado['idUser'];
            $_SESSION['passTag'] = $resultado['password_bd'];

            $consultarEstatus = getAdmin($Nomina);
            if ($consultarEstatus['success']) {
                $_SESSION['admin'] = $consultarEstatus['estatus'];
            } else {
                $_SESSION['admin'] = 0;
            }

            $respuesta = array("success" => true, "message" => "Inicio de sesión correcto.");
        } else {
            $respuesta = array("success" => false, "message" => "TAG incorrecto.");
        }
    }
    else{
        $respuesta = array("success" => false, "message" => "Faltan datos en el formulario.");
    }
}else{
    $respuesta = array("success" => false, "message" => "Se esperaba REQUEST_METHOD");
}

echo json_encode($respuesta);
?>
