

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
</head>
<body>
    <div class="content-wrapper">
    <div class="page-title">
    <div>
        <h2>Formulario de Contacto</h2>
        <p>Formulario de Contacto</p>
    </div>
    <div>
        <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=contacto">Contacto</a></li>
        </ul>
    </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <form action="?c=contacto&a=enviarFormulario" method="post">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required><br><br>
                
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required><br><br>
                
                <label for="mensaje">Mensaje:</label><br>
                <textarea id="mensaje" class="form-control" name="mensaje" rows="5" required></textarea><br><br>
                
                <input type="submit" class= "btn btn-primary" value="Enviar">
            </form>
            <?php
             if (isset($_SESSION['mensajeExito'])) {
                echo '<p style="color:green;font-weight: bold;margin: 10px 0;">' . $_SESSION['mensajeExito'] . '</p>';
                unset($_SESSION['mensajeExito']); //elimina
             }

            if (isset($_SESSION['mensajeError'])) {
                echo '<p style="color:red;font-weight: bold;margin: 10px 0;">' . $_SESSION['mensajeError'] . '</p>';
                unset($_SESSION['mensajeError']); 
             }
            ?>
        </div>
    </div>
    </div>
    </div>
    
    
</body>
</html>
