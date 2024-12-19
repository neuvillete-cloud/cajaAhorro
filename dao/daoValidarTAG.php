<?php
include_once('connection.php');

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['password'])){
        if($_SESSION['passTag'] == $_POST['password']){
            $respuesta = array("success" => true);
        }else{
            $respuesta = array("success" => false, "message" => "TAG incorrecto.");
        }
    }
    else{
        $respuesta = array("success" => false, "message" => "TAG no proporcionado.");
    }
}else{
    $respuesta = array("success" => false, "message" => "Se esperaba REQUEST_METHOD");
}

echo json_encode($respuesta);
?>