<?php
/**
 * webEdition CMS
 *
 * $Rev: 5594 $
 * $Author: mokraemer $
 * $Date: 2013-01-19 22:19:42 +0100 (Sat, 19 Jan 2013) $
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
//  This page gives the possibility to check a document via a known web-Service
//  supports w3c (xhtml) and css validation via fileupload.
//  There is also the possibility to check a file via url, this is only possible,
//  when the server is accessible via web

we_html_tools::protect();
we_html_tools::htmlTop();

//  for predefined services include properties file, depending on content-Type
//  and depending on fileending.

if($we_doc->ContentType == 'text/css' || $we_doc->Extension == '.css'){
	include_once(WE_INCLUDES_PATH . 'accessibility/services_css.inc.php');
} else{
	include_once(WE_INCLUDES_PATH . 'accessibility/services_html.inc.php');
}

$services = array();
$js = '';

foreach($validationService AS $_service){
	$services[$_service->art][$_service->category][] = $_service;
}

//  get custom services from database ..
$customServices = validation::getValidationServices('use');

if(!empty($customServices)){
	foreach($customServices as $_cService){
		$services['custom'][$_cService->category][] = $_cService;
	}
}

//  Generate Select-Menu with optgroups
krsort($services);

$_select = '';
$_lastArt = '';
$_lastCat = '';
$_hiddens = '';
$_js = '';
if(!empty($services)){
	$_select = '<select name="service" class="weSelect" style="width:350px;" onchange="switchPredefinedService(this.options[this.selectedIndex].value);">';
	foreach($services as $art => $arr){
		foreach($arr as $cat => $arrServices){
			foreach($arrServices as $service){

				if($_lastArt != $art){
					if($_lastArt != ''){
						$_select .= '</optgroup>';
						$_lastCat = '1';
					}
					$_lastArt = $art;
					$_select .= '<optgroup class="lvl1" label="' . g_l('validation', '[art_' . $art . ']') . '">';
				}
				if($_lastCat != $cat){
					if($_lastCat != ''){
						$_select .= '</optgroup>';
					}
					$_lastCat = $cat;

					$_select .= '<optgroup class="lvl2" label="-- ' . g_l('validation', '[category_' . $cat . ']') . '">';
				}
				$_select .= '<option value="' . $service->getName() . '">' . oldHtmlspecialchars($service->name) . '</option>';
				$js .= '				host["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->host) . '";
                        path["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->path) . '";
                        s_method["' . $service->getName() . '"] = "' . $service->method . '";
                        varname["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->varname) . '";
                        checkvia["' . $service->getName() . '"] = "' . $service->checkvia . '";
                        ctype["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->ctype) . '";
                        additionalVars["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->additionalVars) . '";';
			}
		}
	}
	$_select .= '</optgroup></optgroup></select>';
	$selectedService = $validationService[0];
	$_hiddens = we_html_tools::hidden('host', $selectedService->host) .
		we_html_tools::hidden('path', $selectedService->path) .
		we_html_tools::hidden('ctype', $selectedService->ctype) .
		we_html_tools::hidden('s_method', $selectedService->method) .
		we_html_tools::hidden('checkvia', $selectedService->checkvia) .
		we_html_tools::hidden('varname', $selectedService->varname) .
		we_html_tools::hidden('additionalVars', $selectedService->additionalVars);
} else{
	$_select = g_l('validation', '[no_services_available]');
}

//  css for webSite
print STYLESHEET;

//  js-functions for the select-men�
?>
<script type="text/javascript">


	function we_submitForm(target,url){
		var f = self.document.we_form;
		f.target = target;
		f.action = url;
		f.method = "post";

		f.submit();
	}

	function we_cmd(){

		var args = "";
		var url = "<?php echo WEBEDITION_DIR; ?>we_cmd.php?";

		for(var i = 0; i < arguments.length; i++){
			url += "we_cmd["+i+"]="+escape(arguments[i]);
			if(i < (arguments.length - 1)){
				url += "&";
			}
		}
		switch(arguments[0]){
			case 'checkDocument':
				if(top.weEditorFrameController.getActiveDocumentReference().frames["1"].we_submitForm){
					top.weEditorFrameController.getActiveDocumentReference().frames["1"].we_submitForm("validation",url);
				}
				break;
			default:
				for(var i = 0; i < arguments.length; i++){
					args += 'arguments['+i+']' + ((i < (arguments.length-1)) ? ',' : '');
				}
				eval('parent.we_cmd('+args+')');
				break;
		}
	}

	host = new Array();
	path = new Array();
	varname = new Array();
	checkvia = new Array();
	ctype = new Array();
	s_method = new Array();
	additionalVars = new Array();

<?php print $js; ?>

	function switchPredefinedService(name){

		var f = self.document.we_form;

		f.host.value = host[name];
		f.path.value = path[name];
		f.ctype.value = ctype[name];
		f.varname.value = varname[name];
		f.additionalVars.value = additionalVars[name];
		f.checkvia.value = checkvia[name];
		f.s_method.value = s_method[name];


	}
	function setIFrameSize(){
		var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
		var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
		w = Math.max(w,680);
		var iframeWidth = w - 52;
		var validiframe = document.getElementById("validation");
		validiframe.style.width=iframeWidth;
		if (h) { // h must be set (h!=0), if several documents are opened very fast -> editors are not loaded then => h = 0
			validiframe.style.height=h - 185;
		}
	}

</script>
<?php
print '</head>';

//  generate Body of page
$parts = array(
	array('html' => g_l('validation', '[description]'), 'space' => 0),
	array('headline' => g_l('validation', '[service]'),
		'html' =>
		'<table border="0" cellpadding="0" cellspacing="0">
                                 <tr><td class="defaultfont">' .
		$_select .
		$_hiddens .
		'</td><td>' . we_html_tools::getPixel(20, 5) . '</td><td>' .
		we_button::create_button('edit', 'javascript:we_cmd(\'customValidationService\')', true, 100, 22, "", "", !we_hasPerm("CAN_EDIT_VALIDATION"))
		. '</td><td>' . we_html_tools::getPixel(20, 5) . '</td><td>' .
		we_button::create_button('ok', 'javascript:we_cmd(\'checkDocument\')', true, 100, 22, '', '', (empty($services)))
		. '</td></tr></table>'
		, 'space' => 95),
	array('html' => g_l('validation', '[result]'), 'noline' => 1, 'space' => 0),
	array('html' => '<iframe name="validation" id="validation" src="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=checkDocument" width="680" height="400"></iframe>', 'space' => 5),
);

$body = '<form name="we_form">'
	. we_html_tools::hidden('we_transaction', (isset($_REQUEST['we_transaction']) && preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0))
	. we_multiIconBox::getHTML('weDocValidation', "100%", $parts, 20, '', -1, '', '', false) .
	'</form>';

print we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'setIFrameSize()', 'onresize' => 'setIFrameSize()'), $body) .
	'</html>';