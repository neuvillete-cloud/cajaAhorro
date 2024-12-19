<?php
    $conexion = mysqli_connect("127.0.0.1:3306","u909553968_Ale","Grammer2024a","u909553968_testAle");
    if (!$conexion) {
        echo 'conexion exitosa';
    }
    else {
        echo 'conexion fallida';
    }

    ?>