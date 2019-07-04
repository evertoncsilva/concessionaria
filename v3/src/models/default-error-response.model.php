<?php
class DefaultErrorResponse {
    public $message;
    public $code;
    public $formErrors = array();
    public $info = array();
    protected $http_response_code = 400;
    public function __construct($args = null, PDOStatement $pdo = null) {

        if ($args != null && !empty($args)) { // argumentos passados via array
            $this->message = (isset($args['msg'])) ? $args['msg'] : "Internal error";
            $this->code = (isset($args['error-code'])) ? $args['error-code'] : 1;
            $this->code = $args['code'] ?? 1;

            if (isset($args['info'])) { 
                $this->info = $args['info']; 
            }
            if (isset($args['formErrors'])) { 
                $this->formErrors = $args['formErrors']; 
            }
        }
        else if ($pdo != null) {
            $this->errorFromPDOStatement($pdo);
        }
        else {
            throw new Exception("Argumentos inválido no construtor do DefaultErrorResponse"); 
        }
    }
    /**
     * Adiciona um erro de form às informações do erro, 
     * para posteriormente ser consumido pelo form
     *
     * @param [array] $formError
     *          ex.: ['propriedade' => 'msg de erro']
     * @return void
     */
    public function addFormError($error) {
        array_push($this->formErrors, $error);
    }
    public function httpCode($code = null) {
        if ($code === null) {
            return $this->http_response_code;
        }
        else {
            if (is_numeric($code)) {
                $this->http_response_code = $code;
            }
            else {
                throw new Exception("DefaultErrorResponse: código de erro http deve ser numérico!");
            }
        }
    }
}
?>