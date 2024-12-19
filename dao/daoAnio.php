<?php
include_once('connectionCajita.php');
ContadorAnios();

function ContadorAnios(){
    $con = new LocalConectorCajita();
    $conex = $con->conectar();

    $datos = mysqli_query($conex, " SELECT DISTINCT YEAR(c.fechaSolicitud) AS anio
                                            FROM CajaAhorro c
                                           WHERE c.fechaSolicitud IS NOT NULL 
                                             AND c.fechaSolicitud != '0000-00-00'
                                           UNION
                                          SELECT DISTINCT YEAR(p.fechaSolicitud) AS anio
                                            FROM Prestamo p
                                           WHERE p.fechaSolicitud IS NOT NULL 
                                             AND p.fechaSolicitud != '0000-00-00'
                                        ORDER BY anio;
                                            ");

    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}

?>