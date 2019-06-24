<?php
    require_once 'util/config.php';
    require_once _ROOT_FOLDER.'/src/controllers/automovel.controller.php';
    $rteq = $_REQUEST;
    $rpost = $_POST;
    $rqget = $_GET;
    $controller = new AutomovelController();
    $controller->request();
?>