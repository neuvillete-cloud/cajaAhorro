/***********************************************************************************************************************
 *********************************************CONFIGURAR FECHAS*********************************************************
 * *********************************************************************************************************************/

document.getElementById('guardarFechas').addEventListener('click', async function() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaCierre = document.getElementById('fechaCierre').value;
    const horaInicio = document.getElementById('horaInicio').value;
    const horaCierre = document.getElementById('horaCierre').value;

    if (!fechaInicio || !fechaCierre) {
        Swal.fire({
            icon: 'error',
            title: 'Por favor, complete ambas fechas.'
        });
        return;
    }

    // Obtener los años de las fechas
    const anioInicio = new Date(fechaInicio).getFullYear();
    const anioCierre = new Date(fechaCierre).getFullYear();
    const anioActual = new Date().getFullYear();


    if (anioInicio !== anioCierre) {
        Swal.fire({
            icon: 'error',
            title: 'Ambas fechas deben pertenecer al mismo año.'
        });
        return;
    }
    // Validar que el año de las fechas sea el actual
    if (anioInicio !== anioActual) {
        Swal.fire({
            icon: 'error',
            title: `Las fechas deben corresponder al año actual (${anioActual}).`
        });
        return;
    }

    // Validar que la fecha de inicio sea anterior a la fecha de cierre
    if (new Date(fechaInicio) >= new Date(fechaCierre)) {
        Swal.fire({
            icon: 'error',
            title: 'La fecha de inicio debe ser anterior a la fecha de cierre.'
        });
        return;
    }

    try {
        const url = 'dao/daoGuardarFechas.php'; // URL del archivo PHP que manejará la solicitud

        // Crear el cuerpo de la solicitud en formato x-www-form-urlencoded
        const data = new URLSearchParams();
        data.append('fechaInicio', fechaInicio);
        data.append('fechaCierre', fechaCierre);
        data.append('horaInicio', horaInicio);
        data.append('horaCierre', horaCierre);
        data.append('anio', anioActual);

        // Realizar la solicitud con fetch
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded', // Especificamos el tipo de contenido
            },
            body: data.toString() // Convertimos los parámetros a string
        });

        // Obtener la respuesta en formato JSON
        const result = await response.json();

        // Manejar la respuesta
        if (result.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: result.message || 'Fechas guardadas exitosamente'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: result.message || 'Ocurrió un error al guardar las fechas.'
            });
        }
    } catch (error) {
        console.error('Error al enviar las fechas:', error);
        Swal.fire({
            title: "Error",
            text: error.message,
            icon: "error",
            confirmButtonText: "OK"
        });
    }
});


