<?php

/**
 * webEdition CMS
 *
 * $Rev: 5691 $
 * $Author: mokraemer $
 * $Date: 2013-01-31 21:42:24 +0100 (Thu, 31 Jan 2013) $
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
$we_EDITOR = true;

we_html_tools::protect();
// prevent persmissions overriding
$perms = $_SESSION['perms'];
// init document


$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : '';

include(WE_INCLUDES_PATH . '/we_editors/we_init_doc.inc.php');

$_insertReloadFooter = '';
$wasNew = 0;

switch($_REQUEST['we_cmd'][0]){
	case 'load_editor':
		// set default tab for creating new imageDocuments to "metadata":
		if($we_doc->ContentType == 'image/*' && $we_doc->ID == 0){
			$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_CONTENT;
			$we_doc->EditPageNr = WE_EDITPAGE_CONTENT;
			$_REQUEST['we_cmd'][1] = WE_EDITPAGE_CONTENT;
		}
		break;
	case 'resizeImage':
		$we_doc->resizeImage($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][3]);
		break;
	case 'rotateImage':
		$we_doc->rotateImage($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
		break;
	case 'del_thumb':
		$we_doc->del_thumbnails($_REQUEST['we_cmd'][1]);
		break;
	case 'do_add_thumbnails':
		$we_doc->add_thumbnails($_REQUEST['we_cmd'][1]);
		break;
	case 'copyDocument':
		$we_doc->copyDoc($_REQUEST['we_cmd'][1]);
		$we_doc->InWebEdition = 1;
		break;
	case 'new_alias':
		$we_doc->newAlias();
		break;
	case 'delete_alias':
		$we_doc->deleteAlias($_REQUEST['we_cmd'][1]);
		break;
	case 'delete_list':
		$we_doc->removeEntryFromList($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
		break;
	case 'insert_entry_at_list':
		$we_doc->insertEntryAtList($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], isset($_REQUEST['we_cmd'][3]) ? $_REQUEST['we_cmd'][3] : 1);
		break;
	case 'up_entry_at_list':
		$we_doc->upEntryAtList($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'down_entry_at_list':
		$we_doc->downEntryAtList($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'up_link_at_list':
		$we_doc->upEntryAtLinklist($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'down_link_at_list':
		$we_doc->downEntryAtLinklist($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'add_entry_to_list':
		$we_doc->addEntryToList($_REQUEST['we_cmd'][1], isset($_REQUEST['we_cmd'][2]) ? $_REQUEST['we_cmd'][2] : 1);
		break;
	case 'add_link_to_linklist':
		$GLOBALS['we_list_inserted'] = $_REQUEST['we_cmd'][1];
		$we_doc->addLinkToLinklist($_REQUEST['we_cmd'][1]);
		break;
	case 'delete_linklist':
		$we_doc->removeLinkFromLinklist($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][3]);
		break;
	case 'insert_link_at_linklist':
		$GLOBALS['we_list_insertedNr'] = $_REQUEST['we_cmd'][2];
		$GLOBALS['we_list_inserted'] = $_REQUEST['we_cmd'][1];
		$we_doc->insertLinkAtLinklist($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'change_linklist':
		$we_doc->changeLinklist($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'change_link':
		$we_doc->changeLink($_REQUEST['we_cmd'][1]);
		break;
	case 'doctype_changed':
		$we_doc->changeDoctype('', true);
		$_insertReloadFooter = we_html_element::jsElement('try{parent.editFooter.location.reload();parent.editHeader.location.reload();}catch(exception){};');
		break;
	case 'template_changed':
		$we_doc->changeTemplate();
		$_insertReloadFooter = we_html_element::jsElement('try{parent.editFooter.location.reload();parent.editHeader.location.reload();}catch(exception){};');
		break;
	case 'remove_image':
		$we_doc->remove_image($_REQUEST['we_cmd'][1]);
		break;
	case 'wrap_on_off':
		$_SESSION['we_wrapcheck'] = ($_REQUEST['we_cmd'][1] == 'true') ? 1 : 0;
		$we_doc->EditPageNr = WE_EDITPAGE_CONTENT;
		$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_CONTENT;
		break;
	case 'add_owner':
		$we_doc->add_owner($_REQUEST['we_cmd'][1]);
		break;
	case 'del_owner':
		$we_doc->del_owner($_REQUEST['we_cmd'][1]);
		break;
	case 'add_user':
		$we_doc->add_user($_REQUEST['we_cmd'][1]);
		break;
	case 'del_user':
		$we_doc->del_user($_REQUEST['we_cmd'][1]);
		break;
	case 'del_all_owners':
		$we_doc->del_all_owners();
		break;

	case 'applyWeDocumentCustomerFilterFromFolder':
		$we_doc->applyWeDocumentCustomerFilterFromFolder();
		break;

	case 'restore_defaults':
		$we_doc->restoreDefaults();
		break;

	case 'add_workspace':
		$we_doc->add_workspace($_REQUEST['we_cmd'][1]);
		break;
	case 'del_workspace':
		$we_doc->del_workspace($_REQUEST['we_cmd'][1]);
		break;
	case 'add_extraworkspace':
		$we_doc->add_extraWorkspace($_REQUEST['we_cmd'][1]);
		break;
	case 'del_extraworkspace':
		$we_doc->del_extraWorkspace($_REQUEST['we_cmd'][1]);
		break;
	case 'ws_from_class':
		$we_doc->ws_from_class();
		break;
	case 'switch_edit_page':
		$_SESSION['weS']['EditPageNr'] = $_REQUEST['we_cmd'][1];
		$we_doc->EditPageNr = $_REQUEST['we_cmd'][1];
		if($_SESSION['weS']['we_mode'] == 'seem'){
			$_insertReloadFooter = we_html_element::jsElement('try{parent.editFooter.location.reload();}catch(exception){};') . SCRIPT_BUTTONS_ONLY . STYLESHEET_BUTTONS_ONLY;
		}
		break;
	case 'delete_link':
		if(isset($we_doc->elements[$_REQUEST['we_cmd'][1]])){
			unset($we_doc->elements[$_REQUEST['we_cmd'][1]]);
		}
		break;
	case 'add_cat':
		$we_doc->addCat($_REQUEST['we_cmd'][1]);
		break;
	case 'delete_cat':
		$we_doc->delCat($_REQUEST['we_cmd'][1]);
		break;
	case 'changeTempl_ob':
		$we_doc->changeTempl_ob($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'delete_all_cats':
		$we_doc->Category = '';
		break;
	case 'add_schedule':
		$we_doc->add_schedule();
		break;
	case 'del_schedule':
		$we_doc->del_schedule($_REQUEST['we_cmd'][1]);
		break;
	case 'delete_schedcat':
		$we_doc->delete_schedcat($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'delete_all_schedcats':
		$we_doc->schedArr[$_REQUEST['we_cmd'][1]]['CategoryIDs'] = '';
		break;
	case 'add_schedcat':
		$we_doc->add_schedcat($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2]);
		break;
	case 'doImage_convertGIF':
		$we_doc->convert('gif');
		break;
	case 'doImage_convertPNG':
		$we_doc->convert('png');
		break;
	case 'doImage_convertJPEG':
		$we_doc->convert('jpg', $_REQUEST['we_cmd'][1]);
		break;
	case 'doImage_crop':
		$filename = TEMP_PATH . '/' . weFile::getUniqueId();
		copy($we_doc->getElement('data'), $filename);


		//$filename = weFile::saveTemp($we_doc->getElement('data'));

		$x = $_REQUEST['we_cmd'][1];
		$y = $_REQUEST['we_cmd'][2];
		$width = $_REQUEST['we_cmd'][3];
		$height = $_REQUEST['we_cmd'][4];

		$img = Image_Transform::factory('GD');
		if(PEAR::isError($stat = $img->load($filename))){
			trigger_error($stat->getMessage() . ' Filename: ' . $filename);
		}
		if(PEAR::isError($stat = $img->crop($width, $height, $x, $y))){
			trigger_error($stat->getMessage() . ' Filename: ' . $filename);
		}
		if(PEAR::isError($stat = $img->save($filename))){
			trigger_error($stat->getMessage() . ' Filename: ' . $filename);
		}

		$we_doc->setElement('data', $filename);
		$we_doc->setElement('width', $width, 'attrib');
		$we_doc->setElement('origwidth', $width, 'attrib');
		$we_doc->setElement('height', $height, 'attrib');
		$we_doc->setElement('origheight', $height, 'attrib');
		$we_doc->DocChanged = true;
		break;
	case 'add_css':
		$we_doc->add_css($_REQUEST['we_cmd'][1]);
		break;
	case 'del_css':
		$we_doc->del_css($_REQUEST['we_cmd'][1]);
		break;
	case 'add_navi':
		$we_doc->addNavi($_REQUEST['we_cmd'][1], $_REQUEST['we_cmd'][2], $_REQUEST['we_cmd'][3], $_REQUEST['we_cmd'][4]);
		break;
	case 'delete_navi':
		$we_doc->delNavi($_REQUEST['we_cmd'][1]);
		break;
	case 'delete_all_navi':
		$we_doc->delAllNavi();
		break;
	case 'revert_published':
		$we_doc->revert_published();
		break;
}

//	if document is locked - only Preview mode is possible. otherwise show warning.
$_userID = $we_doc->isLockedByUser();
if($_userID != 0 && $_userID != $_SESSION['user']['ID'] && $we_doc->ID){ // document is locked
	if(in_array(WE_EDITPAGE_PREVIEW, $we_doc->EditPageNrs)){
		$we_doc->EditPageNr = WE_EDITPAGE_PREVIEW;
		$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PREVIEW;
	} else{
		include_once(WE_USERS_MODULE_PATH . 'we_users_lockmessage.inc.php');
		exit;
	}
} elseif($_userID != $_SESSION['user']['ID'] && $_SESSION['weS']['we_mode'] == 'seem' && $we_doc->EditPageNr != WE_EDITPAGE_PREVIEW){
	// lock document, if in seeMode and EditMode !!, don't lock when already locked
	$we_doc->lockDocument();
}


/*
 * if the document is a webEdition document, we save it with a temp-name (path of document+extension) and redirect
 * to the tmp-location. This is done for the content- and preview-editpage.
 * With html-documents this is only done for preview-editpage.
 * We need to do this, because, when the pages has for example jsp. content, it will be parsed right!
 * This is only done when the IsDynamic - PersistantSlot is false.
 */
