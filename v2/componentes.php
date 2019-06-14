<?php

    include_once __DIR__.'/util/constants.php';
    include_once __DIR__.'/src/controllers/componente.controller.php';

    $controller = new ComponenteController();

    var_dump($controller);
    if (!empty($_GET)) 
    {
        $controller->get($_GET);
    } 
    elseif (!empty($_POST)) 
    {
        $controller->post($_POST);
    }
    else {
        $controller->renderIndex();
    }
?>
