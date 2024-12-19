<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/icons/Grammer_Logo.ico" type="image/x-icon">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Administrar</title>

    <!-- Tippy.js core styles -->
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">

    <!-- CSS FILES -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css" rel="stylesheet">

    <link href="css/styles.css" rel="stylesheet">

    <link href="css/misSolicitudes.css" rel="stylesheet">

    <link href="css/admin.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&display=swap" rel="stylesheet">

    <?php
    session_start();
    $nombreUser = $_SESSION['nombreUsuario'];
    $esAdmin = $_SESSION['admin'];

    if ($nombreUser == null || $esAdmin == 0){
        header("Location: https://grammermx.com/RH/CajaDeAhorro/login.php");
    }

    ?>
</head>

<body>
<main>

    <nav class="navbar navbar-expand-lg bg-light shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/icons/croc_logo.png" class="logo img-fluid" alt="Logo CROC">
                <img src="images/icons/GrammerAzul.png" class="logo img-fluid" alt="Logo Grammer">
                <span id="tittleAdmin">Caja de Ahorro</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link click-scroll" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link click-scroll" href="#adminPrestamosSeccion">Préstamos</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link click-scroll" href="#adminAhorroSeccion">Caja de Ahorro</a>
                    </li>

                    <li class="nav-item">
                        <!-- Botón para abrir el modal -->
                        <a class="nav-link click-scroll" href="#" data-bs-toggle="modal" data-bs-target="#fechasModal" onclick="consultarFechas()">Fechas</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link click-scroll" href="#adminFiltrarSolicitudes">Filtrar Solicitudes</a>
                    </li>

                    <li class="nav-item ms-3">
                        <form id="logoutForm" action="dao/daoLogin.php" method="POST" style="display: none;">
                            <input type="hidden" name="cerrarSesion" value="true">
                        </form>
                        <a class="nav-link" id="cerrarSesion" href="#">Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <section class="tabla-section" id="adminPrestamosSeccion">
        <div class=""></div>
        <div class="container">
            <div class="row">
                <div class="container mt-5">
                    <h2 class="text-center">Solicitudes de Préstamos</h2>
                    <button class="btn btn-success text-right btnExcel" id="btnExcelPrestamos">Exportar a Excel</button>
                    <button class="btn btn-secondary text-right btnExcel" id="btnInsertarPrestamosExcel"> Cargar Archivo</button>
                    <button class="circular-btn" id="ejemploExcelP"><span>?</span></button>
                    <input type="file" id="fileInputPrestamos" accept=".xlsx, .xls" style="display: none;" />
                    <table class="table table-striped table-bordered mt-3" id="tablaPrestamosAdmin">
                        <thead>
                        <tr>
                            <th>FOLIO</th>
                            <th>FECHA SOLICITUD</th>
                            <th>NÓMINA</th>
                            <th>MONTO</th>
                            <th>TELÉFONO</th>
                            <th>ESTATUS</th>
                            <th>ACCIONES</th>
                        </tr>
                        </thead>
                        <tbody id="bodyPrestamosAdmin"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="tabla-section" id="adminAhorroSeccion">
        <div class="section-overlay"></div>
        <div class="container">
            <div class="row">
                <div class="container mt-5" id="divAhorroAdmin">
                    <h2 class="text-center">Solicitudes de Caja de Ahorro</h2>
                    <h3 class="text-center"><br>Iniciar ahorro</br></h3>
                    <button class="btn btn-success text-right btnExcel" id="btnExcelAhorro">Exportar a Excel</button>
                    <table class="table table-striped table-bordered mt-3" id="tablaAhorroAdmin">
                        <thead>
                        <tr>
                            <th>FOLIO</th>
                            <th>FECHA SOLICITUD</th>
                            <th>NÓMINA</th>
                            <th>MONTO</th>
                            <th>ACCIONES</th>
                        </tr>
                        </thead>
                        <tbody id="bodyAhorroAdmin"></tbody>
                    </table>

                    <h3 class="text-center"><br>Retiros</br></h3>
                    <button class="btn btn-success text-right btnExcel" id="btnRetirosExcel"> Exportar a Excel</button>
                    <button class="btn btn-secondary text-right btnExcel" id="btnInsertarRetirosExcel"> Cargar Archivo</button>
                    <input type="file" id="fileInputRetiros" accept=".xlsx, .xls" style="display: none;" />
                    <table class="table table-striped table-bordered mt-3" id="tablaRetirosAdmin">
                        <thead>
                        <tr>
                            <th>FOLIO</th>
                            <th>FECHA SOLICITUD</th>
                            <th>ID CAJA</th>
                            <th>NÓMINA</th>
                            <th>FECHA DE DEPÓSITO</th>
                            <th>MONTO DEPOSITADO</th>
                        </tr>
                        </thead>
                        <tbody id="bodyRetirosAdmin"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding" id="adminFiltrarSolicitudes">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-12 text-center mx-auto">
                    <h2 class="mb-5">Filtrar solicitudes</h2>
                </div>
                <div class="col-lg-6 col-md-6 col-12 mb-4 mb-lg-0 text-center mx-auto">
                    <div id="divForm" class="featured-block d-flex justify-content-center align-items-center p-4 border rounded">
                        <form class="w-100">
                            <div class="form-group mb-3">
                                <label for="selectTipoConsulta" class="form-label">Tipo de consulta</label>
                                <select id="selectTipoConsulta" name="selectTipoConsulta" class="form-control" onchange="cargarAnio()" required data-error="Por favor seleccione un tipo de consulta.">
                                    <option value="">Seleccione el tipo de consulta*</option>
                                    <option value="1">Préstamos</option>
                                    <option value="2">Caja de ahorro</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="selectAnio" class="form-label">Año</label>
                                <select id="selectAnio" name="selectAnio" class="form-control" required data-error="Por favor seleccione un año válido.">
                                    <option value="">Seleccione el año*</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3" onclick="cargarSolicitudes()">Ver solicitudes</button>
                            <input type="file" id="fileInputRetiros" accept=".xlsx, .xls" style="display: none;" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Modal para responder solicitud -->
    <div class="modal fade" id="modalRespPrestamo" tabindex="-1" aria-labelledby="responderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="respModalTit">Responder Solicitud de Préstamo Folio <span id="numSolPres"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <td>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="folioSolicitud" placeholder="Folio de solicitud" disabled>
                                    <label for="folioSolicitud">Folio de solicitud</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="fechaSolicitud" placeholder="Fecha solicitud" disabled>
                                    <label for="fechaSolicitud">Fecha solicitud</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="montoSolicitado" placeholder="Monto solicitado" disabled>
                                    <label for="montoSolicitado">Monto solicitado</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="nominaSol" placeholder="Nómina" disabled>
                                    <label for="nominaSol">Nómina</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="nombreSol" placeholder="Nombre" disabled>
                                    <label for="nombreSol">Nombre</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="telefonoSol" placeholder="Teléfono" disabled>
                                    <label for="telefonoSol">Teléfono</label>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <form>
                        <div class="container col-md-12">
                            <div class="row">
                                <div class="col-sm-5 col-md-6 mb-3">
                                    <label for="inMontoAprobado" class="form-label">Monto Aprobado</label>
                                    <input type="text" class="form-control" id="inMontoAprobado" placeholder="$5,000">
                                </div>
                                <div class="col-sm-5 col-md-6 mb-3">
                                    <label for="solEstatus" class="form-label">Estatus del préstamo</label>
                                    <select class="form-control" id="solEstatus">
                                        <option value="">Seleccione un estatus*</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="textareaComentarios" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="textareaComentarios" rows="3" placeholder="Escribe tus observaciones aquí..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="actualizarSolicitud()">Enviar Respuesta</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Fechas-->
    <div class="modal fade" id="fechasModal" tabindex="-1" aria-labelledby="fechasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fechasModalLabel">Configurar Fechas y Horas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="fechasForm">
                        <!-- Fecha de Inicio -->
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" required>
                        </div>
                        <!-- Hora de Inicio -->
                        <div class="mb-3">
                            <label for="horaInicio" class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control" id="horaInicio" name="horaInicio" required>
                        </div>
                        <!-- Fecha de Cierre -->
                        <div class="mb-3">
                            <label for="fechaCierre" class="form-label">Fecha de Cierre</label>
                            <input type="date" class="form-control" id="fechaCierre" name="fechaCierre" required>
                        </div>
                        <!-- Hora de Cierre -->
                        <div class="mb-3">
                            <label for="horaCierre" class="form-label">Hora de Cierre</label>
                            <input type="time" class="form-control" id="horaCierre" name="horaCierre" required>
                        </div>
                    </form>
                    <p class="text-muted mt-2"><br>
                        <strong>Nota:</strong> La fecha y hora de inicio indican el momento a partir del cual los solicitantes podrán comenzar a realizar solicitudes de préstamos. La fecha y hora de cierre marcan el último momento en el que podrán enviar dichas solicitudes.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="guardarFechas">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('modalAvales.php'); ?>
    <?php require_once ("modalAhorro.php"); ?>

