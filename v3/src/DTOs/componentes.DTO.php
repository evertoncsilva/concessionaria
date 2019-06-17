<?php
    require_once 'DTO.php';
    require_once __DIR__.'/../models/componente.model.php';

    class ComponentesDTO extends DTO
    {
        public $modelName = 'Componente';
        public $tableName = "componente";
        public $properties = array();
        public $primaryKey = "id";

        /**
         * Model constructor
         *
         * @param [string] $tableName nome da tabela do modelo
         */
        public function __construct()
        {
            parent::__construct($tableName, $modelName);
        }
    }
?>