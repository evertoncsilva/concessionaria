<?php
    // INSERIR CONTEÚDO AQUI
?>
<?php include __DIR__.'/../partials/header.php' ?>
<?php include __DIR__.'/../partials/navbar.php' ?>

<div id="table-container" class="container table_container col-lg-10 col-md-12">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h3 class="panel-title">Componentes</h3>
      
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
      <tbody id="tableContent">

      </tbody>
    </table>
    <div id="spinner" class="spinner"></div>
  </div>
    <div id="tablePaginator" class="pull-right">
      <nav aria-label="...">
        <ul class="pagination">
          <li id="paginator-previous" class="page-item" onclick="onPreviousPage()">
            <a class="page-link" href="#" tabindex="-1">Anterior</a>
          </li>
          <li id="paginator-next" class="page-item" onclick="onNextPage()">
            <a class="page-link" href="#">Próximo</a>
          </li>
        </ul>
      </nav>
    </div>

</div>


<?php include __DIR__.'/../partials/sidebar-right.php'?>
<?php include __DIR__.'/../partials/footer.php'?>