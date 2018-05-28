<?php
// CLASE PARA PODER LEER LOS DATOS PRELLENADOS EN ARCHIVO DE CONFIGURACION

class ReadSource{
    private $filename;
    private $data = array();

    function __construct($fn = ""){
        if ($fn != "")
            $this->filename = $fn;
        else
            $this->filename = $_SERVER['DOCUMENT_ROOT']."/framework/config/sourceconnect.src";
        $this->readsource();
    }

    private function readsource(){
        $file = fopen($this->filename, 'r');
        while (!feof($file)){
            $linea = fgets($file);
            if ($linea[0] != ";") {
                if ($linea != "") {
                    $linea = explode("=", $linea);
                    if (isset($linea[1]))
                        $this->data[trim($linea[0])] = trim($linea[1]);
                }
            }
        }
        fclose($file);
    }

    public function refresh(){
        $this->readsource();
    }

    public function get_data(){
        return $this->data;
    }
}

?>