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

    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="../imagenes/favicon-32x32.png" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
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

    $productos = isset($_SESSION['carrito']['variantes']) ? $_SESSION['carrito']['variantes'] : [];
    // echo '<pre>';
    // print_r($_SESSION);
    // echo '</pre>';
    
    $lista_carrito = array();
    if ($productos != null) {
        foreach ($productos as $clave => $cantidad) {
            // Separar ID_producto e ID_variante
            $partes      = explode('-', $clave);
            $id_producto = $partes[0];
            $id_talla    = $partes[1];
            $id_color    = $partes[2];

            $id_variante = isset($cantidad['id_variante']) ? $cantidad['id_variante'] : null;
            $cantidad    = isset($cantidad['cantidad']) ? intval($cantidad['cantidad']) : 1;
        
            // Traer los datos del producto
            $consulta_productos = $conexion->prepare("
                SELECT p.*, pv.precio, pv.stock, pv.ID_producvar, t.nombre AS talla, c.nombre AS color, :cantidad AS cantidad
                FROM producto p
                JOIN productos_variantes pv ON pv.ID_producto = p.ID_producto
                LEFT JOIN c_talla t ON t.ID_talla = pv.ID_talla
                LEFT JOIN c_colores c ON c.ID_colores = pv.ID_color
                WHERE p.ID_producto = :id_producto
                  AND pv.ID_producvar = :id_variante
                  AND p.estado = 1
            ");
            $consulta_productos->execute([
                ':id_producto' => $id_producto,
                ':id_variante' => $id_variante,
                ':cantidad'    => $cantidad
            ]);
            $producto = $consulta_productos->fetch(PDO::FETCH_ASSOC);
            if ($producto) {
                $producto['clave']    = $clave;    // Guardamos la clave completa para usar en el ID/JS
                $producto['cantidad'] = $cantidad;
                $lista_carrito[]      = $producto;
            }
        }
    }

    include 'menu.php';
    ?>

    <!-- breadcrumb -->
    <div class="container">
        <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
            <a href="index.php" class="stext-109 cl8 hov-cl1 trans-04">
                Inicio
                <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
            </a>

            <span class="stext-109 cl4">
                Carrito de Compras
            </span>
        </div>
    </div>

    <!-- Shoping Cart -->
    <!-- CAMBIAMOS ESTE <form> EXTERIOR POR UN <div> PARA EVITAR NESTED FORMS -->
    <div class="bg0 p-t-75 p-b-85">
        <div class="container">
            <div class="row">
                <!-- ===== Lista de Productos en Carrito ===== -->
                <div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
                    <div class="m-l-25 m-r--38 m-lr-0-xl">
                        <div class="wrap-table-shopping-cart">
                            <table class="table-shopping-cart">
                                <tr class="table_head">
                                    <th class="column-1">Producto</th>
                                    <th class="column-2"></th>
                                    <th class="column-3">Precio</th>
                                    <th class="column-4">Cantidad</th>
                                    <th class="column-5">Subtotal</th>
                                    <th class="column-5"></th>
                                </tr>
                                <?php
                                if ($lista_carrito == null) {
                                    echo '<tr><td colspan="6" class="txt-center"><b>Lista Vacía</b></td></tr>';
                                    $total = 0.00;
                                } else {
                                    $total = 0.00;
                                    foreach ($lista_carrito as $producto) {
                                        $id       = $producto['clave'];
                                        $nombre   = $producto['nombre'];
                                        $precio   = $producto['precio'];
                                        $cantidad = $producto['cantidad'];
                                        $imagen   = '../' . $producto['ruta_imagen'];
                                        $subtotal = $cantidad * $precio;
                                        $total   += $subtotal;
                                        $talle    = $producto['talla'];
                                        $color    = $producto['color'];

                                        $id_html = str_replace('|', '_', $id);
                                ?>
                                <tr class="table_row">
                                    <td class="column-1">
                                        <div class="how-itemcart1">
                                            <img src="<?php echo $imagen ?>" alt="IMG">
                                        </div>
                                    </td>
                                    <td class="column-2 js-name-detail">
                                        <?php echo $nombre; ?><br>
                                        <span class="stext-111 cl6"><strong>Talle:</strong> <?php echo $talle; ?></span><br>
                                        <span class="stext-111 cl6"><strong>Color:</strong> <?php echo $color; ?></span>
                                    </td>
                                    <td class="column-3">
                                        <?php echo MONEY; ?><?php echo number_format($precio, 2, '.', ','); ?>
                                    </td>
                                    <td class="column-4">
                                        <div class="quantity-selector">
                                            <div onclick="cambiarCantidad(-1, '<?php echo $id; ?>')">
                                                <i class="decrement fs-16 zmdi zmdi-minus"></i>
                                            </div>

                                            <input
                                                class="mtext-104 cl3 txt-center num-product"
                                                type="number"
                                                name="num-product1"
                                                max="10"
                                                min="1"
                                                value="<?php echo $cantidad; ?>"
                                                id="cantidad_<?php echo $id_html; ?>"
                                                onchange="actualizaCantidad(this.value,'<?php echo $id; ?>')"
                                            >

                                            <div onclick="cambiarCantidad(1, '<?php echo $id; ?>')">
                                                <i class="increment fs-16 zmdi zmdi-plus"></i>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="column-5">
                                        <div id="subtotal_<?php echo $id_html ?>" name="subtotal[]">
                                            <?php echo MONEY; ?><?php echo number_format($subtotal, 2, '.', ','); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a
                                            id="eliminar"
                                            class="btn btn-danger btn-sm text-white"
                                            data-bs-id="<?php echo $id; ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#eliminaModal"
                                        >Eliminar</a>
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
                <div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">
                    <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm">
                        <h4 class="mtext-109 cl13 p-b-30">
                            Carrito de Compras
                        </h4>

                        <!-- Total sin envío -->
                        <div class="flex-w flex-t bor12 p-b-13">
                            <div class="size-208">
                                <span class="stext-110 cl13">
                                    Total:
                                </span>
                            </div>
                            <div class="size-209">
                                <span class="mtext-110 cl13" id="total">
                                    <?php
                                        if (isset($total)) {
                                            echo MONEY . number_format($total, 2, '.', ',');
                                        } else {
                                            echo MONEY . '0.00';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>

                        <!-- === SECCIÓN ENVÍO === -->
                        <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                            <div class="size-208 w-full-ssm">
                                <span class="stext-110 cl13">Envío:</span>
                            </div>
                            <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                                <span class="stext-112 cl8">Calcular Envío</span>

                                <!-- Provincia -->
                                <div class="bor8 bg0 m-b-12">
                                    <select id="provinciaEnvio" class="stext-111 cl8 plh3 size-111 p-lr-15">
                                        <option value="">Seleccionar provincia</option>
                                        <?php
                                        $prov = $conexion->query("SELECT id, provincia FROM provincias ORDER BY provincia");
                                        foreach ($prov->fetchAll(PDO::FETCH_ASSOC) as $p) {
                                            echo "<option value='{$p['id']}'>{$p['provincia']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Localidad -->
                                <div class="bor8 bg0 m-b-12">
                                    <select id="localidadEnvio" class="stext-111 cl8 plh3 size-111 p-lr-15" disabled>
                                        <option value="">Seleccione provincia primero</option>
                                    </select>
                                </div>

                                <!-- Costo envío -->
                                <div class="bor8 bg0 m-b-22">
                                    <input
                                        type="text"
                                        id="costoEnvio"
                                        class="stext-111 cl8 plh3 size-111 p-lr-15"
                                        placeholder="Costo de Envío"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Total con Envío -->
                        <div class="flex-w flex-t p-t-27 p-b-33">
                            <div class="size-208">
                                <span class="mtext-101 cl13">Total con envío:</span>
                            </div>
                            <div class="size-209 p-t-1">
                                <span class="mtext-110 cl13" id="totalConEnvio">
                                    <?php
                                        // Al cargar, inicialmente se muestra igual que el total (sin envío)
                                        if (isset($total)) {
                                            echo MONEY . number_format($total, 2, '.', ',');
                                        } else {
                                            echo MONEY . '0.00';
                                        }
                                    ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($lista_carrito != null): ?>
                            <?php if (isset($_SESSION['user_cliente'])): ?>
                                <!-- Formulario que envía el valor de envío a pago.php -->
                                <form id="formPagar" action="pago.php" method="POST">
                                    <!-- Campo oculto para enviar el costo de envío -->
                                    <input type="hidden" name="envio" id="envioOculto" value="0">
                                    <button id="btnProcesarPago" type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer" disabled>
                                        Procesar Pago
                                    </button>
                                </form>
                            <?php else: ?>
                                <a
                                    href="../paginalogin/login.php?pago"
                                    class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer"
                                >
                                    Procesar Pago
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN DEL <div> QUE REEMPLAZA AL <form> EXTERIOR -->

    <?php include 'footer.php'; ?>

    <!-- Modal Eliminar Producto -->
    <div class="modal fade" id="eliminaModal" tabindex="-1" aria-labelledby="eliminaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="eliminaModalLabel">Alerta</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Desea eliminar el producto del carrito?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btn-elimina" type="button" class="btn btn-danger" onclick="eliminar()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    <!--===============================================================================================-->

    <!-- Scripts de cantidad, eliminar y actualización -->
    <script>
        function cambiarCantidad(cantidad, id) {
            let id_html = id.replace('|', '_');
            let inputCantidad = document.getElementById("cantidad_" + id_html);
            let cantidadActual = parseInt(inputCantidad.value);

            let nuevaCantidad = cantidadActual + cantidad;
            if (nuevaCantidad < 1) return;

            inputCantidad.value = nuevaCantidad;
            actualizaCantidad(nuevaCantidad, id, cantidadActual);
        }
    </script>

    <script>
        let eliminaModal = document.getElementById('eliminaModal');
        eliminaModal.addEventListener('show.bs.modal', function(event) {
            let button = event.relatedTarget;
            let id = button.getAttribute('data-bs-id');
            let buttonElimina = eliminaModal.querySelector('.modal-footer #btn-elimina');
            buttonElimina.value = id;
        });

        const MONEDA = "<?php echo MONEY; ?>";

        function actualizaCantidad(cantidad, id, cantidadAnterior) {
            if (cantidad < 1) {
                alert("La cantidad debe ser al menos 1.");
                return;
            }

            const id_html = id.replace('|', '_');
            let inputCantidad = document.getElementById("cantidad_" + id_html);

            let url = 'clases/actualizarcarrito.php';
            let formData = new FormData();
            formData.append('id_variante', id);
            formData.append('action', 'agregar');
            formData.append('cantidad', cantidad);

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    // Actualizar cantidad en el input
                    inputCantidad.value = cantidad;

                    // Actualizar subtotal en pantalla
                    let divsubtotal = document.getElementById("subtotal_" + id_html);
                    divsubtotal.innerHTML = MONEDA + data.sub;

                    // Recalcular total (solo productos) y actualizar en pantalla
                    let totalProductos = 0.00;
                    let list = document.getElementsByName('subtotal[]');
                    for (let i = 0; i < list.length; i++) {
                        let valor = list[i].innerHTML.replace(/[^\d.-]/g, '');
                        totalProductos += parseFloat(valor);
                    }
                    totalProductos = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(totalProductos);
                    document.getElementById('total').innerHTML = MONEDA + totalProductos;

                    // Además, recalcular “Total con envío”: tomamos el valor actual del envío
                    let envioActual = parseFloat(
                        document.getElementById('costoEnvio').value.replace(/[^0-9\.-]/g, '')
                    ) || 0;
                    let totalConEnvio = (parseFloat(totalProductos.replace(/,/g, '')) + envioActual).toFixed(2);
                    document.getElementById('totalConEnvio').innerHTML = MONEDA + totalConEnvio;

                    // Actualizar el campo oculto “envioOculto” en el formulario de pago
                    document.getElementById('envioOculto').value = envioActual.toFixed(2);
                } else {
                    // Si no hay stock o error, restablecer cantidad anterior
                    inputCantidad.value = cantidadAnterior;
                    let nameProductError = $(".js-name-detail").html();
                    Swal.fire({
                        html: nameProductError + "<br><br>¡No hay suficientes existencias!",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
                inputCantidad.value = cantidadAnterior;
            });
        }

        function eliminar() {
            let botonElimina = document.getElementById("btn-elimina");
            let id = botonElimina.value;
            let url = 'clases/actualizarcarrito.php';
            let formData = new FormData();

            formData.append('id_variante', id);
            formData.append('action', 'eliminar');

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error("Error al eliminar producto:", error);
            });
        }
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
    })
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
        })
    });
    </script>
    <!--===============================================================================================-->
    <script src="js/main.js"></script>
    <!--===============================================================================================-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(function() {
        // Guarda subtotal inicial en JS (solo productos, sin envío)
        const subtotal = <?php echo isset($total) ? number_format($total,2,'.','') : '0.00'; ?>;

        // 1) Al cambiar Provincia → cargo Localidades
        $('#provinciaEnvio').change(function() {
            const idProv = $(this).val();
            $('#localidadEnvio')
                .prop('disabled', !idProv)
                .html('<option>Cargando…</option>');
            $('#costoEnvio').val('');
            $('#totalConEnvio').text('<?php echo MONEY;?>' + subtotal.toFixed(2));
            $('#envioOculto').val('0.00');
            if (!idProv) return;

            $.getJSON('clases/ajaxlocalidades.php', {
                id_provincia: idProv
            })
            .done(function(list) {
                let opts = '<option value="">Seleccionar localidad</option>';
                list.forEach(o => {
                    opts += `<option value="${o.id_localidad}" data-costo="${o.costo_envio}">
                        ${o.localidad}
                    </option>`;
                });
                $('#localidadEnvio').html(opts);
            })
            .fail(() => Swal.fire('Error', 'No se cargaron localidades', 'error'));
        });

        // 2) Al cambiar Localidad → muestro costo y sumo
        $('#localidadEnvio').change(function() {
            const opt   = $(this).find('option:selected'),
                  costo = parseFloat(opt.data('costo')) || 0;
            $('#costoEnvio').val('<?php echo MONEY;?>' + costo.toFixed(2));

            // Calculamos total con envío
            const totalConEnvio = subtotal + costo;
            $('#totalConEnvio').text('<?php echo MONEY;?>' + totalConEnvio.toFixed(2));

            // Guardamos el envío en el campo oculto para enviarlo al pago
            $('#envioOculto').val(costo.toFixed(2));
        });
    });

	$(function() {
		const btnPago = $('#btnProcesarPago');
		const selectProv = $('#provinciaEnvio');
		const selectLoc  = $('#localidadEnvio');

		// Función que habilita o deshabilita el botón
		function verificarSeleccion() {
			const prov = selectProv.val();
			const loc  = selectLoc.val();
			// habilita si ambos tienen valor, sino deshabilita
			btnPago.prop('disabled', !(prov && loc));
		}

		// Llamar cada vez que cambie Provincia o Localidad
		selectProv.on('change', function() {
			verificarSeleccion();
		});

		selectLoc.on('change', function() {
			// tu código existente de actualización de costo/envío...
			verificarSeleccion();
		});

		// Al cargar la página, por si recarga con valores previos
		verificarSeleccion();
	});


	$('#formPagar').on('submit', function(e) {
		const prov = $('#provinciaEnvio').val();
		const loc  = $('#localidadEnvio').val();

		if (!prov || !loc) {
			e.preventDefault();
			alert('Por favor seleccioná provincia y localidad antes de procesar el pago.');
			// opcional: volver a deshabilitar el botón
			$('#btnProcesarPago').prop('disabled', true);
		}
	});
    </script>
</body>



</html>