

    
    <div class="content-wrapper">
    <div class="page-title">
    <div>
        <h2>Configuraciones</h2>
        <p>Definir Configuraciones Basicas</p>
    </div>
    <div>
        <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=configuracion">Configuracion</a></li>
        </ul>
    </div>
    </div>
    <form action="?c=configuracion&a=GuardarConfiguracion" method= "POST">
        <div class= "row">
            <h3>CONFIGURACION DE CORREO ELECTRONICO</h3>
        </div>
        <div class= "row">
            <div class="col-md-6">
                <label for="smtp">SMTP</label>
                <input type="text" class="form-control" name="smtp" id="smtp" value="<?php echo isset($config['correo_smtp']) ? $config['correo_smtp'] : '';?>" readonly>
            </div>
            <div class="col-md-6">
                <label for="puerto">Puerto</label>
                <input type="text" class="form-control" name="puerto" id="puerto" value="<?php echo isset($config['correo_puerto']) ? $config['correo_puerto'] : '';?>"readonly>
            </div>
        </div>
        <div class= "row">
            <div class="col-md-6">
                <label for="correo-electronico">Correo Electronico</label>
                <input type="email" class="form-control" name="correo-electronico" id="correo-electronico" value="<?php echo isset($config['correo_email']) ? $config['correo_email'] : '';?>">
            </div>
            <div class="col-md-6">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" name="password" id="password" value="<?php echo isset($config['correo_password']) ? $config['correo_password'] : '';?>">
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-primary" style="margin:20px">Guardar</button>
            </div>
    
        </div>
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
    </form>
    