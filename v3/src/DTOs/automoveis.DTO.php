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
            $auto = $this->modelName::validateAndCreate($args);
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

            $sql = "UPDATE {$this->tableName}"
                    ." SET"
                        ." descricao='{$args['descricao']}',"
                        ." placa='{$args['placa']}',"
                        ." renavam='{$args['renavam']}',"
                        ." ano_modelo={$args['ano_modelo']},"
                        ." ano_fabricacao={$args['ano_fabricacao']},"
                        ." cor='{$args['cor']}',"
                        ." km={$args['km']},"
                        ." marca_id={$args['marca_id']},"
                        ." preco={$args['preco']},"
                        ." preco_fipe={$args['preco_fipe']}"
                    ." WHERE id={$args['id']};";

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

        public function compareAndUpdateComponentes($auto_id, $componentes)
        {
            //componentes que já estão neste
            $sql = "SELECT c.id FROM componente AS c LEFT JOIN automovel_componente AS ac ON ac.componente_id = c.id WHERE ac.automovel_id = {$auto_id}; "; 

            $query  = $this->conn->prepare($sql);
            $query->execute();
            $existingComponentes = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            //após comparação teremos as array do que adicionar e do que remover
            $to_add     = array();
            $to_remove  = array();

            //comparação do que adicionar (o que não está na arrau das esistentes mas está na $componentes)
                foreach($componentes as $key => $id)
                {
                    if(!in_array($id, $existingComponentes)) {
                        array_push($to_add, $id);
                    }
                }
            
            //comparação do que remover (está na $existingComponentes mas não na $componentes)
                foreach ($existingComponentes as $key => $id) {
                    
                    if(!in_array($id, $componentes)) {
                        array_push($to_remove, $id);
                    }

                }
                
                
            // adicionar
            foreach ($to_add as $key => $id) {
                $sql_add = "INSERT INTO automovel_componente (automovel_id, componente_id) VALUES({$auto_id}, {$id})";
                $query = $this->conn->prepare($sql_add);
                $query->execute();
            }
            
            // remover
            foreach ($to_remove as $key => $id) {
                $sql_remove = "DELETE FROM automovel_componente WHERE automovel_id ={$auto_id} AND componente_id={$id}";
                $query = $this->conn->prepare($sql_remove);
                $query->execute();
            }
        }

    }
?>