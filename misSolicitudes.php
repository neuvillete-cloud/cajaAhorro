<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/icons/Grammer_Logo.ico" type="image/x-icon">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mis solicitudes</title>

    <!-- CSS FILES -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css" rel="stylesheet">

    <link href="css/styles.css" rel="stylesheet">

    <link href="css/misSolicitudes.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >
    <link href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&display=swap" rel="stylesheet">

    <?php
    session_start();
    $nombreUser = $_SESSION['nombreUsuario'];
    $esAdmin = $_SESSION['admin'];

    if ($nombreUser == null){
        header("Location: https://grammermx.com/RH/CajaDeAhorro/login.php");
    }


    ?>

</head>
<body>
<nav class="navbar navbar-expand-lg bg-light shadow-lg">
    <div clas s="container" id="top">
        <a class="navbar-brand" href="index.php">
            <img src="images/icons/croc_logo.png" class="logo img-fluid" alt="Logo CROC">
            <img src="images/icons/GrammerAzul.png" class="logo img-fluid" alt="Logo Grammer">
            <span id="tituloCajita">Caja de Ahorro</span>
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
                    <a class="nav-link click-scroll" href="#sectionMisPrestamos">Prestamos</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link click-scroll" href="#sectionMiCaja">Mi Caja de Ahorro</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link click-scroll" onclick="estatutosAhorro()">Estatutos</a>
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

