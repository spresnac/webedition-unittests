<?php

/**
 * webEdition CMS
 *
 * $Rev: 5870 $
 * $Author: lukasimhof $
 * $Date: 2013-02-22 17:06:45 +0100 (Fri, 22 Feb 2013) $
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
function we_tag_write($attribs){
	$type = weTag_getAttribute('type', $attribs, 'document');

	switch($type){
		case 'object':
			if(($foo = attributFehltError($attribs, 'classid', __FUNCTION__))){
				return $foo;
			}
			break;
		default:
			$type = 'document'; //make sure type is known!
			if(($foo = attributFehltError($attribs, 'doctype', __FUNCTION__))){
				return $foo;
			}
			break;
	}

	$name = weTag_getAttribute('formname', $attribs, ((isset($GLOBALS['WE_FORM']) && $GLOBALS['WE_FORM']) ? $GLOBALS['WE_FORM'] : 'we_global_form'));

	$publish = weTag_getAttribute('publish', $attribs, false, true);
	$triggerid = weTag_getAttribute('triggerid', $attribs, 0);
	$charset = weTag_getAttribute('charset', $attribs, 'iso-8859-1');
	$doctype = weTag_getAttribute('doctype', $attribs);
	$tid = weTag_getAttribute('tid', $attribs);
	$categories = weTag_getAttribute('categories', $attribs);
	$classid = weTag_getAttribute('classid', $attribs);
	$userid = weTag_getAttribute('userid', $attribs); // deprecated  use protected=true instead
	$protected = weTag_getAttribute('protected', $attribs, false, true);
	$admin = weTag_getAttribute('admin', $attribs);
	$mail = weTag_getAttribute('mail', $attribs);
	$mailfrom = weTag_getAttribute('mailfrom', $attribs);
	$forceedit = weTag_getAttribute('forceedit', $attribs, false, true);
	$workspaces = weTag_getAttribute('workspaces', $attribs);
	$objname = preg_replace('/[^a-z0-9_-]/i', '', weTag_getAttribute('name', $attribs));
	$onduplicate = ($objname == '' ? 'overwrite' : weTag_getAttribute('onduplicate', $attribs, 'increment'));
	$onpredefinedname = weTag_getAttribute('onpredefinedname', $attribs, 'appendto');
	$workflowname = weTag_getAttribute('workflowname', $attribs);
	$workflowuserid = weTag_getAttribute('workflowuserid', $attribs, 0);
	$doworkflow = ($workflowname != '' && $workflowuserid != 0);
	$searchable = weTag_getAttribute('searchable', $attribs, true, true);
	if(isset($_REQUEST['edit_' . $type]) && $_REQUEST['edit_' . $type]){

		switch($type){
			case 'document':
				$ok = we_webEditionDocument::initDocument($name, $tid, $doctype, $categories);
				break;
			case 'object':
				$parentid = weTag_getAttribute('parentid', $attribs);
				$ok = we_objectFile::initObject(intval($classid), $name, $categories, intval($parentid));
				break;
		}

		if($ok){

			$isOwner = false;
			if($protected && isset($_SESSION['webuser']['ID'])){
				$isOwner = ($_SESSION['webuser']['ID'] == $GLOBALS['we_' . $type][$name]->WebUserID);
			} else{
				$isOwner = ($userid) && ($_SESSION['webuser']['ID'] == $GLOBALS['we_' . $type][$name]->getElement($userid));
			}
			$isAdmin = ($admin) && isset($_SESSION['webuser'][$admin]) && $_SESSION['webuser'][$admin];

			if($isAdmin || ($GLOBALS['we_' . $type][$name]->ID == 0) || $isOwner || $forceedit){
				$doWrite = true;
				$GLOBALS['we_' . $type . '_write_ok'] = true;
				//$newObject = ($GLOBALS['we_'.$type][$name]->ID) ? false : true;
				if($protected){
					if(!isset($_SESSION['webuser']['ID'])){
						return;
					}
					if(!$GLOBALS['we_' . $type][$name]->WebUserID){
						$GLOBALS['we_' . $type][$name]->WebUserID = $_SESSION['webuser']['ID'];
					}
				} else{
					if($userid){
						if(!isset($_SESSION['webuser']['ID']))
							return;
						if(!$GLOBALS['we_' . $type][$name]->getElement($userid)){
							$GLOBALS['we_' . $type][$name]->setElement($userid, $_SESSION['webuser']['ID']);
						}
					}
				}

				checkAndCreateImage($name, ($type == 'document') ? 'we_document' : 'we_object');
				checkAndCreateFlashmovie($name, ($type == 'document') ? 'we_document' : 'we_object');
				checkAndCreateQuicktime($name, ($type == 'document') ? 'we_document' : 'we_object');
				checkAndCreateBinary($name, ($type == 'document') ? 'we_document' : 'we_object');

				$GLOBALS['we_' . $type][$name]->i_checkPathDiffAndCreate();
				if($objname == ''){
					$GLOBALS['we_' . $type][$name]->i_correctDoublePath();
				}
				if(isset($GLOBALS['we_doc'])){
					$_WE_DOC_SAVE = $GLOBALS['we_doc'];
				}
				$GLOBALS['we_doc'] = &$GLOBALS['we_' . $type][$name];
				$GLOBALS['we_doc']->IsSearchable = $searchable;
				if(strlen($workspaces) > 0 && $type == 'object'){
					$wsArr = makeArrayFromCSV($workspaces);
					$tmplArray = array();
					foreach($wsArr as $wsId){
						$tmplArray[] = $GLOBALS['we_' . $type][$name]->getTemplateFromWs($wsId);
					}
					$GLOBALS['we_' . $type][$name]->Workspaces = makeCSVFromArray($wsArr, true);
					$GLOBALS['we_' . $type][$name]->Templates = makeCSVFromArray($tmplArray, true);
				}

				$GLOBALS['we_' . $type][$name]->Path = $GLOBALS['we_' . $type][$name]->getPath();

				if(defined('OBJECT_FILES_TABLE') && $type == 'object'){
					$db = new DB_WE();
					if($GLOBALS['we_' . $type][$name]->Text == ''){
						if($objname == ''){
							$objname = 1 + intval(f('SELECT MAX(ID) AS ID FROM ' . OBJECT_FILES_TABLE, 'ID', $db));
						}
					} else{
						switch($onpredefinedname){
							case 'appendto':
								$objname = ($objname != '' ? $GLOBALS['we_' . $type][$name]->Text . '_' . $objname : $GLOBALS['we_' . $type][$name]->Text);
								break;
							case 'infrontof':
								$objname .= ($objname != '' ? '_' . $GLOBALS['we_' . $type][$name]->Text : $GLOBALS['we_' . $type][$name]->Text);
								break;
							case 'overwrite':
								if($objname == ''){
									$objname = $GLOBALS['we_' . $type][$name]->Text;
								}
								break;
						}
					}
					$objexists = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $db->escape(str_replace('//', '/', $GLOBALS['we_' . $type][$name]->Path . '/' . $objname)) . '"', 'ID', $db);
					if($objexists == ''){
						$GLOBALS['we_' . $type][$name]->Text = $objname;
						$GLOBALS['we_' . $type][$name]->Path = str_replace('//', '/', $GLOBALS['we_' . $type][$name]->Path . '/' . $objname);
					} else{
						switch($onduplicate){
							case 'abort':
								$GLOBALS['we_object_write_ok'] = false;
								$doWrite = false;
								break;
							case 'overwrite':
								$GLOBALS['we_' . $type][$name]->ID = $objexists;
								$GLOBALS['we_' . $type][$name]->Path = str_replace('//', '/', $GLOBALS['we_' . $type][$name]->Path . '/' . $objname);
								$GLOBALS['we_' . $type][$name]->Text = $objname;
								break;
							case 'increment':
								$z = 1;
								$footext = $objname . "_" . $z;
								while(f("SELECT ID FROM " . OBJECT_FILES_TABLE . " WHERE Path='" . escape_sql_query(str_replace('//', '/', $GLOBALS["we_$type"][$name]->Path . "/" . $footext)) . "'", "ID", $db)) {
									$z++;
									$footext = $objname . "_" . $z;
								}
								$GLOBALS["we_$type"][$name]->Path = str_replace('//', '/', $GLOBALS["we_$type"][$name]->Path . '/' . $footext);
								$GLOBALS["we_$type"][$name]->Text = $footext;
								break;
						}
					}
				}
				if($doWrite){
					$ret = $GLOBALS['we_' . $type][$name]->we_save();
					if($publish && !$doworkflow){
						if($type == 'document' && (!$GLOBALS['we_' . $type][$name]->IsDynamic) && isset($GLOBALS['we_doc'])){ // on static HTML Documents we have to do it different
							$ret1 = $GLOBALS['we_doc']->we_publish();
						} else{
							$ret1 = $GLOBALS['we_' . $type][$name]->we_publish();
						}
					}

					if($doworkflow){
						$wf_text = $workflowname . '  ';
						switch($type){
							default:
							case 'document':
								$wf_text .= 'Document ID: ' . $GLOBALS['we_doc']->ID;
								$tab = FILE_TABLE;
								break;
							case 'object':
								$wf_text .= 'Object ID: ' . $GLOBALS['we_doc']->ID;
								$tab = OBJECT_FILES_TABLE;
								break;
						}
						$workflowID = we_workflow_utility::getWorkflowID($workflowname, $tab);

						if(!we_workflow_utility::insertDocInWorkflow($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table, $workflowID, $workflowuserid, $wf_text)){
							t_e('error inserting document to workflow. Additional data:', $GLOBALS['we_doc']->Table, $workflowID, $workflowuserid);
						}
					}
					$GLOBALS['we_object_write_ID'] = $GLOBALS['we_doc']->ID;
				}

				unset($GLOBALS['we_doc']);
				if(isset($_WE_DOC_SAVE)){
					$GLOBALS['we_doc'] = $_WE_DOC_SAVE;
					unset($_WE_DOC_SAVE);
				}
				$_REQUEST['we_returnpage'] = $GLOBALS['we_' . $type][$name]->getElement('we_returnpage');

				if($doWrite && $mail){
					if(!$mailfrom){
						$mailfrom = 'dontReply@' . $_SERVER['SERVER_NAME'];
					}
					$path = $GLOBALS['we_' . $type][$name]->Path;
					switch($type){
						case 'object':
							$classname = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid), 'Text', $GLOBALS['DB_WE']);
							$mailtext = sprintf(g_l('global', '[std_mailtext_newObj]'), $path, $classname) . "\n" . ($triggerid ? getServerUrl() . id_to_path($triggerid) . '?we_objectID=' : 'ObjectID: ') . $GLOBALS['we_object'][$name]->ID;
							$subject = g_l('global', '[std_subject_newObj]');
							break;
						default:
						case 'document':
							$mailtext = sprintf(g_l('global', '[std_mailtext_newDoc]'), $path) . "\n" . $GLOBALS['we_' . $type][$name]->getHttpPath();
							$subject = g_l('global', '[std_subject_newDoc]');
							break;
					}
					$phpmail = new we_util_Mailer($mail, $subject, $mailfrom);
					$phpmail->setCharSet($charset);
					$phpmail->addTextPart($mailtext);
					$phpmail->buildMessage();
					$phpmail->Send();
				}
			} else{
				$GLOBALS['we_object_write_ok'] = false;
			}
		}
	}
	if(isset($GLOBALS['WE_SESSION_START']) && $GLOBALS['WE_SESSION_START']){
		unset($_SESSION['we_' . $type . '_session_' . $name]);
		$GLOBALS['we_' . $type . '_session_' . $name] = array();
	}
}

function checkAndCreateFlashmovie($formname, $type = 'we_document'){
	$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;
	$regs = array();
	foreach($_REQUEST as $key => $_flashmovieDataId){
		if(preg_match('|^WE_UI_FLASHMOVIE_DATA_ID_(.*)$|', $key, $regs)){

			$_flashName = $regs[1];
			$flashId = isset($_SESSION[$_flashmovieDataId]['id']) ? $_SESSION[$_flashmovieDataId]['id'] : 0;
			if(isset($_SESSION[$_flashmovieDataId]['doDelete']) && $_SESSION[$_flashmovieDataId]['doDelete'] == 1){

				if($flashId){
					$flashDocument = new we_flashDocument();
					$flashDocument->initByID($flashId);
					if($flashDocument->WebUserID == $webuserId){
						//everything ok, now delete
						$GLOBALS['NOT_PROTECT'] = true;
						include_once (WE_INCLUDES_PATH . 'we_delete_fn.inc.php');
						deleteEntry($flashId, FILE_TABLE);
						$GLOBALS['NOT_PROTECT'] = false;
						$GLOBALS[$type][$formname]->setElement($_flashName, 0);
					}
				}
			} else
			if(isset($_SESSION[$_flashmovieDataId]['serverPath'])){
				if(substr($_SESSION[$_flashmovieDataId]['type'], 0, 29) == 'application/x-shockwave-flash'){
					$flashDocument = new we_flashDocument();

					if($flashId){
						// document has already an image
						// so change binary data
						$flashDocument->initByID($flashId);
					}

					$flashDocument->Filename = $_SESSION[$_flashmovieDataId]['fileName'];
					$flashDocument->Extension = $_SESSION[$_flashmovieDataId]['extension'];
					$flashDocument->Text = $_SESSION[$_flashmovieDataId]['text'];

					if(!$flashId){
						$flashDocument->setParentID($_SESSION[$_flashmovieDataId]['parentid']);
					}
					$flashDocument->Path = $flashDocument->getParentPath() . (($flashDocument->getParentPath() != '/') ? '/' : '') . $flashDocument->Text;

					$flashDocument->setElement('width', $_SESSION[$_flashmovieDataId]['imgwidth'], 'attrib');
					$flashDocument->setElement('height', $_SESSION[$_flashmovieDataId]['imgheight'], 'attrib');
					$flashDocument->setElement('origwidth', $_SESSION[$_flashmovieDataId]['imgwidth']);
					$flashDocument->setElement('origheight', $_SESSION[$_flashmovieDataId]['imgheight']);

					$flashDocument->setElement('type', 'application/x-shockwave-flash', 'attrib');

					$flashDocument->setElement('data', $_SESSION[$_flashmovieDataId]['serverPath'], 'image');

					$flashDocument->setElement('filesize', $_SESSION[$_flashmovieDataId]['size'], 'attrib');

					$flashDocument->Table = FILE_TABLE;
					$flashDocument->Published = time();
					$flashDocument->WebUserID = $webuserId;
					$flashDocument->we_save();
					$newId = $flashDocument->ID;

					$t = explode('_', $flashDocument->Filename);
					$t[1] = $newId;
					$fn = implode('_', $t);
					$flashDocument->Filename = $fn;
					$flashDocument->Path = $flashDocument->getParentPath() . (($flashDocument->getParentPath() != '/') ? '/' : '') . $flashDocument->Filename . $flashDocument->Extension;
					$flashDocument->we_save();

					$GLOBALS[$type][$formname]->setElement($_flashName, $newId);
				}
			}
			if(isset($_SESSION[$_flashmovieDataId])){
				unset($_SESSION[$_flashmovieDataId]);
			}
		}
	}
}

function checkAndCreateQuicktime($formname, $type = 'we_document'){
	$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;
	$regs = array();
	foreach($_REQUEST as $key => $_quicktimeDataId){
		if(preg_match('|^WE_UI_QUICKTIME_DATA_ID_(.*)$|', $key, $regs)){
			$_quicktimeName = $regs[1];
			$quicktimeId = isset($_SESSION[$_quicktimeDataId]['id']) ? $_SESSION[$_quicktimeDataId]['id'] : 0;
			if(isset($_SESSION[$_quicktimeDataId]['doDelete']) && $_SESSION[$_quicktimeDataId]['doDelete'] == 1){

				if($quicktimeId){
					$quicktimeDocument = new we_quicktimeDocument();
					$quicktimeDocument->initByID($quicktimeId);
					if($quicktimeDocument->WebUserID == $webuserId){
						//everything ok, now delete
						$GLOBALS['NOT_PROTECT'] = true;
						include_once (WE_INCLUDES_PATH . 'we_delete_fn.inc.php');
						deleteEntry($quicktimeId, FILE_TABLE);
						$GLOBALS['NOT_PROTECT'] = false;
						$GLOBALS[$type][$formname]->setElement($_quicktimeName, 0);
					}
				}
			} else
			if(isset($_SESSION[$_quicktimeDataId]['serverPath'])){
				if(substr($_SESSION[$_quicktimeDataId]['type'], 0, 15) == 'video/quicktime'){
					$quicktimeDocument = new we_quicktimeDocument();

					if($quicktimeId){
						// document has already an image
						// so change binary data
						$quicktimeDocument->initByID(
							$quicktimeId);
					}

					$quicktimeDocument->Filename = $_SESSION[$_quicktimeDataId]['fileName'];
					$quicktimeDocument->Extension = $_SESSION[$_quicktimeDataId]['extension'];
					$quicktimeDocument->Text = $_SESSION[$_quicktimeDataId]['text'];

					if(!$quicktimeId){
						$quicktimeDocument->setParentID($_SESSION[$_quicktimeDataId]['parentid']);
					}
					$quicktimeDocument->Path = $quicktimeDocument->getParentPath() . (($quicktimeDocument->getParentPath() != '/') ? '/' : '') . $quicktimeDocument->Text;

					//$quicktimeDocument->setElement('width', $_SESSION[$_quicktimeDataId]['imgwidth'], 'attrib');
					//$quicktimeDocument->setElement('height', $_SESSION[$_quicktimeDataId]['imgheight'], 'attrib');
					//$quicktimeDocument->setElement('origwidth', $_SESSION[$_quicktimeDataId]['imgwidth']);
					//$quicktimeDocument->setElement('origheight', $_SESSION[$_quicktimeDataId]['imgheight']);

					$quicktimeDocument->setElement('type', 'video/quicktime', 'attrib');

					$quicktimeDocument->setElement('data', $_SESSION[$_quicktimeDataId]['serverPath'], 'image');

					$quicktimeDocument->setElement('filesize', $_SESSION[$_quicktimeDataId]['size'], 'attrib');

					$quicktimeDocument->Table = FILE_TABLE;
					$quicktimeDocument->Published = time();
					$quicktimeDocument->WebUserID = $webuserId;
					$quicktimeDocument->we_save();
					$newId = $quicktimeDocument->ID;

					$t = explode('_', $quicktimeDocument->Filename);
					$t[1] = $newId;
					$fn = implode('_', $t);
					$quicktimeDocument->Filename = $fn;
					$quicktimeDocument->Path = $quicktimeDocument->getParentPath() . (($quicktimeDocument->getParentPath() != '/') ? '/' : '') . $quicktimeDocument->Filename . $quicktimeDocument->Extension;
					$quicktimeDocument->we_save();

					$GLOBALS[$type][$formname]->setElement($_quicktimeName, $newId);
				}
			}
			if(isset($_SESSION[$_quicktimeDataId])){
				unset($_SESSION[$_quicktimeDataId]);
			}
		}
	}
}

function checkAndCreateImage($formname, $type = 'we_document'){
	$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;
	$regs = array();
	foreach($_REQUEST as $key => $_imgDataId){
		if(preg_match('|^WE_UI_IMG_DATA_ID_(.*)$|', $key, $regs)){
			$_imgName = $regs[1];
			$imgId = isset($_SESSION[$_imgDataId]['id']) ? $_SESSION[$_imgDataId]['id'] : 0;
			if(isset($_SESSION[$_imgDataId]['doDelete']) && $_SESSION[$_imgDataId]['doDelete'] == 1){

				if($imgId){
					$imgDocument = new we_imageDocument();
					$imgDocument->initByID($imgId);
					if($imgDocument->WebUserID == $webuserId){
						//everything ok, now delete
						$GLOBALS['NOT_PROTECT'] = true;
						include_once (WE_INCLUDES_PATH . 'we_delete_fn.inc.php');
						deleteEntry($imgId, FILE_TABLE);
						$GLOBALS['NOT_PROTECT'] = false;
						$GLOBALS[$type][$formname]->setElement($_imgName, 0);
					}
				}
			} else
			if(isset($_SESSION[$_imgDataId]['serverPath'])){
				if(substr($_SESSION[$_imgDataId]['type'], 0, 6) == 'image/'){
					$imgDocument = new we_imageDocument();

					if($imgId){
						// document has already an image
						// so change binary data
						$imgDocument->initByID(
							$imgId);
					}

					$imgDocument->Filename = $_SESSION[$_imgDataId]['fileName'];
					$imgDocument->Extension = $_SESSION[$_imgDataId]['extension'];
					$imgDocument->Text = $_SESSION[$_imgDataId]['text'];

					if(!$imgId){
						$imgDocument->setParentID($_SESSION[$_imgDataId]['parentid']);
					}
					$imgDocument->Path = $imgDocument->getParentPath() . (($imgDocument->getParentPath() != '/') ? '/' : '') . $imgDocument->Text;

					$imgDocument->setElement('width', $_SESSION[$_imgDataId]['imgwidth'], 'attrib');
					$imgDocument->setElement('height', $_SESSION[$_imgDataId]['imgheight'], 'attrib');
					$imgDocument->setElement('origwidth', $_SESSION[$_imgDataId]['imgwidth']);
					$imgDocument->setElement('origheight', $_SESSION[$_imgDataId]['imgheight']);

					$imgDocument->setElement('type', 'image/*', 'attrib');

					$imgDocument->setElement('data', $_SESSION[$_imgDataId]['serverPath'], 'image');

					$imgDocument->setElement('filesize', $_SESSION[$_imgDataId]['size'], 'attrib');

					$imgDocument->Table = FILE_TABLE;
					$imgDocument->Published = time();
					$imgDocument->WebUserID = $webuserId;
					$imgDocument->we_save();
					$newId = $imgDocument->ID;

					$t = explode('_', $imgDocument->Filename);
					$t[1] = $newId;
					$fn = implode('_', $t);
					$imgDocument->Filename = $fn;
					$imgDocument->Path = $imgDocument->getParentPath() . (($imgDocument->getParentPath() != '/') ? '/' : '') . $imgDocument->Filename . $imgDocument->Extension;
					$imgDocument->we_save();

					$GLOBALS[$type][$formname]->setElement($_imgName, $newId);
				}
			}
			if(isset($_SESSION[$_imgDataId])){
				unset($_SESSION[$_imgDataId]);
			}
		}
	}
}

function checkAndCreateBinary($formname, $type = 'we_document'){
	$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;
	$regs = array();
	foreach($_REQUEST as $key => $_binaryDataId){
		if(preg_match('|^WE_UI_BINARY_DATA_ID_(.*)$|', $key, $regs)){
			$_binaryName = $regs[1];
			$binaryId = isset($_SESSION[$_binaryDataId]['id']) ? $_SESSION[$_binaryDataId]['id'] : 0;
			if(isset($_SESSION[$_binaryDataId]['doDelete']) && $_SESSION[$_binaryDataId]['doDelete'] == 1){

				if($binaryId){
					$binaryDocument = new we_otherDocument();
					$binaryDocument->initByID($binaryId);
					if($binaryDocument->WebUserID == $webuserId){
						//everything ok, now delete
						$GLOBALS['NOT_PROTECT'] = true;
						include_once (WE_INCLUDES_PATH . 'we_delete_fn.inc.php');
						deleteEntry($binaryId, FILE_TABLE);
						$GLOBALS['NOT_PROTECT'] = false;
						$GLOBALS[$type][$formname]->setElement($_binaryName, 0);
					}
				}
			} else
			if(isset($_SESSION[$_binaryDataId]['serverPath'])){
				if(substr($_SESSION[$_binaryDataId]['type'], 0, 12) == 'application/'){
					$binaryDocument = new we_otherDocument();

					if($binaryId){
						// document has already an image
						// so change binary data
						$binaryDocument->initByID(
							$binaryId);
					}

					$binaryDocument->Filename = $_SESSION[$_binaryDataId]['fileName'];
					$binaryDocument->Extension = $_SESSION[$_binaryDataId]['extension'];
					$binaryDocument->Text = $_SESSION[$_binaryDataId]['text'];

					if(!$binaryId){
						$binaryDocument->setParentID($_SESSION[$_binaryDataId]['parentid']);
					}
					$binaryDocument->Path = $binaryDocument->getParentPath() . (($binaryDocument->getParentPath() != '/') ? '/' : '') . $binaryDocument->Text;


					$binaryDocument->setElement('type', 'application/*', 'attrib');

					$binaryDocument->setElement('data', $_SESSION[$_binaryDataId]['serverPath'], 'application');

					$binaryDocument->setElement('filesize', $_SESSION[$_binaryDataId]['size'], 'attrib');

					$binaryDocument->Table = FILE_TABLE;
					$binaryDocument->Published = time();
					$binaryDocument->WebUserID = $webuserId;
					$binaryDocument->we_save();

					$newId = $binaryDocument->ID;

					$t = explode('_', $binaryDocument->Filename);
					$t[1] = $newId;
					$fn = implode('_', $t);
					$binaryDocument->Filename = $fn;
					$binaryDocument->Path = $binaryDocument->getParentPath() . (($binaryDocument->getParentPath() != '/') ? '/' : '') . $binaryDocument->Filename . $binaryDocument->Extension;
					$binaryDocument->we_save();

					$GLOBALS[$type][$formname]->setElement($_binaryName, $newId);
				}
			}
			if(isset($_SESSION[$_binaryDataId])){
				unset($_SESSION[$_binaryDataId]);
			}
		}
	}
}
