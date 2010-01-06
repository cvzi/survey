<?php

/**
 * include/survey.php
 *
 *       ###packageName###
 *
 *  ###packageName### is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  ###packageName### is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with ###packageName###; if not, write to the Free Software
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
$smarty->assign('moduleTpl', 'single.html');
$survey = new Survey($errors, $surveyfile, $mysql);


// Vote
$result = false;
$statuscode = array(
    -2 => 'Deine Antwort wurde hinzugefügt',
    -1 => 'Deine Antwort wurde verändert',
    1 => 'Deine Antwort wurde gespeichert',
    2 => 'Error #02: Wrong Survey name',
    3 => 'Error #03: Could not test ids',
    4 => 'Error #04: Wrong user id',
    5 => 'Error #05: Wrong quid',
    6 => 'Error #06: Wrong aid',
    7 => 'Error #07: Insert or update failed',
    8 => 'Error #08: Can\'t find vote number',
    9 => 'Error #09: Can\'t overwrite a vote',
    10 => 'Error #10: Can\'t add a vote',
    11 => 'Error #11: Unkown error');

if (isset($_POST['aid']) && $_POST['aid'] == 0) { // Delete the vote
    $result = $survey->removeVote($_POST['name'], $user->get('uid'), (integer) $_POST['qid'], (integer) $_POST['vn']);
    if($result == -1) {
            $errors[] = array('text' => 'Deine Antwort wurde entfernt', 'class' => 'hint');
    } else {
            $errors[] = $statuscode[$result];
    }
}
else { // Normal voting
    if (isset($_POST['save']) && $_POST['combination']) { // combination vote
  $found_unselected = false;
  $found_selected = false;
        foreach ($_POST['aids'] as &$aid) {
          $aid = (integer) $aid;
    if($aid == 0) {
      $found_unselected = true;
    } else {
      $found_selected = true;
    }
        }
  if($found_unselected == true && $found_selected == false) { // All aids are 0 => remove combination Vote
        $result = $survey->removeVote($_POST['name'], $user->get('uid'), (integer) $_POST['qid'], (integer) $_POST['vn']);
       if($result == -1) {
           $errors[] = array('text' => 'Deine Antwort wurde entfernt', 'class' => 'hint');
        } else {
                $errors[] = $statuscode[$result];
        }
  } else { // Normal voting
          $result = $survey->saveCombiVote($_POST['name'], $user->get('uid'), (integer) $_POST['qid'], $_POST['aids'], (integer) $_POST['vn']);
  }
    } elseif ($_POST['save']) {  // single vote
        $result = $survey->saveVote($_POST['name'], $user->get('uid'), (integer) $_POST['qid'], (integer) $_POST['aid'], (integer) $_POST['vn']);
    }

    if (false !== $result) {
        if ($result < 2) {
            $errors[] = array('text' => $statuscode[$result], 'class' => 'hint');
            $mysql->execute(sprintf('UPDATE `user` SET `lastvote` = CURRENT_TIMESTAMP() WHERE `user`.`id` = %u LIMIT 1', $user->get('uid')));
        } else {
            $errors[] = $statuscode[$result];
        }
    }
}




// Settings
if ($_POST['saveSettings']) {
    $user->set('onlyOpen', (boolean) $_POST['onlyOpen']);
    $user->set('autoProceed', (boolean) $_POST['autoProceed']);
    $errors[] = array('text' => 'Einstellungen gespeichert', 'class' => 'hint');
}

// Position
$position = false;
if (isset($_GET['position'])) {
    $position = (integer) $_GET['position'];
}
elseif (isset($_POST['position'])) {
    $position = (integer) $_POST['position'];
}

if($position === false) {
  $position = 0;
} else if ($user->get('autoProceed', true)) {
    ++$position;
}


// Output things
$data = $survey->getQuestion($user->get('uid'), $position, $user->get('onlyOpen', true));
$smarty->assign('data', $data);
$smarty->assign('position', $position);
$smarty->assign('onlyOpen', $user->get('onlyOpen', true));
$smarty->assign('autoProceed', $user->get('autoProceed', true));

$smarty->assign('wide_content', true);


?>
