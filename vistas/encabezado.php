<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <title>Arena Panel</title>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries-->
    <!--if lt IE 9
    script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js')
    script(src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js')
    -->
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.1.0/ckeditor5.css" />
    <script src="https://kit.fontawesome.com/52d6698aac.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>
  <body class="sidebar-mini fixed">
    <div class="wrapper">
      <!-- Navbar-->
      <header class="main-header hidden-print"><a class="logo" href="?c=inicio">Arena Panel</a>
        <nav class="navbar navbar-static-top">
          <!-- Sidebar toggle button--><a class="sidebar-toggle" href="#" data-toggle="offcanvas"></a>
          <!-- Navbar Right Menu-->
          <div class="navbar-custom-menu">
            <ul class="top-nav">
              <!--Notification Menu-->
              <!-- <li class="dropdown notification-menu"><a class="dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-bell-o fa-lg"></i></a>
                <ul class="dropdown-menu">
                  <li class="not-head">You have 4 new notifications.</li>
                  <li><a class="media" href="javascript:;"><span class="media-left media-icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-envelope fa-stack-1x fa-inverse"></i></span></span>
                      <div class="media-body"><span class="block">Lisa sent you a mail</span><span class="text-muted block">2min ago</span></div></a></li>
                  <li><a class="media" href="javascript:;"><span class="media-left media-icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-danger"></i><i class="fa fa-hdd-o fa-stack-1x fa-inverse"></i></span></span>
                      <div class="media-body"><span class="block">Server Not Working</span><span class="text-muted block">2min ago</span></div></a></li>
                  <li><a class="media" href="javascript:;"><span class="media-left media-icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-success"></i><i class="fa fa-money fa-stack-1x fa-inverse"></i></span></span>
                      <div class="media-body"><span class="block">Transaction xyz complete</span><span class="text-muted block">2min ago</span></div></a></li>
                  <li class="not-footer"><a href="#">See all notifications.</a></li>
                </ul>
              </li> -->
              <!-- User Menu-->
              <li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i style="padding-right:5px;" class="fa fa-user fa-lg"></i><?php echo $_SESSION['user_name'];?><i class="fa fa-caret-down" style="padding-left:5px;"></i></a>
                <ul class="dropdown-menu settings-menu">
                  <li><a href="?c=inicio&a=FormEditarPassword&id=<?php echo $_SESSION['user_id']; ?>"><i class="fa fa-user fa-lg"></i> Perfil </a></li>
                  <li><a href="?c=inicio&a=LogOut"><i class="fa fa-sign-out fa-lg"></i> Cerrar Sesión</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Side-Nav-->
      <aside class="main-sidebar hidden-print">
        <section class="sidebar">
          <div class="user-panel">
            <div class="pull-left image"><img class="img-circle" src="imagenes/logo.png" alt="User Image"></div>
            <div class="pull-left info">
              <p>Arena</p>
            </div>
          </div>
          <!-- Sidebar Menu-->
          <ul class="sidebar-menu">
            <li class="treeview"><a href="?c=inicio"><i class="fa fa-dashboard"></i><span>Inicio</span></a></li>
            <li class="treeview"><a href="?c=producto"><i class="fa fa-solid fa-shirt"></i><span>Productos</span><i class="fa fa-angle-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="?c=producto"><i class="fa-solid fa-list-ul"></i> Listado</a></li>
                <li><a href="?c=producto&a=FormCrear"><i class="fa fa-plus"></i> Registrar </a></li>
              </ul>
            </li>
            <!-- <li><a href="charts.html"><i class="fa fa-pie-chart"></i><span>Charts</span></a></li> -->
            <li class="treeview"><a href="?c=usuarios"><i class=" fa fa-solid fa-users "></i><span>Usuarios</span></a></li>
            <!-- <li class="treeview"><a href="#"><i class="fa fa-th-list"></i><span>Tables</span><i class="fa fa-angle-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="table-basic.html"><i class="fa fa-circle-o"></i> Basic Tables</a></li>
                <li><a href="table-data-table.html"><i class="fa fa-circle-o"></i> Data Tables</a></li>
              </ul>
            </li> -->
            
            <li class="treeview"><a href="?c=compras"><i class="fa fa-solid fa-cart-shopping"></i><span>Ventas</span></a>
            <li class="treeview"><a href="?c=categorias"><i class="fa fa-solid fa-table-list" style="color: white;"></i><span>Categorias</span></a>
            <li class="treeview"><a href="?c=variantes"><i class="fa fa-solid fa-keyboard" style="color: white;"></i><span>Variantes</span></a>
            <li class="treeview"><a href="?c=preciosenvios"><i class="fa fa-solid fa-truck" style="color: white;"></i><span>Precios de envios</span></a>
            <li class="treeview"><a href="?c=configuracion"><i class="fa fa-solid fa-gear" style="color: white;"></i><span>Configuracion</span></a>
            <!-- <li class="treeview"><a href="?c=contacto"><i class="fa fa-solid fa-phone"></i><span>Contacto</span></a> -->
              <!-- <ul class="treeview-menu">
                <li><a href=""><i class="fa fa-circle-o"></i> Contactarse</a></li>
                <li><a href="page-login.html"><i class="fa fa-circle-o"></i> Login Page</a></li>
                <li><a href="page-lockscreen.html"><i class="fa fa-circle-o"></i> Lockscreen Page</a></li>
                <li><a href="page-user.html"><i class="fa fa-circle-o"></i> User Page</a></li>
                <li><a href="page-invoice.html"><i class="fa fa-circle-o"></i> Invoice Page</a></li>
                <li><a href="page-calendar.html"><i class="fa fa-circle-o"></i> Calendar Page</a></li>
                <li><a href="page-mailbox.html"><i class="fa fa-circle-o"></i> Mailbox</a></li>
                <li><a href="page-error.html"><i class="fa fa-circle-o"></i> Error Page</a></li>
              </ul> -->
            </li>
            <!-- <li class="treeview"><a href="#"><i class="fa fa-share"></i><span>Multilevel Menu</span><i class="fa fa-angle-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="blank-page.html"><i class="fa fa-circle-o"></i> Level One</a></li>
                <li class="treeview"><a href="#"><i class="fa fa-circle-o"></i><span> Level One</span><i class="fa fa-angle-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="blank-page.html"><i class="fa fa-circle-o"></i> Level Two</a></li>
                    <li><a href="#"><i class="fa fa-circle-o"></i><span> Level Two</span></a></li>
                  </ul>
                </li>
              </ul>
            </li> -->
          </ul>
        </section>
      </aside>
    </body>
</html>
<script>
  //PARA IR CAMBIANDO EL SELECTOR DE DONDE ESTAS PARADO EN LA SLIDEBAR(BARRA LATERAL)
  document.addEventListener("DOMContentLoaded", function() {
    // Obtener la URL actual
    const currentUrl = window.location.href;

    // Obtener todos los elementos de la barra lateral
    const menuItems = document.querySelectorAll('.sidebar-menu li a');

    // Recorrer todos los enlaces del menú
    menuItems.forEach(function(item) {
        // Si la URL del enlace coincide con la URL actual, agregar la clase 'active'
        if (currentUrl.includes(item.getAttribute('href'))) {
            item.parentElement.classList.add('active');  // Agregar la clase 'active' al <li> contenedor
        } else {
            item.parentElement.classList.remove('active');  // Remover la clase 'active' de otros elementos
        }
    });
});
</script>