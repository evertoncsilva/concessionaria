<?php
require_once __DIR__.'/../models/componente.model.php';
require_once __DIR__.'/../listModels/componentes.model.php';

class Componente {

    public function teste($id) 
    {
        $componenteData = new ComponenteModel();
        $componenteData->nome = "teste1";
        $componenteData->id = $id;
        $componenteData->descricao = "componente de teste";

        $componenteData2 = new ComponenteModel();
        $componenteData2->nome = "teste1";
        $componenteData2->id = $id;
        $componenteData2->descricao = "componente de teste";

        $componentes = array($componenteData, $componenteData2);

        header('Content-Type: application/json');
        echo json_encode($componentes);
        die;
    }

    public function getAllJSON() {
        
    }

}

?>