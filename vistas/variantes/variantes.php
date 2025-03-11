
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Variantes</title>
</head>
<body>
    
    <div class="content-wrapper">
    <div class="page-title">
    <div>
        <h2>Variantes</h2>
        <p>Definir Variantes Basicas</p>
    </div>
    <div>
        <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=variantes">Variantes</a></li>
        </ul>
    </div>
    </div>
    <div class = "container-fluid px-4">
        <a href="?c=variantes&a=FormNuevo" class= "btn btn-primary">Nueva</a>
        <div class="table-responsive">
            <div style="float:left;margin-right:30px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Talle</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($variantes1)) { ?>
                            <?php foreach($variantes1 as $variante1){ ?>
                                <tr>
                                    <td><?php echo $variante1['ID_talla']; ?></td>
                                    <td><?php echo ucfirst($variante1['nombre']); ?></td>
                                    <td><a class= "btn btn-warning btn-sm" href="?c=variantes&a=FormEditar&tipo=talla&id=<?php echo $variante1['ID_talla']?>"><i class="fa fa-solid fa-pen"></i></a></td>
                                    
                                </tr>
                            <?php }?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3">No hay variantes disponibles.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div style="float:left;margin-right:30px;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Color</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($variantes2)) { ?>
                            <?php foreach($variantes2 as $variante2){ ?>
                                <tr>
                                    <td><?php echo ucfirst($variante2['ID_colores']); ?></td>
                                    <td><?php echo ucfirst($variante2['nombre']); ?></td>
                                    <td><a class= "btn btn-warning btn-sm" href="?c=variantes&a=FormEditar&tipo=color&id=<?php echo $variante2['ID_colores']?>"><i class="fa fa-solid fa-pen"></i></a></td>
                                    
                                </tr>
                            <?php }?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3">No hay variantes disponibles.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

</body>
</html>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const alerta = urlParams.get("alerta");

        if (alerta === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Registro Exitoso',
                text: 'Variante añadida correctamente.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '?c=variantes'; // Para limpiar la URL
            });
        } else if (alerta === "error") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La variante ya existe en la base de datos.',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '?c=variantes'; // Para limpiar la URL
            });
        }
    });
</script>