const consultarFechas = async () => {
    try {
        const response = await fetch(`https://grammermx.com/RH/CajaDeAhorro/dao/daoConsultarFechaInicio.php`);

        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status} ${response.statusText}`);
        }

        // Parsear la respuesta JSON
        const result = await response.json();

        // Verificar que 'data' tenga elementos
        if (result.data && result.data.length > 0) {
            const fechaInicio = (result.data[0].fechaInicio);  // Acceder a la primera fecha
            const fechaCierre = (result.data[0].fechaFin);     // Acceder a la fecha de cierre
            const horaInicio = (result.data[0].horaInicio);
            const horaCierre = (result.data[0].horaFin);

            // Asignar los valores al formulario
            $("#fechaInicio").val(fechaInicio);
            $("#fechaCierre").val(fechaCierre);
            $("#horaInicio").val(horaInicio);
            $("#horaCierre").val(horaCierre);
        }
    } catch (error) {
        console.error('Error:', error);
    }
};



/***********************************************************************************************************************
 *********************************************SOLICITUDES DE PRESTAMOS *************************************************
 * *********************************************************************************************************************/

document.getElementById("ejemploExcelP").addEventListener("click", function() {
    mostrarImagenTooltip(
        "ejemploExcelP",
        "Nota: El excel exportado puede usarse para actualizar. " +
                "\n Todas las solicitudes contenidas en el archivo serán actualizadas.",
        "https://grammermx.com/RH/CajaDeAhorro/images/others/ejemploExcel.png",
        320,
        200
    );
});

// DataTables
let dataTableAdminPrestamos;
let dataTableInitPrestamosAdmin = false;
let datosPrestamosAdmin;
let anioPrestamos;

const dataTableOptPresAdmin = {
    lengthMenu: [5, 15, 25, 50, 100],
    columnDefs:[
        {className: "centered", targets: [0,1,2,3,4,5,6]},
        {orderable: true, targets: [0,1,2,3,5]},
        {width: "8%", targets: [0]},
        {searchable: true, targets: [0,1,2,3] }
    ],
    pageLength:5,
    destroy: true,
    order: [[0, 'desc']], // Ordenar por la columna 0
    language:{
        lengthMenu: "Mostrar _MENU_ registros pór página",
        sZeroRecords: "Ninguna solicitud encontrada",
        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Ninguna solicitud encontrada",
        infoFiltered: "(filtrados desde _MAX_ registros totales)",
        search: "Buscar: ",
        loadingRecords: "Cargando...",
        paginate:{
            first:"Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        }
    }
};

const initDataTablePresAdmin = async (anio) => {
    if (dataTableInitPrestamosAdmin) {
        dataTableAdminPrestamos.destroy();
    }

    await dataTablePrestamosAdmin(anio);

    dataTableAdminPrestamos = $("#tablaPrestamosAdmin").DataTable(dataTableOptPresAdmin);

    dataTableInitPrestamosAdmin = true;
};


const dataTablePrestamosAdmin = async (anio) => {
    try {
        const response = await fetch(`https://grammermx.com/RH/CajaDeAhorro/dao/daoSolicitudesPrestamos.php?anio=` + anio);

        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status} ${response.statusText}`);
        }

        const result = await response.json();

        let content = '';
        result.data.forEach((item) => {
            const fechaSolicitudFormateada = formatearFecha(item.fechaSolicitud);
            const montoSolFormateado = formatearMonto(item.montoSolicitado);

            content += `
                <tr>
                    <td>${item.idSolicitud}</td>
                    <td>${fechaSolicitudFormateada}</td>
                    <td>${item.nominaSolicitante}</td>
                    <td>${montoSolFormateado}</td>
                    <td>${item.telefono}</td>
                    <td>${item.estatusVisual}</td>
                    <td>
                        <button class="btn btn-primary" onclick="responderPrestamo(${item.idSolicitud}, ${item.anioConvocatoria})" data-bs-toggle="modal" data-bs-target="#modalRespPrestamo">
                            <span>Responder</span>
                        </button>`;

            // Agrega el botón de avales si el estatus es 3
            if (item.idEstatus === '3') {
                content += `
                    <button class="btn btn-secondary" onclick="consultarAvales(${item.idSolicitud}, ${item.anioConvocatoria})" data-bs-toggle="modal" data-bs-target="#modalAgregarAvales">
                        <span>Avales</span>
                    </button>`;
            }

            content += `
                    </td>
                </tr>`;
        });
        bodyPrestamosAdmin.innerHTML = content;

        // Preparar datos para convertir a Excel
        datosPrestamosAdmin = result.data;  // Pasa los datos directamente a la función
        anioPrestamos = anio;

        $('#tablaPrestamosAdmin thead th').css('background-color', '#005195')
        $('#tablaPrestamosAdmin thead th').css('color', '#ffffff')
        $('#tablaPrestamosAdmin').find('tbody td, thead th').css('text-align', 'center');

    } catch (error) {
        console.error('Error:', error);
    }
};


function responderPrestamo(idSolicitud, anio){
    const titulo = "Responder Solicitud de Préstamo Folio " + idSolicitud;
    actualizarTitulo('#respModalTit', titulo);
    let data = "";

    const url = `https://grammermx.com/RH/CajaDeAhorro/dao/daoSolicitudPrestamoPorId.php?sol=${idSolicitud}&a=${anio}`;

    $.getJSON(url, function (response) {

        data = response.data[0];

        let fechaSolicitudFormateada = formatearFecha(data.fechaSolicitud);
        let montoForSol = formatearMonto(data.montoSolicitado);
        let montoForAut = formatearMonto(data.montoAprobado);

        $("#folioSolicitud").val(data.idSolicitud);

        $("#fechaSolicitud").val(fechaSolicitudFormateada);

        $("#montoSolicitado").val(montoForSol);

        $("#nominaSol").val(data.nominaSolicitante);

        $('#telefonoSol').val(data.telefono);

        $("#textareaComentarios").val(data.comentariosAdmin);

        $("#inMontoAprobado").val(montoForAut);

        /*alert(
            "Folio Solicitud: " + $('#folioSolicitud').val() + "\n" +
            "Fecha Solicitud: " + $('#fechaSolicitud').val() + "\n" +
            "Monto Solicitado: " + $('#montoSolicitado').val() + "\n" +
            "Nómina: " + $('#nominaSol').val() + "\n" +
            "Teléfono: " +data.telefono + "\n" +
            "Comentarios Admin: " + $('#textareaComentarios').val() + "\n" +
            "Monto Aprobado: " + montoForAut + "\n" + data.montoAprobado
        );*/
    }).then(function(){
        fCargarSolicitante(data.nominaSolicitante);
    }).then(function(){
        fCargarEstatus(data.idEstatus);
    }).then(function(){
        deshabilitarInputs();
    });
}

