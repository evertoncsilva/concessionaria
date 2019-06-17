<?php
    require_once 'util/config.php';
    require_once _ROOT_FOLDER.'/src/controllers/componente.controller.php';
    $controller = new ComponenteController();
    $controller->render();
?>