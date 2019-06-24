<?php
class ComponenteModel {
    public $id = null;
    public $nome = null;
    public $descricao = null;

    private function __construct() {

    }
    public static function create(array $data) {

        $instance = new ComponenteModel();
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
        $instance = new ComponenteModel();
        return $instance;
    }
}
?>