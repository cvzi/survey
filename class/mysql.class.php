<?php

/**
 * class/mysql.class.php
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

function dsprintf() {
  $data = func_get_args(); // get all the arguments
  $string = array_shift($data); // the string is the first one
  if (is_array(func_get_arg(1))) { // if the second one is an array, use that
    $data = func_get_arg(1);
  }
  $used_keys = array();
  // get the matches, and feed them to our function
  $string = preg_replace('/\%\((.*?)\)(.)/e',
    'dsprintfMatch(\'$1\',\'$2\',\$data,$used_keys)',$string);
  $data = array_diff_key($data,$used_keys); // diff the data with the used_keys
  return vsprintf($string,$data); // yeah!
}

function dsprintfMatch($m1,$m2,&$data,&$used_keys) {

  if (isset($data[$m1])) { // if the key is there
    $str = $data[$m1];
    $used_keys[$m1] = $m1; // dont unset it, it can be used multiple times
    return sprintf("%".$m2,$str); // sprintf the string, so %s, or %d works like it should
  } else {
    return "%".$m2; // else, return a regular %s, or %d or whatever is used
  }
}


/**
 * Description of mysql
 *
 * @author cuzi@openmail.cc
 */
class mysql extends mysqli {

    public $conn = false;

    public $sqlcounter = 0;
    public $executecounter = 0;

    public $sql_query;

    private $showerror;
    private $quitonerror;

    public $htmlerror = false;
    public $errors;


    function __construct(&$errors, $mysqlhost, $mysqluser, $mysqlpasswd, $mysqldb=false, $showerror=true, $quitonerror=false) {
        $this->errors = &$errors;

        if ($mysqldb)
            @parent::__construct($mysqlhost, $mysqluser, $mysqlpasswd, $mysqldb);
        else
            @parent::__construct($mysqlhost, $mysqluser, $mysqlpasswd);

        if (mysqli_connect_errno ()) {
            $this->error('OPEN');
            $this->conn = false;
        }
        else {
            $this->conn = true;
        }

        $this->showerror = $showerror ? true : false;
        $this->quitonerror = $quitonerror ? true : false;

        if($this->conn) {
            $this->execute('SET NAMES "utf8"');
        }

    }

    function prepare($sql) {
        $sql = trim($sql);
        if(substr($sql, -1, 1) == ';') {
            $sql = substr($sql,0,-1);
        }
        $this->sql_query = $sql;
    }
    function select($sql, $resultType='assocList', $cache=false) {
        if(!$this->conn) {
            $this->error('NO.CONN');
            return;
        }
        $this->prepare($sql);
        $this->sqlcounter++;
        $this->htmlerror = false;
        $result = $this->query($this->sql_query);
        if ($result && is_object($result)) {
            if ($result->num_rows) {
                return $this->processResult($result, $resultType);
            } else {
                return false;
            }
        } else {
            $this->error('QUERY FAILED');
            return false;
        }
    }

    function processResult(MySQLi_Result $result, $resultType) {
        switch ($resultType) {
            case 'assoc':
                $re = $result->fetch_assoc();
                return $re;
            case 'assocList':
                $re = array();
                while ($row = $result->fetch_assoc())
                    $re[] = $row;
                return $re;
            case 'array':
                $re = $result->fetch_array(MYSQLI_NUM);
                return $re;
            case 'arrayList':
                $re = array();
                while ($row = $result->fetch_array(MYSQLI_NUM))
                    $re[] = $row;
                return $re;
            case 'valueArray':
                $re = array();
                while ($row = $result->fetch_array(MYSQLI_NUM))
                    $re = array_merge($re, $row);
                return $re;
                $re = array();
                while ($row = $result->fetch_array(MYSQLI_NUM))
                    $re = array_merge($re, $row);
                return $re;
            case 'field':
                $re = $result->fetch_array(MYSQLI_NUM);
                return $re[0];
            case 'table':
                $re = $this->table($result);
                return $re;
            default:
                if (strtolower(substr($resultType, 0, 7)) == 'column:') {
                    $column = substr($resultType, 7);
                    $re = array();
                    while ($row = $result->fetch_assoc())
                        $re[] = $row[$column];
                    return $re;
                } else {
                    var_dump($resultType);
                    $this->error('WRONG RESULT TYPE');
                    return NULL;
                }
        }
    }

