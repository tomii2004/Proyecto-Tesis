
<style>
    /* Estilo para el contenedor de alertas */
    .alert {
        padding: 15px;
        margin-top: 20px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        position: relative;
        transition: opacity 0.15s linear;
        max-width:350px;
    }

    /* Estilo para alertas de advertencia */
    .alert-warning {
        background-color: #fcf8e3;
        border-color: #faebcc;
        color: #8a6d3b;
    }

    /* Botón de cerrar (Close) */
    .alert .btn-close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.2rem;
        background: none;
        border: none;
        color: #000;
        cursor: pointer;
    }

    /* Efecto hover para el botón de cerrar */
    .alert .btn-close:hover {
        color: #555;
    }

    /* Transición de fade (opcional) */
    .fade {
        opacity: 0;
        transition: opacity 0.15s linear;
    }

    .fade.show {
        opacity: 1;
    }
</style>
<script>
    //boton para cerrar la notificacion de los mensajes
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('btn-close')) {
            const alertBox = event.target.parentElement; // Selecciona el contenedor de la alerta
            alertBox.style.opacity = '0'; // Añade un efecto de desvanecimiento
            setTimeout(() => alertBox.remove(), 150); // Elimina la alerta después del desvanecimiento
        }
    });
</script>
<body>
    
    <div class="content-wrapper">
    <div class="page-title">
    <div>
        <h2>Admin</h2>
        <p>Editar Admin</p>
    </div>
    <div>
        <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=inicio">Admin</a></li>
        </ul>
    </div>
    </div>
    <div class="row">
    <div class="col-md-6">
        <legend>Editar Administrador</legend>
        <form action="?c=inicio&a=FormEditarPassword" method="POST" autocomplete= "off">
            <input type="hidden" name="id" value="<?php echo $usuario['ID_admin']?>">

            <h3>Cambiar Contraseña</h3>
            <label for="usuario">Usuario</label>
            <input class = "form-control" type="text" placeholder="Usuario" value="<?php echo $usuario['usuario']?>" disabled>
            
            <label for="email">Ingrese su Nueva Contraseña</label>
            <input class = "form-control" type="password" placeholder="Nueva Contraseña" name= "password" id="password" >
            
            <i class="custom-i"><b>Requiere:</b>La contraseña debe tener al entre 8 y 16 caracteres, al menos un dígito, al menos una minúscula y al menos una mayúscula.</i><br>
            
            <label for="email" style="margin-top:10px;">Confirme la Nueva Contraseña</label>
            <input class = "form-control" type="password" placeholder="Confirmar Contraseña" name= "repassword" id="repassword" >
            <button href="?c=usuarios" onclick="event.preventDefault(); history.back();" class="btn btn-default btn-md w-100" style="margin-top:20px;">Cancelar</button>
            <button type="submit" class="btn btn-primary btn-md w-100" style="margin-top:20px;">Actualizar</button> 
        </form>
        
    </div>
</div>
