<?php
require_once __DIR__.'/../DTOs/auth.DTO.php';
require_once __DIR__.'/../models/default-success-response.model.php';
require_once __DIR__.'/../models/default-error-response.model.php';
require_once 'controller.php';
class AuthController extends Controller {
    
    public $routes = [
        'get' => [
            'logout' => 'logout'
        ],
        'post' => [
            'login' => 'login',
            'registrar' => 'registrar'
        ]
    ];
    public function __construct() {
        $dto = new AuthDTO();
        parent::__construct('componente', $dto);
    }
    public function renderIndex() {
        $loggedIn  = $_SESSION['login'] ?? false;

        if ($loggedIn && isset($_SESSION['user'])) {
            header('Location: automoveis.php', true, 302);
        }
        parent::renderLogin("É necessário efetuar login!");
    }

    public function login($args) {
        if (!isset($args['login']) || !isset($args['senha'])) {
            parent::renderLogin("Dados para login inválidos!");
        }
        $login = $args['login'];
        $senha = $args['senha'];
        $result = $this->DTO->login(['login' => $login, 'senha' => $senha]);

        if ($result) {
            $_SESSION['login'] = true;
            $_SESSION['user'] = $login;
            header('Location: automoveis.php');
            die;
        }
        else {
            parent::renderLogin("Não foi possível efetuar login, tente novamente!");
        }

    }

    public function registrar($args) {
        $login = $args['login'] ?? null;
        $senha = $args['senha'] ?? null;
        $confirma = $args['confirma-senha'] ?? null;

        if (!$login || !$senha || !$confirma) {
            parent::renderLogin("Prencha todos os campos para registrar-se");
        }
        else if ($senha != $confirma) {
            parent::renderLogin("Confirmação de senha inválida!");
        }
        else {
            $register = $this->DTO->registrar(['login' => $login, 'senha' => $senha]);

            if ($register instanceof DefaultErrorResponse) {
                return parent::renderLogin($register->message);
            }
            else if ($register instanceof DefaultSuccesResponse) {
                return parent::renderLogin($register->message, true);
            }
        }


    }

    public function logout() {
        session_destroy();
        $this->renderLogin("Você efetuou logout com sucesso!", true);
    }
}
?>