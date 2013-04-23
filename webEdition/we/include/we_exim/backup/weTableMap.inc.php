<?php

/**
 * webEdition CMS
 *
 * $Rev: 5207 $
 * $Author: arminschulz $
 * $Date: 2012-11-22 17:08:19 +0100 (Thu, 22 Nov 2012) $
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
$tableMap = array(
	'core' => array(
		'tblfile' => FILE_TABLE,
		'tbllink' => LINK_TABLE,
		'tbllanglink' => LANGLINK_TABLE,
		'tbltemplates' => TEMPLATES_TABLE,
		'tblindex' => INDEX_TABLE,
		'tblcontent' => CONTENT_TABLE,
		'tblcategorys' => CATEGORY_TABLE,
		'tbldoctypes' => DOC_TYPES_TABLE,
		'tblthumbnails' => THUMBNAILS_TABLE,
		'tblnavigation' => NAVIGATION_TABLE,
		'tblnavigationrules' => NAVIGATION_RULE_TABLE,
		
		'tblmetadata' => METADATA_TABLE
	),
	'versions' => array(
		'tblversions' => VERSIONS_TABLE,
		'tblversionslog' => VERSIONS_TABLE_LOG
	),
	'settings' => array(
		'tblprefs' => PREFS_TABLE,
		'tblrecipients' => RECIPIENTS_TABLE,
		'tblvalidationservices' => VALIDATION_SERVICES_TABLE,
	),
	'user' => array(
		'tbluser' => USER_TABLE
	),
	'temporary' => array(
		'tbltemporarydoc' => TEMPORARY_DOC_TABLE
	),
	'history' => array(
		'tblhistory' => HISTORY_TABLE
	),
	'backup' => array(
		'tblbackup' => TBL_PREFIX . 'tblbackup'
	),
	'configuration' => array(
	),
);


if(defined('OBJECT_TABLE')){
	$tableMap['object'] = array(
		'tblobject' => OBJECT_TABLE,
		'tblobjectfiles' => OBJECT_FILES_TABLE,
		'tblobject_' => OBJECT_X_TABLE
	);
}

if(defined('CUSTOMER_TABLE')){
	$tableMap['customer'] = array(
		'tblwebuser' => CUSTOMER_TABLE,
		'tblwebadmin' => CUSTOMER_ADMIN_TABLE,
		'tblcustomerfilter' => CUSTOMER_FILTER_TABLE,
		'tblwebuserautologin' => CUSTOMER_AUTOLOGIN_TABLE
	);
}

if(defined('SHOP_TABLE')){
	$tableMap['shop'] = array(
		'tblanzeigeprefs' => ANZEIGE_PREFS_TABLE,
		'tblorders' => SHOP_TABLE,
		'tblshopvats' => WE_SHOP_VAT_TABLE
	);
}

if(defined('WORKFLOW_TABLE')){
	$tableMap['workflow'] = array(
		'tblworkflowdef' => WORKFLOW_TABLE,
		'tblworkflowstep' => WORKFLOW_STEP_TABLE,
		'tblworkflowtask' => WORKFLOW_TASK_TABLE,
		'tblworkflowdoc' => WORKFLOW_DOC_TABLE,
		'tblworkflowdocstep' => WORKFLOW_DOC_STEP_TABLE,
		'tblworkflowdoctask' => WORKFLOW_DOC_TASK_TABLE,
		'tblworkflowlog' => WORKFLOW_LOG_TABLE
	);
}

if(defined('MSG_TODO_TABLE')){
	$tableMap['todo'] = array(
		'tbltodo' => MSG_TODO_TABLE,
		'tbltodohistory' => MSG_TODOHISTORY_TABLE,
		'tblmessages' => MESSAGES_TABLE,
		'tblmsgaccounts' => MSG_ACCOUNTS_TABLE,
		'tblmsgaddrbook' => MSG_ADDRBOOK_TABLE,
		'tblmsgfolders' => MSG_FOLDERS_TABLE,
		'tblmsgsettings' => MSG_SETTINGS_TABLE
	);
}

if(defined('NEWSLETTER_TABLE')){
	$tableMap['newsletter'] = array(
		'tblnewsletter' => NEWSLETTER_TABLE,
		'tblnewslettergroup' => NEWSLETTER_GROUP_TABLE,
		'tblnewsletterblock' => NEWSLETTER_BLOCK_TABLE,
		'tblnewsletterlog' => NEWSLETTER_LOG_TABLE,
		'tblnewsletterprefs' => NEWSLETTER_PREFS_TABLE,
		'tblnewsletterconfirm' => NEWSLETTER_CONFIRM_TABLE
	);
}

if(defined('BANNER_TABLE')){
	$tableMap['banner'] = array(
		'tblbanner' => BANNER_TABLE,
		'tblbannerclicks' => BANNER_CLICKS_TABLE,
		'tblbannerprefs' => BANNER_PREFS_TABLE,
		'tblbannerviews' => BANNER_VIEWS_TABLE
	);
}

if(defined('SCHEDULE_TABLE')){
	$tableMap['schedule'] = array(
		'tblschedule' => SCHEDULE_TABLE
	);
}

if(defined('EXPORT_TABLE')){
	$tableMap['export'] = array(
		'tblexport' => EXPORT_TABLE
	);
}

if(defined('VOTING_TABLE')){
	$tableMap['voting'] = array(
		'tblvoting' => VOTING_TABLE,
		'tblvotinglog' => VOTING_LOG_TABLE
	);
}

if(defined('GLOSSARY_TABLE')){
	$tableMap['glossary'] = array(
		'tblglossary' => GLOSSARY_TABLE
	);
}