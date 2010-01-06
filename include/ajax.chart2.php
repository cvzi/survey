<?php

exit;

/**
 *  ajax.chart2.php
 * 
 *        survey
 * 
 *   UTF-8 encoded
 * 
 *   survey is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 * 
 *   survey is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 * 
 *   You should have received a copy of the GNU General Public License
 *   along with survey; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * 
 *   For questions contact
 *   cuzi@openmail.cc
 * 
 *  @copyright 2010 cuzi
 *  @author cuzi@openmail.cc
 *  @package survey
 *  @version 2.0
 *  @license http://gnu.org/copyleft/gpl.html GNU GPL

 */
/*
  http://chart.apis.google.com/chart
  ?chxt=y
  &chs=250x100
  &cht=lxy
  &chco=3072F3,FF0000,FF9900
  &chds=0,100,0,105
  &chd=t:10,20,40,80,90,95,99|20,30,40,50,60,70,80|-1|5,10,22,35,85|-1|40.576,69.448,75.245,76.006,64.996
  &chdl=Auerbach|Link%2C+A.|Geier
  &chdlp=b
  &chls=2,4,1|1|1
  &chma=5,5,5,25
  &chtt=Bestaussehendster
 */

$t_init = microtime(true);

$serializetime = 0;
$filetime = 0;
$totalfilesize = 0;
$totalfiles = 0;

$errornumber = 0;


$for_id = (integer) $_GET['id'];
$title = $_GET['title'];
$name = $_GET['name'];
$label = $_GET['label'];

// Stats cache

$cache_file = 'statscache/%u.cache';
$cache_array = array();
$handle = opendir('statscache');
$total = 0;

$result = array();


$t0 = microtime(true);
$data = $mysql->select(sprintf('SELECT *,UNIX_TIMESTAMP(`timestamp`) AS `time` FROM `stats_history` WHERE `surveyname` = "%s" AND `for_id` = %u', $mysql->escape($name), $for_id));
$filetime = microtime(true) - $t0;

$timestamps = array();
foreach ($data as $line) {
    $timestamp = (integer) $line['time'];
    $timestamps[] = $timestamp;

    $t0 = microtime(true);
    $stats = json_decode($line['sets']);
    $serializetime += microtime(true) - $t0;
    
    ++$totalfiles;
    $totalfilesize += strlen($line['sets'])*10;

    foreach ($stats as $key => &$v) {
        if (0 != $v) {
            if (isset($result[$key])) {
                $result[$key][$timestamp] = $v;
            } else {
                $result[$key] = array($timestamp => $v);
            }
        }
    }
}

unset($data, $stats);


// Delete sections without slope
$last = array();
foreach ($timestamps as $timestamp) {
    if (!$last) { // First round
        foreach ($result as $name => $value) {
            if (isset($value[$timestamp])) {
                $last[$name] = $value[$timestamp];
            }
        }
        continue;
    }
    $del = true;

    $nlast = array();
    foreach ($result as $name => $value) {
        if (isset($value[$timestamp])) {
            $nlast[$name] = $value[$timestamp];

            if ($last[$name] != $value[$timestamp]) {
                $del = false;
            }
        }
    }

    $last = $nlast;

    if ($del) {
        foreach ($result as &$value) {
            unset($value[$timestamp]);
        }
    }
}



$chd = array();
$chdl = array();
$total = 0;

foreach ($result as $key => &$teacher) {
    ksort($teacher);
    $chdl[] = urlencode($key);
    $chd[] = implode(',', $teacher);
    foreach ($teacher as $key => &$v) {
        if ($v > $total) {
            $total = $v;
        }
    }
}
unset($result);

$chd = 't:' . implode('|', $chd);
$chdl = implode('|', $chdl);

$link = 'http://chart.googleapis.com/chart?cht=lc'
        . '&chtt=' . urldecode($title . ' - ' . $label)
        . '&chd=' . $chd
        . '&chs=400x200'
        . '&chds=0,' . ($total + 2)
        . '&chdl=' . $chdl
        . '&chxt=y'
        . '&chxr=0,0,' . ($total + 2) . ',' . (integer) ($total / 5)
        . '&chco=3072F3,FF0000,FF9900,2ddc67,dcab2d,dc2d92,e9967a,8a2be2,adff2f,add8e6';



$totaltime += microtime(true) - $t_init;

$json = sprintf('{"errornumber":%d,"memory":%u,"filenumber":%u,"filesize":%u,"filetime":%u,"serializetime":%u,"totaltime":%u,"result":%s}', $errornumber,memory_get_peak_usage(true),$totalfiles,$totalfilesize,$filetime*1000,$serializetime*1000,$totaltime*1000,json_encode($link));

?>