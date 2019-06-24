<?php
    require_once 'database.model.php';
    /**
     * Classe Model
     */
    class Model extends Database
    {
        public $tableName;
        public $properties = array();
        public $primaryKey;
    
        /**
         * Model constructor
         *
         * @param [string] $tableName nome da tabela do modelo
         */
        public function __construct($tableName)
        {
            parent::__construct();
            $this->tableName = $tableName;
        }
    
        /**
         * Retorna todos os itens da tabela
         *
         * @return void
         */
        public function getAll()
        {
            $sql = "SELECT * FROM {$this->tableName} LIMIT 10 ";
            $result = $this->query($sql);
            return $result;
        }
        public function getTotalAmount() : int
        {
            $sql = "SELECT COUNT(*) as count FROM {$this->tableName}";
            $result = $this->query($sql);
            return $result[0]['count'];
        }
    
        /**
         * Retorna um item buscado por id
         *
         * @param [int] $pKeyValue id/chave primária da busca
         *
         * @return void
         */
        public function findById($pKeyValue)
        {
            $pKey = isset($this->primaryKey) ? $this->primaryKey : "id";
            $sql = "SELECT * FROM {$this->tableName} WHERE {$pKey} = {$pKeyValue}";
            $result = self::query($sql);
            return $result;
        }
    
        /**
         * Encontra item no banco buscando pela chave especificada
         *
         * @param [string] $key nome da coluna para busca
         * @param [type] $value valor a ser buscado
         *
         * @return void
         */
        public function findBy($key, $value)
        {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$key} = {$value}";
            $result = self::query($sql);
            return $result;
        }
    
        /**
         * Efetua uma busca LIKE na coluna esepecificada uando o valor passado
         *
         * @param [string] $key coluna a ser buscada
         * @param [type] $value valor a ser buscado
         *
         * @return void
         */
        public function findLike($key, $value)
        {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$key} = %{$value}%";
            $result = self::query($sql);
            return $result;
        }
    
        /**
         * Deleta um item da tablea, tendo especificado a chave e o valor do mesmo
         *
         * @param [string] $key chave/coluna
         * @param [type] $value valor de filtro da busca
         * @return void
         */
        public function delete($key, $value)
        {
            $sql = "DELETE FROM {$this->tableName} WHERE {$key} = {$value}";
            $result = self::query($sql);
            return $result;
        }
}
