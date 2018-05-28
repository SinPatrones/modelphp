<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/framework/databasesconnection/conexionmysql.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/framework/security/security.php';
// Esta es la ruta del server para la pagina D:\home\site\wwwroot

class EngineTable{
    private $table_name;
    private $columns_name = Array();
    private $bool_columns_name;
    private $primary_key;
    private $num_columns;

    private $columns_encryted = Array();
    private $cost = 10;

    public $security;

    public function __construct($new_table_name, int $new_cost = 0){
        $this->security = Security::getInstance();
        $this->table_name = $new_table_name;
        $this->bool_columns_name = false;
        if ($new_cost != 0)
            $this->cost = $new_cost;
    }

    // Llenar el nombre de las columnas en la tabla
    public function fillColumns($columns_names_array){
        $this->columns_name = $columns_names_array;
        $this->bool_columns_name = true;
        $this->primary_key = $this->columns_name[0];
        $this->num_columns = sizeof($this->columns_name);
    }

    // obtener el nombre de la tabla que se trabaja
    public function getTableName(): string {
        return $this->table_name;
    }

    // cambiar el nombre de la tabla
    public function setTableName($new_name_table){
        $this->table_name = $new_name_table;
    }

    // mostrar como cadena el nombre de las columnas
    public function showColumns(){
        if ($this->bool_columns_name){
            foreach ($this->columns_name as $columnName){
                echo $columnName."<br>";
            }
        }
        else{
            throw new Exception("No se llenaron las columnas de la tabla<br>");
        }
    }

    // Retorna un ARRAY de nombres de campos de la tabla
    public function getColumns(){
        if ($this->bool_columns_name)
            return $this->columns_name;
        throw new Exception("No hay columnas");
    }

    // obtener el PRIMARY_KEY
    public function getPrimaryKey():string {
        if ($this->bool_columns_name){
            return $this->primary_key;
        }
        throw new Exception("error: no hay primary_key");
    }

    // cambiar el primary key
    public function setPrimaryKey($new_primary_key){
        if (in_array($new_primary_key, $this->columns_name)){
            $this->primary_key = $new_primary_key;
        }
        else{
            throw new Exception("No existe el campo en los campos ingresados anteriormente.");
        }
    }

    public function setEncryptColumns($name_columns_encrypt):bool {
        foreach ($name_columns_encrypt as $column_name)
            if (!in_array($column_name, $this->columns_name))
                return false;
        $this->columns_encryted = $name_columns_encrypt;
        return true;
    }

    public function getEncryptColumns(){
        return $this->columns_encryted;
    }

