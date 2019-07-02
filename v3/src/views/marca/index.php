<?php
    // INSERIR CONTEÚDO AQUI
?>
<?php include __DIR__.'/../partials/header.php' ?>
<?php include __DIR__.'/../partials/navbar.php' ?>


<div id="main-container" class="container table_container col-lg-10 col-md-12">
  <div id="table-panel" class="card">
    <div class="card-header">
      <h3 class="panel-title">Marcas <span id="table-itemcount"class="float-right"></span>
      </h3>
    </div>
    <table class="table table-hover" id="data-table">
      <thead>
        <tr>
          <th class="thead-select"><input id="checkbox-select-all" onclick="toggleSelectAll()" type="checkbox"></th>
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
          <li id="paginator-previous" class="page-item" onclick="onClickPreviousPage()">
            <a class="page-link" href="#" tabindex="-1">Anterior</a>
          </li>
          <li id="paginator-next" class="page-item" onclick="onClickNextPage()">
            <a class="page-link" href="#">Próximo</a>
          </li>
        </ul>
      </nav>
    </div>
</div>


<?php include __DIR__.'/../partials/sidebar-right.php'?>
<?php include __DIR__.'/../partials/footer.php'?>