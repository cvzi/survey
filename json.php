<?php

/**
 * json.php
 *
 *       survey
 *
 *  survey is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  survey is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with survey; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *  For questions contact
 *  cuzi@openmail.cc
 *
 * @copyright 2010 cuzi
 * @author cuzi@openmail.cc
 * @package survey
 * @version 2.0
 * @license http://gnu.org/copyleft/gpl.html GNU GPL
 */
require 'config.php';

// Session
session_start();

// Weitere Ausgaben bzw. PHP Fehler abfangen
ob_start();


// Fehlerverarbeitung
$errors = new errors($errorsfile, 'error'); // error == standard css class
// User
$user = new user();
$user->standard('group', 0);
$user->standard('mobile', null);
$user->standard('uid', 0);

// MySQL Connection
$mysql = new mysql($errors, $MySQLHost, $MySQLName, $MySQLPassword, $MySQLDBName, false, false);
unset($MySQLName);
unset($MySQLPassword);


// Headers schreiben
header('Content-Type: text/plain; charset=utf-8');


$json = '';


// Navi instanzieren
$navi = new navigation($errors, $navifile);
// Angeforderte Seite raussuchen
$page = $navi->getRequestedJSONPage($_GET, $user->get('group'));

$found = @include $page['path'];
if ($found === false) {
    $errors[] = '#PageNotFound#';
}


//Errors:
if ($errors->number()) {
    var_dump($errors);
}

// Buffer
$buffer = ob_get_contents();
ob_end_clean();

if ($buffer) {
    echo '{"buffer":"'.$buffer.'","data":';
    echo $json.'}';
} else {
    echo $json;
}
?>