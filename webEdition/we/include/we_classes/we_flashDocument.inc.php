<?php

/**
 * webEdition CMS
 *
 * $Rev: 5888 $
 * $Author: mokraemer $
 * $Date: 2013-02-25 20:28:04 +0100 (Mon, 25 Feb 2013) $
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
/*  a class for handling flashDocuments. */

class we_flashDocument extends we_binaryDocument{
	/* Parameternames which are placed within the object-Tag */

	var $ObjectParamNames = array('align', 'border', 'id', 'height', 'hspace', 'name', 'width', 'vspace', 'only', 'style');

	function __construct(){
		parent::__construct();
		$this->EditPageNrs[] = WE_EDITPAGE_PREVIEW;
		$this->ContentType = 'application/x-shockwave-flash';
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_PREVIEW:
				return 'we_templates/we_editor_flash_preview.inc.php';
			default:
				return parent::editor();
		}
	}

	// is not written yet
	function initByAttribs($attribs){
		if(isset($attribs['sizingrel'])){
			$orig_w = (isset($attribs['width']) ? $attribs['width'] : $this->elements['width']['dat']);
			$orig_h = (isset($attribs['height']) ? $attribs['height'] : $this->elements['height']['dat']);
			$attribs['width'] = round($orig_w * $attribs['sizingrel']);
			$attribs['height'] = round($orig_h * $attribs['sizingrel']);
			unset($attribs['sizingrel']);
		}
		$sizingbase = (isset($attribs['sizingbase']) && $attribs['sizingbase'] != 16 ? $attribs['sizingbase'] : 16);
		if(isset($attribs['sizingbase'])){
			unset($attribs['sizingbase']);
		}

		if(isset($attribs['sizingstyle'])){
			$sizingstyle = ($attribs['sizingstyle'] == 'none' ? false : $attribs['sizingstyle']);
			unset($attribs['sizingstyle']);
		} else{
			$sizingstyle = false;
		}

		if($sizingstyle){
			$style_width = round($attribs['width'] / $sizingbase, 6);
			$style_height = round($attribs['height'] / $sizingbase, 6);
			$newstyle = (isset($attribs['style']) ? $attribs['style'] : '');

			$newstyle.=';width:' . $style_width . $sizingstyle . ';height:' . $style_height . $sizingstyle . ';';
			$attribs['style'] = $newstyle;
			unset($attribs['width']);
			unset($attribs['height']);
		}
		foreach($attribs as $a => $b){
			if($b != ''){
				$this->setElement($a, $b, ($a == 'Pluginspage' || $a == 'Codebase' ? 'txt' : 'attrib'));
			}
		}
	}

	/* gets the HTML for including in HTML-Docs */

	function getHtml($dyn = false){
		$_data = $this->getElement('data');
		if($this->ID || ($_data && !is_dir($_data) && is_readable($_data))){

			$pluginspage = $this->getElement('Pluginspage') ? $this->getElement('Pluginspage') : 'http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash';
			$codebase = $this->getElement('Codebase') ? $this->getElement('Codebase') : 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';

			// fix. older versions of webEdition bgcolor was type txt and not attrib
			if(isset($this->elements['bgcolor'])){
				$this->elements['bgcolor']['type'] = 'attrib';
			}

			srand((double) microtime() * 1000000);
			$randval = rand();
			$src = $dyn ?
				WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=show_binaryDoc&we_cmd[1]=' . $this->ContentType . '&we_cmd[2]=' . $GLOBALS['we_transaction'] . '&rand=' . $randval :
				$this->Path;
			$attribs = array();
			$params = array();
			$this->html = '';

			/*			 * ****************************************************************************
			  /* take all attribs and seperate them in attribs, params and embeds
			  /***************************************************************************** */

			$xml = (boolean) $this->getElement('xml');

			//   first we deal with alt-content
			$alt = $this->getElement('alt');
			$altContent = '';
			if($alt){
				if(isset($GLOBALS['we_doc']->elements[$alt]) && isset($GLOBALS['we_doc']->elements[$alt]['type'])){
					$altContent = $GLOBALS['we_doc']->getField(array('name' => $alt, 'xml' => $xml), $GLOBALS['we_doc']->elements[$alt]['type']);
				}
			}

			if($xml){ //  XHTML-Version
				$allowedAtts = $this->ObjectParamNames;
				$filter = array('alt', 'parentid', 'startid');

				foreach($this->nextElement('attrib') as $k => $v){

					if(in_array($k, $allowedAtts)){ //  use as name='value'
						$attribs[$k] = $v['dat'];
					} else if(!in_array($k, $filter)){ //  use as <param>
						$params[$k] = $v['dat'];
					}
				}

				//   needed attribs
				$attribs['type'] = 'application/x-shockwave-flash';
				$attribs['data'] = $src;
			} else{ //  Normal-Version - with embed-tag
				$filter = array('type', 'alt', 'parentid', 'startid');

				$allowedAtts = $this->ObjectParamNames;

				while(list($k, $v) = $this->nextElement('attrib')) {

					if(in_array($k, $allowedAtts)){ //  use as name='value'
						$attribs[$k] = $v['dat'];
					} else if(!in_array($k, $filter)){ //  use as <param>
						$params[$k] = $v['dat'];
					}
					if(!in_array($k, $filter)){
						if($v['dat'] !== ''){
							$embedAtts[$k] = $v['dat'];
						}
					}
				}
				$attribs['classid'] = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
				$attribs['codebase'] = $codebase;
			}

			//   handle with params
			$params['movie'] = $src; //  always needed
			$params = removeAttribs($params, array('xml', 'to', 'nameto'));

			foreach($params AS $k => $v){
				if($v !== ''){
					$this->html .= getHtmlTag('param', array('name' => $k, 'value' => $v, 'xml' => $this->getElement('xml')));
				}
			}

			if(!$xml){ //  additional <embed tag>
				$embedAtts['type'] = 'application/x-shockwave-flash';
				$embedAtts['pluginspage'] = $pluginspage;
				$embedAtts['src'] = $src;

				$this->html .= getHtmlTag('embed', $embedAtts, '', true);
			}

			$this->html = getHtmlTag('object', $attribs, $this->html . $altContent);
			if(isset($attribs['only'])){
				$this->html = $attribs[$attribs['only']];
			} else if(isset($attribs['pathonly']) && $attribs['pathonly']){
				$this->html = $src;
			}
		} else{
			if($GLOBALS['we_doc']->InWebEdition == 1){
				/* Anzeige des No_Falsh-Bildes in der Vorschau
				  $imgAtts["src"]    = IMAGE_DIR . 'icons/no_flashmovie.gif';
				  $imgAtts["width"]  = 64;
				  $imgAtts["height"] = 64;
				  $imgAtts["border"] = 0;
				  $imgAtts["style"] = "margin:8px 18px;";
				  $imgAtts["alt"]    = "";
				  $imgAtts["xml"]    = $this->getElement("xml");
				  if(isset($this->name)){
				  $imgAtts["name"] = $this->name;
				  }
				  $this->html = getHtmlTag("img", $imgAtts);
				 */
				$this->html = '';
			} else{
				$this->html = '';
			}
		}
		return $this->html;
	}

	function formProperties(){
		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>' . $this->formInputInfo2(155, "width", 10, "attrib", "onChange=\"_EditorFrame.setEditorIsHot(true);\"", "origwidth") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formInputInfo2(155, "height", 10, "attrib", "onChange=\"_EditorFrame.setEditorIsHot(true);\"", "origheight") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formSelectElement2(155, "scale", array("" => "", "showall" => g_l('global', '[showall]'), "noborder" => g_l('global', '[noborder]'), "exactfit" => g_l('global', '[exactfit]')), "attrib", 1, "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>
	<tr valign="top">
		<td colspan="5">' . we_html_tools::getPixel(2, 5) . '</td>
	</tr>
	<tr valign="top">
		<td>' . $this->formInput2(155, "hspace", 10, "attrib", "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formInput2(155, "vspace", 10, "attrib", "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formInput2(155, "name", 10, "attrib", "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>
	<tr valign="top">
		<td colspan="5">' . we_html_tools::getPixel(2, 5) . '</td>
	</tr>
	<tr valign="top">
		<td>' . $this->formSelectElement2(155, "play", array("" => g_l('global', '[true]'), "false" => g_l('global', '[false]')), "attrib", 1, "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formSelectElement2(155, "quality", array("" => "", "low" => "low", "high" => "high", "autohigh" => "autohigh", "autolow" => "autolow", "best" => "best"), "attrib", 1, "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formColor(155, "bgcolor", 25, "attrib") . '</td>
	</tr>
	<tr valign="top">
		<td colspan="5">' . we_html_tools::getPixel(2, 5) . '</td>
	</tr>
	<tr valign="top">
		<td>' . $this->formSelectElement2(155, "align", array("" => "", "left" => g_l('global', '[left]'), "right" => g_l('global', '[right]'), "top" => g_l('global', '[top]'), "bottom" => g_l('global', '[bottom]')), "attrib", 1, "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formSelectElement2(155, "salign", array("" => "", "l" => g_l('global', '[left]'), "r" => g_l('global', '[right]'), "t" => g_l('global', '[top]'), "b" => g_l('global', '[bottom]'), "tl" => g_l('global', '[topleft]'), "tr" => g_l('global', '[topright]'), "bl" => g_l('global', '[bottomleft]'), "br" => g_l('global', '[bottomright]')), "attrib", 1, "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td>' . $this->formSelectElement2(155, "loop", array("" => g_l('global', '[true]'), "false" => g_l('global', '[false]')), "attrib", 1, "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>
	<tr valign="top">
		<td colspan="5">' . we_html_tools::getPixel(2, 5) . '</td>
	</tr>
	<tr valign="top">
		<td>' . $this->formSelectElement2(155, "wmode", array("" => "", "window" => "window", "opaque" => "opaque", "transparent" => "transparent"), "attrib", 1, "onChange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td></td>
		<td>' . we_html_tools::getPixel(18, 2) . '</td>
		<td></td>
	</tr>

</table>
';
	}

	function formOther(){
		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>' . $this->formInputField("txt", "Pluginspage", "Pluginspage", 24, 388, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>
	<tr valign="top">
		<td>' . $this->formInputField("txt", "Codebase", "Codebase", 24, 388, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>
</table>
';
	}

	function getThumbnail(){
		$_width = $this->getElement('width');
		$_height = $this->getElement('height');
		$_scale = $this->getElement('scale');
		$_hspace = $this->getElement('hspace');
		$_vspace = $this->getElement('vspace');
		$_name = $this->getElement('name');
		$_play = $this->getElement('play');
		$_quality = $this->getElement('quality');
		$_bgcolor = $this->getElement('bgcolor');
		$_align = $this->getElement('align');
		$_salign = $this->getElement('salign');
		$_loop = $this->getElement('loop');
		$_wmode = $this->getElement('wmode');
		$_origwidth = $this->getElement('origwidth');
		$_origheight = $this->getElement('origheight');

		$this->setElement('width', 150, 'attrib');
		$this->setElement('height', 100, 'attrib');
		$this->setElement('scale', '', 'attrib');
		$this->setElement('hspace', '', 'attrib');
		$this->setElement('vspace', '', 'attrib');
		$this->setElement('name', '', 'attrib');
		$this->setElement('play', 'true', 'attrib');
		$this->setElement('quality', '', 'attrib');
		$this->setElement('bgcolor', '', 'attrib');
		$this->setElement('align', '', 'attrib');
		$this->setElement('salign', '', 'attrib');
		$this->setElement('loop', '', 'attrib');
		$this->setElement('wmode', 'window', 'attrib');
		$this->setElement('origwidth', '', 'attrib');
		$this->setElement('origheight', '', 'attrib');

		$html = $this->getHtml(true);
		$this->setElement('width', $_width, 'attrib');
		$this->setElement('height', $_height, 'attrib');
		$this->setElement('scale', $_scale, 'attrib');
		$this->setElement('hspace', $_hspace, 'attrib');
		$this->setElement('vspace', $_vspace, 'attrib');
		$this->setElement('name', $_name, 'attrib');
		$this->setElement('play', $_play, 'attrib');
		$this->setElement('quality', $_quality, 'attrib');
		$this->setElement('bgcolor', $_bgcolor, 'attrib');
		$this->setElement('align', $_align, 'attrib');
		$this->setElement('salign', $_salign, 'attrib');
		$this->setElement('loop', $_loop, 'attrib');
		$this->setElement('wmode', $_wmode, 'attrib');
		$this->setElement('origwidth', $_origwidth, 'attrib');
		$this->setElement('origheight', $_origheight, 'attrib');

		return $html;
	}

	/**
	 * function will determine the size of any GIF, JPG, PNG.
	 * This function uses the php Function with the same name.
	 * But the php function doesn't work with some images created from some apps.
	 * So this function uses the gd lib if nothing is returned from the php function
	 *
	 * @static
	 * @public
	 * @return array
	 * @param $filename complete path of the image
	 */
	function getimagesize($filename){
		$arr = @getimagesize($filename);

		if(isset($arr) && is_array($arr) && (count($arr) >= 4) && $arr[0] && $arr[1]){
			return $arr;
		} else{
			if(we_image_edit::gd_version()){
				return we_image_edit::getimagesize($filename);
			}
			return $arr;
		}
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
		$origw = $this->getElement('origwidth');
		$this->setElement('origwidth', isset($arr[0]) ? $arr[0] : 0);
		$this->setElement('origheight', isset($arr[1]) ? $arr[1] : 0);
		//if ($origw != $this->getElement('origwidth')){$this->DocChanged = true;}
		if($this->getElement('width') == ''){
			$this->setElement('width', $this->getElement('origwidth'));
		}
		if($this->getElement('height') == ''){
			$this->setElement('height', $this->getElement('origheight'));
		}
		if($this->Icon == ''){
			$this->Icon = we_base_ContentTypes::inst()->getIcon($this->ContentType);
		}

		$docChanged = $this->DocChanged; // will be reseted in parent::we_save()
		if(parent::we_save($resave)){
			if($docChanged){
				$this->DocChanged = true;
			}

			return true;
		}

		return false;
	}

	function we_rewrite(){
		parent::we_rewrite();
		$this->we_save();
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
				return $this->getimagesize($_SERVER['DOCUMENT_ROOT'] . (($useOldPath && $this->OldPath) ? $this->OldPath : $this->Path));
			}
		} else if(isset($this->elements['data']['dat']) && $this->elements['data']['dat']){
			$arr = $this->getimagesize($this->elements['data']['dat']);
		}
		return $arr;
	}

	static function checkAndPrepare($formname, $key = 'we_document'){
		// check to see if there is an image to create or to change
		if(!(isset($_FILES["we_ui_$formname"]) && is_array($_FILES["we_ui_$formname"]))){
			return;
		}

		$webuserId = isset($_SESSION['webuser']['ID']) ? $_SESSION['webuser']['ID'] : 0;

		if(isset($_FILES["we_ui_$formname"]["name"]) && is_array($_FILES["we_ui_$formname"]["name"])){
			foreach($_FILES["we_ui_$formname"]["name"] as $flashName => $filename){

				$_flashmovieDataId = isset($_REQUEST['WE_UI_FLASHMOVIE_DATA_ID_' . $flashName]) ? $_REQUEST['WE_UI_FLASHMOVIE_DATA_ID_' . $flashName] : false;

				if($_flashmovieDataId !== false && isset($_SESSION[$_flashmovieDataId])){

					$_SESSION[$_flashmovieDataId]['doDelete'] = false;

					if(isset($_REQUEST["WE_UI_DEL_CHECKBOX_" . $flashName]) && $_REQUEST["WE_UI_DEL_CHECKBOX_" . $flashName] == 1){
						$_SESSION[$_flashmovieDataId]['doDelete'] = true;
					} else
					if($filename){
						// file is selected, check to see if it is an image
						$ct = getContentTypeFromFile($filename);
						if($ct == $this->ContentType){
							$flashId = intval($GLOBALS[$key][$formname]->getElement($flashName));

							// move document from upload location to tmp dir
							$_SESSION[$_flashmovieDataId]["serverPath"] = TEMP_PATH . '/' . weFile::getUniqueId();
							move_uploaded_file($_FILES["we_ui_$formname"]["tmp_name"][$flashName], $_SESSION[$_flashmovieDataId]["serverPath"]);

							$tmp_Filename = $flashName . "_" . weFile::getUniqueId() . "_" . preg_replace(
									'[^A-Za-z0-9._-]', '', $_FILES["we_ui_$formname"]["name"][$flashName]);

							if($flashId){
								$_SESSION[$_flashmovieDataId]["id"] = $flashId;
							}

							$_SESSION[$_flashmovieDataId]["fileName"] = preg_replace(
								'#^(.+)\..+$#', '\\1', $tmp_Filename);
							$_SESSION[$_flashmovieDataId]["extension"] = (strpos($tmp_Filename, ".") > 0) ? preg_replace(
									'#^.+(\..+)$#', '\\1', $tmp_Filename) : '';
							$_SESSION[$_flashmovieDataId]["text"] = $_SESSION[$_flashmovieDataId]["fileName"] . $_SESSION[$_flashmovieDataId]["extension"];

							$we_size = getimagesize($_SESSION[$_flashmovieDataId]["serverPath"]);
							$_SESSION[$_flashmovieDataId]["imgwidth"] = $we_size[0];
							$_SESSION[$_flashmovieDataId]["imgheight"] = $we_size[1];
							$_SESSION[$_flashmovieDataId]["type"] = $_FILES["we_ui_$formname"]["type"][$flashName];
							$_SESSION[$_flashmovieDataId]["size"] = $_FILES["we_ui_$formname"]["size"][$flashName];
						}
					}
				}
			}
		}
	}

}