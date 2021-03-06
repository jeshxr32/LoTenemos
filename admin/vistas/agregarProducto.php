<?php
/*Llamadas de archivos necesarios
por medio de require*/

$titulo = "Agregar producto";

require __DIR__ . '/../../config/auth.php';
require __DIR__ . '/../../config/config.php';
require __DIR__ . '/../templates/header.php';
require __DIR__ . '/../templates/menu.php';
require __DIR__ . '/../templates/sidebar.php';

require __DIR__ . '/../../clases/Tipo_productos.php';

$modeloTipo = new Tipo();
$listaTipo = $modeloTipo->read();
/*
|--------------------------------------------------------------------------
| Contenido del Sitio
|--------------------------------------------------------------------------
|
| Aqui se agrega toda la funcionalidad de la pagina, especialmente deberia
| haber solo HTML cn algunos tags para PHP para acceder a variables.
|
 */
?>

 <div class="content-wrapper">
	<!-- Header de la pagina -->
	<section class="content-header">
		<h1>Productos</h1>
		<ol class="breadcrumb">
			<li><a href="<?=ROOT_ADMIN?>index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="active"><i class="fa fa-shopping-cart"></i> Productos</li>
		</ol>
	</section>
	<!-- Contenido -->
	<section class="content">
		<!-- Otros Contenidos -->
		<div class="row">

		  	<!-- resultado postivo-->
		  	<div id="ok"></div>
		  	<?php if (array_key_exists('prod', $_SESSION)) {
	?>
		  		<div class="col-md-12">
			        <div class="alert alert-info" role="alert">
			            <strong>Hey!</strong>
			            <br>
			            Se agrego correctamente el producto <?=$_SESSION['prod']?>!
			            <?php unset($_SESSION['prod']);?>
			        </div>
			    </div>
		    <?php }
?>
			<!-- resultado negativo segun corresponda -->
			<?php if (array_key_exists('error_tmp', $_SESSION)) {?>
			    <div class="col-md-12">
			        <div class="alert alert-danger" role="alert">
			            <strong><span class="glyphicon glyphicon-exclamation-sign"></span>  D'oh!</strong>
			            <br>
			            <?=$_SESSION['error_tmp']?>
			            <?php unset($_SESSION['error_tmp']);?>
			        </div>
		       	</div>
		    <?php }
?>

			<div class="col-md-offset-2 col-md-8">
				<div class="box box-solid">
					<div class="box-header with-border">
						<h3 class="box-title">Nuevo Producto</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Minimizar"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">

						<form class="form-horizontal" method="post" action="<?=ROOT_ADMIN?>controladores/insert-Producto.php" enctype="multipart/form-data">
							<fieldset>
								<div class="form-group">
									<label for="nombre" class="col-lg-2 control-label">Nombre Producto </label>
									<div class="col-lg-10">
										<input type="text" class="form-control" placeholder="Nombre producto" required patern="[A-Za-z]{150}" id="nombre" name="nombre"/>
									</div>
								</div>
								<div class="form-group">
									<label for="precio" class="col-lg-2 control-label">Precio </label>
									<div class="col-lg-10">
										<input class="form-control" id="precio" placeholder="Precio" type="number" name="precio" required="true" min="1" >
									</div>
								</div>
								<div class="form-group">
									<label for="unidad" class="col-lg-2 control-label">Unidades </label>
									<div class="col-lg-10">
										<input class="form-control" id="unidad" placeholder="Precio" type="number" name="unidad" required="true" min="1" >
									</div>
								</div>

								<div class="form-group">
								<label for="tipo" class="col-lg-2 control-label">Tipo </label>
									<div class="col-lg-10">
										<select class="form-control" id="tipo" name="tipo">
										<?php foreach ($listaTipo as $row) {?>
											<option value="<?=$row['ID_TIPO_PRODUCTO']?>"><?=$row['DESCRIPCION_TIPO']?> </option>
										<?php }
?>
										</select>
										<br>
									</div>
									</div>


								<div class="form-group">
									<div class="col-lg-10 col-lg-offset-2 text-right">
										<button id="btnrg" type="submit" class="btn btn-success">Agregar  <span class="glyphicon glyphicon-send"></span></button>
									</div>
								</div>

							</fieldset>
						</form>

					</div>

				</div>
			</div>
		</div>
	</section>
</div>

<?php

/*
|--------------------------------------------------------------------------
| Footer
|--------------------------------------------------------------------------
|
| Solo se hace un require del footer de la pagina de admin.
|
 */
require __DIR__ . '/../templates/footer.php';

?>
