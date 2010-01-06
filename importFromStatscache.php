<?php

/**
 * importFromStatscache.php
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


// Fehlerverarbeitung
$errors = new errors($errorsfile, 'error'); // error == standard css class
$escapeBuffer = true;

// MySQL Connection
$mysql = new mysql($errors, $MySQLHost, $MySQLName, $MySQLPassword, $MySQLDBName, false, false);
unset($MySQLName);
unset($MySQLPassword);

// Headers schreiben
header('Content-Type: text/html; charset=utf-8');


$namesByTitle = array("Lehrer" => "maleTeacherSurvey",
    "Lehrerin" => "femaleTeacherSurvey",
    "Schüler" => "maleStudentsSurvey",
    "Schülerin" => "femaleStudentsSurvey",
    "Schüler/Schülerin" => "combination_students",
    "Lehrer/Lehrerin" => "combination_teachers",
    "Schüler/Schüler" => false,
    "Schülerin/Schülerin" => false);




// Stats cache
$cache_file = 'statscache/%u.cache';
$cache_array = array();
$handle = opendir('statscache');
$error_number = 0;

$result = array();

$timestamps = array();
while (false !== ($file = readdir($handle))) {
    if ($file != "." && $file != "..") {
        $parts = explode('.', $file);
        $timestamp = (integer) $parts[0];
        $timestamps[] = $timestamp;
        $filename = sprintf($cache_file, $timestamp);

        $data = file_get_contents($filename);
        $stats = unserialize($data);
        unset($data);

        foreach ($stats as $title => &$value) {
            $name = $namesByTitle[$title];
            if (!$name) {
                continue;
            }
            foreach ($value as $record) {
                $sql = sprintf('INSERT INTO `stats_history` (
             `timestamp` ,
             `surveyname` ,
             `title` ,
             `total` ,
             `for_id` ,
             `for_text` ,
             `sets`
             )
             VALUES (
             FROM_UNIXTIME(%u),
             "%s",
             "%s",
             %u,
             %u,
             "%s",
             "%s")',
                                $timestamp,
                                $mysql->escape($name),
                                $mysql->escape($title),
                                $record['total'],
                                $record['for_id'],
                                $mysql->escape($record['for_text']),
                                $mysql->escape(json_encode($record['sets'])));


                $result = $mysql->execute($sql);
                $error_number += $result ? 0 : 1;
            }
        }
    }
}

echo 'Fehler: ' . $error_number;

echo '<br />';
var_dump($errors);
?>