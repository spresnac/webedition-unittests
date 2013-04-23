<?php
/**
 * webEdition CMS
 *
 * $Rev: 5051 $
 * $Author: mokraemer $
 * $Date: 2012-11-02 21:40:23 +0100 (Fri, 02 Nov 2012) $
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

header("Content-Type: text/javascript");
?>
function weNavigationHistory() {

this.documentHistory	= new Array();
this.currentIndex		= -1;
this.saveInHistory		= true;

this.addDocToHistory = function(table, id, ct, editcmd, url, parameters) {

if (this.saveInHistory) {

if ( this.currentIndex != (this.documentHistory.length - 1) ) { // reset navigation History when needed

do {
this.documentHistory.pop();

} while ( this.currentIndex < (this.documentHistory.length - 1) );

// resave document array
var newDocumentHistory = new Array();

}

this.documentHistory.push( new weNavigationHistoryEntry( table, id, ct, editcmd, url, parameters ) );

while ( this.documentHistory.length > 50 ) {
this.documentHistory.shift();

}

this.currentIndex = (this.documentHistory.length - 1);

}
this.saveInHistory = true;

}

this.navigateBack = function() {

if (this.documentHistory.length) {

if (this.currentIndex > 0) {

this.saveInHistory = false;
this.currentIndex--;


if ( !this.documentHistory[this.currentIndex].executeHistoryEntry() ) {
this.navigateBack();
}

} else {
<?php
print we_message_reporting::getShowMessageCall(g_l('alert', '[navigation][first_document]'), we_message_reporting::WE_MESSAGE_NOTICE);
?>
}
} else {
this.getNoDocumentMessage();
}

}

this.navigateNext = function() {

if (this.documentHistory.length) {

if (this.currentIndex < (this.documentHistory.length - 1)) {

this.currentIndex++;
this.saveInHistory = false;

if ( !this.documentHistory[this.currentIndex].executeHistoryEntry() ) {
this.navigateNext();
}


} else {
<?php
print we_message_reporting::getShowMessageCall(g_l('alert', '[navigation][last_document]'), we_message_reporting::WE_MESSAGE_NOTICE);
?>
}
} else {
this.getNoDocumentMessage();
}
}

this.navigateReload = function() {

if (this.documentHistory.length) {

if ( _currentEditor = top.weEditorFrameController.getActiveEditorFrame() ) { // reload current Editor
_currentEditor.setEditorReloadAllNeeded(true);
_currentEditor.setEditorIsActive(true);

} else { // reopen current Editor
<?php
print we_message_reporting::getShowMessageCall(g_l('alert', '[navigation][no_open_document]'), we_message_reporting::WE_MESSAGE_NOTICE);
?>

// this.saveInHistory = false;
// this.documentHistory[this.currentIndex].executeHistoryEntry();

}

} else {
this.getNoDocumentMessage();
}
}

this.getNoDocumentMessage = function() {
<?php
print we_message_reporting::getShowMessageCall(g_l('alert', '[navigation][no_entry]'), we_message_reporting::WE_MESSAGE_NOTICE);
?>
}
}

function weNavigationHistoryEntry(table, id, ct, editcmd, url, parameters) {

this.table		= table;
this.id			= id;
this.ct			= ct;
this.editcmd	= editcmd;
this.url		= url;
this.parameters	= parameters;

this.executeHistoryEntry = function() {

if ( this.editcmd || (this.id && this.id != "0") ) {

top.weEditorFrameController.openDocument(
this.table,
this.id,
this.ct,
this.editcmd,
'',
this.url,
'',
'',
this.parameters
);
return true;
} else {
return false;
}
}
}

top.weNavigationHistory = new weNavigationHistory();