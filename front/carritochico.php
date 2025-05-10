<div class="wrap-header-cart js-panel-cart">
    <div class="s-full js-hide-cart"></div>

    <div class="header-cart flex-col-l p-l-65 p-r-25">
        <div class="header-cart-title flex-w flex-sb-m p-b-8">
            <span class="mtext-103 cl13">Carrito de Compras</span>
            <div class="fs-35 lh-10 cl13 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                <i class="zmdi zmdi-close"></i>
            </div>
        </div>

        <div class="header-cart-content flex-w js-pscroll">
            <ul class="header-cart-wrapitem w-full" id="lista-carrito-flotante">
                <!-- Productos se agregarán dinámicamente -->
            </ul>

            <div class="w-full">
                <div class="header-cart-total w-full p-tb-40">
                    Total: <span id="total-flotante"><?php echo MONEY;?>0.00</span>
                </div>

                <div class="header-cart-buttons flex-w w-full">
                    <a href="#" id="vaciar-carrito" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                        Limpiar Carrito
                    </a>
                    <a href="carrito.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                        Ver Carrito
                    </a>
                    <?php if(isset($_SESSION['user_cliente'])) { ?>
                        <a href="pago.php" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                            Finalizar Compra
                        </a>
                    <?php } else { ?>
                        <a href="../paginalogin/login.php?pago" class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                            Finalizar Compra
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const MONEY = "<?php echo MONEY; ?>";

    document.addEventListener("DOMContentLoaded", function () {
        actualizarCarritoFlotante();

        document.getElementById("vaciar-carrito").addEventListener("click", function (e) {
            e.preventDefault();
            fetch('clases/vaciarcarrito.php', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        actualizarCarritoFlotante();
                    } else {
                        console.error("Error al vaciar el carrito:", data.error);
                    }
                })
                .catch(error => console.error("Error en la solicitud:", error));
        });
    });

    function actualizarCarritoFlotante() {
        fetch('clases/obtenercarritochico.php')
            .then(response => response.json())
            .then(data => {
                console.log("Carrito chico:", data);
                const listaCarrito = document.getElementById("lista-carrito-flotante");
                const totalFlotante = document.getElementById("total-flotante");
                listaCarrito.innerHTML = "";
                let total = 0;

                if (!Array.isArray(data) || data.length === 0) {
                    listaCarrito.innerHTML = '<li class="header-cart-item">Carrito vacío</li>';
                    totalFlotante.innerHTML = MONEY + "0.00";
                    return;
                }

                data.forEach(producto => {
                    const subtotal = producto.cantidad * producto.precio;
                    total += subtotal;

                    const itemHTML = `
                        <li class="header-cart-item flex-w flex-t m-b-12">
                            <div class="header-cart-item-img">
                                <img src="../${producto.ruta_imagen}" alt="IMG">
                            </div>
                            <div class="header-cart-item-txt p-t-8">
                                <span class="header-cart-item-name m-b-18 hov-cl1 trans-04">
                                    ${producto.nombre}<br>
                                    <small>Talla: ${producto.talla} - Color: ${producto.color}</small>
                                </span>
                                <span class="header-cart-item-info">
                                    ${producto.cantidad} x ${MONEY}${parseFloat(producto.precio).toFixed(2)}
                                </span>
                            </div>
                        </li>
                    `;
                    listaCarrito.insertAdjacentHTML('beforeend', itemHTML);
                });

                totalFlotante.innerHTML = MONEY + total.toFixed(2);
            })
            .catch(error => console.error("Error obteniendo el carrito:", error));
    }
</script>
