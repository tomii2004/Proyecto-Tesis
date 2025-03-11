<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="assets/css/mainmio.css">
  
</head>
<body>
  <div class="content-wrapper">
    <div class="page-title">
      <div>
        <h1>Productos</h1>
        <ul class="breadcrumb side">
          <li><i class="fa fa-home fa-lg"></i></li>
          <li>Productos</li>
          <li class="active"><a href="#">Control de Stock</a></li>
        </ul>
      </div>
      <div>
        <a class="btn btn-primary btn-flat" href="?c=producto&a=FormCrear"><i class="fa fa-lg fa-plus"></i></a>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <div style="display: inline-block;position: relative; width: auto;margin-bottom: 20px;">
                <input type="text" id="buscador" class="form-control form-control-sm w-50" placeholder="Buscar productos...">
                <button type="button" id="clear-buscador" class="btnclear">✖</button>
              </div>
              <table class="table table-hover table-bordered" id="tablaproductos">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Talle</th>
                    <th>Color</th>
                    <th>Estado</th>
                    <th>Género</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($productos as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r->ID_producto) ?></td>
                    <td><?= htmlspecialchars($r->nombre) ?></td>
                    <td><?= htmlspecialchars($r->precio)?></td>
                    <td>
                      <div class="stock-buttons">
                        <span id="stock-<?= htmlspecialchars($r->ID_producto) ?>"> <?= htmlspecialchars($r->stock) ?> </span>
                        <button class="btn-custom max" onclick="modificarStock(<?= htmlspecialchars($r->ID_producto) ?>, 'incrementar')">+</button>
                        <button class="btn-custom min " onclick="modificarStock(<?= htmlspecialchars($r->ID_producto) ?>, 'decrementar')">-</button>
                      </div>
                    </td>
                    <td><?= htmlspecialchars(ucfirst($r->tallas_nombre)) ?></td>
                    <td><?= htmlspecialchars($r->colores_nombre) ?></td>
                    <td class="estado-<?= htmlspecialchars($r->ID_producto) ?> <?= $r->estado == 1 ? 'text-activo' : 'text-inactivo' ?>">
                      <span id="estado-<?= htmlspecialchars($r->ID_producto) ?>">
                        <?= $r->estado == 1 ? 'Activo' : 'Inactivo' ?>
                      </span>
                    </td>
                    <td><?= htmlspecialchars($r->genero) ?></td>
                    <td><?= htmlspecialchars($r->categoria_nombre) ?></td>
                    <td><?= $r->descripcion ?></td>
                    <td>
                      <?php if (!empty($r->ruta_imagen)): ?>
                      <img src="<?= htmlspecialchars($r->ruta_imagen) ?>" alt="Imagen del producto" style="width: 100px; height: auto;">
                      <?php else: ?>
                      Sin Imagen
                      <?php endif; ?>
                    </td>
                    <td>
                      <a class="btn btn-info btn-flat btn-equal-size" href="?c=producto&a=FormCrear&id=<?= htmlspecialchars($r->ID_producto) ?>">
                        <i class="fa fa-lg fa-pencil-alt"></i>
                      </a>
                      <a id="btn-estado-<?= htmlspecialchars($r->ID_producto) ?>" class="btn btn-warning btn-flat btn-equal-size" href="?c=producto&a=CambiarEstado&id=<?= $r->ID_producto ?>&estado=<?= $r->estado == 1 ? 0 : 1 ?>">
                        <?= $r->estado == 1 ? 'X' : '✓' ?>
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <nav aria-label="Page navigation">
              <ul class="pagination">
                <li class="page-item <?= ($pagina_actual <= 1) ? 'disabled' : '' ?>">
                  <a class="page-link" href="?c=producto&pagina=<?= max($pagina_actual - 1, 1) ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                    <span class="sr-only">Anterior</span>
                  </a>
                </li>
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= ($i == $pagina_actual) ? 'active' : '' ?>">
                  <a class="page-link" href="?c=producto&pagina=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= ($pagina_actual >= $total_paginas) ? 'disabled' : '' ?>">
                  <a class="page-link" href="?c=producto&pagina=<?= min($pagina_actual + 1, $total_paginas) ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                    <span class="sr-only">Siguiente</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

