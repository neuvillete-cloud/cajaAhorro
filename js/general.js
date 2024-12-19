const formatearFecha = (fecha) => {
    if (fecha && fecha !== '0000-00-00' && typeof fecha === 'string') {
        // Crear la fecha en UTC
        let date = new Date(Date.UTC(...fecha.split('-').map((v, i) => i === 1 ? v - 1 : v)));
        let meses = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];
        let dia = date.getUTCDate(); // Usamos getUTCDate para evitar desfases de zona horaria
        let mes = meses[date.getUTCMonth()];
        let anio = date.getUTCFullYear();
        return `${dia}/${mes}/${anio}`;
    } else {
        return 'Sin registro'; // En caso de que la fecha no sea válida
    }
};
function formatearHora(hora) {
    if (!hora || typeof hora !== "string") {
        throw new Error("La hora proporcionada no es válida.");
    }

    // Dividir la hora en partes utilizando ':' como separador
    const partes = hora.split(":");

    // Verificar que haya al menos horas y minutos
    if (partes.length < 2) {
        throw new Error("La hora proporcionada no tiene el formato esperado 'HH:mm:ss'.");
    }

    // Devolver solo las primeras dos partes (HH:mm)
    return `${partes[0]}:${partes[1]}`;
}

// Función para convertir la fecha de Excel a formato 'YYYY-MM-DD'
function excelDateToJSDate(excelDate) {
    // Helper para formatear fechas en YYYY/MM/DD
    function formatDateToYMD(date) {
        return `${date.getUTCFullYear()}/${(date.getUTCMonth() + 1).toString().padStart(2, '0')}/${date.getUTCDate().toString().padStart(2, '0')}`;
    }

    // Si la entrada es null, undefined o una cadena vacía, retornar "0000-00-00"
    if (!excelDate) {
        return "0000-00-00";
    }

    // Verificar si es una fecha en formato numérico de Excel
    if (typeof excelDate === 'number') {
        const jsDate = new Date((excelDate - 25569) * 86400 * 1000);
        return formatDateToYMD(jsDate);
    }

    // Verificar si es una cadena
    else if (typeof excelDate === 'string') {
        // Intentar varios formatos conocidos
        const formats = [/^\d{2}\/\d{2}\/\d{4}$/, /^\d{4}-\d{2}-\d{2}$/, /^\d{2}-\d{2}-\d{4}$/];

        for (let format of formats) {
            if (format.test(excelDate)) {
                // Si el formato es DD/MM/YYYY
                if (format === formats[0]) {
                    const [day, month, year] = excelDate.split('/');
                    const parsedDate = new Date(Date.UTC(year, month - 1, day));
                    return formatDateToYMD(parsedDate);
                }
                // Si el formato es YYYY-MM-DD o DD-MM-YYYY
                else if (format === formats[1] || format === formats[2]) {
                    const parts = excelDate.split(/[-\/]/);
                    const year = parts[0].length === 4 ? parts[0] : parts[2];
                    const month = parts[1] - 1;
                    const day = parts[0].length === 4 ? parts[2] : parts[0];
                    const parsedDate = new Date(Date.UTC(year, month, day));
                    return formatDateToYMD(parsedDate);
                }
            }
        }
        return "Error: Formato de fecha no válido";
    }

    return "Error: Tipo de entrada no válido";
}


function formatearMonto(numero) {
    if (isNaN(numero)) {
        throw new Error("El valor proporcionado no es un número válido");
    }

    // Convertir el número a formato de pesos mexicanos
    return `$${numero.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`;
    //return `$${numero.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function actualizarTitulo(idTitulo, titulo) {
    let titulo5 = document.querySelector(idTitulo);
    if (titulo5) {
        titulo5.textContent = titulo;
    }
}


//Funcion que elimina signo "$" de la cantidad y valida que sea un numero válido
function validarMonto(montoAhorro) {
    // Elimina el signo de pesos si está al principio
    let valor = montoAhorro.trim();
    if (valor.startsWith('$')) {
        valor = valor.slice(1); // Elimina el primer carácter '$'
    }

    // Convierte el valor en número
    const numero = parseFloat(valor);

    // Verifica si el valor es un número válido
    if (isNaN(numero)) {
        return null;
    }else{
        return numero; // Retorna el número
    }
}


function fCargarPrestamo() {
    let sectionSolPrestamo = document.getElementById("section_1");

    if (sectionSolPrestamo.style.display === "block") {
        sectionSolPrestamo.style.display = "none"; // Oculta la sección si está visible
    } else {
        sectionSolPrestamo.style.display = "block"; // Muestra la sección si está oculta

        fExistePrestamo();
    }
}

