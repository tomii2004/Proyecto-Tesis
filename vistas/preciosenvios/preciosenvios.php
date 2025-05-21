<style>
.btnclear {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    cursor: pointer;
    padding: 0;
    font-size: 16px;
    color: grey;
}

.search-wrapper {
    position: relative;
    display: inline-block;
    width: 300px;
    margin-bottom: 1rem;
}
.swal2-popup.alerta-grande {
    width: 450px !important;
    /* Ancho más grande */
    height: auto !important;
    /* Ajuste automático de altura */
    font-size: 12px !important;
    /* Fuente más grande */
}
</style>

<div class="content-wrapper container-fluid py-4">
    <div class="page-title">
        <div>
            <h2>Precios de Envios</h2>
            <p>Definir Precios de Localidades</p>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="?c=preciosenvios">Precios de Envios</a></li>
            </ul>
        </div>
    </div>

    <div class="container-fluid px-4">
        <div class="table-responsive">

            <div class="row mb-3">
                <label for="provincia" class="col-sm-1 col-form-label">Provincia:</label>
                <div class="col-sm-6">
                    <select id="provincia" class="form-select">
                        <option value="">-- Seleccione una provincia --</option>
                        <?php foreach ($provincias as $prov): ?>
                        <option value="<?= htmlspecialchars($prov['id']) ?>">
                            <?= htmlspecialchars($prov['provincia']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- buscador -->
            <div class="search-wrapper">
                <input type="text" id="buscadorLocalidades" class="form-control form-control-sm"
                    placeholder="Buscar localidad..." style="padding-right:30px;">
                <button type="button" id="clear-buscador" class="btnclear">✖</button>
            </div>

            <table id="tablaenvios" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Localidad</th>
                        <th scope="col">Costo de Envío ($)</th>
                        <th scope="col">Acción</th>
                    </tr>
                </thead>
                <tbody id="localidades-body">
                    <!-- Cargado por AJAX -->
                </tbody>
            </table>

        </div>
    </div>
</div>

<script>
$(function() {
    // Cargar localidades al cambiar provincia
    $('#provincia').change(function() {
        const id_provincia = $(this).val();
        $('#localidades-body').empty();
        if (!id_provincia) return;

        $.post('?c=preciosenvios&a=ajaxListarLocalidades', {
                id_provincia
            }, function(data) {
                let rows = '';
                data.forEach(loc => {
                    rows += `
                <tr>
                  <td>${loc.id}</td>
                  <td>${loc.localidad}</td>
                  <td>
                    <input type="number" min="0" step="0.01"
                           value="${loc.costo_envio}"
                           data-id="${loc.id}"
                           class="form-control form-control-sm costo-input">
                  </td>
                  <td>
                    <button class="btn btn-primary guardar" data-id="${loc.id}">
                      Guardar
                    </button>
                  </td>
                </tr>`;
                });
                $('#localidades-body').html(rows);
                $('#buscadorLocalidades').trigger('input'); // reaplicar filtro
            }, 'json')
            .fail(() => {
                Swal.fire('Error', 'No se pudieron cargar las localidades', 'error');
            });
    });

    // Filtrar tabla al escribir
    $('#buscadorLocalidades').on('input', function() {
        const term = $(this).val().toLowerCase();
        $('#tablaenvios tbody tr').each(function() {
            const text = $(this).find('td:nth-child(2)').text().toLowerCase();
            $(this).toggle(text.includes(term));
        });
    });

    // Limpiar buscador
    $('#clear-buscador').click(function() {
        $('#buscadorLocalidades').val('').trigger('input');
    });

    // Guardar costo al pulsar botón
    $(document).on('click', '.guardar', function() {
        const id = $(this).data('id');
        const costo = $(`input[data-id="${id}"]`).val();
        $.post('?c=preciosenvios&a=ajaxActualizarCosto', {
                id_localidad: id,
                costo
            }, function(res) {
                if (res.exito) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Listo!',
                        text: 'Costo actualizado',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'alerta-grande'
                        }
                    });
                } else {
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            }, 'json')
            .fail(() => {
                Swal.fire('Error', 'Error de red al guardar', 'error');
            });
    });
});
</script>