<?php
    // INSERIR CONTEÚDO AQUI
?>
<?php include __DIR__.'/../partials/header.php' ?>
<?php include __DIR__.'/../partials/navbar.php' ?>


<div id="main_container" class="container table-container col-lg-10 col-md-12">
  <div id="table_panel" class="card">
    <div class="card-header">
      <div class="row">
        <div class="col-md-7">
          <h3 class="panel-title" class="col-md-6">Automóveis</h3>
        </div>
        <div class="col-md-2">
          <a href="automoveis.php?relatorio" class="btn btn-outline-secondary" id="btn_relatorios">Relatórios <i class="material-icons" id="icon-relatorio">assignment</i></a>
        </div>
        <div class="col-md-2" id="col_filtertext">
          <input class="form-control" id="filtertext" type="text" name="filter" placeholder="Filtrar...">
        </div>
        <div class="col-md-1" id="col_filterbutton">
          <span class="btn btn-outline-info filterbutton"><i class="material-icons" onclick="ajaxGetPage()">search</i></span>
          <span class="btn btn-outline-danger filterbutton noshow" id="btn-clearfilter"><i class="material-icons" onclick="onClickClearFilter()">clear</i></span>
        </div>
      </div>
    </div>
    <table class="table table-hover" id="data-table">
      <thead>
        <tr>
          <th class="thead-select"><input id="checkbox-select-all" onclick="toggleSelectAll()" type="checkbox"></th>
          <th class="thead-id" id="testeclick">#</th>
          <th class="thead-placa">Descrição</th>
          <th class="thead-preco">Preco</th>
          <th class="thead-ano">Ano fab/modelo</th>
          <th class="thead-km">Km</th>
          <th class="thead-marca">Placa</th>
          <th class="thead-marca">Marca</th>
          <th class="thead-option">Opções</th>
        </tr>
      </thead>
      <tbody id="tableContent">
      </tbody>
    </table>
    <div id="spinner" class="spinner"></div>
  </div>
    <div id="table_paginator">
    </div>

</div>



<?php include __DIR__.'/../partials/sidebar-right.php'?>
<?php include __DIR__.'/../partials/footer.php'?>