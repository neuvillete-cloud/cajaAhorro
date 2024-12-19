<?php
include_once('connectionCajita.php');
function getAdmin($Nomina){
    $con = new LocalConectorCajita();
    $conexion=$con->conectar();

    $consP="SELECT estatus FROM Administrador WHERE idAdmin = '$Nomina'";
    $rsconsPro=mysqli_query($conexion,$consP);

    mysqli_close($conexion);

    if(mysqli_num_rows($rsconsPro) == 1){
        $row = mysqli_fetch_assoc($rsconsPro);
        return array(
            'success' => true, // Indicador de éxito
            'estatus' => $row['estatus']
        );
    }
    else{
        return array(
            'success' => false
        );
    }
}

?>