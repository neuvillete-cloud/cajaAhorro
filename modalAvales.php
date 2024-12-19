 <!-- Modal para agregar avales del prestamo -->
<div class="modal fade" id="modalAgregarAvales" tabindex="-1" aria-labelledby="responderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitAvales">Registrar avales para la Solicitud</h5>
                <input type="text" id="folioSolPres" value=""/>
                <input type="hidden" id="anioConvA" name="anioConvA">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="col-lg-12 col-12">
                        <?php
                        global$esAdmin;
                        if($esAdmin == 0){?>
                            <strong class="msjAdvertencia"><i class="bi bi-exclamation-triangle"></i> Recuerda que tu aval debe estar activo en caja<br></strong>
                        <?php }
                        ?>
                        <div class="col-lg-12 col-12 row">
                            <h5><br>Aval 1</h5>
                            <div class="col-lg-3 col-12">
                                <label for="nominaAval1">Nómina: </label>
                                <input type="text" name="nominaAval1" id="nominaAval1" class="form-control" placeholder="Nómina" required>
                            </div>
                            <div class="col-lg-6 col-12">
                                <label for="nombreAval1">Nombre: </label>
                                <input type="text" name="nombreAval1" id="nombreAval1" class="form-control" placeholder="Nombre aval 1" required>
                            </div>
                            <div class="col-lg-3 col-12">
                                <label for="telAval1">Teléfono: </label>
                                <input type="tel" name="telAval1" id="telAval1" class="form-control" placeholder="Teléfono aval 1" required>
                            </div>
                        </div>
                        <div class="col-lg-12 col-12 row">
                            <h5><br>Aval 2</h5>
                            <div class="col-lg-3 col-12">
                                <label for="nominaAval2">Nómina: </label>
                                <input type="text" name="nominaAval2" id="nominaAval2" class="form-control" placeholder="Nómina" required>
                            </div>
                            <div class="col-lg-6 col-12">
                                <label for="nombreAval2">Nombre: </label>
                                <input type="text" name="nombreAval2" id="nombreAval2" class="form-control" placeholder="Nombre aval 2" required>
                            </div>
                            <div class="col-lg-3 col-12">
                                <label for="telAval2">Teléfono: </label>
                                <input type="tel" name="telAval2" id="telAval2" class="form-control" placeholder="Teléfono aval 2" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btnAvales" class="btn btn-primary" data-bs-dismiss="modal" onclick="guardarAvales()">Agregar avales</button>
            </div>
        </div>
    </div>
</div>