<script>
function modificarStock(id, accion) {
  let stockElement = document.getElementById('stock-' + id);
  let estadoElement = document.getElementById('estado-' + id);
  let btnEstado = document.getElementById('btn-estado-' + id);
  let stockActual = parseInt(stockElement.innerText);

  fetch(`?c=producto&a=ModificarStockAjax`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}&accion=${accion}&stock=${stockActual}`
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          stockElement.innerText = data.nuevo_stock;
          estadoElement.innerText = data.nuevo_estado;
          estadoElement.className = data.nuevo_estado === 'Activo' ? 'text-activo' : 'text-inactivo';

          btnEstado.innerText = data.nuevo_boton;
          btnEstado.href = `?c=producto&a=CambiarEstado&id=${id}&estado=${data.nuevo_estado_valor ? 0 : 1}`;
      } else {
          alert('Error: ' + data.message);
      }
  })
  .catch(error => console.error('Error:', error));
}
</script>
<script>
  document.getElementById('buscador').addEventListener('input', function () {
    const termino = this.value;

    // Oculta la paginación si hay un término de búsqueda
    const paginacion = document.querySelector('nav[aria-label="Page navigation"]');
    if (termino) {
      paginacion.style.display = 'none';
    } else {
      paginacion.style.display = 'block';
    }
    fetch(`?c=producto&a=BuscarAjax&termino=${encodeURIComponent(termino)}`)
      .then(response => response.json())
      .then(data => {
        const tbody = document.querySelector('#tablaproductos tbody');
        tbody.innerHTML = ''; // Limpia la tabla
        if (data.length > 0) {
          data.forEach(producto => {
            // Construye las filas de la tabla
            const row = `
              <tr>
                <td>${producto.ID_producto}</td>
                <td>${producto.nombre}</td>
                <td>${producto.precio}</td>
                <td>
                  <div class="stock-buttons">
                    <span id="stock-${producto.ID_producto}">${producto.stock}</span>
                    <button class="btn-custom max" onclick="modificarStock(${producto.ID_producto}, 'incrementar')">+</button>
                    <button class="btn-custom min" onclick="modificarStock(${producto.ID_producto}, 'decrementar')">-</button>
                  </div>
                </td>
                <td>${producto.tallas_nombre}</td>
                <td>${producto.colores_nombre}</td>
                <td class="estado-${producto.ID_producto} ${producto.estado == 1 ? 'text-activo' : 'text-inactivo'}">
                  <span id="estado-${producto.ID_producto}">
                    ${producto.estado == 1 ? 'Activo' : 'Inactivo'}
                  </span>
                </td>
                <td>${producto.genero}</td>
                <td>${producto.categoria_nombre}</td>
                <td>${producto.descripcion}</td>
                <td>
                  ${producto.ruta_imagen ? `<img src="${producto.ruta_imagen}" style="width: 100px; height: auto;">` : 'Sin Imagen'}
                </td>
                <td>
                  <a class="btn btn-info btn-flat btn-equal-size" href="?c=producto&a=FormCrear&id=${producto.ID_producto}">
                    <i class="fa fa-lg fa-refresh"></i>
                  </a>
                  <a id="btn-estado-${producto.ID_producto}" class="btn btn-warning btn-flat btn-equal-size" href="?c=producto&a=CambiarEstado&id=${producto.ID_producto}&estado=${producto.estado == 1 ? 0 : 1}">
                    ${producto.estado == 1 ? 'X' : '✓'}
                  </a>
                </td>
              </tr>
            `;
            tbody.innerHTML += row;
          });
        }else{
          tbody.innerHTML = '<tr><td colspan="12">No hay resultados.</td></tr>';
        }
      })
      .catch(error => console.error('Error:', error));
  });
  document.getElementById('clear-buscador').addEventListener('click', function () {
        const buscador = document.getElementById('buscador');
        buscador.value = ''; // Limpia el valor del input
        buscador.dispatchEvent(new Event('input')); // Simula un evento de entrada para actualizar los resultados
    });
</script>