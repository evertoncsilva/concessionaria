<?php
require_once __DIR__.'/../../util/config.php';
require_once 'model.php';

class Componente extends Model {
    public $id = null;
    public $nome = null;
    public $descricao = null;

    private function __construct()  {
        parent::__construct();
    }
    public function getTableProperties() : array {
        return ['nome', 'descricao'];
    }
    public static function create(array $data) : Componente {

        $instance = new Componente();
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->{$key} = $value;
            }
        }
        return $instance;
    }
    public static function createEmpty() {
        $instance = new Componente();
        return $instance;
    }
    public static function validateAndCreate($args) {
        $invalid = array();

        if (!isset($args['nome'])) {
            array_push($invalid, 'nome');
        } 
        elseif (!isset($args['descricao'])) {
            array_push($invalid, 'descricao');
        } 
        else {
            return static::create($args);
        }
        return null;
    }
}
?>