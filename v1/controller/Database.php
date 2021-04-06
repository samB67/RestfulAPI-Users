<?php

require_once ("../../config/config.php");

class Database{

    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PWD;
    private $dbname = DB_NAME;

    private $connection;
    private $error;
    private $stmt;
    private $dbconnected = false;

    public function __construct(){

        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, $options);
            $this->dbconnected = true;
        }catch (PDOException $e){
            $this->error = $e->getMessage() . PHP_EOL;
            $this->dbconnected = false;
        }
    }

    // get errors...
    public function getError(){
        return $this->error;
    }

    // Check connection...
    public function isConnected(){
        return $this->dbconnected;
    }

    // Prepare statments with query...
    public function query($query){
        $this->stmt = $this->connection->prepare($query);
    }

    // Execute the statment...
    public function execute(){
        return $this->stmt->execute();
    }

    //Get result set as array of object...
    public function resultset(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get row count...
    public function rowCount(){
        return $this->stmt->rowCount();
    }

    // execute and fetch single record...
    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // get last inserted id...
    public function lastInsertId(){
        return $this->connection->lastInsertId();
    }

    // bind values...
    public function bind($param, $value, $type = null){
        if (is_null($type)){
            switch (true){
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }



}