function deshabilitarInputs() {
    document.getElementById('telefonoSol').disabled = true;
    document.getElementById('folioSolicitud').disabled = true;
    document.getElementById('fechaSolicitud').disabled = true;
    document.getElementById('montoSolicitado').disabled = true;
    document.getElementById('nominaSol').disabled = true;
    document.getElementById('nombreSol').disabled = true;
}

function fCargarSolicitante(nomina){

    $.getJSON('https://grammermx.com/RH/CajaDeAhorro/dao/daoConsultarSolicitante.php?sol='+nomina, function (response) {
        $('#nombreSol').val(response.data[0].NomUser);
    });
}

function fCargarEstatus(idSeleccionado){
    $.getJSON('https://grammermx.com/RH/CajaDeAhorro/dao/daoEstatusSol.php', function (data){
        let selectS = document.getElementById("solEstatus");
        selectS.innerHTML = ""; //limpiar contenido

        for (var j = 0; j < data.data.length; j++) {
            var createOption = document.createElement("option");
            createOption.value = data.data[j].idEstatus;
            createOption.text = data.data[j].descripcion;
            selectS.appendChild(createOption);
            // Si el valor actual coincide con idSeleccionado, se selecciona por defecto
            if (data.data[j].idEstatus === idSeleccionado) {
                createOption.selected = true;
            }
        }
    });
}

function actualizarSolicitud() {
    let idsolicitud = document.getElementById("folioSolicitud").value;
    let montoAprobado = document.getElementById("inMontoAprobado").value;
    let estatus = document.getElementById("solEstatus").value;
    let comentarios = document.getElementById("textareaComentarios").value;

    // Crear objeto FormData
    let data = new FormData();

    // Agregar los datos al FormData
    data.append("idsolicitud", idsolicitud.trim());
    data.append("montoAprobado", montoAprobado.trim());
    data.append("estatus", estatus.trim());
    data.append("comentarios", comentarios.trim());

    // Llamada a fetch sin paréntesis adicionales
    fetch("https://grammermx.com/RH/CajaDeAhorro/dao/daoActualizarSolPresAdmin.php", {
        method: 'POST',
        body: data
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Actualización exitosa',
                    text: data.message // Mostrar el mensaje devuelto por el servidor
                }).then(() => {
                    initDataTablePresAdmin(anioActual);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message // Mostrar el mensaje devuelto por el servidor
                });
            }
        }).catch(error => {
        console.error('Error:', error);
    });
}

/******************Descargar Excel Prestamos*******************/

document.getElementById('btnExcelPrestamos').addEventListener('click', () => {
    prepararExcelPrestamos(datosPrestamosAdmin);

});
async function prepararExcelPrestamos(data) {
    // Filtra y renombra las columnas de los datos
    const datosFiltrados = data.map(item => ({
        Id_Solicitud: item.idSolicitud,
        Anio: item.anioConvocatoria,
        Nomina_Solicitante: item.nominaSolicitante,
        Nombre_Solicitante: item.NomUser,
        Fecha_Solicitud: item.fechaSolicitud,
        Monto_Solicitado: item.montoSolicitado,
        Telefono: item.telefono,
        Estatus: item.idEstatus,
        Nomina_Aval1: item.nominaAval1,
        Tel_Aval1: item.telAval1,
        Nomina_Aval2: item.nominaAval2,
        Tel_Aval2: item.telAval2,
        Fecha_Respuesta: item.fechaRespuesta,
        Monto_Aprobado: item.montoAprobado,
        Fecha_Deposito: item.fechaDeposito,
        Monto_Depositado: item.montoDeposito,
        Comentarios: item.comentariosAdmin
    }));

    // Convierte el JSON filtrado y renombrado en una hoja de Excel
    const worksheet = XLSX.utils.json_to_sheet(datosFiltrados);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Solicitudes de Prestamos");

    // Guarda el archivo Excel en un Blob (Archivo temporal en memoria)
    const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const datosPrestamosAdmin = new Blob([excelBuffer], { type: "application/octet-stream" });

    // Crear enlace de descarga y disparar clic
    const url = URL.createObjectURL(datosPrestamosAdmin);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Solicitudes_Prestamos_'+anioPrestamos+'.xlsx';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    // Liberar el objeto URL
    URL.revokeObjectURL(url);
}


