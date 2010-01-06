<?php

/**
 * class/errors.class.php
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
 * errors which are arrays are not escaped with htmlspecialchars
 * errors which are plain text are escaped with htmlspecialchars
 *
 * @author cuzi@openmail.cc
 */
class errors extends ArrayObject {

    private $xml = false;
    private $filename;
    public $standard_class;


    function __construct( $file,$standard_class='error') {
        $this->filename = $file;
        $this->standard_class = $standard_class;
    }

    function number() {
        return $this->count();
    }

    function get() {
        $arr = $this->getArrayCopy();
        if($this->checkForHashs()) {
            $strings = $this->getStrings();
            foreach($arr as &$value) {
                $nkey = is_array($value)?substr($value['text'],1,-1):substr($value,1,-1);
                if(isset($strings[$nkey])) {
                    if(!is_array($value)) {
                        $value = array('text' => htmlspecialchars($strings[$nkey]['text']),'class' => $this->standard_class);
                    }
                    else {
                        $value['text'] = $strings[$nkey]['text'];
                    }
                }
            }
        } else {
            foreach($arr as &$value) {
                if(!is_array($value)) {
                      $value = array('text' => htmlspecialchars($value),'class' => $this->standard_class);
                }
            }
        }
        return $arr;
    }

    private function checkFile() {
        if($this->xml === false) {
            $this->xml = new SimpleXMLElement($this->filename, null, true);
        }
    }

    private function checkForHashs() {
        $arr = $this->getArrayCopy();
        foreach($arr as &$value) {
            if(
                (is_array($value) && $value['text'][0] == '#' && $value['text'][strlen($value['text'])-1] == '#') ||
                (!is_array($value) && $value[0] == '#' && $value[strlen($value)-1] == '#')
            ) {
                return true;
            }
        }
        return false;
    }

    private function getStrings() {
        $this->checkFile();
        $strings = array();
        $i = 0;
        while ($this->xml->string[$i]) {
            $attr = $this->xml->string[$i]->attributes();
            $name = (String) $attr['name'];
            $strings[$name] = array(
                    'name' => $name,
                    'text' => (String) $this->xml->string[$i]);
            ++$i;
        }
        return $strings;
    }

}

?>