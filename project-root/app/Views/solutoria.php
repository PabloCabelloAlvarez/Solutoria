
<!doctype html>
<html>
<head>
    <title>Solutoria</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="/assets/css/estilo.css">  
</head>
<body>
    <!--Gráfico-->
    <div class="separador">
        <h1>Gráfico</h1>
        <br>
        <div class="row">
            <div class="col-auto">
                <label for="tipo" class="col-form-label">Tipo de Indicador</label>
            </div>
            <div class="col-3">
                <select class="form-select" aria-label="Default select example" id="tipo">
                    <option value="">Selecciona un tipo</option>
                    <?php foreach ($datos as $nombre=>$codigo):?>
                        <option value="<?=$codigo?>"><?=$nombre?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-1">
                <label for="desde" class="col-form-label">Fecha Desde</label>
            </div>
            <div class="col-2">
                <input id="desde" type="date" class="form-control" disabled>
            </div>
            <div class="col-1">
                <label for="hasta" class="col-form-label">Fecha Hasta</label>
            </div>
            <div class="col-2">
                <input id="hasta" type="date" class="form-control" disabled>
            </div>
            <div class="col-auto">
                <button class="form-control btn btn-primary" type="submit" id="grafico">Generar Grafico</button>
            </div>
            <div class="spinner-border" role="status" style="visibility: hidden;" id="cargando">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <br>
        <canvas id="speedChart"></canvas>
    </div>
    <!--fin del Gráfico-->
    <!--CRUD-->
    <div class="separador">
        <h1>CRUD</h1>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#addModal">Insertar</button>
	    </div>
        <table class="table table-bordered" id="users-list">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Unidad de Medida</th>
                    <th>Fecha</th>
                    <th>Valor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if($datosUf): ?>
                    <?php foreach($datosUf as $datoUf): ?>
                        <tr id="<?php echo $datoUf['uf_id']; ?>">
                            <td id="n<?php echo $datoUf['uf_id']; ?>"><?php echo $datoUf['nombre']; ?></td>
                            <td id="c<?php echo $datoUf['uf_id']; ?>"><?php echo $datoUf['codigo']; ?></td>
                            <td id="u<?php echo $datoUf['uf_id']; ?>"><?php echo $datoUf['unidad_medida']; ?></td>
                            <td id="f<?php echo $datoUf['uf_id']; ?>"><?php echo date("d-m-Y", strtotime($datoUf['fecha'])); ?></td>
                            <td id="v<?php echo $datoUf['uf_id']; ?>"><?php echo $datoUf['valor']; ?></td>
                            <td>
                                <a id="e<?php echo $datoUf['uf_id']; ?>" class="btn btn-info btn-sm" data-id="<?php echo $datoUf['uf_id'];?>" data-nombre="<?php echo $datoUf['nombre'];?>" data-codigo="<?php echo $datoUf['codigo'];?>" data-unidad="<?php echo $datoUf['unidad_medida'];?>" data-fecha="<?php echo $datoUf['fecha'];?>" data-valor="<?php echo $datoUf['valor'];?>">Editar</a>
                                <a class="btn btn-danger btn-sm" data-id="<?php echo $datoUf['uf_id'];?>">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Modal Pregunta para borrar-->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center"> 
                    <h4>¿Estás seguro?</h4>                
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="ufBorrarId" class="ufBorrarId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" onclick="borrar()">Si!</button>
                </div>
            </div>
        </div>
    </div>
    <!-- fin Pregunta para borrar-->
    <!-- Modal para agregar-->
    <form action="" method="post" id="formularioAgregar">
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar Nuevo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" name="codigo" id="codigo" required>
                        </div>
                        <div class="form-group">
                            <label>Unidad de Medida</label>
                            <input type="text" class="form-control" name="unidad_medida" id="unidad_medida" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" class="form-control" name="fecha" id="fecha" required>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <input type="number" class="form-control" name="valor" id="valor" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="guardar">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- fin Modal para agregar-->
    <!-- Modal para editar-->
    <form action="" method="post" id="formularioEditar">
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Elemento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" class="form-control" name="nombreEditar" id="nombreEditar" required>
                        </div>
                        <div class="form-group">
                            <label>Código</label>
                            <input type="text" class="form-control" name="codigoEditar" id="codigoEditar" required>
                        </div>
                        <div class="form-group">
                            <label>Unidad de Medida</label>
                            <input type="text" class="form-control" name="unidad_medidaEditar" id="unidad_medidaEditar" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" class="form-control" name="fechaEditar" id="fechaEditar" required>
                        </div>
                        <div class="form-group">
                            <label>Valor</label>
                            <input type="number" class="form-control" name="valorEditar" id="valorEditar" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="ufEditarId" class="ufEditarId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="editar">Editar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- fin Modal para editar-->
    <!-- fin CRUD-->
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.0/dist/chart.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="/assets/js/util.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
</html>
          
                    