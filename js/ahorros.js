function validarFormAhorro() {
    const montoAhorro = document.getElementById('montoAhorro').value;
    const nombreBen1 = document.getElementById('nombreBen1').value;
    let porcentajeBen1 = document.getElementById('porcentajeBen1').value;
    const telefonoBen1 = document.getElementById('telefonoBen1').value;
    const domicilioBen1 = document.getElementById('domicilioBen1').value;

    // Validar inputs
    const montoValido = validarInput('montoAhorro');
    const nombreBen1Valido = validarInput('nombreBen1');
    const porcentajeBen1Valido = validarInput('porcentajeBen1');
    const telefonoBen1Valido = validarInput('telefonoBen1');
    const domicilioBen1Valido = validarInput('domicilioBen1');

    let nombres = [];
    let porcentajes = [];
    let telefonos = [];
    let domicilios = [];

    // Validación para asegurarse de que todos los campos del Beneficiario 1 estén llenos
    if (montoValido && nombreBen1Valido && porcentajeBen1Valido && telefonoBen1Valido && domicilioBen1Valido) {
        let montoAhorroValidado = validarMonto(montoAhorro);

        if(montoAhorroValidado === null){
            Swal.fire({
                icon: 'error',
                title: 'Monto inválido',
                text: "Ingrese un monto válido"
            });
            return;
        }

        //alert("montoValido:"+montoValido +"nombreBen1Valido:"+ nombreBen1Valido +"porcentajeBen1Valido:"+ porcentajeBen1Valido  +"telefonoBen1Valido:"+ telefonoBen1Valido +"domicilioBen1Valido:"+ domicilioBen1Valido)
        let valporcentajeBen1 = validarPorcentaje(porcentajeBen1);
        let valtelefonoBen1 = validarTelefono(telefonoBen1);

        if (valporcentajeBen1 && valtelefonoBen1) {
            // Verificar si los campos del Beneficiario 2 no están vacíos antes de añadirlos
            if (existeBen2) {
                const nombreBen2 = document.getElementById('nombreBen2').value;
                const porcentajeBen2 = document.getElementById('porcentajeBen2').value;
                const telefonoBen2 = document.getElementById('telefonoBen2').value;
                const domicilioBen2 = document.getElementById('domicilioBen2').value;

                console.log("ben2: " + nombreBen2 + " " + porcentajeBen2 + " " + telefonoBen2 + " " + domicilioBen2);

                const nombreBen2Valido = validarInput('nombreBen2');
                const porcentajeBen2Valido = validarInput('porcentajeBen2');
                const telefonoBen2Valido = validarInput('telefonoBen2');
                const domicilioBen2Valido = validarInput('domicilioBen2');

                if (nombreBen2Valido && porcentajeBen2Valido && telefonoBen2Valido && domicilioBen2Valido) {
                    let valporcentajeBen2 = validarPorcentaje(porcentajeBen2);
                    let valtelefonoBen2 = validarTelefono(telefonoBen2);

                    if (valporcentajeBen2 && valtelefonoBen2) {
                        if ((Number(porcentajeBen1) + Number(porcentajeBen2)) === 100) {
                            nombres.push(nombreBen2.trim());
                            porcentajes.push(porcentajeBen2.trim());
                            telefonos.push(telefonoBen2.trim());
                            domicilios.push(domicilioBen2.trim());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: "Ambos porcentajes deben sumar 100%"
                            });
                            return;  // Detener si los porcentajes no suman 100
                        }
                    }else{
                        mostrarMsjError(valporcentajeBen2,valtelefonoBen2);
                        return;
                    }
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "Ingrese los datos faltantes"
                    });
                    return;  // Detener si los porcentajes no suman 100
                }
            }else{
                porcentajeBen1 = 100;  // Si no hay beneficiario 2, el porcentaje es 100 para el 1
            }

            nombres.push(nombreBen1.trim());
            porcentajes.push(porcentajeBen1);
            telefonos.push(telefonoBen1.trim());
            domicilios.push(domicilioBen1.trim());

            // Si todo está validado, llamar a registrarAhorro
            let dao = "dao/daoGuardarAhorro.php";
            registrarAhorro(montoAhorroValidado,nombres,porcentajes,telefonos,domicilios, dao);
        } else {
            mostrarMsjError(valporcentajeBen1,valtelefonoBen1);
        }
    }
}

function mostrarMsjError(valporcentaje,valtelefono){
    let mensaje = "";
    if (!valporcentaje) mensaje = "Ingrese un porcentaje válido";
    if (!valtelefono) mensaje = "Ingrese un teléfono válido";
    if (!valtelefono && !valporcentaje) mensaje = "Ingrese un porcentaje y un teléfono válidos";

    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}

