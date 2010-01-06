<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty clickable hyperlinks
 *
 * Type:     modifier<br>
 * Name:     clickable_links<br>
 * Date:     March 18, 2009
 * Purpose:  converts URLs in a text to a html hyperlinks
 * Input:    text
 * Example:  {$yourtext|clickable_links}
 * @author   cuzi@openmail.cc
 * @version 1.0
 * @param string
 * @return string
 */
function smarty_modifier_clickable_links($str)
{
  $text = eregi_replace('(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)',

    '<a href="\\1">\\1</a>', $str);

  $text = eregi_replace('([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)',

    '\\1<a href="http://\\2">\\2</a>', $text);

  $text = eregi_replace('([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})',

    '<a href="mailto:\\1">\\1</a>', $text);

	return $text;
}

?>