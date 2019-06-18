<?php
require_once __DIR__.'/../../util/config.php';
require_once __DIR__.'/../DTOs/DTO.php';

class Controller {
    private     $templateFolder;
    private     $baseTemplate;
    private     $modelName;
    public   $routes;

    public $pageTitle;
    public $activePage;
    public $templateStyles;
    protected $DTO;

    protected function __construct($name, DTO $DTO) 
    {
        $nameLowerCase = strtolower($name);
        $nameUcFirst = ucfirst($nameLowerCase);

        $this->templateFolder = $nameLowerCase;
        $this->baseTemplate = $nameLowerCase.'_index.php';
        $this->modelName = $nameUcFirst;
        $this->pageTitle = $nameUcFirst;
        $this->activePage = $nameLowerCase;
        $this->templateStyles = $nameLowerCase;
        $this->DTO = $DTO;
    }

    public function renderIndex() 
    {
            $pageTitle = $this->pageTitle;
            $activePage = $this->activePage;
            $templateStyles = $this->templateStyles;
            require_once _VIEWS_ROOT.$this->templateFolder.'/index.php';
            die;
    }

    public function send($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }

    /**
     *  Recebe uma array de argumentos que inicializam as rotas:
     *  array['opcoes']
     *          ['tipo']    Tipo da rota, 'get' ou 'post'
     *          ['rota']    Nome da rota, ex 'index'
     *          ['metodo']  Metodo chamado ex 'getAll'
     * @param [array] $args
     * @return void
     */
    protected function set_route($args) {

    }
    protected function isValidRoute($route, $type)
    {
        if (isset($this->routes[$type])) {

            if (array_key_exists($route, $this->routes[$type])) {
                return true;
            }
        }

        return false;
    }
    public function request()
    {
        $req    = $_REQUEST;
        $post   = $_POST;
        $get    = $_GET;

        if(empty($req)) {
            $this->renderIndex();
        }
        elseif (!empty($get)) {
            $keys = array_keys($get);
            $route = $keys[0];
            if($this->isValidRoute($route, 'get')) 
            {
                $routeMethod = $this->routes['get'][$route];
                return $this->$routeMethod($req);
            }
        }

    }
}

?>