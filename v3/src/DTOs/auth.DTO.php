<?php
    require_once 'DTO.php';
    class AuthDTO extends DTO
    {
       
        /**
         * Model constructor
         *
         * @param [string] $tableName nome da tabela do modelo
         */
        public function __construct()
        {
            $modelName = null;
            $tableName = "usuario";
    
            parent::__construct($tableName, $modelName);
        }

        public function create($args)
        {

        }

        public function update($args)
        {

        }

        public function login($args)
        {
            $login = $args['login'] ?? null;
            $senha = $args['senha'] ?? null;

            if($login == null or $senha == null)
            {
                return false;
            }
            else if($login & $senha)
            {
                $sql = "SELECT * FROM usuario WHERE `login`='{$login}' AND `senha`='{$senha}'";
                $result = $this->conn->prepare($sql);
                $result->execute();
                
                if($result->rowCount())
                {
                    return true;
                }
            }

           return false; 
        }

        public function registrar($args)
        {
            $login = $args['login'];
            $senha = $args['senha'];

            //primeiro checar se existe usuário
            if($this->usuarioExiste($login))
            {
                $msg = "Usuário já existe!";
                return new DefaultErrorResponse(['msg' => $msg]);
            }
            else
            {
                $sql = "INSERT INTO `usuario` (login, senha) VALUES('{$login}', '{$senha}')";
                $result = $this->conn->prepare($sql);
                $result->execute();

                if($result)
                {
                    $msg = "Usuário criado com sucesso!";
                    return new DefaultSuccesResponse(['msg' => $msg]);
                }
                else 
                {
                    $msg = "Problema ao cadastrar usuário";
                    return new DefaultErrorResponse(['msg' => $msg]);
                }
            }
        }

        private function usuarioExiste($username)
        {
            $sql = "SELECT * FROM `usuario` WHERE `login` = '{$username}'";
            $result = $this->conn->prepare($sql);
            $result->execute();

            if($result->rowCount())
            {
                return true;
            }
            return false;
        }

    }
?>