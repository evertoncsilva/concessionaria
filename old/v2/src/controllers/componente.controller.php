<?php
require_once __DIR__.'/../../util/constants.php';
require_once __DIR__.'/../listModels/componentes.model.php';

class ComponenteController
{
    private $templateFolder  = "componentes";
    private $baseTemplate    = "componentesIndex.php";
    private $modelName       = "Componente";

    // Variáveis abaixo serão acessadas pelo render
    public  $pageTitle          = "Componentes";
    public  $activePage         = 'componentes';
    public  $templateCss        = 'componentes';

    public function __construct() {
        //construct
    }



    //GET Routes
    public function get($request) 
    {
        switch($request) 
        {
            case(isset($request['all'])):
                self::getAll();
                break;
            case(isset($request['range'])):
                self::getPaginated($request);
                break;
            case(isset($request['id'])):
                self::getById($request['id']);
            break;
            default:
                echo 'error';
            break;
        }
    }

    public function post($request)
    {
        switch($request['action']) 
        {
            case('create'):
                self::create($request);
            break;
            default:
                echo 'error post default route';
            break;
        }
    }

    public function renderIndex() {
        include TEMPLATE_FOLDER.$templateFolder."/".$baseTemplate;
    }

    //GET ACTIONS

    public function getById($id) 
    {
        $model = self::getModel();
        $data = $model->getById($id);

        if($data)
        {
            $result['sucess'] = true;
            $result['resultCount'] = 1;
            $result['data'] = array($data);
            self::send($result);
        } else {
            $result['sucess']        = false;
            $result['resultCount']   = 0;
            $result['error_code']    = 1;
            $result['error_message'] = 'Nenhum resultado encontrado';
            $result['data'] = array();
            self::send($result);
        }
    }

    public function getAll() {
        $model = self::getModel();
        $data = $model->getAll();

        if($data)
        {
            $result['sucess'] = true;
            $result['resultCount'] = sizeof($data);
            $result['data'] = $data;
            self::send($result);
        } else {
            $result['sucess']        = false;
            $result['resultCount']   = 0;
            $result['error_code']    = 1;
            $result['error_message'] = 'Nenhum resultado encontrado';
            $result['data'] = array();
            self::send($result);
        }
    }

    public function getPaginated($request)
    {
        $req = $request;
        $start = (isset($request['start']) && $request['start'] > 0)    ?  $request['start'] : 1;
        $end   = (isset($request['end']) && $request['end'] > $start)   ? $request['end'] : ($start + MAX_PAGE_SIZE);
        $model = self::getModel();

        $data       = $model->getRangeById($start, $end);
        $totalCount = $model->getTotalAmount();
        if($data)
        {
            $result['sucess'] = true;
            $result['resultCount'] = sizeof($data);
            $result['countAll'] = $totalCount;
            $result['data'] = $data;
            self::send($result);
        } else {
            $result['sucess']        = false;
            $result['resultCount']   = 0;
            $result['error_code']    = 1;
            $result['error_message'] = 'Nenhum resultado encontrado';
            $result['data'] = array();
            self::send($result);
        }

    }

    //POST

    public function create($request)
    {
        $model = $this->getModel();
        $model->insertNew($request);
        die;
    }

    // Util
    public function send($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }

    private function getModel() {
        return new $this->$modelName();
    }

}


?>