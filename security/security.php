<?php

class Security{
    private static $instance = null;
    private $algoritmn = PASSWORD_BCRYPT;
    private $salt = ["cost" => 10];

    private function __construct(){
    }
    public function __clone()
    {
        trigger_error("Can't clone", E_USER_ERROR);
    }

    public static function getInstance(){
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setTypeEncrypt(int $new_algoritmn){
        $this->algoritmn = $new_algoritmn;
        if ($new_algoritmn == PASSWORD_ARGON2I){
            $this->salt = [
                'memory_cost' => 1<<11,
                'time_cost'   => 4,
                'threads'     => 2
            ];
        }
        elseif ($new_algoritmn == PASSWORD_DEFAULT){
            $this->salt = ["cost" => 10];
        }
    }

    public function modifySalt($new_salt){
        $this->salt = $new_salt;
    }

    private function securitymax(&$valores){
        $valores = str_replace("&", "", $valores);
        $valores = str_replace("=", "", $valores);
        $valores = str_replace(";", "", $valores);
        $valores = str_replace("DELETE", "", $valores);
        $valores = str_replace("SELECT", "", $valores);
        $valores = str_replace("INSERT", "", $valores);
        $valores = str_replace("'", "", $valores);
        $valores = str_replace("\"", "", $valores);
        $valores = addslashes(stripslashes($valores));
        $valores = htmlspecialchars($valores, ENT_QUOTES, 'UTF-8');
    }

    public function applySecurityToObj(&$value){
        $this->securitymax($value);
    }

    public function applySecurityToArray(&$arrayValues){
        for ($i = 0; $i < sizeof($arrayValues); $i++){
            $this->securitymax($arrayValues[$i]);
        }
    }

    public function encryptObj(&$data){
        $con = new Conexion();
        $con->conectar();
        $data = password_hash($data, $this->algoritmn, $this->salt);
        // ----
        //$data = mysqli_real_escape_string($con->mysqli, $data);
        $con->desconectar();
    }

    public function verifyEncryptObj($textPlain, $datagrama):bool{
        return password_verify($textPlain, $datagrama);
    }

    public function testencriptar($data){
        $d = $data;
        $this->encryptObj($data);
        echo $data."<br><br>";
        if (password_verify($d, $data))
            echo "Correcto<br>";
        else
            echo "Malo<br>";
    }
}

?>