<?php
require_once 'DTO.php';
require_once __DIR__.'/../models/componente.model.php';

class ComponentesDTO extends DTO {
    
    /**
     * Model constructor
     *
     * @param [string] $tableName nome da tabela do modelo
     */
    public function __construct() {
        $modelName = 'Componente';
        $tableName = "componente";

        parent::__construct($tableName, $modelName);
    }
    public function create($args) {
        $sql = "INSERT INTO {$this->tableName} (nome, descricao) VALUES('{$args['nome']}','{$args['descricao']}') ";
        $query = $this->conn->prepare($sql);
        $query->execute();

        if ($query->rowCount()) {
            return true;
        }
        else {
            return false;
        }
    }
    public function update($args) {
        $sql = "UPDATE {$this->tableName} SET nome='{$args['nome']}', descricao='{$args['descricao']}' WHERE id = {$args['id']}";
        $query = $this->conn->prepare($sql);
        $query->execute();
        if ($query->rowCount())  {
            return $this->getById($args['id']);
        }
        else {
            return false;
        }
    }
    public function deleteMany($items) {
        $items = array_filter($items);
        $ids = implode(",", $items);
        $sqlAutoRelacionados = "SELECT * FROM automovel_componente WHERE componente_id IN ({$ids})";
        $verifyQuery = $this->query($sqlAutoRelacionados);

        if ($verifyQuery) { // existe algum relacionado
            return new DefaultErrorResponse(['msg' => 'Existem automóveis relacionados a algum destes componentes!', 'error-code' => 2010]);
        } else {
            $sql = "DELETE FROM {$this->tableName} WHERE id IN({$ids})";
            $query = $this->conn->prepare($sql);
            $query->execute();
            $rCount = $query->rowCount();
            
            if ($query->rowCount()) {
                return $query;
            }
            else {
                return false;
            }
        }
        
    }
}
?>