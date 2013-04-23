<?php
/**
 * webEdition CMS
 *
 * $Rev: 5595 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:29:37 +0100 (Sat, 19 Jan 2013) $
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
we_html_tools::protect();
we_html_tools::htmlTop();

//  css for webSite
print STYLESHEET;
?>
<script type="text/javascript"><!--

	function we_cmd(){

		var args = "";
		var url = "<?php echo WEBEDITION_DIR; ?>we_cmd.php?";
		for(var i = 0; i < arguments.length; i++){
			url += "we_cmd["+i+"]="+escape(arguments[i]);
			if(i < (arguments.length - 1)){
				url += "&";
			}
		}

		switch (arguments[0]){

			case "customValidationService":
				self.we_submitForm(url);
				we_cmd("reload_editpage");
				break;
			case "reload_editpage":
				if(top.opener.top.weEditorFrameController.getActiveDocumentReference().frames["1"].we_cmd){
					top.opener.top.weEditorFrameController.getActiveDocumentReference().frames["1"].we_cmd("reload_editpage");
				}
				window.focus();
				break;
			case "close":
				window.close();
				break;
			default :
				for(var i = 0; i < arguments.length; i++){
					args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
				}
				eval('top.opener.we_cmd('+args+')');
				break;
		}
	}

	function we_submitForm(url){

		var f = self.document.we_form;

		f.action = url;
		f.method = "post";

		f.submit();
	}
	//-->
</script>
</head>
<body class="weDialogBody" style="overflow:hidden;">
	<?php
	//  deal with action
	$services = array();

	if(isset($_REQUEST['we_cmd'][1])){

		switch($_REQUEST['we_cmd'][1]){

			case 'saveService':
				$_service = new validationService($_REQUEST['id'], 'custom', $_REQUEST['category'], $_REQUEST['name'], $_REQUEST['host'], $_REQUEST['path'], $_REQUEST['s_method'], $_REQUEST['varname'], $_REQUEST['checkvia'], $_REQUEST['ctype'], $_REQUEST['additionalVars'], $_REQUEST['fileEndings'], $_REQUEST['active']);
				if($selectedService = validation::saveService($_service)){
					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][saved_success]'), we_message_reporting::WE_MESSAGE_NOTICE)
						);
				} else{
					$selectedService = $_service;
					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][saved_failure]') . (isset($GLOBALS['errorMessage']) ? '\n' . $GLOBALS['errorMessage'] : ''), we_message_reporting::WE_MESSAGE_ERROR)
						);
				}
				break;
			case 'deleteService':
				$_service = new validationService($_REQUEST['id'], 'custom', $_REQUEST['category'], $_REQUEST['name'], $_REQUEST['host'], $_REQUEST['path'], $_REQUEST['s_method'], $_REQUEST['varname'], $_REQUEST['checkvia'], $_REQUEST['ctype'], $_REQUEST['additionalVars'], $_REQUEST['fileEndings'], $_REQUEST['active']);
				if(validation::deleteService($_service)){
					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][delete_success]'), we_message_reporting::WE_MESSAGE_NOTICE)
						);
				} else{
					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][delete_failure]'), WE_MESSAGE_ERR
							)
						);
				}
				break;
			case 'selectService';
				$selectedName = $_REQUEST['validationService'];
				break;
			case 'newService':
				$selectedService = new validationService(0, 'custom', 'accessible', g_l('validation', '[edit_service][new]'), 'www.example', '/path', 'get', 'varname', 'url', 'text/html', '', '.html', 1);
				break;
		}
	}

	//  get all custom services from the database - new service select it
	$services = validation::getValidationServices('edit');
	if(isset($_REQUEST['we_cmd'][1]) && $_REQUEST['we_cmd'][1] == 'newService' && $selectedService){
		$services[] = $selectedService;
	}

	if(!empty($services)){
		foreach($services as $service){

			$selectArr[$service->getName()] = htmlentities($service->name);

			if(!isset($selectedService)){
				$selectedService = $service;
			}

			if(isset($selectedName) && $service->getName() == $selectedName){
				$selectedService = $service;
			}
		}
		$hiddenFields = we_html_tools::hidden('id', $selectedService->id) .
			we_html_tools::hidden('art', 'custom');
	} else{
		$hiddenFields = we_html_tools::hidden('art', 'custom');
		$selectArr = array();
	}




	//  table with new and delete
	$_table = '<table>
    <tr><td>' . we_html_tools::htmlSelect('validationService', $selectArr, 5, (isset($selectedService) ? $selectedService->getName() : ''), false, 'onchange=we_cmd(\'customValidationService\',\'selectService\');', "value", 320) . '</td>
        <td>' . we_html_tools::getPixel(10, 2) . '</td>
        <td valign="top">' . we_button::create_button('new_service', 'javascript:we_cmd(\'customValidationService\',\'newService\');')
		. '<div style="height:10px;"></div>'
		. we_button::create_button('delete', 'javascript:we_cmd(\'customValidationService\',\'deleteService\');', true, 100, 22, '', '', (empty($services))) . '
        </td>
    </tr>
    </table>'.
		$hiddenFields;

	$parts = array(
		array('headline' => g_l('validation', '[available_services]'), 'html' => $_table, 'space' => 150)
	);

	if(!empty($services)){
		$parts[] = array('headline' => g_l('validation', '[category]'), 'html' => we_html_tools::htmlSelect('category', validation::getAllCategories(), 1, $selectedService->category), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[service_name]'), 'html' => we_html_tools::htmlTextInput('name', 50, $selectedService->name), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[host]'), 'html' => we_html_tools::htmlTextInput('host', 50, $selectedService->host), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[path]'), 'html' => we_html_tools::htmlTextInput('path', 50, $selectedService->path), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[ctype]'), 'html' => we_html_tools::htmlTextInput('ctype', 50, $selectedService->ctype) . '<br /><span class="small">' . g_l('validation', '[desc][ctype]') . '</span>', 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[fileEndings]'), 'html' => we_html_tools::htmlTextInput('fileEndings', 50, $selectedService->fileEndings) . '<br /><span class="small">' . g_l('validation', '[desc][fileEndings]') . '</span>', 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[method]'), 'html' => we_html_tools::htmlSelect('s_method', array('post' => 'post', 'get' => 'get'), 1, $selectedService->method, false), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[checkvia]'), 'html' => we_html_tools::htmlSelect('checkvia', array('url' => g_l('validation', '[checkvia_url]'), 'fileupload' => g_l('validation', '[checkvia_upload]')), 1, $selectedService->checkvia, false), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[varname]'), 'html' => we_html_tools::htmlTextInput('varname', 50, $selectedService->varname) . '<br /><span class="small">' . g_l('validation', '[desc][varname]') . '</span>', 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[additionalVars]'), 'html' => we_html_tools::htmlTextInput('additionalVars', 50, $selectedService->additionalVars) . '<br /><span class="small">' . g_l('validation', '[desc][additionalVars]') . '</span>', 'space' => 150);
		$parts[] = array('headline' => g_l('validation', '[active]'), 'html' => we_html_tools::htmlSelect('active', array(0 => 'false', 1 => 'true'), 1, $selectedService->active) . '<br /><span class="small">' . g_l('validation', '[desc][active]') . '</span>', 'space' => 150);
	}

	print '<form name="we_form" onsubmit="return false;">' . we_multiIconBox::getHTML('weDocValidation', '100%', $parts, 30, we_button::position_yes_no_cancel(we_button::create_button('save', 'javascript:we_cmd(\'customValidationService\',\'saveService\');', true, 100, 22, '', '', (empty($services))), we_button::create_button('cancel', 'javascript:we_cmd(\'close\');')), -1, '', '', false, g_l('validation', '[adjust_service]'))
		. '</form>' .
		'</body></html>';