<main>
    <section class="section-padding" id="sectionMisPrestamos">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12 p-0">
                    <div class="page-content">
                        <div class="records table-responsive">
                            <div class="table-Conteiner table-responsive-sm" id="divTablaSolicitudes">
                                <h3 class="mb-4">Solicitudes de Préstamo</h3>
                                <div id="contenedorAzul"></div>
                                <table class="dataTable tableSearch table" id="tablaSolicitudes" >
                                    <thead class="thead-light">
                                    <tr>
                                        <th class="centered" id="folio">FOLIO</th>
                                        <th class="centered">FECHA</th>
                                        <th class="centered">MONTO SOLICITADO </th>
                                        <th class="centered">ESTATUS </th>
                                        <th class="centered">ACCIONES</th>

                                        <!--
                                        acciones : ver respuesta, Agregar avales
                                        <th class="centered">FECHA RESPUESTA</th>
                                        <th class="centered">MONTO APROBADO </th>
                                        <th class="centered">FECHA DEPÓSITO</th>
                                        <th class="centered">COMENTARIOS</th>
                                        -->
                                    </tr>
                                    </thead>
                                    <tbody id="misSolicitudesBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding" id="sectionMiCaja">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-12 p-0">
                    <div class="page-content">
                        <div class="records table-responsive">
                            <div class="table-Conteiner table-responsive" id="divTablaSolicitudesA">
                                <h3 class="mb-4">Solicitudes de Caja de Ahorro</h3>
                                <div id="contenedorAzul"></div>
                                <h4 class="mb-4">Mi Caja de Ahorro</h4>
                                <span id="spanAvisoCA"></span>
                                <table class="dataTable tableSearch table" id="tablaCajaAhorro" >
                                    <thead class="thead-light">
                                    <tr>
                                        <th class="centered" id="folioCA">FOLIO</th>
                                        <th class="centered">MI NÓMINA</th>
                                        <th class="centered">FECHA DE SOLICITUD</th>
                                        <th class="centered">MONTO AHORRO </th>
                                        <th class="centered">ACCIONES </th>
                                    </tr>
                                    </thead>
                                    <tbody id="cajaAhorroBody"></tbody>
                                </table>

                                <h4 class="mb-4"><br>Solicitudes de retiro</h4>
                                <table class="dataTable tableSearch table" id="tablaRetiros" >
                                    <thead class="thead-light">
                                    <tr>
                                        <th class="centered" id="folioRetiro">FOLIO RETIRO</th>
                                        <th class="centered">FECHA</th>
                                        <th class="centered">ESTATUS</th>
                                        <th class="centered">ACCIONES</th>
                                    </tr>
                                    </thead>
                                    <tbody id="retirosBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para ver respuesta de prestamo -->
    <div class="modal fade" id="modalRespPresSol" tabindex="-1" aria-labelledby="responderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="respModalTitSol">Solicitud de Préstamo Folio <span id="folioSolPres"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive" id="divTablaModalSol">
                    <table class="table" id="tablaModalSolicitante">
                        <tr>
                            <td class="etiqueta">
                                <strong >Folio de solicitud:</strong>
                            </td>
                            <td id="folioSolicitudMS">[Folio de solicitud]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Fecha solicitud:</strong>
                            </td>
                            <td id="fechaSolicitudMS">[Fecha solicitud]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Monto solicitado:</strong>
                            </td>
                            <td id="montoSolicitadoMS">[Monto solicitado]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Nómina:</strong>
                            </td>
                            <td id="nominaSolMS">[Nómina]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Nombre:</strong>
                            </td>
                            <td id="nombreSolMS">[Nombre]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Teléfono:</strong>
                            </td>
                            <td id="telefonoSolMS">[Teléfono]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Monto Aprobado:</strong>
                            </td>
                            <td id="montoAprobadoMS">[Monto Aprobado]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Estatus del préstamo:</strong>
                            </td>
                            <td id="estatusMS">[Estatus del préstamo]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Monto depositado:</strong>
                            </td>
                            <td id="montoDepP">[Monto depositado]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Fecha depósito:</strong>
                            </td>
                            <td id="FechaDepP">[Fecha depósito]</td>
                        </tr>
                        <tr>
                            <td class="etiqueta">
                                <strong>Comentarios:</strong>
                            </td>
                            <td id="comentariosMS">[Comentarios]</td>
                        </tr>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Prestamo (solicitante)-->
    <div class="modal fade" id="editarPrestamoModal" tabindex="-1" aria-labelledby="editarPrestamoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPrestamoModalLabel">Editar Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="col-12 mx-auto">
                        <form class="custom-form volunteer-form mb-5 mb-lg-0" action="" method="post" role="form" id="editarPrestamoForm">
                            <input type="hidden" id="idSolicitudE" name="idSolicitudE">
                            <input type="hidden" id="anioConvE" name="anioConvE">
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <label for="telefonoE">Teléfono: </label>
                                    <input type="tel" name="telefonoE" id="telefonoE" class="form-control" placeholder="5551234567" required>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <label for="montoSolicitadoE">Monto solicitado: </label>
                                    <input type="text" name="montoSolicitadoE" id="montoSolicitadoE" class="form-control" placeholder="$1,000" required>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary mt-3 w-100" data-bs-dismiss="modal" onclick="actualizarPrestamo()">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para ver solicitud de retiro -->
    <div class="modal fade" id="modalConsultaRetiro" tabindex="-1" aria-labelledby="responderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titModalRetiro">Solicitud de Retiro de Caja de Ahorro<span id="folioRetiro"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive" id="divTablaModalRetiro">
                    <table class="table" id="tablaModalRetiroSol">
                        <tr>
                            <td class="etiquetaR">
                                <strong >Folio de retiro:</strong>
                            </td>
                            <td id="folioRetiroSol">[Folio de solicitud]</td>
                        </tr>
                        <tr>
                            <td class="etiquetaR">
                                <strong >Folio de caja:</strong>
                            </td>
                            <td id="folioRetiroCaja">[Folio de caja]</td>
                        </tr>
                        <tr>
                            <td class="etiquetaR">
                                <strong>Fecha solicitud:</strong>
                            </td>
                            <td id="fechaSolRetiro">[Fecha solicitud]</td>
                        </tr>
                        <tr>
                            <td class="etiquetaR">
                                <strong>Nómina:</strong>
                            </td>
                            <td id="nominaSolRetiro">[Nómina]</td>
                        </tr>
                        <tr>
                            <td class="etiquetaR">
                                <strong>Nombre:</strong>
                            </td>
                            <td id="nombreSolRetiro">[Nombre]</td>
                        </tr>
                        <tr>
                            <td class="etiquetaR">
                                <strong>Estatus del depósito:</strong>
                            </td>
                            <td id="estatusRetiroSol">[Estatus del depósito]</td>
                        </tr>
                        <tr>
                            <td class="etiquetaR">
                                <strong>Fecha depósito:</strong>
                            </td>
                            <td id="fechaDepRetiro">[Fecha solicitud]</td>
                        </tr>
                        <tr>
                            <td class="etiquetaR">
                                <strong>Monto depositado:</strong>
                            </td>
                            <td id="montoRetiroSol">[Monto depositado]</td>
                        </tr>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php require_once ("modalAhorro.php"); ?>
    <?php require_once('modalAvales.php'); ?>
</main>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.sticky.js"></script>
<script src="js/counter.js"></script>
<script src="js/custom.js"></script>
<script src="js/general.js"></script>
<script src="js/misSolicitudes.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

<script>

    document.addEventListener("DOMContentLoaded", function() {
        initDataTable();
        initDataTableCaja();
        initDataTableRetiro();
    });

    //Falta listener que funcione en celulares

    $(document).ready(function() {
        // Listener para Aval 1
        $('#nominaAval1').on('keypress', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                const nomina = $(this).val(); // Obtener el valor del campo de nómina
                if (nomina) {
                    consultarNombreAval(nomina, '#nombreAval1','nominaAval1');
                }
                e.preventDefault(); // Evitar el envío del formulario si es necesario
            }
        });

        // Listener para el campo de nómina del Aval 2
        $('#nominaAval2').on('keypress', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                const nomina = $(this).val(); // Obtener el valor del campo de nómina
                if (nomina) {
                    consultarNombreAval(nomina, '#nombreAval2','nominaAval2');
                }
                e.preventDefault(); // Evitar el envío del formulario si es necesario
            }
        });
    });

    document.getElementById('cerrarSesion').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutForm').submit();
    });


</script>
</body>
</html>