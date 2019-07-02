<?php
    abstract class Model {
        protected function __construct() {
            //vazio
        }
        public static abstract function createEmpty();
        public static abstract function create(array $params);
        public abstract function getTableProperties() : array ;

        public static abstract function validateAndCreate($args);
    }
?>