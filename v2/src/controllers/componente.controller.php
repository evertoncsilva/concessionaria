<?php
require_once __DIR__.'/../../util/constants.php';
require_once __DIR__.'/../listModels/componentes.model.php';

class ComponenteController
{
    private static $templateFolder = "componentes";
    private static $baseTemplate = "componentesIndex.php";
    //GET Routes
    public static function get($request) 
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

    public static function post($request)
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

    public static function renderIndex() {
        include TEMPLATE_FOLDER.self::$templateFolder."/".self::$baseTemplate;
    }

    //GET ACTIONS

    public static function getById($id) 
    {
        header('Content-Type: application/json');
        $componente = new ComponentesModel();
        $data = $componente->getById($id);

        if($data)
        {
            $return['sucess'] = true;
            $return['resultCount'] = 1;
            $return['data'] = array($data);
            echo json_encode($return);
            die;
        } else {
            $return['sucess']        = false;
            $return['resultCount']   = 0;
            $return['error_code']    = 1;
            $return['error_message'] = 'Nenhum resultado encontrado';
            $return['data'] = array();
            echo json_encode($return);
            die;
        }
    }

    function getAll() {
        header('Content-Type: application/json');
        $componente = new ComponentesModel();
        $data = $componente->getAll();

        if($data)
        {
            $return['sucess'] = true;
            $return['resultCount'] = sizeof($data);
            $return['data'] = $data;
            echo json_encode($return);
            die;
        } else {
            $return['sucess']        = false;
            $return['resultCount']   = 0;
            $return['error_code']    = 1;
            $return['error_message'] = 'Nenhum resultado encontrado';
            $return['data'] = array();
            echo json_encode($return);
            die;
        }
    }

    public static function getPaginated($request)
    {
        header('Content-Type: application/json');
        $start = (isset($request['start']) && $request['start'] > 0)    ?  $request['start'] : 1;
        $end   = (isset($request['end']) && $request['end'] > $start)   ? $request['end'] : ($start + MAX_PAGE_SIZE);
        $componente = new ComponentesModel();

        $data       = $componente->getRangeById($start, $end);
        $totalCount = $componente->getTotalAmount();
        if($data)
        {
            $return['sucess'] = true;
            $return['resultCount'] = sizeof($data);
            $return['countAll'] = $totalCount;
            $return['data'] = $data;
            echo json_encode($return);
            die;
        } else {
            $return['sucess']        = false;
            $return['resultCount']   = 0;
            $return['error_code']    = 1;
            $return['error_message'] = 'Nenhum resultado encontrado';
            $return['data'] = array();
            echo json_encode($return);
            die;
        }

    }

    //POST

    public static function create($request)
    {
        header('Content-Type: application/json');
        $componente = new ComponentesModel();
        $componente->insertNew($request);
        die;
    }

}


?>