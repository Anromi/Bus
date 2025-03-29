<?php

require_once 'constants.php';

class DB {
    // Переменная, хранящая объект PDO
    private $db;
    private $host, $port, $user, $password, $handle, $result,$_last_id;

    // Соединение с БД
    public function __construct(){

        if (!$this->db) {
            try {
                $dsn = 'pgsql:host=' . DB_SERVER . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
                $this->db = new PDO($dsn, DB_USER, DB_PASS);
            } catch (PDOException $pe) {
                die("Could not connect to the database DB_NAME :" . $pe->getMessage());
            }
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
    }

    public function query($query, $params = []) {
        if(!empty($this->db)) {
            $this->result = $this->db->prepare($query);
            $this->result->execute($params);
        }
    }

    function log($data, $file="system"){
        if (is_array($data)){
            file_put_contents('../logs/'.$file.'.log', print_r($data,true)."\n", FILE_APPEND);
        }else {
            $str = date('d.m.Y H:i', time()) . ' ' . $data . "\n";
            file_put_contents('../logs/'.$file.'.log', $str, FILE_APPEND);
        }
    }

    public function rows($params = PDO::FETCH_ASSOC) {
        if(!empty($this->result)){
            return $this->result->fetch($params);
        }
    }

    public function lastInsertId() {
        return $this->db->lastInsertId();
    }
}
