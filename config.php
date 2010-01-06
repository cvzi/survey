<?php

// MySQL Daten
$MySQLHost = 'localhost';
$MySQLName = 'root';
$MySQLPassword = '';
$MySQLDBName = 'survey';

// Survey
$surveyfile = 'templates/survey.xml';

// Smarty Dir
$smartydir = 'smarty/';

// Navi File
$navifile = 'navigation.xml';

// Class Dir
$classdir = 'class/';

// Error Strings File
$errorsfile = 'templates/errors.xml';



// autoload Funktion zum Nachladen der Klassendateien zur Laufzeit
function __autoload($class_name) {
    global $classdir;
    require_once "$classdir$class_name.class.php";
}

// vermeidet Warnungen bei nicht definierten Variablen
function check(&$var) {
    $var = isset($var) ? $var : null;
}

?>