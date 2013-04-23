<?php

/**
 * webEdition CMS
 *
 * $Rev: 4598 $
 * $Author: mokraemer $
 * $Date: 2012-06-17 02:02:35 +0200 (Sun, 17 Jun 2012) $
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
	}
';



// initialise the vatRuleObject
if (isset($_REQUEST['we_cmd']) && $_REQUEST['we_cmd'][0] == 'saveVatRule') {

	// initialise the vatRule by request
	$weShopVatRule = weShopVatRule::initByRequest($_REQUEST);
	$weShopVatRule->save();

} else {

	$weShopVatRule = weShopVatRule::getShopVatRule();
}

// array with all rules

$customerTableFields = $DB_WE->metadata(CUSTOMER_TABLE);
foreach ($customerTableFields as $tblField) {
	$selectFields[$tblField['name']] = $tblField['name'];
}

$parts = array();

// default value f�r mwst
	$defaultInput = we_class::htmlSelect('defaultValue', array('true'=>'true', 'false' => 'false'), 1, $weShopVatRule->defaultValue);
	array_push($parts, array(
			'headline' => g_l('modules_shop','[vat_country][defaultReturn]'),
			'space' => 300,
			'html' => $defaultInput,
			'noline' => 1
		)
	);
	array_push($parts, array(
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop','[vat_country][defaultReturn_desc]'), 2,600),
			'space' => 0
		)
	);

// select field containing land
	$countrySelect = we_class::htmlSelect('stateField', $selectFields, 1, $weShopVatRule->stateField);
	$countrySelectISO = we_forms::checkboxWithHidden($weShopVatRule->stateFieldIsISO, 'stateFieldIsISO', g_l('modules_shop','[preferences][ISO-Kodiert]'),false,"defaultfont");
	array_push($parts, array(
			'headline' => g_l('modules_shop','[vat_country][stateField]'). ':',
			'space' => 300,
			'html' => $countrySelect.$countrySelectISO,
			'noline' => 1
		)
	);
	array_push($parts, array(
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop','[vat_country][stateField_desc]'), 2,600),
			'space' => 0
		)
	);


// states which must always pay vat

	$textAreaLiableStates = we_class::htmlTextArea('liableToVat', 3, 30, implode("\n", $weShopVatRule->liableToVat));

	array_push($parts, array(
			'headline' => g_l('modules_shop','[vat_country][statesLiableToVat]') . ':',
			'space' => 300,
			'html' => $textAreaLiableStates,
			'noline' => 1
		)
	);
	array_push($parts, array(
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop','[vat_country][statesLiableToVat_desc]'),2,600),
			'space' => 0
		)
	);

// states which must never pay vat

	$textAreaNotLiableStates = we_class::htmlTextArea('notLiableToVat', 3, 30, implode("\n", $weShopVatRule->notLiableToVat));

	array_push($parts, array(
			'headline' => g_l('modules_shop','[vat_country][statesNotLiableToVat]') . ':',
			'space' => 300,
			'html' => $textAreaNotLiableStates,
			'noline' => 1
		)
	);
	array_push($parts, array(
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop','[vat_country][statesNotLiableToVat_desc]'),2,600),
			'space' => 0
		)
	);


// states which must only pay under certain circumstances

	// if we make more rules possible - adjust here
	$actCondition = $weShopVatRule->conditionalRules[0];

	$conditionTextarea = we_class::htmlTextArea('conditionalStates[]', 3, 30, implode("\n", $actCondition['states']));
	$conditionField = we_class::htmlSelect('conditionalCustomerField[]', $selectFields, 1, $actCondition['customerField']);
	$conditionSelect = we_class::htmlSelect('conditionalCondition[]', array('is_empty' => g_l('modules_shop','[vat_country][condition_is_empty]'), 'is_set' => g_l('modules_shop','[vat_country][condition_is_set]')), 1, $actCondition['condition']);
	$conditionReturn = we_class::htmlSelect('conditionalReturn[]', array('false' => 'false', 'true' => 'true'), 1, $actCondition['returnValue']);

	array_push($parts, array(
			'headline' => g_l('modules_shop','[vat_country][statesSpecialRules]') .':',
			'space' => 300,
			'html' => $conditionTextarea,
			'noline' => 1
		)
	);
	array_push($parts, array(
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_shop','[vat_country][statesSpecialRules_desc]'),2,600),
			'space' => 0,
			'noline' => 1
		)
	);
	array_push($parts, array(
			'headline' => g_l('modules_shop','[vat_country][statesSpecialRules_condition]'),
			'space' => 300,
			'html' =>  $conditionField . ' ' . $conditionSelect,
			'noline' => 1
		)
	);
	array_push($parts, array(
			'headline' => g_l('modules_shop','[vat_country][statesSpecialRules_result]'),
			'space' => 300,
			'html' => $conditionReturn
		)
	);

print we_html_element::jsElement($jsFunction);


print '</head>
<body class="weDialogBody" onload="window.focus();">
	<form name="we_form" method="post">
	<input type="hidden" name="we_cmd[0]" value="saveVatRule" />
';

print we_multiIconBox::getHTML(
	'weShopCountryVat',
	"100%",
	$parts,
	30,
	we_button::position_yes_no_cancel(
		we_button::create_button('save', 'javascript:we_cmd(\'save\');'),
		'',
		we_button::create_button('cancel', 'javascript:we_cmd(\'close\');')
	),
	-1,
	'',
	'',
	false,
	g_l('modules_shop','[vat_country][box_headline]'),'',741
);


print '
	</form>
</body>
</html>';

?>