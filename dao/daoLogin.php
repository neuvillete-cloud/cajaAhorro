<?php
require_once 'daoUsuario.php';
require_once 'daoVerificarAdmin.php';

if (isset($_POST['iniciarSesionBtn'])) {
    session_start();
    $Nomina = $_POST['numNomina'];

    if (strlen($Nomina) < 8) { // Validar los ceros (8 dígitos)
        $Nomina = str_pad($Nomina, 8, "0", STR_PAD_LEFT);
    }

    $resultado = Usuario($Nomina);

    if ($resultado['success']) {
        $_SESSION['nombreUsuario'] = $resultado['nombreUsuario'];
        $_SESSION['nomina'] = $resultado['idUser'];
        $_SESSION['passTag'] = $resultado['password_bd'];

        $password_bd = $resultado['password_bd'];
        $passwordS = $_POST['password'];

        $consultarEstatus = getAdmin($Nomina);
        if ($consultarEstatus['success']) {
            $_SESSION['admin'] = $consultarEstatus['estatus'];
        } else {
            $_SESSION['admin'] = 0;
        }

        // Output SweetAlert2 JavaScript
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                if ('$password_bd' === '$passwordS') {
                        window.location.href = '../index.php';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'TAG incorrecto',
                        text: 'TAG incorrecto, verifique sus datos.',
                    }).then(function() {
                        window.location.href = 'https://grammermx.com/RH/CajaDeAhorro/login.php';
                    });
                }
            });
        </script>";
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Usuario no encontrado',
                    text: 'Verifique el número de nómina ingresado.',
                }).then(function() {
                    window.location.href = 'https://grammermx.com/RH/CajaDeAhorro/login.php';
                });
            });
        </script>";
    }
}

if (isset($_POST['cerrarSesion']) || isset($_POST['cerrarSesionMisSs'])) {
    session_start();
    session_destroy();
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Sesión finalizada',
                text: 'Sesión cerrada exitosamente.',
            }).then(function() {
                window.location.href = 'https://grammermx.com/RH/CajaDeAhorro/login.php';
            });
        });
    </script>";
}

?>