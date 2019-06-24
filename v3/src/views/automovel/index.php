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
          <th class="thead-placa">Placa</th>
          <th class="thead-preco">Preco</th>
          <th class="thead-ano">Ano fab/modelo</th>
          <th class="thead-km">Km</th>
          <th class="thead-marca">Marca</th>
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
          <li id="paginator-previous" class="page-item" onclick="onClick_PreviousPage()">
            <a class="page-link" href="#" tabindex="-1">Anterior</a>
          </li>
          <li id="paginator-next" class="page-item" onclick="onClick_NextPage()">
            <a class="page-link" href="#">Próximo</a>
          </li>
        </ul>
      </nav>
    </div>
<!-- form -->


<div id="editor-form-wrapper2" class="card">
    <div class="card-header">
      <h4 class="panel-title">Editar automóvel</h4>
    </div>
    <form id="editor-form" action="">
    <input type="hidden" name="id" value="1">
      <div class="container-fluid">
            <!-- ROW -->
            <div class="row">
              <div class="col-md-6">
                  <div class="form group">
                      <label for="edito-form-descricao">Descrição do automóvel</label>
                      <input type="text" class="form-control" name="descricao" id="edito-form-descricao" placeholder="Insira uma descrição para seu automóvel" value="">
                  </div>
              </div>

              <div class="col-md-3">
                  <div class="form-group">
                      <label class="control-label" for="editor-form-nome">Placa</label>
                      <input type="text" class="form-control" name="placa" id="editor-form-nome" placeholder="XXX1234" value="" maxlength="7">
                  </div>  
              </div>

              <div class="col-md-3">
                  <div class="form-group">
                      <label class="control-label" for="editor-form-renavam">Código RENAVAM</label>
                      <input type="text" class="form-control" name="renavam" id="editor-form-renavam" placeholder="00000000000" value="" maxlength="11">
                  </div>  
              </div>
            </div>
            <!-- ENDROW -->
            <!-- ROW -->
            <div class="row">
              <div class="col-md-2">
                  <div class="form group" data-group="descricao">
                      <label for="editor-form-ano_modelo">Ano Modelo</label>
                      <select name="ano_modelo" id="editor-form-ano_modelo" class="form-control">
                        <!-- ANO MODELO OPTIONS -->
                        <option value="0">Selecione</option>
                        <option value="2018">2018</option>
                        <option value="2018">2018</option>
                      </select>
                  </div>
              </div>

              <div class="col-md-2">
                  <div class="form-group">
                      <label class="control-label" for="editor-form-ano_fabricacao">Ano Fabricação</label>
                      <select name="ano_fabricacao" id="editor-form-ano_fabricacao" class="form-control">
                        <!-- ANO FABRICACAO OPTIONS -->
                        <option value="0">Selecione</option>
                        <option value="2018">2018</option>
                        <option value="2018">2018</option>
                      </select>
                  </div>  
              </div>

              <div class="col-md-2">
                  <div class="form-group">
                      <label class="control-label" for="editor-form-cor">Cor</label>
                      <input type="text" class="form-control" name="cor" id="editor-form-cor" placeholder="Cor" value="" maxlength="20">
                  </div>  
              </div>
              <div class="col-md-2">
                  <div class="form-group">
                      <label class="control-label" for="editor-form-km">KM</label>
                      <input type="text" class="form-control" name="km" id="editor-form-km" placeholder="..." value="" maxlength="7">
                  </div>  
              </div>
              <div class="col-md-2">
                  <div class="form-group">
                      <label class="control-label" for="editor-form-marca_id">Marca</label>
                      <select name="marca_id" id="editor-form-marca_id" class="form-control">
                        <!-- MARCA ID OPTIONS -->
                        <option value="0">Selecione</option>
                        <option value="1">Fiat</option>
                        <option value="1">Fiat</option>
                      </select>
                  </div>  
              </div>
            </div>
            <!-- ENDROW -->
            <!-- ROW -->
            <div class="row">
              <div class="col-md-2">
                  <div class="form-group">
                      <label for="editor-form-preco">Preço</label>
                      <input type="text" class="form-control" name="preco" id="editor-form-preco" placeholder="Preço" value="">
                  </div>
              </div>

              <div class="col-md-2">
                  <div class="form-group">
                      <label class="control-label" for="editor-form-preco_fipe">Preço FIPE</label>
                      <input type="text" class="form-control" name="preco_fipe" id="editor-form-preco_fipe" placeholder="Preço FIPE" value="" maxlength="40">
                  </div>  
              </div>
            </div>
            <!-- ENDROW -->
            <h4>Componentes adicionais</h4>
            <hr>
            <!-- COMPONENTES CHECKBOXES -->
            <div class="form-check-inline">
              <input type="checkbox" class="form-check-input" name="10" id="checkbox-componente-10">
              <label for="checkbox-componente-10" class="form-check-label"> Ar Condicionado</label>
            </div>
            <div class="form-check-inline">
              <input type="checkbox" class="form-check-input" name="10" id="checkbox-componente-10">
              <label for="checkbox-componente-10" class="form-check-label"> Ar Condicionado</label>
            </div>
            
            <div class="row editor-button-container">
                <button type="reset" class="btn btn-default">Limpar</button>
                <button type="button" class="btn btn-warning" onclick="onClick_CreateFormCancelar()">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="ajax_EditAutomovel(1)">Salvar</button>
            </div>
              

      </div>  
    </form>
            
</div>
<!-- end form -->
</div>



<?php include __DIR__.'/../partials/sidebar-right.php'?>
<?php include __DIR__.'/../partials/footer.php'?>