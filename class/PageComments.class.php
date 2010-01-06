<?php

/**
 * class/PageComments.class.php
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

/**
 * Description of PageComments
 *
 * Table Structure
 *
 * CREATE TABLE `pagecomments` (
 *   `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 *   `displayname` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
 *   `text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
 *   `uid` INT NOT NULL DEFAULT '0',
 *   `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
 * ) ENGINE = MYISAM ;
 *
 *
 *
 *
 * @author cuzi
 */
class PageComments {

    private $mysql;
    private $tablename;
    public $allowed;
    public $itemsPerPage = 10;
    public $maxText = 3000;

    function __construct(&$errors, &$mysql, $tablename) {
        if (!$errors) {
            $this->allowed = false;
            return;
        }

        $this->errors = &$errors;
        $this->mysql = &$mysql;


        if ($errors && (!$mysql || !$tablename)) {
            $this->errors[] = 'Unexpected Parameter in PageComments::__construct(&$errors,&$mysql,$tablename)';
            return;
        }

        $this->tablename = $this->mysql->escape($tablename);
        $this->allowed = true;
    }

    function optimizeTable() {
        return $this->mysql->execute(sprintf('OPTIMIZE TABLE `%s`',$this->tablename));
    }

    function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage > 1 ? (integer) $itemsPerPage : 1;
    }

    function active($x=null) {
        if (true === $x && isset($this->errors) && isset($this->mysql) && isset($this->tablename)) {
            $this->allowed = true;
        } else if (false === $x) {
            $this->allowed = false;
        }
        return $this->allowed;
    }



    function deleteComment($id) {
        $id = (integer) $id;


        $sql = sprintf('DELETE FROM `%s` WHERE `id` = %u LIMIT 1', $this->tablename, $id);
        $result = $this->mysql->execute($sql);

        return $result;
    }


    /*
     *  @return int statuscode:  1 -> everything ok and done
     *                           2 -> displayname is wrong
     *                           3 -> text is wrong
     *                           4 -> inserting failed
     */

    function saveComment($text, $displayname, $uid=false) {
        $text = $this->mysql->escape(substr(trim($text), 0, $this->maxText));
        $displayname = $this->mysql->escape(trim($displayname));
        if (false !== $uid)
            $uid = (integer) $uid;

        if ('' == displayname) {
            return 2;
        }
        if ('' == $text) {
            return 3;
        }

        // Check wether user id is possible
        if (false !== $uid) {
            $sql = dsprintf('SELECT 1 FROM `user` WHERE `user`.`id` = %u LIMIT 1', $uid);
            $result = $this->mysql->select($sql, 'field');
            if (!$result) {
                $uid = 0;
            }
        }

        $sql = sprintf('INSERT INTO `%s` (`displayname`,`text`,`uid`,`time`) VALUES ("%s","%s",%u,CURRENT_TIMESTAMP())', $this->tablename, $displayname, $text, $uid);
        $result = $this->mysql->execute($sql);
        if (!$result) {
            return 4;
        }

        return 1;
    }

    /*
     * getComments($page)
     * getComments($latestid,$oldestid)
     *
     */

    function getComments($page=1) {
        if (!$this->allowed) {
            return array();
        }

        --$page;
        $page = $page > 0 ? (integer) $page : 0;

        $from = (integer) $this->itemsPerPage * $page;
        $to = (integer) $from + $this->itemsPerPage + 1;

        $sql = sprintf('SELECT `id`,`displayname`,`text`,`time` FROM `%s` ORDER BY `time` DESC LIMIT %u,%u', $this->tablename, $from, $to);
        $result = $this->mysql->select($sql, 'assocList');
        if (!$result) {
            $this->errors[] = 'Unkown Database Error in PageComments::getComments()';
            return array();
        }

        if ($result[$this->itemsPerPage]) {
            $more = true;
        } else {
            $more = false;
        }

        return array('data' => $result, 'more' => $more);
    }

    /*
     * getCommentsRange(-1)         ->      Get all comments
     * getCommentsRange(78,0,-1)    ->      Get all comments with id from 0 to 78
     * getCommentsRange(78,0)       ->      Get latest 20 comments with id from 0 to 78
     * getCommentsRange(78,0,30)    ->      Get latest 30 comments with id from 0 to 78
     */
    function getCommentsRange($latestid, $oldestid=false, $limit=20) {

        if($latestid == -1 && $oldestid == false) {
            $limit = -1;
            $where_clause = '';
        } else {
           $where_clause = sprintf('WHERE `id` > %u OR `id` < %u',(integer) $latestid, (integer) $oldestid);
        }


        $limit_clause = '';
        if($limit != -1)
          $limit_clause = 'LIMIT '.((integer)$limit+1);


        $sql = sprintf('SELECT `id`,`displayname`,`text`,`time` FROM `%s` %s ORDER BY `time` DESC %s', $this->tablename,$where_clause,$limit_clause);

        $result = $this->mysql->select($sql, 'assocList');

        if (!$result) {
            return array('data' => array(), 'more' => false);
        }

        if ($limit != -1 && $result[$limit]) {
            $more = true;
        } else {
            $more = false;
        }

        return array('data' => $result, 'more' => $more);
    }

}

?>