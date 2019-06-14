<?php
    require_once 'database.model.php';
    require_once __DIR__.'/../models/componente.model.php';

    class ComponentesModel extends DatabaseModel
    {
        public $modelName = 'ComponenteModel';
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
            parent::__construct();
        }

        /**
         * Retorna todos os itens da tabela
         *
         * @return ComponenteModel[]
         */
        public function getAll(): ?array
        {
            $sql = "SELECT * FROM {$this->tableName} LIMIT 10 ";
            $data = $this->query($sql);

            if($data)
            {
                $result = array();
                foreach ($data as $key => $value) {
                    array_push($result, ComponenteModel::create($value));
                }

                return $result;
            } else {
                return null;
            }
        }

        public function getRangeById($startRange, $endRange): ?array
        {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} >= {$startRange} AND {$this->primaryKey} <= {$endRange}";
            $data = $this->query($sql);

            if($data) 
            {
                $result = array();
                foreach ($data as $key => $value) {
                    array_push($result, ComponenteModel::create($value));
                }

                return $result;
            } else {
                return null;
            }
        }

        /**
         * Retona um componente localizado por id
         *
         * @param [int] $id
         * @return ComponenteModel|null
         */
        public function getById($id): ?ComponenteModel
        {
            $sql = "SELECT * FROM {$this->tableName} WHERE id = {$id}";
            $data = $this->query($sql);
            if ($data) 
            {
                $result = ComponenteModel::create($data[0]);
                return $result;
            } else {
                return null;
            }
        }

        public function getTotalAmount() : int
        {
            $sql = "SELECT COUNT(*) as count FROM {$this->tableName}";
            $data = $this->query($sql);
            $result = $data[0]['count'];
            return $result;
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

        //INSERTS
        public function insertNew($data)
        {
            $object_vars = get_object_vars($this->modelName::createEmpty());
            $sql = "INSERT INTO {$this->tableName} values(".implode(',',$object_vars).")";
            echo $sql;
        }
        //DELETE
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
?>