/******************Cargar e insertar datos de Excel*******************/
document.getElementById('btnInsertarPrestamosExcel').addEventListener('click', () => {
    document.getElementById('fileInputPrestamos').click();
});

document.getElementById('fileInputPrestamos').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        insertarExcelPrestamos(file);
    }
});
async function insertarExcelPrestamos(file) {
    try {
        // Leer el archivo Excel
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        // Mapear los datos, asegurándonos de convertir las fechas correctamente
        const prestamosData = jsonData.slice(1).map((row) => {
            return {
                idSolicitud: row[0],
                anioConvocatoria: row[1],
                nominaSolicitante: row[2],
                NomUser: row[3],
                fechaSolicitud: excelDateToJSDate(row[4]),
                montoSolicitado: row[5],
                telefono: row[6],
                idEstatus: row[7],
                nominaAval1: row[8],
                telAval1: row[9],
                nominaAval2: row[10],
                telAval2: row[11],
                fechaRespuesta: excelDateToJSDate(row[12]),
                montoAprobado: row[13],
                fechaDeposito: excelDateToJSDate(row[14]),
                montoDeposito: row[15],
                comentariosAdmin: row[16]

            };
        });

        // Enviar los datos al backend
        const response = await fetch('https://grammermx.com/RH/CajaDeAhorro/dao/daoActualizarPrestamosExcel.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ prestamos: prestamosData })
        });

        // Obtener la respuesta del backend
        const result = await response.json();

        if (result.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Actualización exitosa',
                text: result.message
            });

            initDataTablePresAdmin(anioActual);
        } else {
            // Mostrar el mensaje de error que viene del backend
            throw new Error(result.message + " \nDetalles: " + result.detalles );
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Ocurrió un error al procesar el archivo. Recargue la página e intente nuevamente.'
        });
    }
}

/***********************************************************************************************************************
 *********************************************SOLICITUDES DE AHORRO ****************************************************
 * *********************************************************************************************************************/

// DataTables
let dataTableAdminAhorro;
let dataTableInitAhorroAdmin = false;
let datosSolicitudesAhorro;
let anioCajaAhorro;

