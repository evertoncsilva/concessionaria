<?php
require_once __DIR__.'/../DTOs/automoveis.DTO.php';
require_once 'controller.php';
class AutomovelController extends Controller {
    
    public $routes = [
        'get' => [
            'get_componentes' => 'getComponentes',
            'paginated' => 'getPaginated',
            'getpage' => 'getpage',
            'all' => 'getAll',
            'relatorio' => 'relatorioForm',
            'gerarelatorio' => 'geraRelatorio',
            'getAutomovel' => 'getAutomovel',
            'edit' => 'renderIndex'
        ],
        'post' => [
            'delete_many' => 'deleteMany',
            'delete_one' => 'deleteOne',
            'create' => 'create',
            'update' => 'update'
        ]
    ];
    public function __construct() {
        $dto = new AutomoveisDTO();
        parent::__construct('automovel', $dto);
        $this->pageTitle = 'Automóveis';
        $this->requireAuth = true;
    }
    public function getAll($args) {
        if (!isset($args)) {
            $args = array();
        }
        $data = $this->DTO->getAll($args);
        $this->send($data);
    }
    public function getPaginated($args) {
        $ar = $args;
        $lastItem = null;
        $orderBy = $this->DTO->primaryKey;
        $pageSize = 25; // tamanho de pagina default
        $action = 'next';

        if (isset($args['last-item'])) {
            $lastItem = $args['last-item'];
        }
        if (isset($args['page-size']) && is_numeric($args['page-size'])) {
            $pageSize = $args['page-size'];
        }
        if (isset($args['order-by']) && $this->DTO->isColumnNameValid($args['order-by'])) {
            $orderBy = $args['order-by'];
        }
        if(isset($args['action']) && $args['action'] == 'previous') {
            $action = 'previous'; 
        }

        $itens = $this->DTO->getAll();

        if ($itens) {
            $this->send($itens);
        }  
    }
    public function index() {
        renderIndex();
    }
    public function deleteOne($args) {
        //TODO: deletar componentes do automóvel também
        $id = (isset($args['id'])) ? $args['id'] : null;

        if ($id != null && is_numeric($id)) {
            if($this->DTO->deleteOne($id)) {
                $this->send(new DefaultSuccesResponse(['msg' => "Deletado item {$id} com sucesso!"]));
            }
            else {
                $this->error(['msg' => 'Id não localizado', 'error-code' => 2]);
            }
        }
        else {
            $this->error(['msg'=>'Argumentos inválidos', 'error-code' => 3]);
        }
    }
    public function create($args) {
                $result = $this->DTO->create($args);
                if ($result instanceof DefaultErrorResponse) {
                        return $this->error($result);
                }
                else if ($result === false) {
                        return $this->error(['msg' => 'Não foi possível criar novo automóvel']);
                }
                else if($result instanceof Automovel) {
                        $componentes = $args['componentes_ids'] ?? array();
                        if (is_array($componentes) && !empty($componentes)) {   //se há componentes a serem adicionados
                                $newlyCreated = $this->DTO->getByPlaca($result->placa);
                                $this->DTO->compareAndUpdateComponentes($newlyCreated->id, $componentes);
                        }

                        return $this->success(['msg' => 'Automovel criado com sucesso']);
                }
    }
    public function update($args) {
            $result = $this->DTO->update($args);

            if ($result instanceof DefaultErrorResponse) {
                    return $this->error($result);
            }

            else if($result === false) {
                    return $this->error(['msg' => 'Não foi possível atualizar automóvel']);
            }
            else if($result === true) {
                    $componentes = $args['componentes_ids'] ?? array();

                    $this->DTO->compareAndUpdateComponentes($args['id'], $componentes);
                    return $this->success(['msg' => 'Editado com sucesso!']);
            }
    }
    public function deleteMany($args) {
        if (isset($args['items'])) {
            
            if ($this->DTO->deleteMany($args['items'])) {
                    $res = ['msg' => 'Deletado  itens com sucesso!'];
                    $this->success($res);
            }
            else {
                    $res = ['msg' => 'Erro ao deletar itens selecionados'];
                    $this->error($res);
            }
        }
        else {
            $res = ['msg' => 'Requisição inválida'];
            $this->error($res);
        }
        
    }
    public function getComponentes($args) {
            $automovel_id = $args['id'] ?? null;

            if ($automovel_id == null) {
                $err  = ['msg' => 'Argumentos inválidos'];
                $this->error($err);
            }
            else {
                $res = $this->DTO->getComponentes($automovel_id);
                $this->send($res);
            }

    }
    public function getpage($args) {
        $page = $args['page'] ?? 0;
        $itensPerPage = $args['itemsperpage'] ?? 3;
        $orderby = $args['orderby'] ?? 'data_add';
        $filter = $args['filter'] ?? null;
        $data = $this->DTO->getPage($page, $itensPerPage, $orderby, $filter);
        $this->send($data);
    }
    public function relatorioForm($args) {
        $pageTitle = $this->pageTitle;
        $activePage = $this->activePage;
        $templateStyles = $this->templateStyles;
        require_once _VIEWS_ROOT.$this->templateFolder.'/automoveis.relatorio.form.php';
        die;
    }
    public function geraRelatorio($args) {
            /**
             * orderby:
             *      id
             *      data add
             *      data edit
             *      preco
             *      placa
             * order:
             *      asc
             *      desc
             * filterby:
             *      descricao
             *      nome_marca
             * filterby_value: texto filtro
             */
        $orderby = $this->relatorioValidaOrderby($args['orderby'] ?? 'id');
        $order = $this->relatorioValidaOrder($args['order'] ?? 'asc');
        $filterby = $this->relatorioValidaFilterby($args['filterby'] ?? 'descricao');
        $filtro = $this->relatorioValidaFilterbyValue($args['filterby_value']);
        
        $sql = "SELECT a.*, m.nome AS nome_marca FROM automovel AS a JOIN marca AS m ON m.id = a.marca_id WHERE {$filterby} LIKE :filter ORDER BY {$orderby} {$order}; ";
        $stmt = $this->DTO->conn->prepare($sql);
        $stmt->bindValue(':filter', '%'.$filtro.'%');
        $stmt->execute();

        $queryResult = $stmt->fetchAll();

        $pageTitle = $this->pageTitle;
        $activePage = $this->activePage;
        $templateStyles = $this->templateStyles;
        require_once _VIEWS_ROOT.$this->templateFolder.'/automoveis.relatorio.form.php';
        die;

    }
    private function relatorioValidaOrderby($orderby = 'id') {
        switch($orderby) {
            case 'id':
                return 'a.id';
            case 'data_add';
                return 'a.data_add';
            case 'data_edit':
                return 'a.data_edit';
            case 'preco':
                return 'a.preco';
            case 'placa':
                return 'a.placa';
            default:
                return 'a.descricao';

        }
    }
    private function relatorioValidaOrder($order = 'asc') {
        switch($order) {
            case 'desc':
                return 'desc';
            default:
                return 'asc';
        }
    }
    private function relatorioValidaFilterby($filterby = 'descricao') {
        switch($filterby) {
            case 'nome_marca':
                return 'm.nome';
            default:
                return 'a.descricao';
        }
    }
    private function relatorioValidaFilterbyValue($filtro = '') {
        return $filtro;
    }
    public function getAutomovel($args) {
        $errMsg = 'Não foi possível localizar o id solicitado!';
        $id = $args['id'] ?? null;
        if ($id == null) {
            $this->error($errMsg);
        }
        else {
            $result = $this->DTO->getById($id);
            if ($result == null) {
                $this->error($errMsg);
            }
            else {
                $this->send($result);
            }
        }
    }
}
?>