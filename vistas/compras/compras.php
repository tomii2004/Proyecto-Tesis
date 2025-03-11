
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compras</title>
    <style>.swal2-popup.alerta-grande {
        width: 450px !important; /* Ancho más grande */
        height: auto !important; /* Ajuste automático de altura */
        font-size: 12px !important; /* Fuente más grande */
        }
        .btnclear{
            position: absolute; 
            top: 50%; 
            right: 10px; 
            transform: translateY(-50%); 
            border: none; 
            background: transparent; 
            cursor: pointer; 
            padding: 0;
            font-size: 16px;
            color:grey;
        }
    </style>
    
</head>
<body>
    
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
    <div class = "container-fluid px-4">
        <div class="table-responsive">
            <div style="display: inline-block; position: relative; width: auto;margin-bottom: 20px;">
                <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Buscar compras..." style="padding-right: 30px;">
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
                                <td><?php echo number_format($compra['total'],2,'.',','); ?></td>
                                <td><?php echo $compra['fecha_formateada']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary" onclick="vercompra('<?php echo $compra['ID_transaccion']; ?>')"><i class="fa fa-regular fa-eye"></i> </button>
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
                        <a class="page-link" href="?c=compras&pagina=<?= max($pagina_actual - 1,1)?>&orden=<?= $orden ?>" aria-label="Previous">
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
                    <a class="page-link" href="?c=compras&pagina=<?= min($pagina_actual + 1,$total_paginas)?>&orden=<?= $orden ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Siguiente</span>
                    </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    
    
</body>
</html>
<script>
    function vercompra(orden) {
        $.ajax({
            url: '?c=compras&a=PeticionDetalleCompra', // Ajusta según tu estructura de rutas
            method: 'POST',
            data: { orden: orden },
            dataType: 'json',
            success: function(response) {
                if (response.html) {
                    Swal.fire({
                        title: 'Detalles de la Compra',
                        html: response.html,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        customClass: {
                            popup: 'alerta-grande'  // Clase personalizada para el tamaño
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.error || 'Ocurrió un error desconocido',
                        icon: 'error',
                        confirmButtonText: 'Cerrar',
                        customClass: {
                            popup: 'alerta-grande'  // Clase personalizada para el tamaño
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
                        popup: 'alerta-grande'  // Clase personalizada para el tamaño
                    }
                });
            }
        });
    }
</script>
<script>
    // Actualización de la tabla al escribir en el buscador
    document.getElementById('buscador').addEventListener('input', function () {
        const termino = this.value;
        // Oculta la paginación si hay un término de búsqueda
        const paginacion = document.querySelector('nav[aria-label="Page navigation"]');
        if (termino) {
        paginacion.style.display = 'none';
        } else {
        paginacion.style.display = 'block';
        }
        fetch(`?c=compras&a=BuscarAjax&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#tablacompras tbody');
                tbody.innerHTML = ''; // Limpia la tabla
                if (data.length > 0) {
                    data.forEach(compra => {
                        const row = `
                            <tr>
                                <td>${compra.ID_transaccion}</td>
                                <td>${compra.cliente}</td>
                                <td>${Number(compra.total).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                <td>${compra.fecha_formateada}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" onclick="vercompra('${compra.ID_transaccion}')">
                                        <i class="fa fa-regular fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                }else{
                    tbody.innerHTML = '<tr><td colspan="5">No hay resultados.</td></tr>';
                }
            })
            .catch(error => console.error('Error:', error));
    });
    document.getElementById('clear-buscador').addEventListener('click', function () {
        const buscador = document.getElementById('buscador');
        buscador.value = ''; // Limpia el valor del input
        buscador.dispatchEvent(new Event('input')); // Simula un evento de entrada para actualizar los resultados
    });
</script>
      