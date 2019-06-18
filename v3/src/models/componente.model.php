<?php
require_once __DIR__.'/../../util/config.php';
require_once 'model.php';

class Componente extends Model {
    public $id = null;
    public $nome = null;
    public $descricao = null;

    private function __construct() {
        parent::__construct();
    }

    public static function create(array $data) {

        $instance = new Componente();
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key))
            {
                $instance->{$key} = $value;
            }
        }
        return $instance;
    }
    

    public static function createEmpty() 
    {
        $instance = new Componente();
        return $instance;
    }
}
?>