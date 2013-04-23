<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
include_once (WE_INCLUDES_PATH . 'we_widgets/mod/wePadFunctions.inc.php');

we_html_tools::protect();
/**
 * Table with the notes
 * @var string
 */
$_table = NOTEPAD_TABLE;
$_sInitProps = substr($_REQUEST['we_cmd'][0], -5);
$bSort = $_sInitProps{0};
$bDisplay = $_sInitProps{1};
$bDate = $_sInitProps{2};
$bPrio = $_sInitProps{3};
$bValid = $_sInitProps{4};
$q_Csv = isset($_REQUEST['we_cmd'][1]) ? $_REQUEST['we_cmd'][1] : '';
$_title = base64_decode($_REQUEST['we_cmd'][4]);
$_sObjId = $_REQUEST['we_cmd'][5];


switch($_REQUEST['we_cmd'][2]){
	case 'delete' :
		$_sql = 'DELETE FROM ' . $GLOBALS['DB_WE']->escape($_table) . ' WHERE ID = ' . intval($q_Csv);
		break;
	case 'update' :
		list($q_ID, $q_Title, $q_Text, $q_Priority, $q_Valid, $q_ValidFrom, $q_ValidUntil) = explode(';', $q_Csv);
		$entTitle = str_replace(array("'", '"'), array('&#039;', '&quot'), base64_decode($q_Title));
		$entText = str_replace(array("'", '"'), array('&#039;', '&quot'), base64_decode($q_Text));
		if($q_Valid == "always" || $q_Valid == "date"){
			$q_ValidUntil = "3000-01-01";
		}
		$_sql = 'UPDATE ' . $GLOBALS['DB_WE']->escape($_table) . " SET
			Title = '" . $GLOBALS['DB_WE']->escape($entTitle) . "',
			Text = '" . $GLOBALS['DB_WE']->escape($entText) . "',
			Priority = '" . $GLOBALS['DB_WE']->escape($q_Priority) . "',
			Valid = '" . $GLOBALS['DB_WE']->escape($q_Valid) . "',
			ValidFrom = '" . $GLOBALS['DB_WE']->escape($q_ValidFrom) . "',
			ValidUntil = '" . $GLOBALS['DB_WE']->escape($q_ValidUntil) . "'
			WHERE ID = " . intval($q_ID);
		break;
	case 'insert' :
		list($q_Title, $q_Text, $q_Priority, $q_Valid, $q_ValidFrom, $q_ValidUntil) = explode(';', $q_Csv);
		if($q_Valid == "always"){
			$q_ValidUntil = "3000-01-01";
			$q_ValidFrom = date("Y-m-d");
		} else
		if($q_Valid == "date"){
			$q_ValidUntil = "3000-01-01";
		}
		$entTitle = str_replace(array("'", '"'), array('&#039;', '&quot'), base64_decode($q_Title));
		$entText = str_replace(array("'", '"'), array('&#039;', '&quot'), base64_decode($q_Text));
		$_sql = "INSERT INTO " . $GLOBALS['DB_WE']->escape($_table) . " (
			WidgetName,
			UserID,
			CreationDate,
			Title,
			Text,
			Priority,
			Valid,
			ValidFrom,
			ValidUntil
		) VALUES (
			'" . ($_title) . "',
			" . intval($_SESSION['user']['ID']) . ",
			CURDATE(),
			'" . $GLOBALS['DB_WE']->escape($entTitle) . "',
			'" . $GLOBALS['DB_WE']->escape($entText) . "',
			'" . $GLOBALS['DB_WE']->escape($q_Priority) . "',
			'" . $GLOBALS['DB_WE']->escape($q_Valid) . "',
			'" . $GLOBALS['DB_WE']->escape($q_ValidFrom) . "',
			'" . $GLOBALS['DB_WE']->escape($q_ValidUntil) . "'
		)";
		break;
}

if(isset($_sql) && !empty($_sql)){
	$DB_WE->query($_sql);
}

switch($bSort){
	case 1 :
		$q_sort = 'Priority, Title';
		break;
	case 2 :
		$q_sort = 'ValidFrom, Title';
		break;
	case 3 :
		$q_sort = 'Title';
		break;
	case 4 :
		$q_sort = 'ValidUntil, Title';
		break;
	default :
		$q_sort = 'CreationDate, Title';
}

