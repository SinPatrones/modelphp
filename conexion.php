<?php
$rpta = include_once $_SERVER['DOCUMENT_ROOT'].'/framework/readsource.php';
/*
if ($rpta)
    echo "<br>SI IMPORTO: ".$_SERVER['DOCUMENT_ROOT']."/system/readsource.php DESDE >>conexion.php<<<br>";
else
    echo "<br>NOO SE IMPORTO: ".$_SERVER['DOCUMENT_ROOT']."/system/readsource.php DESDE >>conexion.php<<<br>";
*/

$rs = new ReadSource();
$controldata = $rs->get_data();

if ($controldata['TYPE'] == "mysql"){
//    echo "--->".$_SERVER['DOCUMENT_ROOT']."/system/enginetables/enginetablemysql.php<br>";
    $rpta = include_once 'enginetables/enginetablemysql.php';
/*    if ($rpta)
        echo "SI SE IMPORTO ".$_SERVER['DOCUMENT_ROOT']."/system/enginetables/enginetablemysql.php DESDE >>conexion.php<<<br>";
    else
        echo "NOOO SE IMPORTO ".$_SERVER['DOCUMENT_ROOT']."/system/enginetables/enginetablemysql.php DESDE >>conexion.php<<<br>";
*/
}
elseif ($controldata['TYPE'] == "sqlserver"){
    echo "<h1 align='center'>NO ESTA IMPLEMENTADA CON SQLSERVER AUN<h1>";
}
elseif ($controldata['TYPE'] == "oracle"){
    echo "<h1 align='center'>NO ESTA IMPLEMENTADA CON ORACLE AUN<h1>";
}

?>