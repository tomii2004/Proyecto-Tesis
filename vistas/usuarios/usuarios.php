
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
        <h2>Usuarios</h2>
        <p>Ver Usuarios </p>
    </div>
    <div>
        <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=usuarios">Usuarios</a></li>
        </ul>
    </div>
    </div>
    <div class = "container-fluid px-4">
        <div class="table-responsive">
            <div style="display: inline-block; position: relative; width: auto;margin-bottom: 20px;">
                <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Buscar usuario..." style="padding-right: 30px;">
                <button type="button" id="clear-buscador" class="btnclear">✖</button>
            </div>
            <table id="tablausuarios" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)) { ?>
                        <?php foreach($usuarios as $usuario){ ?>
                            <tr>
                                <td><?php echo $usuario['cliente']; ?></td>
                                <td><?php echo $usuario['usuario']; ?></td>
                                <td><?php echo $usuario['estatus']; ?></td>
                                <td>
                                    <a href="?c=usuarios&a=FormEditarPassword&user_id=<?php echo $usuario['ID_usuario'];?>" class="btn btn-primary"><i class="fa fa-solid fa-pen"></i></a>
                                    <?php if($usuario['activacion'] == 1):?>
                                        <button type="button" class="btn btn-danger" onclick="deshabilitarusuario('<?php echo $usuario['ID_usuario']; ?>')"><i class="fa fa-solid fa-arrow-down"></i> </button>
                                    <?php else : ?>
                                        <button type="button" class="btn btn-success" onclick="habilitarusuario('<?php echo $usuario['ID_usuario']; ?>')"><i class="fa fa-solid fa-arrow-up"></i> </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php }?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3">No hay usuarios registradas.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Botón "Anterior" -->
                    <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?c=compras&pagina=<?= max($pagina_actual - 1,1)?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Anterior</span>
                        </a>
                    </li>

                    <!-- Números de página -->
                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                        <a class="page-link" href="?c=compras&pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>

                    <!-- Botón "Siguiente" -->
                    <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?c=compras&pagina=<?= min($pagina_actual + 1,$total_paginas)?>" aria-label="Next">
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
    function deshabilitarusuario(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Realmente desea deshabilitar a este usuario?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, deshabilitar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'alerta-grande'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '?c=usuarios&a=PeticionBajaUsuario', // Ajusta según tu estructura de rutas
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.datosUsuario) {
                            Swal.fire({
                                title: 'Usuario deshabilitado',
                                html: response.datosUsuario,
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
    function habilitarusuario(id) {
        Swal.fire({
            title: '¿Está seguro?',
            text: '¿Realmente desea habilitar a este usuario?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, habilitar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'alerta-grande'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '?c=usuarios&a=PeticionAltaUsuario', // Ajusta según tu estructura de rutas
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.datosUsuario) {
                            Swal.fire({
                                title: 'Usuario habilitado',
                                html: response.datosUsuario,
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
        fetch(`?c=usuarios&a=BuscarAjax&termino=${encodeURIComponent(termino)}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#tablausuarios tbody');
                tbody.innerHTML = ''; // Limpia la tabla
                if (data.length > 0) {
                    data.forEach(usuario => {
                        const botonEstado = usuario.activacion == 1
                        ? `<button type="button" class="btn btn-danger" onclick="deshabilitarusuario('${usuario.ID_usuario}')"><i class="fa fa-solid fa-arrow-down"></i></button>`
                        : `<button type="button" class="btn btn-success" onclick="habilitarusuario('${usuario.ID_usuario}')"><i class="fa fa-solid fa-arrow-up"></i></button>`;
                        const row = `
                            <tr>
                                    <td>${usuario.cliente}</td>
                                    <td>${usuario.usuario}</td>
                                    <td>${usuario.estatus}</td>
                                    <td>
                                        <a href="?c=usuarios&a=FormEditarPassword&user_id=${usuario.ID_usuario}" class="btn btn-primary"><i class="fa fa-solid fa-pen"></i></a>
                                        ${botonEstado}
                                    </td>
                                </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                }else{
                    tbody.innerHTML = '<tr><td colspan="4">No hay resultados.</td></tr>';
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