if(!$bDisplay){
	$_sql = "SELECT * FROM " . $GLOBALS['DB_WE']->escape($_table) . " WHERE
		WidgetName = '" . $GLOBALS['DB_WE']->escape($_title) . "' AND
		UserID = " . intval($_SESSION['user']['ID']) . "
		ORDER BY " . $q_sort;
} else{
	$_sql = "SELECT * FROM " . $GLOBALS['DB_WE']->escape($_table) . " WHERE
		WidgetName = '" . $GLOBALS['DB_WE']->escape($_title) . "' AND
		UserID = " . intval($_SESSION['user']['ID']) . " AND (
			Valid = 'always' OR (
				Valid = 'date' AND ValidFrom <= DATE_FORMAT(NOW(), \"%Y-%m-%d\")
			) OR (
				Valid = 'period' AND ValidFrom <= DATE_FORMAT(NOW(), \"%Y-%m-%d\") AND ValidUntil >= DATE_FORMAT(NOW(), \"%Y-%m-%d\")
			)
		)

		ORDER BY " . $q_sort;
}
// validity settings
$sctValid = we_html_tools::htmlSelect("sct_valid", array(
		g_l('cockpit', '[always]'), g_l('cockpit', '[from_date]'), g_l('cockpit', '[period]')
		), 1, g_l('cockpit', '[always]'), false, 'style="width:100px;" onChange="toggleTblValidity()"', 'value', 100, 'middlefont');
$oTblValidity = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0", "id" => "oTblValidity"
		), 1, 3);
