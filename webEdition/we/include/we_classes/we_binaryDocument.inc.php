<?php

/**
 * webEdition CMS
 *
 * $Rev: 5706 $
 * $Author: mokraemer $
 * $Date: 2013-02-02 18:12:44 +0100 (Sat, 02 Feb 2013) $
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
/*  a class for handling binary-documents like images. */

class we_binaryDocument extends we_document{
	/* The HTML-Code which can be included in a HTML Document */

	protected $html = '';

	/**
	 * Flag which indicates that the doc has changed!
	 * @var boolean
	 */
	public $DocChanged = false;

	/**
	 * @var object instance of metadata reader for accessing metadata functionality
	 */
	private $metaDataReader = null;

	/**
	 * @var array for metadata read via $metaDataReader
	 */
	var $metaData = array();

	/** Constructor
	 * @return we_binaryDocument
	 * @desc Constructor for we_binaryDocument
	 */
	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'html', 'DocChanged');
		array_push($this->EditPageNrs, WE_EDITPAGE_PROPERTIES, WE_EDITPAGE_INFO, WE_EDITPAGE_CONTENT, WE_EDITPAGE_VERSIONS);
		if(defined("CUSTOMER_TABLE")){
			$this->EditPageNrs[] = WE_EDITPAGE_WEBUSER;
		}
		$this->LoadBinaryContent = true;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
				return "we_templates/we_editor_properties.inc.php";
			case WE_EDITPAGE_IMAGEEDIT:
				return "we_templates/we_image_imageedit.inc.php";
			case WE_EDITPAGE_INFO:
				return "we_templates/we_editor_info.inc.php";
			case WE_EDITPAGE_CONTENT:
				return "we_templates/we_editor_binaryContent.inc.php";
			case WE_EDITPAGE_WEBUSER:
				return "we_modules/customer/editor_weDocumentCustomerFilter.inc.php";
			case WE_EDITPAGE_VERSIONS:
				return "we_versions/we_editor_versions.inc.php";
				break;
			default:
				$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PROPERTIES;
				return "we_templates/we_editor_properties.inc.php";
		}
	}

	protected function i_getContentData(){
		parent::i_getContentData(true);
		$_sitePath = $this->getSitePath();
		$_realPath = $this->getRealPath();
		if(!file_exists($_sitePath) && file_exists($_realPath)){
			we_util_File::copyFile($_realPath, $this->getSitePath());
		}
		if(file_exists($_sitePath) && filesize($_sitePath)){
			$this->setElement('data', $_sitePath, 'image');
		}
	}

	public function we_save($resave = 0){
		if(!isset($this->elements['data']['dat'])){
			$this->i_getContentData();
		}
		if($this->getFilesize() == 0){
			print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('metadata', '[file_size_0]'), we_message_reporting::WE_MESSAGE_ERROR));
			return false;
		} else{
			if(parent::we_save($resave)){
				$this->DocChanged = false;
				$this->elements["data"]["dat"] = $this->getSitePath();
				return $this->insertAtIndex();
			} else{
				return false;
			}
		}
	}

	function i_getDocument($size = -1){
		return (isset($this->elements['data']['dat']) && file_exists($this->elements["data"]["dat"])) ? ($size == -1 ? weFile::load($this->elements["data"]["dat"]) : weFile::loadPart($this->elements["data"]["dat"], 0, $size)) : '';
	}

	protected function i_writeDocument(){
		if(isset($this->elements["data"]["dat"]) && file_exists($this->elements["data"]["dat"])){
			if($this->elements["data"]["dat"] != $this->getSitePath()){
				if(!we_util_File::copyFile($this->elements["data"]["dat"], $this->getSitePath())){
					return false;
				}
			}
			if(!we_util_File::copyFile($this->elements["data"]["dat"], $this->getRealPath())){
				return false;
			}
			if($this->i_isMoved()){
				we_util_File::delete($this->getRealPath(true));
				we_util_File::delete($this->getSitePath(true));
				$this->rewriteNavigation();
			}
		} else{
			return false;
		}

		return true;
	}

	private function writeFile($to, $old){
		$is_ok = false;
		if(isset($this->elements["data"]["dat"]) && file_exists($this->elements["data"]["dat"])){
			$is_ok = we_util_File::copyFile($this->elements["data"]["dat"], $to);
			if($this->i_isMoved()){
				we_util_File::delete($old);
			}
		}
		return $is_ok;
	}

	protected function i_writeSiteDir(){
		return $this->writeFile($this->getSitePath(), $this->getSitePath(true));
	}

	protected function i_writeMainDir(){
		return $this->writeFile($this->getRealPath(), $this->getRealPath(true));
	}

	/** gets the filesize of the document */
	function getFilesize(){
		return (file_exists($this->elements['data']['dat']) ? filesize($this->elements['data']['dat']) : 0);
	}

	function insertAtIndex(){
		if(isset($this->IsSearchable) && $this->IsSearchable && $this->Published){
			$text = "";
			$this->resetElements();
			while((list($k, $v) = $this->nextElement(""))) {
				$foo = (isset($v["dat"]) && substr($v["dat"], 0, 2) == "a:") ? unserialize($v["dat"]) : "";
				if(!is_array($foo)){
					if(isset($v["type"]) && $v["type"] == "txt"){
						$text .= ' ' . (isset($v["dat"]) ? $v["dat"] : '');
					}
				}
			}
			$set = array('DID' => intval($this->ID),
				'Text' => $text,
				'Workspace' => $this->ParentPath,
				'WorkspaceID' => intval($this->ParentID),
				'Category' => $this->Category,
				'Doctype' => '',
				'Title' => $this->getElement('Title'),
				'Description' => $this->getElement("Description"),
				'Path' => $this->Path);
			return $this->DB_WE->query('REPLACE INTO ' . INDEX_TABLE . ' SET ' . we_database_base::arraySetter($set));
		}
		$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE DID=' . intval($this->ID));
		return true;
	}

	public function we_new(){
		parent::we_new();
		$this->Filename = $this->i_getDefaultFilename();
	}

	/**
	 * create instance of weMetaData to access metadata functionality:
	 */
	protected function getMetaDataReader($force = false){
		if($force){
			if(!$this->metaDataReader){
				$source = $this->getElement('data');
				if(file_exists($source)){
					$this->metaDataReader = new weMetaData($source);
				}
			}
			return $this->metaDataReader;
		} else{
			return false;
		}
	}

	/**
	 * @abstract tries to read ebmedded metadata from file
	 * @return bool false if either no metadata is available or something went wrong
	 */
	function getMetaData(){
		$_reader = $this->getMetaDataReader();
		if($_reader){
			$this->metaData = $_reader->getMetaData();
			if(!is_array($this->metaData)){
				return false;
			}
		}
		return $this->metaData;
	}

	protected function i_setElementsFromHTTP(){
		// preventing fields from override
		if(isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] == 'update_file'){
			return;
		}
		parent::i_setElementsFromHTTP();
	}

	/**
	 * returns HTML code for embedded metadata of current image with custom form fields
	 */
	function formMetaData(){
		/*
		 * the following steps are to be implemented in this method:
		 * 1. fetch all metadata fields from db
		 * 2. fetch metadata for this image from db (is already done via $this->elements)
		 * 3. render form fields with metadata from db
		 * 4. show button to copy metadata from image into the form fields
		 */
		// first we fetch all defined metadata fields from tblMetadata:
		$_defined_fields = weMetaData::getDefinedMetaDataFields();


		// show an alert if there are none
		if(empty($_defined_fields)){
			return "";
		}

		// second we build all input fields for them and take
		// the elements of this imageDocument as values:
		$_fieldcount = count($_defined_fields);
		$_fieldcounter = (int) 0; // needed for numbering the table rows
		$_content = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0, "style" => "margin-top:4px;"), ($_fieldcount * 2), 5);
		$_mdcontent = "";
		for($i = 0; $i < $_fieldcount; $i++){
			$_tagName = $_defined_fields[$i]["tag"];
			if($_tagName != "Title" && $_tagName != "Description" && $_tagName != "Keywords"){
				$_type = $_defined_fields[$i]["type"];


				switch($_type){

					case "textarea":
						$_inp = $this->formTextArea('txt', $_tagName, $_tagName, 10, 30, ' onChange="_EditorFrame.setEditorIsHot(true);" style="width:508px;height:150px;border: #AAAAAA solid 1px" class="wetextarea"');
						break;

					case "wysiwyg":
						$_inp = $this->formTextArea('txt', $_tagName, $_tagName, 10, 30, ' onChange="_EditorFrame.setEditorIsHot(true);" style="width:508px;height:150px;border: #AAAAAA solid 1px" class="wetextarea"');
						break;

					case "date":
						$_inp = we_html_tools::htmlFormElementTable(
								we_html_tools::getDateInput2('we_' . $this->Name . '_date[' . $_tagName . ']', abs($this->getElement($_tagName)), true), $_tagName
						);
						break;

					default:
						$_inp = $this->formInput2(508, $_tagName, 23, "txt", ' onChange="_EditorFrame.setEditorIsHot(true);"');
				}


				$_content->setCol($_fieldcounter, 0, array("colspan" => 5), $_inp);
				$_fieldcounter++;
				$_content->setCol($_fieldcounter, 0, array("colspan" => 5), we_html_tools::getPixel(1, 5));
				$_fieldcounter++;
			}
		}

		$_mdcontent.=$_content->getHtml();

		// Return HTML
		return $_mdcontent;
	}

	/**
	 * Returns HTML code for Upload Button and infotext
	 */
	function formUpload(){
		$uploadButton = we_button::create_button("upload", "javascript:we_cmd('editor_uploadFile')", true, 150, 22, "", "", false, true, "", true);
		$fs = $GLOBALS['we_doc']->getFilesize();
		$fs = g_l('metadata', "[filesize]") . ": " . round(($fs / 1024), 2) . "&nbsp;KB";
		$_metaData = $this->getMetaData();
		$_mdtypes = array();

		if($_metaData){
			if(isset($_metaData["exif"]) && !empty($_metaData["exif"])){
				$_mdtypes[] = "Exif";
			}
			if(isset($_metaData["iptc"]) && !empty($_metaData["iptc"])){
				$_mdtypes[] = "IPTC";
			}
			if(isset($_metaData["pdf"]) && !empty($_metaData["pdf"])){
				$_mdtypes[] = "PDF";
			}
		}

		$filetype = g_l('metadata', '[filetype]') . ': ' . (empty($this->Extension) ? '' : substr($this->Extension, 1));

		$md = ($_SESSION['weS']['we_mode'] == "seem" ?
				'' :
				g_l('metadata', "[supported_types]") . ': ' .
				'<a href="javascript:parent.frames[0].setActiveTab(\'tab_2\');we_cmd(\'switch_edit_page\',2,\'' . $GLOBALS['we_transaction'] . '\');">' .
				(count($_mdtypes) > 0 ? implode(', ', $_mdtypes) : g_l('metadata', "[none]")) .
				'</a>');


		return '<table cellpadding="0" cellspacing="0" border="0" width="500">
			<tr style="vertical-align:top;"><td class="defaultfont">' .
			$uploadButton . '<br />' .
			$fs . '<br />' .
			$filetype . '<br />' .
			$md . '</td><td width="100px" style="text-align:right;">' .
			$this->getThumbnail() .
			'</td></tr>
			<tr><td colspan="2">' . we_html_tools::getPixel(4, 20) . '</td></tr>
			<tr><td colspan="2" class="defaultfont">' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', ($GLOBALS['we_doc']->getFilesize() != 0 ? "[upload_will_replace]" : "[upload_single_files]")), 1, 508) . '</td></tr>
			</table>';
	}

	function getThumbnail(){
		return '';
	}

	function savebinarydata(){
		$_data = $this->getElement("data");
		if($_data && !file_exists($_data)){
			$_path = weFile::saveTemp($_data);
			$this->setElement('data', $_path);
		}
	}

	public function isBinary(){
		return true;
	}

}
