<?php

/**
 * include/login.php
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
$smarty->assign('moduleTpl', 'login.html');


if (isset($_POST['otp'])) {

    $user->reset();

    $otp = strtolower(trim($_POST['otp']));
    $otp_hash = hash('sha512',$otp);
    $otp = $mysql->escape($otp);
    $otp = substr($otp,0,30); // render hashes innocuous


    $result = $mysql->select(sprintf('SELECT * FROM `user` WHERE `user`.`password` = "%s" OR `user`.`password` = "%s" LIMIT 1', $otp,$otp_hash), 'assoc');

    // hash nur um ausspähen von admin passwörtern zu verhindern, weil hash zu lange zum merken ist.


    if ($result) {

        $mysql->execute(sprintf('UPDATE `user` SET `lastlogin` = CURRENT_TIMESTAMP() WHERE `user`.`id` = %u LIMIT 1', $result['id']));
     
        $user->set('name', $result['name']);
        $user->set('uid', $result['id']);
        $user->set('group', (integer) $result['group']);


        $errors[] = array('class' => 'hint', 'text' => '#LoggedIn#');

        if ($user->get('group') >= $pages['survey']['group']) {
            $smarty->assign('redirect', $pages['survey']['link']);
        }

        // Profile image?
        if(file_exists(sprintf('images/user/profile_%s.jpg',$result['name']))) {
            $user->set('profileImage', sprintf('images/user/profile_%s.jpg',$result['name']));
        }

    } else {
        $errors[] = '#LoginFailed#';
    }
}



?>