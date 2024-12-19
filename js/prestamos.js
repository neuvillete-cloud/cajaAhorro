const consultarFechaConvocatoria = async () => {
    try {
        const response = await fetch(`https://grammermx.com/RH/CajaDeAhorro/dao/daoConsultarFechaInicio.php`);

        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status} ${response.statusText}`);
        }

        // Parsear la respuesta JSON
        const result = await response.json();
        let fechaInicio = "";
        let horaInicio = "";
        let avisoFechas = "";

        // Verificar que 'data' tenga elementos
        if (result.data && result.data.length > 0) {
            fechaInicio = formatearFecha(result.data[0].fechaInicio);  // Acceder a la primera fecha
            horaInicio = formatearHora(result.data[0].horaInicio);

            avisoFechas = "Recepción de préstamos a partir del día " + fechaInicio + " a las " + horaInicio + " horas.";

        }else {
            avisoFechas = "Las fechas para la recepción de solicitudes de préstamos aún no están definidas.<br>Te invitamos a estar pendiente de futuras actualizaciones.";
        }

        // Asignar los valores al formulario
        $("#avisoFechas").html(avisoFechas);
        $("#avisoFechasP").html(avisoFechas);

        // Comparar con la fecha y hora actuales
        const fechaHoy = formatearFecha(new Date().toISOString().split('T')[0]);

        // Comparar las fechas
        if (fechaHoy > fechaInicio) {
            let avisoFecha = document.getElementById("avisoPrestamo");
            avisoFecha.style.display = "none";
        }

    } catch (error) {
        console.error('Error:', error);
    }
};

function validarUser(user) {
    Swal.fire({
        title: 'Autorización requerida',
        text: 'Para acceder a la Caja de Ahorro, es necesario confirmar tu identidad mediante el TAG, lo que permitirá procesar tus solicitudes.',
        input: 'password',
        inputLabel: 'TAG',
        inputPlaceholder: 'TAG',
        inputAttributes: {
            'aria-label': 'Contraseña'
        },
        showCancelButton: true,
        confirmButtonText: 'Autorizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const password = result.value;

            if (password === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe ingresar un TAG válido.'
                });
            } else {
                // Crea un objeto FormData para enviar los datos al servidor
                const formData = new FormData();
                formData.append('password', password);
                formData.append('user', user);

                // Enviar los datos al servidor mediante fetch
                fetch('dao/daoCompararTAG.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'index.php';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'TAG incorrecto.'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Llamar a la función validarUser(user) cuando se confirme el Swal
                                    validarUser(user);
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema con la conexión.'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                validarUser(user);
                            }
                        });
                    });
            }
        }
    });
}


function validarMonto(montoInput) {
    const montoSinSimbolo = montoInput.replace(/[$\s]/g, '');
    const monto = parseFloat(montoSinSimbolo);

    if (isNaN(monto) || monto <= 0) {
        return null; // Devuelve null si el monto es inválido o no es positivo
    } else {
        return monto; // Devuelve el monto positivo
    }
}



function registrarPrestamo() {
    const telefono = document.getElementById("telefono").value;
    const montoSolicitado = document.getElementById('montoPrestamo').value;


    if (validarTelefono(telefono)) {

        let montoValidado = validarMonto(montoSolicitado);

        if(montoValidado !== null) {

            const data = new FormData();

            data.append('telefono', telefono.trim());
            data.append('montoSolicitado', montoValidado);

            fetch('dao/daoSolicitudPrestamo.php', {
                method: 'POST',
                body: data
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(error => {
                            throw new Error(error.message);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: data.message,
                            icon: "success",
                            text: "¡Solicitud realizada exitosamente!",
                            confirmButtonText: "OK"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "https://grammermx.com/RH/CajaDeAhorro/misSolicitudes.php";
                            }
                        });
                    }else if (data.status === 'error') {
                        console.log(data.message);
                        Swal.fire({
                            title: "Error",
                            text: data.message,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                }).catch(error => {
                Swal.fire({
                    title: "Error",
                    text: error.message,
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });


        }else {
            Swal.fire({
                title: "Datos incorrectos",
                text: "Por favor, ingresa un monto válido.",
                icon: "error"
            });
        }

    } else {
        Swal.fire({
            title: "Datos incorrectos",
            text: "Número de teléfono inválido. Debe ingresar 10 dígitos.",
            icon: "error"
        });
    }
}





