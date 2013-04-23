<?php

/**
 * webEdition CMS
 *
 * $Rev: 5003 $
 * $Author: mokraemer $
 * $Date: 2012-10-23 02:08:54 +0200 (Tue, 23 Oct 2012) $
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
class weGlossarySettingControl{

	function processCommands(){
		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){
				case 'save_glossary_setting':
					$html = ($this->saveSettings() ?
							we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[preferences_saved]'), we_message_reporting::WE_MESSAGE_NOTICE)) :
							we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_glossary', '[preferences_not_saved]'), we_message_reporting::WE_MESSAGE_ERROR)));
					break;
			}

			print we_html_tools::htmlTop() .
				'</head><body>' . $html . '</body></html>';
			exit;
		}
	}

	function processVariables(){

	}

	function saveSettings($default = false){

		if($default){
			$GlossaryAutomaticReplacement = 'false';
		} else{

			$GlossaryAutomaticReplacement = 'false';
			if(isset($_REQUEST['GlossaryAutomaticReplacement']) && $_REQUEST['GlossaryAutomaticReplacement'] == 1){
				$GlossaryAutomaticReplacement = 'true';
			}
		}

		$code = <<<EOF
<?php

\$GLOBALS['weGlossaryAutomaticReplacement'] = {$GlossaryAutomaticReplacement};

EOF;

		return weFile::save(WE_GLOSSARY_MODULE_PATH . weGlossaryReplace::configFile, $code, 'w+');
	}

}
