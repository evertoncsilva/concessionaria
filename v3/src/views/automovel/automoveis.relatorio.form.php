<?php
    // INSERIR CONTEÚDO AQUI
    $orderby ?? '';
    $order ?? '';
    $filterby ?? '';
    $filtro ?? '';
?>
<?php include __DIR__.'/../partials/header.php' ?>
<?php include __DIR__.'/../partials/navbar.php' ?>

<div class="container-fluid container-form-relatorio" style="max-width="800px">
    <div class="card">
        <div class="card-body">
            <form action="automoveis.php" method="get">
                <input type="hidden" name="gerarelatorio">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <h5 class="text-center">Cofigure o relatório a ser gerado:</h5>
                    </div>
                    <div class="col-md-3">
                        <label for="relatorio_orderby">Ordernar itens por: </label>
                        <select class="form-control" name="orderby" id="relatorio_orderby">
                                <option value="id" <?= isset($orderby) && $orderby  == 'a.id' ? 'selected' : '' ?>>Id</option>
                                <option value="data_add" <?= isset($orderby) && $orderby == 'a.data_add' ? 'selected' : '' ?>>Data Criação</option>
                                <option value="data_edit" <?= isset($orderby) && $orderby == 'a.data_edit' ? 'selected' : '' ?>>Data Editado</option>
                                <option value="preco" <?= isset($orderby) && $orderby == 'a.preco' ? 'selected' : '' ?>>Preço</option>
                                <option value="placa" <?= isset($orderby) && $orderby == 'a.data_placa' ? 'selected' : '' ?>>Placa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="relatorio_order">Ordernar: </label>
                        <select class="form-control" name="order" id="relatorio_order">
                                <option value="asc"<?= isset($order) && $order == 'asc' ? 'selected' : '' ?>>Crescente</option>
                                <option value="desc" <?= isset($order) && $order == 'desc' ? 'selected' : '' ?>>Decrescente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="relatorio_orderby">Filtrar por: </label>
                        <select class="form-control" name="filterby" id="relatorio_filterby">
                                <option value="descricao" <?= isset($filterby) && $filterby == 'a.descricao' ? 'selected' : '' ?>>Descrição</option>
                                <option value="nome_marca" <?= isset($filterby) && $filterby == 'm.nome' ? 'selected' : '' ?>>Nome da Marca</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="relatorio_orderby">Valor: </label>
                        <input type="text" class="form-control" name="filterby_value" id="relatorio_filterby_value" value="<?= isset($filtro) ? $filtro : '' ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mx-auto botoes-relatorio">
                        <a href="automoveis.php">
                            <span class="btn btn-warning">Voltar</span>
                        </a>
                        <button type="submit" class="btn btn-success" onclick="buscaRelatorios();">Aplicar Filtros</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
if (isset($queryResult))
{
?>
    <div class="container-fluid container-relatorio-content" id="printarea">
        <div class="card">
            <div class="card-header">
                <?php $data = date_format(date_create(), 'd/m/Y H:i:s')?>
                <div class="row">
                    <div class="col-md-11">
                        <h6>Relatório gerado <?= $data ?> - <?= sizeof($queryResult) ?> Itens</h6>
                    </div>
                    <div class="col-md-1">
                        <button id="print-button" class="btn btn-light" style="padding: 0 8px; 0 8px; margin 0;" onclick="window.print()">
                            <i class="material-icons" >print</i>
                        </button> 
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <th id="col-descricao">Descrição</th>
                        <th id="col-preco">Preço</th>
                        <th id="col-placa">Placa</th>
                        <th id="col-marca">Marca</th>
                        <th id="col-data-add">Data Adicionado</th>
                        <th id="col-data-alt">Data Alterado</th>
                    </thead>
                    <tbody>
                        <?php foreach($queryResult as $item) {
                            $dataAdd = date_create($item['data_add']);
                            $dataEdit = isset($item['data_edit']) ? date_format(date_create($item['data_edit']), 'd/m/Y H:i:s') : '--';
                        ?>
                            <tr>
                                <td><?= $item['descricao'] ?></td>
                                <td>R$ <?= number_format($item['preco']) ?></td>
                                <td><?= $item['placa'] ?></td>
                                <td><?= $item['nome_marca'] ?></td>
                                <td><?= date_format($dataAdd, 'd/m/Y H:i:s') ?></td>
                                <td><?= $dataEdit ?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printarea * {
            visibility: visible;
        }
        #printarea {
            position: absolute;
            left: 0;
            top: 0;
        }
        #print-button {
            display: none:
        }
    }
</style>
<?php    
}
?>
<?php include __DIR__.'/../partials/footer.php'?>