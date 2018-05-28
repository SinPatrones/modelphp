<?php
include_once 'variables.php';

$file = fopen($fileName.".php", 'w');

fputs($file, $opener);
fputs($file, $include);
fputs($file, $name);

fclose($file);