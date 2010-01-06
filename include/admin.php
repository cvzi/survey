<?php

/**
 * include/admin.php
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
// Template auswählen
$smarty->assign('moduleTpl', 'admin.html');

$smarty->assign('surveyfile', $surveyfile);

// Stats cache
function cmp($a, $b) {
    if ($a['time'] == $b['time']) {
        return 0;
    }
    return ($a['time'] < $b['time']) ? 1 : -1;
}

$cache_file = 'statscache/%u.cache';
$cache_array = array();
$handle = opendir('statscache');
$total = 0;
while (false !== ($file = readdir($handle))) {
    if ($file != "." && $file != "..") {
        $parts = explode('.', $file);
        $timestamp = (integer) $parts[0];

        $path = sprintf($cache_file, $timestamp);
        $size = filesize($path);
        if($size == 0) {
            continue;
        }

        $cache_array[] = array('filename' => $file, 'path' => $path, 'time' => $timestamp, 'size' => $size, 'hash' => md5_file($path));
        $total += $size;
    }
}
usort($cache_array, 'cmp');
$smarty->assign('cache_array', $cache_array);
$smarty->assign('total_cache_size', $total);

// MYSQL Database backup
/**
 * Get the directory size
 * @param directory $directory
 * @return integer
 */
$path = $navi->getPath();
$dbbackup_dir = sprintf('%sdbbackup', $path);
$dbbackup_array = array();
$handle = opendir($dbbackup_dir);
$total = 0;
while (false !== ($file = readdir($handle))) {
    if ($file != "." && $file != "..") {
        $timestamp = explode('_', $file);
        $timestamp = $timestamp[0];
        $size = filesize($dbbackup_dir . '/' . $file);
        if (isset($dbbackup_array[$timestamp])) {
            $dbbackup_array[$timestamp]['size'] += $size;
        } else {
            $dbbackup_array[$timestamp] = array('time' => $timestamp, 'size' => $size);
        }
        $total += $size;
    }
}
usort($dbbackup_array, 'cmp');
$smarty->assign('dbbackup_array', $dbbackup_array);
$smarty->assign('total_dbbackup_size', $total);


// Comments 
$allcomments = $pagecomments->getCommentsRange(-1);
$allcomments = $allcomments['data'];
$smarty->assign('allcomments', $allcomments);


// Survey online?
$survey_php_file_normal = 'include/survey.php';
$survey_json0_file_normal = 'include/ajax.vote.php';
$survey_json1_file_normal = 'include/ajax.votecombi.php';
$survey_single_file_normal = 'include/single.php';

$survey_php_file_offline = $survey_php_file_normal . '1';
$survey_json0_file_offline = $survey_json0_file_normal . '1';
$survey_json1_file_offline = $survey_json1_file_normal . '1';
$survey_single_file_offline = $survey_single_file_normal . '1';

$surveyonline = file_exists($survey_php_file_normal);
$smarty->assign('surveyonline', $surveyonline);

// Stats online?
$stats_php_file_normal = 'include/stats.php';
$stats_php_file_offline = $stats_php_file_normal . '1';
$statsonline = file_exists($stats_php_file_normal);
$smarty->assign('statsonline', $statsonline);


// Read Surveyfile
$surveyfileContent = file_get_contents($surveyfile);
$smarty->assign('surveyfileContent', $surveyfileContent);


$survey = new Survey($errors, $surveyfile, $mysql);

check($_GET['do']);

