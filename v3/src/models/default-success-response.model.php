<?php
class DefaultSuccesResponse {
    public $message;
    public $info;

    public function __construct($args) {
        if (isset($args['msg'])) {
            $this->message = $args['msg'];
        } 
        if (isset($args['info'])) {
            $this->info = $args['info'];
        } 
    }
}
?>