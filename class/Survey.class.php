<?php

/**
 * class/Survey.class.php
 *
 *       survey
 *
 *  UTF-8 encoded
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
 * @copyright Copyright (c) 2010, cuzi
 * @author cuzi@openmail.cc
 * @package survey
 * @version 2.0
 * @license http://gnu.org/copyleft/gpl.html GNU GPL
 *
 */
function compare_assoc_id($a, $b) {
    if ($a['id'] == $b['id']) {
        return 0;
    }

    return $a['id'] > $b['id'] ? 1 : -1;
}

/**
 * Description of Survey
 *
 * @author cuzi
 */
class Survey {

    private $xml;
    private $mysql;
    public $errors;
    public $filename;

    function __construct(&$errors, $file, &$mysql) {
        $this->filename = $file;
        $this->errors = &$errors;
        $this->mysql = &$mysql;


        if ($errors && (!$file || !$mysql)) {
            $this->errors[] = 'Unexpected Parameter in Survey::__construct(&$errors, $file, &$mysql)';
            return;
        }



        $this->xml = new SimpleXMLElement($file, null, true);
        $syntax = $this->checkIntegrity();
        if (!$syntax) {
            $this->xml = false;
        }
    }

    /*
     *  @return int statuscode: -1 -> everything ok
     *                           2 -> Wrong Survey name
     *                           7 -> Deleting failed (Probably MySQL Error)
     */

    function removeVote($surveyname, $uid, $qid, $vote_number) {
        $surveyname = $this->mysql->escape($surveyname);
        $uid = (integer) $uid;
        $qid = (integer) $qid;
        $aid = (integer) $aid;
        $vote_number = (integer) $vote_number;

        // Get tablenames
        $found = false;
        foreach ($this->xml->set as $set) {
            $attr = $set->attributes();
            $name = (String) $attr['name'];
            $foreach = $this->mysql->escape((String) $set->foreach);
            $stats = $this->mysql->escape((String) $set->result);
            $a = $this->mysql->escape((String) $set->a);
            $votes = 1;
            if ($set->votes) {
                $votes = (Integer) ((String) $set->votes);
            }

            if ($name == $surveyname) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            return 2;
        }


        $sql = dsprintf('DELETE FROM `%(stats)s` WHERE `for_id` = %(qid)u AND `uid` = %(uid)u AND `vote_number` = %(vn)u', array(
                    'uid' => $uid,
                    'stats' => $stats,
                    'qid' => $qid,
                    'vn' => $vote_number
                ));
        $result = $this->mysql->execute($sql);

        if (!$result) {
            return 7;
        }

        return -1;
    }

    /*
     *  @return int statuscode: -2 -> everything ok and done (added 1)
     *                          -1 -> everything ok and done (overwritten 1)
     *                           1 -> everything ok and done
     *                           2 -> Wrong Survey name
     *                           3 -> Could not test ids
     *                           4 -> Wrong user id
     *                           5 -> Wrong quid
     *                           6 -> Wrong aid
     *                           7 -> Insert or update failed
     *                           8 -> Can't find vote number
     *                           9 -> Can't overwrite a vote
     *                          10 -> Can't add a vote
     *                          11 -> Too many votes? Actually impossible
     */

    function saveCombiVote($surveyname, $uid, $qid, $aids_raw, $vote_number) {
        $surveyname = $this->mysql->escape($surveyname);
        $uid = (integer) $uid;
        $qid = (integer) $qid;
        $aids = array();
        foreach ($aids_raw as $aid) {
            $aids[] = (integer) $aid;
        }

        $vote_number = (integer) $vote_number;

        // Get tablenames
        $found = false;
        foreach ($this->xml->set as $set) {
            $attr = $set->attributes();
            $combination = (Boolean) $attr['combination'];
            $name = (String) $attr['name'];
            $foreach = $this->mysql->escape((String) $set->foreach);
            $stats = $this->mysql->escape((String) $set->result);
            if ($combination) {

                $as = array();
                foreach ($set->a as $a) {
                    $as[] = (String) $a;
                }
            }
            $votes = 1;
            if ($set->votes) {
                $votes = (Integer) ((String) $set->votes);
            }

            if ($name == $surveyname && $combination) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            return 2;
        }


        // Check wether everything is possible
        $parts = '';
        $i = 0;
        foreach ($as as $a) {
            $parts .= dsprintf('(SELECT 1 FROM `%(a)s` WHERE `%(a)s`.`id` = %(aid)u) AS `%(a)s`,', array(
                        'a' => $a,
                        'aid' => $aids[$i]
                    ));
            $i++;
        }


        $sql = dsprintf('
            SELECT     (SELECT 1 FROM `user` WHERE `user`.`id` = %(uid)u) as `user`,
                       %(parts)s
                       (SELECT 1 FROM `%(foreach)s` WHERE `%(foreach)s`.`id` = %(qid)u) as `foreach`', array(
                    'uid' => $uid,
                    'foreach' => $foreach,
                    'qid' => $qid,
                    'a' => $a,
                    'parts' => $parts
                ));


