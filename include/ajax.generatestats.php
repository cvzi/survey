<?php

/**
 * include/ajax.generatestats.php
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
$jsonresult = 0;

$cache_file = 'statscache/%u.cache';


if (file_exists(sprintf($cache_file, 0))) { // Block file exists => currently generating
    $jsonresult = 2;
} else {

// Get last file
    $timestamps_array = array();
    $handle = opendir('statscache');
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $parts = explode('.', $file);
            $timestamps_array[] = (integer) $parts[0];
        }
    }


    if (!$timestamps_array) {
        $lastGenerate = 0;
    } else {
        rsort($timestamps_array);
        $lastGenerate = $timestamps_array[0];
    }

    $diff = time() - $lastGenerate;
    $howold = 0;

    if ($diff < 5 * 60) {
        $jsonresult = 1;
    } else {
        $jsonresult = 0;
    }
}

$json = sprintf('{"result":%s}', $jsonresult);
?>