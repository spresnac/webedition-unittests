<?php

/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/*  a class for handling text-documents */
class we_textDocument extends we_document{
	/* Constructor */

	function __construct(){
		parent::__construct();
		array_push($this->EditPageNrs, WE_EDITPAGE_PROPERTIES, WE_EDITPAGE_INFO, WE_EDITPAGE_CONTENT, WE_EDITPAGE_VALIDATION, WE_EDITPAGE_VERSIONS);
		$this->elements["Charset"]["dat"] = DEFAULT_CHARSET;
		$this->Icon = we_base_ContentTypes::LINK_ICON;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor($baseHref = true){
		$GLOBALS["we_baseHref"] = $baseHref ? getServerUrl(true) . $this->Path : "";

		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
				return "we_templates/we_editor_properties.inc.php";
			case WE_EDITPAGE_INFO:
				return "we_templates/we_editor_info.inc.php";
			case WE_EDITPAGE_CONTENT:
				$GLOBALS["we_editmode"] = true;
				return "we_templates/we_srcTmpl.inc.php";
			case WE_EDITPAGE_PREVIEW:
				if($GLOBALS["we_EDITOR"]){
					$GLOBALS['we_file_to_delete_after_include'] = TEMP_PATH . '/' . weFile::getUniqueId() . $this->Extension;
					we_util_File::saveFile($GLOBALS["we_file_to_delete_after_include"], $this->i_getDocument());
					return $GLOBALS["we_file_to_delete_after_include"];
				} else{
					$GLOBALS["we_editmode"] = false;
					return 'we_templates/we_srcTmpl.inc.php';
				}
			case WE_EDITPAGE_VALIDATION:
				return "we_templates/validateDocument.inc.php";
			case WE_EDITPAGE_VERSIONS:
				return "we_versions/we_editor_versions.inc.php";
			default:
				$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PROPERTIES;
				return "we_templates/we_editor_properties.inc.php";
		}
		return $this->TemplatePath;
	}

	public function we_new(){
		parent::we_new();
		$this->Filename = $this->i_getDefaultFilename();
	}

	function isValidEditPage($editPageNr){
		if($editPageNr == WE_EDITPAGE_VALIDATION){
			return ($this->ContentType == "text/css");
		}

		return parent::isValidEditPage($editPageNr);
	}

}
