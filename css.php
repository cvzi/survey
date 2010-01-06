<?php
/**
 * css.php
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

require 'config.php';


// Navi instanzieren
$errors = array();
$navi = new navigation($errors, $navifile);
$baseurl = $navi->getBase();



$q = explode(',', $_GET['q']);

header('content-type: text/css; charset: UTF-8');
header('cache-control: must-revalidate');
$offset = 60 * 60;
$expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
header ($expire);


ob_start('compress');

function compress($buffer) {
    // remove tabs, spaces, newlines, etc.
    $buffer = str_replace(array("\n","\t"), '', $buffer);
    return $buffer;
}


?>
<?php if(in_array('general',$q)): ?>
.fl {
    float:left
}
.fr {
    float:right
}
.cl {
    clear:left
}
.cr {
    clear:right
}
.cb {
    clear:both
}


html {
    color:#000;
    background:#FFF;
}
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,button,textarea,p,blockquote,th,td {
    margin:0;
    padding:0;
}
a{
    text-decoration:none;
}
table{
    border-collapse:collapse;
    border-spacing:0;
}

fieldset,img {
    border:0;
}

address,caption,cite,code,dfn,em,strong,th,var,optgroup {
    font-style:inherit;
    font-weight:inherit;
}

del,ins{
    text-decoration:none;
}
li{
    list-style:none;
}

caption,th{
    text-align:left;
}

h1,h2,h3,h4,h5,h6{
    font-size:100%;
    font-weight:normal;
}

input,button,textarea,select,optgroup,option{
    font-family:inherit;
    font-size:inherit;
    font-style:inherit;
    font-weight:inherit;
}
input,button,textarea,select{
    font-size:100%; border:none;
}
<?php endif; ?>

<?php if (in_array('errors',$q)): ?>
.hint {
    font-family:sans-serif;
    padding:10px;
    margin-top:5px;
    color:Black;
    background:palegreen;
    border:5px #8EB5CB solid
}

.error {
    font-family:sans-serif;
    padding:10px;
    margin-top:5px;
    color:White;
    background:LightCoral;
    border:5px Crimson solid
}

.buffer {
    font-family:sans-serif;
    padding:10px;
    margin-top:5px;
    color:White;
    background:LightSkyBlue;
    border:5px DodgerBlue solid;
    white-space:pre-wrap;
}
<?php endif; ?>

<?php if (in_array('design',$q)): ?>

html {
    background:#5993b5;
    color:#4a4a4a;
    font-family:"Arial","Helvetica", sans-serif;
    font-size:11px;
}
h1 {
    margin-bottom:10px;
    color:#3d3d3d;
    font-size:14px;
    font-weight:bold;
}
h2 {
    margin-bottom:3px;
    color:#3d3d3d;
    font-size:14px;
    font-weight:bold;
}
a {
    color:#1b576f;
}
a:hover {
    text-decoration:underline;
    color:#094a6f;
}

input[type=button],input[type=submit],input[type=text],textarea {
    padding:0px 15px;
    height:30px;
    margin:0 8px;
    color:#367b98;
    font:12px arial;
    font-weight:bold;
    text-align:center;
    cursor:pointer;
    border:1px #d1d1d1 solid;
}
input[type=button]:hover,input[type=submit]:hover {
    color:#094a6f;
}
input[type=text],textarea {
    text-align:left;
    line-height:28px;
    vertical-align:middle;
}
input[type=button],input[type=submit] {
    background:#f0f0f0;
}

#wrapper {
    background:#8eb5cb;
    width:750px;
    margin:40px auto;
    padding:10px;
}
#content {
    border-top:1px #e0e0e0 solid;
    border-bottom:5px solid #115B85;
    background:#d6e4ec url(<?php echo $baseurl; ?>images/divider.gif) repeat-y  500px 100%;
}

#navigation {
    margin:-29px -1px 0 0;
    float:left;
}

#navigation ul li {
    background:#5d6b72;
    width:76px;
    height:29px;
    margin:0 5px 0;
    float:left;
}

#navigation ul li.selected {
    background:#115b85;
    font-weight:bold;
}

#navigation ul li.selected a:hover {
    text-decoration:none;
}

#navigation ul li a {
    height:16px;
    padding:6px 0;
    color:#fff;
    font-size:12px;
    text-align:center;
    display:block;
    outline:0;
}

#navigation ul li a:hover {
    text-decoration:underline;
}

#header {
    background:White no-repeat top right;
    width:100%;
    height:84px;
    border-top:5px #115b85 solid;
}

#logo {
    margin:0;
}

#logo a {
    background:url(<?php echo $baseurl; ?>images/logo.png) no-repeat top center;
    width:300px;
    height:66px;
    margin:1px 1px 0;
    text-indent:-5000px;
    display:block;
    float:left;
    outline:0;
}

#user {
    width:200px;
    height:64px;
    padding:10px 20px;
    float:right;
}

#user #welcome #message {
    font:11px arial;
    color:#9d9d9d;
    margin:5px 0 0 5px;
}

#user #welcome #message span.green {
    color:#6d9836;
    font-weight:bold;
}

#user #buttons {
    margin:10px -8px 0;
}

#left {
 float:left;
 width:450px;
 padding:20px 30px;
 font-size:14px
}

#right {
    float:right;
    width:219px;
    padding:20px 10px 5px;
}

#right h1 {
    margin:0 10px 6px;
}

#right .infobox {
    background:#e7f4fc;
    width:197px;
    margin-bottom:15px;
    padding:10px;
    border:1px #d1d1d1 solid;
}

#footer
{
    clear:both;
    background-color:#fff;
    height:30px;
    line-height:30px;
    border:#ddd 1px solid;
    margin-top:10px;
    margin-bottom:0px;
}

#footlink {
    float:right;
}

#footlink a {
    margin-right:30px;
    color:#777;
}

.inputdec {
    display:none;
}

.tool-tip {
    color:#fff;
    width:139px;
    z-index:13000;
}

.tip-top {
    font-weight:bold;
    font-size:11px;
    margin:0;
    background:url(<?php echo $baseurl; ?>images/bubble.png) top left;
}

.tip {
    background:url(<?php echo $baseurl; ?>images/bubble.png) bottom right;
    color:White;
    padding:8px 8px 4px;
}

.tip-bottom {
    background:url(<?php echo $baseurl; ?>images/bubble.png) bottom right;
}

#ask td {
    height:60px;
}

#ask tr.question td.questionholder,#ask tr.question td.selectholder {
    padding-right:10px
}
#ask tr.question td.statusholder {
    color:Crimson;
}
#ask tr.question td.statusholder.done {
    color:Green;
    font-weight:bold;
}


.chatmessage {
    border:1px solid #D1D1D1;
}

.chatmessage .headline {
    line-height:20px;
    background:#8EB5CB;
    border-bottom:2px solid #D1D1D1;
}

.chatmessage .post {
    background:#E3ECF8;
    font-size: 15px;
    padding: 5px 15px;
}

abbr[title], acronym[title] {
    border-bottom: 1px dotted;
}

.jslink {
    color:#1b576f;
}
.jslink:hover {
    text-decoration:underline;
    color:#094a6f;
}

table.stats th {
  font-weight:bolder;
}

table.stats tr.odd {
  background:White;
}
table.stats tr.even {
  background:gainsboro;
}

table.stats td {
padding:3px;
}
table.stats td li {
list-style:decimal inside;
padding:1px;
}

table.stats li.even.dark {
  background:#ededed;
}
table.stats li.odd.dark {
  background:#cbcbcb;
}

ul.square li {
  list-style-type:square;
}


<?php endif; ?>

<?php if(in_array('mobile',$q)): ?>

#wrapper {
    background:#8eb5cb;
    width:750px;
    margin:3px auto;
    padding:10px;
}


<?php endif; ?>

<?php ob_end_flush(); ?>