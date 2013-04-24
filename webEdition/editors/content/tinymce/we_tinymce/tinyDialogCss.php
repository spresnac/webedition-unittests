<?php
/**
 * webEdition CMS
 *
 * $Rev: 5851 $
 * $Author: lukasimhof $
 * $Date: 2013-02-20 16:04:07 +0100 (Wed, 20 Feb 2013) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
header("Content-type: text/css");
?>

/* TODO: clean up this messy css! */

/* CSS for all tiny Dialogs */
body{ /* [role=application]? */
letter-spacing: normal !important;
font-family: Verdana,Arial,Helvetica,sans-serif;
font-size: 11px;
margin:0;
}

body form table{
display:block;
}

.tabs{
background: url("/webEdition/images/multiTabs/tabsBG_border.gif") repeat-x scroll 0 0 transparent;
display:block;
border: none;
height: 21px;
padding-left:0px;
}

.tabs li span{
background-image: none;
background-color: transparent;
border: none;
margin: 0;
padding: 0;
vertical-align: top;
}

.tabs li {
height: 21px;
background-image: url("/webEdition/images/multiTabs/tabsBG_normal.gif");
background-repeat: repeat-x;
border-right: 1px solid gray;
cursor: pointer;
display: inline-block;
float: left;
font-size: 17px;
line-height: 16px;
margin: 0;
padding: 0 6px 0 6px;
}

.tabs li.current {
background-image: none;
background-color:#F0F0EE;
margin:0;
}

.tabs .current span {
background-image: none;
background-color: transparent;
border: none;
margin: 0;
padding: 0;
}

form div{
color:black;
}

.title{
color:black;	
}

.panel_wrapper{
background-color: transparent;
border: none;
}

label{
color:black;
margin-right:6px;
}

legend{
color:black;
margin-top:16px;
}

body select, #block_text_indent, #box_width, #box_height, #box_padding_top, #box_padding_right, #box_padding_bottom, #box_padding_left {
height: 18px;
}

#styleSelectRow select{
width:110px;	
}

body[role=application] div#iframecontainer{
margin-top:6px 0 0 0;
margin-right:0px;
}

.panel_wrapper #class{
width:110px;	
}

textarea{
margin-left:0px;
}

body input[type="text"]{
border: 1px solid #AAAAAA;
color: black;
font-family: Verdana,Arial,Helvetica,Liberation Sans,sans-serif;
font-size: 11px;
height: 18px;
line-height: 18px;
}
/*
#colorpicker #preview{
margin-right:10px;
height:19px;
}
*/
.mceActionPanel{
display:block;
background-color:transparent;
text-align:right;
padding:0;
padding-bottom:10px;
}

.mceActionPanel #insert, .mceActionPanel #cancel, .mceActionPanel #apply, body .mceActionPanel #replaceBtn, .mceActionPanel #replaceAllBtn {
background-image: url("/webEdition/images/button/btn_normal_middle.gif");
background-position: left top;
height: 22px !important;
color: black !important;
float: none;
margin-left: 0px;
margin-right:10px;
font-weight:normal; 
}

<?php print (we_base_browserDetect::isMAC()) ? "
.mceActionPanel #replaceBtn{
position:absolute;
right: 325px;
}

.mceActionPanel #replaceAllBtn, .mceActionPanel #apply{
position:absolute;
right: 220px;
}

.mceActionPanel #insert{
position:absolute;
right: 10px;
}

.mceActionPanel #cancel{
position:absolute;
right: 115px;
}

.mceActionPanel #action{
position:absolute;
right: 230px;
}
" : "/* no special position for buttons on WIN/Linux */" ?>


.mceActionPanel #preview{
/* padding-right:0px; */
<?php
/*
print (we_base_browserDetect::isMAC()) ? "
position:absolute;
right: 125px;
" : ""
*/
?>
}

#colorpicker #previewblock{
margin-right:0; 
<?php 
/*
print (we_base_browserDetect::isMAC()) ? "
position:absolute;
right: 205px;
" : "" 
*/
?>
}

#colorpicker #preview_wrapper{
display:block;
position:absolute;
bottom:6px;
left:0px;
}

#colorpicker #preview_wrapper span{
margin-top:3px;
margin-bottom:-2px;
height:18px;
width:20px;
}

.mceActionPanel div {
display: inline;
margin-right:20px;
}

.panel_toggle_insert_span{
margin: 20px 0px 0 14px;
}

/* Some dialogs do not work not with webEDition-Footer in IE: they do not have attribute role="application" in body tag */
body[role=application] fieldset{
border:none;	
}

body[role=application] label{
margin-left:6px;
margin-right:6px;
}

body[role=application] textarea{
margin-left:6px;
}

body[role=application] .mceActionPanel{
background-image: url("/webEdition/images/edit/editfooterback.gif");
height: 30px;
position:absolute;
bottom: 0;
width: 100%;
padding: 10px 10px 0 0;
margin-left: 0;
}

body[role=application] .mceActionPanel #insert, body[role=application] .mceActionPanel #cancel, body[role=application] .mceActionPanel #apply, body[role=application] .mceActionPanel #replaceBtn, body[role=application] .mceActionPanel #replaceAllBtn {
margin: 0 10px 0 0;
}

/* Dialogs which work not with webEDition-Footer in IE get body-class weFooter when initialized by tinyMce_popup.
	=> This specially added class is not recognized by IE */
body.useWeFooter{
letter-spacing: normal !important;
font-family: Verdana,Arial,Helvetica,sans-serif;
font-size: 11px;
margin:0;
}

body.useWeFooter div{
padding-left:6px;
}

body.useWeFooter label{
margin-left:4px;
margin-right:12px;
}

body.useWeFooter div.title{
padding-top:6px;
}

body.useWeFooter textarea{
margin-left:10px;
}

body.useWeFooter .mceActionPanel{
background-image: url("/webEdition/images/edit/editfooterback.gif");
height: 30px;
position:absolute;
bottom: 0;
width: 100%;
padding: 10px 10px 0 0;
margin-left: 0;
}

body.useWeFooter .mceActionPanel #insert, body.useWeFooter .mceActionPanel #cancel, body.useWeFooter .mceActionPanel #apply, body.useWeFooter .mceActionPanel #replaceBtn, body.useWeFooter .mceActionPanel #replaceAllBtn {
margin: 0 10px 0 0;
}