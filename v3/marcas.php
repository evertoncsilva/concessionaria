<?php
    require_once 'util/config.php';
    require_once _ROOT_FOLDER.'/src/controllers/marca.controller.php';
    $rteq = $_REQUEST;
    $rpost = $_POST;
    $rqget = $_GET;
    $controller = new MarcaController();
    $controller->request();
?>