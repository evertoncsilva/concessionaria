<?php
    require_once 'DTO.php';
    require_once __DIR__.'/../models/automovel.model.php';

    class AutomoveisDTO extends DTO
    {
       
        /**
         * Model constructor
         *
         * @param [string] $tableName nome da tabela do modelo
         */
        public function __construct()
        {
            $modelName = 'Automovel';
            $tableName = "automovel";
    
            parent::__construct($tableName, $modelName);
        }
        public function create($args) {
            $sql =  "INSERT INTO {$this->tableName} (descricao, placa, renavam, ano_modelo, ano_fabricacao, cor, km, marca_id, preco, preco_fipe) " .
                    "VALUES(    '{$args['descricao']}'" . 
                            ",  '{$args['placa']}'" .
                            ",  '{$args['renavam']}'" .
                            ",  {$args['ano_modelo']}" .
                            ",  {$args['ano_fabricacao']}" .
                            ",  '{$args['cor']}'" .
                            ",  {$args['km']}" .
                            ",  {$args['marca_id']}" .
                            ",  {$args['preco']}" .
                            ",  {$args['preco_fipe']}" .
                    ") ";

            $query = $this->conn->prepare($sql);
            $query->execute();

            if($query->rowCount()) return true;
            else return false;
        }

        public function getAll(): ?array
        {
            $sql =  "SELECT a.*, m.nome AS nome_marca FROM {$this->tableName} AS a ".
                    "LEFT JOIN marca AS m ON m.id = a.marca_id";

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

        public function update($args) {

            $sql = "UPDATE {$this->tableName} SET nome='{$args['nome']}', descricao='{$args['descricao']}' WHERE id = {$args['id']}";
            $query = $this->conn->prepare($sql);
            $query->execute();
            if($query->rowCount()) 
            {
                return $this->getById($args['id']);
            }
            else return false;
        }

        public function getComponentes($automovel_id) {
            $sql = "SELECT c.id FROM componente AS c LEFT JOIN automovel_componente AS ac ON ac.componente_id = c.id WHERE ac.automovel_id = {$automovel_id}; "; 
            
            $data = $this->query($sql);

            if($data)
                return $data;
            else 
                return array();

        }
    }
?>