</main>

<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-12 mb-4">
                <img src="images/icons/GrammerBlanco.png" class="logo img-fluid" alt="Logo Grammer">
                <img src="images/icons/CROCblanco.png" class="logo img-fluid m-gl-3" alt="Logo CROC">
            </div>

            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <h5 class="site-footer-title mb-3">Enlaces rápidos</h5>

                <ul class="footer-menu">
                    <li class="footer-menu-item"><a href="index.php" class="footer-menu-link">Inicio</a></li>

                    <li class="footer-menu-item"><a href="index.php#section_1" class="footer-menu-link">Préstamo</a></li>

                    <li class="footer-menu-item"><a class="footer-menu-link" href="index.php#section_2">Caja de Ahorro</a></li>

                    <li class="footer-menu-item"><a class="footer-menu-link" href="index.php#section_4">Preguntas Frecuentes</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6 col-12 mx-auto">
                <h5 class="site-footer-title mb-3">Información de contacto</h5>

                <p class="text-white d-flex mb-2">
                    <i class="bi-telephone me-2"></i>

                    <a href="tel: 442-475-2898" class="site-footer-link">
                        442 475 2898
                    </a>
                </p>

                <p class="text-white d-flex">
                    <i class="bi-envelope me-2"></i>
                        Lic. Irma Yomara Soto Cabello<br>
                        Coordinadora Nóminas<br>
                        yomara.soto@grammer.com<br>
                </p>

                <p class="text-white d-flex">
                    <i class="bi-envelope me-2"></i>
                        Lic. Juan Roberto Arreola Hernandez<br>
                        Administrador y analista de nómina<br>
                        juanroberto.arreola@grammer.com
                </p>

                <p class="text-white d-flex mt-3">
                    <i class="bi-geo-alt me-2"></i>
                    Av. de la Luz 24, Benito Juárez, 76120<br>
                    Santiago de Querétaro, Qro.
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- -Archivos de jQuery-->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- JAVASCRIPT FILES -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.sticky.js"></script>
<script src="js/counter.js"></script>
<script src="js/custom.js"></script>
<script src="js/general.js"></script>
<script src="js/administrador.js"></script>
<script src="js/misSolicitudes.js"></script>

<!-- DataTable -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.js"></script>
<script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.dataTables.js"></script>

<!-- moment.js para manejo de fechas-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://unpkg.com/tippy.js@6"></script>


<script>
    const anioActual = new Date().getFullYear();
    document.addEventListener("DOMContentLoaded", function() {
        initDataTablePresAdmin(anioActual);
        initDataTableAhorroAdmin(anioActual);
        initDataTableRetiroAdmin(anioActual);
    });

    $("#montoAprobado").on({
        "focus": function (event) {
            $(event.target).select();
        },
        "keyup": function (event) {
            $(event.target).val(function (index, value ) {
                return value.replace(/\D/g, "")
                    .replace(/([0-9])([0-9]{2})$/, '$1.$2')
                    .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ",");
            });
        }
    });

    document.getElementById('cerrarSesion').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutForm').submit();
    });
</script>


</body>

</html>
