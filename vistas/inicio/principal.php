<style>
  .btnclear{
            position: absolute; 
            top: 50%; 
            right: 10px; 
            transform: translateY(-50%); 
            border: none; 
            background: transparent; 
            cursor: pointer; 
            padding: 0;
            font-size: 16px;
            color:grey;
        }
</style>
<div class="content-wrapper">
  <div class="page-title">
    <div>
      <h1><i class="fa fa-dashboard"></i> Inicio</h1>
      <p>Estadísticas Semanales</p>
    </div>
    <div>
      <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=inicio">Inicio</a></li>

      </ul>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          Ventas De la Semana ($)
        </div>
        <div class="panel-body">
          <canvas id="myChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-5">
      <div class="panel panel-default">
        <div class="panel-heading">
          Productos Más Vendidos de la Semana
        </div>
        <div class="panel-body">
          <canvas id="chart-productos"></canvas>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Historial de Ventas Semanales
            </div>
            <div class="panel-body">
                <div style="display: inline-block; position: relative; width: auto;margin-bottom: 20px;">
                    <input type="text" id="buscador" class="form-control form-control-sm" placeholder="Buscar compras..." style="padding-right: 30px;">
                    <button type="button" id="clear-buscador" class="btnclear">✖</button>
                </div>
                <div style="display: inline-block; position: relative; width: auto; float: right;">
                    <a id="LimpiarHistorial" href="?c=inicio&a=LimpiarHistorial" class="btn-danger btn-sm"><i class="fa-regular fa-trash-can"></i></a>
                </div>
                <table class="table table-striped" id="tablahistorial">
                    <thead>
                        <tr>
                            <th>Semana</th>
                            <th>Total Ventas</th>
                            <th>Productos Más Vendidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($historial)) : ?>
                            <?php foreach ($historial as $semana) : ?>
                                <tr>
                                    <td><?php echo $semana['inicio'] . ' - ' . $semana['fin']; ?></td>
                                    <td><?php echo '$' . number_format($semana['total_ventas'], 2); ?></td>
                                    <td>
                                        <ul>
                                            <?php 
                                                $productos = json_decode($semana['productos_vendidos'], true); 
                                                foreach ($productos as $producto) : 
                                            ?>
                                                <li><?php echo $producto['nombre'] . ' - ' . $producto['cantidad']; ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3">No hay historial disponible.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
  </div>
</div>

<script>
  const ctx = document.getElementById('myChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
      datasets: [{
        data: [<?php echo $diasVentasEstadisticas; ?>],
        label: 'Ventas Totales',
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(255, 159, 64, 0.2)',
          'rgba(255, 205, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(201, 203, 207, 0.2)'
        ],
        borderColor: [
          'rgb(255, 99, 132)',
          'rgb(255, 159, 64)',
          'rgb(255, 205, 86)',
          'rgb(75, 192, 192)',
          'rgb(54, 162, 235)',
          'rgb(153, 102, 255)',
          'rgb(201, 203, 207)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  const ctxProductos = document.getElementById('chart-productos');

  new Chart(ctxProductos, {
    type: 'pie',
    data: {
      labels: <?php echo $nombreProductos; ?>,
      datasets: [{
        label: 'Producto Vendidos',
        data: [<?php echo $cantidadProductos; ?>],
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        legend: {
          display: true
        }
      }
    }
  });
</script>
<script>
  document.getElementById('buscador').addEventListener('input', function () {
    const termino = this.value;
    

    fetch(`?c=inicio&a=BuscarAjax&termino=${encodeURIComponent(termino)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.querySelector('#tablahistorial tbody');
            tbody.innerHTML = '';
            if (data.length > 0) {
                data.forEach(historial => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${historial.inicio} - ${historial.fin}</td>
                            <td>$${parseFloat(historial.total_ventas).toFixed(2)}</td>
                            <td>
                                <ul>${JSON.parse(historial.productos_vendidos).map(p => `<li>${p.nombre} - ${p.cantidad}</li>`).join('')}</ul>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="3">No hay resultados.</td></tr>';
            }
        })
        .catch(error => console.error('Error:', error));
});

document.getElementById('clear-buscador').addEventListener('click', function () {
    const buscador = document.getElementById('buscador');
    buscador.value = '';
    buscador.dispatchEvent(new Event('input'));
});
</script>
<script>
    document.getElementById('LimpiarHistorial').addEventListener('click', function(event) {
        event.preventDefault(); // Evita que se ejecute la acción del enlace inmediatamente
        
        // Mostrar la alerta de confirmación con SweetAlert
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará todo el historial.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, limpiar historial',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, redirige al enlace
                window.location.href = '?c=inicio&a=LimpiarHistorial';
            }
        });
    });
</script>