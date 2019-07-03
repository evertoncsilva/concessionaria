<?php
    abstract class Model {
        protected function __construct() {
            //vazio
        }
        public static abstract function createEmpty();
        public static abstract function create(array $params);
        public static abstract function validateAndCreate($args);
        
        public abstract function getTableProperties() : array ;
    }
?>