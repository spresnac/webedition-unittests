<?php

/**
 * webEdition CMS
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
function we_tag_saveRegisteredUser($attribs){
	$userexists = weTag_getAttribute('userexists', $attribs);
	$userempty = weTag_getAttribute('userempty', $attribs);
	$passempty = weTag_getAttribute('passempty', $attribs);
	$changesessiondata = weTag_getAttribute('changesessiondata', $attribs, true, true);
	$default_register = f('SELECT Value FROM ' . CUSTOMER_ADMIN_TABLE . ' WHERE Name="default_saveRegisteredUser_register"', 'Value', $GLOBALS['DB_WE']) == 'true';
	$registerallowed = (isset($attribs['register']) ? weTag_getAttribute('register', $attribs, $default_register, true) : $default_register);
	$protected = makeArrayFromCSV(weTag_getAttribute('protected', $attribs));
	$GLOBALS['we_customer_writen'] = false;
	$GLOBALS['we_customer_written'] = false;
	if(defined('CUSTOMER_TABLE') && isset($_REQUEST['s'])){
		if(isset($_REQUEST['s']['Password2'])){
			unset($_REQUEST['s']['Password2']);
		}

		$dates = array(); //type date
		foreach($_REQUEST['s'] as $n => $v){
			if(preg_match('/^we_date_([a-zA-Z0-9_]+)_(day|month|year|minute|hour)$/', $n, $regs)){
				$dates[$regs[1]][$regs[2]] = $v;
				unset($_REQUEST['s'][$n]);
			}
		}
		foreach($dates as $k => $vv){
			$_REQUEST['s'][$k] = $vv['year'] . '-' . $vv['month'] . '-' . $vv['day'] . ' ' . $vv['hour'] . ':' . $vv['minute'] . ':00';
		}

		//register new User
		if(isset($_REQUEST['s']['ID']) && (!isset($_SESSION['webuser']['ID'])) && intval($_REQUEST['s']['ID']) <= 0 && $registerallowed && (!isset($_SESSION['webuser']['registered']) || !$_SESSION['webuser']['registered'])){ // neuer User
			if($_REQUEST['s']['Password'] != '' && $_REQUEST['s']['Username'] != ''){ // wenn password und Username nicht leer
				if(!weCustomer::customerNameExist($_REQUEST['s']['Username'], $GLOBALS['DB_WE'])){ // username existiert noch nicht!
					$hook = new weHook('customer_preSave', '', array('customer' => &$_REQUEST['s'], 'from' => 'tag', 'type' => 'new', 'tagname' => 'saveRegisteredUser'));
					$ret = $hook->executeHook();

					// skip protected Fields
					if(!empty($protected)){
						foreach($_REQUEST['s'] as $name => $val){
							if(in_array($name, $protected)){
								unset($_REQUEST['s'][$name]);
							}
						}
					}
					we_saveCustomerImages();
					$set = we_tag_saveRegisteredUser_processRequest();

					if(!empty($set)){
						// User in DB speichern
						$set['ModifyDate'] = 'UNIX_TIMESTAMP()';
						$set['ModifiedBy'] = 'frontend';
						$GLOBALS['DB_WE']->query('INSERT INTO ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($set));

						// User in session speichern
						$uID = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $GLOBALS['DB_WE']->escape($_REQUEST['s']['Username']) . '"', 'ID', $GLOBALS['DB_WE']);
						$GLOBALS["we_customer_write_ID"] = $uID;
						$GLOBALS["we_customer_writen"] = true;
						$GLOBALS['we_customer_written'] = true;
						if($uID && $changesessiondata){
							$_SESSION['webuser'] = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . $uID, $GLOBALS['DB_WE']);
							$_SESSION['webuser']['registered'] = true;

							$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET MemberSince=UNIX_TIMESTAMP(),LastAccess=UNIX_TIMESTAMP(),LastLogin=UNIX_TIMESTAMP(),
									ModifyDate=UNIX_TIMESTAMP(),ModifiedBy="frontend" WHERE ID=' . $_SESSION['webuser']['ID']);
						}
					}
				} else{ // Username existiert schon!
					if(!$userexists){
						$userexists = g_l('customer', '[username_exists]');
					}

					// Eingabe in Session schreiben, damit die eingegebenen Werte erhalten bleiben!
					we_tag_saveRegisteredUser_keepInput();

					print getHtmlTag('script', array('type' => 'text/javascript'), 'history.back(); ' . we_message_reporting::getShowMessageCall(sprintf($userexists, $_REQUEST['s']['Username']), we_message_reporting::WE_MESSAGE_FRONTEND));
				}
			} else{ // Password oder Username leer!
				// Eingabe in Session schreiben, damit die eingegebenen Werte erhalten bleiben!
				if(isset($_REQUEST['s'])){
					we_tag_saveRegisteredUser_keepInput();
				}

				if(strlen($_REQUEST['s']['Username']) == 0){


					if(!$userempty){
						$userempty = g_l('customer', '[username_empty]');
					}
					print getHtmlTag('script', array('type' => 'text/javascript'), 'history.back();' . we_message_reporting::getShowMessageCall($userempty, we_message_reporting::WE_MESSAGE_FRONTEND));
				} else if(strlen($_REQUEST['s']['Password']) == 0){

					if(!$passempty){
						$passempty = g_l('customer', '[password_empty]');
					}
					print getHtmlTag('script', array('type' => 'text/javascript'), 'history.back();' . we_message_reporting::getShowMessageCall($passempty, we_message_reporting::WE_MESSAGE_FRONTEND));
				}
			}
		} else if(isset($_REQUEST['s']['ID']) && $_REQUEST['s']['ID'] == $_SESSION['webuser']['ID'] && $_SESSION['webuser']['registered']){ // existing user
			// existierender User (Daten werden von User geaendert)!!
			$Username = isset($_REQUEST['s']['Username']) ? $_REQUEST['s']['Username'] : $_SESSION['webuser']['Username'];

			if(f('SELECT 1 AS a FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $GLOBALS["DB_WE"]->escape($Username) . '" AND ID!=' . intval($_REQUEST['s']['ID']), 'a', $GLOBALS['DB_WE']) != '1'){ // es existiert kein anderer User mit den neuen Username oder username hat sich nicht geaendert
				if(isset($_REQUEST['s'])){


					$hook = new weHook('customer_preSave', '', array('customer' => &$_REQUEST['s'], 'from' => 'tag', 'type' => 'modify', 'tagname' => 'saveRegisteredUser'));
					$ret = $hook->executeHook();

					// skip protected Fields
					if(!empty($protected)){
						foreach($_REQUEST['s'] as $name => $val){
							if(in_array($name, $protected)){
								unset($_REQUEST['s'][$name]);
							}
						}
					}

					we_saveCustomerImages();
					$set_a = we_tag_saveRegisteredUser_processRequest();

					if(isset($_REQUEST['s']['Password']) && $_REQUEST['s']['Password'] != $_SESSION['webuser']['Password']){//bei Password�nderungen m�ssen die Autologins des Users gel�scht werden
						$GLOBALS['DB_WE']->query('DELETE FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE WebUserID=' . intval($_REQUEST['s']['ID']));
					}
					if(!empty($set_a)){
						$set_a['ModifyDate'] = 'UNIX_TIMESTAMP()';
						$set_a['ModifiedBy'] = 'frontend';
						$GLOBALS['DB_WE']->query('UPDATE ' . CUSTOMER_TABLE . ' SET ' . we_database_base::arraySetter($set_a) . ' WHERE ID=' . intval($_REQUEST['s']['ID']));
						$GLOBALS["we_customer_writen"] = true;
						$GLOBALS['we_customer_written'] = true;
					}
				}
			} else{
				$userexists = $userexists ? $userexists : g_l('customer', '[username_exists]');
				print getHtmlTag('script', array('type' => 'text/javascript'), 'history.back(); ' . we_message_reporting::getShowMessageCall(sprintf($userexists, $_REQUEST['s']['Username']), we_message_reporting::WE_MESSAGE_FRONTEND));
			}

			//die neuen daten in die session schreiben
			$oldReg = $_SESSION['webuser']['registered'];
			if($changesessiondata){
				$_SESSION['webuser'] = getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($_REQUEST['s']['ID']), $GLOBALS['DB_WE']);
			}
			//don't set anything that wasn't set before
			$_SESSION['webuser']['registered'] = $oldReg;
		}
	}
}

function we_saveCustomerImages(){

	if(isset($_FILES['WE_SF_IMG_DATA']['name']) && is_array($_FILES['WE_SF_IMG_DATA']['name'])){
		$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;
		foreach($_FILES['WE_SF_IMG_DATA']['name'] as $imgName => $filename){
			$imgId = isset($_SESSION['webuser'][$imgName]) ? $_SESSION['webuser'][$imgName] : 0;
			$_foo = id_to_path($imgId);
			if(!$_foo){
				$imgId = 0;
			}

			if(isset($_REQUEST['WE_SF_DEL_CHECKBOX_' . $imgName]) && $_REQUEST['WE_SF_DEL_CHECKBOX_' . $imgName] == 1){
				if($imgId){
					$imgDocument = new we_imageDocument();
					$imgDocument->initByID($imgId);
					if($imgDocument->WebUserID == $webuserId){
						//everything ok, now delete

						$GLOBALS['NOT_PROTECT'] = true;
						include_once(WE_INCLUDES_PATH . 'we_delete_fn.inc.php');
						deleteEntry($imgId, FILE_TABLE);
						$GLOBALS['NOT_PROTECT'] = false;
						// reset image field
						$_SESSION['webuser'][$imgName] = 0;
						$_REQUEST['s'][$imgName] = 0;
					}
				}
			} else if($filename){
				// file is selected, check to see if it is an image
				$ct = getContentTypeFromFile($filename);
				if($ct == 'image/*'){

					$_serverPath = TEMP_PATH . '/' . weFile::getUniqueId();
					move_uploaded_file($_FILES['WE_SF_IMG_DATA']['tmp_name'][$imgName], $_serverPath);

					$we_size = we_thumbnail::getimagesize($_serverPath);

					if(!empty($we_size)){

						$tmp_Filename = $imgName . '_' . weFile::getUniqueId() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['WE_SF_IMG_DATA']['name'][$imgName]);
						$tmp = explode('.', $tmp_Filename);
						$_extension = '.' . $tmp[count($tmp) - 1];
						unset($tmp[count($tmp) - 1]);
						$_fileName = implode('.', $tmp);
						$_text = $_fileName . $_extension;

						//image needs to be scaled
						if((isset($_SESSION['webuser']['imgtmp'][$imgName]['width']) && $_SESSION['webuser']['imgtmp'][$imgName]['width']) ||
							(isset($_SESSION['webuser']['imgtmp'][$imgName]['height']) && $_SESSION['webuser']['imgtmp'][$imgName]['height'])){
							$imageData = weFile::load($_serverPath);
							$thumb = new we_thumbnail();
							$thumb->init('dummy', $_SESSION['webuser']['imgtmp'][$imgName]['width'], $_SESSION['webuser']['imgtmp'][$imgName]['height'], $_SESSION['webuser']['imgtmp'][$imgName]['keepratio'], $_SESSION['webuser']['imgtmp'][$imgName]['maximize'], false, false, '', 'dummy', 0, '', '', $_extension, $we_size[0], $we_size[1], $imageData, '', $_SESSION['webuser']['imgtmp'][$imgName]['quality'], true);

							$imgData = '';
							$thumb->getThumb($imgData);

							weFile::save($_serverPath, $imgData);
							$we_size = we_thumbnail::getimagesize($_serverPath);
						}

						$_imgwidth = $we_size[0];
						$_imgheight = $we_size[1];
						//$_type = $_FILES['WE_SF_IMG_DATA']['type'][$imgName];
						$_size = $_FILES['WE_SF_IMG_DATA']['size'][$imgName];

						$imgDocument = new we_imageDocument();

						if($imgId){
							// document has already an image
							// so change binary data
							$imgDocument->initByID($imgId);
						}

						$imgDocument->Filename = $_fileName;
						$imgDocument->Extension = $_extension;
						$imgDocument->Text = $_text;

						if(!$imgId){
							$imgDocument->setParentID($_SESSION['webuser']['imgtmp'][$imgName]['parentid']);
						}

						$imgDocument->Path = $imgDocument->getParentPath() . (($imgDocument->getParentPath() != '/') ? '/' : '') . $imgDocument->Text;

						$imgDocument->setElement('width', $_imgwidth, 'attrib');
						$imgDocument->setElement('height', $_imgheight, 'attrib');
						$imgDocument->setElement('origwidth', $_imgwidth);
						$imgDocument->setElement('origheight', $_imgheight);
						$imgDocument->setElement('type', 'image/*', 'attrib');

						$imgDocument->setElement('data', $_serverPath, 'image');

						$imgDocument->setElement('filesize', $_size, 'attrib');

						$imgDocument->Table = FILE_TABLE;
						$imgDocument->Published = time();
						$imgDocument->WebUserID = $webuserId;
						$imgDocument->we_save();
						$newId = $imgDocument->ID;

						$_SESSION['webuser'][$imgName] = $newId;
						$_REQUEST['s'][$imgName] = $newId;
					}
				}
			}
		}
	}
}

function we_tag_saveRegisteredUser_keepInput(){
	if(isset($_REQUEST['s'])){
		$registered = $_SESSION['webuser']['registered'];
		$_SESSION['webuser'] = $_REQUEST['s'];
		//never set ID + Password
		if(isset($_SESSION['webuser']['ID'])){
			unset($_SESSION['webuser']['ID']);
		}
		if(isset($_SESSION['webuser']['Password'])){
			unset($_SESSION['webuser']['Password']);
		}
		//make sure we stay unregistered!
		$_SESSION['webuser']['registered'] = $registered;
	}
}

function we_tag_saveRegisteredUser_processRequest(){
	$set = array();

	foreach($_REQUEST['s'] as $name => $val){
		switch($name){
			case 'Username': ### QUICKFIX !!!
				$set['Username'] = $val;
				$set['Path'] = '/' . $val;
				$set['Text'] = $val;
				$set['Icon'] = 'customer.gif';
				break;
			case 'Text':
			case 'Path':
			case 'Icon':
			case 'ID':
				break;
			default:
				$set[$name] = $val;
				break;
		}
	}
	return $set;
}