        $result = $this->mysql->select($sql, 'assoc');
        if (!$result) {
            return 3;
        }
        if (1 != $result['user']) {
            return 4;
        }
        if (1 != $result['foreach']) {
            return 5;
        }
        foreach ($result as $field) {
            if (1 != $field) {
                return 6;
            }
        }


        // Check for existing answers
        $sql = dsprintf('SELECT MAX(`vote_number`) AS `total_vote_number` FROM `%(stats)s` WHERE `for_id` = %(qid)u AND `uid` = %(uid)u', array(
                    'uid' => $uid,
                    'stats' => $stats,
                    'qid' => $qid
                ));

        $result = $this->mysql->select($sql, 'assoc');

        if (!$result) {
            return 8;
        }

        if (!$result['total_vote_number']) { // No votes yet, just insert with vote_number=1
            $sets = array();
            foreach ($as as $a) {
                $sets[] = sprintf('`set_%s_id`', $a);
            }
            $aids_str = implode(',', $aids);
            $sql = sprintf('INSERT INTO `%s` (`for_id`,`set_id`,`vote_number`,`uid`,`time`,%s) VALUES (%u,1,1,%u,CURRENT_TIMESTAMP(),%s)', $stats, implode(',', $sets), $qid, $uid, $aids_str);

            $result = $this->mysql->execute($sql);
            if (!$result) {
                return 7;
            }
        } elseif ($votes == (integer) $result['total_vote_number']) { // There are already enough votes
            if ($vote_number > $votes || $vote_number < 1) {
                $vote_number = $votes;
            }

            $sets = array();
            $i = 0;
            foreach ($as as $a) {
                $sets[] = sprintf('`set_%s_id` = %u', $a, $aids[$i]);
                $i++;
            }
            $sets = implode(',', $sets);

            $sql = sprintf('UPDATE `%s` SET `set_id` = 1, `time` = CURRENT_TIMESTAMP(),%s WHERE `uid` = %u AND `for_id` = %u AND `vote_number` = %u',
                            $stats, $sets, $uid, $qid, $vote_number);
            $result = $this->mysql->execute($sql);
            if (!$result) {
                return 9;
            }
            return -1;
        } elseif ($votes > (integer) $result['total_vote_number']) { // There are already votes, but still empty votes left
            $sets = array();
            foreach ($as as $a) {
                $sets[] = sprintf('`set_%s_id`', $a);
            }
            $aids_str = implode(',', $aids);

            $sql = sprintf('INSERT INTO `%s` (`for_id`,`set_id`,`vote_number`,`uid`,`time`,%s) VALUES (%u,1,%u,%u,CURRENT_TIMESTAMP(),%s)',
                            $stats, implode(',', $sets), $qid, 1 + ( (integer) $result['total_vote_number']), $uid, $aids_str);
            $result = $this->mysql->execute($sql);
            if (!$result) {
                return 10;
            }
            return -2;
        } else { // Probably to many votes?
            return 11;
        }