function autorizarSolicitudAhorro(montoAhorro,nombres,porcentajes,telefonos,domicilios){

    Swal.fire({
        title: 'Autorización requerida',
        input: 'password',
        inputLabel: 'Ingresa tu TAG',
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

                const formData = new FormData(document.getElementById('formRegistrarAhorro'));

                formData.append('password', password);

                // Enviar los datos al servidor
                fetch('dao/daoValidarTAG.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {

                            registrarAhorro(montoAhorro,nombres,porcentajes,telefonos,domicilios);

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message // Mostrar el mensaje de error devuelto por el servidor
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al validar la contraseña. Intente nuevamente.'
                        });
                    });
            }
        }
    });
}


function registrarAhorro(montoAhorro,nombres,porcentajes,telefonos,domicilios, dao) {

    const formData = new FormData();
    formData.append('montoAhorro', montoAhorro);
    formData.append('nombres', nombres.join(', '));
    formData.append('porcentajes', porcentajes.join(', '));
    formData.append('telefonos', telefonos.join(', '));
    formData.append('domicilios', domicilios.join(', '));

    let formDataContents = '';
    for (let pair of formData.entries()) {
        formDataContents += `${pair[0]}: ${pair[1]}\n`;
    }
    //alert(formDataContents);

    // Enviar los datos utilizando fetch
    fetch(dao, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success"){
                Swal.fire({
                    title: "¡Solicitud exitosa!",
                    icon: "success",
                    text: data.message,
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "https://grammermx.com/RH/CajaDeAhorro/misSolicitudes.php";
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message // Mostrar el mensaje de error devuelto por el servidor
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            //alert('Ocurrió un error al procesar la solicitud.');
        });
}

function validarPorcentaje(valor) {
    valor = valor.trim();

    // Si el valor termina con '%', lo eliminamos
    if (valor.endsWith('%')) {
        valor = valor.slice(0, -1);
    }

    // Convertir el valor a número
    const porcentaje = parseFloat(valor);

    // Verificar que el valor sea un número válido y esté entre 0 y 100
    if (isNaN(porcentaje) || porcentaje < 0 || porcentaje > 100) {
        return false; // No es un porcentaje válido
    }

    return true; // Es un porcentaje válido
}

function validarInput(idInput) {
    const inputElement = document.getElementById(idInput);
    if (inputElement) {
        const inputValue = inputElement.value.trim();
        const feedbackElement = inputElement.parentElement.querySelector('.invalid-feedback');

        if (!inputValue) {
            inputElement.classList.add('is-invalid');
            inputElement.parentElement.classList.add('has-error');
            if (feedbackElement) {
                feedbackElement.textContent = inputElement.getAttribute('data-error');
                feedbackElement.style.display = 'block';
            }
            return false;
        } else {
            inputElement.classList.remove('is-invalid');
            inputElement.parentElement.classList.remove('has-error');
            if (feedbackElement) {
                feedbackElement.style.display = 'none';
            }
            return true;
        }
    } else {
        console.log(`Elemento con id ${idInput} no encontrado.`);
        return false;
    }
}

function estatutosAhorro() {
    var url = "https://grammermx.com/RH/CajaDeAhorro/docs/ESTATUTOS_CAJA_AHORRO_2025.pdf";
    window.open(url, '_blank');
}

function autorizarAhorros(){
    let mensaje = "Recuerda que el retiro de tu ahorro será total y sólo podrás iniciar un nuevo plan de ahorro hasta el próximo año. Además, si tienes un préstamo, se descontará del monto que has ahorrado."
    Swal.fire({
        title: 'Autorización requerida',
        text: mensaje,
        input: 'password',
        inputLabel: 'Ingresa tu TAG para confirmar la solicitud',
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

                const formData = new FormData(document.getElementById('formRetirarAhorro'));

                formData.append('password', password);

                // Enviar los datos al servidor
                fetch('dao/daoValidarTAG.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {

                            retirarAhorros();

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message // Mostrar el mensaje de error devuelto por el servidor
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al validar la contraseña. Intente nuevamente.'
                        });
                    });
            }
        }
    });
}

function retirarAhorros(){

    fetch('dao/daoRetirarAhorro.php', {
        method: 'POST'
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {

                Swal.fire({
                    icon: 'success',
                    title: 'Solicitud exitosa',
                    text: data.message,
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "https://grammermx.com/RH/CajaDeAhorro/misSolicitudes.php";
                    }
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message // Mostrar el mensaje de error devuelto por el servidor
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al validar la contraseña. Intente nuevamente.'
            });
        });

}