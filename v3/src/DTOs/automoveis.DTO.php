<?php
require_once 'DTO.php';
require_once __DIR__.'/../models/automovel.model.php';

class AutomoveisDTO extends DTO {
    
    /**
     * Model constructor
     *
     * @param [string] $tableName nome da tabela do modelo
     */
    public function __construct() {
        $modelName = 'Automovel';
        $tableName = "automovel";
        parent::__construct($tableName, $modelName);
    }
    public function create($args) {
        $auto = $this->modelName::validateAndCreate($args);

        if ($auto instanceof DefaultErrorResponse) {
            return $auto; //retorna o erro caso não for válido
        }
        $sql =  "INSERT INTO {$this->tableName} (descricao, placa, renavam, ano_modelo, ano_fabricacao, cor, km, marca_id, preco, preco_fipe) VALUES(     '{$auto->descricao}', '{$auto->placa}', '{$auto->renavam}', {$auto->ano_modelo}, {$auto->ano_fabricacao}, '{$auto->cor}', {$auto->km}, {$auto->marca_id}, {$auto->preco}, {$auto->preco_fipe}); ";

        $query = $this->conn->prepare($sql);
        try {
            $query->execute();
        }
        catch(PDOException $e) {
            $errorInfo = $query->errorInfo();
            if ($errorInfo[1] == 1062) {
                $err = ['msg' => 'Não é permitido registros duplicados!',
                    'code' => 1012];
                $error = new DefaultErrorResponse($err);
                return $error;
            }
        }
        if ($query->rowCount()) {
            return $auto;
        }
        else {
            return false;
        }
    }
    public function getAll(): ?array {
        $sql =  "SELECT a.*, m.nome AS nome_marca FROM {$this->tableName} AS a LEFT JOIN marca AS m ON m.id = a.marca_id; ";
        $data = $this->query($sql);
        if ($data) {
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
        $auto = $this->modelName::validateAndCreate($args);

        if ($auto instanceof DefaultErrorResponse) {
            return $auto;
        }
        $sql = "UPDATE {$this->tableName} SET descricao='{$auto->descricao}', placa='{$auto->placa}', renavam='{$auto->renavam}', ano_modelo={$auto->ano_modelo}, ano_fabricacao={$auto->ano_fabricacao}, cor='{$auto->cor}', km={$auto->km}, marca_id={$auto->marca_id}, preco={$auto->preco}, preco_fipe={$auto->preco_fipe} WHERE id={$auto->id}; ";
        $query = $this->conn->prepare($sql);
        $result = $query->execute();
        if ($result) {
            // return $this->getById($args['id']);
            return true;
        }
        else {
            return false;
        }
    }
    public function getComponentes($automovel_id) {
        $sql = "SELECT c.id FROM componente AS c LEFT JOIN automovel_componente AS ac ON ac.componente_id = c.id WHERE ac.automovel_id = {$automovel_id}; "; 
        $data = $this->query($sql);

        if ($data) {
            return $data;
        }
        else {
            return array();
        }
    }
    public function compareAndUpdateComponentes($auto_id, $componentes) {
        if ($componentes === null) {
            // caso não haja compoentes marcados assegura de que 
            // serão deletadas todas as referências
            $sql = "DELETE FROM automovel_componente WHERE automovel_id = {$auto_id}";
            $query  = $this->conn->prepare($sql);
            $query->execute();
            return;
        }
        //componentes que já estão neste
        $sql = "SELECT c.id FROM componente AS c LEFT JOIN automovel_componente AS ac ON ac.componente_id = c.id WHERE ac.automovel_id = {$auto_id}; "; 
        $query  = $this->conn->prepare($sql);
        $query->execute();
        $existingComponentes = $query->fetchAll(PDO::FETCH_COLUMN, 0);
        //após comparação teremos as array do que adicionar e do que remover
        $to_add     = array();
        $to_remove  = array();

        //comparação do que adicionar (o que não está na arrau das esistentes mas está na $componentes)
        foreach ($componentes as $key => $id) {
            if (!in_array($id, $existingComponentes)) {
                array_push($to_add, $id);
            }
        }
        //comparação do que remover (está na $existingComponentes mas não na $componentes)
        foreach ($existingComponentes as $key => $id) {
            if (!in_array($id, $componentes)) {
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
    public function getByPlaca($placa) {
        $sql = "SELECT * FROM {$this->tableName} WHERE placa = '{$placa}'";
        $data = $this->query($sql);
        if ($data) {
            $result = $this->modelName::create($data[0]);
            return $result;
        }
        else {
            return null;
        }
    }
    public function getPage($pageRequested, $itemsPerPage, $orderby = null, $filter = null) {
        $totalItems = $this->getTotalCountFiltered($filter);
        $totalPages = ($totalItems == 0 ? 0 : (int) ceil($totalItems/$itemsPerPage));
        $pageToShow = $pageRequested <= $totalPages ? $pageRequested : 0;
        //TODO ASC/DESC CHOOSER
        $limit = $itemsPerPage;
        $offset = $pageRequested * $itemsPerPage;
        $orderby = $orderby == null ? 'id' : $orderby;
        $sql = "SELECT automovel.*, marca.nome AS nome_marca FROM automovel  LEFT JOIN marca  ON marca.id = automovel.marca_id ORDER BY automovel.{$orderby} DESC LIMIT :limit OFFSET :offset";
        if ($filter != null ) {
            $sql =  "SELECT a.*, m.nome AS nome_marca FROM automovel AS a LEFT JOIN marca AS m ON m.id = a.marca_id WHERE a.descricao LIKE '%{$filter}%' OR m.nome LIKE '%{$filter}%' ORDER BY {$orderby} ASC LIMIT :limit OFFSET :offset";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit);
        $stmt->bindParam(':offset', $offset);
        $stmt->execute();

        $data = $stmt->fetchAll();

        $result = array(
            'totalitems' => $totalItems,
            'totalpages' => $totalPages,
            'currentpage' => $pageToShow,
            'data' => $data
        );
        return $result;
    }
    private function getTotalCountFiltered($filter = null) {
        if ($filter == null ) {
            return $this->getTotalAmount();
        }
        $sql = "SELECT COUNT(*) AS count FROM automovel AS a INNER JOIN marca AS m ON m.id = a.marca_id WHERE a.descricao LIKE '%{$filter}%' OR m.nome LIKE '%{$filter}%'; ";
        $data = $this->query($sql);
        $result = $data[0]['count'];
        return $result;
    }
    public function deleteOne($id): bool {
        $sql = "DELETE FROM automovel_componente WHERE automovel_id = '{$id}'";
        $this->query($sql);
        $deleteResponse = parent::deleteOne($id);
        return $deleteResponse();
    }
    public function deleteMany($items) {
        $items = array_filter($items);
        $ids = implode(",", $items);
        $sql = "DELETE FROM {$this->tableName} WHERE id IN({$ids})";
        $sqlComponentes = "DELETE FROM automovel_componente WHERE automovel_id IN({$ids})";
        $queryComponentes = $this->conn->prepare($sqlComponentes);
        $query = $this->conn->prepare($sql);
        $query->execute();
        $rCount = $query->rowCount();
        $queryComponentes->execute();
        if ($query->rowCount()) {
            return $query;
        }
        else {
            return false;
        }
    }
}
?>