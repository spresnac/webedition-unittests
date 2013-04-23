<?php

/**
 * webEdition CMS
 *
 * $Rev: 5746 $
 * $Author: mokraemer $
 * $Date: 2013-02-07 01:04:25 +0100 (Thu, 07 Feb 2013) $
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
class weGlossarySettingFrames{

	var $Frameset = '/webEdition/we/include/we_modules/glossary/edit_glossary_settings_frameset.php';
	var $Controller;
	var $db;

	function __construct(){
		$this->Controller = new weGlossarySettingControl();
		$this->db = new DB_WE();
	}

	function getHTML($what){
		switch($what){
			case 'frameset': print $this->getHTMLFrameset();
				break;
			case 'content': print $this->getHTMLContent();
				break;
			default:
				t_e(__FILE__ . " unknown reference: $what");
		}
	}

	function getHTMLFrameset(){
		return we_html_tools::htmlTop() . '
   <frameset rows="*,0" framespacing="0" border="1" frameborder="Yes">
   <frame src="' . $this->Frameset . '?pnt=content" name="content" scrolling=no>
   <frame src="' . HTML_DIR . 'white.html" name="cmdFrame" scrolling=no noresize>
  </frameset>
</head>
 <body background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
 </body>
</html>';
	}

	function getHTMLContent(){
		$configFile = WE_GLOSSARY_MODULE_PATH . weGlossaryReplace::configFile;
		if(!file_exists($configFile) || !is_file($configFile)){
			weGlossarySettingControl::saveSettings(true);
		}
		include($configFile);

		// Automatic Replacement
		$content = we_forms::checkboxWithHidden($GLOBALS['weGlossaryAutomaticReplacement'], 'GlossaryAutomaticReplacement', g_l('modules_glossary', '[enable_replacement]'));

		$parts = array(
			array(
				'headline' => "",
				'space' => 0,
				'html' => $content,
				'noline' => 1)
		);

		$saveButton = we_button::create_button('save', 'javascript:document.we_form.submit();');
		$closeButton = we_button::create_button('close', 'javascript:top.window.close();');

		return we_html_tools::htmlTop() .
			STYLESHEET .
			we_html_element::jsScript(JS_DIR . 'formFunctions.js') .
			'</head>
<body class="weDialogBody">
	<form name="we_form" target="cmdFrame" action="' . $this->Frameset . '">
	' . we_html_tools::hidden('cmd', 'save_glossary_setting') . '
	' . we_multiIconBox::getHTML('GlossaryPreferences', "100%", $parts, 30, we_button::position_yes_no_cancel($saveButton, null, $closeButton), -1, '', '', false, g_l('modules_glossary', '[menu_settings]')) . '
	</form>
</body>
</html>';
	}

}