        return 1;
    }

    /*
     *  @return int statuscode: -2 -> everything ok and done (added 1)
     *                          -1 -> everything ok and done (overwritten 1)
     *                           1 -> everything ok and done
     *                           2 -> Wrong Survey name
     *                           3 -> Could not test ids
     *                           4 -> Wrong user id
     *                           5 -> Wrong quid
     *                           6 -> Wrong aid
     *                           7 -> Insert or update failed
     *                           8 -> Can't find vote number
     *                           9 -> Can't overwrite a vote
     *                          10 -> Can't add a vote
     *                          11 -> Too many votes? Actually impossible
     */

    function saveVote($surveyname, $uid, $qid, $aid, $vote_number) {
        $surveyname = $this->mysql->escape($surveyname);
        $uid = (integer) $uid;
        $qid = (integer) $qid;
        $aid = (integer) $aid;
        $vote_number = (integer) $vote_number;

        // Get tablenames
        $found = false;
        foreach ($this->xml->set as $set) {
            $attr = $set->attributes();
            $name = (String) $attr['name'];
            $foreach = $this->mysql->escape((String) $set->foreach);
            $stats = $this->mysql->escape((String) $set->result);
            $a = $this->mysql->escape((String) $set->a);
            $votes = 1;
            if ($set->votes) {
                $votes = (Integer) ((String) $set->votes);
            }

            if ($name == $surveyname) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            return 2;
        }


        // Check wether everything is possible
        $sql = dsprintf('
            SELECT     (SELECT 1 FROM `user` WHERE `user`.`id` = %(uid)u) as `user`,
                       (SELECT 1 FROM `%(foreach)s` WHERE `%(foreach)s`.`id` = %(qid)u) as `foreach`,
                       (SELECT 1 FROM `%(a)s` WHERE `%(a)s`.`id` = %(aid)u) as `a`', array(
                    'uid' => $uid,
                    'foreach' => $foreach,
                    'qid' => $qid,
                    'a' => $a,
                    'aid' => $aid,
                ));

        $result = $this->mysql->select($sql, 'assoc');
        if (!$result) {
            return 3;
        }
        if (1 != $result['user']) {
            return 4;
        }
        if (1 != $result['foreach']) {
            return 5;
        }
        if (1 != $result['a']) {
            return 6;
        }

        // Check for existing answers
        $sql = dsprintf('SELECT MAX(`vote_number`) AS `total_vote_number` FROM `%(stats)s` WHERE `for_id` = %(qid)u AND `uid` = %(uid)u', array(
                    'uid' => $uid,
                    'stats' => $stats,
                    'qid' => $qid
                ));
        $result = $this->mysql->select($sql, 'assoc');

        if (!$result) {
            return 8;
        }

        if (!$result['total_vote_number']) { // No votes yet, just insert with vote_number=1
            $sql = sprintf('INSERT INTO `%s` (`for_id`,`set_id`,`vote_number`,`uid`,`time`) VALUES (%u,%u,1,%u,CURRENT_TIMESTAMP())', $stats, $qid, $aid, $uid);
            $result = $this->mysql->execute($sql);
            if (!$result) {
                return 7;
            }
        } elseif ($votes == (integer) $result['total_vote_number']) { // There are already enough votes
            if ($vote_number > $votes || $vote_number < 1) {
                $vote_number = $votes;
            }

            $sql = sprintf('UPDATE `%s` SET `set_id` = %u, `time` = CURRENT_TIMESTAMP() WHERE `uid` = %u AND `for_id` = %u AND `vote_number` = %u',
                            $stats, $aid, $uid, $qid, $vote_number);
            $result = $this->mysql->execute($sql);
            if (!$result) {
                return 9;
            }
            return -1;
        } elseif ($votes > (integer) $result['total_vote_number']) { // There are already votes, but still empty votes left
            $sql = sprintf('INSERT INTO `%s` (`for_id`,`set_id`,`vote_number`,`uid`,`time`) VALUES (%u,%u,%u,%u,CURRENT_TIMESTAMP())',
                            $stats, $qid, $aid, 1 + ( (integer) $result['total_vote_number']), $uid);
            $result = $this->mysql->execute($sql);
            if (!$result) {
                return 10;
            }
            return -2;
        } else { // Probably to many votes?
            return 11;
        }

        /*
          $sql = dsprintf('DELETE FROM `%(stats)s` WHERE `for_id` = %(qid)u AND `uid` = %(uid)u', array(
          'uid' => $uid,
          'stats' => $stats,
          'qid' => $qid
          ));
          $this->mysql->execute($sql);

          $sql = sprintf('INSERT INTO `%s` (`for_id`,`set_id`,`uid`,`time`) VALUES (%u,%u,%u,CURRENT_TIMESTAMP())
          ON DUPLICATE KEY UPDATE `set_id` = %u, `time` = CURRENT_TIMESTAMP()', $stats, $qid, $aid, $uid, $aid);
          $result = $this->mysql->execute($sql);
          if (!$result) {
          return 7;
          }


         */
        return 1;
    }

    function getEverythingForVoting($uid) {
        $uid = (integer) $uid;
        $result = array();
        foreach ($this->xml->set as $set) {
            $attr = $set->attributes();
            $name = (String) $attr['name'];
            $combination = (Boolean) $attr['combination'];
            $title = (String) $attr['title'];

            $foreach = $this->mysql->escape((String) $set->foreach);
            $a = $this->mysql->escape((String) $set->a);
            $stats = $this->mysql->escape((String) $set->result);
            $votes = 1;

            if ($set->votes) {
                $votes = (Integer) ((String) $set->votes);
            }

            if ($combination) {

                $as = array();
                foreach ($set->a as $a) {
                    $as[] = (String) $a;
                }
            }


            // Select a (which will be available for choice)
            if ($combination) {
                $a_array = array();
                foreach ($as as $a) {
                    $sql = sprintf('SELECT `id`,`text` FROM `%s` ORDER BY `text` ASC', $a);
                    $a_array[] = $this->mysql->select($sql, 'assocList');
                }

                $question_array = $this->mysql->select($sql, 'assocList');
            } else {
                $sql = sprintf('SELECT `id`,`text` FROM `%s` ORDER BY `text` ASC', $a);
                $a_array = $this->mysql->select($sql, 'assocList');
            }



            // Select the questions
            if ($combination) {
                $columns = array();
                foreach ($as as $a) {
                    $columns[] = sprintf('`set_%s_id` AS %s', $a, $a);
                }

                $sql = 'SELECT          `%(foreach)s`.`id`,
                                        `%(foreach)s`.`text`,
                                        `%(stats)s`.`set_id` AS `status`,
                                        `%(stats)s`.`vote_number`,
                                        %(columns)s
                            FROM `%(foreach)s`
                            LEFT JOIN   `%(stats)s`
                            ON          `%(stats)s`.`for_id` = `%(foreach)s`.`id`
                            AND         `%(stats)s`.`uid` = %(uid)u ';
                $sql = dsprintf($sql, array(
                            'foreach' => $foreach,
                            'stats' => $stats,
                            'uid' => $uid,
                            'columns' => implode(',', $columns)
                        ));
            } else {

                $sql = 'SELECT          `%(foreach)s`.`id`,
                                        `%(foreach)s`.`text`,
                                        `%(stats)s`.`set_id` AS `status`,
                                        `%(stats)s`.`vote_number`
                            FROM `%(foreach)s`
                            LEFT JOIN   `%(stats)s`
                            ON          `%(stats)s`.`for_id` = `%(foreach)s`.`id`
                            AND         `%(stats)s`.`uid` = %(uid)u ';
                $sql = dsprintf($sql, array(
                            'foreach' => $foreach,
                            'stats' => $stats,
                            'uid' => $uid
                        ));
            }

            $question_array = $this->mysql->select($sql, 'assocList');

            $mupltiple_question_array = array();
            // Add virtual questions for multiple vote
            $done_question = array();
            $allbyid = array();
            foreach ($question_array as &$value) {
                $allbyid[$value['id']] = $value;
                if ($done_question[$value['id']]) {
                    if ($votes == $done_question[$value['id']]) {
                        continue;
                    } else {
                        $mupltiple_question_array[] = $value;
                        ++$done_question[$value['id']];
                    }
                } else {
                    $value['vote_number'] = 1;
                    $mupltiple_question_array[] = $value;
                    $done_question[$value['id']] = 1;
                }
            }


            foreach ($done_question as $key => &$value) {
                while ($votes != $value) {
                    ++$value;
                    $q = $allbyid[$key];
                    $q['status'] = null;
                    $q['vote_number'] = $value;
                    $mupltiple_question_array[] = $q;
                }
            }

            // Merge sets
            if ($combination) {
                foreach ($mupltiple_question_array as &$value) {
                    $ar = array();
                    foreach ($as as $a) {
                        $ar[] = $value[$a];
                    }

                    $value['status_array'] = $ar;
                }
            }


            uasort($mupltiple_question_array, 'compare_assoc_id');


            $result[$name] = array('title' => $title, 'combination' => $combination, 'questions' => $mupltiple_question_array, 'a' => $a_array);
        }

        return $result;
    }

    function getQuestion($uid, $index, $open=false) {
        $uid = (integer) $uid;
        $index = (integer) $index;

        $all = $this->getEverythingForVoting($uid);

        if ($index >= 0) {
            $i = 0;
            foreach ($all as $name => &$section) {
                foreach ($section['questions'] as &$question) {
                    if ($open && $question['status']) {
                        continue;
                    }
                    if ($i == $index) {
                        return array(
                            'combination' => $section['combination'],
                            'question' => $question,
                            'a' => $section['a'],
                            'title' => $section['title'],
                            'name' => $name);
                    }
                    ++$i;
                }
            }
        } else {
            $list = array();
            $i = 0;
            foreach ($all as $name => &$section) {
                foreach ($section['questions'] as &$question) {
                    if ($open && $question['status']) {
                        continue;
                    }
                    $list[] = array(
                        'combination' => $section['combination'],
                        'question' => $question,
                        'a' => $section['a'],
                        'title' => $section['title'],
                        'name' => $name);
                    ++$i;
                }
            }
            if (isset($list[$i + $index])) {
                return $list[$i + $index];
            }
        }
        return false;
    }

    function getStatistics() {

        /*
          ############new overview:



          SELECT
          `studentquestions.id` AS `for_id`,
          `studentquestions.text` AS `for_text`,
          GROUP_CONCAT(`students.text`) AS `a_text`,
          COUNT(1) AS `coequals`,
          `number` AS `set_number`,
          `t3`.`set_total`,
          `number` / `t3`.`set_total` * 100 AS `set_percent`


          FROM (
          SELECT
          `studentquestions`.`id` as `studentquestions.id`,
          `studentquestions`.`text` as `studentquestions.text`,
          `students`.`text` as `students.text`,
          COUNT(1) AS `number`

          FROM `studentquestions`
          LEFT JOIN `studentstats` ON `studentstats`.`for_id` = `studentquestions`.`id`
          LEFT JOIN `students` ON `studentstats`.`set_id` = `students`.`id`
          GROUP BY `studentstats`.`set_id`
          ORDER BY `number` DESC
          )
          AS t1
          LEFT JOIN(

          SELECT
          `studentquestions.id` AS `for_id`,
          `number` AS `set_number`,
          max(`number`) AS `for_max`,
          SUM(`number`) AS `set_total`

          FROM (
          SELECT
          `studentquestions`.`id` as `studentquestions.id`,
          `studentquestions`.`text` as `studentquestions.text`,
          `students`.`text` as `students.text`,
          COUNT(1) AS `number`

          FROM `studentquestions`
          LEFT JOIN `studentstats` ON `studentstats`.`for_id` = `studentquestions`.`id`
          LEFT JOIN `students` ON `studentstats`.`set_id` = `students`.`id`
          GROUP BY `studentstats`.`set_id`
          ORDER BY `number` DESC
          )
          AS t2
          GROUP BY `studentquestions.id`


          ) t3 ON t3.`for_id` = `studentquestions.id`

          WHERE t3.`for_max` = `number`

          GROUP BY `studentquestions.id`
          ORDER BY `for_text` DESC











          // ######### details:
          SELECT
          `studentquestions`.`id` as `studentquestions.id`,
          `studentquestions`.`text` as `studentquestions.text`,
          `students`.`text` as `students.text`,
          COUNT(1) AS `Anzahl`

          FROM `studentquestions`

          LEFT JOIN `studentstats` ON `studentstats`.`for_id` = `studentquestions`.`id`

          LEFT JOIN `students` ON `studentstats`.`set_id` = `students`.`id`

          GROUP BY `studentstats`.`for_id`,`studentstats`.`set_id` ORDER BY `studentquestions.text`,`Anzahl` DESC


          // ######### overview:


          SELECT
          `studentquestions.id`,
          `studentquestions.text`,
          `students.text`,
          `Anzahl`

          FROM (
          SELECT
          `studentquestions`.`id` as `studentquestions.id`,
          `studentquestions`.`text` as `studentquestions.text`,
          `students`.`text` as `students.text`,
          COUNT(1) AS `Anzahl`

          FROM `studentquestions`

          LEFT JOIN `studentstats` ON `studentstats`.`for_id` = `studentquestions`.`id`

          LEFT JOIN `students` ON `studentstats`.`set_id` = `students`.`id`

          GROUP BY `studentstats`.`set_id`
          ORDER BY `Anzahl` DESC

          ) AS T1

          GROUP BY `studentquestions.id`

         */
        $result = array();

        foreach ($this->xml->set as $set) {
            $foreach = $this->mysql->escape((String) $set->foreach);
            $a = $this->mysql->escape((String) $set->a);
            $stats = $this->mysql->escape((String) $set->result);
            $votes = 1;
            if ($set->votes) {
                $votes = (Integer) ((String) $set->votes);
            }


            $attr = $set->attributes();
            $name = (String) $attr['name'];
            $title = (String) $attr['title'];
            $combination = (Boolean) $attr['combination'];

            if ($combination) {

                $as = array();
                foreach ($set->a as $a) {
                    $as[] = (String) $a;
                }
            }


            if ($combination) {

                // Select the questions with result

                $left_joins = '';
                $ands = '';
                $ws_concat = array();
                foreach ($as as $a) {
                    $left_joins .= dsprintf(' LEFT JOIN `%(a)s` ON `%(a)s`.`id` = `t1`.`set_%(a)s_id` ', array(
                                'a' => $a,
                                'stats' => $stats
                            ));
                    $ands .= sprintf(' AND `t2`.`set_%s_id` = `t1`.`set_%s_id` ', $a, $a);

                    //$ws_concat[] = sprintf('`t1`.`set_%s_id`',$a);
                    $ws_concat[] = sprintf('`%s`.`text`', $a);
                }
                $ws_concat = implode(',', $ws_concat);


                $sql = '

SELECT 	`for_id` AS `for_id`,
		`%(foreach)s`.`text` AS `for_text`,
		CONCAT_WS(" und ", %(ws_concat)s  )  AS `a_text` ,
		COUNT( 1 ) AS `set_number`

FROM `%(stats)s` AS `t1`

LEFT JOIN `%(foreach)s` ON `for_id` = `%(foreach)s`.`id`

%(leftjoins)s


GROUP BY CONCAT_WS(" ",`for_id`,`a_text`)
ORDER BY `for_id` ASC,`set_number` DESC

';



                $sql = dsprintf($sql, array(
                            'foreach' => $foreach,
                            'a' => $a,
                            'stats' => $stats,
                            'leftjoins' => $left_joins,
                            'ands' => $ands,
                            'ws_concat' => $ws_concat
                        ));
            } else {


                // Select the questions with result
                $sql = '

SELECT 	`for_id` AS `for_id`,
		`%(foreach)s`.`text` AS `for_text`,
		`set_id`,
		`%(a)s`.`text`  AS `a_text` ,
		COUNT( 1 ) AS `set_number`

FROM `%(stats)s` AS `t1`

LEFT JOIN `%(foreach)s` ON `for_id` = `%(foreach)s`.`id`

LEFT JOIN `%(a)s` ON `set_id` = `%(a)s`.`id`


GROUP BY CONCAT_WS(" ",`for_id`,`a_text`)
ORDER BY `for_id` ASC,`set_number` DESC 

';

                $sql = dsprintf($sql, array(
                            'foreach' => $foreach,
                            'a' => $a,
                            'stats' => $stats
                        ));
            }

            $question_array = $this->mysql->select($sql, 'assocList');
            unset($sql);

            $result_assoc = array();

            foreach ($question_array as &$record) {

                $for_id = (integer) $record['for_id'];
                $for_text = $record['for_text'];
                $set_number = (integer) $record['set_number'];
                $a_text = $record['a_text'];

                if (strpos($a_text, ' und ') !== false) {
                    $key_ar = explode(' und ', $a_text);
                    rsort($key_ar);
                    $a_text = implode(' und ', $key_ar);
                }

                if (isset($result_assoc[$for_id])) {
                    $result_assoc[$for_id]['total'] += $set_number;
                    if (isset($result_assoc[$for_id]['sets'][$a_text])) {
                        $result_assoc[$for_id]['sets'][$a_text] += $set_number;
                    } else {
                        $result_assoc[$for_id]['sets'][$a_text] = $set_number;
                    }
                } else {
                    $result_assoc[$for_id] = array('name' => $name, 'for_id' => $for_id, 'for_text' => $for_text, 'total' => $set_number, 'sets' => array(
                            $a_text => $set_number
                            ));
                }
            }
            unset($question_array);

            foreach ($result_assoc as &$record) {

                // Sets
                arsort($record['sets']);
                if (3 < count($record['sets'])) {
                    $i = 0;
                    $last = 0;
                    $del = false;
                    foreach ($record['sets'] as &$n) {
                        if ($del) {
                            $n = 0;
                        } else {
                            if ($i > 2) {
                                if ($last != $n) {
                                    $n = 0;
                                    $del = true;
                                }
                            }
                            if ($last != $n) {
                                ++$i;
                            }
                            $last = $n;
                        }
                    }
                }
            }

            $result[$title] = $result_assoc;
            unset($result_assoc);
        }

        return $result;
    }

    function saveStatistics($data=null) {
        // $data is the result of Survey::getStatistics()

        if (null == $data) {
            $data = $this->getStatistics();
        }

        $error = 0;

        // Save in history table `stats_history`:
        foreach ($data as $title => &$table) {
            $s_title = $this->mysql->escape($title);

            foreach ($table as &$record) {

                $s_name = $this->mysql->escape($record['name']);
                $s_for_id = $record['for_id'];
                $s_for_text = $this->mysql->escape($record['for_text']);
                $s_total = $record['total'];
                $s_sets = $this->mysql->escape(json_encode($record['sets']));

                $sql = dsprintf('INSERT INTO `stats_history` (
                  `timestamp` ,
                  `surveyname` ,
                  `title`,
                  `total`,
                  `for_id` ,
                  `for_text` ,
                  `sets`
                )
                VALUES (
                  CURRENT_TIMESTAMP ,
                  \'%(name)s\',
                  \'%(title)s\',
                  %(total)u,
                  %(for_id)u,
                  \'%(for_text)s\',
                  \'%(sets)s\'
                )
               ', $s_name, $s_title, $s_total, $s_for_id, $s_for_text, $s_sets);

                $result = $this->mysql->execute($sql);
                if (!$result) {
                    ++$error;
                }
            }
        }

        if (!$error) {
            return true;
        }
        $this->errors[] = 'In Survey::saveStatistics() occurred ' . $error . ' Errors';
        return false;
    }

    function getMemberStatus($order_by='surname', $order_direction='ASC') {


        /*

          SELECT `tables2`.`uid` AS `id`,`sets` AS `sets`,`user`.`name` FROM (
          SELECT `uid`,SUM(`sets`) AS `sets` FROM (
          SELECT `uid`,COUNT(*) AS `sets` FROM `teachersstats` GROUP BY `uid`
          UNION SELECT `uid`,COUNT(*) AS `sets` FROM `studentstats` GROUP BY `uid`
          UNION SELECT `id` AS `uid`,0 AS `sets` FROM `user` ) AS tables
          GROUP BY `uid` ) AS tables2
          LEFT JOIN `user` ON `user`.`id` = `tables2`.`uid`

          Result:
          id 	sets 	name
          1 	3 	Max Mustermann
          2 	3 	Thomas Müller
          3 	0 	Lise Lotte
          .       .       .
          .       .       .
          .       .       .
          .       .       .



         */
        $middlePart = array();
        $total_foreach = 0;
        foreach ($this->xml->set as $set) {
            $foreach = $this->mysql->escape((String) $set->foreach);
            $a = $this->mysql->escape((String) $set->a);
            $stats = $this->mysql->escape((String) $set->result);
            $votes = 1;
            if ($set->votes) {
                $votes = (Integer) ((String) $set->votes);
            }

            $attr = $set->attributes();
            $name = (String) $attr['name'];
            $combination = (Boolean) $attr['combination'];

            $middlePart[] = sprintf('SELECT `uid`,COUNT(*) AS `sets` FROM `%s` GROUP BY `uid`', $stats);

            $sql = sprintf('SELECT COUNT(*) FROM `%s`', $foreach);

            $field = $this->mysql->select($sql, 'field');

            $total_foreach += ( (integer) $field ) * $votes;
        }

        $middlePart = implode(' UNION ALL ', $middlePart);

        $sql = sprintf('
SELECT `tables2`.`uid` AS `id`,tables2.`sets` AS `sets`,`user`.`name`,
(SELECT SUBSTR(`user`.`name`,LENGTH(`user`.`name`) - LOCATE(" ", REVERSE(`user`.`name`))+1) AS `surname`) AS `surname`

FROM (
    SELECT `uid`,SUM(`sets`) AS `sets` FROM (
        %s
        UNION ALL SELECT `id` AS `uid`,0 AS `sets` FROM `user` ) AS tables
    GROUP BY `uid` ) AS tables2
LEFT JOIN `user` ON `user`.`id` = `tables2`.`uid` ORDER BY `%s` %s', $middlePart, $this->mysql->escape($order_by), $this->mysql->escape($order_direction));

        $everyone = $this->mysql->select($sql);

        $result = array('data' => $everyone, 'total' => $total_foreach);

        return $result;
    }

    private function backupTable($name, $file) {
        //$sql = sprintf('SELECT * FROM `%s` INTO OUTFILE "%s" FIELDS TERMINATED BY ";" OPTIONALLY ENCLOSED BY "\'"',$name,$this->mysql->escape($file));
        //return $this->mysql->execute($sql);
        $f = fopen($file, 'w+');
        if (!$f) {
            $this->errors[] = "Failed to open file: '$file'";
            return false;
        }

        if (!is_writable($file)) {
            fclose($f);
            $this->errors[] = "File: '$file' is not writable";
            return false;
        }

        $sql = sprintf('SELECT * FROM `%s`', $name);
        $result = $this->mysql->select($sql);
        if (!$result) {
            fclose($f);
            return false;
        }
        foreach ($result as &$record) {
            $line = '';
            $i = 0;
            $len = count($record) - 1;
            foreach ($record as &$field) {
                if (is_numeric($field)) {
                    $line .= $field;
                } else {
                    $line .= '"' . $field . '"';
                }
                if ($i != $len) {
                    $line .= ';';
                } else {
                    $line .= "\n";
                }
                ++$i;
            }
            fwrite($f, $line);
        }
        fclose($f);
        return true;
    }

    function backup($fileprefix, $filesuffix='.csv') {
        $error = 0;
        foreach ($this->xml->set as $set) {
            foreach ($set->a as $a) {
                $result = $this->backupTable((string) $a, $fileprefix . (string) $a . $filesuffix);
                $error = $result ? $error : $error + 1;
            }

            $result = $this->backupTable((string) $set->result, $fileprefix . (string) $set->result . $filesuffix);
            $error = $result ? $error : $error + 1;

            $result = $this->backupTable((string) $set->foreach, $fileprefix . (string) $set->foreach . $filesuffix);
            $error = $result ? $error : $error + 1;
        }
        return $error ? false : true;
    }

    function optimizeTable() {

        $error = 0;
        foreach ($this->xml->set as $set) {
            foreach ($set->a as $a) {
                $result = $this->mysql->execute(sprintf('OPTIMIZE TABLE `%s`', (string) $a));
                $error = $result ? $error : $error + 1;
            }

            $result = $this->mysql->execute(sprintf('OPTIMIZE TABLE `%s`', (string) $set->result));
            $error = $result ? $error : $error + 1;

            $result = $this->mysql->execute(sprintf('OPTIMIZE TABLE `%s`', (string) $set->foreach));
            $error = $result ? $error : $error + 1;
        }
        return $error ? false : true;
    }

    function checkIntegrity() {

        foreach ($this->xml->set as $set) {
            $attr = $set->attributes();
            $combination = (Boolean) $attr['combination'];
            if (!$set->foreach || !$set->a || !$set->result || ($combination && !$set->a[1])) {
                $this->errors[] = 'Syntax error in ' . $this->filename . ' in Survey::db_createTables()';
                return false;
            }
        }
        return true;
    }

    function db_createTables() {
        $sql = array();
        foreach ($this->xml->set as $set) {
            $attr = $set->attributes();
            $combination = (Boolean) $attr['combination'];

            $sql[] = $this->db_table_structure('foreach', (string) $set->foreach);

            if ($combination) { // Combination
                $names = array();
                foreach ($set->a as $a) {
                    $sql[] = $this->db_table_structure('a', (string) $a);
                    $names[] = (string) $a;
                }
                $sql[] = $this->db_table_structure('combination_result', (string) $set->result, $names);
            } else {
                $sql[] = $this->db_table_structure('a', (string) $set->a);
                $sql[] = $this->db_table_structure('result', (string) $set->result);
            }
        }


        $error = 0;
        foreach ($sql as $command) {
            $result = $this->mysql->execute($command);
            if (!$result)
                ++$error;
        }

        return $error;
    }

    private function db_table_structure($type, $name, $combi_sets=false) {
        $name = $this->mysql->escape($name);
        switch ($type) {
            case 'a':
            case 'foreach':
                return 'CREATE TABLE IF NOT EXISTS ' . $name . ' (
         id INT AUTO_INCREMENT PRIMARY KEY,
         text TEXT not null
       )';
                break;
            case 'result':
                return 'CREATE TABLE IF NOT EXISTS ' . $name . ' (
         id INT AUTO_INCREMENT PRIMARY KEY,
         for_id INT not null COMMENT "id from foreach e.g. teacherquestion",
         set_id INT not null COMMENT "id from a e.g. teacher",
         vote_number TINYINT not null DEFAULT 1,
         uid INT not null COMMENT "User id",
         time TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP
       )';
                break;
            case 'combination_result':
                $sets = '';
                foreach ($combi_sets as $set) {
                    $sets .= 'set_' . $set . '_id INT not null COMMENT "id from ' . $set . '",';
                }

                $sql = 'CREATE TABLE IF NOT EXISTS ' . $name . ' (
         id INT AUTO_INCREMENT PRIMARY KEY,
         for_id INT not null COMMENT "id from foreach e.g. teacherquestion",
         set_id TINYINT not null DEFAULT 1 COMMENT "compatibility to non-combination",
         ' . $sets . '
         vote_number TINYINT not null DEFAULT 1,
         uid INT not null COMMENT "User id",
         time TIMESTAMP not null DEFAULT CURRENT_TIMESTAMP
       )';

                return $sql;
                break;
            default:
                return false;
        }
        return false;
    }

    function db_createTableTriggers() {
        foreach ($this->xml->set as $set) {
            $sql_ar = $this->db_trigger_structure((string) $set->result);
            $result = $this->mysql->execute($sql_ar[0]);
            $result = $this->mysql->execute($sql_ar[1]);
            if (!$result)
                ++$error;
        }

        return $error;
    }

    private function db_trigger_structure($name) {
        $name = $this->mysql->escape($name);

        $drop = 'DROP TRIGGER IF EXISTS `' . $name . '`';

        $create = 'CREATE TRIGGER `' . $name . '` BEFORE UPDATE ON `' . $name . '`
                FOR EACH ROW
                UPDATE `user` SET `user`.`lastvote` = CURRENT_TIMESTAMP() WHERE `user`.`id` = `NEW`.`uid`';

        return array($drop, $create);
    }

}

?>