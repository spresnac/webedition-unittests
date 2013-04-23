<?php

/**
 * webEdition CMS
 *
 * $Rev: 5777 $
 * $Author: mokraemer $
 * $Date: 2013-02-09 19:04:18 +0100 (Sat, 09 Feb 2013) $
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
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_defines.inc.php');


/* * New Format for classes: we_Domain_specific
 * 1. ALL classes start with we_
 * 2. ALL files in one domain
 * 3. all files of one domain reside in the same directory
 * 4. if further division is needed, create a subdomain which does not contain _ !
 * 5. domains are looked up in this class
 * 6. NOTE that files MUST end with .class.php
 */

abstract class we_autoloader{

	private static $domains = array(
		'base' => 'we_classes/base',
		'database' => 'we_classes/database',
		'customer' => 'we_modules/customer',
		'helpers' => 'we_classes/helpers',
		'html' => 'we_classes/html',
		'import' => 'we_classes/import',
		'main' => 'we_classes/main',
		'messaging' => 'we_modules/messaging',
		'users' => 'we_modules/users',
		'shop' => 'we_modules/shop',
		'tag' => 'we_classes/tag',
		'workflow' => 'we_modules/workflow',
		'wysiwyg' => 'we_classes/wysiwyg',
		'xml' => 'we_classes/xml',
	);
	private static $classes = array(
		'we_classes' => array(
			'Captcha' => 'captcha/captcha.class.php',
			'CaptchaImage' => 'captcha/captchaImage.class.php',
			'CaptchaMemory' => 'captcha/captchaMemory.class.php',
			'charsetHandler' => 'charsetHandler.class.php',
			'Console_Getopt' => 'Getopt.php',
			'copyFolderFinishFrag' => 'we_copyFolderFinishFrag.class.php',
			'copyFolderFrag' => 'we_copyFolderFrag.class.php',
			'CSV' => 'csv.inc.php',
			'DB_WE' => 'database/DB_WE.inc.php', //pseudo-element which loads a wrapper, doesn't contrain a real class!
			/* DB is never autoloaded
			  'DB_WE' => 'database/we_database_mysql.class.inc.php',
			  'DB_WE' => 'database/we_database_mysqli.class.inc.php', */
			'delBackup' => 'delete/delBackup.inc.php',
			'deleteProgressDialog' => 'delete/deleteProgressDialog.inc.php',
			'delFragment' => 'delete/delFragment.inc.php',
			'HttpRequest' => 'http/HttpRequest.class.php',
			'HttpResponse' => 'http/HttpResponse.class.php',
			'Image_IPTC' => 'weMetaData/lib/PEAR_IPTC.php',
			'Image_Transform_Driver_GD' => 'Transform/Driver/GD.php',
			'Image_Transform' => 'Transform.php',
			'leWizardCollection' => 'leWizard/leWizardCollection.class.php',
			'leWizardContent' => 'leWizard/leWizardContent.class.php',
			'leWizard' => 'leWizard/leWizard.class.php',
			'leWizardProgress' => 'leWizard/leWizardProgress.class.php',
			'leWizardStatus' => 'leWizard/leWizardStatus.class.php',
			'leWizardStepBase' => 'leWizard/leWizardStepBase.class.php',
			'leWizardTemplateBase' => 'leWizard/leWizardTemplateBase.class.php',
			'listviewBase' => 'listview/listviewBase.class.php',
			'liveUpdateFunctions' => '../../../liveUpdate/classes/liveUpdateFunctions.class.php',
			'liveUpdateHttp' => 'leWizard/liveUpdateHttp.class.php',
			'liveUpdateHttpWizard' => 'leWizard/liveUpdateHttpWizard.class.php',
			'liveUpdateResponse' => 'leWizard/liveUpdateResponse.class.php',
			'liveUpdateTemplates' => 'leWizard/liveUpdateTemplates.class.php',
			'liveUpdateTemplatesWizarad' => 'leWizard/liveUpdateTemplatesWizard.class.php',
			'metadatatag' => 'listview/metadatatag.class.php',
			'myFrag' => 'taskFragment.class.php',
			'PEAR5' => 'PEAR5.php',
			'PEAR_Error' => 'PEAR.php',
			'PEAR' => 'PEAR.php',
			'permissionhandler' => 'permissionhandler/permissionhandler.class.php',
			'rebuildFragment' => 'rebuild/rebuildFragment.inc.php',
			'rndConditionPass' => 'utils/rndGenPass.inc.php',
			'Services_JSON_Error' => 'JSON.php',
			'Services_JSON' => 'JSON.php',
			'taskFragment' => 'taskFragment.class.php',
			'weAbbrDialog' => 'weAbbrDialog.class.inc.php',
			'weAcronymDialog' => 'weAcronymDialog.class.inc.php',
			'weBackup' => 'base/weBackup.class.php',
			'we_backup' => 'base/we_backup.inc.php',
			'weBackupWizard' => 'weBackupWizard.inc.php',
			'we_baseCollection' => 'html/we_baseCollection.inc.php',
			'we_baseElement' => 'html/we_baseElement.inc.php',
			'weBinary' => 'base/weBinary.class.php',
			'we_binaryDocument' => 'we_binaryDocument.inc.php',
			'we_button' => 'html/we_button.inc.php',
			'we_category' => 'we_category.inc.php',
			'we_catListview' => 'listview/we_catListview.class.php',
			'we_catSelector' => 'we_catSelector.inc.php',
			'weCellDialog' => 'weCellDialog.class.inc.php',
			'we_class' => 'we_class.inc.php',
			'we_codeConvertor' => 'we_codeConvertor.inc.php',
			'weColorDialog' => 'weColorDialog.class.inc.php',
			'we_delSelector' => 'we_delSelector.inc.php',
			'weDialog' => 'weDialog.class.inc.php',
			'we_dirSelector' => 'we_dirSelector.inc.php',
			'we_docSelector' => 'we_docSelector.inc.php',
			'we_docTypes' => 'we_docTypes.inc.php',
			'we_document' => 'we_document.inc.php',
			'we_dynamicControls' => 'html/we_dynamicControls.inc.php',
			'we_element' => 'we_element.inc.php',
			'we_errorHandling' => 'helpers/we_errorHandling.inc.php',
			'weFile' => 'base/weFile.class.php',
			'we_fileselector' => 'we_fileselector.inc.php',
			'we_flashDocument' => 'we_flashDocument.inc.php',
			'we_folder' => 'we_folder.inc.php',
			'we_forms' => 'html/we_forms.inc.php',
			'weFullscreenEditDialog' => 'weFullscreenEditDialog.class.inc.php',
			'we_history' => 'we_history.class.php',
			'we_htmlDocument' => 'we_htmlDocument.inc.php',
			'weHyperlinkDialog' => 'weHyperlinkDialog.class.inc.php',
			'we_image_crop' => 'base/we_image_crop.class.php',
			'weImageDialog' => 'weImageDialog.class.inc.php',
			'we_imageDocument' => 'we_imageDocument.inc.php',
			'we_image_edit' => 'base/we_image_edit.class.php',
			'weImportRtfDialog' => 'weImportRtfDialog.class.inc.php',
			'weJavaMenu' => 'java_menu/weJavaMenu.inc.php',
			'weLangDialog' => 'weLangDialog.class.inc.php',
			'we_langlink_listview' => 'listview/we_langlink_listview.class.php',
			'we_listview' => 'listview/we_listview.class.php',
			'weMainTree' => 'weMainTree.inc.php',
			'weMetaData_Exif' => 'weMetaData/classes/Exif.class.php',
			'weMetaData_IPTC' => 'weMetaData/classes/IPTC.class.php',
			'weMetaData_PDF' => 'weMetaData/classes/PDF.class.php',
			'weMetaData' => 'weMetaData/weMetaData.class.php',
			'weModelBase' => 'modules/weModelBase.php',
			'weModuleFrames' => 'modules/weModuleFrames.php',
			'we_multiIconBox' => 'html/we_multiIconBox.class.inc.php',
			'we_multiSelector' => 'we_multiSelector.inc.php',
			'weOrderContainer' => 'js_gui/weOrderContainer.class.php',
			'we_otherDocument' => 'we_otherDocument.inc.php',
			'we_progressBar' => 'we_progressBar.inc.php',
			'we_quicktimeDocument' => 'we_quicktimeDocument.inc.php',
			'we_rebuild' => 'rebuild/we_rebuild.class.php',
			'we_rebuild_wizard' => 'rebuild/we_rebuild_wizard.inc.php',
			'we_root' => 'we_root.inc.php',
			'we_rtf2html' => 'we_rtf2html.inc.php',
			'weRuleDialog' => 'weRuleDialog.class.inc.php',
			'we_search_listview' => 'listview/we_search_listview.class.php',
			'we_search' => 'we_search.inc.php',
			'we_SEEM' => 'SEEM/we_SEEM.class.php',
			'weSelectorQuery' => 'weSelectorQuery.class.inc.php',
			'weSidebarDocumentParser' => 'weSidebarDocumentParser.class.php',
			'weSideBarFrames' => 'weSideBarFrames.class.php',
			'weSpecialCharDialog' => 'weSpecialCharDialog.class.inc.php',
			'weSuggest' => 'weSuggest.class.inc.php',
			'weTableAdv' => 'base/weTable.class.php',
			'weTable' => 'base/weTable.class.php',
			'weTableDialog' => 'weTableDialog.class.inc.php',
			'weTableItem' => 'base/weTableItem.class.php',
			'we_tabs' => 'we_tabs.class.inc.php',
			'we_tab' => 'we_tab.class.inc.php',
			'we_template' => 'we_template.inc.php',
			'we_temporaryDocument' => 'we_temporaryDocument.inc.php',
			'we_textContentDocument' => 'we_textContentDocument.inc.php',
			'we_textDocument' => 'we_textDocument.inc.php',
			'we_thumbnail' => 'base/we_thumbnail.class.php',
			'weToolFrames' => 'tools/weToolFrames.class.php',
			'weToolLookup' => 'tools/weToolLookup.class.php',
			'weToolModel' => 'tools/weToolModel.class.php',
			'weToolTreeDataSource' => 'tools/weToolTreeDataSource.class.php',
			'weToolTree' => 'tools/weToolTree.class.php',
			'weToolView' => 'tools/weToolView.class.php',
			'weTree' => 'weTree.inc.php',
			'we_updater' => 'base/we_updater.inc.php',
			'we_util' => 'we_util.inc.php',
			'weVersion' => 'base/weVersion.class.php',
			'we_webEditionDocument' => 'we_webEditionDocument.inc.php',
			'we_widget' => 'we_widget.inc.php',
			'we_wysiwyg' => 'we_wysiwyg.class.inc.php',
			'we_xhtmlConverter' => 'helpers/we_xhtmlConverter.inc.php',
			'weXMLComposer' => 'weXMLComposer.class.php',
			'XML_Export' => 'xml_export.inc.php',
			'XML_Import' => 'xml_import.inc.php',
			'XML_Parser_Error' => 'Parser.php',
			'XML_Parser' => 'Parser.php',
			'XML_RSS' => 'RSS.php',
			'XML_SplitFile' => 'xml_splitFile.inc.php',
			'XML_Validate' => 'xml_validate.inc.php',
		),
		'we_modules' => array(
			'weBanner' => 'banner/weBanner.php',
			'weBannerBase' => 'banner/weBannerBase.php',
			'weBannerFrames' => 'banner/weBannerFrames.php',
			'weBannerView' => 'banner/weBannerView.php',
			'weModuleBannerFrames' => 'banner/weModuleBannerFrames.class.php',
			'we_bannerDirSelector' => 'banner/we_bannerDirSelector.php',
			'we_bannerSelector' => 'banner/we_bannerSelector.php',
			'we_listview_banner' => 'banner/we_listview_banner.inc.php',
			'copyWeDocumentCustomerFilterFrag' => 'customer/copyWeDocumentCustomerFilter.inc.php',
			'weAbstractCustomerFilter' => 'customer/weAbstractCustomerFilter.class.php',
			'weCustomer' => 'customer/weCustomer.php',
			'weCustomerAdd' => 'customer/weCustomerAdd.php',
			'weCustomerEI' => 'customer/weCustomerEI.php',
			'weCustomerEIWizard' => 'customer/weCustomerEIWizard.php',
			'weCustomerFilterView' => 'customer/weCustomerFilterView.class.php',
			'weCustomerFrames' => 'customer/weCustomerFrames.php',
			'weCustomerSettings' => 'customer/weCustomerSettings.php',
			'weCustomerTree' => 'customer/weCustomerTree.php',
			'weCustomerTreeLoader' => 'customer/weCustomerTreeLoader.php',
			'weCustomerView' => 'customer/weCustomerView.php',
			'weDocumentCustomerFilter' => 'customer/weDocumentCustomerFilter.class.php',
			'weDocumentCustomerFilterView' => 'customer/weDocumentCustomerFilterView.class.php',
			'weNavigationCustomerFilter' => 'customer/weNavigationCustomerFilter.class.php',
			'weNavigationCustomerFilterView' => 'customer/weNavigationCustomerFilterView.class.php',
			'we_customertag' => 'customer/we_customertag.inc.php',
			'we_listview_customer' => 'customer/we_listview_customer.class.php',
			'we_listview_onlinemonitor' => 'customer/we_listview_onlinemonitor.class.php',
			'weExport' => 'export/weExport.php',
			'weExportFrames' => 'export/weExportFrames.php',
			'weExportTreeLoader' => 'export/weExportTreeLoader.php',
			'weExportTreeMain' => 'export/weExportTreeMain.php',
			'weExportView' => 'export/weExportView.php',
			'we_exportDirSelector' => 'export/we_exportDirSelector.php',
			'weGlossary' => 'glossary/weGlossary.php',
			'weGlossaryCache' => 'glossary/weGlossaryCache.php',
			'weGlossaryFrameEditor' => 'glossary/weGlossaryFrameEditor.php',
			'weGlossaryFrameEditorDictionary' => 'glossary/weGlossaryFrameEditorDictionary.php',
			'weGlossaryFrameEditorException' => 'glossary/weGlossaryFrameEditorException.php',
			'weGlossaryFrameEditorFolder' => 'glossary/weGlossaryFrameEditorFolder.php',
			'weGlossaryFrameEditorHome' => 'glossary/weGlossaryFrameEditorHome.php',
			'weGlossaryFrameEditorItem' => 'glossary/weGlossaryFrameEditorItem.php',
			'weGlossaryFrameEditorType' => 'glossary/weGlossaryFrameEditorType.php',
			'weGlossaryFrames' => 'glossary/weGlossaryFrames.php',
			'weGlossaryReplace' => 'glossary/weGlossaryReplace.php',
			'weGlossarySearch' => 'glossary/weGlossarySearch.php',
			'weGlossarySettingControl' => 'glossary/weGlossarySettingControl.class.php',
			'weGlossarySettingFrames' => 'glossary/weGlossarySettingFrames.php',
			'weGlossaryTree' => 'glossary/weGlossaryTree.php',
			'weGlossaryTreeLoader' => 'glossary/weGlossaryTreeLoader.php',
			'weGlossaryView' => 'glossary/weGlossaryView.php',
			'we_format' => 'messaging/messaging_format.inc.php',
			'we_message' => 'messaging/we_message.inc.php',
			'we_messaging' => 'messaging/we_messaging.inc.php',
			'we_msg_email' => 'messaging/we_msg_email.inc.php',
			'we_msg_proto' => 'messaging/we_msg_proto.inc.php',
			'we_msg_update' => 'messaging/we_msg_update.inc.php',
			'we_todo' => 'messaging/we_todo.inc.php',
			'weNewsletter' => 'newsletter/weNewsletter.php',
			'weNewsletterBase' => 'newsletter/weNewsletterBase.php',
			'weNewsletterBlock' => 'newsletter/weNewsletterBlock.php',
			'weNewsletterDirSelector' => 'newsletter/weNewsletterDirSelector.inc.php',
			'weNewsletterFrames' => 'newsletter/weNewsletterFrames.php',
			'weNewsletterGroup' => 'newsletter/weNewsletterGroup.php',
			'weNewsletterTree' => 'newsletter/weNewsletterTree.php',
			'weNewsletterTreeLoader' => 'newsletter/weNewsletterTreeLoader.php',
			'weNewsletterView' => 'newsletter/weNewsletterView.php',
			'we_class_folder' => 'object/we_class_folder.inc.php',
			'we_listview_multiobject' => 'object/we_listview_multiobject.class.php',
			'we_listview_object' => 'object/we_listview_object.class.php',
			'we_object' => 'object/we_object.inc.php',
			'we_objectEx' => 'object/we_objectEx.inc.php',
			'we_objectFile' => 'object/we_objectFile.inc.php',
			'we_makenewtemplate' => 'object/we_object_createTemplate.inc.php',
			'we_objecttag' => 'object/we_objecttag.inc.php',
			'objectsearch' => 'object/we_searchobject_class.inc.php',
			'we_schedpro' => 'schedule/we_schedpro.inc.php',
			'paypal_class' => 'shop/paypal.class.php',
			'weShippingControl' => 'shop/weShippingControl.class.php',
			'weShipping' => 'shop/weShippingControl.class.php',
			'weShopStatusMails' => 'shop/weShopStatusMails.class.php',
			'weShopVariants' => 'shop/weShopVariants.inc.php',
			'weShopVatRule' => 'shop/weShopVatRule.class.php',
			'weShopVats' => 'shop/weShopVats.class.php',
			'weShopVat' => 'shop/weShopVats.class.php',
			'weShopView' => 'shop/weShopView.php',
			'shop' => 'shop/we_shop_shop.class.php',
			'Basket' => 'shop/we_shop_Basket.class.php',
			'blaettern' => 'shop/we_pager_class.inc.php',
			'we_user' => 'users/we_users.inc.php',
			'we_usersSelector' => 'users/we_usersSelector.inc.php',
			'weVoting' => 'voting/weVoting.php',
			'weVotingFrames' => 'voting/weVotingFrames.php',
			'weVotingList' => 'voting/weVotingList.php',
			'weVotingTree' => 'voting/weVotingTree.php',
			'weVotingTreeLoader' => 'voting/weVotingTreeLoader.php',
			'weVotingView' => 'voting/weVotingView.php',
			'we_votingDirSelector' => 'voting/we_votingDirSelector.php',
			'weModuleInfo' => 'weModuleInfo.class.php',
		),
		'we_hook/class' => array(
			'weHook' => 'weHook.class.php',
		),
		'validation' => array(
			'validation' => 'validation.class.php',
			'validationService' => 'validationService.class.php',
		),
		'weCodeWizard/classes' => array(
			'weCodeWizard' => 'weCodeWizard.inc.php',
			'weCodeWizardSnippet' => 'weCodeWizardSnippet.inc.php',
		),
		'weTagWizard/classes' => array(
			'weTagData' => 'weTagData.class.php',
			'weTagDataAttribute' => 'weTagDataAttribute.class.php',
			'weTagDataOption' => 'weTagDataOption.class.php',
			'weTagData_choiceAttribute' => 'weTagData_choiceAttribute.class.php',
			'weTagData_cmdAttribute' => 'weTagData_cmdAttribute.class.php',
			'weTagData_linkAttribute' => 'weTagData_linkAttribute.class.php',
			'weTagData_multiSelectorAttribute' => 'weTagData_multiSelectorAttribute.class.php',
			'weTagData_selectAttribute' => 'weTagData_selectAttribute.class.php',
			'weTagData_selectorAttribute' => 'weTagData_selectorAttribute.class.php',
			'weTagData_sqlColAttribute' => 'weTagData_sqlColAttribute.class.php',
			'weTagData_sqlRowAttribute' => 'weTagData_sqlRowAttribute.class.php',
			'weTagData_textAttribute' => 'weTagData_textAttribute.class.php',
			'weTagData_typeAttribute' => 'weTagData_typeAttribute.class.php',
			'weTagWizard' => 'weTagWizard.class.php',
		),
		'we_doclist' => array(
			'doclistView' => 'doclistView.class.inc.php',
		),
		'we_exim' => array(
			'weBackupExport' => 'backup/weBackupExport.class.php',
			'weBackupFileReader' => 'backup/weBackupFileReader.class.php',
			'weBackupImport' => 'backup/weBackupImport.class.php',
			'weBackupImportSql' => 'backup/weBackupImportSql.class.php',
			'weBackupPreparer' => 'backup/weBackupPreparer.class.php',
			'weBackupSqlFileReader' => 'backup/weBackupSqlFileReader.class.php',
			'weBackupUtil' => 'backup/weBackupUtil.class.php',
			'weContentProvider' => 'weContentProvider.class.php',
			'weExportPreparer' => 'weExportPreparer.class.php',
			'weImportUpdater' => 'weImportUpdater.class.php',
			'RefData' => 'weRefTable.class.php',
			'RefTable' => 'weRefTable.class.php',
			'weSearchPatterns' => 'weSearchPatterns.class.php',
			'weXMLBrowser' => 'weXMLBrowser.class.php',
			'weXMLExIm' => 'weXMLExIm.class.php',
			'weXMLExport' => 'weXMLExport.class.php',
			'weXMLFileReader' => 'weXMLFileReader.class.php',
			'weXMLImport' => 'weXMLImport.class.php',
			'weXMLParser' => 'weXMLParser.class.php',
			'we_thumbnailEx' => 'we_thumbnailEx.class.php',
		),
		'we_export' => array(
			'exportFunctions' => 'exportFunctions.class.inc.php',
			'weExportTree' => 'weExportTree.inc.php',
			'weExportWizard' => 'weExportWizard.inc.php',
		),
		'we_import' => array(
			'importFunctions' => 'importFunctions.class.inc.php',
			'we_wizard_import' => 'we_wiz_import.inc.php',
			'we_wizard' => 'we_wizard.inc.php',
			'weSiteImport' => 'weSiteImport.class.php',
			'siteimportFrag' => 'siteimportFrag.class.php',
			'we_import_files' => 'we_import_files.class.php',
			'weJUpload' => 'weJUpload.class.php',
		),
		'we_logging' => array(
			'logging' => 'logging.class.php',
			'versionsLog' => 'versions/versionsLog.class.php',
			'versionsLogView' => 'versions/versionsLogView.class.php',
		),
		'we_message_reporting' => array(
			'we_message_reporting' => 'we_message_reporting.class.php',
		),
		'we_tools' => array(
			'MultiDirAndTemplateChooser' => 'MultiDirAndTemplateChooser.inc.php',
			'MultiDirChooser' => 'MultiDirChooser.inc.php',
			'MultiDirChooser2' => 'MultiDirChooser2.inc.php',
			'MultiDirTemplateAndDefaultChooser' => 'MultiDirTemplateAndDefaultChooser.inc.php',
			'MultiFileChooser' => 'MultiFileChooser.inc.php',
			'ChooseDesign' => 'first_steps_wizard/DetailWizard/ChooseDesign.class.php',
			'DetermineFiles' => 'first_steps_wizard/DetailWizard/DetermineFiles.class.php',
			'DownloadFiles' => 'first_steps_wizard/DetailWizard/DownloadFiles.class.php',
			'Finish' => 'first_steps_wizard/DetailWizard/Finish.class.php',
			'ImportFiles' => 'first_steps_wizard/DetailWizard/ImportFiles.class.php',
			'ImportOptions' => 'first_steps_wizard/DetailWizard/ImportOptions.class.php',
			'PostDownloadFiles' => 'first_steps_wizard/DetailWizard/PostDownloadFiles.class.php',
			'Startscreen' => 'first_steps_wizard/DetailWizard/Startscreen.class.php',
			'ChooseDesign' => 'first_steps_wizard/MasterWizard/ChooseDesign.class.php',
			'DetermineFiles' => 'first_steps_wizard/MasterWizard/DetermineFiles.class.php',
			'DownloadFiles' => 'first_steps_wizard/MasterWizard/DownloadFiles.class.php',
			'Finish' => 'first_steps_wizard/MasterWizard/Finish.class.php',
			'ImportFiles' => 'first_steps_wizard/MasterWizard/ImportFiles.class.php',
			'ImportOptions' => 'first_steps_wizard/MasterWizard/ImportOptions.class.php',
			'Startscreen' => 'first_steps_wizard/MasterWizard/Startscreen.class.php',
			'weDynList' => 'navigation/class/weDynList.class.php',
			'weNavigation' => 'navigation/class/weNavigation.class.php',
			'weNavigationCache' => 'navigation/class/weNavigationCache.class.php',
			'weNavigationFrames' => 'navigation/class/weNavigationFrames.class.php',
			'weNavigationItem' => 'navigation/class/weNavigationItem.class.php',
			'weNavigationItems' => 'navigation/class/weNavigationItems.class.php',
			'weNavigationRule' => 'navigation/class/weNavigationRule.class.php',
			'weNavigationRuleControl' => 'navigation/class/weNavigationRuleControl.class.php',
			'weNavigationRuleFrames' => 'navigation/class/weNavigationRuleFrames.class.php',
			'weNavigationSettingControl' => 'navigation/class/weNavigationSettingControl.class.php',
			'weNavigationTree' => 'navigation/class/weNavigationTree.class.php',
			'weNavigationTreeDataSource' => 'navigation/class/weNavigationTreeDataSource.class.php',
			'weNavigationView' => 'navigation/class/weNavigationView.class.php',
			'we_navigationDirSelector' => 'navigation/class/we_navigationDirSelector.php',
			'treePopup' => 'treePopup.inc.php',
			'searchtool' => 'weSearch/class/searchtool.class.php',
			'searchtoolExp' => 'weSearch/class/searchtoolExp.class.inc.php',
			'searchtoolFrames' => 'weSearch/class/searchtoolFrames.class.php',
			'searchtoolsearch' => 'weSearch/class/searchtoolSearch.class.inc.php',
			'searchtoolTree' => 'weSearch/class/searchtoolTree.class.php',
			'searchtoolTreeDataSource' => 'weSearch/class/searchtoolTreeDataSource.class.php',
			'searchtoolView' => 'weSearch/class/searchtoolView.class.php',
		),
		'we_versions' => array(
			'versionFragment' => 'version_wizard/versionFragment.inc.php',
			'we_version' => 'version_wizard/we_version.class.php',
			'we_versions_wizard' => 'version_wizard/we_versions_wizard.inc.php',
			'weVersions' => 'weVersions.class.inc.php',
			'weVersionsSearch' => 'weVersionsSearch.class.inc.php',
			'weVersionsView' => 'weVersionsView.class.inc.php',
		),
		'we_widgets/dlg' => array(
			'weExportTree' => 'tree.inc.php',
		),
		'' => array(
			'we_linklist' => 'we_linklist.inc.php',
		),
	);

	/**
	 * default webEdition autoloader
	 * @param type $class_name
	 */
	static public function autoload($class_name){
		//no we-class
		//FIXME: this should be expected in future
		if(substr($class_name, 0, 3) === 'we_'){
			@list(, $domain) = explode('_', $class_name);
			if(!isset(self::$domains[$domain])){
				//				t_e('Error class domain not set in autoloader!');
			} else{
				if(file_exists(WE_INCLUDES_PATH . self::$domains[$domain] . '/' . $class_name . '.class.php')){
					include(WE_INCLUDES_PATH . self::$domains[$domain] . '/' . $class_name . '.class.php');
					return;
				}
			}
//			return;
		}

		foreach(self::$classes as $path => $array){
			if(array_key_exists($class_name, $array)){
				include(WE_INCLUDES_PATH . $path . '/' . $array[$class_name]);
				break;
			}
		}
		//will try next auto-loader
	}

	/**
	 * Added after Zend-Loader to trigger not found classes
	 * @param type $class_name
	 */
	static public function finalLoad($class_name){
		t_e('info', 'we_autoloader: class ' . $class_name . ' not found');
	}

}

/* todo:
'DB_WE_abstract' => 'we_db.inc.php',
*/