if((($_REQUEST['we_cmd'][0] != 'save_document' && $_REQUEST['we_cmd'][0] != 'publish' && $_REQUEST['we_cmd'][0] != 'unpublish') && (($we_doc->ContentType == 'text/webedition') && ($we_doc->EditPageNr == WE_EDITPAGE_PREVIEW || $we_doc->EditPageNr == WE_EDITPAGE_CONTENT )) || ($we_doc->ContentType == 'text/html' && $we_doc->EditPageNr == WE_EDITPAGE_PREVIEW && $_REQUEST['we_cmd'][0] != 'save_document')) && (!$we_doc->IsDynamic)){
	$we_include = $we_doc->editor();
	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
	ob_start();
	if(substr(strtolower($we_include), 0, strlen($_SERVER['DOCUMENT_ROOT'])) == strtolower($_SERVER['DOCUMENT_ROOT'])){
		include($we_include);
	} else{
		include(WE_INCLUDES_PATH . $we_include);
	}
	$contents = ob_get_contents();
	ob_end_clean();
	//  SEEM the file
	//  but only, if we are not in the template-editor
	if($we_doc->ContentType != 'text/weTmpl'){
		$contents = we_SEEM::parseDocument($contents);

		if(strpos($contents, '</head>')){
			$contents = str_replace('</head>', $_insertReloadFooter . '</head>', $contents);
		} else{
			$contents = $_insertReloadFooter . $contents;
		}
	}
	/*
	  $we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
	 */
	$we_ext = ($we_doc->Extension == '.js' || $we_doc->Extension == '.css' || $we_doc->Extension == '.wml' || $we_doc->Extension == '.xml') ? '.html' : $we_doc->Extension;
	//FIXME: php temporary file?
	$tempName = str_replace('\\', '/', dirname($we_doc->getSitePath()) . '/' . session_id() . $we_ext);
	we_util_File::insertIntoCleanUp($tempName, time());
	$cf = array();

	$parent = str_replace('\\', '/', dirname($tempName));

	while(!we_util_File::checkAndMakeFolder($parent)) {
		$cf[] = $parent;
		$parent = str_replace('\\', '/', dirname($parent));
	}

	// url of document !!
	srand((double) microtime() * 1000000);
	$r = rand();
	$_url = str_replace('\\', '/', dirname($we_doc->getHttpSitePath()));

	$contents = str_replace('<?xml', '<?php print "<?xml"; ?>', $contents);

	ob_start();
	eval('?>' . $contents);
	$contents = ob_get_contents();
	ob_end_clean();

	//
	// --> Glossary Replacement
	//

	if(defined('GLOSSARY_TABLE') && ((isset($GLOBALS['we_editmode']) && !$GLOBALS['we_editmode']) || !isset($GLOBALS['we_editmode']))){
		if(isset($we_doc->InGlossar) && $we_doc->InGlossar == 0){
			$contents = weGlossaryReplace::replace($contents, $we_doc->Language);
		}
	}


	we_util_File::saveFile($tempName, $contents);

	//  we need to add the parameters at the urls
	//  we_cmds are deleted.
	//  in which case??
	//	parastr isn't greater than 255 letters.
	$parastr = we_SEEM::arrayToParameters($_REQUEST, '', array('we_cmd'));

	// When the url is too long, this will not work anymore - therefore we cut the string.
	// we don't need this anymore? check seeMode
//    $_url = $_url . "/" . session_id() . $we_ext . "?r=" . $r . $parastr;
//    $_url = strlen($_url) > 255 ? substr($_url,0,240) : $_url;

	header('Location: ' . $_url . '/' . session_id() . $we_ext . '?r=' . $r);
} else{
	$we_JavaScript = '';
	switch($_REQUEST['we_cmd'][0]){
		case 'save_document':
			if(!$we_doc->ContentType){
				exit(' ContentType Missing !!! ');
			}
			$saveTemplate = true;
			if($we_doc->i_pathNotValid()){
				$we_responseText = sprintf(g_l('weClass', '[notValidFolder]'), $we_doc->Path);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_filenameEmpty()){
				$we_responseText = g_l('weEditor', '[' . $we_doc->ContentType . '][filename_empty]');
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
				$saveTemplate = false;
			} else if(!$we_doc->i_canSaveDirinDir()){
				$we_responseText = g_l('weEditor', '[pfolder_notsave]');
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_sameAsParent()){
				$we_responseText = g_l('weEditor', '[folder_save_nok_parent_same]');
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_fileExtensionNotValid()){
				$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][we_filename_notValid]'), $we_doc->Path);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_filenameNotValid()){
				$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][we_filename_notValid]'), $we_doc->Path);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_descriptionMissing()){
				$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][we_description_missing]'), $we_doc->Path);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_filenameNotAllowed()){
				$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][we_filename_notAllowed]'), $we_doc->Path);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_filenameDouble()){
				$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_path_exists]'), $we_doc->Path);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_urlDouble()){
				$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][we_objecturl_exists]'), $we_doc->Url);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if(!$we_doc->i_checkPathDiffAndCreate()){
				$we_responseText = sprintf(g_l('weClass', '[notValidFolder]'), $we_doc->Url);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if(($n = $we_doc->i_check_requiredFields())){
				$we_responseText = sprintf(g_l('weEditor', '[required_field_alert]'), $n);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if($we_doc->i_scheduleToBeforeNow()){
				$we_responseText = g_l('modules_schedule', '[toBeforeNow]');
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if(($n = $we_doc->i_hasDoubbleFieldNames())){
				$we_responseText = sprintf(g_l('weEditor', '[doubble_field_alert]'), $n);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else if(!$we_doc->i_areVariantNamesValid()){
				$we_responseText = g_l('weEditor', '[variantNameInvalid]');
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			} else{
				$we_JavaScript = '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');'; // save/ rename a document
				if($we_doc->ContentType == 'text/weTmpl'){
					if(isset($_REQUEST['we_cmd'][8]) && $_REQUEST['we_cmd'][8]){
						// if  we_cmd[8] is set, it means that 'automatic rebuild' was clicked
						// so we need to check we_cmd[3] (means save immediately) and we_cmd[4] (means rebuild immediately)
						$_REQUEST['we_cmd'][3] = 1;
						$_REQUEST['we_cmd'][4] = 1;
					}
					if($_REQUEST['we_cmd'][5]){ //Save in version
						$_REQUEST['we_cmd'][5] = '';
						$we_doc->we_publish();
					}


####TEMPLATE_SAVE_CODE2_START###
					$TEMPLATE_SAVE_CODE2 = true;
					$arr = we_rebuild::getTemplAndDocIDsOfTemplate($we_doc->ID, true, true);
					$nrDocsUsedByThisTemplate = count($arr['documentIDs']);
					$nrTemplatesUsedByThisTemplate = count($arr['templateIDs']);
					$somethingNeedsToBeResaved = ($nrDocsUsedByThisTemplate + $nrTemplatesUsedByThisTemplate) > 0;

					if($_REQUEST['we_cmd'][2]){
						//this is the second call to save_document (see next else command)
						include(WE_INCLUDES_PATH . 'we_templates/we_template_save_question.inc.php'); // this includes the gui for the save question dialog
						$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
						exit();
					} else if(!$_REQUEST['we_cmd'][3] && $somethingNeedsToBeResaved){
						// this happens when the template is saved and there are documents which use the template and "automatic rebuild" is not checked!
						include(WE_INCLUDES_PATH . 'we_TemplateSave.inc.php'); // this calls again we_cmd with save_document and sets we_cmd[2]
						$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
						exit();
					} else{
						//this happens when we_cmd[3] is set and not we_cmd[2]
						$oldID = $we_doc->ID;
						if($we_doc->we_save()){
							if($oldID == 0){
								$we_doc->lockDocument();
							}
							$wasSaved = true;
							$wasNew = (intval($we_doc->ID) == 0) ? true : false;
							$we_JavaScript .= "_EditorFrame.getDocumentReference().frames[0].we_setPath('" . $we_doc->Path . "', '" . $we_doc->Text . "', '" . $we_doc->ID . "');" .
								'_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');' . $we_doc->getUpdateTreeScript() . ';'; // save/ rename a document
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_ok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
							if($_REQUEST['we_cmd'][4]){
								// this happens when the documents which uses the templates has to be rebuilt. (if user clicks "yes" at template save question or if automatic rebuild was set)
								if($somethingNeedsToBeResaved){
									$we_JavaScript .= '_EditorFrame.setEditorIsHot(false);top.toggleBusy(0);top.openWindow(\'' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=rebuild&step=2&btype=rebuild_filter&templateID=' . $we_doc->ID . '&responseText=' . rawurlencode(sprintf($we_responseText, $we_doc->Path)) . '\',\'resave\',-1,-1,600,130,0,true);';
									$we_responseText = '';
								}
							}
						} else{
							// we got an error while saving the template
							$we_JavaScript = '';
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_notok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
						}
					}
####TEMPLATE_SAVE_CODE2_END###
					if(!isset($TEMPLATE_SAVE_CODE2) || !$TEMPLATE_SAVE_CODE2){
						$we_responseText = g_l('weEditor', '[text/weTmpl][no_template_save]');
						$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
						include(WE_INCLUDES_PATH . 'we_templates/we_editor_save.inc.php');
						exit();
					}
					//FIXME: is this safe??? Code-Injection!
					if(isset($_REQUEST['we_cmd'][6]) && $_REQUEST['we_cmd'][6]){
						$we_JavaScript .= $_REQUEST['we_cmd'][6];
					}
				} else{
					if((!we_hasPerm('NEW_SONSTIGE')) && $we_doc->ContentType == 'application/*' && in_array($we_doc->Extension, we_base_ContentTypes::inst()->getExtension('text/html'))){
						$we_JavaScript = '';
						$we_responseText = sprintf(g_l('weEditor', '[application/*][response_save_wrongExtension]'), $we_doc->Path, $we_doc->Extension);
						$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
					} else{

						$wf_flag = false;
						$wasNew = (intval($we_doc->ID) == 0) ? true : false;
						$wasPubl = (isset($we_doc->Published) && $we_doc->Published) ? true : false;
						if(!$_SESSION['perms']['ADMINISTRATOR'] && $we_doc->ContentType != 'object' && $we_doc->ContentType != 'objectFile' && !in_workspace($we_doc->ParentID, get_ws($we_doc->Table), $we_doc->Table)){
							$we_responseText = g_l('alert', '[' . FILE_TABLE . '][not_im_ws]');
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
							include(WE_INCLUDES_PATH . 'we_templates/we_editor_save.inc.php');
							exit();
						}
						if(!$we_doc->userCanSave()){
							$we_responseText = g_l('alert', '[access_denied]');
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
							include(WE_INCLUDES_PATH . 'we_templates/we_editor_save.inc.php');
							exit();
						}

						$oldID = $we_doc->ID;
						if($we_doc->we_save()){
							if($oldID == 0){
								$we_doc->lockDocument();
							}
							$wasSaved = true;
							if($we_doc->ContentType == 'object'){
								//FIXME: removed: top.header.document.location.reload(); - what should be reloaded?!
								$we_JavaScript .= "if(top.treeData.table=='" . OBJECT_FILES_TABLE . "'){top.we_cmd('load', 'tblObjectFiles', 0);}";
							}
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_ok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;

							if($_REQUEST['we_cmd'][5]){
								$_REQUEST['we_cmd'][5] = '';
								if($we_doc->i_publInScheduleTable()){
									$foo = $we_doc->getNextPublishDate();
									if($foo){
										$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][autoschedule]'), date(g_l('date', '[format][default]'), $foo));
										$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
									}
								} else{
									if($we_doc->we_publish() == true){
										if(defined('WORKFLOW_TABLE')){
											if(we_workflow_utility::inWorkflow($we_doc->ID, $we_doc->Table)){
												we_workflow_utility::removeDocFromWorkflow($we_doc->ID, $we_doc->Table, $_SESSION['user']['ID'], '');
											}
										}
										$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_ok]'), $we_doc->Path);
										$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
										// SEEM, here a doc is published
										$GLOBALS['publish_doc'] = true;
										if($_SESSION['weS']['we_mode'] != 'seem' && ($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == WE_EDITPAGE_INFO || $we_doc->EditPageNr == WE_EDITPAGE_PREVIEW) && (!$_REQUEST['we_cmd'][4])){
											$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");
													_EditorFrame.getDocumentReference().frames[3].location.reload();'; // reload the footer with the buttons
										}
									} else{
										$we_responseText .= ' - ' . sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_publish_notok]'), $we_doc->Path);
										$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
									}
								}
							} else{
								if(($we_doc->EditPageNr == WE_EDITPAGE_INFO && (!$_REQUEST['we_cmd'][4])) || (isset($_REQUEST['we_cmd'][7]) && $_REQUEST['we_cmd'][7])){
									$we_responseText = (isset($_REQUEST['we_cmd'][7]) && $_REQUEST['we_cmd'][7]) ? '' : $we_responseText;
									$we_responseTextType = (isset($_REQUEST['we_cmd'][7]) && $_REQUEST['we_cmd'][7]) ? we_message_reporting::WE_MESSAGE_ERROR : $we_responseTextType;
									$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");';
									if(isset($_REQUEST['we_cmd'][7])){
										switch($_REQUEST['we_cmd'][7]){
											case 1:
												$we_JavaScript .= 'top.we_cmd("in_workflow","' . $we_transaction . '","' . $_REQUEST['we_cmd'][4] . '");';
												$wf_flag = true;
												break;
											case 2:
												$we_JavaScript .= 'top.we_cmd("pass","' . $we_transaction . '");';
												$wf_flag = true;
												break;
											case 3:
												$we_JavaScript .= 'top.we_cmd("decline","' . $we_transaction . '");';
												$wf_flag = true;
												break;
										}
									}
								}
								// Bug Fix #2065 -> Reload Preview Page of other documents
								elseif($we_doc->EditPageNr == WE_EDITPAGE_PREVIEW && $we_doc->ContentType == "application/*"){
									$we_JavaScript .= 'top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");';
								}
							}

							$we_JavaScript .= $we_doc->getUpdateTreeScript(!$_REQUEST['we_cmd'][4]);

							if($wasNew || (!$wasPubl)){
								$we_JavaScript .= ($we_doc->ContentType == "folder" ? 'top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");' : '') .
									'_EditorFrame.getDocumentReference().frames[3].location.reload();';
							}
							$we_JavaScript .= "_EditorFrame.getDocumentReference().frames[0].we_setPath('" . $we_doc->Path . "','" . $we_doc->Text . "', '" . $we_doc->ID . "');";


							if(!defined('SCHEDULE_TABLE')){
								$we_JavaScript .= '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');';
							}

							if(($we_doc->ContentType == 'text/webedition' || $we_doc->ContentType == 'objectFile') && $we_doc->canHaveVariants(true)){
								weShopVariants::setVariantDataForModel($we_doc, true);
							}
						} else{
							$we_JavaScript = '';
							$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_save_notok]'), $we_doc->Path);
							$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
						}
					}
					if($_REQUEST['we_cmd'][6]){
						$we_JavaScript .= $_REQUEST['we_cmd'][6];
					} else if($_REQUEST['we_cmd'][4] && (!$wf_flag)){

						$we_doc->makeSameNew();
						if(isset($we_doc->NavigationItems)){
							$we_doc->NavigationItems = '';
						}
						$we_JavaScript .= "_EditorFrame.getDocumentReference().frames[0].we_setPath('" . $we_doc->Path . "','" . $we_doc->Text . "', '" . $we_doc->ID . "');";
						//	switch to propertiy page, when user is allowed to do so.
						switch($_SESSION['weS']['we_mode']){
							case 'seem':
								$_showAlert = true; //	don't show confirm box in editor_save.inc
								$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page","' . (we_hasPerm('CAN_SEE_PROPERTIES') ? WE_EDITPAGE_PROPERTIES : $we_doc->EditPageNr) . '","' . $we_transaction . '");';
								break;
							case 'normal':
								$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");';
								break;
						}
					}
				}

				if($wasNew){ // add to history
					$we_JavaScript .= "top.weNavigationHistory.addDocToHistory('" . $we_doc->Table . "', " . $we_doc->ID . ", '" . $we_doc->ContentType . "');";
				}
			}
			$we_responseText.=$we_doc->getErrMsg();
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session

			if(defined('SCHEDULE_TABLE')){
				we_schedpro::trigger_schedule();
				$we_JavaScript .= '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');'; // save/ rename a document
			}
			include(WE_INCLUDES_PATH . 'we_templates/we_editor_save.inc.php');
			break;
		case 'unpublish':
			if($we_doc->Published){
				if($we_doc->we_unpublish()){
					$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_unpublish_ok]'), $we_doc->Path);
					$we_responseTextType = we_message_reporting::WE_MESSAGE_NOTICE;
					if($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == WE_EDITPAGE_INFO){
						$_REQUEST['we_cmd'][5] = 'top.we_cmd("switch_edit_page","' . $we_doc->EditPageNr . '","' . $we_transaction . '");'; // wird in Templ eingef?gt
					}
					if(!isset($_REQUEST['we_cmd'][5])){
						$_REQUEST['we_cmd'][5] = '';
					}
					//	When unpublishing a document stay where u are.
					//	uncomment the following line to switch to preview page.
					$_REQUEST['we_cmd'][5] .= '_EditorFrame.getDocumentReference().frames[3].location.reload();';

					$we_JavaScript = '_EditorFrame.setEditorDocumentId(' . $we_doc->ID . ');' . $we_doc->getUpdateTreeScript() . ';'; // save/ rename a document
				} else{
					$we_JavaScript = '';
					$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_unpublish_notok]'), $we_doc->Path);
					$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
				}
				$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
			} else{
				$we_JavaScript = '';
				$we_responseText = sprintf(g_l('weEditor', '[' . $we_doc->ContentType . '][response_not_published]'), $we_doc->Path);
				$we_responseTextType = we_message_reporting::WE_MESSAGE_ERROR;
			}
			include(WE_INCLUDES_PATH . 'we_templates/we_editor_publish.inc.php');
			break;
		default:
			$we_include = $we_doc->editor();
			if(!$we_include){ // object does not handle html-output, so we need to include a template( return value)
				exit('Nothing to include ...');
			}
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
			$_serverDocRoot = $_SERVER['DOCUMENT_ROOT'];
			if($_serverDocRoot != '' && substr(strtolower($we_include), 0, strlen($_SERVER['DOCUMENT_ROOT'])) == strtolower($_SERVER['DOCUMENT_ROOT'])){

				ob_start();
				if(!defined('WE_CONTENT_TYPE_SET')){
					$charset = (isset($we_doc->elements['Charset']['dat']) && $we_doc->elements['Charset']['dat']) ? //	send charset which might be determined in template
						$we_doc->elements['Charset']['dat'] :
						DEFAULT_CHARSET;
					define('WE_CONTENT_TYPE_SET', 1);
					we_html_tools::headerCtCharset('text/html', $charset);
				}
				include($we_include);
				$contents = ob_get_contents();
				ob_end_clean();

				//  SEEM the file
				//  but only, if we are not in the template-editor
				if($we_doc->ContentType != 'text/weTmpl' || ($we_doc->ContentType == 'text/weTmpl' && $we_doc->EditPageNr == WE_EDITPAGE_PREVIEW_TEMPLATE)){
					$tmpCntnt = we_SEEM::parseDocument($contents);

					// insert $_reloadFooter at right place
					$tmpCntnt = (strpos($tmpCntnt, '</head>')) ?
						str_replace('</head>', $_insertReloadFooter . '</head>', $tmpCntnt) :
						$_insertReloadFooter . $tmpCntnt;

					// --> Start Glossary Replacement

					if(defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_DOC']) || $GLOBALS['WE_MAIN_DOC'] == $GLOBALS['we_doc']) && !$GLOBALS['we_editmode']){
						if(isset($we_doc->InGlossar) && $we_doc->InGlossar == 0){
							weGlossaryReplace::start();
						}
					}

					print $tmpCntnt;

					// --> Finish Glossary Replacement

					if(defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_DOC']) || $GLOBALS['WE_MAIN_DOC'] == $GLOBALS['we_doc']) && !$GLOBALS['we_editmode']){
						if(isset($we_doc->InGlossar) && $we_doc->InGlossar == 0){
							weGlossaryReplace::end($GLOBALS['we_doc']->Language);
						}
					}
				} else{
					print $contents;
				}
			} else{
				//  These files were edited only in source-code mode, so no seeMode is needed.
				if(preg_match('#^' . WEBEDITION_DIR . 'we/#', $we_include)){
					include($_SERVER['DOCUMENT_ROOT'] . $we_include);
				} else{
					include(WE_INCLUDES_PATH . $we_include);
				}
				print $_insertReloadFooter;
			}
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
			if(isset($GLOBALS['we_file_to_delete_after_include'])){
				we_util_File::deleteLocalFile($GLOBALS['we_file_to_delete_after_include']);
			}
			if($we_doc->EditPageNr == WE_EDITPAGE_PROPERTIES || $we_doc->EditPageNr == WE_EDITPAGE_SCHEDULER || $we_doc->EditPageNr == WE_EDITPAGE_THUMBNAILS){
				print we_html_element::jsElement('setTimeout("doScrollTo();",100);');
			}
	}
}

// prevent persmissions overriding
$_SESSION['perms'] = $perms;
