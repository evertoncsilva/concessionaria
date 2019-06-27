<?php
require_once __DIR__.'/../../util/config.php';
require_once 'default-error-response.model.php';
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

    private $validationStatus = true;

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
                if(ctype_space($value)) $value = null;
                $value = trim($value);
                if($instance->{$key} === null)
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
        $instance   = Automovel::create($args);
        $formErrors = array();
        $errors = new DefaultErrorResponse(['msg' => "Erro(s) no preenchimento do formulário", 'code' => 1010]);

        $instance->validaDescricao($args['descricao'], $errors);
        $instance->validaPlaca($args['placa'], $errors);
        $instance->validaRenavam($args['renavam'], $errors);
        $instance->validaAnoModelo($args['ano_modelo'], $errors);
        $instance->validaAnoFabricacao($args['ano_fabricacao'], $errors);
        $instance->validaCor($args['cor'], $errors);
        $instance->validaKm($args['km'], $errors);
        $instance->validaMarcaID($args['marca_id'], $errors);
        $instance->validaPreco($args['preco'], $errors);
        $instance->validaPrecoFipe($args['preco_fipe'], $errors);
        
        if($instance->isValid()) return $instance;
        else return $errors;

    }

    public function validaDescricao($descricao, &$errors)
    {
        if(!isset($descricao) || $descricao === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['descricao' => 'Não pode ser vazio']);
            }
        if(strlen($descricao) > 60)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['descricao' => 'Tamanho máximo 60 caracteres']);
            }
        if(strlen($descricao) < 8)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['descricao' => 'Pelo menos 8 caracteres']);
            }    
    }

    public function validaPlaca($placa, &$errors)
    {
        $regex = '/^[a-z]{3}[-]?[0-9]{4}$/mi';
        preg_match_all($regex, $placa, $matches, PREG_SET_ORDER, 0);
        
        if(sizeof($matches) < 1)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['placa' => 'Placa inválida']);
            }
    }

    public function validaRenavam($renavam, &$errors) 
    {
        if(!is_numeric($renavam))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['renavam' => 'Apenas números!']);
            }
        if(strlen($renavam > 11))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['renavam' => 'Máximo de 11 dígitos']);
            }
    }

    public function validaAnoModelo($ano, &$errors)
    {
        if($ano === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_modelo' => 'Ano do modelo é obrigatório!']);
            }
        if($this->ano_fabricacao === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_fabricacao' => 'Ano de fabricação é obrigatório!']);
            }    
        if(!is_numeric($ano))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_modelo' => 'Apenas números']);
            }
        if($ano > date('Y') +1)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_modelo' => 'Ano inválido!']);
            }   
        if($ano < 1900)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_modelo' => 'Ano inválido!']);
            }  
        if($ano < $this->ano_fabricacao)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_modelo' => 'Ano do modelo menor que fabricação!']);
            } 
        if($ano > $this->ano_fabricacao +1)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_modelo' => 'Ano de modelo inválido!']);
            }            
    }
    public function validaAnoFabricacao($ano, &$errors)
    {
        if($ano === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_fabricacao' => 'Ano do modelo é obrigatório!']);
            }
        if($this->ano_modelo === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_fabricacao' => 'Ano de modelo é obrigatório!']);
            }    
        if(!is_numeric($ano))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_fabricacao' => 'Apenas números!']);
            }
        if($ano > date('Y'))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_fabricacao' => 'Ano inválido!']);
            }   
        if($ano < 1900)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_fabricacao' => 'Ano inválido!']);
            }  
        if($ano > $this->ano_modelo)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['ano_fabricacao' => 'Ano de fabricação maior que modelo!']);
            }            
    }
    public function validaCor($cor, &$errors)
    {
        if(strlen($cor) > 20)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['cor' => 'Máximo de 20 caracteres!']);
            }
    }
    public function validaKm($km, &$errors)
    {
        if($km < 0)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['km' => 'Km menor que zero!']);
            }  
        if(!is_numeric($km))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['km' => 'Apenas números!']);
            }  
        if($km > 999999)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['km' => 'Máximo 999999!']);
            }  
    }
    public function validaMarcaID($id, &$errors)
    {
        if($id === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['marca_id' => 'Marca obrigatória!']);
            }   
        if($id < 1)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['marca_id' => 'Marca inválida!']);
            }
    }
    public function validaPreco($preco, &$errors)
    {
        if($preco === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['preco' => 'Preço obrigatório!']);
            }   
        $preco = str_replace(',','.', $preco);  // substitui virgulas por pontos
            $this->preco = $preco;
        if(!is_numeric($preco))
                 if(!is_numeric($preco))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['preco' => 'Apenas números!']);
            }
        if($preco >= 1000000)
        {
            $this->validationStatus = false;
            return $errors->addFormError(['preco' => 'Número acima do permitido!']);
        }
    }
    public function validaPrecoFipe($preco, &$errors)
    {
        if($preco === null)
            {
                $this->validationStatus = false;
                return $errors->addFormError(['preco_fipe' => 'Preço obrigatório!']);
            }   
        $preco = str_replace(',','.', $preco);  // substitui virgulas por pontos
            $this->preco = $preco;
        if(!is_numeric($preco))
                 if(!is_numeric($preco))
            {
                $this->validationStatus = false;
                return $errors->addFormError(['preco_fipe' => 'Apenas números!']);
            }
        if($preco >= 1000000)
        {
            $this->validationStatus = false;
            return $errors->addFormError(['preco_fipe' => 'Número acima do permitido!']);
        }
    }
    public function isValid() :boolean
    {
        return $this->validationStatus;
    }
}
?>