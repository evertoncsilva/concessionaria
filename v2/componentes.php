<?php

    include_once __DIR__.'/util/constants.php';
    include_once __DIR__.'/src/controllers/componente.controller.php';

    if (!empty($_GET)) 
    {
        ComponenteController::get($_GET);
    } 
    elseif (!empty($_POST)) 
    {
        ComponenteController::post($_POST);
    }
    else {
        ComponenteController::renderIndex();
    }

?>
