
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias</title>
    <style>
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
        <h2>Categorias</h2>
        <p>Definir Categorias Basicas</p>
    </div>
    <div>
        <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=categorias">Categorias</a></li>
        </ul>
    </div>
    </div>
    <div class = "container-fluid px-4">
        <div class="table-responsive">
            <div style="display: inline-block; position: relative; width: auto;margin-bottom: 20px;">
                <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Buscar categorias..." style="padding-right: 30px;">
                <button type="button" id="clear-buscador" class="btnclear">✖</button>
            </div>
            <div style="margin-bottom: 20px;">
                <a href="?c=categorias&a=FormNuevo" class= "btn btn-primary">Nueva</a>
            </div>
            <table class="table table-hover" id="tablacategorias">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categorias)) { ?>
                        <?php foreach($categorias as $categoria){ ?>
                            <tr>
                                <td><?php echo $categoria['ID_categoria']; ?></td>
                                <td><?php echo ucfirst($categoria['nombre']); ?></td>
                                <td>
                                    <a class= "btn btn-warning btn-sm" href="?c=categorias&a=FormEditar&id=<?php echo $categoria['ID_categoria']?>"><i class="fa fa-solid fa-pen"></i></a>
                                    <?php if($categoria['activo'] == 1):?>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deshabilitarcategoria('<?php echo $categoria['ID_categoria']; ?>')"><i class="fa fa-solid fa-arrow-down"></i> </button>
                                    <?php else : ?>
                                        <button type="button" class="btn btn-success btn-sm" onclick="habilitarcategoria('<?php echo $categoria['ID_categoria']; ?>')"><i class="fa fa-solid fa-arrow-up"></i> </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php }?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3">No hay categorías disponibles.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Botón "Anterior" -->
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?c=categorias&pagina=<?= max($pagina_actual - 1,1)?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Anterior</span>
                        </a>
                    </li>

                    <!-- Números de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                        <a class="page-link" href="?c=categorias&pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <!-- Botón "Siguiente" -->
                    <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?c=categorias&pagina=<?= min($pagina_actual + 1,$total_paginas)?>" aria-label="Next">
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
        fetch(`?c=categorias&a=BuscarAjax&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#tablacategorias tbody');
                tbody.innerHTML = ''; // Limpia la tabla
                if (data.length > 0) {
                    data.forEach(categoria => {
                        const row = `
                            <tr>
                                <td>${categoria.ID_categoria}</td>
                                <td>${categoria.nombre.charAt(0).toUpperCase() + categoria.nombre.slice(1).toLowerCase()}</td>
                                <td>
                                    <a class="btn btn-warning btn-sm" href="?c=categorias&a=FormEditar&id=${categoria.ID_categoria}">
                                        <i class="fa fa-solid fa-pen"></i>
                                    </a>
                                    ${categoria.activo == 1 
                                        ? `<button type="button" class="btn btn-danger btn-sm" onclick="deshabilitarcategoria('${categoria.ID_categoria}')">
                                                <i class="fa fa-solid fa-arrow-down"></i>
                                        </button>`
                                        : `<button type="button" class="btn btn-success btn-sm" onclick="habilitarcategoria('${categoria.ID_categoria}')">
                                                <i class="fa fa-solid fa-arrow-up"></i>
                                        </button>`
                                    }
                                </td>
                            </tr>
                    `;
                        tbody.innerHTML += row;
                    });
                }else{
                    tbody.innerHTML = '<tr><td colspan="3">No hay resultados.</td></tr>';
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const alerta = urlParams.get("alerta");

        if (alerta === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Registro Exitoso',
                text: 'Categoria añadida correctamente.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '?c=categorias'; // Para limpiar la URL
            });
        } else if (alerta === "error") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La categoria ya existe en la base de datos.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '?c=categorias'; // Para limpiar la URL
            });
        } else if (alerta === "uso") {  // 🚀 Nuevo caso para variantes en uso
            Swal.fire({
                icon: 'warning',
                title: 'No se puede eliminar',
                text: 'Esta categoria está en uso y no se puede eliminar.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '?c=categorias'; // Para limpiar la URL
            });
        }
    });
</script>
<script>
    function deshabilitarcategoria(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Realmente desea desactivar a esta categoria?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'alerta-grande'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '?c=categorias&a=PeticionBajaCategoria', // Ajusta según tu estructura de rutas
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.datosCategoria) {
                            Swal.fire({
                                title: 'Categoria Desactivada',
                                html: response.datosCategoria,
                                icon: 'success',
                                confirmButtonText: 'Cerrar',
                                customClass: {
                                    popup: 'alerta-grande'
                                }
                            }).then(() => {
                                // Recarga la página después de cerrar el SweetAlert
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.error || 'Ocurrió un error desconocido',
                                icon: 'error',
                                confirmButtonText: 'Cerrar',
                                customClass: {
                                    popup: 'alerta-grande'
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo completar la solicitud',
                            icon: 'error',
                            confirmButtonText: 'Cerrar',
                            customClass: {
                                popup: 'alerta-grande'
                            }
                        });
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelado',
                    text: 'No se ha realizado ninguna acción.',
                    icon: 'info',
                    confirmButtonText: 'Cerrar',
                    customClass: {
                        popup: 'alerta-grande'
                    }
                });
            }
        });
    }
    function habilitarcategoria(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Realmente desea activar esta categoria?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'alerta-grande'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '?c=categorias&a=PeticionAltaCategoria', // Ajusta según tu estructura de rutas
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.datosCategoria) {
                            Swal.fire({
                                title: 'Categoria activada',
                                html: response.datosCategoria,
                                icon: 'success',
                                confirmButtonText: 'Cerrar',
                                customClass: {
                                    popup: 'alerta-grande'
                                }
                            }).then(() => {
                                // Recarga la página después de cerrar el SweetAlert
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.error || 'Ocurrió un error desconocido',
                                icon: 'error',
                                confirmButtonText: 'Cerrar',
                                customClass: {
                                    popup: 'alerta-grande'
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo completar la solicitud',
                            icon: 'error',
                            confirmButtonText: 'Cerrar',
                            customClass: {
                                popup: 'alerta-grande'
                            }
                        });
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelado',
                    text: 'No se ha realizado ninguna acción.',
                    icon: 'info',
                    confirmButtonText: 'Cerrar',
                    customClass: {
                        popup: 'alerta-grande'
                    }
                });
            }
        });
    }
</script>