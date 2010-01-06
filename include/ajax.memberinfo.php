<?php

/**
 * include/ajax.memberinfo.php
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
$uid = (integer) $_GET['id'];

$errornumber = 0;
$jsonresult = 0;
// ,UNIX_TIMESTAmP(`lastvote`) AS `time_lastvote`,UNIX_TIMESTAmP(`lastlogin`) AS `time_lastlogin`
$sql = sprintf('SELECT * FROM `user` WHERE `id` = %u', $uid);

$result = $mysql->select($sql,'assoc');
if ($result) {
    if ((integer)$result['group'] >= $user->get('group')) {
        $errornumber = 2; // No Access
    } else {
        $jsonresult = json_encode($result);
    }
} else {
    $errornumber = 1; // User Not Found
}


$json = sprintf('{"result":%s,"error":%d}', $jsonresult,$errornumber);
?>