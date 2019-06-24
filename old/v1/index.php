<?php require_once 'views/partials/header.php'; ?>
<?php require_once 'views/partials/navbar.php'; ?>

<?php 
// Rotas

$request = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '';

    switch ($request) {
        case '/':
            include __DIR__ . '/views/automovel.php';
            break;
        case '':
            include __DIR__ . '/views/automovel.php';
            break;
        case '/automovel':
            include __DIR__ . '/views/automovel.php';
            break;
        default:
            include __DIR__ . '/views/automovel.php';
            break;
    }

?>



<?php require_once 'views/partials/footer.php'; ?>