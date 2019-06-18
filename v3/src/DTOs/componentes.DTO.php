<?php
    require_once 'DTO.php';
    require_once __DIR__.'/../models/componente.model.php';

    class ComponentesDTO extends DTO
    {
       
        /**
         * Model constructor
         *
         * @param [string] $tableName nome da tabela do modelo
         */
        public function __construct()
        {
            $modelName = 'Componente';
            $tableName = "componente";
    
            parent::__construct($tableName, $modelName);
        }
    }
?>