<?php

class DatabaseConnector
{
    public static $hostname     = "127.0.0.1";
    public static $username     = "root";
    public static $password     = "";
    public static $dbname       = "automobilles";
    public static $charset      = "utf8";
    public static $dbOptions    = [
        \PDO::ATTR_ERRMODE              =>  \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE   =>  \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES     =>  false
    ];
    /**
     * Conexão com o banco
     *
     * @var [mysqli]
     */
    public $conn = null;
    public $dsn = null;

    public function __construct()
    {
        $this->dsn =  "mysql:host=".static::$hostname.
                ";dbname=".static::$dbname
                .";charset=".static::$charset;
        $this->connect();
    }

    public function connect() 
    {

        try {
            $this->conn = new PDO($this->dsn, static::$username, static::$password, static::$dbOptions);
        } catch (Exception $err) {
            echo $err;
        }
       
    }

    public function disconnect() 
    {
        if ($this->conn) {
            $this->conn = null;
        }
    }

    public function query($sql)
    {
        try {
            $query = $this->conn->query($sql);
            $result = $query->fetchAll();
            return $result;
        } catch(Exception $e) {
            echo $e;
        }
    }
}
?>