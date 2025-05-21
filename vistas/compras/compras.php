<style>
.swal2-popup.alerta-grande {
    width: 450px !important;
    /* Ancho más grande */
    height: auto !important;
    /* Ajuste automático de altura */
    font-size: 12px !important;
    /* Fuente más grande */
}

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

.toast {
  background-color: #333;
  color: #fff;
}
</style>



<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h2>Compras</h2>
            <p>Ver Compras </p>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="?c=compras">Compras</a></li>
            </ul>
        </div>
    </div>
    <div class="container-fluid px-4">
        <div class="table-responsive">
            <div style="display: inline-block; position: relative; width: auto;margin-bottom: 20px;">
                <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Buscar compras..."
                    style="padding-right: 30px;">
                <button type="button" id="clear-buscador" class="btnclear">✖</button>
            </div>
            <div style="display: inline-block; position: relative; width: auto; float: right;">
                <a href="?c=compras&a=GenerarReporte" class="btn-primary btn-sm">Generar Reporte</a>
            </div>
            <table id="tablacompras" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Folio</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Total</th>
                        <th scope="col">
                            <a href="?c=compras&orden=<?= ($orden == 'asc') ? 'desc' : 'asc' ?>&pagina=1">Fecha</a>
                        </th>
                        <th scope="col">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($compras)) { ?>
                    <?php foreach($compras as $compra){ ?>
                    <tr>
                        <td><?php echo $compra['ID_transaccion']; ?></td>
                        <td><?php echo $compra['cliente']; ?></td>
                        <td><?php echo $compra['telefono']; ?></td>
                        <td><?php echo number_format($compra['total'],2,'.',','); ?></td>
                        <td><?php echo $compra['fecha_formateada']; ?></td>
                        <td>
                            <button type="button" class="btn btn-primary"
                                onclick="vercompra('<?php echo $compra['ID_transaccion']; ?>')"><i
                                    class="fa-regular fa-eye"></i> </button>
                        </td>
                    </tr>
                    <?php }?>
                    <?php } else { ?>
                    <tr>
                        <td colspan="3">No hay compras registradas.</td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Botón "Anterior" -->
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?c=compras&pagina=<?= max($pagina_actual - 1,1)?>&orden=<?= $orden ?>"
                            aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Anterior</span>
                        </a>
                    </li>

                    <!-- Números de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                        <a class="page-link" href="?c=compras&pagina=<?= $i ?>&orden=<?= $orden ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <!-- Botón "Siguiente" -->
                    <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?c=compras&pagina=<?= min($pagina_actual + 1,$total_paginas)?>&orden=<?= $orden ?>"
                            aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Siguiente</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <script>
    //para evitar duplicados cuando se hace el polling
    const idsComprasRenderizadas = new Set();    
    
    function mostrarToast(mensaje) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: mensaje,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'swal2-toast'
            }
        });
    }


    function renderizarTablaCompras(data) {
        const tbody = document.querySelector('#tablacompras tbody');
        tbody.innerHTML = ''; // Limpia la tabla

        if (data.length > 0) {
            data.forEach(compra => {
                const row = `
                        <tr>
                            <td>${compra.ID_transaccion}</td>
                            <td>${compra.cliente}</td>
                            <td>${compra.telefono}</td>
                            <td>${Number(compra.total).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${compra.fecha_formateada}</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="vercompra('${compra.ID_transaccion}')">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                tbody.innerHTML += row;
                idsComprasRenderizadas.add(compra.ID_transaccion);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5">No hay resultados.</td></tr>';
        }
    }



    // Fecha de la última actualización para consultar solo novedades
    let ultimaActualizacion = '<?= $ultima_fecha ?>';

    // Buscar nuevas compras desde la última actualización
    function buscarNuevasCompras() {
        fetch(`?c=compras&a=ObtenerNuevasComprasDesde&desde=${encodeURIComponent(ultimaActualizacion)}`)
            .then(response => response.json())
            .then(data => {
                console.log('Nuevas compras:', data);
                if (data.length > 0) {
                    ultimaActualizacion = data[data.length - 1].fecha_raw;

                    const tbody = document.querySelector('#tablacompras tbody');

                    data.forEach(compra => {
                        const fila = document.createElement('tr');
                        fila.innerHTML = `
                            <td>${compra.ID_transaccion}</td>
                            <td>${compra.cliente}</td>
                            <td>${compra.telefono}</td>
                            <td>${Number(compra.total).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${compra.fecha_formateada}</td>
                            <td>
                                <button type="button" class="btn btn-primary" onclick="vercompra('${compra.ID_transaccion}')">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </td>
                        `;
                        tbody.prepend(fila);
                        idsComprasRenderizadas.add(compra.ID_transaccion);
                    });

                    mostrarToast(`¡Se agregaron ${data.length} nuevas compra(s)!`);
                }
            })
            .catch(error => console.error('Error al buscar nuevas compras:', error));
    }

    // Ejecutar la búsqueda de nuevas compras cada 5 segundos
    setInterval(buscarNuevasCompras, 5000);
    </script>




    <script>
    function vercompra(orden) {
        $.ajax({
            url: '?c=compras&a=PeticionDetalleCompra', // Ajusta según tu estructura de rutas
            method: 'POST',
            data: {
                orden: orden
            },
            dataType: 'json',
            success: function(response) {
                if (response.html) {
                    Swal.fire({
                        title: 'Detalles de la Compra',
                        html: response.html,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        customClass: {
                            popup: 'alerta-grande' // Clase personalizada para el tamaño
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.error || 'Ocurrió un error desconocido',
                        icon: 'error',
                        confirmButtonText: 'Cerrar',
                        customClass: {
                            popup: 'alerta-grande' // Clase personalizada para el tamaño
                        }
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo obtener los detalles de la compra',
                    icon: 'error',
                    confirmButtonText: 'Cerrar',
                    customClass: {
                        popup: 'alerta-grande' // Clase personalizada para el tamaño
                    }
                });
            }
        });
    }

    function enviarMailCompra(orden) {
        Swal.fire({
            title: '¿Enviar correo al cliente?',
            text: 'Se disparará un email desde el servidor',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '?c=compras&a=EnviarCorreo',
                    method: 'POST',
                    data: {
                        orden: orden
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Enviado', res.success, 'success');
                        } else {
                            Swal.fire('Error', res.error || 'No se pudo enviar el correo', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Fallo en la petición', 'error');
                    }
                });
            }
        });
    }
    </script>
    <script>
    // Actualización de la tabla al escribir en el buscador
    document.getElementById('buscador').addEventListener('input', function() {
        const termino = this.value;
        const paginacion = document.querySelector('nav[aria-label="Page navigation"]');

        paginacion.style.display = termino ? 'none' : 'block';

        fetch(`?c=compras&a=BuscarAjax&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                renderizarTablaCompras(data);
            })
            .catch(error => console.error('Error:', error));
    });
    document.getElementById('clear-buscador').addEventListener('click', function() {
        const buscador = document.getElementById('buscador');
        buscador.value = ''; // Limpia el valor del input
        buscador.dispatchEvent(new Event(
            'input')); // Simula un evento de entrada para actualizar los resultados
    });
    </script>