function fExistePrestamo(){
    let data = "";

    $.getJSON('https://grammermx.com/RH/CajaDeAhorro/dao/daoCargarUltimoPrestamo.php', function (response) {

        data = response.data[0];

        let montoSolicitado = formatearMonto(data.montoSolicitado);

        $("#montoPrestamo").val(montoSolicitado);
        $('#telefono').val(data.telefono);

    });
}

function fCrearAhorro(){
    let sectionSolAhorro = document.getElementById("section_2");

    if(sectionSolAhorro.style.display === "block"){
        sectionSolAhorro.style.display = "none";
    }else{
        sectionSolAhorro.style.display = "block";

        fExisteAhorro();
    }
}

function fExisteAhorro(){

    let idCaja = "";

    $.getJSON('https://grammermx.com/RH/CajaDeAhorro/dao/daoCargarUltimoAhorro.php', function (response) {

        let data = response.data[0];
        let data2 = response.data[2];

        let montoSolicitado = formatearMonto(data.montoAhorro);
        idCaja = data.idCaja;

        //Caja de ahorro

        $("#montoAhorro").val(montoSolicitado);

        //Beneficiario 1
        $("#nombreBen1").val(data.nombre);
        $("#porcentajeBen1").val(data.porcentaje);
        $("#telefonoBen1").val(data.telefono);
        $("#domicilioBen1").val(data.direccion);

        if(data2) {
            let divBen2 = document.getElementById("divBeneficiario2");
            divBen2.style.display = "block";
            //Beneficiario 2
            $("#nombreBen2").val(data2.nombre);
            $("#porcentajeBen2").val(data2.porcentaje);
            $("#telefonoBen2").val(data2.telefono);
            $("#domicilioBen2").val(data2.direccion);
        }
    });
    //alert("idCaja:"+idCaja)
    if (idCaja !== ""){
        let btnSolAhorro = document.getElementById("btnSolAhorro");
        btnSolAhorro.style.display = "none";

        document.addEventListener("DOMContentLoaded", function() {
            const btnActualizar = document.getElementById('btnActAhorro');

            btnActualizar.addEventListener('click', function() {
                validarFormAhorro(true); // Llama a la función con el argumento `true`
            });
            }
        )
    }
}


function fSolicitarRetiro(){
    let sectionSolRetiro = document.getElementById("section_3");

    if(sectionSolRetiro.style.display === "block"){
        sectionSolRetiro.style.display = "none";
    }else{
        sectionSolRetiro.style.display = "block";
    }
}

function fCargarPreguntas(){
    let sectionPreguntas = document.getElementById("section_4");

    if (sectionPreguntas.style.display === "block"){
        sectionPreguntas.style.display = "none";
    }else{
        sectionPreguntas.style.display = "block";
    }
}


function validarTelefono(telefono) {
    // Expresión regular para validar un número de teléfono de 10 dígitos
    const regex = /^\d{10}$/; // Formato: 5551234567

    if (regex.test(telefono)) {
        return true; // Teléfono válido
    } else {
        return false; // Teléfono inválido
    }
}

/**********************************************************************************************************************/
/********************************************************TOOLTIPS******************************************************/
/**********************************************************************************************************************/

function mostrarImagenTooltip(idTooltip, message, imageUrl, width, height) {
    const tooltip = document.getElementById(idTooltip);

    // Configuración de Tippy.js
    tippy(tooltip, {
        trigger: 'click', // Mostrar el tooltip al hacer clic
        animation: 'shift-away',
        theme: 'light',
        arrow: true, // Mostrar flecha en el tooltip
        allowHTML: true, // Permitir contenido HTML dentro del tooltip
        onShow(instance) {
            // Crear una estructura HTML personalizada
            const container = document.createElement('div');
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.alignItems = 'center';
            container.style.padding = '10px';
            container.style.backgroundColor = '#fff';
            container.style.borderRadius = '8px';
            container.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
            container.style.width = `${width}px`;
            container.style.height = `${height}px`;

            // Crear el elemento de texto para el mensaje
            const text = document.createElement('p');
            text.textContent = message; // Mensaje dinámico
            text.style.margin = '0 0 10px'; // Margen inferior para separar del contenido siguiente
            text.style.fontSize = '10px';
            text.style.textAlign = 'center';
            text.style.color = '#4d5154';

            // Crear el elemento de imagen
            const image = new Image();
            image.src = imageUrl;
            image.style.width = '100%'; // Ajustar al ancho del contenedor
            image.style.height = '100%'; // Ajustar al alto del contenedor
            image.style.objectFit = 'contain'; // Escalar la imagen para que no se deforme
            image.style.borderRadius = '5px';

            // Agregar la imagen al contenedor
            container.appendChild(image);
            container.appendChild(text);

            // Asignar el contenedor al contenido del tooltip
            instance.setContent(container);
        },
    });
}

