<?php

include_once('connection.php');

function Usuario($Nomina){
    $con = new LocalConector();
    $conexion = $con->conectar();

    // Usar una consulta preparada
    $consP = "SELECT IdUser, NomUser, IdTag FROM Empleados WHERE IdUser = ?";
    $stmt = mysqli_prepare($conexion, $consP);

    // Vincular el parámetro
    mysqli_stmt_bind_param($stmt, "s", $Nomina);

    // Ejecutar la consulta
    mysqli_stmt_execute($stmt);

    // Obtener los resultados
    $rsconsPro = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conexion);

    if(mysqli_num_rows($rsconsPro) == 1){
        $row = mysqli_fetch_assoc($rsconsPro);
        return array(
            'success' => true,
            'password_bd' => $row['IdTag'],
            'nombreUsuario' => $row['NomUser'],
            'idUser' => $row['IdUser']
        );
    }
    else{
        return array(
            'success' => false
        );
    }
}

?>