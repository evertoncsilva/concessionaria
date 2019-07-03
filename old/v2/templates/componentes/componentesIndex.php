<?php

    // INSERIR CONTEÚDO AQUI
?>
<?php include __DIR__.'/../partials/header.php' ?>
<?php include __DIR__.'/../partials/navbar.php' ?>


<div class="container table-container col-lg-10 col-md-12">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title"><?php echo $pageTitle ?></h3>
      <div class="pull-right">
        <!-- <span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
								<i class="glyphicon glyphicon-filter"></i>
				</span> -->
      </div>
    </div>
    <!-- <div class="panel-body">
						<input type="text" class="form-control" id="dev-table-filter" data-action="filter" data-filters="#dev-table" placeholder="Filter Developers" />
					</div> -->
    <table class="table table-hover" id="dev-table">
      <thead>
        <tr>
          <th class="thead-select"><input type="checkbox"></th>
          <th class="thead-id" id="testeclick">#</th>
          <th class="thead-nome">Nome</th>
          <th class="thead-descricao">Descrição</th>
          <th class="thead-option">Opções</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="td-select"><input type="checkbox"></td>
          <td>1</td>
          <td>Kilgore</td>
          <td>Trout</td>
          <td>
            <span class="glyphicon glyphicon-trash table-option pull-right" aria-hidden="true"></span>
            <span class="glyphicon glyphicon-pencil table-option pull-right " aria-hidden="true"></span>
          </td>
        </tr>
        <tr>
          <td class="td-select"><input type="checkbox"></td>
          <td>2</td>
          <td>Bob</td>
          <td>Loblaw</td>
          <td>
            <span class="glyphicon glyphicon-trash table-option pull-right" aria-hidden="true"></span>
            <span class="glyphicon glyphicon-pencil table-option pull-right " aria-hidden="true"></span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>


</div>
</div>


<?php include __DIR__.'/../partials/sidebar-right.php'?>
<?php include __DIR__.'/../partials/footer.php' ?>