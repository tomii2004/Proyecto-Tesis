
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Compras</title>
</head>
<body>
    
    <div class="content-wrapper">
    <div class="page-title">
    <div>
        <h2>Compras</h2>
        <p>Reporte Compras </p>
    </div>
    <div>
        <ul class="breadcrumb">
        <li><i class="fa fa-home fa-lg"></i></li>
        <li><a href="?c=compras">Compras</a></li>
        </ul>
    </div>
    </div>
    <div class = "container-fluid px-4">
        <form action="?c=compras&a=DescargarPdf" method="POST" autocomplete="off">
            <div class="row mb-2">
                <div class="col-12 col-md-4">
                    <label for="fecha_ini" class="form-label">Fecha Inicial:</label>
                    <input type="date" class="form-control" name="fecha_ini" id="fecha_ini" required autofocus>
                </div>
                <div class="col-12 col-md-4">
                    <label for="fecha_fin" class="form-label">Fecha Final:</label>
                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" required autofocus>
                </div>
            </div>
            <br>
            <input type="submit" class="btn btn-primary btn-md w-100" value="Descargar Pdf">
        </form>
    </div>
    
    
</body>
</html>