$oTblValidity->setCol(0, 0, null, getDateSelector(g_l('cockpit', '[from]'), "f_ValidFrom", "_from"));
$oTblValidity->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
$oTblValidity->setCol(0, 2, null, getDateSelector(g_l('cockpit', '[until]'), "f_ValidUntil", "_until"));
$oTblPeriod = new we_html_table(array(
		"width" => "100%", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 1, 2);
$oTblPeriod->setCol(0, 0, array(
	"class" => "middlefont"
	), $sctValid);
$oTblPeriod->setCol(0, 1, array(
	"align" => "right"
	), $oTblValidity->getHTML());

// Edit note prio settings
$rdoPrio[0] = we_forms::radiobutton(
		$value = 0, $checked = 0, $name = "rdo_prio", $text = g_l('cockpit', '[high]'), $uniqid = true, $class = "middlefont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$rdoPrio[1] = we_forms::radiobutton(
		$value = 1, $checked = 0, $name = "rdo_prio", $text = g_l('cockpit', '[medium]'), $uniqid = true, $class = "middlefont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$rdoPrio[2] = we_forms::radiobutton(
		$value = 2, $checked = 1, $name = "rdo_prio", $text = g_l('cockpit', '[low]'), $uniqid = true, $class = "middlefont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oTblPrio = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 1, 8);
$oTblPrio->setCol(0, 0, null, $rdoPrio[0]);
$oTblPrio->setCol(
	0, 1, null, we_html_element::htmlImg(
		array(
			"src" => IMAGE_DIR . "pd/prio_high.gif",
			"width" => 13,
			"height" => 14,
			"style" => "margin-left:5px"
	)));
$oTblPrio->setCol(0, 2, null, we_html_tools::getPixel(15, 1));
$oTblPrio->setCol(0, 3, null, $rdoPrio[1]);
$oTblPrio->setCol(
	0, 4, null, we_html_element::htmlImg(
		array(
			"src" => IMAGE_DIR . "pd/prio_medium.gif",
			"width" => 13,
			"height" => 14,
			"style" => "margin-left:5px"
	)));
$oTblPrio->setCol(0, 5, null, we_html_tools::getPixel(15, 1));
$oTblPrio->setCol(0, 6, null, $rdoPrio[2]);
$oTblPrio->setCol(
	0, 7, null, we_html_element::htmlImg(
		array(
			"src" => IMAGE_DIR . "pd/prio_low.gif",
			"width" => 13,
			"height" => 14,
			"style" => "margin-left:5px"
	)));

// Edit note buttons
$delete_button = we_button::create_button("delete", "javascript:deleteNote();", false, -1, -1, "", "", true, false);
$cancel_button = we_button::create_button("cancel", "javascript:cancelNote();", false, -1, -1);
$save_button = we_button::create_button("save", "javascript:saveNote();");
$buttons = we_button::position_yes_no_cancel($delete_button, $cancel_button, $save_button);

// Edit note dialog
$oTblProps = new we_html_table(array(
		"width" => "100%", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 9, 2);
$oTblProps->setCol(0, 0, array(
	"class" => "middlefont"
	), g_l('cockpit', '[valid]') . '&nbsp;');
$oTblProps->setCol(0, 1, array(
	"colspan" => 2, "align" => "right"
	), $oTblPeriod->getHTML());
$oTblProps->setCol(1, 0, null, we_html_tools::getPixel(1, 8));
$oTblProps->setCol(2, 0, array(
	"class" => "middlefont"
	), g_l('cockpit', '[prio]'));
$oTblProps->setCol(2, 1, null, $oTblPrio->getHTML());
$oTblProps->setCol(3, 0, null, we_html_tools::getPixel(1, 8));
$oTblProps->setCol(4, 0, array(
	"class" => "middlefont"
	), g_l('cockpit', '[title]'));
$oTblProps->setCol(
	4, 1, null, we_html_tools::htmlTextInput(
		$name = "props_title", $size = 255, $value = "", $maxlength = 255, $attribs = "", $type = "text", $width = "100%", $height = 0));
$oTblProps->setCol(5, 0, null, we_html_tools::getPixel(1, 8));
$oTblProps->setCol(6, 0, array(
	"class" => "middlefont", "valign" => "top"
	), g_l('cockpit', '[note]'));
$oTblProps->setCol(
	6, 1, null, we_html_element::htmlTextArea(
		array(
		'name' => 'props_text',
		'id' => 'previewCode',
		'style' => 'width:100%;height:60px;',
		'class' => 'wetextinput',
		'onblur' => 'this.className=\'wetextinput\';',
		'onfocus' => 'this.className=\'wetextinputselected\''
		), ""));
$oTblProps->setCol(7, 0, null, we_html_tools::getPixel(1, 8));
$oTblProps->setCol(8, 0, array(
	"colspan" => 3
	), $buttons);

// Button: add note
$oTblBtnProps = new we_html_table(array(
		"width" => "100%", "cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 1, 1);
$oTblBtnProps->setCol(0, 0, array(
	"align" => "right"
	), we_button::create_button("image:btn_add_note", "javascript:displayNote();", false, -1, -1));

// Table with the note list
$oPad = new we_html_table(
		array(
			"width" => "100%",
			"cellpadding" => "0",
			"cellspacing" => "0",
			"border" => "0",
			"style" => "table-layout:fixed;"
		),
		3,
		3);
$oPad->setCol(0, 0, array(
	"width" => "6"
	), we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/pad_corner_lt.gif", "width" => 6, "height" => 4
	)));
$oPad->setCol(0, 1, array(
	"class" => "cl_notes"
	), "");
$oPad->setCol(0, 2, array(
	"width" => "6"
	), we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/pad_corner_rt.gif", "width" => 6, "height" => 4
	)));
$oPad->setCol(1, 0, array(
	"colspan" => 3, "class" => "cl_notes"
	), we_html_element::htmlDiv(array(
		"id" => "notices"
		), getNoteList($_sql, $bDate, $bDisplay)));
$oPad->setCol(2, 0, array(
	"width" => "6"
	), we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/pad_corner_lb.gif", "width" => 6, "height" => 6
	)));
$oPad->setCol(2, 1, array(
	"class" => "cl_notes"
	), "");
$oPad->setCol(2, 2, array(
	"width" => "6"
	), we_html_element::htmlImg(array(
		"src" => IMAGE_DIR . "pd/pad_corner_rb.gif", "width" => 6, "height" => 6
	)));

$_notepad = $oPad->getHTML() . we_html_element::htmlDiv(array(
		"id" => "props"
		), $oTblProps->getHTML()) . we_html_element::htmlDiv(array(
		"id" => "view"
		), $oTblBtnProps->getHTML());