    function execute($sql) {
        if(!$this->conn) {
            $this->error('NO.CONN');
            return;
        }
        $this->prepare($sql);
        $this->sqlcounter++;
        $this->executecounter++;
        $this->htmlerror = false;
        $result = $this->real_query($this->sql_query);

        /*
         * Avoid following error:
         * Error 2014: Commands out of sync; you can't run this command now
         *
         * That error may occur if a query is performed after a "OPTIMIZE TABLE"-query (during one connection)
         *
         * store_result() returns the mysqli_result of the last query and deletes it from the connection
         * Transfer result to "void":
         */
        $this->store_result();


        if ($result)
            return true;
        else {
            $this->error('QUERY FAILED');
            return false;
        }
    }

    function table(MySQLi_Result $result) {
        $re = array();
        $fields = $result->fetch_fields();
        $head = $body = '';
        $i = 0;



        while ($field = $result->fetch_field()) {
            $head .= sprintf('<th>`%s`.`%s`</th>', htmlspecialchars(utf8_encode($field->orgtable),ENT_QUOTES,'UTF-8'),htmlspecialchars(utf8_encode($field->name),ENT_QUOTES,'UTF-8'));
        }

        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $body .= '<tr>';
            foreach ($row as &$value) {
                $body .= sprintf('<td %s>%s</td>', $i % 2 == 1 ? 'style="background:GainsBoro;"' : '', htmlspecialchars(utf8_encode($value),ENT_QUOTES,'UTF-8'));
            }
            $body .= '</tr>';
            $i++;
        }

        return sprintf('<div style="font-family:sans-serif; padding:10px; margin-top:5px; color:Black; ">Query:<br /><pre>%s</pre>Ergebnis:<br /><br /><table border="1"><tr>%s</tr>%s</table><br />%d Datensätze</div>', htmlspecialchars(utf8_encode($this->sql_query)), $head, $body, $i);
    }

    function id() {
        return $this->insert_id;
    }

    function escape($txt) {
        return $this->escape_string($txt);
    }

    function likeSelect($sql) {
        $sql = strtoupper(trim($sql));
        $part = substr($sql,0,10);
        if(     strpos($part,'INSERT') !== false ||
                strpos($part,'UPDATE') !== false ||
                strpos($part,'ALTER') !== false ||
                strpos($part,'DROP') !== false ||
                strpos($part,'DELETE') !== false ||
                strpos($part,'CREATE') !== false ||
                strpos($part,'TRUNCATE') !== false)
            return false;
        return true;
    }

    function error($error_type) {
        if (!$this->showerror && $this->quitonerror) {
            exit();
        }
        switch ($error_type) {
            case 'OPEN':
                $text = 'MySQL Error: Beim Öffnen der Verbindung zur Datenbank ist ein Fehler aufgetreten';
                break;

            case 'QUERY FAILED':
                $text = sprintf('MySQL Error: Folgender Query war fehlerhaft:<br /><pre>%s</pre><b>Error %d:</b><br />%s<br />', htmlspecialchars(utf8_encode($this->sql_query),ENT_QUOTES,'UTF-8'), $this->errno, htmlspecialchars(utf8_encode($this->error),ENT_QUOTES,'UTF-8'));
                break;

            case 'WRONG RESULT TYPE':
                $text = 'MySQL Error: Der Rückgabetyp ist unbekannt';
                break;

            case 'NO.CONN':
                $text = 'MySQL Error: Es besteht keine Verbindung zu einer Datenbank';
                break;

            default:
                $text = sprintf('MySQL Error: Es ist folgender, unbekannter Fehler aufgetreten: <b>%s</b>', htmlspecialchars(utf8_encode($error_type),ENT_QUOTES,'UTF-8'));
                break;
        }
        $this->errors[] = array('text' => $text,'class' => 'error');
        $this->htmlerror = sprintf('<div style="font-family:sans-serif; padding:10px; margin-top:5px; color:White; background:LightCoral; border:5px Crimson solid;">%s</div>', $text);
        if ($this->showerror) {
            echo $this->htmlerror;
        }


        if ($this->quitonerror) {
            exit();
        }
    }

}

?>