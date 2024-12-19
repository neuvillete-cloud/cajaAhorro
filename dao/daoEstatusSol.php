<?php
include_once('connectionCajita.php');
ContadorEstatus();

function ContadorEstatus(){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datos = mysqli_query($conex, "SELECT idEstatus,descripcion
                                           FROM EstatusPrestamo 
                                          ORDER BY idEstatus;");

    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}