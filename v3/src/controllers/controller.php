<?php
require_once __DIR__.'/../../util/config.php';
require_once __DIR__.'/../DTOs/DTO.php';
require_once __DIR__.'/../models/default-error-response.model.php';
require_once __DIR__.'/../models/default-success-response.model.php';

class Controller {
    private     $templateFolder;
    private     $baseTemplate;
    private     $modelName;
    public      $routes;

    public $pageTitle;
    public $activePage;
    public $templateStyles;
    protected $DTO;
    protected $requireAuth = false;

    protected function __construct($name, DTO $DTO) 
    {
        $nameLowerCase = strtolower($name);
        $nameUcFirst = ucfirst($nameLowerCase);

        $this->templateFolder   = $nameLowerCase;
        $this->baseTemplate     = $nameLowerCase.'_index.php';
        $this->modelName        = $nameUcFirst;
        $this->pageTitle        = $nameUcFirst;
        $this->activePage       = $nameLowerCase;
        $this->templateStyles   = $nameLowerCase;
        $this->DTO              = $DTO;
    }

    public function renderIndex() 
    {
            $pageTitle = $this->pageTitle;
            $activePage = $this->activePage;
            $templateStyles = $this->templateStyles;
            require_once _VIEWS_ROOT.$this->templateFolder.'/index.php';
            die;
    }
    public function renderLogin($msg = null, bool $isSuccess = false)
    {
            $pageTitle = "Login";
            $activePage = "Login";
            $templateStyles = "login";
            $msg = $msg;
            $success = $isSuccess;
            require_once _VIEWS_ROOT.'login/index.php';
            die;
    }

    public function send($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }

    /**
     * Evia um Json com mensagem de erro
     *
     * @param [array] $args
     *              [   'response-code' -> Código HTTP de resposta, default: 400
     *                  'msg'           -> Mensagem de resposta, default: "Internal error"
     *                  'error-code'    -> Código de erro, default: 1
     *              ]
     * @return void
     */
    public function error($args)
    {
        header('Content-Type: application/json');

        //ACEITANDO DefaultErrorResponse
        if($args instanceof DefaultErrorResponse)
            {
                http_response_code($args->http_code());
                echo json_encode($args);
                die;
            }


        //ACEITANDO ARRAY COMO ARGUMENTO
        $responseCode = isset($args['response-code']) ? $args['response-code'] : 400;
        $errocode = isset($args['error-code']) ? $args['error-code'] : null;
        $msg = isset($args['msg']) ? $args['msg'] : null;

        http_response_code($responseCode);

        $error_obj = new DefaultErrorResponse($args);

        echo json_encode($error_obj);

        die;
    }
    public function success($args) {
        http_response_code(200);
        $this->send(new DefaultSuccesResponse($args));
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
        session_start();

        if($this->requireAuth && (!isset($_SESSION['login']) || $_SESSION['login'] == false))
            {
                $this->renderLogin();
            }
        else
            {
                if(empty($req)) {
                    $this->renderIndex();
                }
                elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $keys = array_keys($get);
                    $route = $keys[0];
                    if($this->isValidRoute($route, 'get')) 
                    {
                        $routeMethod = $this->routes['get'][$route];
                        return $this->$routeMethod($req);
                    }
                    else 
                    {
                        $this->error(['response-code'=> 404, 'msg' => 'Route not found']);
                    }
                }
                elseif($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $keys = array_keys($post);
                    $route = (isset($post['action'])) ? $post['action'] : '';
                    if($this->isValidRoute($route, 'post'))
                    {
                        $routeMethod = $this->routes['post'][$route];
                        return $this->$routeMethod($post);
                    } 
                    else 
                    {
                        $this->error(['response-code'=> 404, 'msg' => 'Route not found']);
                    }
                }
            }
        

        // TODO: render 404

    }

    protected function setRequireAuth(bool $option)
    {
        $this->requireAuth = $option;
    }
}

?>