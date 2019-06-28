<?php
    // INSERIR CONTEÚDO AQUI
?>
<?php include __DIR__.'/../partials/header.php' ?>
<?php include __DIR__.'/../partials/navbar.php' ?>


<div id="main-container" class="container table_container col-lg-10 col-md-12">
  <div id="table-panel" class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-9">
          <h3 class="panel-title" class="col-md-6">Componentes</h3>
        </div>
        <div class="col-md-2" id="col-filtertext">
          <input class="form-control" id="filtertext" type="text" name="filter" placeholder="Filtrar...">
        </div>
        <div class="col-md-1" id="col-filterbutton">
          <span class="btn btn-outline-info filterbutton"><i class="material-icons" onclick="ajax_getPage()">search</i></span>
          <span class="btn btn-outline-danger filterbutton noshow" id="btn-clearfilter"><i class="material-icons" onclick="clearFilter()">clear</i></span>
        </div>
      </div>
    </div>
    <table class="table table-hover" id="data-table">
      <thead>
        <tr>
          <th class="thead-select"><input id="checkbox-select-all" onclick="toggle_SelectAll()" type="checkbox"></th>
          <th class="thead-id" id="testeclick">#</th>
          <th class="thead-placa">  Descrição</th>
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
      
    </div>

</div>



<?php include __DIR__.'/../partials/sidebar-right.php'?>
<?php include __DIR__.'/../partials/footer.php'?>