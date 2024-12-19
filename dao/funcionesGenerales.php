<?php

function actualizarBitacoraCambios( $nomina, $fechaResp, $descripcion, $conex)
{
    $insertBitacora = $conex->prepare("INSERT INTO BitacoraCambios (nomina, fecha, descripcion) VALUES(?,?,?)");
    $insertBitacora->bind_param("sss", $nomina, $fechaResp, $descripcion);
    return $insertBitacora->execute();
}

function formatearFecha($fecha) {
    // Intentar crear un objeto DateTime desde la fecha ingresada
    $fechaFormateada = DateTime::createFromFormat('Y/m/d', $fecha);

    // Verificar si la fecha es válida
    if ($fechaFormateada && $fechaFormateada->format('Y/m/d') === $fecha) {
        return $fechaFormateada->format('Y/m/d');
    } else {
        // Intentar crear un objeto DateTime desde la fecha en otros formatos comunes
        $fechaAlternativa = DateTime::createFromFormat('d/m/Y', $fecha) // d/m/Y
            ?: DateTime::createFromFormat('m/d/Y', $fecha) // m/d/Y
                ?: DateTime::createFromFormat('Y-m-d', $fecha) // Y-m-d
                    ?: DateTime::createFromFormat('d/m/y', $fecha) // d/m/y
                        ?: DateTime::createFromFormat('m/d/y', $fecha) // m/d/y
                            ?: DateTime::createFromFormat('Y-m-d H:i:s', $fecha); // Y-m-d H:i:s

        // Manejo del formato con mes en texto
        if (!$fechaAlternativa) {
            $meses = [
                'enero' => '01', 'febrero' => '02', 'marzo' => '03', 'abril' => '04',
                'mayo' => '05', 'junio' => '06', 'julio' => '07', 'agosto' => '08',
                'septiembre' => '09', 'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'
            ];
            // Cambiar la fecha a formato d/m/Y o m/d/Y si tiene un mes en texto
            foreach ($meses as $mesTexto => $mesNumero) {
                if (stripos($fecha, $mesTexto) !== false) {
                    // Reemplazar el mes por su número correspondiente
                    $fecha = preg_replace('/\b' . preg_quote($mesTexto, '/') . '\b/i', $mesNumero, $fecha);
                    break; // Salir del bucle una vez que se haya encontrado y reemplazado
                }
            }
            // Intentar nuevamente con el formato cambiado
            $fechaAlternativa = DateTime::createFromFormat('d/m/Y', $fecha)
                ?: DateTime::createFromFormat('m/d/Y', $fecha);
        }

        // Si se pudo crear un objeto DateTime, ajustar la fecha al formato yyyy/mm/dd
        if ($fechaAlternativa) {
            return $fechaAlternativa->format('Y/m/d'); // Convertir a formato deseado
        } else {
            return false; // La fecha no es válida
        }
    }
}

// Función auxiliar para validar montos
function validarMonto($montoAhorro) {
    $valor = trim($montoAhorro);

    if (strpos($valor, '$') === 0) {
        $valor = substr($valor, 1);
    }

    if (!is_numeric($valor) || floatval($valor) <= 0) {
        return array(
            'status' => 'error',
            'message' => 'El monto ingresado no es válido.'
        );
    }

    $numero = number_format(floatval($valor), 2, '.', '');

    return array(
        'status' => 'success',
        'message' => 'El monto ingresado es válido.',
        'monto' => $numero
    );
}
/**
 * Formatea una fecha y hora para mostrarla en español.
 *
 * @param string $fecha Fecha en formato "Y-m-d".
 * @param string $hora Hora en formato "H:i:s".
 * @return string Fecha y hora formateada (e.g., "25 de noviembre a las 13:00").
 */
function formatearFechaHora($fecha, $hora) {
    // Crear objetos DateTime para la fecha y hora
    $fechaDateTime = DateTime::createFromFormat("Y-m-d", $fecha);
    $horaDateTime = DateTime::createFromFormat("H:i:s", $hora);

    // Diccionario para traducir los meses manualmente
    $meses = [
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    ];

    // Obtener el nombre del mes en español
    $nombreMes = $meses[$fechaDateTime->format('F')];

    // Formatear la fecha y la hora
    $fechaFormateada = $fechaDateTime->format("d") . " de " . $nombreMes;
    $horaFormateada = $horaDateTime->format("H:i");

    return "$fechaFormateada a las $horaFormateada";
}

function generarNomina($nomina) {
    // Asegura que la nómina es un string y completa con ceros a la izquierda hasta alcanzar 8 caracteres
    return str_pad($nomina, 8, "0", STR_PAD_LEFT);
}

?>