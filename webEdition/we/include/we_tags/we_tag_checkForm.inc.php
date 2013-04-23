<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
function we_parse_tag_checkForm($attribs, $content){
	$arr = array();
	eval('$arr = ' . (PHPLOCALSCOPE ? str_replace('$', '\$', $attribs) : $attribs) . ';'); //Bug #6516
	if(($foo = attributFehltError($arr, 'match', __FUNCTION__)))
		return $foo;
	if(($foo = attributFehltError($arr, 'type', __FUNCTION__)))
		return $foo;

	return '<?php printElement(' . we_tag_tagParser::printTag('checkForm', $attribs, $content, true) . '); ?>';
}

/**
 * @return string
 * @param array $attribs
 * @param string $content
 */
function we_tag_checkForm($attribs, $content){
	//  dont make this in editMode
	if(isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"]){
		return "";
	}

	//  check required Fields
	if(($missingAttrib = attributFehltError($attribs, "match", __FUNCTION__))){
		print $missingAttrib;
		return "";
	}

	if(($missingAttrib = attributFehltError($attribs, "type", __FUNCTION__))){
		print $missingAttrib;
		return "";
	}

	ob_start();
	eval('?>' . $content);
	$content = ob_get_contents();
	ob_end_clean();

	// get fields of $attribs
	$match = weTag_getAttribute("match", $attribs);
	$type = weTag_getAttribute("type", $attribs);
	$mandatory = weTag_getAttribute("mandatory", $attribs);
	$email = weTag_getAttribute("email", $attribs);
	$password = weTag_getAttribute("password", $attribs);
	$onError = weTag_getAttribute("onError", $attribs);
	$jsIncludePath = weTag_getAttribute("jsIncludePath", $attribs);
	$xml = weTag_getAttribute("xml", $attribs);

	//  Generate errorHandler:
	if($onError){
		$jsOnError = '
            if(self.' . $onError . '){
                ' . $onError . '(formular,missingReq,wrongEmail,pwError);
            } else {
            	' . we_message_reporting::getShowMessageCall(
				$content, we_message_reporting::WE_MESSAGE_FRONTEND) . '
            }
        ';
	} else{
		$jsOnError = we_message_reporting::getShowMessageCall($content, we_message_reporting::WE_MESSAGE_FRONTEND);
	}

	//  Generate mandatory array
	if($mandatory){
		$_fields = explode(',', $mandatory);
		$jsMandatory = '//  check mandatory
        var required = new Array("' . implode('", "', $_fields) . '");
        missingReq = weCheckFormMandatory(formular, required);';
	} else{
		$jsMandatory = '';
	}

	if($email){ //  code to check Emails
		$_emails = explode(',', $email);
		$jsEmail = '//  validate emails
        var email = new Array("' . implode('", "', $_emails) . '");
        wrongEmail = weCheckFormEmail(formular, email);';
	} else{
		$jsEmail = '';
	}

	if($password){
		$_pwFields = explode(',', $password);
		if(count($_pwFields) != 3){
			$jsPasword = '';
			return parseError(g_l('parser', '[checkForm_password]'));
		}
		$jsPasword = '//  check passwords
        var password = new Array("' . implode('", "', $_pwFields) . '");
        pwError = weCheckFormPassword(formular, password);
        ';
	} else{
		$jsPasword = '';
	}

	//  deal with alwasy needed stuff - "class weCheckFormEvent"
	if($jsIncludePath){

		if(is_numeric($jsIncludePath)){
			$jsTag = we_tag('js', array('id' => $jsIncludePath, 'xml' => $xml));
			if($jsTag){
				$jsEventHandler = $jsTag;
			} else{
				$jsEventHandler = '';
				return parseError(g_l('parser', '[checkForm_jsIncludePath_not_found]'));
			}
		} else{
			$jsEventHandler = we_html_element::jsScript($jsIncludePath);
		}
	} else{
		$jsEventHandler = we_html_element::jsScript(JS_DIR . 'external/weCheckForm.js');
	}

	switch($type){
		case "id" : //  id of formular is given
			$initFunction = 'weCheckFormEvent.addEvent( window, "load", function(){
        initWeCheckForm_by_id("' . $match . '");
        }
    );';
			$checkFunction = 'function weCheckForm_id_' . $match . '(ev){

        var missingReq = new Array(0);
        var wrongEmail = new Array(0);
        var pwError    = false;

        formular = document.getElementById("' . $match . '");

        ' . $jsMandatory . '

        ' . $jsEmail . '

        ' . $jsPasword . '

        //  return true or false depending on errors
        if( (wrongEmail.length>0) || (missingReq.length>0) || pwError){

            ' . $jsOnError . '
            weCheckFormEvent.stopEvent(ev);
            return false;
        } else {
            return true;
        }
    }
            ';

			$function = we_html_element::jsElement($initFunction . ' ' . $checkFunction);
			break;

		case "name" : //  name of formular is given
			$initFunction = 'weCheckFormEvent.addEvent( window, "load", function(){
        initWeCheckForm_by_name("' . $match . '");
        }
    );';
			$checkFunction = '
    function weCheckForm_n_' . $match . '(ev){
        var missingReq = new Array(0);
        var wrongEmail = new Array(0);
        var pwError    = false;

        formular = document.forms["' . $match . '"];

        ' . $jsMandatory . '

        ' . $jsEmail . '

        ' . $jsPasword . '

        //  return true or false depending on errors
        if( wrongEmail.length || missingReq.length || pwError){

            ' . $jsOnError . '
            weCheckFormEvent.stopEvent(ev);
            return false;
        } else {
            return true;
        }
    }
            ';

			$function = we_html_element::jsElement($initFunction . ' ' . $checkFunction);
			break;
	}

	return $jsEventHandler . $function;
}
