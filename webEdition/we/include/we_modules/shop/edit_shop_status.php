<?php

/**
 * webEdition CMS
 *
 * $Rev: 5195 $
 * $Author: mokraemer $
 * $Date: 2012-11-21 16:31:39 +0100 (Wed, 21 Nov 2012) $
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

we_html_tools::protect();
we_html_tools::htmlTop();

print STYLESHEET;

$jsFunction = '
	function we_submitForm(url){

        var f = self.document.we_form;

    	f.action = url;
    	f.method = "post";

    	f.submit();
    }

	function doUnload() {
		if (!!jsWindow_count) {
			for (i = 0; i < jsWindow_count; i++) {
				eval("jsWindow" + i + "Object.close()");
			}
		}
	}

	function we_cmd(){

		switch (arguments[0]) {

			case "close":
				window.close();
			break;

			case "save":
				we_submitForm("' . $_SERVER['SCRIPT_NAME'] . '");
			break;
		}
	}';

// initialise the shopStatusMails Object
if(isset($_REQUEST['we_cmd']) && $_REQUEST['we_cmd'][0] == 'saveShopStatusMails'){
	//p_r($_REQUEST);
	// initialise the vatRule by request
	$weShopStatusMails = weShopStatusMails::initByRequest($_REQUEST);
	$weShopStatusMails->save();
} else{

	$weShopStatusMails = weShopStatusMails::getShopStatusMails();
}

// array with all rules

$ignoreFields = array('ID', 'Forename', 'Surname', 'Password', 'Username', 'ParentID', 'Path', 'IsFolder', 'Icon', 'Text');
$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
$selectFields['-'] = '-';
foreach($customerTableFields as $tblField){
	if(!in_array($tblField['name'], $ignoreFields)){
		$selectFields[$tblField['name']] = $tblField['name'];
	}
}

$tabStatus = new we_html_table(array("border" => "0", "cellpadding" => "2", "cellspacing" => "4"), $rows_num = 5, $cols_num = 17);
$i = 0;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), g_l('modules_shop', '[statusmails][fieldname]'));

foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 120), $fieldname);
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][hidefield]'));
foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_forms::checkboxWithHidden($weShopStatusMails->FieldsHidden[$fieldname], 'FieldsHidden[' . $fieldname . ']', g_l('modules_shop', '[statusmails][hidefieldJa]'), false, "defaultfont"));
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][hidefieldCOV]'));
foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_forms::checkboxWithHidden($weShopStatusMails->FieldsHiddenCOV[$fieldname], 'FieldsHiddenCOV[' . $fieldname . ']', g_l('modules_shop', '[statusmails][hidefieldJa]'), false, "defaultfont"));
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][fieldtext]'));
foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), '<input name="FieldsText[' . $fieldname . ']" size="15" type="text" value="' . $weShopStatusMails->FieldsText[$fieldname] . '" />');
}
$i++;
$tabStatus->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][EMailssenden]'));
foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
	$tabStatus->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_class::htmlRadioButton('FieldsMails[' . $fieldname . ']', 0, ($weShopStatusMails->FieldsMails[$fieldname] == 0 ? '1' : '0'), '', g_l('modules_shop', '[statusmails][EMailssendenNein]'), 'right') . we_class::htmlRadioButton('FieldsMails[' . $fieldname . ']', 1, ($weShopStatusMails->FieldsMails[$fieldname] == 1 ? '1' : '0'), '', g_l('modules_shop', '[statusmails][EMailssendenHand]'), 'right') . we_class::htmlRadioButton('FieldsMails[' . $fieldname . ']', 2, ($weShopStatusMails->FieldsMails[$fieldname] == 2 ? '1' : '0'), '', g_l('modules_shop', '[statusmails][EMailssendenAuto]'), 'right'));
}
$parts = array(
	array(
		'headline' => g_l('modules_shop', '[statusmails][AnzeigeDaten]'),
		'space' => 100,
		'html' => '',
		'noline' => 1
	),
	array(
		'headline' => '',
		'space' => 0,
		'html' => $tabStatus->getHtml()
	),
);

$tabEMail = new we_html_table(array("border" => "0", "cellpadding" => "2", "cellspacing" => "4"), $rows_num = 4, $cols_num = 6);
$tabEMail->setCol(0, 0, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 220), g_l('modules_shop', '[statusmails][AbsenderAdresse]') .
	'<br/>' . we_class::htmlTextInput("EMailData[address]", 30, $weShopStatusMails->EMailData['address']));
$tabEMail->setCol(1, 0, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 220), g_l('modules_shop', '[statusmails][AbsenderName]') .
	'<br/>' . we_class::htmlTextInput("EMailData[name]", 30, $weShopStatusMails->EMailData['name']));
$tabEMail->setCol(2, 0, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 220), g_l('modules_shop', '[statusmails][bcc]') .
	'<br/>' . we_class::htmlTextInput("EMailData[bcc]", 30, $weShopStatusMails->EMailData['bcc']));
$tabEMail->setCol(0, 1, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 340), g_l('modules_shop', '[statusmails][EMailFeld]') .
	'<br/>' . we_class::htmlSelect('EMailData[emailField]', $selectFields, 1, $weShopStatusMails->EMailData['emailField']));
$tabEMail->setCol(1, 1, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 340), g_l('modules_shop', '[statusmails][TitelFeld]') .
	'<br/>' . we_class::htmlSelect('EMailData[titleField]', $selectFields, 1, $weShopStatusMails->EMailData['titleField']));
$tabEMail->setCol(2, 1, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 340), g_l('modules_shop', '[statusmails][DocumentSubjectField]') .
	'<br/>' . we_class::htmlTextInput("EMailData[DocumentSubjectField]", 30, $weShopStatusMails->EMailData['DocumentSubjectField']));
$tabEMail->setCol(3, 0, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 340), g_l('modules_shop', '[statusmails][DocumentAttachmentFieldA]') . '<br/>' . we_class::htmlTextInput("EMailData[DocumentAttachmentFieldA]", 30, $weShopStatusMails->EMailData['DocumentAttachmentFieldA']));
$tabEMail->setCol(3, 1, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 340), g_l('modules_shop', '[statusmails][DocumentAttachmentFieldB]') . '<br/>' . we_class::htmlTextInput("EMailData[DocumentAttachmentFieldB]", 30, $weShopStatusMails->EMailData['DocumentAttachmentFieldB']));


$parts[] = array(
	'headline' => g_l('modules_shop', '[statusmails][EMailDaten]'),
	'html' => '',
	'space' => 110,
	'noline' => 1
);
$parts[] = array(
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintEMailDaten]'), 2, 650, false),
	'space' => 0,
	'noline' => 1
);
$parts[] = array(
	'space' => 110,
	'html' => $tabEMail->getHtml(),
);

$tabSprache = new we_html_table(array("border" => "0", "cellpadding" => "2", "cellspacing" => "4"), $rows_num = 2, $cols_num = 5);
$tabSprache->setCol(0, 0, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 220), we_forms::checkboxWithHidden($weShopStatusMails->LanguageData['useLanguages'], 'LanguageData[useLanguages]', g_l('modules_shop', '[statusmails][useLanguages]'), false, "defaultfont"));
$tabSprache->setCol(0, 2, array("class" => "defaultfont", "nowrap" => "nowrap", "width" => 220), g_l('modules_shop', '[statusmails][SprachenFeld]') . we_class::htmlSelect('LanguageData[languageField]', $selectFields, 1, $weShopStatusMails->LanguageData['languageField']) . we_forms::checkboxWithHidden($weShopStatusMails->LanguageData['languageFieldIsISO'], 'LanguageData[languageFieldIsISO]', g_l('modules_shop', '[preferences][ISO-Kodiert]'), false, "defaultfont"));

$parts[] = array(
	'headline' => g_l('modules_shop', '[statusmails][Spracheinstellungen]'),
	'space' => 110,
	'html' => '',
	'noline' => 1
);
$parts[] = array(
	'space' => 0,
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintSprache]'), 2, 650, false),
	'noline' => 1
);
$parts[] = array(
	'space' => 110,
	'html' => $tabSprache->getHtml(),
	'noline' => 1
);
$parts[] = array(
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintISO]'), 2, 650, false),
	'space' => 0
);
$tabDokumente = new we_html_table(array("border" => "0", "cellpadding" => "2", "cellspacing" => "4"), $rows_num = 2, $cols_num = 17);
$i = 0;
$tabDokumente->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 110), g_l('modules_shop', '[statusmails][fieldname]'));

foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
	$tabDokumente->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap", "width" => 120), $fieldname);
}
$i++;
$tabDokumente->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('modules_shop', '[statusmails][defaultDocs]'));
foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
	$tabDokumente->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_class::htmlTextInput("FieldsDocuments[default][" . $fieldname . "]", 15, $weShopStatusMails->FieldsDocuments['default'][$fieldname]));
}

$frontendL = $GLOBALS["weFrontendLanguages"];
foreach($frontendL as $lc => &$lcvalue){
	$lccode = explode('_', $lcvalue);
	$lcvalue = $lccode[0];
}
unset($lcvalue);
foreach($frontendL as $langkey){
	$tabDokumente->addRow();
	$i++;
	$tabDokumente->setCol($i, 0, array("class" => "defaultfont", "style" => "font-weight:bold", "nowrap" => "nowrap"), g_l('languages', "[$langkey]") . ' (' . $langkey . ')');
	foreach(weShopStatusMails::$StatusFields as $fieldkey => $fieldname){
		$tabDokumente->setCol($i, $fieldkey + 1, array("class" => "defaultfont", "nowrap" => "nowrap"), we_class::htmlTextInput("FieldsDocuments[" . $langkey . "][" . $fieldname . "]", 15, $weShopStatusMails->FieldsDocuments[$langkey][$fieldname]));
	}
}

$parts[] = array(
	'headline' => g_l('modules_shop', '[statusmails][Dokumente]'),
	'space' => 100,
	'html' => '',
	'noline' => 1
);
$parts[] = array(
	'space' => 0,
	'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[statusmails][hintDokumente]'), 2, 650, false),
	'noline' => 1
);
$parts[] = array(
	'headline' => '',
	'space' => 0,
	'html' => $tabDokumente->getHtml()
);

print we_html_element::jsElement($jsFunction) .
	'</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post" >
	<input type="hidden" name="we_cmd[0]" value="saveShopStatusMails" />';

print we_multiIconBox::getHTML(
		'weShopStatusMails', "700", $parts, 30, we_button::position_yes_no_cancel(
			we_button::create_button('save', 'javascript:we_cmd(\'save\');'), '', we_button::create_button('cancel', 'javascript:we_cmd(\'close\');')
		), -1, '', '', false, g_l('modules_shop', '[statusmails][box_headline]'), '', '', 'scroll'
	);


print '</form>
</body>
</html>';