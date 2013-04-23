<?php

/**
 * webEdition CMS
 *
 * $Rev: 5940 $
 * $Author: mokraemer $
 * $Date: 2013-03-11 15:24:27 +0100 (Mon, 11 Mar 2013) $
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

/**
 * class for handling image documents
 */
class we_imageDocument extends we_binaryDocument{

	/**
	 * Comma separated value of IDs from THUMBNAILS_TABLE  This value is not stored in DB!!
	 * @var string
	 */
	var $Thumbs = -1;

	/**
	 * Constructor of we_imageDocument
	 *
	 * @return we_imageDocument
	 */
	function __construct(){
		parent::__construct();
		$this->persistent_slots[] = 'Thumbs';
		$this->Icon = we_base_ContentTypes::IMAGE_ICON;
		$this->ContentType = 'image/*';
		array_push($this->EditPageNrs, WE_EDITPAGE_IMAGEEDIT, WE_EDITPAGE_THUMBNAILS);
		/* 		if(defined("CUSTOMER_TABLE")){
		  array_push($this->EditPageNrs, WE_EDITPAGE_WEBUSER);
		  } */
	}

	/**
	 * saves the data of the document
	 *
	 * @return boolean
	 * @param boolean $resave
	 */
	public function we_save($resave = 0){
		// get original width and height of the image
		$arr = $this->getOrigSize(true, true);
		$this->setElement('origwidth', isset($arr[0]) ? $arr[0] : 0);
		$this->setElement('origheight', isset($arr[1]) ? $arr[1] : 0);
		$docChanged = $this->DocChanged; // will be reseted in parent::we_save()
		if(parent::we_save($resave)){
			$thumbs = $this->getThumbs();
			if($docChanged){
				we_thumbnail::deleteByImageID($this->ID);
			}
			if(!empty($thumbs)){
				foreach($thumbs as $thumbID){
					$thumbObj = new we_thumbnail();
					$thumbObj->initByThumbID($thumbID, $this->ID, $this->Filename, $this->Path, $this->Extension, $this->getElement('origwidth'), $this->getElement('origheight'), $this->getDocument());
					if(($docChanged || !$thumbObj->exists()) && ($thumbObj->createThumb() == we_thumbnail::BUILDERROR)){
						t_e('Error creating thumbnail for file', $this->Filename . $this->Extension);
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Calculates the original image size of the image.
	 * Returns an array like the PHP function getimagesize().
	 * If the array is empty the image is not uploaded or an error occured
	 *
	 * @param boolean $calculateNew
	 * @return array
	 */
	function getOrigSize($calculateNew = false, $useOldPath = false){
		$arr = array(0, 0, 0, '');
		if(!$this->DocChanged && $this->ID){
			if($this->getElement('origwidth') && $this->getElement('origheight') && ($calculateNew == false)){
				return array($this->getElement('origwidth'), $this->getElement('origheight'), 0, '');
			} else{
				// we have to calculate the path, because maybe the document was renamed
				$path = $this->getParentPath() . '/' . $this->Filename . $this->Extension;
				return we_thumbnail::getimagesize($_SERVER['DOCUMENT_ROOT'] . (($useOldPath && $this->OldPath) ? $this->OldPath : $this->Path));
			}
		} else if(isset($this->elements['data']['dat']) && $this->elements['data']['dat']){
			$arr = we_thumbnail::getimagesize($this->elements['data']['dat']);
		}
		return $arr;
	}

	/**
	 * Returns an array with the Thumbnail IDs for the image.
	 *
	 * @return array
	 */
	function getThumbs(){
		$thumbs = array();
		if($this->Thumbs == -1){
			$this->DB_WE->query('SELECT * FROM ' . THUMBNAILS_TABLE);
			$thumbObj = new we_thumbnail();

			while($this->DB_WE->next_record()) {
				$thumbObj->init($this->DB_WE->f('ID'), $this->DB_WE->f('Width'), $this->DB_WE->f('Height'), $this->DB_WE->f('Ratio'), $this->DB_WE->f('Maxsize'), $this->DB_WE->f('Interlace'), false, $this->DB_WE->f('Format'), $this->DB_WE->f('Name'), $this->ID, $this->Filename, $this->Path, $this->Extension, $this->getElement('origwidth'), $this->getElement('origheight'), $this->DB_WE->f('Quality'));

				if(file_exists($_SERVER['DOCUMENT_ROOT'] . $thumbObj->getOutputPath()) && $thumbObj->getOutputPath() != $this->Path){
					$thumbs[] = $this->DB_WE->f('ID');
				}
			}

			$this->Thumbs = makeCSVFromArray($thumbs, true);
		} else{
			$thumbs = makeArrayFromCSV($this->Thumbs);
		}
		return $thumbs;
	}

	/**
	 * returns the path for the template to be included
	 *
	 * @return string
	 */
	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_THUMBNAILS:
				return 'we_templates/we_editor_thumbnails.inc.php';

			default:
				return parent::editor();
		}
	}

	/**
	 * adds thumbnails to the image document
	 *
	 * @return void
	 * @param string $thumbnails
	 */
	function add_thumbnails($thumbnails){
		$thumbsToAdd = makeArrayFromCSV($thumbnails);
		$thumbsArray = ($this->Thumbs == -1) ? array() : makeArrayFromCSV($this->Thumbs);

		foreach($thumbsToAdd as $t){
			if(!in_array($t, $thumbsArray)){
				$thumbsArray[] = $t;
			}
		}

		$this->Thumbs = makeCSVFromArray($thumbsArray, true);
		$this->DocChanged = true;
	}

	/**
	 * deletes a thumbnail from the image document
	 *
	 * @return void
	 * @param int $thumbnailID
	 */
	function del_thumbnails($thumbnailID){
		$thumbsArray = ($this->Thumbs == -1) ? array() : makeArrayFromCSV($this->Thumbs);
		$newArray = array();

		foreach($thumbsArray as $t){
			if($t != $thumbnailID){
				array_push($newArray, $t);
			}
		}

		$this->Thumbs = makeCSVFromArray($newArray, true);
		$this->DocChanged = true;
	}

	/**
	 * sets extra attributes for the image
	 *
	 * @return void
	 * @param array $attribs
	 */
	function initByAttribs($attribs){
		foreach($attribs as $a => $b){
			if(strtolower($a) != 'id' && $b != ''){
				$this->setElement($a, $b, 'attrib');
			}
		}
	}

	/**
	 * returns the javascript for the rollover function
	 *
	 * @return string
	 * @param string $src
	 * @param string $src_over
	 */
	function getRollOverScript($src = '', $src_over = '', $useScript = true){
		if(!$this->getElement('RollOverFlag')){
			return '';
		}
		if(!$src){
			$src = (we_isHttps() ? '' : BASE_IMG) . $this->Path;
		}

		if(!$src_over){
			$src_over = (we_isHttps() ? '' : BASE_IMG) . f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID = ' . intval($this->getElement('RollOverID')), 'Path', $this->DB_WE);
		}

		if(!$this->getElement('name')){
			$this->setElement('name', 'ro_' . $this->Name, 'attrib');
		}

		$js = '
we' . $this->getElement('name') . 'Over = new Image();
we' . $this->getElement('name') . 'Out = new Image();
we' . $this->getElement('name') . 'Over.src = "' . $src_over . '";
we' . $this->getElement('name') . 'Out.src = "' . $src . '";';
		return ($useScript ? we_html_element::jsElement($js) : $js);
	}

	/**
	 * @return array
	 * @desc returns the rollover attribs as array
	 */
	function getRollOverAttribsArr(){
		if($this->getElement('RollOverFlag')){
			return array(
				'onmouseover' => 'if (this.firstChild) { this.firstChild.src = we' . $this->getElement('name') . 'Over.src; }',
				'onmouseout' => 'if (this.firstChild) { this.firstChild.src = we' . $this->getElement('name') . 'Out.src;}',
			);
		} else{
			return array();
		}
	}

	/**
	 * resizes the image with the new $width & $height
	 *
	 * @return void
	 * @param int $width
	 * @param int $height
	 * @param int $quality
	 * @param bool $ratio
	 */
	function resizeImage($width, $height, $quality = 8, $ratio = false){
		if(!is_numeric($quality)){
			return false;
		}
		$quality = ($quality > 10 ? 10 : ($quality < 0 ? 0 : $quality)) * 10;
		$dataPath = TEMP_PATH . '/' . weFile::getUniqueId();
		$_resized_image = we_image_edit::edit_image($this->getElement('data'), $this->getGDType(), $dataPath, $quality, $width, $height, $ratio);
		if(!$_resized_image[0]){
			return false;
		}
		$this->setElement('data', $dataPath);

		$this->setElement('width', $_resized_image[1], 'attrib');
		$this->setElement('origwidth', $_resized_image[1], 'attrib');

		$this->setElement('height', $_resized_image[2], 'attrib');
		$this->setElement('origheight', $_resized_image[2], 'attrib');

		$this->DocChanged = true;
		return true;
	}

	/**
	 * rotates the image with the new $width, $height and rotation angle
	 *
	 * @return void
	 * @param int $width
	 * @param int $height
	 * @param int $rotation
	 * @param int $quality
	 */
	function rotateImage($width, $height, $rotation, $quality = 8){
		if(!is_numeric($quality)){
			return false;
		}
		$quality = max(min($quality, 10), 0) * 10;

		$dataPath = TEMP_PATH . '/' . weFile::getUniqueId();
		$_resized_image = we_image_edit::edit_image($this->getElement('data'), $this->getGDType(), $dataPath, $quality, $width, $height, false, true, 0, 0, -1, -1, $rotation);

		if(!$_resized_image[0]){
			return false;
		}
		$this->setElement('data', $dataPath);

		$this->setElement('width', $_resized_image[1]);
		$this->setElement('origwidth', $_resized_image[1], 'attrib');

		$this->setElement('height', $_resized_image[2]);
		$this->setElement('origheight', $_resized_image[2], 'attrib');

		$this->DocChanged = true;
		return true;
	}

	/**
	 * gets the HTML for including in HTML-Docs.
	 * If a thumbnail should displayed and it doesn't exists,
	 * it will be created automatically
	 *
	 * @return string
	 * @param boolean $dyn
	 * @param string $inc_href
	 */
	function getHtml($dyn = false, $inc_href = true, $pathOnly=false){
		$_data = $this->getElement('data');
		if($this->ID || ($_data && !is_dir($_data) && is_readable($_data))){
			switch($this->getElement('LinkType')){
				case 'int':
					$href = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID = ' . intval($this->getElement('LinkID')), 'Path', $this->DB_WE);
					break;
				case 'ext':
					$href = $this->getElement('LinkHref');
					break;
				case 'obj':
					$id = $this->getElement('ObjID');
					if(isset($GLOBALS['WE_MAIN_DOC'])){
						$pid = $GLOBALS['WE_MAIN_DOC']->ParentID;
					} else{
						$pidCvs = f('SELECT Workspaces FROM ' . OBJECT_FILES_TABLE . ' WHERE ID = ' . intval($id), 'Workspaces', $this->DB_WE);
						$foo = makeArrayFromCSV($pidCvs);
						$pid = (empty($foo) ? 0 : $foo[0]);
					}

					$path = isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC']->Path : '';
					$href = getHrefForObject($this->getElement('ObjID'), $pid, $path, $this->DB_WE);
					if(isset($GLOBALS['we_link_not_published'])){
						unset($GLOBALS['we_link_not_published']);
					}
					break;
			}

			$img_path = $this->Path;


			// we need to create a thumbnail - check if image exists
			if(($thumbname = $this->getElement('thumbnail')) && ($img_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path))){
				$thumbObj = new we_thumbnail();
				if($thumbObj->initByThumbName($thumbname, $this->ID, $this->Filename, $this->Path, $this->Extension, 0, 0)){
					$img_path = $thumbObj->getOutputPath();

					if($thumbObj->isOriginal()){
//						$create = false;
					} elseif((!$thumbObj->isOriginal()) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path) &&
						// open a file
						intval(filectime($_SERVER['DOCUMENT_ROOT'] . $img_path)) > intval($thumbObj->getDate())){
//						$create = false;
					} else{
						$thumbObj->createThumb();
					}


					$this->setElement('width', $thumbObj->getOutputWidth(), 'attrib');
					$this->setElement('height', $thumbObj->getOutputHeight(), 'attrib');
				}
			}

			if($pathOnly){
				return $img_path;
			}

			$target = $this->getElement('LinkTarget');

			srand((double) microtime() * 1000000);
			$randval = rand();
			$src = $dyn ?
				WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=show_binaryDoc&we_cmd[1]=' .
				$this->ContentType . '&we_cmd[2]=' .
				$GLOBALS['we_transaction'] . '&rand=' . $randval :
				($this->getElement('LinkType') == 'int' ? (we_isHttps() ? '' : BASE_IMG) : '') .
				$img_path;

			if(isset($this->elements['sizingrel'])){
				$this->setElement('width', round($this->elements['width']['dat'] * $this->elements['sizingrel']['dat']), 'attrib');
				$this->setElement('height', round($this->elements['height']['dat'] * $this->elements['sizingrel']['dat']), 'attrib');
				unset($this->elements['sizingrel']);
			}

			$sizingbase = (isset($this->elements['sizingbase']) && $this->elements['sizingbase']['dat'] != 16 ? $this->elements['sizingbase']['dat'] : 16);

			if(isset($this->elements['sizingbase'])){
				unset($this->elements['sizingbase']);
			}

			if(isset($this->elements['sizingstyle'])){
				$sizingstyle = ($this->elements['sizingstyle']['dat'] == 'none' ? false : $this->elements['sizingstyle']['dat']);
				unset($this->elements['sizingstyle']);
			} else{
				$sizingstyle = false;
			}

			if($sizingstyle){
				$style_width = round($this->elements['width']['dat'] / $sizingbase, 6);
				$style_height = round($this->elements['height']['dat'] / $sizingbase, 6);
				$newstyle = (isset($this->elements['style']) ? $this->elements['style']['dat'] : '');

				$newstyle.=';width:' . $style_width . $sizingstyle . ';height:' . $style_height . $sizingstyle . ';';
				$this->setElement('style', $newstyle, 'attrib');
				unset($this->elements['width']);
				unset($this->elements['height']);
			}

			$this->resetElements();

			//  Here we generate the image-tag
			//   attribs for the image tag
			$attribs = array(
				'src' => $src
			);

			$filter = array('filesize', 'type', 'id', 'showcontrol', 'showthumbcontrol', 'thumbnail', 'href', 'longdescid', 'showimage', 'showinputs', 'listviewname', 'parentid', 'startid'); //  dont use these array-entries

			if(defined('HIDENAMEATTRIBINWEIMG_DEFAULT') && HIDENAMEATTRIBINWEIMG_DEFAULT){
				$filter[] = 'name';
			}

			// check longdesc here - does file exist?
			if($this->getElement('longdescid') && $this->getElement('longdescid') != '-1'){
				$longdesc = id_to_path($this->getElement('longdescid'));
				$attribs['longdesc'] = $longdesc;
			}


			// $attribs['Title'] changed to $attribs['title'] to fix bug #5814
			if($this->getElement('useMetaTitle') && $this->getElement('Title') != ''){ //  set title if set in image
				$attribs['title'] = $this->getElement('Title');
			}

			if(($this->getElement('alt') == '')){ //  always use alt-Text -> can be empty
				$attribs['alt'] = '';
			}

			while((list($k, $v) = $this->nextElement('attrib'))) {
				if(!in_array($k, $filter)){
					if(isset($v['dat']) && $v['dat'] != ''){
						$attribs[$k] = $v['dat'];
					}
				}
			}

//  If needed - js output for rollover.

			if(isset($attribs['only'])){
				$this->html = (array_key_exists($attribs['only'], $attribs) ? $attribs[$attribs['only']] : '');
				return $this->html;
			} else if(isset($attribs['pathonly']) && $attribs['pathonly']){
				$this->html = $attribs['src'];
				return $this->html;
			}

			if((isset($href) && $href) && (isset($inc_href) && $inc_href)){ //  use link with rollover
				$_aAtts['href'] = $href;
				if($target){
					$_aAtts['target'] = $target;
				}
				if(isset($attribs['xml'])){
					$_aAtts['xml'] = $attribs['xml'];
				}

				$ro_script = $this->getRollOverScript($src); //  has to be called first!
				$_roAttribs = $this->getRollOverAttribsArr();

				$_aAtts = array_merge($_aAtts, $_roAttribs);

				$this->html = ( trim($ro_script) . getHtmlTag('a', $_aAtts, getHtmlTag('img', $attribs)) );
			} else{
				$this->html = (defined('WE_EDIT_IMAGE')) ?
					we_image_crop::getJS() . we_image_crop::getCSS() . we_image_crop::getCrop($attribs) :
					getHtmlTag('img', $attribs);
			}
		} else{
			if($pathOnly){
				//be compatible
				return '';
			}
			$xml = isset($attribs) ? weTag_getAttribute('xml', $attribs, false, true) : true; //rest is done in getHtmlTag
			$attribs = array('style' => 'margin:8px 18px;border-style:none;width:64px;height:64px;',
				'src' => IMAGE_DIR . 'icons/no_image.gif',
				'alt' => 'no-image',
				'xml' => $xml,
			);
			if(isset($this->name)){
				$attribs['name'] = $this->name;
			}

			$this->html = getHtmlTag('img', $attribs);
		}

		return $this->html;
	}

	/**
	 * function will determine the size of any GIF, JPG, PNG.
	 * This function uses the php Function with the same name.
	 * But the php function doesn't work with some images created from some apps.
	 * So this function uses the gd lib if nothing is returned from the php function
	 *
	 * @static
	 * @return array
	 * @param $filename complete path of the image
	 */
	function getimagesize($filename){
		return we_thumbnail::getimagesize($filename);
	}

	/**
	 * Overwrites formInput2() in we:class.inc:
	 * Method adds parameter $text, which is used only if field-name in db and field-name in language files are different
	 *
	 * @return string
	 */
	function formInput2($width, $name, $size = 25, $type = 'txt', $attribs = '', $text = ''){
		$text = $text == '' ? $name : $text;
		return $this->formInputField($type, $name, (g_l('weClass', '[' . $text . ']', true) != false ? g_l('weClass', '[' . $text . ']') : $text), $size, $width, '', $attribs);
	}

	/**
	 * Returns the HTML for the properties part in the properties view
	 *
	 * @return string
	 */
	function formProperties(){
		// Create table
		$_content = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 12, 5);

		// Row 1
		$_content->setCol(0, 0, null, $this->formInputInfo2(155, 'width', 10, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"', "origwidth"));
		$_content->setCol(0, 2, null, $this->formInputInfo2(155, 'height', 10, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"', "origheight"));
		$_content->setCol(0, 4, null, $this->formInput2(155, 'border', 10, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"'));

		$_content->setCol(0, 1, null, we_html_tools::getPixel(18, 1));
		$_content->setCol(0, 3, null, we_html_tools::getPixel(18, 1));

		// Row 2
		$_content->setCol(1, 0, array('colspan' => 5), we_html_tools::getPixel(1, 5));

		// Row 3
		$_content->setCol(2, 0, null, $this->formInput2(155, 'align', 10, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"'));
		$_content->setCol(2, 2, null, $this->formInput2(155, 'hspace', 10, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"'));
		$_content->setCol(2, 4, null, $this->formInput2(155, 'vspace', 10, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"'));

		$_content->setCol(2, 1, null, we_html_tools::getPixel(18, 1));
		$_content->setCol(2, 3, null, we_html_tools::getPixel(18, 1));

		// Row 4
		$_content->setCol(3, 0, array('colspan' => 5), we_html_tools::getPixel(1, 5));

		// Row 5
		$_content->setCol(4, 0, array('colspan' => 3), $this->formInput2(328, 'alt', 23, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"'));
		$_content->setCol(4, 3, null, we_html_tools::getPixel(18, 1));
		$_content->setCol(4, 4, null, $this->formInput2(155, 'name', 10, 'attrib', 'onChange="_EditorFrame.setEditorIsHot(true);"'));

		// Row 6
		$_content->setCol(5, 0, array('colspan' => 5), we_html_tools::getPixel(1, 5));

		//	Row 7
		$_content->setCol(6, 0, array('colspan' => 3), $this->formInput2(328, 'title', 23, 'attrib', ($this->getElement('useMetaTitle') == 1 ? "readonly='readonly'" : "") . '" onChange="_EditorFrame.setEditorIsHot(true);"', 'Title'));

		$_content->setCol(6, 3, null, we_html_tools::getPixel(18, 1));
		$_titleField = 'we_' . $this->Name . '_attrib[title]';
		$_metaTitleField = 'we_' . $this->Name . '_txt[Title]';
		$useMetaTitle = 'we_' . $this->Name . '_txt[useMetaTitle]';
		//	disable field 'title' when checked or not.   on checked true: document.forms[0]['$_titleField'].value='$this->getElement('Title')' and  onchecked false: document.forms[0]['$_titleField'].value='' added to fix bug #5814
		$_content->setCol(6, 4, array('valign' => 'bottom'), we_forms::checkboxWithHidden($this->getElement('useMetaTitle'), $useMetaTitle, g_l('weClass', '[use_meta_title]'), false, 'defaultfont', "if(this.checked){ document.forms[0]['$_titleField'].setAttribute('readonly', 'readonly', 'false'); document.forms[0]['$_titleField'].value = '" . $this->getElement('Title') . "'; }else{ document.forms[0]['$_titleField'].removeAttribute('readonly', 'false'); document.forms[0]['$_titleField'].value='';}_EditorFrame.setEditorIsHot(true);"));

		//  longdesc should be available in images.
		//    check if longdesc is set and get path
		$longdesc_id_name = 'we_' . $this->Name . '_attrib[longdescid]';
		$longdesc_text_name = 'tmp_longdesc';
		$longdesc_id = $this->getElement('longdescid');
		$longdescPath = ($longdesc_id ? id_to_path($longdesc_id) : '');

		$yuiSuggest = & weSuggest::getInstance();
		$yuiSuggest->setAcId('LonDesc');
		$yuiSuggest->setContentType('folder,text/webEdition,text/html');
		$yuiSuggest->setInput($longdesc_text_name, $longdescPath);
		$yuiSuggest->setLabel(g_l('weClass', '[longdesc_text]'));
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($longdesc_id_name, $longdesc_id);
		$yuiSuggest->setSelector('Docselector');
		$yuiSuggest->setWidth(328);
		//javascript:we_cmd('openDocselector',document.we_form.elements['$longdesc_id_name'].value,'" . FILE_TABLE . "','document.we_form.elements[\\'$longdesc_id_name\\'].value','document.we_form.elements[\\'$longdesc_text_name\\'].value','opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd(\'reload_editpage\');','".session_id()."','','text/webedition,text/plain,text/html',1)
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$longdesc_id_name'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$longdesc_text_name'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('reload_editpage');");

		$yuiSuggest->setSelectButton(we_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['$longdesc_id_name'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','','text/webedition,text/plain,text/html',1)"));
		$yuiSuggest->setTrashButton(we_button::create_button('image:btn_function_trash', "javascript:document.we_form.elements['$longdesc_id_name'].value='-1';document.we_form.elements['$longdesc_text_name'].value='';_EditorFrame.setEditorIsHot(true); YAHOO.autocoml.setValidById('" . $yuiSuggest->getInputId() . "')"));
		$_content->setCol(7, 0, array('colspan' => 5), we_html_tools::getPixel(1, 5));
		$_content->setCol(8, 0, array('valign' => 'bottom', 'colspan' => 5), $yuiSuggest->getYuiFiles() . $yuiSuggest->getHTML() . $yuiSuggest->getYuiCode());

		// Return HTML
		return $_content->getHtml();
	}

	/**
	 * Returns true if the gd lib supports the Type of the image
	 *
	 * @return boolean
	 */
	function gd_support(){
		return in_array($this->getGDType(), we_image_edit::supported_image_types());
	}

	/**
	 * Returns the Type for the image to use for the gd library functions
	 *
	 * @return string
	 */
	function getGDType(){
		return isset(we_image_edit::$GDIMAGE_TYPE[strtolower($this->Extension)]) ? we_image_edit::$GDIMAGE_TYPE[strtolower($this->Extension)] : 'jpg';
	}

	function convert($type, $quality = 8){
		if(!is_numeric($quality)){
			return false;
		}
		list($width, $height) = $this->getOrigSize();
		$quality = max(min($quality, 10), 0) * 10;

		$dataPath = TEMP_PATH . '/' . weFile::getUniqueId();
		we_image_edit::edit_image($this->getElement('data'), $type, $dataPath, $quality, $width, $height, false);

		$this->setElement('data', $dataPath);
		$this->Extension = '.' . $type;
		$this->Text = $this->Filename . $this->Extension;
		$this->Path = $this->getParentPath() . $this->Text;

		$this->DocChanged = true;
	}

	function getThumbnail(){
		return ($this->getElement('data') && is_readable($this->getElement('data')) ?
				'<img src="' . WEBEDITION_DIR . 'thumbnail.php?id=' . $this->ID . '&size=150&path=' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->getElement('data')) . '&extension=' . $this->Extension . '&size2=200" border="0" /></a>' :
				$this->getHtml());
	}

	protected function getMetaDataReader($force = false){
		return parent::getMetaDataReader(true);
	}

	function importMetaData($fieldsToImport = null, $importOnlyEmptyFields = false){
		$this->getMetaData();
		if(!isset($this->metaData) || !is_array($this->metaData)){
			return;
		}

		$_fields = array();

		// first we fetch all defined metadata fields from tblMetadata:
		$GLOBALS['DB_WE']->query('SELECT tag,type,importFrom FROM ' . METADATA_TABLE);
		while($GLOBALS['DB_WE']->next_record()) {
			list($_fieldName, $_fieldType, $_importFrom) = $GLOBALS['DB_WE']->getRecord();
			$_fieldType = $_fieldType ? $_fieldType : 'textfield';

			$_parts = explode(',', $_importFrom);
			foreach($_parts as $_part){
				$_part = trim($_part);
				$_fieldParts = explode('/', $_part);
				if(count($_fieldParts) > 1){
					$_tagType = strtolower(trim($_fieldParts[0]));
					$_tagName = trim($_fieldParts[1]);
					if(!(isset($_fields[$_fieldName]) && is_array($_fields[$_fieldName]))){
						$_fields[$_fieldName] = array();
					}
					$_fields[$_fieldName][] = array($_tagType, $_tagName, $_fieldType);
				}
			}
		}

		$_typeMap = array('textfield' => 'txt', 'wysiwyg' => 'txt', 'textarea' => 'txt', 'date' => 'date');

		foreach($_fields as $fieldName => $_arr){
			$_fieldVal = $this->getElement($fieldName);

			if((is_null($fieldsToImport) || in_array($fieldName, array_keys($fieldsToImport))) && ($importOnlyEmptyFields == false || $_fieldVal === '')){
				foreach($_arr as $_impFr){
					if(isset($this->metaData[$_impFr[0]][$_impFr[1]]) && !empty($this->metaData[$_impFr[0]][$_impFr[1]])){
						$_val = $this->metaData[$_impFr[0]][$_impFr[1]];
						if($_impFr[2] == 'date'){
							// here we need to parse the date
							if(preg_match('|^(\d{4}):(\d{2}):(\d{2}) (\d{2}):(\d{2}):(\d{2})$|', $_val, $regs)){
								$_val = sprintf('%016d', mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]));
							}
						}
						$this->setElement($fieldName, trim($_val), $_typeMap[$_impFr[2]]);
						break;
					}
				}
			}
		}
	}

	/**
	 * Returns the HTML for the link part in the properties view
	 *
	 * @return string
	 */
	function formLink(){
		$textname = 'we_' . $this->Name . '_txt[LinkPath]';
		$idname = 'we_' . $this->Name . '_txt[LinkID]';
		$extname = 'we_' . $this->Name . '_txt[LinkHref]';
		$linkType = $this->getElement('LinkType') ? $this->getElement('LinkType') : 'no';
		$linkPath = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID = ' . intval($this->getElement('LinkID')), 'Path', $this->DB_WE);

		$RollOverFlagName = 'we_' . $this->Name . '_txt[RollOverFlag]';
		$RollOverFlag = $this->getElement('RollOverFlag') ? 1 : 0;
		$RollOverIDName = 'we_' . $this->Name . '_txt[RollOverID]';
		$RollOverID = $this->getElement('RollOverID') ? $this->getElement('RollOverID') : '';
		$RollOverPathname = 'we_' . $this->Name . '_txt[RollOverPath]';
		$RollOverPath = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID = ' . intval($RollOverID), 'Path', $this->DB_WE);

		$checkFlagName = 'check_' . $this->Name . '_RollOverFlag';

		//javascript:we_cmd('openDocselector', document.forms['we_form'].elements['$idname'].value,'" . FILE_TABLE . "','document.forms[\'we_form\'].elements[\'$idname\'].value','document.forms[\'we_form\'].elements[\'$textname\'].value','opener._EditorFrame.setEditorIsHot(true);opener.document.we_form.elements[\\'we_".$this->Name."_txt[LinkType]\\'][2].checked=true;','',0,'',".(we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1).");
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.document.we_form.elements['we_" . $this->Name . "_txt[LinkType]'][2].checked=true;");
		$but1 = we_button::create_button('select', "javascript:we_cmd('openDocselector', document.forms['we_form'].elements['$idname'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','',0,''," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");

		//javascript:we_cmd('openDocselector', document.forms['we_form'].elements['$RollOverIDName'].value,'" . FILE_TABLE . "','document.forms[\'we_form\'].elements[\'$RollOverIDName\'].value','document.forms[\'we_form\'].elements[\'$RollOverPathname\'].value','opener._EditorFrame.setEditorIsHot(true);opener.document.we_form.elements[\'$RollOverFlagName\'].value=1;opener.document.we_form.elements[\'$checkFlagName\'].checked=true;','',0,'image/*',".(we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1).");
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$RollOverIDName'].value");
		$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$RollOverPathname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.document.we_form.elements['$RollOverFlagName'].value=1;opener.document.we_form.elements['$checkFlagName'].checked=true;");
		$but2 = we_button::create_button('select', "javascript:we_cmd('openDocselector', document.forms['we_form'].elements['$RollOverIDName'].value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','',0,'image/*'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");

		//javascript:formFileChooser('browse_server','document.we_form.elements[\\'$IDName\\'].value','$filter',document.we_form.elements['$IDName'].value,'$cmd');
		$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$extname'].value");
		$wecmdenc4 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.document.we_form.elements['we_" . $this->Name . "_txt[LinkType]'][1].checked=true;");
		$butExt = we_hasPerm('CAN_SELECT_EXTERNAL_FILES') ?
			we_button::create_button('select', "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','',document.forms['we_form'].elements['$extname'].value,'" . $wecmdenc4 . "')") : "";

		if(defined('OBJECT_TABLE')){
			$objidname = 'we_' . $this->Name . '_txt[ObjID]';
			$objtextname = 'we_' . $this->Name . '_txt[ObjPath]';
			$objPath = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID = ' . intval($this->getElement('ObjID')), 'Path', $this->DB_WE);
			//javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$objidname'].value,'" . OBJECT_FILES_TABLE . "','document.forms[\'we_form\'].elements[\'$objidname\'].value','document.forms[\'we_form\'].elements[\'$objtextname\'].value','opener._EditorFrame.setEditorIsHot(true);opener.document.we_form.elements[\\'we_".$this->Name."_txt[LinkType]\\'][3].checked=true;','','','objectFile',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).");
			$wecmdenc1 = we_cmd_enc("document.forms['we_form'].elements['$objidname'].value");
			$wecmdenc2 = we_cmd_enc("document.forms['we_form'].elements['$objtextname'].value");
			$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.document.we_form.elements['we_" . $this->Name . "_txt[LinkType]'][3].checked=true;");
			$butObj = we_button::create_button('select', "javascript:we_cmd('openDocselector',document.forms['we_form'].elements['$objidname'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','objectFile'," . (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ");");
		}

		// Create table
		$_content = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), (defined('OBJECT_TABLE') ? 11 : 9), 2);

		// No link
		$_content->setCol(0, 0, array('valign' => 'top'), we_forms::radiobutton('no', ($linkType == 'no'), 'we_' . $this->Name . '_txt[LinkType]', g_l('weClass', '[nolink]'), true, 'defaultfont', '_EditorFrame.setEditorIsHot(true);'));
		$_content->setCol(0, 1, null, '');

		// Space
		$_content->setCol(1, 0, null, we_html_tools::getPixel(100, 10));
		$_content->setCol(1, 1, null, we_html_tools::getPixel(400, 10));

		// External link
		$_ext_link_table = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 1, 3);

		$_ext_link_table->setCol(0, 0, null, $this->htmlTextInput('we_' . $this->Name . '_txt[LinkHref]', 25, $this->getElement('LinkHref'), '', 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 280));
		$_ext_link_table->setCol(0, 1, null, we_html_tools::getPixel(20, 1));
		$_ext_link_table->setCol(0, 2, null, $butExt);

		$_ext_link = "href" . we_html_element::htmlBr() . $_ext_link_table->getHtml();

		$_content->setCol(2, 0, array('valign' => 'top'), we_forms::radiobutton('ext', ($linkType == 'ext'), 'we_' . $this->Name . '_txt[LinkType]', g_l('weClass', '[extern]'), true, 'defaultfont', '_EditorFrame.setEditorIsHot(true)'));
		$_content->setCol(2, 1, array('class' => 'defaultfont', 'valign' => 'top'), $_ext_link);

		// Space
		$_content->setCol(3, 0, null, we_html_tools::getPixel(100, 10));
		$_content->setCol(3, 1, null, we_html_tools::getPixel(400, 10));

		// Internal link
		$_int_link_table = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 1, 3);

		$_int_link_table->setCol(0, 0, null, $this->htmlTextInput($textname, 25, $linkPath, '', 'onkeydown="return false"', 'text', 280));
		$_int_link_table->setCol(0, 1, null, we_html_tools::getPixel(20, 1));
		$_int_link_table->setCol(0, 2, null, $this->htmlHidden($idname, $this->getElement('LinkID')) . $but1);

		$_int_link = 'href' . we_html_element::htmlBr() . $_int_link_table->getHtml();

		$_content->setCol(4, 0, array('valign' => 'top'), we_forms::radiobutton('int', ($linkType == 'int'), 'we_' . $this->Name . '_txt[LinkType]', g_l('weClass', '[intern]'), true, 'defaultfont', '_EditorFrame.setEditorIsHot(true);'));
		$_content->setCol(4, 1, array('class' => 'defaultfont', 'valign' => 'top'), $_int_link);

		// Object link
		if(defined('OBJECT_TABLE')){
			$_content->setCol(5, 0, null, we_html_tools::getPixel(100, 10));
			$_content->setCol(5, 1, null, we_html_tools::getPixel(400, 10));

			$_obj_link_table = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 1, 3);

			$_obj_link_table->setCol(0, 0, null, $this->htmlTextInput($objtextname, 25, $objPath, '', 'onkeydown="return false"', 'text', 280));
			$_obj_link_table->setCol(0, 1, null, we_html_tools::getPixel(20, 1));
			$_obj_link_table->setCol(0, 2, null, $this->htmlHidden($objidname, $this->getElement('ObjID')) . $butObj);

			$_obj_link = 'href' . we_html_element::htmlBr() . $_obj_link_table->getHtml();

			$_content->setCol(6, 0, array('valign' => 'top'), we_forms::radiobutton('obj', ($linkType == 'obj'), 'we_' . $this->Name . '_txt[LinkType]', g_l('linklistEdit', '[objectFile]'), true, 'defaultfont', '_EditorFrame.setEditorIsHot(true);'));
			$_content->setCol(6, 1, array('class' => 'defaultfont', 'valign' => 'top'), $_obj_link);
		}

		// Space
		$_content->setCol((defined('OBJECT_TABLE') ? 7 : 5), 0, null, we_html_tools::getPixel(100, 20));
		$_content->setCol((defined('OBJECT_TABLE') ? 7 : 5), 1, null, we_html_tools::getPixel(400, 20));

		// Target
		$_content->setCol((defined('OBJECT_TABLE') ? 8 : 6), 0, array('colspan' => 2, 'class' => 'defaultfont', 'valign' => 'top'), g_l('weClass', '[target]') . we_html_element::htmlBr() . we_html_tools::targetBox('we_' . $this->Name . '_txt[LinkTarget]', 33, 380, '', $this->getElement('LinkTarget'), '_EditorFrame.setEditorIsHot(true);', 20, 97));

		// Space
		$_content->setCol((defined('OBJECT_TABLE') ? 9 : 7), 0, null, we_html_tools::getPixel(100, 20));
		$_content->setCol((defined('OBJECT_TABLE') ? 9 : 7), 1, null, we_html_tools::getPixel(400, 20));

		// Rollover image
		$_rollover_table = new we_html_table(array('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0), 1, 3);

		$_rollover_table->setCol(0, 0, null, $this->htmlTextInput($RollOverPathname, 25, $RollOverPath, '', 'onkeydown="return false"', 'text', 280));
		$_rollover_table->setCol(0, 1, null, we_html_tools::getPixel(20, 1));
		$_rollover_table->setCol(0, 2, null, $this->htmlHidden($RollOverIDName, $RollOverID) . $but2);

		$_rollover = 'href' . we_html_element::htmlBr() . $_rollover_table->getHtml();

		$_content->setCol((defined('OBJECT_TABLE') ? 10 : 8), 0, array('valign' => 'top'), we_forms::checkbox(1, $RollOverFlag, $checkFlagName, 'Roll Over', false, 'defaultfont', "_EditorFrame.setEditorIsHot(true); this.form.elements['$RollOverFlagName'].value = (this.checked ? 1 : 0); ") . $this->htmlHidden($RollOverFlagName, $RollOverFlag));
		$_content->setCol((defined('OBJECT_TABLE') ? 10 : 8), 1, array('class' => 'defaultfont', 'valign' => 'top'), $_rollover);

		return $_content->getHtml();
	}

	function hasMetaField($name){
		$_defined_fields = weMetaData::getDefinedMetaDataFields();
		foreach($_defined_fields as $field){
			if($field['tag'] === $name){
				return true;
			}
		}
		return false;
	}

	function formMetaInfos(){
		$content = '<table border="0" cellpadding="0" cellspacing="0">
	<tr><td colspan="2">' . $this->formInputField("txt", "Title", g_l('weClass', '[Title]'), 40, 508, '', "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	<tr><td colspan="2">' . $this->formInputField("txt", "Description", g_l('weClass', '[Description]'), 40, 508, '', "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	<tr><td colspan="2">' . $this->formInputField("txt", "Keywords", g_l('weClass', '[Keywords]'), 40, 508, '', "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
</table>';

		if($this->ContentType == 'image/*'){
			$content .= $this->formCharset(true);
		}

		return $content;
	}

	static function checkAndPrepare($formname, $key = 'we_document'){
		// check to see if there is an image to create or to change
		if(isset($_FILES['we_ui_' . $formname]) && is_array($_FILES['we_ui_' . $formname])){

			if(isset($_FILES['we_ui_' . $formname]['name']) && is_array($_FILES['we_ui_' . $formname]['name'])){
				foreach($_FILES['we_ui_' . $formname]['name'] as $imgName => $filename){
					$_imgDataId = isset($_REQUEST['WE_UI_IMG_DATA_ID_' . $imgName]) ? $_REQUEST['WE_UI_IMG_DATA_ID_' . $imgName] : false;

					if($_imgDataId !== false && isset($_SESSION[$_imgDataId])){
						$_SESSION[$_imgDataId]['doDelete'] = false;

						if(isset($_REQUEST['WE_UI_DEL_CHECKBOX_' . $imgName]) && $_REQUEST['WE_UI_DEL_CHECKBOX_' . $imgName] == 1){
							$_SESSION[$_imgDataId]['doDelete'] = true;
						} elseif($filename){
							// file is selected, check to see if it is an image
							$ct = getContentTypeFromFile($filename);
							if($ct == 'image/*'){
								$imgId = intval($GLOBALS[$key][$formname]->getElement($imgName));

								// move document from upload location to tmp dir
								$_SESSION[$_imgDataId]['serverPath'] = TEMP_PATH . '/' . weFile::getUniqueId();
								move_uploaded_file($_FILES['we_ui_' . $formname]['tmp_name'][$imgName], $_SESSION[$_imgDataId]['serverPath']);

								$we_size = we_thumbnail::getimagesize($_SESSION[$_imgDataId]['serverPath']);

								if(empty($we_size)){
									unset($_SESSION[$_imgDataId]);
									return;
								}

								$tmp_Filename = $imgName . '_' . weFile::getUniqueId() . '_' .
									preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['we_ui_' . $formname]['name'][$imgName]);

								if($imgId){
									$_SESSION[$_imgDataId]['id'] = $imgId;
								}

								$_SESSION[$_imgDataId]['fileName'] = preg_replace('#^(.+)\..+$#', '\\1', $tmp_Filename);
								$_SESSION[$_imgDataId]['extension'] = (strpos($tmp_Filename, '.') > 0) ?
									preg_replace('#^.+(\..+)$#', '\\1', $tmp_Filename) : '';
								$_SESSION[$_imgDataId]['text'] = $_SESSION[$_imgDataId]['fileName'] . $_SESSION[$_imgDataId]['extension'];

								//image needs to be scaled
								if((isset(
										$_SESSION[$_imgDataId]['width']) && $_SESSION[$_imgDataId]['width']) || (isset(
										$_SESSION[$_imgDataId]['height']) && $_SESSION[$_imgDataId]['height'])){
									$imageData = weFile::load($_SESSION[$_imgDataId]['serverPath']);
									$thumb = new we_thumbnail();
									$thumb->init('dummy', $_SESSION[$_imgDataId]['width'], $_SESSION[$_imgDataId]['height'], $_SESSION[$_imgDataId]['keepratio'], $_SESSION[$_imgDataId]['maximize'], false, false, '', 'dummy', 0, '', '', $_SESSION[$_imgDataId]['extension'], $we_size[0], $we_size[1], $imageData, '', $_SESSION[$_imgDataId]['quality']);

									$imgData = '';
									$thumb->getThumb($imgData);

									weFile::save($_SESSION[$_imgDataId]['serverPath'], $imageData);

									$we_size = we_thumbnail::getimagesize($_SESSION[$_imgDataId]['serverPath']);
								}

								$_SESSION[$_imgDataId]['imgwidth'] = $we_size[0];
								$_SESSION[$_imgDataId]['imgheight'] = $we_size[1];
								$_SESSION[$_imgDataId]['type'] = $_FILES['we_ui_' . $formname]['type'][$imgName];
								$_SESSION[$_imgDataId]['size'] = $_FILES['we_ui_' . $formname]['size'][$imgName];
							}
						}
					}
				}
			}
		}
	}

}
