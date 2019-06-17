<?php
require_once __DIR__.'/../../util/config.php';

class Controller {
    private     $templateFolder;
    private     $baseTemplate;
    private     $modelName;
    protected   $routes = array('get' => array(), 'set' => array());

    public $pageTitle;
    public $activePage;
    public $templateStyles;

    protected function __construct($name) 
    {
        $nameLowerCase = strtolower($name);
        $nameUcFirst = ucfirst($nameLowerCase);

        $this->templateFolder = $nameLowerCase;
        $this->baseTemplate = $nameLowerCase.'_index.php';
        $this->modelName = $nameUcFirst;
        $this->pageTitle = $nameUcFirst;
        $this->activePage = $nameLowerCase;
        $this->templateStyles = $nameLowerCase;
    }

    public function GET($route, $args) {
        
        if($route == '') {

        }

        if(array_key_exists($route, $this->routes['get']))
        {
            $this->{'get_'.$route}($args);
        }

    }

    public function render() 
    {
            $pageTitle = $this->pageTitle;
            $activePage = $this->activePage;
            $templateStyles = $this->templateStyles;
            require_once _VIEWS_ROOT.$this->templateFolder.'/index.php';
            die;
    }
}

?>