<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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

function prepareFieldname($str){

	if(strpos($str, '_')){
		return substr_replace($str, "/", strpos($str, '_'), 1);
	} else{
		return $str;
	}
}

we_html_tools::protect();

we_html_tools::htmlTop();

print STYLESHEET;


$ignoreFields = array('ID', 'Forename', 'Surname', 'Password', 'Username', 'ParentID', 'Path', 'IsFolder', 'Icon', 'Text');
$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
$selectFields['-'] = '-';
foreach($customerTableFields as $tblField){
	if(!in_array($tblField['name'], $ignoreFields)){
		$selectFields[$tblField['name']] = $tblField['name'];
	}
}

if(!empty($_REQUEST["format"])){ //	save data in arrays ..
	$_REQUEST['classID'] = isset($_REQUEST['classID']) ? trim($_REQUEST['classID']) : '';

	$DB_WE->query("REPLACE " . ANZEIGE_PREFS_TABLE . " SET strDateiname = 'shop_pref',strFelder= '" . $DB_WE->escape($_REQUEST["waehr"]) . "|" . $DB_WE->escape($_REQUEST["mwst"]) . "|" . $DB_WE->escape($_REQUEST["format"]) . "|" . $DB_WE->escape($_REQUEST["classID"]) . "|" . $DB_WE->escape($_REQUEST["pag"]) . "'");

	$fields['customerFields'] = isset($_REQUEST['orderfields']) ? $_REQUEST['orderfields'] : array();
	$fields['orderCustomerFields'] = isset($_REQUEST['ordercustomerfields']) ? $_REQUEST['ordercustomerfields'] : array();

	// check if field exists
	$DB_WE->query("REPLACE " . ANZEIGE_PREFS_TABLE . ' SET strDateiname="edit_shop_properties", strFelder="' . $DB_WE->escape(serialize($fields)) . '"');

	$CLFields['stateField'] = isset($_REQUEST['stateField']) ? $_REQUEST['stateField'] : '-';
	$CLFields['stateFieldIsISO'] = isset($_REQUEST['stateFieldIsISO']) ? $_REQUEST['stateFieldIsISO'] : 0;
	$CLFields['languageField'] = isset($_REQUEST['languageField']) ? $_REQUEST['languageField'] : '-';
	$CLFields['languageFieldIsISO'] = isset($_REQUEST['languageFieldIsISO']) ? $_REQUEST['languageFieldIsISO'] : 0;

	// check if field exists
	$DB_WE->query("REPLACE " . ANZEIGE_PREFS_TABLE . " SET strDateiname ='shop_CountryLanguage', strFelder = '" . $DB_WE->escape(serialize($CLFields)) . "'");
	// Update Country Field in weShopVatRule
	$weShopVatRule = weShopVatRule::getShopVatRule();
	$weShopVatRule->stateField = $CLFields['stateField'];
	$weShopVatRule->stateFieldIsISO = $CLFields['stateFieldIsISO'];
	$weShopVatRule->save();
	// Update Language Field in weShopStatusMails
	$weShopStatusMails = weShopStatusMails::getShopStatusMails();
	$weShopStatusMails->LanguageData['languageField'] = $CLFields['languageField'];
	$weShopStatusMails->LanguageData['languageFieldIsISO'] = $CLFields['languageFieldIsISO'];
	$weShopStatusMails->save();

	//	Close window when finished
	echo we_html_element::jsElement('self.close();');
	exit;
} else{
	$strFelder = f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_CountryLanguage"', 'strFelder', $DB_WE);
	if($strFelder !== ''){
		$CLFields = unserialize($strFelder);
	} else{
		$CLFields['stateField'] = '-';
		$CLFields['stateFieldIsISO'] = 0;
		$CLFields['languageField'] = '-';
		$CLFields['languageFieldIsISO'] = 0;
	}
}

//	generate html-output table
$_htmlTable = new we_html_table(array('border' => 0,
		'cellpadding' => 0,
		'cellspacing' => 0,
		'width' => "410"),
		35,
		3);


//	NumberFormat - currency and taxes
$DB_WE->query("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'shop_pref'");
$DB_WE->next_record();
$feldnamen = explode("|", $DB_WE->f("strFelder"));

if(isset($feldnamen[3])){
	$fe = explode(",", $feldnamen[3]);
} else{
	$fe = array();
}
if(!isset($feldnamen[4])){
	$feldnamen[4] = "-";
}




$_row = 0;
$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[waehrung]'));
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));
$_htmlTable->setColContent($_row++, 2, we_html_tools::htmlTextInput("waehr", 6, $feldnamen[0], "", "", "text", 50));
$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));


$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont', 'valign' => 'top'), g_l('modules_shop', '[mwst]'));
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));
$_htmlTable->setCol($_row++, 2, array('class' => 'defaultfont'), we_html_tools::htmlTextInput("mwst", 6, $feldnamen[1], "", "", "text", 50) . '&nbsp;');
$_htmlTable->setCol($_row++, 0, array('colspan' => 3), we_html_tools::getPixel(5, 5));
$_htmlTable->setCol($_row++, 0, array('colspan' => 3, 'class' => 'small'), we_html_tools::htmlAlertAttentionBox(g_l('modules_shop', '[mwst_expl]'), 2, "100%", false, 100));
$_htmlTable->setCol($_row++, 0, array('colspan' => 3), we_html_tools::getPixel(20, 15));

