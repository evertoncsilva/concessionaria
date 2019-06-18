<?php
    require_once __DIR__.'/../../util/config.php';
    require_once 'database.connector.php';
    require_once __DIR__.'/../models/componente.model.php'; 
    /**
     * Classe Model
     */
    class DTO extends DatabaseConnector
    {
        public $tableName;
        public $properties = array();
        public $modelName;
        public $primaryKey = "id";
    
        /**
         * DTO constructor
         *
         * @param [string] $tableName nome da tabela e do modelo
         */
        protected function __construct($tableName, $modelName)
        {
            parent::__construct();
            $this->tableName = $tableName;
            $this->modelName = $modelName;
        }
        protected function setPrimaryKey($key) {
            $this->primaryKey = $key;
        }
        /**
         * Retorna todos os itens da tabela
         *
         * @return Model[] array de models
         */
        public function getAll(): ?array
        {
            $sql = "SELECT * FROM {$this->tableName}";

            $data = $this->query($sql);
            
            if($data)
            {
                $result = array();
                foreach ($data as $key => $value) {
                    array_push($result, $this->modelName::create($value));
                }

                /** @var Model[] */
                return $result;
            }
            return null;
        }
        public function getTotalAmount() : int
        {
            $sql = "SELECT COUNT(*) as count FROM {$this->tableName}";
            $data = $this->query($sql);
            $result = $data[0]['count'];

            return $result;
        }
        public function getRangeById($startRange, $endRange) : ?array
        {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} >= {$startRange} AND {$this->primaryKey}  <=  {$endRange} ";
            $data = $this->query($sql);

            if($data)
            {
                $result = array();
                foreach($data as $key => $value) {
                    array_push($result, $this->modelName::create($value));
                }

                return $result;
            } 
            else 
            {
                return null;
            }
        }
        public function getNextPage($lastItem, $pageSize, $orderBy) : ?array
        {
            $startSQL = (isset($lastItem)) ? "WHERE {$orderBy} > {$lastItem}" : "";
            $pageSize = (isset($pageSize) && is_numeric($pageSize)) ? $pageSize : 50;
            $sql = "SELECT * FROM {$this->tableName} {$startSQL} ORDER BY `{$orderBy}` ASC LIMIT {$pageSize}";
            $itemCountSQL = "SELECT COUNT(*) as count FROM {$this->tableName}";
            if(isset($lastItem)) 
            {
                $itemsBeforeSQL = "SELECT COUNT(*) as count FROM {$this->tableName} WHERE {$orderBy} <= {$lastItem}";
                $itemsBeforeResult = $this->query($itemsBeforeSQL);

            }
            
            
            $data = $this->query($sql);
            $count = $this->query($itemCountSQL);
            $totalItemsBefore = (isset($itemsBeforeResult[0]['count'])) ? $itemsBeforeResult[0]['count'] : 0;
            $totalItemCount = $count[0]['count'];
            
            if($data)
            {
                $result = array();
                $result['data'] = array();
                foreach($data as $key => $value) {
                    array_push($result['data'], $this->modelName::create($value));
                }
                $result['total-pages'] = ceil($totalItemCount / $pageSize);
                $result['items-before'] = $totalItemsBefore;
                $result['pages-before'] = ($totalItemsBefore > 0) ? ceil($totalItemsBefore / $pageSize) : 0;
                $result['total-item-count'] = $totalItemCount;


                return $result;
            } 
            else 
            {
                return null;
            }
        }

        public function getPreviousPage($lastItem, $pageSize, $orderBy)
        {
            $endSQL = (isset($lastItem)) ? "WHERE {$orderBy} < {$lastItem}" : "";
            $pageSize = (isset($pageSize) && is_numeric($pageSize)) ? $pageSize : 50;
            $sql = "SELECT * FROM {$this->tableName} {$endSQL} ORDER BY `{$orderBy}` ASC LIMIT {$pageSize} OFFSET {$pageSize}";
            $itemCountSQL = "SELECT COUNT(*) as count FROM {$this->tableName}";
            if(isset($lastItem)) 
            {
                $itemsBeforeSQL = "SELECT (COUNT(*) - {$pageSize}) as count FROM {$this->tableName} WHERE {$orderBy} <{$lastItem}";
                $itemsBeforeResult = $this->query($itemsBeforeSQL);

            }

            $data = $this->query($sql);
            $count  = $this->query($itemCountSQL);
            $totalItemsBefore = (isset($itemsBeforeResult[0]['count'])) ? $itemsBeforeResult[0]['count'] : 0;
            $totalItemCount = $count[0]['count'];

            if($data)
            {
                $result = array();
                $result['data'] = array();
                foreach($data as $key => $value) {
                    array_push($result['data'], $this->modelName::create($value));
                }
                $result['total-pages'] = ceil($totalItemCount / $pageSize);
                $result['items-before'] = $totalItemsBefore;
                $result['pages-before'] = ($totalItemsBefore > 0) ? ceil($totalItemsBefore / $pageSize) : 0;

                $teste = $result;
                return $result;
            }
            else 
            {
                return null;
            }



        }

        public function getById($id): Model
        {
            $sql = "SELECT * FROM {$this->tableName} WHERE id = {$id}";
            $data = $this->query($sql);
            if ($data) 
            {
                $result = $this->modelName::create($data[0]);
                return $result;
            } else {
                return null;
            }
        }
        /**
         * Encontra item no banco buscando pela coluna especificada
         *
         * @param [string] $key nome da coluna para busca
         * @param [type] $value valor a ser buscado
         *
         * @return void
         */
        public function findByKey($key, $value)
        {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$key} = {$value}";
            $data = $this->query($sql);

            if($data)
            {
                $result = array();
                foreach($data as $key => $value) {
                    array_push($result, $this->modelName::create($value));
                }
            }
            else 
            {
                return null;
            }

        }

        public function isColumnNameValid($nomeColuna) : bool
        {
            $model = $this->modelName::createEmpty();
            
            if(property_exists($model, $nomeColuna)) {
                return true;
            }
            return false;
        }

        //TODO: insertNew($data)
        //TODO: deleteOne_byId($id)
        //TODO: deleteMany_byId($array)
}