$_notepad .= we_html_element::jsElement('
function toggleTblValidity(){
	var weNoteValidity = getCurrentQuery().Validity;
	if (getCurrentQuery().Validity=="always") {
		document.getElementById("f_ValidFrom_cell").style.visibility = "hidden";
		document.getElementById("f_ValidUntil_cell").style.visibility = "hidden";
	} else if(weNoteValidity=="date"){
		document.getElementById("f_ValidFrom_cell").style.visibility = "visible";
		document.getElementById("f_ValidUntil_cell").style.visibility = "hidden";
	} else {
		document.getElementById("f_ValidFrom_cell").style.visibility = "visible";
		document.getElementById("f_ValidUntil_cell").style.visibility = "visible";
	}
}
toggleTblValidity();');

print we_html_element::htmlDocType() . we_html_element::htmlHtml(
		we_html_element::htmlHead(
			we_html_tools::getHtmlInnerHead(g_l('cockpit', '[notepad]')) . STYLESHEET . we_html_element::cssElement(
				getCSS()) . we_html_element::linkElement(
				array(
					"rel" => "stylesheet",
					"type" => "text/css",
					"href" => JS_DIR . "jscalendar/skins/aqua/theme.css",
					"title" => "Aqua"
			)) . we_html_element::jsScript(JS_DIR . "jscalendar/calendar.js") .
			we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/calendar.js") .
			we_html_element::jsScript(JS_DIR . "jscalendar/calendar-setup.js") .
			we_button::create_state_changer() . we_html_element::jsElement(
				(($_REQUEST['we_cmd'][6] == "pad/pad") ? "
			var _sObjId='" . $_sObjId . "';
			var _sCls_=parent.gel(_sObjId+'_cls').value;
			var _sType='pad';
			var _sTb='" . g_l('cockpit', '[notes]') . " - " . $_title . "';
			function init(){
				parent.rpcHandleResponse(_sType,_sObjId,document.getElementById(_sType),_sTb);
			}
			" : "
			var _sObjId='m_" . $_sObjId . "';
			var _sTb='" . $_title . "';
			var _sInitProps='" . $_sInitProps . "';") . "
			var _ttlB64Esc='';
			if(typeof parent.base64_encode=='function')_ttlB64Esc=escape(parent.base64_encode(_sTb));

			function gel(id_){
				return document.getElementById?document.getElementById(id_):null;
			}

			function weEntity2char(weString){
				weString = weString.replace('&lt;','<');
				weString = weString.replace('&gt;','>');
				return weString;
			}

			function weChar2entity(weString){
				weString = weString.replace('<','&lt;');
				weString = weString.replace('>','&gt;');
				return weString;
			}

			function weEntity2char(weString){
				weString = weString.replace('&lt;','<');
				weString = weString.replace('&gt;','>');
				return weString;
			}

			function weChar2entity(weString){
				weString = weString.replace('<','&lt;');
				weString = weString.replace('>','&gt;');
				return weString;
			}

			function calendarSetup(){
				Calendar.setup({inputField:'f_ValidFrom',ifFormat:'%d.%m.%Y',button:'date_picker_from',align:'Tl',singleClick:true});
				Calendar.setup({inputField:'f_ValidUntil',ifFormat:'%d.%m.%Y',button:'date_picker_until',align:'Tl',singleClick:true});
			}

			function getCls(){
				return parent.gel(_sObjId+'_cls').value;
			}
			// displays the note dialog on click on a note
			function selectNote(id){
				var fo=document.forms[0];
				if(!isHotNote()){
					cancelNote();
					setColor(gel(id+'_tr'),id,'#EDEDED');
					fo.elements['mark'].value=id;
					populate(id);
				}
			}
			// displays the note dialog on click the add note button
			function displayNote(){
				gel('view').style.display='none';
				gel('notices').style.height='90px';
				gel('props').style.display='block';
				toggleTblValidity();
			}
			//close a open note
			function cancelNote(){
				fo=document.forms[0];
				gel('props').style.display='none';
				gel('notices').style.height='250px';
				gel('view').style.display='block';
				var oMark=fo.elements['mark'];
				var mark=oMark.value;
				if(mark!=''){
					oMark.value='';
					setColor(gel(mark+'_tr'),mark,'#FFFFFF');
				}
				unpopulate();
				switch_button_state('delete','delete_enabled','disabled');
			}
			// deletes a note
			function deleteNote(){
				var fo=document.forms[0];
				var mark=fo.elements['mark'].value;
				var q_ID=gel(mark+'_ID').value;
				parent.rpc(_ttlB64Esc.concat(','+_sInitProps),q_ID,'delete','',_ttlB64Esc,_sObjId,'pad/pad');
			}

			function isHotNote(){
				var fo=document.forms[0];
				var _id=fo.elements['mark'].value;
				var q_init;
				if(_id!='')q_init=getInitialQueryById(_id);
				else q_init={'Validity':'always','ValidFrom':'','ValidUntil':'','Priority':'low','Title':'','Text':''};
				var q_curr=getCurrentQuery();
				var idx=['Title','Text','Priority','Validity','ValidFrom','ValidUntil'];
				var idx_len=idx.length;
				for(var i=0;i<idx_len;i++){
					if(q_init[idx[i]]!=q_curr[idx[i]]) return true;
				}
				return false;
			}
			// saves a note, using the function rpc() in home.inc.php (750)
			function saveNote(){
				var fo=document.forms[0];
				var _id=fo.elements['mark'].value;
				var q_init;
				if(_id!='') q_init=getInitialQueryById(_id);
				else q_init={'Validity':'always','ValidFrom':'','ValidUntil':'','Priority':'low','Title':'','Text':''};
				var q_curr=getCurrentQuery();
				var hot=false;
				var idx=['Title','Text','Priority','Validity','ValidFrom','ValidUntil'];
				var csv='';
				var idx_len=idx.length;
				for(var i=0;i<idx_len;i++){
					if(q_init[idx[i]]!=q_curr[idx[i]])hot=true;
					csv+=(idx[i]=='Title'||idx[i]=='Text')?parent.base64_encode(q_curr[idx[i]]):q_curr[idx[i]];
					if(i<idx_len-1)csv+=';';
				}

				if(_id!=''){
					if(hot){
						// update note

						if(q_curr['Validity'] == 'period') {
							weValidFrom = q_curr['ValidFrom'].replace(/-/g, '');
							weValidUntil = q_curr['ValidUntil'].replace(/-/g, '');
							if(weValidFrom>weValidUntil) {
								" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[until_befor_from]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
								return false;
							}
						}
						if(q_curr['Title']=='') {
							" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[title_empty]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
							return false;
						}
						var q_ID=gel(_id+'_ID').value;
						parent.rpc(_ttlB64Esc.concat(','+_sInitProps),(q_ID+';'+escape(csv)),'update','',_ttlB64Esc,_sObjId,'pad/pad',escape(q_curr['Title']),escape(q_curr['Text']));
					}else{
						" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[note_not_modified]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
					}
				}else{
					if(hot){
						// insert note
						if(q_curr['Validity'] == 'period') {
							weValidFrom = q_curr['ValidFrom'].replace(/-/g, '');
							weValidUntil = q_curr['ValidUntil'].replace(/-/g, '');
							if(weValidFrom>weValidUntil) {
								" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[until_befor_from]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
								return false;
							} else if(!weValidFrom || !weValidUntil) {
								" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[date_empty]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
								return false;
							}
						} else if(q_curr['Validity'] == 'date' && !q_curr['ValidFrom']){
								" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[date_empty]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
								return false;
						}
						if(q_curr['Title']=='') {
							" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[title_empty]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
							return false;
						}
						parent.rpc(_ttlB64Esc.concat(','+_sInitProps),escape(csv),'insert','',_ttlB64Esc,_sObjId,'pad/pad',escape(q_curr['Title']),escape(q_curr['Text']));
					}else{
						" . we_message_reporting::getShowMessageCall(
					g_l('cockpit', '[title_empty]'), we_message_reporting::WE_MESSAGE_NOTICE) . "
					}
				}
			}

			function getInitialQueryById(id){
				return asoc={
					'Validity':gel(id+'_Valid').value,
					'ValidFrom':gel(id+'_ValidFrom').value,
					'ValidUntil':gel(id+'_ValidUntil').value,
					'Priority':gel(id+'_Priority').value,
					'Title':gel(id+'_Title').value,
					'Text':gel(id+'_Text').value
				};
			}

			function getCurrentQuery(){
				var fo=document.forms[0];
				var oSctValid=fo.elements['sct_valid'];
				var validSel=oSctValid.options[oSctValid.selectedIndex].value;
				var oRdoPrio=fo.elements['rdo_prio'];
				var sValidFrom=fo.elements['f_ValidFrom'].value;
				var sValidUntil=fo.elements['f_ValidUntil'].value;
				return asoc={
					'Validity':(validSel==0)?'always':((validSel==1)?'date':'period'),
					'ValidFrom':convertDate(sValidFrom,'%Y-%m-%d'),
					'ValidUntil':convertDate(sValidUntil,'%Y-%m-%d'),
					'Priority':(oRdoPrio[0].checked)?'high':(oRdoPrio[1].checked)?'medium':'low',
					'Title':fo.elements['props_title'].value,
					'Text':fo.elements['props_text'].value
				};
			}

			function populate(r){
				fo=document.forms[0];
				var sValidity=gel(r+'_Valid').value;
				var sValidityIndex = sValidity == 'always' ? 0 : (sValidity == 'date' ? 1 : 2);
				var oSctValid=fo.elements['sct_valid'];
				var iSctValidLen=oSctValid.length;
				for(var i=iSctValidLen-1;i>=0;i--){
					if(oSctValid.options[i].value==sValidityIndex){
						oSctValid.options[i].selected=true;
						break;
					}
				}
				toggleTblValidity();
				fo.elements['f_ValidFrom'].value=convertDate(gel(r+'_ValidFrom').value,'%d.%m.%Y');
				fo.elements['f_ValidUntil'].value=convertDate(gel(r+'_ValidUntil').value,'%d.%m.%Y');
				var prio=gel(r+'_Priority').value;
				fo.elements['rdo_prio'][prio=='high'?0:prio=='medium'?1:2].checked=true;
				fo.elements['props_title'].value=gel(r+'_Title').value;
				fo.elements['props_text'].value=gel(r+'_Text').value;
				switch_button_state('delete','delete_enabled','enabled');
				displayNote();
			}

			function unpopulate(){
				fo=document.forms[0];
				var oSctValid=fo.elements['sct_valid'];
				oSctValid.options[0].selected=true;
				fo.elements['f_ValidFrom'].value='';
				fo.elements['f_ValidUntil'].value='';
				fo.elements['rdo_prio'][2].checked=true;
				fo.elements['props_title'].value='';
				fo.elements['props_text'].value='';
			}

			function setColor(theRow,theRowNum,newColor){
				fo=document.forms[0];
				var theCells=null;
				if(fo.elements['mark'].value!=''||typeof(theRow.style)=='undefined'){
					return false;
				}
				if(typeof(document.getElementsByTagName)!='undefined'){
					theCells=theRow.getElementsByTagName('td');
				}
				else if(typeof(theRow.cells)!='undefined'){
					theCells=theRow.cells;
				}else{
					return false;
				}
				var rowCellsCnt=theCells.length;
				var domDetect=null;
				if(typeof(window.opera)=='undefined'&&typeof(theCells[0].getAttribute)!='undefined'){
					domDetect=true;
				}else{
					domDetect=false;
				}
				var c=null;
				if(domDetect){
					for(c=0;c<rowCellsCnt;c++){
						theCells[c].setAttribute('bgcolor',newColor,0);
					}
				}else{
					for(c=0;c<rowCellsCnt;c++){
						theCells[c].style.backgroundColor=newColor;
					}
				}
				return true;
			}

			function convertDate(sDate,sFormat){
				var fixedImplode='';
				var arr=sDate.split((sFormat=='%Y-%m-%d')?'.':'-')
				separator=(sFormat=='%Y-%m-%d')?'-':'.';
				for(var x=arr.length-1;x>=0;x--){
					fixedImplode+=(separator+String(arr[x]));
				}
				fixedImplode=fixedImplode.substring(separator.length,fixedImplode.length);
				return fixedImplode;
			}
		")) . we_html_element::htmlBody(
			array(
			"marginwidth" => "0",
			"marginheight" => "0",
			"leftmargin" => "0",
			"topmargin" => "0",
			"onload" => (($_REQUEST['we_cmd'][6] == "pad/pad") ? "if(parent!=self)init();" : "")
			), we_html_element::htmlForm(
				array(
				"style" => "display:inline;"
				), we_html_element::htmlDiv(
					array(
					"id" => "pad"
					), $_notepad . we_html_element::htmlHidden(
						array(
							"name" => "mark", "value" => ""
					)) . we_html_element::jsElement("calendarSetup();")))));
