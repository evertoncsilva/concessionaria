<?php
class DefaultErrorResponse {
    public $message;
    public $code;
    public $formErrors = array();
    public $info = array();


    public function __construct($args) {
        $this->message = (isset($args['msg'])) ? $args['msg'] : "Internal error";
        $this->code = (isset($args['error-code'])) ? $args['error-code'] : 1;

        if(isset($args['info'])) { $this->info = $args['info']; }
        if(isset($args['formErrors'])) { $this->formErrors = $args['formErrors']; }
    }
}

?>