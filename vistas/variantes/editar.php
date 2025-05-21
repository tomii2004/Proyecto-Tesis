
<style>
.swal2-popup.alerta-grande {
    width: 450px !important;
    /* Ancho más grande */
    height: auto !important;
    /* Ajuste automático de altura */
    font-size: 12px !important;
    /* Fuente más grande */
}
</style>



<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h2>Variantes</h2>
            <p>Editar Variantes Basicas</p>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="?c=variantes">Variantes</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <legend>Editar Variante</legend>
            <form action="?c=variantes&a=ActualizarVariante" method="post">
                <input type="hidden" name="id" value="<?php echo $variante['ID_talla'] ?? $variante['ID_colores'];?>">
                <input type="hidden" name="tipo"
                    value="<?php echo isset($variante['ID_talla']) ? 'talla' : 'color'; ?>">

                <label for="nombre">Nuevo Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="<?php echo $variante['nombre'];?>" required><br><br>

                <button href="?c=variantes" onclick="event.preventDefault(); history.back();"
                    class="btn btn-default btn-md w-100">Cancelar</button>

                <!-- Botón para actualizar -->
                <input type="submit" class="btn btn-primary btn-md w-100" value="Actualizar">

                <!-- Botón para eliminar, ejecuta la función confirmarEliminacion -->
                <button type="button" class="btn btn-danger btn-md w-100 mt-2"
                    onclick="confirmarEliminacion('<?php echo $variante['ID_talla'] ?? $variante['ID_colores']; ?>', '<?php echo isset($variante['ID_talla']) ? 'talla' : 'color'; ?>')">Eliminar</button>

            </form>
        </div>
    </div>

    <script>
    function confirmarEliminacion(id, tipo) {
        Swal.fire({
            title: '¿Estás seguro que quieres eliminarla?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'alerta-grande' // Clase personalizada para el tamaño
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear un formulario dinámico para enviar los datos de eliminación
                const form = document.createElement('form');
                form.method = 'post';
                form.action = '?c=variantes&a=EliminarVariante';

                // Campo oculto para el ID
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = id;

                // Campo oculto para el tipo de variante
                const inputTipo = document.createElement('input');
                inputTipo.type = 'hidden';
                inputTipo.name = 'tipo';
                inputTipo.value = tipo;

                form.appendChild(inputId);
                form.appendChild(inputTipo);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    </script>