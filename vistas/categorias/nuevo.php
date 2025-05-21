<div class="content-wrapper">
    <div class="page-title">
        <div>
            <h2>Categorias</h2>
            <p>Definir Categorias Basicas</p>
        </div>
        <div>
            <ul class="breadcrumb">
                <li><i class="fa fa-home fa-lg"></i></li>
                <li><a href="?c=categorias">Categorias</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <legend>Nueva Categoria</legend>
            <form action="?c=categorias&a=AñadirCategoria" method="post">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required><br><br>
                <button href="?c=categorias" onclick="event.preventDefault(); history.back();"
                    class="btn btn-default btn-md w-100">Cancelar</button>
                <input type="submit" class="btn btn-primary" value="Enviar">
            </form>
        </div>
    </div>