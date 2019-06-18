<?php
require_once __DIR__.'/../DTOs/componentes.DTO.php';
require_once 'controller.php';
class ComponenteController extends Controller {
    
    public $routes = [
        'get' => [
            'all' => 'getAll',
            'paginated' => 'getPaginated'
        ],
        'post' => [
        ]
    ];
    public function __construct() {
        $dto = new ComponentesDTO();
        parent::__construct('componente', $dto);
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

    public function getPaginated($args) {
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
    

}
?>