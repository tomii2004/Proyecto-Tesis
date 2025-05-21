<!DOCTYPE html>
<html lang="en">

<head>
    <title>Carrito de Compras</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    

    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <!--===============================================================================================-->
    <!--===============================================================================================-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="../imagenes/favicon-32x32.png" />
    <!--===============================================================================================-->
    <!-- <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css"> -->
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/linearicons-v1.0.0/icon-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <!--===============================================================================================-->
</head>

<body class="animsition">

    <?php
    include '../modelos/basededatos.php';

    $conexion = BasedeDatos::Conectar();
    
    require '../modelos/configproduct-detail.php';

    ///////////// MERCADO PAGO ////////////
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    // Desactivar solo los warnings deprecados (E_DEPRECATED):
    error_reporting(E_ALL & ~E_DEPRECATED);
    
    require_once 'vendor/autoload.php';
    
    // Importa las clases necesarias del SDK de MercadoPago
    use MercadoPago\MercadoPagoConfig;
    use MercadoPago\Client\Preference\PreferenceClient;
    use MercadoPago\Exceptions\MPApiException;
    MercadoPagoConfig::setAccessToken(TOKEN_MP);
    $client = new PreferenceClient();
    ///////////////////////////////////////

    // 1) Leemos el costo de envío enviado desde carrito (si no viene, 0.00)
    $envio = isset($_POST['envio']) ? floatval($_POST['envio']) : 0.00;

    //Guardo el envio en la sesion
    $_SESSION['envio'] = $envio;

    $productos = isset($_SESSION['carrito']['variantes']) ? $_SESSION['carrito']['variantes'] : null;

    if ($productos == null) {
        header("Location: index.php");
        exit;
    }

    // 2) Recuperamos cada variante, calculamos subtotal de productos y armamos $lista_carrito
    $lista_carrito   = array();
    $total_sin_envio = 0.00;
    $productos_mp    = array();

    foreach ($productos as $clave => $info) {
        $id_variante = $info['id_variante'];
        $cantidad    = $info['cantidad'];

        $consulta = $conexion->prepare("
            SELECT p.ID_producto, p.nombre, p.ruta_imagen,
                   v.precio, v.ID_producvar, v.ID_talla, v.ID_color,t.nombre AS 'talle',c.nombre AS 'color'
            FROM producto p
            INNER JOIN productos_variantes v ON p.ID_producto = v.ID_producto
            INNER JOIN c_talla t ON v.ID_talla = t.ID_talla
            INNER JOIN c_colores c ON v.ID_color = c.ID_colores
            WHERE v.ID_producvar = ?
        ");
        $consulta->execute([$id_variante]);
        $datos = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($datos) {
            $datos['cantidad'] = $cantidad;
            $datos['clave']    = $clave;
            $lista_carrito[]   = $datos;

            // Acumular subtotal de productos
            $subtotal = $datos['precio'] * $cantidad;
            $total_sin_envio += $subtotal;

            // Agregar ítem de producto para MP
            $productos_mp[] = [
                "id"          => $datos['clave'], 
                "title"       => $datos['nombre'] . " - Talle " . $datos['talle'] . " / Color " . $datos['color'],
                "quantity"    => (int) $cantidad,
                "unit_price"  => (float) $datos['precio'],
                "currency_id" => "ARS"
            ];
        }
    }

    // 3) Creamos la preferencia incluyendo “shipments” para el envío
    $preference_data = [
        "items"     => $productos_mp,
        "shipments" => [
            "cost" => (float) $envio
        ],
        "back_urls" => [
            "success" => "https://f59f-181-91-7-178.ngrok-free.app/front/clases/captura_MP.php",
            "failure" => "https://f59f-181-91-7-178.ngrok-free.app/front/clases/failure.php",
            "pending" => "https://f59f-181-91-7-178.ngrok-free.app/front/clases/pending.php"
        ],
        "auto_return" => "approved"
    ];

    try {
        $preference = $client->create($preference_data);
    } catch (MPApiException $e) {
        echo "Error al crear la preferencia:<br>";
        echo "<pre>" . print_r($e->getApiResponse()->getContent(), true) . "</pre>";
        exit;
    } catch (Exception $e) {
        echo "Error al crear la preferencia: " . $e->getMessage();
        exit;
    }

    $preferenceId = $preference ? $preference->id : null;
    
    
    ?>

    <?php include 'menu.php'; ?>

    <!-- breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
                Inicio
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>
            <span class="stext-109 cl4">Carrito de Compras</span>
        </div>
    </div>

    <!-- Shoping Cart -->
    <form class="bg0 p-t-75 p-b-10">
        <div class="container">
            <div class="row">
                <!-- ===== Lista de Productos ===== -->
                <div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
                    <div class="m-l-25 m-r--38 m-lr-0-xl">
                        <div class="wrap-table-shopping-cart">
                            <table class="table-shopping-cart">
                                <tr class="table_head">
                                    <th class="column-1">Producto</th>
                                    <th class="column-2"></th>
                                    <th class="column-5">Subtotal</th>
                                </tr>
                                <?php
                                if ($lista_carrito == null) {
                                    echo '<tr><td colspan="5" class="txt-center"><b>Lista Vacía</b></td></tr>';
                                } else {
                                    foreach ($lista_carrito as $producto) {
                                        $id       = $producto['ID_producto'];
                                        $nombre   = $producto['nombre'];
                                        $precio   = $producto['precio'];
                                        $cantidad = $producto['cantidad'];
                                        $imagen   = '../' . $producto['ruta_imagen'];
                                        $subtotal = $cantidad * $precio;
                                ?>
                                <tr class="table_row">
                                    <td class="column-1">
                                        <div class="how-itemcart1">
                                            <img src="<?php echo $imagen ?>" alt="IMG">
                                        </div>
                                    </td>
                                    <td class="column-2">
                                        <?php echo $nombre; ?><br>
                                        <small><strong>Talle:</strong> <?php echo $producto['talle']; ?> | <strong>Color:</strong> <?php echo $producto['color']; ?></small>
                                    </td>
                                    <td class="column-5">
                                        <div id="subtotal_<?php echo $id ?>" name="subtotal[]">
                                            <?php echo MONEY; ?> <?php echo number_format($subtotal, 2, '.', ','); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                    } // foreach
                                } // else
                                ?>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ===== Resumen de Totales y Envío ===== -->
                <div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-10">
                    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                        <h4 class="mtext-109 cl13 p-b-30">Carrito de Compras</h4>

                        <!-- Total sin envío -->
                        <div class="flex-w flex-t bor12 p-b-13">
                            <div class="size-208">
                                <span class="stext-110 cl13">Total:</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl13" id="total">
                                    <?php echo MONEY . number_format($total_sin_envio, 2, '.', ','); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Costo de envío (sólo se muestra si viene > 0) -->
                        <?php if ($envio > 0): ?>
                        <div class="flex-w flex-t bor12 p-b-13">
                            <div class="size-208">
                                <span class="stext-110 cl13">Costo de envío:</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl13" id="costoEnvioPago">
                                    <?php echo MONEY . number_format($envio, 2, '.', ','); ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Total con envío -->
                        <div class="flex-w flex-t bor12 p-b-13">
                            <div class="size-208">
                                <span class="stext-110 cl13">Total con envío:</span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl13" id="totalConEnvioPago">
                                    <?php
                                        $total_con_envio = $total_sin_envio + $envio;
                                        echo MONEY . number_format($total_con_envio, 2, '.', ',');
                                    ?>
                                </span>
                            </div>
                        </div>

                        <!-- ===== Campos para Dirección de Envío ===== -->
                        <div class="mt-4">
                            <h5 class="mb-3">Dirección de envío</h5>
                            <div class="mb-3">
                                <label for="calleEnvio" class="form-label">Calle:</label>
                                <input type="text" id="calleEnvio" class="form-control"
                                    placeholder="Ej: Av. Santa Fe">
                            </div>
                            <div class="mb-3">
                                <label for="numeroEnvio" class="form-label">Número:</label>
                                <input type="text" id="numeroEnvio" class="form-control" placeholder="Ej: 742">
                            </div>
                            <div class="mb-3">
                                <label for="codigoPostalEnvio" class="form-label">Código Postal:</label>
                                <input type="text" id="codigoPostalEnvio" class="form-control" placeholder="Ej: S2000">
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
            
        </div>
    </form>
    <!-- ===== AQUÍ VA EL BOTÓN DE MERCADO PAGO (checkout pro) ===== -->
    <div class="container"> 
      <div class="row justify-content-end">
        <div class="col-md-4 p-b-10">
          <!-- Este es el contenedor que usamos para renderizar Checkout Pro -->
          <div id="wallet_container"></div>
        </div>
      </div>
    </div>

    <?php include 'footer.php'; ?>
    

    <!--============================= MERCADO PAGO ==================================================-->
    <!-- Carga el SDK de Mercado Pago v2 -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script>
    // Inicializa Mercado Pago con tu PUBLIC_KEY
    const mp = new MercadoPago("<?php echo PUBLIC_KEY_MP ?>", {
        locale: 'es-AR'
    });

    // Crea el Checkout Pro y coloca el botón en #wallet_container
    mp.checkout({
        preference: {
            id: "<?php echo $preferenceId; ?>"
        },
        render: {
            container: "#wallet_container"// ID del contenedor donde aparecerá el botón
            // label: "Pagar con Mercado Pago"
        }
    });

      // Espera que cargue el DOM
    document.addEventListener('DOMContentLoaded', function() {
        const btnContainer = document.querySelector('#wallet_container');

        // Estado inicial: bloqueamos la interacción hasta que el usuario complete la dirección
        btnContainer.style.pointerEvents = 'none';
        btnContainer.style.opacity = '0.5';

        const calleInput         = document.getElementById('calleEnvio');
        const numeroInput        = document.getElementById('numeroEnvio');
        const codigoPostalInput  = document.getElementById('codigoPostalEnvio');

        // Función que habilita o deshabilita el botón de MP según la validación de campos
        function validarCampos() {
            const calleVal = calleInput.value.trim();
            const numeroVal = numeroInput.value.trim();
            const cpVal = codigoPostalInput.value.trim();

            if (calleVal !== '' && numeroVal !== '' && cpVal !== '') {
                btnContainer.style.pointerEvents = 'auto';
                btnContainer.style.opacity = '1';
            } else {
                btnContainer.style.pointerEvents = 'none';
                btnContainer.style.opacity = '0.5';
            }
        }

        // Cada vez que cambie alguno de los tres inputs, validamos
        [calleInput, numeroInput, codigoPostalInput].forEach(input => {
            input.addEventListener('input', validarCampos);
        });

        validarCampos(); // validación inicial al cargar

        // Cuando el usuario finalmente hace clic sobre el botón (container) de MercadoPago,
        // vamos a tomar los valores de los 3 campos y enviarlos a guardar_direccion_sesion.php
        btnContainer.addEventListener('click', function() {
            const calle = calleInput.value.trim();
            const numero = numeroInput.value.trim();
            const codigopostal = codigoPostalInput.value.trim();

            fetch('clases/guardar_direccion_sesion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    calle: calle,
                    numero: numero,
                    codigopostal: codigopostal
                })
            })
            // No bloqueamos el flujo; asumimos que se guardará rápido en sesión.
            .then(response => {
                if (!response.ok) {
                    console.error('Error guardando dirección en sesión');
                }
                return response.json();
            })
            .catch(err => {
                console.error('Error en fetch de guardar_direccion_sesion:', err);
            });
        });
    });
</script>

    </script>

    <!--===============================================================================================-->
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/animsition/js/animsition.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/select2/select2.min.js"></script>
    <script>
    $(".js-select2").each(function() {
        $(this).select2({
            minimumResultsForSearch: 20,
            dropdownParent: $(this).next('.dropDownSelect2')
        });
    });
    </script>
    <!--===============================================================================================-->
    <script src="vendor/MagnificPopup/jquery.magnific-popup.min.js"></script>
    <!--===============================================================================================-->
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script>
    $('.js-pscroll').each(function() {
        $(this).css('position', 'relative');
        $(this).css('overflow', 'hidden');
        var ps = new PerfectScrollbar(this, {
            wheelSpeed: 1,
            scrollingThreshold: 1000,
            wheelPropagation: false,
        });
        $(window).on('resize', function() {
            ps.update();
        });
    });
    </script>
    <!--===============================================================================================-->
    <script src="js/main.js"></script>

</body>

</html>