    // insertar resgistro con Auto Primary Key
    public function insertAllRecordAuto($values_array, &$id, $debug_mode = false): bool {
        if ($this->bool_columns_name){
            $this->security->applySecurityToArray($values_array);     // aplicando seguridad
            $n_c = $this->num_columns; // a copy of $num_columns
            // COMPLETANDO CON LOS CAMPOS
            $sql = "INSERT INTO $this->table_name(";
            for ($i = 1; $i < $this->num_columns - 1; $i++) {
                $sql .= $this->columns_name[$i].", ";
            }
            $sql .= $this->columns_name[--$n_c].") VALUES (";
            // COMPLETANDO CON LOS VALUES
            $num_values = sizeof($values_array);
            for ($j = 0; $j < $num_values - 1; $j++){
                $sql .= '"'.$values_array[$j].'", ';
            }
            $sql .= '"'.$values_array[$num_values - 1].'")';

            // HACIENDO LA CONSULTA SQL
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result){
                $id = mysqli_insert_id($con->mysqli);
                $con->desconectar();
                return true;
            }
            $id = -1;
            if ($debug_mode)
                echo mysqli_error($con->mysqli);
            return false;
        }
        else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // insertar registro SIN auto primary key
    public function insertAllRecord($values_array, &$id, $debug_mode = false):bool {
        if ($this->bool_columns_name){
            $this->security->applySecurityToArray($values_array);
            // COMPLETANDO CON LOS CAMPOS
            $sql = "INSERT INTO $this->table_name(";
            for ($i = 0; $i < $this->num_columns - 1; $i++) {
                $sql .= $this->columns_name[$i].", ";
            }
            $n_c = $this->num_columns;
            $sql .= $this->columns_name[--$n_c].") VALUES (";
            // COMPLETANDO CON LOS VALUES
            $num_values = sizeof($values_array);
            for ($j = 0; $j < $num_values - 1; $j++){
                $sql .= '"'.$values_array[$j].'", ';
            }
            $sql .= '"'.$values_array[$num_values - 1].'")';

            // HACIENDO LA CONSULTA SQL
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result){
                $id = mysqli_insert_id($con->mysqli);
                $con->desconectar();
                return true;
            }
            if ($debug_mode)
                echo mysqli_error($con->mysqli);
            $con->desconectar();
            $id = -1;
            return false;
        }
        else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // Borrar un registro usando ID
    public function deleteRecord($id, $debug_mode = false):bool{
        if ($this->bool_columns_name) {
            $this->security->applySecurityToObj($id);
            $sql = "DELETE FROM $this->table_name WHERE $this->primary_key =" . '"' . $id . '"';
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result) {
                $con->desconectar();
                return true;
            }
            if ($debug_mode) {
                echo mysqli_error($con->mysqli);
            }
            $con->desconectar();
            return false;
        }else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    public function selectAll($debug_mode = false){
        if ($this->bool_columns_name){
            $con = Conexion::getInstance();
            $sql = "SELECT * from $this->table_name";
            $con->conectar();
            $result = $con->querry($sql);
            $con->desconectar();
            if (!$result){
                if (!$debug_mode)
                    echo mysqli_error($con->mysqli);
                return false;
            }
            return $result;
        }
        else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // Seleccionar por un campo especifico
    public function selectAllByX($column, $value_column, $debug_mode = false){
        if ($this->bool_columns_name) {
            $this->security->applySecurityToObj($column);
            $this->security->applySecurityToObj($value_column);
            if (in_array($column, $this->columns_name)) {
                $con = Conexion::getInstance();
                $con->conectar();
                $value_column = mysqli_real_escape_string($con->mysqli, $value_column);
                mysqli_set_charset($con->mysqli, 'utf8');
                $sql = "SELECT * FROM $this->table_name WHERE $column = " . '"' . $value_column . '"';
                $result = $con->querry($sql);
                $con->desconectar();
                if (!$result) {
                    if ($debug_mode)
                        echo mysqli_error($con->mysqli);
                    return false;
                }
                return $result;
            } else {
                throw new Exception("NO EXISTE LA COLUMNA EN LA TABLA");
            }
        }else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // Seleccionar solo algunos campos dependiendo de X
    public function selectSomesByX($colums_array, $column, $value_column, $debug_mode = false) {
        if ($this->bool_columns_name) {
            for ($i = 0; $i < sizeof($colums_array); $i++) {
                if (in_array($colums_array[$i], $this->columns_name) == false)
                    throw new Exception("No existe la columna " . $this->columns_name[$i]);
            }
            if (in_array($column, $this->columns_name) == false)
                throw new Exception("No existe la columna " . $column);

            $sql = "SELECT ";
            $num_columns_array = sizeof($colums_array);
            for ($i = 0; $i < $num_columns_array - 1; $i++) {
                $sql .= "$colums_array[$i], ";
            }
            $sql .= $colums_array[$num_columns_array - 1] . " FROM $this->table_name WHERE $column = " . '"' . $value_column . '"';
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result) {
                return $result;
            }
            if ($debug_mode)
                echo mysqli_error($con->mysqli);
            return false;
        }else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // Actualizar registro completo
    public function updateAllRowAuto($values_array, $id, $debug_mode = false):bool {
        if ($this->bool_columns_name) {
            if (sizeof($values_array) != (sizeof($this->columns_name) - 1))
                throw new Exception("NO COINCIDEN LOS CAMPOS INSERTADOS CON LA TABLA");

            $sql = "UPDATE $this->table_name SET ";
            $num_values_array = sizeof($values_array);
            for ($i = 0; $i < $num_values_array - 1; $i++) {
                $sql .= $this->columns_name[$i + 1] . ' = "' . $values_array[$i] . '", ';
            }
            $sql .= $this->columns_name[$num_values_array] . ' = "' . $values_array[$num_values_array - 1] . '" WHERE ' . $this->primary_key . ' = "' . $id . '"';
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result) {
                return true;
            }
            if ($debug_mode)
                echo mysqli_error($con->mysqli);
            return false;
        }else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // Actualizar tabla SIN auto primary key
    public function updateAllRow($values_array, $id, $debug_mode = false):bool {
        if ($this->bool_columns_name) {
            if (sizeof($values_array) != (sizeof($this->columns_name)))
                throw new Exception("NO COINCIDEN LOS CAMPOS INSERTADOS CON LA TABLA");

            $sql = "UPDATE $this->table_name SET ";
            $num_values_array = sizeof($values_array);
            for ($i = 0; $i < $num_values_array - 1; $i++) {
                $sql .= $this->columns_name[$i] . ' = "' . $values_array[$i] . '", ';
            }
            $sql .= $this->columns_name[$num_values_array - 1] . ' = "' . $values_array[$num_values_array - 1] . '" WHERE ' . $this->primary_key . ' = "' . $id . '"';
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result) {
                return true;
            }
            if ($debug_mode)
                echo mysqli_error($con->mysqli);
            return false;
        }else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // actualizar parcialmente un registro
    public function updatePartlyRecord($columns_array, $values_array, $id, $debug_mode = false): bool{
        if ($this->bool_columns_name) {
            $sql = "UPDATE $this->table_name SET ";
            $num = sizeof($columns_array);
            for ($i = 0; $i < $num - 1; $i++) {
                $sql .= "$columns_array[$i] = '$values_array[$i]', ";
            }
            $sql .= $columns_array[$num - 1] . " = '" . $values_array[$num - 1] . "' WHERE $this->primary_key = '$id'";
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result) {
                return true;
            }
            if ($debug_mode)
                echo mysqli_error($con->mysqli);
            return false;
        }else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // actualizar parcialmente pero desde array incompleto
    // incluso el PRIMARY_KEY
    public function updateFromSemiEmptyArray($values_array, $id, $anotherPrimaryKey = "", $debug_mode = false):bool {
        if($this->bool_columns_name) {
            // seleecionar los campos que no estan vacios
            $index_and_values = Array();
            for ($i = 0; $i < $this->num_columns; $i++) {
                if ($values_array[$i] != "") {
                    $index_and_values[$this->columns_name[$i]] = $values_array[$i];
                }
            }
            $sql = "UPDATE $this->table_name SET ";
            foreach ($index_and_values as $column => $value) {
                $sql .= "$column = '$value',";
            }
            $sql = substr($sql, 0, -1);
            if ($anotherPrimaryKey == "")
                $sql .= " WHERE $this->primary_key = '$id'";
            else
                $sql .= " WHERE $anotherPrimaryKey = '$id'";
            $con = Conexion::getInstance();
            $con->conectar();
            $result = $con->querry($sql);
            if ($result) {
                return true;
            }
            if ($debug_mode)
                echo mysqli_error($con->mysqli);
            return false;
        }else{
            throw new Exception("NO SE INSERTARON LOS CAMPOS");
        }
    }

    // This is only for using when you can't see the DB by your own
    // so you can see the tables in the database and be sure what are their names.
    public function getTablesDb(){
        // para obtener las tablas de la base de datos
        $con = Conexion::getInstance();
        $con->conectar();
        $sql = "SHOW FULL TABLES FROM sistdelivery";
        $result = $con->querry($sql);
        while ($record = mysqli_fetch_array($result)){
            echo $record['Tables_in_sistdelivery'].",    ".$record['Table_type']."<br>";
        }
    }
}

?>