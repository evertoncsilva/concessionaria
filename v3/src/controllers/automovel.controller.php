<?php
require_once __DIR__.'/../DTOs/automoveis.DTO.php';
require_once 'controller.php';
class AutomovelController extends Controller {
    
    public $routes = [
        'get' => [
            'all'                   => 'getAll',
            'paginated'             => 'getPaginated',
            'get_componentes'       => 'get_componentes',
            'getpage'              => 'getpage'
        ],
        'post' => [
            'delete_one'            => 'delete_one',
            'create'                => 'create',
            'update'                => 'update',
            'delete_many'           => 'delete_many'
        ]
    ];
    public function __construct() 
    {
        $dto = new AutomoveisDTO();
        parent::__construct('automovel', $dto);
    }
    public function getAll($args)
    {
        if(!isset($args))
        {
            $args = array();
        }
        $data = $this->DTO->getAll($args);
        $this->send($data);
    }
    public function getPaginated($args) 
    {
        $ar = $args;
        $lastItem = null;
        $orderBy = $this->DTO->primaryKey;
        $pageSize = 25; // tamanho de pagina default
        $action = 'next';

        if(isset($args['last-item']))
            $lastItem = $args['last-item'];
        if(isset($args['page-size']) && is_numeric($args['page-size']))
            $pageSize = $args['page-size'];
        if(isset($args['order-by']) && $this->DTO->isColumnNameValid($args['order-by']))
            $orderBy = $args['order-by'];
        if(isset($args['action']) && $args['action'] == 'previous')
            $action = 'previous'; 

        
        $itens = $this->DTO->getAll();

        if($itens)  $this->send($itens);
    }
    public function index() 
    {
        renderIndex();
    }
    public function delete_one($args) 
    {
        //TODO: deletar componentes do automóvel também
        $id = (isset($args['id'])) ? $args['id'] : null;

        if($id != null && is_numeric($id))
        {
            if($this->DTO->delete_one($id)) 
            {
                $this->send(new DefaultSuccesResponse(['msg' => "Deletado item {$id} com sucesso!"]));
            }
            else
            {
                $this->error(['msg' => "Id não localizado", 'error-code' => 2]);
            }
        }
        else 
        {
            $this->error(['msg'=>"Argumentos inválidos", 'error-code' => 3]);
        }
    }
    public function create($args)
    {
                $result = $this->DTO->create($args);
                if($result instanceof DefaultErrorResponse)
                    {
                        return $this->error($result);
                    }
                else if($result === false)
                    {
                        return $this->error(['msg' => "Não foi possível criar novo automóvel"]);
                    }
                else if($result instanceof Automovel)
                    {
                        $componentes = $args['componentes_ids'] ?? array();
                        if(is_array($componentes) && !empty($componentes))  //se há componentes a serem adicionados
                            {
                                $newlyCreated = $this->DTO->getByPlaca($result->placa);
                                $this->DTO->compareAndUpdateComponentes($newlyCreated->id, $componentes);
                            }

                        return $this->success(['msg' => "Automovel criado com sucesso"]);
                    }
            
    }
    public function update($args) 
    {
            $result = $this->DTO->update($args);

            if($result instanceof DefaultErrorResponse)
                {
                    return $this->error($result);
                }

            else if($result === false)
                {
                    return $this->error(['msg' => "Não foi possível atualizar automóvel"]);
                }
            else if($result === true)
                {
                    $componentes = $args['componentes_ids'] ?? array();

                    $this->DTO->compareAndUpdateComponentes($args['id'], $componentes);
                    return $this->success(['msg' => "Editado com sucesso!"]);
                }

            //update dos componentes

            //valida_AndUpdate($automovel)
            //updateComponentes($args['id'], $componentes)
    }
    public function delete_many($args)
    {
        if(isset($args['items']))
        {
            
            if($this->DTO->delete_many($args['items']))
                {
                    $res = ['msg' => "Deletado  itens com sucesso!"];
                    $this->success($res);
                }
            else
                {
                    $res = ['msg' => "Erro ao deletar itens selecionados"];
                    $this->error($res);
                }
        }
        else
        {
            $res = ['msg' => "Requisição inválida"];
            $this->error($res);
        }
        
    }
    public function get_componentes($args)
    {
            $automovel_id = $args['id'] ?? null;

            if($automovel_id == null)
            {
                $err  = ['msg' => 'Argumentos inválidos'];
                $this->error($err);
            }
            else 
            {
                $res = $this->DTO->getComponentes($automovel_id);
                $this->send($res);
            }

    }
    public function getpage($args)
    {
        $page = $args['page'] ?? 0;
        $itensPerPage = $args['itemsperpage'] ?? 10;
        $orderby = $args['orderby'] ?? 'id';
        $data = $this->DTO->getPage($page, $itensPerPage, $orderby);
        $this->send($data);
    }

}
?>