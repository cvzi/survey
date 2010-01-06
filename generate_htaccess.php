<?php

/**
 * generate_htaccess.php
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

// Fehlerverarbeitung
$errors = new errors($errorsfile,'error'); // error == standard css class


// Navi instanzieren
$navi = new navigation($errors, $navifile);
$pages = $navi->getPages();
$key = $navi->getHtaccess();


// Headers schreiben
header('Content-Type: text/plain; charset=utf-8');

$handler = fopen('.htaccess', 'w+');

if(!$handler)
    exit('Am error occured while trying to open/create the file .htaccess');


fwrite($handler,"RewriteEngine On\n#generated with generate_htaccess.php using $navifile\n\n");



foreach($pages as $page) {
    if($page['param'] == $key) {
      fwrite($handler, 'RewriteRule ^'.$page['name'].'$ ?'.$page['param'].'='.$page['name']." [qsappend]\n".'RewriteRule ^'.$page['name'].'/$ ?'.$page['param'].'='.$page['name']." [qsappend]\n\n");
    }
}

fwrite($handler,"\n#Vulnerable Files and Dirs\n\n");

$public = $navi->getVulnerableFiles();
foreach($public as $file) {
    if('dir' == $file[0]) {
fwrite($handler,"RewriteRule ^{$file[1]}.* index.php\n");
    } else {
        fwrite($handler,"RewriteRule ^{$file[1]}$ index.php\n");
    }
}


fwrite($handler,'#generated with generate_htaccess.php');

fclose($handler);

echo "Successfully created: .htaccess\nWith following content:\n###########################\n\n";

readfile('.htaccess');

?>