if ('createTables' == $_GET['do']) {
    $errornumber = $survey->db_createTables();
    if (0 == $errornumber) {
        $errors[] = array('text' => 'Die Tabellen wurden erstellt', 'class' => 'hint');
    } else {
        $errors[] = 'Die Tabellen konnten nicht erstellt werden. Es traten (' . $errornumber . ') Fehler auf';
    }
} elseif ('createTriggers' == $_GET['do']) {
    $errornumber = $survey->db_createTableTriggers();
    if (0 == $errornumber) {
        $errors[] = array('text' => 'Die Tabellentrigger wurden erstellt', 'class' => 'hint');
    } else {
        $errors[] = 'Die Tabellentrigger konnten nicht erstellt werden. Es traten (' . $errornumber . ') Fehler auf';
    }
} elseif ('showPasswords' == $_GET['do']) {
    $sql = 'SELECT `name`,`password` FROM `user` WHERE `group` < 5';
    var_dump($sql);
    $result = $mysql->select($sql, 'assocList');
    $smarty->assign('memberpasswords', $result);

    $shortindex = $navi->getShortIndex();
    $smarty->assign('shortindex', $shortindex);
} elseif ('deleteComment' == $_GET['do']) {
    $res = $pagecomments->deleteComment((integer) $_GET['id']);
    if ($res) {
        $errors[] = array('text' => 'Kommentar wurde gelöscht', 'class' => 'hint');
    } else {
        $errors[] = 'Es trat ein unbekannter MySQL Fehler auf! pageComments::deleteComment() returned with false ';
    }
    $smarty->assign('redirect', $pages['admin']['link']);
} elseif ('toggleSurvey' == $_GET['do']) {
    if ($surveyonline) {
        rename($survey_php_file_normal, $survey_php_file_offline);
        rename($survey_json0_file_normal, $survey_json0_file_offline);
        rename($survey_json1_file_normal, $survey_json1_file_offline);
        rename($survey_single_file_normal, $survey_single_file_offline);
    } else {
        rename($survey_php_file_offline, $survey_php_file_normal);
        rename($survey_json0_file_offline, $survey_json0_file_normal);
        rename($survey_json1_file_offline, $survey_json1_file_normal);
        rename($survey_single_file_offline, $survey_single_file_normal);
    }
    $smarty->assign('redirect', $pages['admin']['link']);
    $smarty->assign('statsonline', $surveyonline ? false : true);
    $errors[] = array('text' => $surveyonline ? 'Offline' : 'Online', 'class' => 'hint');
} elseif ('toggleStats' == $_GET['do']) {
    if ($statsonline) {
        rename($stats_php_file_normal, $stats_php_file_offline);
    } else {
        rename($stats_php_file_offline, $stats_php_file_normal);
    }
    $smarty->assign('redirect', $pages['admin']['link']);
    $smarty->assign('statsonline', $statsonline ? false : true);
    $errors[] = array('text' => $statsonline ? 'Offline' : 'Online', 'class' => 'hint');
} elseif ('optimize' == $_GET['do']) {

    $result = $pagecomments->optimizeTable();

    if ($result) {
        $errors[] = array('text' => 'Kommentar-Tabelle optimiert!', 'class' => 'hint');
    } else {
        $errors[] = 'Es trat ein unbekannter MySQL Fehler auf! pageComments::optimizeTable() returned with false ';
    }

    $result = $survey->optimizeTable();

    if ($result) {
        $errors[] = array('text' => 'Survey-Tabellen optimiert!', 'class' => 'hint');
    } else {
        $errors[] = 'Es trat ein unbekannter MySQL Fehler auf! Survey::optimizeTable() returned with false ';
    }

    $smarty->assign('redirect', $pages['admin']['link']);
} elseif ('unblock' == $_GET['do']) {
    unlink(sprintf($cache_file, 0));
} elseif ('backupdatabase' == $_GET['do']) {
    $path = $navi->getPath();
    $prefix = sprintf('%sdbbackup/%u_', $path, time());

    $result = $survey->backup($prefix);
    if ($result) {
        $smarty->assign('redirect', $pages['admin']['link']);
        $errors[] = array('text' => 'Backup erstellt', 'class' => 'hint');
    } else {
        $errors[] = 'Es trat ein unbekannter Fehler auf! Survey::backup() returned with false ';
    }
} elseif ('cleanupcache' == $_GET['do']) {
    $result = array();
    $total = 0;
    $i = 0;
    foreach ($cache_array as &$value) {
        if (isset($result[$value['hash']])) {
            $total += $value['size'];
            unlink($value['path']);
            ++$i;
        } else {
            $result[$value['hash']] = $value;
        }
    }

    $cache_array = $result;
    unset($result);

    $total /= 1024;
    
    $smarty->assign('redirect', $pages['admin']['link']);
    $errors[] = array('text' => 'Cache aufgeräumt. Insgesamt '.$i.' Dateien mit '.$total.' KB entfernt!', 'class' => 'hint');
}
?>