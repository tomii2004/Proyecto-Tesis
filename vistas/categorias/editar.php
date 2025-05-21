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
            <h2>Categorias</h2>
            <p>Editar Categorias Basicas</p>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="?c=categorias">Categorias</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <legend>Editar Categoria</legend>
            <form action="?c=categorias&a=ActualizarCategoria" method="post">
                <input type="hidden" name="id" value="<?php echo $categoria['ID_categoria'];?>">

                <label for="nombre">Nuevo Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    value="<?php echo $categoria['nombre'];?>" required><br><br>

                <button href="?c=categorias" onclick="event.preventDefault(); history.back();"
                    class="btn btn-default btn-md w-100">Cancelar</button>
                <!-- Botón para actualizar -->
                <input type="submit" class="btn btn-primary btn-md w-100" value="Actualizar">

                <!-- Botón para eliminar, ejecuta la función confirmarEliminacion -->
                <button type="button" class="btn btn-danger btn-md w-100 mt-2"
                    onclick="confirmarEliminacion(<?php echo $categoria['ID_categoria']; ?>)">Eliminar</button>

            </form>
        </div>
    </div>


    <script>
    function confirmarEliminacion(id) {
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
                form.action = '?c=categorias&a=EliminarCategoria';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    </script>