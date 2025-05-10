

<div class="content-wrapper">
        <div class="page-title">
          <div>
            <h1><i class="fa fa-edit"></i> Productos</h1>
            <p>Ingresa los datos para <?=$titulo?> un producto nuevo </p>
          </div>
          <div>
            <ul class="breadcrumb">
              <li><i class="fa fa-home fa-lg"></i></li>
              <li>Productos</li>
              <li><a href="?c=producto"><?=$titulo?> Producto</a></li> <!--preguntar porque en esta linea no puedo poner vistas/productos/index.php -->
            </ul>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="row">
                <div class="col-lg-6">
                  <div class="well bs-component">
                  <?php if (!empty($_SESSION['errores'])): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($_SESSION['errores'] as $campo => $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['errores']); ?> <!-- Limpiar los errores después de mostrarlos -->
                    <?php endif; ?>

                    <form class="form-horizontal" method="POST" action= "?c=producto&a=Guardar" enctype="multipart/form-data" id="formulario" novalidate > <!-- la a es la accion y el enctype es para permitir la subida de archivos-->
                      <fieldset>
                        <legend><?=$titulo?> Producto</legend>
                        <div class="form-group">
                            <input class="form-control" name="ID_producto" type="hidden" value="<?=$p -> getID_producto()?>">

                            <label class="col-lg-2 control-label" for="Nombre">Nombre</label>
                            <div class="col-lg-10">
                              <input required class="form-control" name="Nombre" type="text" placeholder="Nombre Producto" value="<?=$p -> getNombre()?>">
                              <?php if (isset($_SESSION['errores']['Nombre'])): ?>
                                  <div class="invalid-feedback"><?= $_SESSION['errores']['Nombre'] ?></div>
                              <?php endif; ?>
                            </div>
                        </div>


                        <div class="form-group">
                          <label class="col-lg-2 control-label" for="Stock">Stock</label>
                          <div class="col-lg-10">
                            <input required class="form-control" name="Stock" type="number" placeholder="Stock" value="<?=$p -> getStock()?>">
                            <?php if (isset($_SESSION['errores']['Stock'])): ?>
                                <div class="invalid-feedback"><?= $_SESSION['errores']['Stock'] ?></div>
                            <?php endif; ?>
                          </div>

                        </div>


                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="Estado">Estado:</label>
                            <div class="col-lg-10">
                                <p class="form-control-static">
                                    <?= $p->getEstado() == 1 ? 'Activo' : 'Inactivo'; ?>
                                </p>

                            </div>
                        </div>

                        <div class="form-group">
                          <label class="col-lg-2 control-label" for="Genero">Genero</label>
                          <div class="col-lg-10">
                            <input required class="form-control" name="Genero" type="number" placeholder="Genero" value="<?=$p -> getGenero()?>">
                            <?php if (isset($_SESSION['errores']['Genero'])): ?>
                                <div class="invalid-feedback"><?= $_SESSION['errores']['Genero'] ?></div>
                            <?php endif; ?>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="col-lg-2 control-label" for="Categoria">Categoria</label>
                          <div class="col-lg-10">
                            <select required id="Categoria" name="Categoria" class="form-control">
                              <option value="<?=$p -> getCategoria()?>">Selecciona una categoría</option>
                              <?php foreach($categorias as $categoria){ ?>
                                <option value="<?= $categoria->ID_categoria ?>"
                                    <?= isset($p) && $p->getCategoria() == $categoria->ID_categoria ? 'selected' : '' ?>>
                                    <?= ucfirst($categoria->Nombre) ?>
                                </option>
                              <?php } ?>
                            </select>
                            <?php if (isset($_SESSION['errores']['Categoria'])): ?>
                                <div class="invalid-feedback"><?= $_SESSION['errores']['Categoria'] ?></div>
                            <?php endif; ?>
                          </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="Imagen">Imagen Principal</label>
                            <div class="col-lg-10">
                                <input class="form-control" name="Imagen" type="file" accept="image/*">  <!--el accept es para que pueda subir cualquier tipo de formato -->
                                <?php if (isset($_SESSION['errores']['Imagen'])): ?>
                                    <div class="invalid-feedback"><?= $_SESSION['errores']['Imagen'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="Otras_imagenes">Otras imagenes</label>
                            <div class="col-lg-10">
                                <input class="form-control" name="Otras_imagenes[]" type="file" accept="image/*" multiple> <!--  el accept es para que pueda subir cualquier tipo de formato  -->
                            </div>
                        </div>

                        <div class="form-group">
                          <label class="col-lg-2 control-label" for="Descripcion">Descripcion</label>
                          <div class="col-lg-10">
                            <textarea class="form-control" id="editor" name="Descripcion" placeholder="Descripcion"><?=$p->getDesc()?></textarea>
                            <?php if (isset($_SESSION['errores']['Descripcion'])): ?>
                                <div class="invalid-feedback"><?= $_SESSION['errores']['Descripcion'] ?></div>
                            <?php endif; ?>
                          </div>
                        </div>

                        <div class="form-group">
                          <div class="col-lg-10 col-lg-offset-2">
                            <button type="button" class="btn btn-success btn-md" id="agregar-variante"> + Variante</button>
                          </div>
                        </div>

                        <div id="contenido">

                          <?php foreach($variantes as $variante){?>
                            <div class="variant-block">
                              <input type="hidden" name="id_variante[]" value="<?php echo $variante['ID_producvar']?>">
                            
                              <div class="form-group">
                                <label class="col-lg-2 control-label" for="Talla">Talle</label>
                                <div class="col-lg-10">
                                  <select class="form-control" name="talla[]">
                                    <option value="">Selecccionar</option>
                                    <?php foreach($tallas as $talla){ ?>
                                      <option value="<?php echo $talla['ID_talla'];?>"
                                        <?php if($talla['ID_talla'] == $variante['ID_talla'])echo 'selected'; ?>>
                                        <?php echo $talla['nombre'];?>
                                    </option>
                                  <?php } ?>
                                  </select>
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="col-lg-2 control-label" for="Color">Color</label>
                                <div class="col-lg-10">
                                  <select class="form-control" name="color[]">
                                    <option value="">Selecccionar</option>
                                    <?php foreach($colores as $color){ ?>
                                    <option value="<?php echo $color['ID_colores'];?>"
                                        <?php if($color['ID_colores'] == $variante['ID_color'])echo 'selected'; ?>>
                                        <?php echo $color['nombre'];?>
                                    </option>
                                  <?php } ?>
                                  </select>
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="col-lg-2 control-label" for="Precio">Precio</label>
                                <div class="col-lg-10">
                                  <input class="form-control" name="precio_variante[]" type="text" placeholder="Precio" value="<?php echo $variante['precio']?>">
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="col-lg-2 control-label" for="Stock">Stock</label>
                                <div class="col-lg-10">
                                  <input class="form-control" name="stock_variante[]" type="text" placeholder="Stock" value="<?php echo $variante['stock']?>">
                                </div>
                                <div class="col-lg-10 col-lg-offset-2">
                                  <button type="button" class="btn btn-danger btn-remove-variante">Eliminar</button>
                                </div>
                              </div>
                              <hr style="border: 1px  solid #ccc;">
                            </div>
                          <?php } ?>
                        </div>
                        



                        <template id="plantilla_variante">
                          <div class="variant-block">
                            <div class="form-group">
                              <label class="col-lg-2 control-label" for="Talla">Talle</label>
                              <div class="col-lg-10">
                                <select class="form-control" name="talla[]">
                                  <option value="">Selecccionar</option>
                                  <?php foreach($tallas as $talla){ ?>
                                    <option value="<?php echo $talla['ID_talla'] ?>">
                                        <?php echo $talla['nombre'] ?>
                                    </option>
                                <?php } ?>
                                </select>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label" for="Color">Color</label>
                              <div class="col-lg-10">
                                <select class="form-control" name="color[]">
                                  <option value="">Selecccionar</option>
                                  <?php foreach($colores as $color){ ?>
                                  <option value="<?php echo $color['ID_colores'] ?>">
                                      <?php echo $color['nombre'] ?>
                                  </option>
                                <?php } ?>
                                </select>
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label" for="Precio">Precio</label>
                              <div class="col-lg-10">
                                <input class="form-control" name="precio_variante[]" type="text" placeholder="Precio">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-lg-2 control-label" for="Stock">Stock</label>
                              <div class="col-lg-10">
                                <input class="form-control" name="stock_variante[]" type="text" placeholder="Stock">
                              </div>
                              <div class="col-lg-10 col-lg-offset-2">
                                <button type="button" class="btn btn-danger btn-remove-variante">Eliminar</button>
                              </div>
                            </div>
                            <hr style="border: 1px  solid #ccc;">
                          </div>
                        </template>




                        <div class="form-group">
                          <div class="col-lg-10 col-lg-offset-2">
                            <button href="?c=producto" onclick="event.preventDefault(); history.back();" class="btn btn-default" id="cancelar">Cancelar</button>
                            <button class="btn btn-primary" type="submit">Cargar</button>
                          </div>
                        </div>
                      </fieldset>
                    </form>

                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.ckeditor.com/ckeditor5/44.1.0/ckeditor5.umd.js"></script>
<script>
const {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Font,
    Paragraph
} = CKEDITOR;

ClassicEditor
    .create( document.querySelector( '#editor' ), {
        licenseKey: 'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3Njc4MzAzOTksImp0aSI6IjE5M2NjMmNkLWU1MWItNGFjMi1hYTJlLTg1NDU1YjAxOGIzNiIsImxpY2Vuc2VkSG9zdHMiOlsiMTI3LjAuMC4xIiwibG9jYWxob3N0IiwiMTkyLjE2OC4qLioiLCIxMC4qLiouKiIsIjE3Mi4qLiouKiIsIioudGVzdCIsIioubG9jYWxob3N0IiwiKi5sb2NhbCJdLCJ1c2FnZUVuZHBvaW50IjoiaHR0cHM6Ly9wcm94eS1ldmVudC5ja2VkaXRvci5jb20iLCJkaXN0cmlidXRpb25DaGFubmVsIjpbImNsb3VkIiwiZHJ1cGFsIl0sImxpY2Vuc2VUeXBlIjoiZGV2ZWxvcG1lbnQiLCJmZWF0dXJlcyI6WyJEUlVQIl0sInZjIjoiMGM0MmM0MDEifQ.i5s795XEjAZgirY0tLVZZ7W5RLnk8D-86knvbU6yQLvklPh4LznQzTHzeWDx4j5yKtklNJXnjNT3wk8ToofR8g',
        plugins: [ Essentials, Bold, Italic, Font, Paragraph ],
        toolbar: [
            'undo', 'redo', '|', 'bold', 'italic', '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
        ]
    } )
    .then( /* ... */ )
    .catch( /* ... */ );



</script>

<script>
  const btnVariante = document.getElementById('agregar-variante')
  btnVariante.addEventListener('click',agregaVariante);

  function agregaVariante(){
    const contenido = document.getElementById('contenido')
    const plantilla = document.getElementById('plantilla_variante').content.cloneNode(true)

    contenido.appendChild(plantilla)
  }
  document.getElementById('contenido')
  .addEventListener('click', function(e) {
    if (!e.target.classList.contains('btn-remove-variante')) return;
    // 1) Encuentra el contenedor completo:
    const block = e.target.closest('.variant-block');
    if (!block) return;

    // 2) Saca el ID de la BD (si existe):
    const hidden = block.querySelector('input[name="id_variante[]"]');
    const idVar = hidden ? hidden.value : null;

    // 3) Función para remover del DOM:
    const quitarDOM = () => block.remove();

    // 4) Si venía de la BD, avisa al servidor:
    if (idVar) {
      fetch('?c=producto&a=EliminarVarianteAjax', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `idVariante=${encodeURIComponent(idVar)}`
      })
      .then(r=>r.json())
      .then(json=>{
        if (json.success) quitarDOM();
        else alert('Error: '+json.message);
      })
      .catch(()=>alert('Error de conexión al eliminar variante.'));
    } else {
      // si era recién agregada, basta con quitarla del form
      quitarDOM();
    }
  });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("formulario");
    const campos = document.querySelectorAll("#formulario input, #formulario select, #formulario textarea");

    // Solo cargar valores desde sessionStorage si estamos creando un producto
    if (!<?php echo isset($p) ? 'true' : 'false'; ?>) {
        // Cargar los valores guardados en sessionStorage solo si no estamos editando
        campos.forEach(campo => {
            if (sessionStorage.getItem(campo.name)) {
                campo.value = sessionStorage.getItem(campo.name);
            }

            // Guardar los valores mientras se escribe en los campos
            campo.addEventListener("input", function () {
                sessionStorage.setItem(campo.name, campo.value);
            });
        });
    }

    formulario.addEventListener("submit", function (event) {
        event.preventDefault();  // Evitar el envío inmediato del formulario

        let formValido = true;
        const camposRequeridos = document.querySelectorAll("#formulario input[required], #formulario select[required], #formulario textarea[required]");

        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                campo.classList.add("error");
                formValido = false;
            } else {
                campo.classList.remove("error");
            }
        });

        if (!formValido) {
            alert("Por favor, completa todos los campos obligatorios.");
        } else {
            // Si todo está bien, enviar el formulario manualmente
            sessionStorage.clear(); // Limpiar sessionStorage si el formulario es válido
            formulario.submit();  // Enviar el formulario
        }
    });
});
</script>

<style>
.error {
    border: 2px solid red !important;
    background-color: #ffdddd;
}
</style>
