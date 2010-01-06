<?php

/**
 * index.php
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
$escapeBuffer = true;

// User
$user = new user();
$user->standard('group', 0);
$user->standard('mobile', null);

// MySQL Connection
$mysql = new mysql($errors, $MySQLHost, $MySQLName, $MySQLPassword, $MySQLDBName, false, false);
unset($MySQLName);
unset($MySQLPassword);


// Smarty instanzieren
require($smartydir . 'libs/Smarty.class.php');
$smarty = new Smarty;
$smarty->caching = false;
$smarty->template_dir = 'templates/';
$smarty->config_dir = $smartydir . 'config/';
$smarty->cache_dir = $smartydir . 'cache/';
$smarty->compile_dir = $smartydir . 'templates_c/';

// Navi instanzieren
try {
    $navi = new navigation($errors, $navifile);
    $pages = $navi->getPages();
    $smarty->template_dir = $navi->getTemplateDir();
} catch (Exception $e) {
    $buffer = ob_get_contents();
    ob_end_clean();
    echo '<h1>Failed to load navigation file</h1>';
    if ($buffer) {
        echo '<pre><h2>Info:</h2>
' . $e->getMessage() . '

<h2>Output:</h2>' . $buffer . '</pre>';
    }
    exit;
}



// Mobile Browser?
if (($user->get('mobile') || isset($_GET['mobile'])) && !isset($_GET['classic'])) {
    $isMobileDevice = $navi->tryMobileDevice('', true);
} else if ($user->get('mobile') === false || isset($_GET['classic'])) {
    $isMobileDevice = $navi->tryMobileDevice('', false);
} else {
    $isMobileDevice = $navi->tryMobileDevice($_SERVER['HTTP_USER_AGENT']);
}
if ($isMobileDevice !== $user->get('mobile')) {
    $user->set('mobile', $isMobileDevice);
}

// Javascript
$jscode = '';
$jsscripts = array();


// Comments
$pagecomments = new PageComments($errors, $mysql, 'pagecomments');

// Angeforderte Seite raussuchen
$page = $navi->getRequestedPage($_GET, $user->get('group'));

$found = false;
$found = include $page['path'];
if ($found === false) {
    $errors[] = '#PageNotFound#';
    $escapeBuffer = false;
}



// Fetch comments (might have been deactivated in above include)
$getcomments = $pagecomments->getComments();
$comments = $getcomments['data'];
$comments_more = $getcomments['more'];

// Headers schreiben
header('Content-Type: text/html; charset=utf-8');

// Vars
$smarty->assign('user', $user);
$smarty->assign('mobile', $isMobileDevice);
$smarty->assign('pages', $pages);
$smarty->assign('page', $page);
$smarty->assign('baseurl', $navi->getBase());
$smarty->assign('commentsactive', $pagecomments->active());
$smarty->assign('comments', $comments);
$smarty->assign('comments_more', $comments_more);
$smarty->assign('jscode', $jscode);
$smarty->assign('jsscripts', $jsscripts);

$smarty->assign('errors', $errors->get()); // last!
// Buffer
$buffer = ob_get_contents();
if ($buffer) {
    if ($escapeBuffer) {
        if (strpos($buffer, '<b>') === false) { // It is probably not a PHP Error
            $smarty->assign('outputbuffer', htmlspecialchars($buffer));
        } else {
            $smarty->assign('outputbuffer', $buffer);
        }
    } else {
        $smarty->assign('outputbuffer', $buffer);
    }
} else {
    $smarty->assign('outputbuffer', false);
}
ob_end_clean();


// Ausgabe
$smarty->display($navi->getFrame());
?>
