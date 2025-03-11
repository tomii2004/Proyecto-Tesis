<?php
// Detectar página actual
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        a {
            text-decoration: none !important;
        }
        a:hover {
            text-decoration: none !important;
        }
        #btn_session:focus,
        #btn_session:active {
            outline: none !important;
            box-shadow: none !important;
        }
        .dropdown i{
            font-size: 1.5em;
        }
        .dropdown-toggle::after {
            display: none !important; /* Oculta la flecha */
        }
    </style>
</head>
<body>
    
</body>
</html>
<header>
    <!-- Header desktop -->
    <div class="container-menu-desktop">
        <!-- Topbar -->
        <div class="top-bar">
            <div class="content-topbar flex-sb-m h-full container">
                <div class="left-top-bar">
                    texto de lo que quieras
                </div>
            </div>
        </div>

        <div class="wrap-menu-desktop">
            <nav class="limiter-menu-desktop container">
                <!-- Logo desktop -->        
                <a href="index.php" class="logo">
                    <img src="../imagenes/logo.png" alt="IMG-LOGO">
                </a>

                <!-- Menu desktop -->
                <div class="menu-desktop">
                    <ul class="main-menu">
                        <li class="<?= ($current_page == 'index.php') ? 'active-menu' : '' ?>">
                            <a href="index.php">Inicio</a>
                        </li>
                        <li class="<?= ($current_page == 'product.php') ? 'active-menu' : '' ?>">
                            <a href="product.php">Tienda</a>
                        </li>
                        <li class="<?= ($current_page == 'about.php') ? 'active-menu' : '' ?>">
                            <a href="about.php">Sobre Nosotros</a>
                        </li>
                        <li class="<?= ($current_page == 'contact.php') ? 'active-menu' : '' ?>">
                            <a href="contact.php">Contacto</a>
                        </li>
                    </ul>
                </div>    

                <!-- Icon header -->
                <div class="wrap-icon-header flex-w flex-r-m">
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart me-2" id="num_cart" data-notify="<?php echo $num_cart ?>">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </div>
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <div class="dropdown">
                            <button class="btn btn-succes btn-md dropdown-toggle text-white " type="button" id="btn_session" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person"><span style="font-family:Poppins-Medium;font-size:16px;padding-left:5px;"><?php echo $_SESSION['user_name']; ?></span> </i>
                            </button>
                            <ul class="dropdown-menu " aria-labelledby="btn_session">
                                <?php if($_SESSION['user_cliente'] == 1){ ?>
                                    <li><a class="dropdown-item" href="../paginalogin/loginadmin/loginadmin.php">Panel de Administrador</a></li>
                                    <li><a class="dropdown-item" href="../paginalogin/logout.php">Cerrar sesion</a></li>
                                <?php }else{ ?>
                                    <li><a class="dropdown-item" href="miscompras.php">Mis compras</a></li>
                                    <li><a class="dropdown-item" href="../paginalogin/logout.php">Cerrar sesion</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } else { ?>
                        
                        <div class="dropdown">
                            <button class="btn btn-succes btn-lg dropdown-toggle text-white " type="button" id="btn_session" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person "></i>
                            </button>
                            <ul class="dropdown-menu " aria-labelledby="btn_session">
                                <li><a class="dropdown-item" href="../paginalogin/register.php">Registrarse</a></li>
                                <li><a class="dropdown-item" href="../paginalogin/login.php">Iniciar Sesion</a></li>
                              
                            </ul>
                        </div>
                       
                    <?php } ?>
                </div>
            </nav>
        </div>  
    </div>
    <!-- Header Mobile -->
    <div class="wrap-header-mobile">
        <!-- Logo moblie -->		
        <div class="logo-mobile">
            <a href="index.php"><img src="../imagenes/logo.png" alt="IMG-LOGO"></a>
        </div>

        <!-- Icon header -->
        <div class="wrap-icon-header flex-w flex-r-m m-r-15">
            <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart" id="num_cart" data-notify="<?php echo $num_cart ?>">
                <i class="zmdi zmdi-shopping-cart"></i>
            </div>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <div class="dropdown">
                    <button class="btn btn-succes btn-md dropdown-toggle text-white " type="button" id="btn_session" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person"></i>
                    </button>
                    <ul class="dropdown-menu " aria-labelledby="btn_session">
                        <?php if($_SESSION['user_cliente'] == 1){ ?>
                            <li><a class="dropdown-item" href="../paginalogin/loginadmin/loginadmin.php">Panel de Administrador</a></li>
                            <li><a class="dropdown-item" href="../paginalogin/logout.php">Cerrar sesion</a></li>
                        <?php }else{ ?>
                            <li><a class="dropdown-item" href="miscompras.php">Mis compras</a></li>
                            <li><a class="dropdown-item" href="../paginalogin/logout.php">Cerrar sesion</a></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } else { ?>
                <div class="dropdown">
                    <button class="btn btn-succes btn-lg dropdown-toggle text-white " type="button" id="btn_session" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person "></i>
                    </button>
                    <ul class="dropdown-menu " aria-labelledby="btn_session">
                        <li><a class="dropdown-item" href="../paginalogin/register.php">Registrarse</a></li>
                        <li><a class="dropdown-item" href="../paginalogin/login.php">Iniciar Sesion</a></li>
                    </ul>
                </div>
            <?php } ?>
        </div>

        <!-- Button show menu -->
        <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </div>
    </div>


    <!-- Menu Mobile -->
    <div class="menu-mobile">
        <ul class="main-menu-m">
            <li class="<?= ($current_page == 'index.php') ? 'active-menu' : '' ?>">
                <a href="index.php">Inicio</a>
            </li>
            <li class="<?= ($current_page == 'product.php') ? 'active-menu' : '' ?>">
                <a href="product.php">Tienda</a>
            </li>
            <li class="<?= ($current_page == 'about.php') ? 'active-menu' : '' ?>">
                <a href="about.php">Sobre Nosotros</a>
            </li>
            <li class="<?= ($current_page == 'contact.php') ? 'active-menu' : '' ?>">
                <a href="contact.php">Contacto</a>
            </li>
        </ul>
    </div>	
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</header>
<?php include 'carritochico.php';?>