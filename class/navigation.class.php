<?php

/**
 * class/navigation.class.php
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
 * Description of navigation
 *
 * @author cuzi@openmail.cc
 */
class navigation {

    private $xml;
    private $var = array();
    public $errors;
    public $mobileDevice = false;
    public $viewMobile = false;

    function __construct(&$errors, $file) {
        $this->errors = &$errors;

        $this->xml = new SimpleXMLElement($file, null, true);
    }

    function getBase() {
        if (isset($this->var['baseurl'])) {
            return $this->var['baseurl'];
        }
        $this->var['baseurl'] = (String) $this->xml->base;
        return $this->var['baseurl'];
    }

    function getPath() {
        if (isset($this->var['path'])) {
            return $this->var['path'];
        }
        $this->var['path'] = (String) $this->xml->path;
        return $this->var['path'];
    }

    function getShortIndex() {
        if (isset($this->var['shortindex'])) {
            return $this->var['shortindex'];
        }
        $this->var['shortindex'] = (String) $this->xml->shortindex;
        return $this->var['shortindex'];
    }

    function getHtaccess() {
        if (isset($this->var['htaccess'])) {
            return $this->var['htaccess'];
        }
        $this->var['htaccess'] = (String) $this->xml->htaccess;
        return $this->var['htaccess'];
    }

    function getURLConnector($key) {

        if (isset($this->var['htaccess'])) {
            if ($this->var['htaccess'] == $key)
                return '';
            return '?' . $key . '=';
        }
        $this->var['htaccess'] = (String) $this->xml->htaccess;
        if ($this->var['htaccess'] == $key)
            return '';
        return '?' . $key . '=';
    }

    function getTemplateDir() {
        if (isset($this->var['tpl_directory'])) {
            return $this->var['tpl_directory'];
        }
        $this->var['tpl_directory'] = (String) $this->xml->templates;
        return $this->var['tpl_directory'];
    }

    function getPHPDir() {
        if (isset($this->var['php_directory'])) {
            return $this->var['php_directory'];
        }
        $this->var['php_directory'] = (String) $this->xml->php;
        return $this->var['php_directory'];
    }

    function getFrame() {
        if (isset($this->var['frame'])) {
            return $this->var['frame'];
        }
        $this->var['frame'] = (String) $this->xml->frame;
        return $this->var['frame'];
    }

    function getPages() {
        if (isset($this->var['pages'])) {
            return $this->var['pages'];
        }

        $pages = array();
        $params = array();
        $index;
        $i = 0;
        while ($this->xml->page[$i]) {
            $attr = $this->xml->page[$i]->attributes();
            $name = (String) $attr['name'];
            $pages[$name] = array(
                'name' => $name,
                'param' => (String) $attr['param'],
                'index' => (isset($attr['index']) && $attr['index']) ? true : false,
                'group' => (isset($attr['group'])) ? $attr['group'] : 0,
                'path' => $this->getPHPDir() . (String) $this->xml->page[$i],
                'filename' => (String) $this->xml->page[$i],
                'link' => $this->getBase() . $this->getURLConnector((String) $attr['param']) . (String) $attr['name'],
                'link_get' => $this->getBase() . $this->getURLConnector((String) $attr['param']) . (String) $attr['name'] . ($this->var['htaccess']?'?':'&amp;')
                );
            isset($params[(String) $attr['param']]) ? ($params[(String) $attr['param']][] = (String) $attr['name']) : ($params[(String) $attr['param']] = array((String) $attr['name']));
            if (isset($attr['index']) && $attr['index']) {
                $index = (String) $attr['name'];
            }
            ++$i;
        }
        $this->var['pages'] = $pages;
        $this->var['params'] = $params;
        $this->var['index'] = $index;
        return $pages;
    }

    function getJSONPages() {
        if (isset($this->var['JSONpages'])) {
            return $this->var['JSONpages'];
        }

        $pages = array();
        $params = array();
        $index;
        $i = 0;
        while ($this->xml->json[$i]) {
            $attr = $this->xml->json[$i]->attributes();
            $name = (String) $attr['name'];
            $pages[$name] = array(
                'name' => $name,
                'param' => (String) $attr['param'],
                'index' => (isset($attr['index']) && $attr['index']) ? true : false,
                'group' => (isset($attr['group'])) ? $attr['group'] : 0,
                'path' => $this->getPHPDir() . (String) $this->xml->json[$i],
                'filename' => (String) $this->xml->json[$i],
                'link' => $this->getBase() . $this->getURLConnector((String) $attr['param']) . (String) $attr['name'],
                'link_get' => $this->getBase() . $this->getURLConnector((String) $attr['param']) . (String) $attr['name'] . ($this->var['htaccess']?'?':'&amp;')
                );
            isset($params[(String) $attr['param']]) ? ($params[(String) $attr['param']][] = (String) $attr['name']) : ($params[(String) $attr['param']] = array((String) $attr['name']));
            ++$i;
        }
        $this->var['JSONpages'] = $pages;
        $this->var['JSONparams'] = $params;
        return $pages;
    }

    function getRequestedPage($get, $group=0) {
        $this->getPages();

        $result = array_intersect_key($get, $this->var['params']);

        if (!$result) {
            $name = $this->var['index'];
        } else {
            $params = array_keys($result);
            $name = $result[$params[0]];
            if ($this->var['pages'][$name]['group'] > $group) {
                $name = $this->var['index'];
                $this->errors[] = '#NoAccess#';
            }
        }
        return $this->var['pages'][$name];
    }

    function getRequestedJSONPage($get, $group=0) {
        $this->getJSONPages();

        $result = array_intersect_key($get, $this->var['JSONparams']);

        if (!$result) {
            $this->errors[] = '#PageNotFound#';
            return false;
        } else {
            $params = array_keys($result);
            $name = $result[$params[0]];
            if ($this->var['JSONpages'][$name]['group'] > $group) {
                $this->errors[] = '#NoAccess#';
                return;
            }
        }
        return $this->var['JSONpages'][$name];
    }

    function isMobileDevice($useragent) {
        // found on http://detectmobilebrowser.com/
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            return true;
        }
        return false;
    }

    function getVulnerableFiles() {
        if (!$this->xml->public) {
            return array();
        }

        $i = 0;
        $public = array();
        while ($this->xml->public->file[$i]) {
            $public [] = (String) $this->xml->public->file[$i];
            ++$i;
        }


        $files = array();
        $d = dir('./');

        while (false !== ($file = $d->read())) {
            if ('.' == $file || '..' == $file || '.htaccess' == $file || in_array($file,$public)) {
                continue;
            }
            if(true == is_dir($file)) {
                $files[] = array('dir',$file);
            } else {
                $files[] = array('file',$file);
            }

        }
        $d->close();

        return $files;
    }

    function tryMobileDevice($useragent, $force=null) {
        if ($force == true) {
            $this->mobileDevice = true;
        } else if ($force === false) {
            $this->mobileDevice = false;
            $this->viewMobile = false;
            return false;
        } else {
            $this->mobileDevice = $this->isMobileDevice($useragent);
            if (!($this->mobileDevice)) {
                $this->viewMobile = false;
                return false;
            }
        }

        $this->viewMobile = true;

        // Check whether there is special frame (<mobile_frame>) for mobile devices in the XML Config.
        if (!(String) $this->xml->mobile_frame) {
            return true; // ! we might switch between mobile code in templates
        }
        // If there is one, just use it as the normal frame. NOTE: getFrame() does't overwrite $this->var['frame']
        $this->var['frame'] = (String) $this->xml->mobile_frame;
        return true;
    }

}

?>