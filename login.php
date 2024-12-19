<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inicio Sesión</title>

    <!-- CSS FILES -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/bootstrap-icons.css" rel="stylesheet">

    <link href="css/styles.css" rel="stylesheet">

    <link rel="stylesheet" href="css/login.css">

    <link rel= "stylesheet" href= "https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css" >

</head>
<body>
    <main>
        <div class="container container-center">
            <div class="rounded-div shadow"  id="divPrincipalLogin">
                <div class="left-side">
                    <div id="divLogos">
                        <img src="images/icons/GrammerAzul.png" id="grammerLogo" class="float-end img-fluid" alt="Grammer Logo">
                        <img src="images/icons/croc_logo.png" id="grammerCROC" class="float-end img-fluid" alt="CROC Logo">
                    </div>

                    <h2 id="iniciarSesionTit">¡Hola,</h2>
                    <h2 ><strong>Bienvenido!</strong></h2>
                    <form id="formInicioSesion" class="form-floating" action="dao/daoLogin.php" method="post">
                        <div class="input-box form-floating" id="userDiv">
                            <input type="text" class="form-control" name="numNomina" id="numNomina" placeholder="No. de nómina" required data-error="Ingrese un número de nómina válido.">
                            <label for="numNomina"><i class="las la-user"></i> Nómina</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="input-box form-floating" id="tagDiv">
                            <input type="password" class="form-control" name="password" id="password" placeholder="TAG" required data-error="Ingrese un TAG válido.">
                            <label for="password"><i class="las la-lock"></i> TAG</label>
                            <div class="invalid-feedback"></div>
                        </div>
                        <button type="submit" class="btn login" name="iniciarSesionBtn">Iniciar Sesión</button>
                    </form>

                    <div id="divSiguenos">
                        <label>S Í G U E N O S </label>
                        <a href="https://www.facebook.com/grammermexico/?locale=es_LA"><i class="lab la-facebook-f"></i></a>
                        <a href="https://www.instagram.com/grammerqro/"><i class="lab la-instagram"></i></a>
                        <a href="https://mx.linkedin.com/company/grammer-automotive-puebla-s-a-de-c-v-"><i class="lab la-linkedin"></i></a>
                    </div>

                </div>
                <div class="right-side" >
                    <!-- La parte derecha tendrá la imagen de fondo -->

                    <div class="d-flex flex-column justify-content-end align-items-end" style="height: 100%;">
                        <div class="p-2 m-3">
                            <small><strong>Caja de Ahorro</strong></small>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>

<!-- JAVASCRIPT FILES -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.sticky.js"></script>
<script src="js/counter.js"></script>
<script src="js/custom.js"></script>

</html>