$list = array("german" => "german", "english" => "english", "french" => "french", "swiss" => "swiss");
$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[format]'));
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));
$_htmlTable->setColContent($_row++, 2, we_html_tools::htmlSelect('format', $list, 1, $feldnamen[2]));
$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));


$pager = array("default" => "-", "5" => "5", "10" => "10", "15" => "15", "20" => "20", "25" => "25", "30" => "30", "35" => "35", "40" => "40", "45" => "45", "50" => "50");

$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[pageMod]'));
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));
$_htmlTable->setColContent($_row++, 2, we_html_tools::htmlSelect('pag', $pager, 1, $feldnamen[4]));
$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));


if(defined('OBJECT_TABLE')){

	$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont'), g_l('modules_shop', '[classID]'));
	$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));
	$_htmlTable->setColContent($_row++, 2, we_html_tools::htmlTextInput("classID", 6, (isset($feldnamen[3]) ? $feldnamen[3] : ''), "", "", "text", 50) . '<span class="small">&nbsp;' . g_l('modules_shop', '[classIDext]') . ' </span>');
	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));
} else{


	$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));
}

// look for all available fields in tblCustomer
$DB_WE->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);
$_availFields = array();

while($DB_WE->next_record()) {

	if(!in_array($DB_WE->f('Field'), $ignoreFields)){
		$_availFields[$DB_WE->f('Field')] = prepareFieldname($DB_WE->f('Field'));
	}
}
asort($_availFields);

//	get the already selected fields ...
$DB_WE->query("SELECT strFelder from " . ANZEIGE_PREFS_TABLE . " WHERE strDateiname = 'edit_shop_properties'");
$DB_WE->next_record();
$_entry = $DB_WE->f("strFelder");

// ...
if(($fields = @unserialize($_entry))){
	// we have an array with following syntax:
	// array ( 'customerFields' => array('fieldname ...',...)
	//         'orderCustomerFields' => array('fieldname', ...) )
} else{

	$fields['customerFields'] = array();
	$fields['orderCustomerFields'] = array();

	// the save format used to be ...
	// Vorname:tblWebUser||Forename,Nachname:tblWebUser||Surname,Contact/Address1:tblWebUser||Contact_Address1,Contact/Address1:tblWebUser||Contact_Address1,...
	$_fieldInfos = explode(",", $_entry);

	foreach($_fieldInfos as $_fieldInfo){

		$tmp1 = explode('||', $_fieldInfo);
		$tmp2 = explode(':', $tmp1[0]);

		$_fieldname = $tmp1[1];
		$_titel = $tmp2[0];
		$_tbl = $tmp2[1];

		if($_tbl != 'webE'){
			$fields['customerFields'][] = $_fieldname;
		}
	}
	$fields['customerFields'] = array_unique($fields['customerFields']);

	unset($_tmpEntries);
}

$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont', 'valign' => 'top'), g_l('modules_shop', '[preferences][customerFields]'));
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));
$_htmlTable->setColContent($_row++, 2, we_html_tools::htmlSelect('orderfields[]', $_availFields, (count($_availFields) > 5 ? '5' : count($_availFields)), implode(",", $fields['customerFields']), true, "", "value", 280));
$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));

$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont', 'valign' => 'top'), g_l('modules_shop', '[preferences][orderCustomerFields]'));
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));
$_htmlTable->setColContent($_row++, 2, we_html_tools::htmlSelect('ordercustomerfields[]', $_availFields, (count($_availFields) > 5 ? '5' : count($_availFields)), implode(",", $fields['orderCustomerFields']), true, "", "value", 280));
$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));

$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont', 'valign' => 'top'), g_l('modules_shop', '[preferences][CountryField]'));
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));

$countrySelect = we_class::htmlSelect('stateField', $selectFields, 1, $CLFields['stateField']);
$countrySelectISO = we_forms::checkboxWithHidden($CLFields['stateFieldIsISO'], 'stateFieldIsISO', g_l('modules_shop', '[preferences][ISO-Kodiert]'), false, "defaultfont");
$_htmlTable->setColContent($_row++, 2, $countrySelect . '<br/>' . $countrySelectISO);

$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 15));
$_htmlTable->setCol($_row, 0, array('class' => 'defaultfont', 'valign' => 'top'), g_l('modules_shop', '[preferences][LanguageField]'));
$languageSelect = we_class::htmlSelect('languageField', $selectFields, 1, $CLFields['languageField']);
$languageSelectISO = we_forms::checkboxWithHidden($CLFields['languageFieldIsISO'], 'languageFieldIsISO', g_l('modules_shop', '[preferences][ISO-Kodiert]'), false, "defaultfont");
$_htmlTable->setColContent($_row++, 2, $languageSelect . '<br/>' . $languageSelectISO);
$_htmlTable->setColContent($_row, 1, we_html_tools::getPixel(10, 5));

$_htmlTable->setCol($_row++, 0, array('colspan' => 4), we_html_tools::getPixel(20, 25));



$_buttons = we_button::position_yes_no_cancel(we_button::create_button("save", "javascript:document.we_form.submit();"), "", we_button::create_button("cancel", "javascript:self.close();")
);

$frame = we_html_tools::htmlDialogLayout($_htmlTable->getHtml(), g_l('modules_shop', '[pref]'), $_buttons);


echo we_html_element::jsElement('self.focus();') . '
	</head>
	<body class="weDialogBody">
	<form name="we_form" method="post" style="margin-left:8px; margin-top:16px;">
	' . $frame . '</form>


 	</body></html>';