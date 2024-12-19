<!-- Modal para ver Mi caja de ahorro -->
<div class="modal fade" id="modalConsultaAhorro" tabindex="-1" aria-labelledby="responderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titModalMiAhorro">Solicitud de Retiro de Caja de Ahorro<span id="folioRetiro"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body table-responsive" id="divTablaModalAhorro">
                <table class="table" id="tablaModalAhorro">
                    <tr>
                        <td class="etiquetaR">
                            <strong >Folio de caja:</strong>
                        </td>
                        <td id="folioCaja"></td>
                    </tr>
                    <tr>
                        <td class="etiquetaR">
                            <strong>Monto para ahorro:</strong>
                        </td>
                        <td id="montoAhorro"></td>
                    </tr>
                    <tr>
                        <td class="etiquetaR">
                            <strong>Fecha solicitud:</strong>
                        </td>
                        <td id="fechaAhorro"></td>
                    </tr>
                    <tr>
                        <td class="etiquetaR">
                            <strong>Nómina:</strong>
                        </td>
                        <td id="nominaSolAho"></td>
                    </tr>
                    <tr>
                        <td class="etiquetaR">
                            <strong>Nombre:</strong>
                        </td>
                        <td id="nombreAho"></td>
                    </tr>
                    <tr>
                        <td class="etiquetaR">
                            <strong>Beneficiario 1:</strong>
                        </td>
                        <td id="beneficiarioUno"></td>
                    </tr>
                    <tr id="rowBenDos">
                        <td class="etiquetaR">
                            <strong>Beneficiario 2:</strong>
                        </td>
                        <td id="beneficiarioDos"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>