<?php
include_once $_SERVER['DOCUMENT_ROOT']."/framework/readsource.php";
// importar readsource.php

class Conexion{
    private $sourcedata = "";
    private $path = "";
    private $db = "";
    private $user = "";
    private $pass = "";

    public $mysqli;

    private static $instance = null;

    function __construct(){
        $this->sourcedata = new ReadSource();
        $data = $this->sourcedata->get_data();

        $this->path = $data['PATH'];
        $this->db = $data['DB'];
        $this->user = $data['USER'];
        $this->pass = $data['PWS'];
    }

    public static function getInstance(){
        if (!isset(self::$instance))
            self::$instance = new self();
        return self::$instance;
    }

    public function getPath(){
        return $this->path;
    }

    public function getDb(){
        return $this->db;
    }

    public function getUsr(){
        return $this->user;
    }

    public function getPws(){
        return $this->pass;
    }

    public function getfulldata(){
        return $this->path.", ".$this->user.", ".$this->pass.", ".$this->db."<br>";
    }

    public function conectar($debug_mode = false){
        if (!$debug_mode){
            $this->mysqli = mysqli_connect($this->path, $this->user, $this->pass, $this->db);
            if (!$this->mysqli) {
                return false;
            }
            return true;
        }
        $this->mysqli = mysqli_connect($this->path, $this->user, $this->pass, $this->db);
        if (!$this->mysqli) {
            echo "Falló la conexión a MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
            return false;
        }
        echo 'Conexion exitosa<br>';
        return true;
    }

    public function desconectar($debug_mode = false){
        if (!$debug_mode) {
            return mysqli_close($this->mysqli);
        }
        if (mysqli_close($this->mysqli)){
            echo "Falló la des-conexión a MySQL: (".$this->mysqli->connect_errno.") ".$this->mysqli->connect_error;
            return true;
        }
        echo "Falló la des-conexión a MySQL: (".$this->mysqli->connect_errno.") ".$this->mysqli->connect_error;
        return false;
    }

    public function querry($consulta, $debug_mode = false){
        if (!$debug_mode){
            return $this->mysqli->query($consulta);
        }
        $result = $this->mysqli->query($consulta);
        if (!$result){
            echo "Falló la consulta ".mysqli_error().".";
            return false;
        }
        echo "Consulta realizada con exito";
        return true;
    }
}

?>