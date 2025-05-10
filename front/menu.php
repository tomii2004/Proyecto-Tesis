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
        .dropdown-item:hover {
            color: #ff6daf !important;    /* Color del texto al pasar el mouse */
        }
        .dropdown-toggle::after {
            display: none !important; /* Oculta la flecha */
        }
        .custom-dropdown {
            min-width: 220px;
           /*  max-height: 300px; ajustá a tu gusto */
            /* overflow-y: auto; */
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            top: calc(100% + 10px); /* Desplazamiento hacia abajo */
            left: 50%;
            transform: translateX(-50%);
            position: absolute;
            z-index: 1000;
            /* scrollbar-width: thin;       /* Firefox */
            /* scrollbar-color: #ff6daf #fff; */ 
        }

        /* Mejora de visibilidad en hover */
        @media (min-width: 992px) {
            .dropdown:hover .dropdown-menu {
                display: block;
                opacity: 1;
                pointer-events: auto;
            }

            .dropdown-menu {
                display: none;
                opacity: 0;
                pointer-events: none;
                transition: all 0.3s ease;
            }
        }

        /* Estilo de los ítems */
        .custom-dropdown .dropdown-item {
            text-align: center;
            font-size: 16px;
            font-weight: 500;
            padding: 0.6rem 1rem;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .custom-dropdown .dropdown-item:hover {
            background-color: #ffe1ed;
            color: #d63384;
            border-radius: 6px;
        }

        /* Para navegadores basados en WebKit como Chrome y Edge 
        .custom-dropdown::-webkit-scrollbar {
            width: 6px;
        }
        .custom-dropdown::-webkit-scrollbar-thumb {
            background-color: #ff6daf;
            border-radius: 10px;
        }*/

    </style>
</head>
<body>
    
</body>
</html>
<header>
    <?php 
    include_once  "../modelos/basededatos.php";
    $conexion = BasedeDatos::Conectar();
    
    $categorias = $conexion->query("SELECT c.ID_categoria, c.nombre, c.activo, COUNT(p.ID_producto) AS cantidad_productos FROM categoria c LEFT JOIN producto p ON c.ID_categoria = p.categoria WHERE c.activo = 1 GROUP BY c.ID_categoria, c.nombre, c.activo HAVING cantidad_productos > 0 ")->fetchAll(PDO::FETCH_ASSOC);
    ?>
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
                        <li class="nav-item dropdown position-relative <?= ($current_page == 'product.php') ? 'active-menu' : '' ?>">
                            <a class="nav-link dropdown-toggle" href="product.php" id="dropdownTienda" role="button">
                                Tienda
                            </a>
                            <ul class="dropdown-menu custom-dropdown" aria-labelledby="dropdownTienda">
                                <?php foreach($categorias as $cat){ ?>
                                    <li>
                                        <a class="dropdown-item" href="product.php?cat=<?= $cat['ID_categoria'] ?>">
                                            <?= ucfirst(htmlspecialchars($cat['nombre'])) ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
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

<script>
    //esto es para retardo en el select de la tienda
    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.querySelector('.nav-item.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        let timeout;

        dropdown.addEventListener('mouseenter', () => {
            clearTimeout(timeout);
            menu.style.display = 'block';
            menu.style.opacity = '1';
            menu.style.pointerEvents = 'auto';
        });

        dropdown.addEventListener('mouseleave', () => {
            timeout = setTimeout(() => {
                menu.style.display = 'none';
                menu.style.opacity = '0';
                menu.style.pointerEvents = 'none';
            }, 300); // Tiempo de retardo en milisegundos
        });
    });
</script>