const dataTableOptAhorroAdmin = {
    lengthMenu: [5, 15, 25, 50, 100],
    columnDefs:[
        {className: "centered", targets: [0,1,2,3]},
        {orderable: false, targets: [0,1,2,3]},
        {width: "8%", targets: [0]},
        {searchable: true, targets: [0,1,2] }
    ],
    pageLength:5,
    destroy: true,
    order: [[0, 'desc']], // Ordenar por la columna 0
    language:{
        lengthMenu: "Mostrar _MENU_ registros pór página",
        sZeroRecords: "Ninguna solicitud encontrada",
        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Ninguna solicitud encontrada",
        infoFiltered: "(filtrados desde _MAX_ registros totales)",
        search: "Buscar: ",
        loadingRecords: "Cargando...",
        paginate:{
            first:"Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        }
    }
};
const initDataTableAhorroAdmin = async (anio) => {
    if (dataTableInitAhorroAdmin) {
        dataTableAdminAhorro.destroy();
    }
    await dataTableAhorroAdmin(anio);

    dataTableAdminAhorro = $("#tablaAhorroAdmin").DataTable(dataTableOptAhorroAdmin);

    dataTableInitAhorroAdmin = true;
};


const dataTableAhorroAdmin = async (anio) => {
    try {
        const response = await fetch(`https://grammermx.com/RH/CajaDeAhorro/dao/daoSolicitudesAhorro.php?anio=` + anio);

        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status} ${response.statusText}`);
        }

        const result = await response.json();
        let content = '';
        result.data.forEach((item) => {
            const fechaSolicitudFormateada = formatearFecha(item.fechaSolicitud);
            const montoSolFormateado = formatearMonto(item.montoAhorro);

            content += `
                <tr>
                    <td>${item.idCaja}</td>
                    <td>${fechaSolicitudFormateada}</td>
                    <td>${item.nomina}</td>
                    <td>${montoSolFormateado}</td>
                    <td>
                        <button class="btn btn-primary" onclick="consultarAhorro('${item.idCaja}')" 
                                data-bs-toggle="modal" data-bs-target="#modalConsultaAhorro">
                            <i class="fas fa-info-circle"></i> <span>Detalles</span>
                        </button>
                    </td>
                </tr>`;
        });
        bodyAhorroAdmin.innerHTML = content;


        datosSolicitudesAhorro = result.data;
        anioCajaAhorro = anio

        $('#tablaAhorroAdmin thead th').css('background-color', '#005195')
        $('#tablaAhorroAdmin thead th').css('color', '#ffffff')
        $('#tablaAhorroAdmin').find('tbody td, thead th').css('text-align', 'center');

    } catch (error) {
        console.error('Error:', error);
    }
};

document.getElementById('btnExcelAhorro').addEventListener('click', () => {
    prepararExcelAhorro(datosSolicitudesAhorro);

});


async function prepararExcelAhorro(data) {
    // Filtra y renombra las columnas de los datos
    const datosFiltrados = data.map(item => ({
        ID_Caja: item.idCaja,
        Nomina_Solicitante: item.nomina,
        Nombre: item.NomUser,
        Monto_Ahorro: item.montoAhorro,
        Fecha_Solicitud: item.fechaSolicitud
    }));

    // Convierte el JSON filtrado y renombrado en una hoja de Excel
    const worksheet = XLSX.utils.json_to_sheet(datosFiltrados);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Solicitudes de Retiros");

    // Guarda el archivo Excel en un Blob (Archivo temporal en memoria)
    const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const datosRetirosAdmin = new Blob([excelBuffer], { type: "application/octet-stream" });

    // Crear enlace de descarga y disparar clic
    const url = URL.createObjectURL(datosRetirosAdmin);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Caja_de_Ahorro_'+anioCajaAhorro+'.xlsx';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    // Liberar el objeto URL
    URL.revokeObjectURL(url);
}


/***********************************************************************************************************************
 *********************************************RETIROS DE AHORRO ********************************************************
 * *********************************************************************************************************************/

// DataTables
let dataTableAdminRetiro;
let dataTableInitRetiroAdmin = false;
let datosRetirosAhorro;
let anioRetiroAhorro;

const dataTableOptRetiroAdmin = {
    lengthMenu: [5, 15, 25, 50, 100],
    columnDefs:[
        {className: "centered", targets: [0,1,2,3,4,5]},
        {orderable: false, targets: [0,1,2,3,4,5]},
        {width: "8%", targets: [0]},
        {searchable: true, targets: [0,1,2,3,4,5] }
    ],
    pageLength:5,
    destroy: true,
    order: [[0, 'desc']], // Ordenar por la columna 0
    language:{
        lengthMenu: "Mostrar _MENU_ registros pór página",
        sZeroRecords: "Ninguna solicitud encontrada",
        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Ninguna solicitud encontrada",
        infoFiltered: "(filtrados desde _MAX_ registros totales)",
        search: "Buscar: ",
        loadingRecords: "Cargando...",
        paginate:{
            first:"Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        }
    }
};
const initDataTableRetiroAdmin = async (anio) => {
    if (dataTableInitRetiroAdmin) {
        dataTableAdminRetiro.destroy();
    }
    await dataTableRetiroAdmin(anio);

    dataTableAdminRetiro = $("#tablaRetirosAdmin").DataTable(dataTableOptRetiroAdmin);

    dataTableInitRetiroAdmin = true;
};


const dataTableRetiroAdmin = async (anio) => {
    try {
        const response = await fetch(`https://grammermx.com/RH/CajaDeAhorro/dao/daoSolicitudesRetiro.php?anio=` + anio);

        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status} ${response.statusText}`);
        }

        const result = await response.json();
        let content = '';
        result.data.forEach((item) => {
            const fechaSolicitudFormateada = formatearFecha(item.fechaSolicitud);
            const fechaDepositoFormateada = formatearFecha(item.fechaDeposito);
            const montoDepFormateado = formatearMonto(item.montoDepositado);

            content += `
                <tr>
                    <td>${item.idRetiro}</td>
                    <td>${fechaSolicitudFormateada}</td>
                    <td>${item.idCaja}</td>
                    <td>${item.nomina}</td>
                    <td>${fechaDepositoFormateada}</td>
                    <td>${montoDepFormateado}</t>
                </tr>`;
        });
        bodyRetirosAdmin.innerHTML = content;

        datosRetirosAhorro = result.data;
        anioRetiroAhorro = anio;

        $('#tablaRetirosAdmin thead th').css('background-color', '#005195')
        $('#tablaRetirosAdmin thead th').css('color', '#ffffff')
        $('#tablaRetirosAdmin').find('tbody td, thead th').css('text-align', 'center');

    } catch (error) {
        console.error('Error:', error);
    }
};


async function prepararExcelRetiros(data) {
    // Filtra y renombra las columnas de los datos
    const datosFiltrados = data.map(item => ({
        ID_Retiro: item.idRetiro,
        id_Caja: item.idCaja,
        Fecha_Solicitud: item.fechaSolicitud,
        Nomina_Solicitante: item.nomina,
        Nombre: item.NomUser,
        Fecha_Deposito: item.fechaDeposito,
        Monto_Depositado: item.montoDepositado
    }));

    // Convierte el JSON filtrado y renombrado en una hoja de Excel
    const worksheet = XLSX.utils.json_to_sheet(datosFiltrados);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Retiros de Caja de Ahorro");

    // Guarda el archivo Excel en un Blob (Archivo temporal en memoria)
    const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
    const datosRetirosAdmin = new Blob([excelBuffer], { type: "application/octet-stream" });

    // Crear enlace de descarga y disparar clic
    const url = URL.createObjectURL(datosRetirosAdmin);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Retiros_de_Ahorro_'+anioRetiroAhorro+'.xlsx';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    // Liberar el objeto URL
    URL.revokeObjectURL(url);
}

document.getElementById('btnRetirosExcel').addEventListener('click', () => {
    prepararExcelRetiros(datosRetirosAhorro);
});

/******************Cargar e insertar datos de Excel*******************/
document.getElementById('btnInsertarRetirosExcel').addEventListener('click', () => {
    document.getElementById('fileInputRetiros').click();
});

document.getElementById('fileInputRetiros').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file) {
        insertarExcelRetiros(file);
    }
});

async function insertarExcelRetiros(file) {
    try {
        // Leer el archivo Excel
        const data = await file.arrayBuffer();
        const workbook = XLSX.read(data, { type: 'array' });
        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
        const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        // Extraer y mapear los datos de las columnas
        const retirosData = jsonData.slice(1).map((row) => {
            return {
                idRetiro: row[0],
                montoDepositado: row[1],
                fechaDeposito: excelDateToJSDate(row[2])// Convertir la fecha
            };
        });

        // Enviar los datos al backend en un solo array
        const response = await fetch('https://grammermx.com/RH/CajaDeAhorro/dao/daoActualizarRetirosExcel.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ retiros: retirosData }) // Enviar como un array bajo la clave "retiros"
        });

        // Obtener la respuesta del backend
        const result = await response.json();

        if (result.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Actualización exitosa',
                text: result.message
            });

            initDataTableRetiroAdmin(anioActual);
        } else {
            // Mostrar el mensaje de error que viene del backend
            throw new Error(result.message );
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar el archivo. Recargue la página e intente nuevamente.'
        });
    }
}

/***********************************************************************************************************************
 *****************************************CARGAR SOLICITUDES POR AÑO****************************************************
 * *********************************************************************************************************************/


function cargarAnio() {
    $.getJSON('https://grammermx.com/RH/CajaDeAhorro/dao/daoAnio.php', function (data) {
        let selectS = document.getElementById("selectAnio");
        selectS.innerHTML = ""; //limpiar contenido

        let createOptionDef = document.createElement("option");
        createOptionDef.text = "Seleccione el año*";
        createOptionDef.value = "";
        selectS.appendChild(createOptionDef);

        for (var i = 0; i < data.data.length; i++) {
            var createOption = document.createElement("option");
            createOption.value = data.data[i].anio;
            createOption.text = data.data[i].anio;
            selectS.appendChild(createOption);
        }
    });
}

async function cargarSolicitudes() {
    const seccionPrestamo = document.getElementById("solicitarPrestamoSeccion");
    const seccionAhorro = document.getElementById("solicitarAhorroSeccion");
    let tipoConsulta = document.getElementById("selectTipoConsulta").value;
    let anio = document.getElementById("selectAnio").value;

    seccionPrestamo.style.display = "none";
    seccionAhorro.style.display = "none";

    if (tipoConsulta === "1") { // Préstamos
        seccionPrestamo.style.display = "block";
        await initDataTablePresAdmin(anio);

    } else if (tipoConsulta === "2") { // Caja de Ahorro
        seccionAhorro.style.display = "block";
        await initDataTableAhorroAdmin(anio);
    }
}
