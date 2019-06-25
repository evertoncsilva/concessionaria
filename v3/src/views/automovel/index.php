<?php
    // INSERIR CONTEÚDO AQUI
?>
<?php include __DIR__.'/../partials/header.php' ?>
<?php include __DIR__.'/../partials/navbar.php' ?>


<div id="main-container" class="container table_container col-lg-10 col-md-12">
  <div id="table-panel" class="card">
    <div class="card-header">
      <h3 class="panel-title">Componentes <span id="table-itemcount"class="float-right"></span>
      </h3>
    </div>
    <table class="table table-hover" id="data-table">
      <thead>
        <tr>
          <th class="thead-select"><input id="checkbox-select-all" onclick="toggle_SelectAll()" type="checkbox"></th>
          <th class="thead-id" id="testeclick">#</th>
          <th class="thead-placa">  Placa</th>
          <th class="thead-preco">  Preco</th>
          <th class="thead-ano">    Ano fab/modelo</th>
          <th class="thead-km">     Km</th>
          <th class="thead-marca">  Marca</th>
          <th class="thead-option"> Opções</th>
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
          <li id="paginator-previous" class="page-item" onclick="onClick_PreviousPage()">
            <a class="page-link" href="#" tabindex="-1">Anterior</a>
          </li>
          <li id="paginator-next" class="page-item" onclick="onClick_NextPage()">
            <a class="page-link" href="#">Próximo</a>
          </li>
        </ul>
      </nav>
    </div>

</div>



<?php include __DIR__.'/../partials/sidebar-right.php'?>
<?php include __DIR__.'/../partials/footer.php'?>