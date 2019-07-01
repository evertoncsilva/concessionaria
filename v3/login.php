<?php
    require_once 'util/config.php';
    require_once _ROOT_FOLDER.'/src/controllers/auth.controller.php';
    $controller = new AuthController();
    $controller->request();
?>