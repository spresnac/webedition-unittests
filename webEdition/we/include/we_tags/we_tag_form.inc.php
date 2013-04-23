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
function we_parse_tag_form($attribs, $content){
	return '<?php if(!isset($GLOBALS[\'we_editmode\']) || !$GLOBALS[\'we_editmode\']){
		printElement(' . we_tag_tagParser::printTag('form', $attribs) . ');}?>' .
		$content .
		'<?php if(!isset($GLOBALS[\'we_editmode\']) || !$GLOBALS[\'we_editmode\']){ echo \'</form>\';unset($GLOBALS[\'WE_FORM\']); if (isset($GLOBALS[\'we_form_action\'])) {unset($GLOBALS[\'we_form_action\']);}}?>';
}

function we_tag_form($attribs){
	$ret = '';
	$method = weTag_getAttribute('method', $attribs, 'post');
	$id = weTag_getAttribute('id', $attribs);
	$action = weTag_getAttribute('action', $attribs);
	$classid = weTag_getAttribute('classid', $attribs);
	$parentid = weTag_getAttribute('parentid', $attribs);
	$doctype = weTag_getAttribute('doctype', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$tid = weTag_getAttribute('tid', $attribs);
	$categories = weTag_getAttribute('categories', $attribs);
	$onsubmit = weTag_getAttribute('onSubmit', $attribs, weTag_getAttribute('onsubmit', $attribs));
	$onsuccess = weTag_getAttribute('onsuccess', $attribs);
	$onerror = weTag_getAttribute('onerror', $attribs);
	$onmailerror = weTag_getAttribute('onmailerror', $attribs);
	$confirmmail = weTag_getAttribute('confirmmail', $attribs);
	$preconfirm = weTag_getAttribute('preconfirm', $attribs);
	$postconfirm = weTag_getAttribute('postconfirm', $attribs);
	$order = weTag_getAttribute('order', $attribs);
	$required = weTag_getAttribute('required', $attribs);
	$remove = weTag_getAttribute('remove', $attribs);
	$subject = weTag_getAttribute('subject', $attribs);
	$recipient = weTag_getAttribute('recipient', $attribs);
	$mimetype = weTag_getAttribute('mimetype', $attribs);
	$from = weTag_getAttribute('from', $attribs);
	$charset = weTag_getAttribute('charset', $attribs);
	$xml = weTag_getAttribute('xml', $attribs);
	$formname = weTag_getAttribute('name', $attribs, 'we_global_form');
	if(array_key_exists('nameid', $attribs)){ // Bug #3153
		$formname = weTag_getAttribute('nameid', $attribs, 'we_global_form');
		$attribs['pass_id'] = weTag_getAttribute('nameid', $attribs);
		unset($attribs['nameid']);
	}
	$onrecipienterror = weTag_getAttribute('onrecipienterror', $attribs);
	$forcefrom = weTag_getAttribute('forcefrom', $attribs);
	$captchaname = weTag_getAttribute('captchaname', $attribs);
	$oncaptchaerror = weTag_getAttribute('oncaptchaerror', $attribs);
	$enctype = weTag_getAttribute('enctype', $attribs);
	$target = weTag_getAttribute('target', $attribs);
	$formAttribs = removeAttribs($attribs, array(
		'onsubmit', 'onSubmit', 'name', 'method', 'xml', 'charset', 'id', 'action',
		'order', 'required', 'onsuccess', 'onerror', 'type', 'recipient', 'mimetype',
		'subject', 'onmailerror', 'preconfirm', 'postconfirm', 'from', 'confirmmail',
		'classid', 'doctype', 'remove', 'onrecipienterror', 'tid', 'forcefrom', 'categories'
	));

	$formAttribs['xml'] = $xml;
	$formAttribs['method'] = $method;

	if($id){
		$GLOBALS['we_form_action'] = ($id == 'self' ? (defined('WE_REDIRECTED_SEO') ? WE_REDIRECTED_SEO : $_SERVER['SCRIPT_NAME']) : f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'Path', $GLOBALS['DB_WE']));
	} else{
		$GLOBALS['we_form_action'] = ($action ? $action : $_SERVER['SCRIPT_NAME']);
	}
	if($type != 'search'){
		$regs = array();
		if(preg_match('/^(.*)return (.+)$/i', $onsubmit, $regs)){
			$onsubmit = $regs[1] . ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return ' . $regs[2];
		} else{
			$onsubmit .= ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return true;';
		}
	}
	switch($type){
		case 'shopliste' :
			$formAttribs['action'] = $GLOBALS['we_form_action'];
			$formAttribs['name'] = 'form' . ((isset($GLOBALS['lv']) && isset($GLOBALS['lv']->IDs[$GLOBALS['lv']->count - 1]) && strlen($GLOBALS['lv']->IDs[$GLOBALS['lv']->count - 1])) ? $GLOBALS['lv']->IDs[$GLOBALS['lv']->count - 1] : $GLOBALS['we_doc']->ID);
			if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
				$ret = getHtmlTag(
						'form', $formAttribs, '', false, true) . getHtmlTag(
						'input', array(
						'xml' => $xml,
						'type' => 'hidden',
						'name' => 'type',
						'value' => ( isset($GLOBALS['lv']->classID) ? 'o' : (isset($GLOBALS['lv']->ID) ? 'w' : (isset($GLOBALS['we_doc']->ClassID) || isset($GLOBALS['we_doc']->ObjectID)) ? 'o' : 'w' )),
					)) . getHtmlTag(
						'input', array(
						'xml' => $xml,
						'type' => 'hidden',
						'name' => 'shop_artikelid',
						'value' => (isset($GLOBALS['lv']->classID) || isset($GLOBALS['we_doc']->ClassID) || isset($GLOBALS['we_doc']->ObjectID) ?
							((isset($GLOBALS['lv']) && $GLOBALS['lv']->getDBf('OF_ID') != '') ?
								$GLOBALS['lv']->getDBf('OF_ID') :
								($GLOBALS['we_doc']->getDBf('OF_ID') ?
									$GLOBALS['we_doc']->getDBf('OF_ID') :
									(isset($GLOBALS['we_doc']->OF_ID) ?
										$GLOBALS['we_doc']->OF_ID :
										$GLOBALS['we_doc']->ID))) :
							((isset($GLOBALS['lv']) && isset($GLOBALS['lv']->IDs[$GLOBALS['lv']->count - 1]) && $GLOBALS['lv']->IDs[$GLOBALS['lv']->count - 1] != '') ?
								$GLOBALS['lv']->IDs[$GLOBALS['lv']->count - 1] :
								$GLOBALS['we_doc']->ID) ),
					)) . getHtmlTag(
						'input', array(
						'xml' => $xml,
						'type' => 'hidden',
						'name' => 'we_variant',
						'value' => (isset($GLOBALS['we_doc']->Variant) ? $GLOBALS['we_doc']->Variant : ''),
					)) . getHtmlTag(
						'input', array(
						'xml' => $xml,
						'type' => 'hidden',
						'name' => 't',
						'value' => time(),
				));
			}
			break;
		case 'object' :
		case 'document' :
			if(!isset($_REQUEST['edit_' . $type])){
				if(isset($GLOBALS['WE_SESSION_START']) && $GLOBALS['WE_SESSION_START']){
					unset($_SESSION['we_' . $type . '_session_' . $formname]);
				}
			}

			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['name'] = $formname;
			$formAttribs['action'] = $GLOBALS['we_form_action'];

			if($enctype){
				$formAttribs['enctype'] = $enctype;
			}
			if($target){
				$formAttribs['target'] = $target;
			}
			if($classid || $doctype){
				$GLOBALS['WE_FORM'] = $formname;
				if(!$GLOBALS['we_doc']->InWebEdition){
					if($type == 'object'){
						we_objectFile::initObject($classid, $formname, $categories, $parentid);
					} else{
						we_webEditionDocument::initDocument($formname, $tid, $doctype, $categories);
					}
				}
				$typetmp = (($type == 'object') ? 'Object' : 'Document');

				if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
					$ret.=getHtmlTag(
							'form', $formAttribs, '', false, true) . getHtmlTag(
							'input', array(
							'type' => 'hidden', 'name' => 'edit_' . $type, 'value' => 1, 'xml' => $xml
						)) . getHtmlTag(
							'input', array(
							'type' => 'hidden',
							'name' => 'we_edit' . $typetmp . '_ID',
							'value' => isset($_REQUEST['we_edit' . $typetmp . '_ID']) ? intval($_REQUEST['we_edit' . $typetmp . '_ID']) : 0,
							'xml' => $xml
					));
				}
			} else{
				if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
					$ret.=getHtmlTag('form', $formAttribs, '', false, true);
				}
			}
			break;
		case 'formmail' :
			$successpage = $onsuccess ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onsuccess), 'Path', $GLOBALS['DB_WE']) : '';
			$errorpage = $onerror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onerror), 'Path', $GLOBALS['DB_WE']) : '';
			$mailerrorpage = $onmailerror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onmailerror), 'Path', $GLOBALS['DB_WE']) : '';
			$recipienterrorpage = $onrecipienterror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onrecipienterror), 'Path', $GLOBALS['DB_WE']) : '';
			$captchaerrorpage = $oncaptchaerror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($oncaptchaerror), 'Path', $GLOBALS['DB_WE']) : '';

			$confirmmail = ($confirmmail == 'true');
			$preconfirm = $confirmmail && $preconfirm ? str_replace("'", "\\'", $GLOBALS['we_doc']->getElement($preconfirm)) : '';
			$postconfirm = $confirmmail && $postconfirm ? str_replace("'", "\\'", $GLOBALS['we_doc']->getElement($postconfirm)) : '';
			if($enctype){
				$formAttribs['enctype'] = $enctype;
			}
			if($target){
				$formAttribs['target'] = $target;
			}

			$formAttribs['name'] = $formname;
			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['action'] = WEBEDITION_DIR . 'we_formmail.php';
			if($id){
				$formAttribs['action'] = ($id == 'self' ? $_SERVER['SCRIPT_NAME'] : f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'Path', $GLOBALS['DB_WE']));
			}


			//  now prepare all needed hidden-fields:
			if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
				$ret.=getHtmlTag('form', $formAttribs, '', false, true);
				$_recipientString = $recipient;
				$_recipientArray = explode(',', $_recipientString);
				foreach($_recipientArray as $_key => $_val){
					$_recipientArray[$_key] = '"' . trim($_val) . '"';
				}
				$_recipientString = implode(',', $_recipientArray);

				$_ids = array();
				$GLOBALS['DB_WE']->query('SELECT ID FROM ' . RECIPIENTS_TABLE . ' WHERE Email IN(' . $_recipientString . ')');
				while($GLOBALS['DB_WE']->next_record()) {
					$_ids[] = $GLOBALS['DB_WE']->f('ID');
				}

				$_recipientIdString = '';
				if(!empty($_ids)){
					$_recipientIdString = implode(',', $_ids);
				}

				$ret.='<div class="weHide" style="display: none;">' .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'order',
						'value' => $order,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'required',
						'value' => $required,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'subject',
						'value' => $subject,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'recipient',
						'value' => $_recipientIdString,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'mimetype',
						'value' => $mimetype,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'from',
						'value' => $from,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden', 'name' => 'error_page', 'value' => $errorpage, 'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'mail_error_page',
						'value' => $mailerrorpage,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'recipient_error_page',
						'value' => $recipienterrorpage,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden', 'name' => 'ok_page', 'value' => $successpage, 'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'charset',
						'value' => $charset,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'confirm_mail',
						'value' => $confirmmail,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'pre_confirm',
						'value' => $preconfirm,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'post_confirm',
						'value' => $postconfirm,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden', 'name' => 'we_remove', 'value' => $remove, 'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden', 'name' => 'forcefrom', 'value' => $forcefrom, 'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'captcha_error_page',
						'value' => $captchaerrorpage,
						'xml' => $xml
					)) .
					getHtmlTag(
						'input', array(
						'type' => 'hidden',
						'name' => 'captchaname',
						'value' => $captchaname,
						'xml' => $xml
					)) .
					'</div>';
			}
			break;
		default :
			if($enctype){
				$formAttribs['enctype'] = $enctype;
			}
			if($target){
				$formAttribs['target'] = $target;
			}
			$formAttribs['name'] = $formname;
			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['action'] = $GLOBALS['we_form_action'];


			if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
				$ret.=getHtmlTag('form', $formAttribs, '', false, true);
			}
	}
	return $ret;
}