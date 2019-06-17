<?php
    abstract class Model {
        protected function __construct() {
            
        }

        public static abstract function create($params);
    }
?>