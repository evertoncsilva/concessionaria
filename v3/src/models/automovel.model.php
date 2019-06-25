<?php
require_once __DIR__.'/../../util/config.php';
require_once 'model.php';

class Automovel extends Model {
    public $id              = null;
    public $descricao       = null;
    public $placa           = null;
    public $renavam         = null;
    public $ano_modelo      = null;
    public $ano_fabricacao  = null;
    public $cor             = null;
    public $km              = null;
    public $marca_id        = null;
    public $preco           = null;
    public $preco_fipe      = null;
    public $nome_marca      = null;
    public $componentes     = null;

    private function __construct() 
    {
        parent::__construct();
    }
    public function getTableProperties() : array 
    {
        return ['placa', 'descricao', 'renavam', 'ano_modelo', 'ano_fabricacao', 'cor', 'km', 'marca_id', 'preco', 'preco_fipe'];
    }
    public static function create(array $data) : Automovel
    {

        $instance = new Automovel();
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
        $instance = new Automovel();
        return $instance;
    }
    public static function validateAndCreate($args) 
    {
        //TODO: VALIDAÇÃO DO AUTOMOVEL
        $instance = new Automovel();
        $formErrors = array();

        // DESCRICAO
        // PLACA
        // RENAVAM
        // ANO_MODELO
        // ANO_FABRICACAO
        // COR
        // KM
        // MARCA_ID
        // PRECO
        // PRECO_FIPE



        // ok -> instancia
        // error -> new DefaultErrorResponse($args)
        // depois no retorno checa se instanceof Automovel ou DefaultErrorResponse

    }
}
?>