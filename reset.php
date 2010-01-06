<?php

/**
 * reset.php
 *
 *       survey
 *
 *  UTF-8 encoded
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
 * @copyright Copyright (c) 2010, cuzi
 * @author cuzi@openmail.cc
 * @package survey
 * @version 2.0
 * @license http://gnu.org/copyleft/gpl.html GNU GPL
 *
 */
require 'config.php';


// Fehlerverarbeitung
$errors = new errors($errorsfile, 'error'); // error == standard css class


$mysql = new mysql($errors, $MySQLHost, $MySQLName, $MySQLPassword, $MySQLDBName, false, false);
unset($MySQLName);
unset($MySQLPassword);


// Headers schreiben
header('Content-Type: text/plain; charset=utf-8');

// MySQL Connection
$mysql->execute('TRUNCATE TABLE `studentstats`');
$mysql->execute('TRUNCATE TABLE `teachersstats`');
$mysql->execute('UPDATE `user` SET `password` = "admin2" WHERE `user`.`id` = 1 LIMIT 1');
$mysql->execute('UPDATE `user` SET `password` = "test" WHERE `user`.`id` =61 LIMIT 1');

if ($errors->number()) {
    var_dump($errors);
} else {
    header('Location: index.php');
}
?>