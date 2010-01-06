<?php

/**
 * include/ajax.vote.php
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

// Check whether user is authorized
if(1 > $user->get('group')) {
    exit('{"error":"Authorization"}');
}

$survey = new Survey($errors,$surveyfile,$mysql);

$result = $survey->saveCombiVote($_GET['name'],$user->get('uid'),(integer) $_GET['qid'],explode(',',$_GET['aids']),(integer)$_GET['vn'] );
$mysql->execute(sprintf('UPDATE `user` SET `lastvote` = CURRENT_TIMESTAMP() WHERE `user`.`id` = %u LIMIT 1', $user->get('uid')));

$json = sprintf('{"result":%d}',$result); // %d because $result might be negative

?>