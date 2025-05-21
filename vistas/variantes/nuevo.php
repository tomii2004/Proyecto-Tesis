 <div class="content-wrapper">
     <div class="page-title">
         <div>
             <h2>Variantes</h2>
             <p>Definir Variantes Basicas</p>
         </div>
         <div>
             <ul class="breadcrumb">
                 <li><i class="fa fa-home fa-lg"></i></li>
                 <li><a href="?c=variantes">Variantes</a></li>
             </ul>
         </div>
     </div>
     <div class="row">
         <div class="col-md-6">
             <legend>Nueva Variante</legend>
             <form action="?c=variantes&a=AñadirVariante" method="post">
                 <label for="variantes">Variante:</label>
                 <select name="variantes" id="variantes" class="form-control">
                     <option value="talla">Talle</option>
                     <option value="color">Color</option>
                 </select><br>
                 <label for="nombre">Nombre:</label>
                 <input type="text" class="form-control" id="nombre" name="nombre" required><br><br>
                 <button href="?c=variantes" onclick="event.preventDefault(); history.back();" class="btn btn-default"
                     id="cancelar">Cancelar</button>
                 <input type="submit" class="btn btn-primary" value="Enviar">
             </form>
         </div>
     </div>