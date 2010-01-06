<?php

/**
 * include/stats3.php
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

$cache_file = 'statscache/%u.cache';

$smarty->assign('moduleTpl', 'stats.html');


// Get last file
$timestamps_array = array();
$handle = opendir('statscache');
while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $parts = explode('.',$file);
            $timestamps_array[] = (integer) $parts[0];
        }
}


if(!$timestamps_array) {
    $lastGenerate = 0;

} else {
   rsort($timestamps_array);
   $lastGenerate = $timestamps_array[0];
}



$diff = time() - $lastGenerate;
$howold = 0;

if ($diff < 30 * 60 && !$_GET['force']) {
    $t0 = microtime(true);
    $data = file_get_contents(sprintf($cache_file,$lastGenerate));
    
    $stats = unserialize($data);

    $t = microtime(true) - $t0;

    $howold = floor($diff / 6) / 10;
} else {
    $blockfp = fopen(sprintf($cache_file,0), 'w+');
    fclose($blockfp);

    $t0 = microtime(true);
    $survey = new Survey3($errors, $surveyfile, $mysql);
    $stats = $survey->getStatistics();

    $t = microtime(true) - $t0;

    $data = serialize($stats);

    $fp = fopen(sprintf($cache_file, time()), 'w+');
    fwrite($fp, $data);
    fclose($fp);

    unlink(sprintf($cache_file,0));
}


$smarty->assign('stats', $stats);
$smarty->assign('howold', $howold);

$smarty->assign('timeOfGeneration', $t*1000);

$smarty->assign('wide_content', true);
?>