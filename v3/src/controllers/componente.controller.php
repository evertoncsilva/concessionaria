<?php
require_once __DIR__.'/../DTOs/componentes.DTO.php';
require_once 'controller.php';
class ComponenteController extends Controller {
    
    public $routes = [
        'get' => [
            'all' => 'getAll',
            'paginated' => 'getPaginated',
        ],
        'post' => [
            'deleteOne' => 'deleteOne',
            'create' => 'create',
            'update' => 'update',
            'delete_many' => 'deleteMany'
        ]
    ];
    public function __construct() {
        $dto = new ComponentesDTO();
        parent::__construct('componente', $dto);
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
        if (isset($args['action']) && $args['action'] == 'previous') {
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
        $id = (isset($args['id'])) ? $args['id'] : null;

        if ($id != null && is_numeric($id)) {
            if ($this->DTO->deleteOne($id)) {
                $this->send(new DefaultSuccesResponse(['msg' => "Deletado item {$id} com sucesso!"]));
            }
            else {
                $this->error(['msg' => "Id não localizado", 'error-code' => 2]);
            }
        }
        else {
            $this->error(['msg'=>"Argumentos inválidos", 'error-code' => 3]);
        }
    }
    public function create($args) {
        if (!isset($args['nome'])) {

            $error = [  'msg' => "Dados inválidos",
                        'formErrors' => ['nome' => 'Não pode ser nulo'],
                        'response-code' => 400
            ];
            return $this->error($error);
        }
        elseif (trim($args['nome']) === '') {
            $error = [  'msg' => "Dados inválidos",
                        'formErrors' => ['nome' => 'Campo obrigatório'],
                        'response-code' => 400
            ];
            return $this->error($error);
        }
        else {
            $nome = $args['nome'];
            $descricao = (isset($args['descricao'])) ? $args['descricao'] : null;

            $createArgs = array('nome' => $nome, 'descricao' => $descricao);
            
            if($this->DTO->create($args)) {
                return $this->success();
            }
            else {
                return $this->error(['msg' => "Não foi possível criar novo componente"]);
            }
        }
    }
    public function update($args) {
        //TODO: validação de update
        $args = $args;
        if(     !isset($args['id']) 
            ||  !isset($args['nome'])
            ||  trim($args['id']) === '' 
            ||  trim($args['nome']) === ''
            ||  $args['id'] < 0) {
            $error = [
                'msg' => "Dados inválidos",
                'response-code' => 400
            ];
            return $this->error($error);
        }
        else {
            $data = array(
                'id' => $args['id'],
                'nome' => $args['nome'], 
                'descricao' => $args['descricao'] 
            );
            $updated =  $this->DTO->update($data);
            
            $res = [
                'msg' => 'Componente atualizado com sucesso!',
                'info' => $updated
            ];

            $this->success($res);
        }
    }
    public function deleteMany($args) {
        if (isset($args['items'])) {
            if ($this->DTO->deleteMany($args['items'])) {
                    $res = ['msg' => "Deletado  itens com sucesso!"];
                    $this->success($res);
            }
            else {
                    $res = ['msg' => "Erro ao deletar itens selecionados"];
                    $this->error($res);
            }
        }
        else {
            $res = ['msg' => "Requisição inválida"];
            $this->error($res);
        }
        
    }
}
?>