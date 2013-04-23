<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
abstract class we_SEEM{

	/**
	 * we_SEEM::getClassVars
	 *
	 * @desc    This function is a workaround for using variables within a "static" class.
	  in this case it is only used in in normal mode and has no effect in super-easy-edit-mode.

	 * @param	string	name of the variable
	 * @return  string	value of the variable
	 */
	static function getClassVars($name){
		return '';
		//	here are all variables.
		if($_SESSION['weS']['we_mode'] == "normal"){
			$vtabSrcDocs = "top.Vtabs.we_cmd('loadVTab','" . FILE_TABLE . "',0);top.we_cmd('exit_delete');";
			if(defined("OBJECT_FILES_TABLE")){
				$vtabSrcObjs = (we_hasPerm("CAN_SEE_OBJECTFILES") ?
						"top.Vtabs.we_cmd('loadVTab','" . OBJECT_FILES_TABLE . "',0);top.we_cmd('exit_delete');" :
						"top.we_cmd('exit_delete');");
			}
		} else{
			$vtabSrcDocs = "";
			$vtabSrcObjs = "";
		}


		return (isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == "normal" ? (isset($$name) ? $$name : '') : '');
	}

	/**
	 * we_SEEM::parseDocument()
	 * @desc     Parses all links/forms in the webededition preview or edit mode of a given HTML, PHP code.
	  Pressing these links/ Submitting these forms has the same effect, than selecting the correspondent document in the
	  Tree-Menue on the left side. Extern docs (with parameters) are also opened within webEdition.
	 *
	 * @see      we_SEEM::getAllHrefs
	 * @see      we_SEEM::parseLinksForEditMode
	 * @see      we_SEEM::parseLinksForPreviewMode
	 * @see      we_SEEM::getAllForms
	 * @see      we_SEEM::parseFormsForEditMode
	 * @see      we_SEEM::parseFormsForPreviewMode
	 *
	 * @param    code    string
	 * @return   code    string with parsed links
	 */
	static function parseDocument($code){
		//  Parse all links of the webedition-document (Preview Mode)
		//  Pressing the link inside the Preview of webedtion must show
		//  the same behaviour like selecting the document in the file
		//  Browser on the left side.
		//  First get all Hrefs of the document
		//  $linkarray[0] - Array of all inside the "<a ... >"
		//  $linkarray[1] - Array of all between -><a href="<-
		//  $linkarray[2] - Array of all between <a href="->...<-?test=1" ...
		//  $linkarray[3] - Array of all get-Parameters: <a href="...->?test=1&..."<- ...
		//  $linkarray[4] - Array of all after the href (styles and stuff)<a href="...?test=1"-> ... <->
		//  All these informations are needed to replace the old link with a new one
		$linkArray = self::getAllHrefs($code);

		if(isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_CONTENT && !defined("WE_SIDEBAR")){

			//  The edit-mode only changes SEEM-links
			$code = self::parseLinksForEditMode($code, $linkArray);
		}

		if(!isset($GLOBALS['we_doc']) || $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PREVIEW || $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PREVIEW_TEMPLATE || defined("WE_SIDEBAR")){

			//  in the preview mode all found links in the document shall be changed
			$code = self::parseLinksForPreviewMode($code, $linkArray);
		}



		//  Now deal with all form-tags and submit-buttons
		//  they shall be replaced in edit-mode
		//  $allForms[0] - contains the original formular - this is needed to be replaced
		//  if no form is found in $code, then false is returned.
		//  This must be done always

		$allForms = self::getAllForms($code);
		//  if in editMode, remove all forms but the "we_form"
		if(isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_CONTENT && !defined("WE_SIDEBAR")){
			return self::parseFormsForEditMode($code, $allForms);
		}
		//  we are in preview mode or open an extern document - parse all found forms
		if(!isset($GLOBALS['we_doc']) || $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PREVIEW || defined("WE_SIDEBAR")){
			return self::parseFormsForPreviewMode($code, $allForms);
		}

		//  All is done - return the code
		return $code;
	}

	/**
	 * we_SEEM::parseLinksForEditMode
	 *
	 * @desc     This function parses the given code for the edit-mode.
	  It will change all Links with a SEEM attribute
	 *
	 * @see      we_SEEM::getSEEM_Links
	 * @see      we_SEEM::replaceSEEM_Links
	 *
	 * @param    code        string of the complete source code
	 * @param    linkArray   array of all found links in the document
	 *
	 * @return string of the source code with changed links
	 */
	static function parseLinksForEditMode($code, $linkArray){

		//  Take all links with a seem="<attrib>" and put them in a new Array
		//  $SEEM_Links[0] - Array of all found "<a ... >"
		//  $SEEM_Links[1] - Array of all between href="<- ... ->", this is the path to the document
		//  $SEEM_Links[2] - Array containing the the value of SEEM="<attrib>"
		//  if no array is returned - false is returned.
		$SEEM_Links = self::getSEEM_Links($linkArray);

		//  if an array is returned, modify the code
		if($SEEM_Links && is_array($SEEM_Links)){
			return self::replaceSEEM_Links($code, $SEEM_Links);
		}
		return $code;
	}

	/**
	 * we_SEEM::parseLinksForPreviewMode
	 *
	 * @desc     This function parses the given code for the preview-mode.
	 *
	 * @see      we_SEEM::onlyUseHyperlinks
	 * @see      we_SEEM::cleanLinks
	 * @see      we_SEEM::findRelativePaths
	 * @see      we_SEEM::getDocIDsByPaths
	 * @see      we_SEEM::replaceLinks
	 *
	 * @param    code        string of the complete source code
	 * @param    linkArray   array of all found links in the document
	 *
	 * @return   string of the source code with changed links
	 */
	static function parseLinksForPreviewMode($code, $linkArray){

		$SEEM_Links = self::getSEEM_Links($linkArray);
		//  if an array is returned, modify the code
		if($SEEM_Links && is_array($SEEM_Links)){
			$code = self::replaceSEEM_Links($code, $SEEM_Links);
		}
		$linkArray = self::removeSEEMLinks($linkArray);

		//  Remove all other Stuff from the linkArray
		//  Here all further SEEM - Links are removed as well
		if($linkArray && is_array($linkArray)){

			$linkArray = self::onlyUseHyperlinks($linkArray);

			//  if an array is returned in onlyUseHyperlinks, then parse the $code, otherwise return the same code.
			if($linkArray && is_array($linkArray)){

				//  Remove all javascript, or target stuff, from links, they could disturb own functionality
				//  Important are $linkArray[1][*] and $linkArray[4][*]
				$linkArray = self::cleanLinks($linkArray);

				//  $linkArray[5] - Array of the relative translation of given Link-targets, only with webEdition-Docs
				$linkArray[5] = self::findRelativePaths($linkArray[2]);

				//  $linkArray[6] - Array which contains the docIds of the Documents, or -1
				$linkArray[6] = self::getDocIDsByPaths($linkArray[5]);

				//	$linkArray[7] - Array which contains the content-types of the documents or ''
				$linkArray[7] = self::getDocContentTypesByID($linkArray[6]);

				$code = (defined("WE_SIDEBAR") ?
						self::replaceLinksForSidebar($code, $linkArray) :
						self::replaceLinks($code, $linkArray));
			}
		}
		return $code;
	}

	/**
	 * we_SEEM::parseFormsForPreviewMode
	 *
	 * @desc     This function parses all forms for the Preview mode, they will behave like viewing
	  the page outside webEdition.
	 *
	 * @see      we_SEEM::getPathsFromForms
	 * @see      we_SEEM::findRelativePaths
	 * @see      we_SEEM::getDocIDsByPaths
	 * @see      we_SEEM::rebuildForms
	 *
	 * @param   code        string src-code of the document
	 * @param   allForms    array with all forms found in the code
	 * @return  code        string
	 */
	static function parseFormsForPreviewMode($code, $allForms){

		if($allForms && is_array($allForms)){

			//  $allForms[1] - the actions of the forms, or -1, when action is missing, then we must take the doc-ID
			$allForms[1] = self::getPathsFromForms($allForms);

			//  $allForms[1] now has the relative translation of paths if possible
			$allForms[1] = self::findRelativePaths($allForms[1]);

			//  $allForms[2] contains all doc-ids of the found forms
			$allForms[2] = self::getDocIDsByPaths($allForms[1]);

			$code = self::rebuildForms($code, $allForms);
		}
		return $code;
	}

	/**
	 * we_SEEM::parseFormsForEditMode
	 *
	 * @desc    This function removes all forms from the code in the edit mode.
	  Also 'input type="submits"' will be changed to 'input type="button"'
	 *
	 * @see     we_SEEM::removeAllButWE_FORM
	 * @see     we_SEEM::changeSubmitToButton
	 *
	 * @param   code        string the srcCode of the document
	 * @param   allForms    array with all found forms
	 * @return  code        the new code
	 */
	static function parseFormsForEditMode($code, $allForms){

		//  remove all forms but the form with name "we_form" from the code.
		//  also remove all tags where forms where closed and add one at the end of the file.
		//  This makes some problems, if forms are given in some HTML-Preview fields.
//            $code = self::removeAllButWE_FORM($code, $allForms);
		//  now we must change all submit-buttons to normal buttons
//            $code = self::changeSubmitToButton($code);

		return $code;
	}

	/**
	 * we_SEEM::changeSubmitToButton
	 *
	 * @desc    Changes submit buttons in given code to normal buttons
	 *
	 * @param   code        string, source code of the document
	 * @return  code        string only with the "we_form", needed to edit the page
	 */
	static function changeSubmitToButton($code){
		$allInputs = array();
		//  Searchpattern for all <input ..> in the code
		$pattern = "/<input[^>]*type=[\"|']?submit[\"|']?[^>]*>/si";
		preg_match_all($pattern, $code, $allInputs);

		//  Replace the input type="submit" with input type="button"
		foreach($allInputs[0] as $cur){

			$attribs = self::getAttributesFromTag($cur);
			// THIS FUNCTION IS NOT USED ATM
			$tmpInput = '<input onclick="#"';

			foreach($attribs as $key => $value){
				$tmpInput .= ' ' . $key . '="' . (strtolower($key) == "type" && strtolower($value) == "submit" ? 'button' : $value) . '"';
			}
			$tmpInput .= '>';
			$code = str_replace($cur, $tmpInput, $code);
		}
		return $code;
	}

	/**
	 * we_SEEM::removeAllButWE_FORM
	 *
	 * @desc    Removes all <forms> from the $code but the "we_form"
	 *
	 * @param   formArray   array with all found forms from the document
	 * @param   code        string, source code of the document
	 * @return  code        string only with the "we_form", needed to edit the page
	 */
	static function removeAllButWE_FORM($code, $formArray){
		$deletedForms = false;
		foreach($formArray[0] as $cur){

			$attribs = self::getAttributesFromTag($cur);
			$we_form = false;
			foreach($attribs as $key => $value){
				if($key == "name" && $value == "we_form"){
					$we_form = true;
				}
			}

			//  it is not the "we_form" so delete it from the code
			if(!$we_form){
				$code = str_replace($cur, "<!--removed from SEEM-->", $code);
				$deletedForms = true;
			}

			if($deletedForms){
				$code = str_replace("</form>", "", $code) . '</form>';
			}
		}
		return $code;
	}

	/**
	 * we_SEEM::replaceSEEM_Links()
	 *
	 * @desc    This function replaces the SEEM-Links added by the Tag-Parser.
	 *
	 * @param   oldcode         string This is the original code of the document.
	 * @param   SEEM_LinkArray  array filled with the seem - Links,
	  [0] is the old link, which must be replaced
	  [1] contains the id of the document
	  [2] contains the SEEM-attribute, p.ex "include"

	 * @return   code           string the new code, where all seem_links are replaced with new functionality
	 */
	static function replaceSEEM_Links($code, $SEEM_LinkArray){
		$mode = (isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_CONTENT ? "edit" : "preview");

		$_REQUEST['we_transaction'] = (preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_transaction']) ? $_REQUEST['we_transaction'] : 0);
		for($i = 0; $i < count($SEEM_LinkArray[0]); $i++){

			if(isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == "seem" && $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_CONTENT){ //	in Super-Easy-Edit-Mode only in Editmode !!!
				switch($SEEM_LinkArray[2][$i]){

					//  Edit an included document from webedition.
					case "edit_image":
						$handler = "if(top.edit_include){top.edit_include.close();}top.edit_include=window.open('" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=edit_include_document&we_cmd[1]=" . FILE_TABLE . "&we_cmd[2]=" . $SEEM_LinkArray[1][$i] . "&we_cmd[3]=image/*&we_cmd[4]=" . FILE_TABLE . "&we_cmd[5]=" . $SEEM_LinkArray[1][$i] . "&we_cmd[6]=" . $_REQUEST["we_transaction"] . "&we_cmd[7]='" . ",'_blank','width=800,height=600,status=yes');return true;";
						$code = str_replace($SEEM_LinkArray[0][$i] . "</a>", we_button::create_button("image:btn_edit_image", "javascript:$handler", true), $code);
						break;
					case "include" :
						//  a new window is opened which stays as long, as the browser is closed, or the window is closed manually
						$handler = "if(top.edit_include){top.edit_include.close();}top.edit_include=window.open('" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=edit_include_document&we_cmd[1]=" . FILE_TABLE . "&we_cmd[2]=" . $SEEM_LinkArray[1][$i] . "&we_cmd[3]=text/webedition&we_cmd[4]=" . FILE_TABLE . "&we_cmd[5]=" . $SEEM_LinkArray[1][$i] . "&we_cmd[6]=" . $_REQUEST["we_transaction"] . "&we_cmd[7]='" . ",'_blank','width=800,height=600,status=yes');return true;";
						$code = str_replace($SEEM_LinkArray[0][$i] . "</a>", we_button::create_button("image:btn_edit_include", "javascript:$handler", true), $code);
						break;

					case "object" :
						$handler = "top.doClickDirect('" . $SEEM_LinkArray[1][$i] . "','objectFile','" . OBJECT_FILES_TABLE . "');";
						$code = str_replace($SEEM_LinkArray[0][$i] . '</a>', we_button::create_button("image:btn_edit_object", "javascript:$handler", true) . "</a>", $code);
						break;

					default :
						break;
				}
			} else{ //	we are in normal mode, so just delete the links
				$code = str_replace($SEEM_LinkArray[0][$i] . '</a>', "", $code);
			}
		}
		return $code;
	}

	/**
	 * we_SEEM::getSEEM_Links()
	 *
	 * @desc     Looks for special Links within the from function getAllHrefs(). found links
	  are saved in returned array
	 *
	 * @param    oldArray    array with all found hyperlinks of getAllHrefs()
	 * @return   $newArray   array with the SEEM-Links
	 */
	static function getSEEM_Links($oldArray){
		$newArray = array();
		$seem_attrib = array();
		for($i = 0; $i < count($oldArray[0]); $i++){
			if(preg_match('/ seem="(.*)"/', $oldArray[0][$i], $seem_attrib)){

				$newArray[0][] = $oldArray[0][$i];
				$newArray[1][] = $oldArray[2][$i];
				$newArray[2][] = $seem_attrib[1];
			} else{
				//  this link has no function="seem" inside, so it isn't taken to newArray
			}
		}

		return (empty($newArray) ? false : $newArray);
	}

	/**
	 * we_SEEM::cleanLinks()
	 * @desc     Removes any attributes from the given links, which can affect with webEdition.
	  p.ex target, some java-script eventhandlers
	 *
	 * @param    $linkArray  array with <a hrefs ... > in the document
	 * @return               links without attributes, which can affect bad with webEdition.
	 */
	static function cleanLinks($linkArray){
		$trenner = "[\040|\n|\t|\r]*";
		$pattern = array(
			'/' . $trenner . 'onclick' . $trenner . '=/i' => ' thiswasonclick=',
			'/' . $trenner . 'onmouseover' . $trenner . '=/i' => ' thiswasonmouseover=',
			'/' . $trenner . 'onmouseout' . $trenner . '=/i' => ' thiswasonmouseout=',
			'/' . $trenner . 'ondblclick' . $trenner . '=/i' => ' thiswasondblclick=',
		);

		for($i = 0; $i < count($linkArray[0]); $i++){
			$linkArray[1][$i] = preg_replace(array_keys($pattern), array_values($pattern), $linkArray[1][$i]);
			$linkArray[4][$i] = preg_replace(array_keys($pattern), array_values($pattern), $linkArray[4][$i]);
		}
		return $linkArray;
	}

	static function replaceLinksForSidebar($srcCode, $linkArray){

		//	This is Code, to have the same effect like pressing a vertical tab
		$destCode = $srcCode;

		for($i = 0; $i < count($linkArray[0]); $i++){

			if($linkArray[6][$i] != -1){ //  The target of the Link is a webEdition - Document.
				if($linkArray[3][$i] != ""){ //  we have several parameters, deal with them
					$theParameterArray = self::getAttributesFromGet($linkArray[3][$i], 'we_cmd');

					$javascriptCode = (array_key_exists("we_objectID", $theParameterArray) ?
							//	target is a object
							" onclick=\"" . self::getClassVars("vtabSrcObjs") . "top.weSidebar.load('" . $linkArray[2][$i] . "');\" onMouseOver=\"top.info('ID: " . $theParameterArray["we_objectID"] . "');\" onMouseOut=\"top.info(' ')\" " :
							//	target is a normal file.
							" onclick=\"" . self::getClassVars("vtabSrcDocs") . "top.weSidebar.load('" . $linkArray[2][$i] . "');\"  onMouseOver=\"top.info('ID: " . $linkArray[6][$i] . "');\" onMouseOut=\"top.info(' ')\" " . $linkArray[4][$i] . " ");
				} else{ //  without parameters
					//$javascriptCode = " onclick=\"" . self::getClassVars("vtabSrcDocs") . "top.weSidebar.load('" . $linkArray[2][$i] . "');return true;\" onMouseOver=\"top.info('ID: " . $linkArray[6][$i] . "');\" onMouseOut=\"top.info(' ')\" ".$linkArray[4][$i]." ";
					$javascriptCode = "  onMouseOver=\"top.info('ID: " . $linkArray[6][$i] . "');\" onMouseOut=\"top.info(' ')\" " . $linkArray[4][$i] . " ";
				}

				$destCode = str_replace($linkArray[0][$i], "<" . $linkArray[1][$i] . $linkArray[2][$i] . "\"" . $javascriptCode . ">", $destCode);
			}
		}

		return $destCode;
	}

	/**
	 * we_SEEM::replaceLinks()
	 * @desc     Here all the found links in the examined code are replaced.
	 *
	 * @param    srcCode     string the source code
	 * @param    linkArray   array with all links
	 * @return   code        string the new HTML code with for SEEM changed links
	 */
	static function replaceLinks($srcCode, $linkArray){
		if(!isset($linkArray[0])){
			return $srcCode;
		}
		//	This is Code, to have the same effect like pressing a vertical tab
		$destCode = $srcCode;

		for($i = 0; $i < count($linkArray[0]); $i++){

			if($linkArray[6][$i] != -1){ //  The target of the Link is a webEdition - Document.
				if($linkArray[3][$i] != ""){ //  we have several parameters, deal with them
					$theParameterArray = self::getAttributesFromGet($linkArray[3][$i], 'we_cmd');

					if(array_key_exists("we_objectID", $theParameterArray)){ //	target is a object
						$javascriptCode = " onclick=\"" . self::getClassVars("vtabSrcObjs") . "top.doClickDirect('" . $theParameterArray["we_objectID"] . "','objectFile','" . OBJECT_FILES_TABLE . "');\" onMouseOver=\"top.info('ID: " . $theParameterArray["we_objectID"] . "');\" onMouseOut=\"top.info(' ')\" ";
					} else{ //	target is a normal file.
						$theParameters = self::arrayToParameters($theParameterArray, "", array('we_cmd'));
						$javascriptCode = " onclick=\"" . self::getClassVars("vtabSrcDocs") . "top.doClickWithParameters('" . $linkArray[6][$i] . "','" . $linkArray[7][$i] . "','" . FILE_TABLE . "', '" . $theParameters . "');\"  onMouseOver=\"top.info('ID: " . $linkArray[6][$i] . "');\" onMouseOut=\"top.info(' ')\" ";
					}
				} else{ //  without parameters
					$javascriptCode = " onclick=\"" . self::getClassVars("vtabSrcDocs") . "top.doClickDirect(" . $linkArray[6][$i] . ",'" . $linkArray[7][$i] . "','" . FILE_TABLE . "');return true;\" onMouseOver=\"top.info('ID: " . $linkArray[6][$i] . "');\" onMouseOut=\"top.info(' ')\" ";
				}
				$destCode = str_replace($linkArray[0][$i], "<" . $linkArray[1][$i] . "javascript://" . $linkArray[4][$i] . $javascriptCode . ">", $destCode);

				//  The target is NO webEdition - Document
			} else{

				//  Target document is on another Web-Server - leave webEdition !!!!!
				if(strpos($linkArray[5][$i], 'http://') === 0 || strpos($linkArray[5][$i], 'https://') === 0){
					$javascriptCode = " onclick=\"if(confirm('" . g_l('SEEM', "[ext_document_on_other_server_selected]") . "')){ window.open('" . $linkArray[5][$i] . $linkArray[3][$i] . "','_blank');top.info(' '); } else { return false; };\" onMouseOver=\"top.info('" . g_l('SEEM', "[info_ext_doc]") . "');\" onMouseOut=\"top.info(' ');\" ";
					$destCode = str_replace($linkArray[0][$i], "<" . $linkArray[1][$i] . "javascript://" . $linkArray[4][$i] . $javascriptCode . ">", $destCode);
				} else{ //  Target is on the same Web-Server - open doc with webEdition.
					if(strpos($linkArray[5][$i], WEBEDITION_DIR . 'we_cmd.php') === 0){ //  it is a command link - use open_document_with_parameters
						//  Work with the parameters
						$theParameters = "";

						if($linkArray[3][$i] != ""){
							$theParameterArray = self::getAttributesFromGet($linkArray[3][$i], 'we_cmd');
							$theParameters = self::arrayToParameters($theParameterArray, "", array('we_cmd'));
						}

						$javascriptCode = (array_key_exists("we_objectID", $theParameterArray) ? //	target is a object
								" onclick=\"" . self::getClassVars("vtabSrcObjs") . "top.doClickDirect('" . $theParameterArray["we_objectID"] . "','objectFile','" . OBJECT_FILES_TABLE . "');\" onMouseOver=\"top.info('ID: " . $theParameterArray["we_objectID"] . "');\" onMouseOut=\"top.info(' ')\" " :
								" onclick=\"top.doClickWithParameters('" . $GLOBALS['we_doc']->ID . "','text/webedition','" . FILE_TABLE . "', '" . $theParameters . "');top.info(' ');\" onMouseOver=\"top.info('" . g_l('SEEM', "[info_doc_with_parameter]") . "');\" onMouseOut=\"top.info(' ');\"");

						$destCode = str_replace($linkArray[0][$i], "<" . $linkArray[1][$i] . "javascript://\"" . $javascriptCode . $linkArray[4][$i] . " >", $destCode);
					} else{
						//	This is a javascript:history link, to get back to the last document.
						$javascriptCode = (strpos($linkArray[2][$i], "javascript") === 0 && strpos($linkArray[2][$i], "history") ?
								' onclick="' . we_message_reporting::getShowMessageCall(g_l('SEEM', '[link_does_not_work]'), we_message_reporting::WE_MESSAGE_FRONTEND) . "\" onMouseOver=\"top.info('" . g_l('SEEM', "[info_link_does_not_work]") . "')\" onMouseOut=\"top.info('');\"" :
								//  Check, if the current document was changed
								" onclick=\"if(confirm('" . g_l('SEEM', "[ext_doc_selected]") . "')){top.doExtClick('" . $linkArray[5][$i] . $linkArray[3][$i] . "');top.info(' ');} else { return false; };\" onMouseOver=\"top.info('" . g_l('SEEM', "[info_ext_doc]") . "');\" onMouseOut=\"top.info(' ')\" ");

						$destCode = str_replace($linkArray[0][$i], "<" . $linkArray[1][$i] . "javascript://\"" . $linkArray[4][$i] . $javascriptCode . ">", $destCode);
					}
				}
			}
		}
		return $destCode;
	}

	/**
	 * we_SEEM::getAllHrefs()
	 *
	 * @desc     Returns array with all <a hrefs ...> of the given HTML-Code
	 *
	 * @param    code        string Some HTML, PHP-Code
	 * @return   allLinks    array containing all <a href ...>-Tags, the targets and parameters
	 */
	static function getAllHrefs($code){

		$trenner = "[\040|\n|\t|\r]*";
		$allLinks = array();
		//  <a href="(Ziele)(?Parameter)" ...> Ziele und Parameter eines Links ermitteln.
		//  $pattern = "/<(a".$trenner."[^>]+href".$trenner."[=\"|=\'|=\\\\|=]*".$trenner.")([^\'\">\040? ]*)([^\"\' \040\\\\]*)(".$trenner."[^>]*)>/sie";
		// der pattern ist aber nicht mit neuem PCRE kompatibel, da = nicht excaped wird, sollte sein -Armin Schulz
		//  $pattern = "/<(a".$trenner."[^>]+href".$trenner."[\=\"|\=\'|\=\\\\|\=]*".$trenner.")([^\'\">\040? ]*)([^\"\' \040\\\\]*)(".$trenner."[^>]*)>/sie";
		//  Now no more mistake, when href=\" ... \" ...
		// Auch dieser pattern excaped das = nicht richtig, entsprechend ersetzt durch Armin Schulz
		// $pattern = "/<(a".$trenner."[^>]+href".$trenner."[=\"|=\'|=|=\\\\]*".$trenner.")([^\'\">\040? \\\]*)([^\"\' \040\\\\>]*)(".$trenner."[^>]*)>/sie";
		$pattern = '/<(a' . $trenner . '[^>]+href' . $trenner . "[\=\"|\=\'|\=|\=\\\\]*" . $trenner . ")([^\'\">\040? \\\]*)([^\"\' \040\\\\>]*)(" . $trenner . '[^>]*)>/sie';

		preg_match_all($pattern, $code, $allLinks);
		return $allLinks;
	}

	/**
	 * we_SEEM::findRelativePaths()
	 * @desc     Replaces all relative Paths which point to the webEdition-Server, by the relative Translation
	 *
	 * @see      we_SEEM::translateRelativePath
	 *
	 * @param    foundPaths      array with all paths in the document
	 * @return   relativePaths   array with the relative translation of the paths
	 */
	static function findRelativePaths($foundPaths){

		$relativePaths = array();
		$url = getServerUrl();
		foreach($foundPaths as $i => $path){
			$relativePaths[$i] = self::translateRelativePath(str_replace($url, '', $path));
		}
		return $relativePaths;
	}

	/**
	 * we_SEEM::translateRelativePath()
	 * @desc     Found paths must be translated to the relative path from the document root of the webserver
	 *
	 * @param    path            string a path found in a link
	 * @return   path            string absulute translation of the path matching from the DOCUMENT_ROOT
	 */
	static function translateRelativePath($path){

		//	Take the path of the doc to find out, if the same doc is target
		//	or from the url of the document (only when extern)
		//	or none, when the full path is known (getJavaScriptCommandForOneLink)
		$tmpPath = isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : (isset($_REQUEST["url"]) ? str_replace(getServerUrl(), "", $_REQUEST["url"]) : "");

		//  extern or as absolut recognized paths shall not be changed.
		if(substr($path, 0, 1) != "/" && strpos($path, "http://") === FALSE && strpos($path, "https://") === FALSE){
			$tmpPath = substr($tmpPath, 0, strrpos($tmpPath, '/'));
			while(substr($path, 0, 3) == '../') {
				$path = substr($path, 3);
				$tmpPath = substr($tmpPath, 0, strrpos($tmpPath, '/'));
			}
			return $tmpPath . '/' . $path;
		} else{
			return $path;
		}
	}

	/**
	 * we_SEEM::getDocIDsByPaths()
	 * @desc     This function searches the DocID from a given array of Paths. It is used to look for targets of links
	  and targets of forms.
	 *
	 * @see      we_SEEM::getDocIDbyPath
	 *
	 * @param    docPaths    array of Paths to documents
	 * @return   docIds      array with the document-id ofthe correspending document
	 *
	 */
	static function getDocIDsByPaths($docPaths){
		$docIds = array();
		$db = new DB_WE();
		foreach($docPaths as $path){

			//  if the link still begins with "http://", the links points to no we-document, so we neednt look for his id
			//	all links to same webServer have been removed
			$docIds[] = (strpos($path, "http://") === 0 || strpos($path, "https://") === 0 ? -1 : self::getDocIDbyPath($path, '', $db));
		}
		return $docIds;
	}

	/**
	 * we_SEEM::getDocIDbyPath()
	 * @desc     Looks for the document-ID of a document with a certain path, if no document was found, -1 is returned
	 *
	 * @param    docPath         string path on the server.
	 * @param    tbl             string table to look for the paths
	 * @return   ID              string Document-ID to which the path belongs to or -1
	 */
	static function getDocIDbyPath($docPath, $tbl = "", $db = ''){
		//FIXME: does this work for SEO Url's???
		$db = ($db ? $db : new DB_WE());
		$docPath = $db->escape(trim($docPath));
		if(NAVIGATION_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && $docPath[strlen($docPath) - 1] == '/'){
			$indexFileNames = array_map('trim', explode(',', $db->escape(NAVIGATION_DIRECTORYINDEX_NAMES)));
			$docPath = $docPath . implode('","' . $docPath, $indexFileNames);
		}
		$id = f('SELECT ID FROM ' . $db->escape($tbl ? $tbl : FILE_TABLE) . ' WHERE Path IN ("' . $docPath . '") LIMIT 1', 'ID', $db);
		return $id ? $id : -1;
	}

	/**
	 * we_SEEM::removeSEEMLinks()
	 * @desc     All SEEM-Links are removed from the array, they will be handled seperately
	 *
	 * @param    oldArray        array with all found hrefs
	 * @return   array
	 */
	static function removeSEEMLinks($oldArray){
		$newArray = array();

		for($i = 0, $j = 0; $i < count($oldArray[2]); $i++){
			if(preg_match('/ seem="(.*)"/', $oldArray[0][$i])){
				//  This link is a SEEM Link, this is handled seperately - so it will be removed
			} else{
				$newArray[0][$j] = $oldArray[0][$i];
				$newArray[1][$j] = $oldArray[1][$i];
				$newArray[2][$j] = $oldArray[2][$i];
				$newArray[3][$j] = $oldArray[3][$i];
				$newArray[4][$j] = $oldArray[4][$i];
				$j++;
			}
		}
		return (empty($newArray) ? false : $newArray);
	}

	/**
	 * we_SEEM::onlyUseHyperlinks()
	 * @desc     All unnecessary links (like mailto, javascript, ..) are removed from the found links. If all links are
	  removed from the array false is returned. In this function other protocols like ftp can be removed as well
	 *
	 * @param    oldArray        array with all found hrefs
	 * @return   newArray        array - false if all links were removed or array of hyperlinks
	 */
	static function onlyUseHyperlinks($oldArray){

		$newArray = array();

		for($i = 0; $i < count($oldArray[2]); $i++){
			if(substr($oldArray[2][$i], 0, 1) == '#' || strpos($oldArray[2][$i], "javascript") === 0 && strpos($oldArray[2][$i], "javascript:history") === FALSE || strpos($oldArray[2][$i], "mailto") === 0 || strpos($oldArray[2][$i], "document:") === 0 || strpos($oldArray[2][$i], "object:") === 0){
				//  this link must not be changed - so it will be removed
			} else{
				$newArray[0][] = $oldArray[0][$i];
				$newArray[1][] = $oldArray[1][$i];
				$newArray[2][] = $oldArray[2][$i];
				$newArray[3][] = $oldArray[3][$i];
				$newArray[4][] = $oldArray[4][$i];
			}
		}

		return (empty($newArray)) ? false : $newArray;
	}

	/**
	 * we_SEEM::getAllForms
	 *
	 * @desc    This function searches all <form>-tags in the given code and saves them in an array
	 *
	 * @param   code        string   the source code of a special document
	 * @return  allForms    array with all found form-tags
	 */
	static function getAllForms($code){
		$allForms = array();
		$pattern = "/<form[^>]*>/sie";

		preg_match_all($pattern, $code, $allForms);

		return $allForms;
	}

	/**
	 * we_SEEM::getPathsFromForms
	 *
	 * @desc Searches the action of the given <form>-tags - if no action is given -1 is returned
	 *
	 * @param   formArray   array with all forms
	 * @return  thePaths    array with all actions of the given form-tags
	 */
	static function getPathsFromForms($formArray){
		$thePaths = array();

		for($i = 0; $i < count($formArray[0]); $i++){
			$theAttribs = self::getAttributesFromTag($formArray[0][$i]);
			$thePaths[$i] = (isset($theAttribs["action"]) ?
					$theAttribs["action"] :
					(isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->Path : $_REQUEST["filepath"]));
		}
		return $thePaths;
	}

	/**
	 * we_SEEM::getAttributesFromGet
	 *
	 * @desc    Searches a string for get-Parameters and gives them back in an array.
	  The string must begin (and should end) with "&", name, value pairs must be seperated with "="
	 *
	 * @param   $paraStr    string with all get-Parameters
	 * @param   $ignor      string variablenames which begin with this are ignored
	 *
	 * @return  $code       string with the replaced forms
	 */
	static function getAttributesFromGet($paraStr, $ignor){

		$attribs = array();

		if(substr($paraStr, 0, 1) == "?"){
			$paraStr = "&" . substr($paraStr, 1) . "&";
		}
		preg_match_all('/([^&]*=[^&]*)&/U', $paraStr, $parameters);

		//  now get the single attributes and remember path
		foreach($parameters[1] as $par){

			list($key, $value) = explode('=', $par);
			if(substr($key, 0, strlen($ignor)) != $ignor){
				$attribs[$key] = $value;
			}
		}
		return $attribs;
	}

	/**
	 * we_SEEM::getAttributesFromTag
	 *
	 * @desc    Searches a tag for Parameters and gives them back in an assoziative array.
	 *
	 * @param   tag         string the complete <form> tag
	 *
	 * @return  attribs     array (assoziative) with name/value pairs of the Parameters in the form
	 */
	static function getAttributesFromTag($tag){
		$attribs = array();
		$trenner = "[\040|\n|\t|\r]*";
		$parameters = array();

		preg_match_all('/(\w+)' . $trenner . '=' . $trenner . "[\"|\']?([^\"|\'|\040|>]*)[\"|\']?/i", $tag, $parameters);

		for($j = 0; $j < count($parameters[1]); $j++){
			$attribs[$parameters[1][$j]] = $parameters[2][$j];
		}
		return $attribs;
	}

	/**
	 * we_SEEM::rebuildForms
	 *
	 * @desc    Replaces all possible forms in the document, so they will be opened within webEdition
	 *
	 * @param   code        string of the source code
	 * @param   formArray   array with all form tags and their actions
	 *
	 * @return  code        string the new code
	 */
	static function rebuildForms($code, $formArray){
		for($i = 0; $i < count($formArray[0]); $i++){

			$theAttribs = self::getAttributesFromTag($formArray[0][$i]);
			$newForm = '<form';

			if($formArray[2][$i] == -1 && (strpos($formArray[1][$i], "http://") === 0 || strpos($formArray[1][$i], "https://") === 0)){ // Formular is on another webServer
				$newForm .= " onsubmit='if(confirm(\"" . g_l('SEEM', "[ext_form_target_other_server]") . "\")){return true;} else {return false;};' target='_blank'";

				foreach($theAttribs as $key => $value){
					//  the target must be changed and shall open in a new window
					if(strtolower($key) != 'target'){
						$newForm .= ' ' . $key . '="' . $value . '"';
					}
				}
				if(substr($newForm, strlen($newForm), 1) != '>'){
					$newForm .= '>';
				}
			} else{

				// target is a webEdition Document
				$newForm .= ' target="load" action="' . WEBEDITION_DIR . 'we_cmd.php"' .
					($formArray[2][$i] != -1 || strpos($formArray[1][$i], WEBEDITION_DIR . 'we_cmd.php') === 0 ? '' :
						" onsubmit='if(confirm(\"" . g_l('SEEM', "[ext_form_target_we_server]") . "\")){return true;} else {return false;};'");

				foreach($theAttribs as $key => $value){
					if(strtolower($key) == "target" || strtolower($key) == "action"){

					} else{
						$newForm .= ' ' . $key . '="' . $value . '"';
					}
				}
				if(substr($newForm, strlen($newForm), 1) != '>'){
					$newForm .= '>';
				}
				//  Now add some hidden fields.
				$newForm .= '<input type="hidden" name="we_cmd[0]" value="open_form_in_editor"><input type="hidden" name="original_action" value="' . $formArray[1][$i] . '" />';
			}

			$code = str_replace($formArray[0][$i], $newForm, $code);
		}
		return $code;
	}

	/**
	 * we_SEEM::arrayToParameters
	 *
	 * @desc    Takes all values from the "array" and generates an get-String from this data.
	  Ignores parameters with names in $ignor. Returns the string with parameters
	 *
	 * @param   array       array
	 * @param   arrayname   string
	 * @param   ignor       array
	 * @return  string
	 */
	static function arrayToParameters($array, $arrayname, $ignor){

		//	possible improvement - handle none arrays first!!!!!

		$ignor = array_merge($ignor, array_keys($_COOKIE));

		$parastr = '';
		foreach($array AS $key => $val){
			if(!in_array($key, $ignor)){
				if($arrayname != ''){
					$key = '[' . $key . ']';
				}
				if(is_array($val)){
					$parastr .= self::arrayToParameters($val, $arrayname . $key, $ignor);
				} else if($val != ''){
					$parastr .= '&' . $arrayname . $key . '=' . $val;
				}
			}
		}
		return strlen($parastr) > 255 ? substr($parastr, 0, 255) : $parastr;
	}

	/**
	 *
	 * Gets the correct JavaScript-command for one single link.
	 * The result is the same like selecting the document in the left side in the
	 * document-tree
	 *
	 * @param   link   		string
	 * @return  string
	 */
	static function getJavaScriptCommandForOneLink($link){

		$linkArray = self::getAllHrefs($link);

		//  Remove all other Stuff from the linkArray
		//  Here all SEEM - Links are removed as well
		$linkArray = self::onlyUseHyperlinks($linkArray);

		//  if an array is returned in onlyUseHyperlinks, then parse the $code, otherwise return the same code.
		if($linkArray && is_array($linkArray)){

			//  Remove all javascript, or target stuff, from links, they could disturb own functionality
			//  Important are $linkArray[1][*] and $linkArray[4][*1]
			$linkArray = self::cleanLinks($linkArray);

			//  $linkArray[5] - Array of the relative translation of given Link-targets, only with webEdition-Docs
			$linkArray[5] = self::findRelativePaths($linkArray[2]);

			//  $linkArray[6] - Array which contains the docIds of the Documents, or -1
			$linkArray[6] = self::getDocIDsByPaths($linkArray[5]);

			//	$linkArray[7] - contains the ContentTypes of the target, or ''
			$linkArray[7] = self::getDocContentTypesByID($linkArray[6]);

			$code = self::link2we_cmd($linkArray);
		}
		return $code;
	}

	/**
	 * Parses a single link (with several data stored in $linkarray) to a we_cmd!
	 *
	 * @param   linkArray	array
	 * @return  string
	 */
	static function link2we_cmd($linkArray){

		$i = 0;

		$code = "";

		//  The target of the Link is a webEdition - Document.
		if($linkArray[6][$i] != -1){

			if($linkArray[3][$i] != ""){ //  we have several parameters, deal with them
				$theParameterArray = self::getAttributesFromGet($linkArray[3][$i], 'we_cmd');

				if(array_key_exists("we_objectID", $theParameterArray)){ //	target is a object
					$code = self::getClassVars("vtabSrcObjs") . "top.doClickDirect('" . $theParameterArray["we_objectID"] . "','objectFile','" . OBJECT_FILES_TABLE . "');";
				} else{ //	target is a normal file.
					$theParameters = self::arrayToParameters($theParameterArray, "", array('we_cmd'));
					$code = self::getClassVars("vtabSrcDocs") . "top.doClickWithParameters('" . $linkArray[6][$i] . "','" . $linkArray[7][$i] . "','" . FILE_TABLE . "', '" . $theParameters . "');";
				}
			} else{ //	No Parameters
				$code = self::getClassVars("vtabSrcDocs") . "top.doClickDirect(" . $linkArray[6][$i] . ",'" . $linkArray[7][$i] . "','" . FILE_TABLE . "');";
			}

			//  The target is NO webEdition - Document
		} else{

			//  Target document is on another Web-Server - leave webEdition !!!!!
			if(strpos($linkArray[5][$i], "http://") === 0){

				$code = "window.open('" . $linkArray[5][$i] . $linkArray[3][$i] . "','_blank');";

				//  Target is on the same Werb-Server - open doc with webEdition.
			} else{
				//  it is a command link - use open_document_with_parameters

				if(strpos($linkArray[5][$i], WEBEDITION_DIR . 'we_cmd.php') === 0){

					//  Work with the parameters
					$theParameters = "";

					if($linkArray[3][$i] != ""){
						$theParametersArray = self::getAttributesFromGet($linkArray[3][$i], 'we_cmd');
						$theParameters = self::arrayToParameters($theParametersArray, "", array('we_cmd'));
					}

					if(!isset($GLOBALS['we_doc'])){
						$GLOBALS['we_doc']->ID = $_SESSION['weS']['we_data'][$theParametersArray["we_transaction"]][0]["ID"];
					}

					$code = (isset($theParameterArray) && is_array($theParameterArray) && array_key_exists("we_objectID", $theParameterArray) ? //	target is a object
							"top.doClickDirect('" . $theParameterArray["we_objectID"] . "','objectFile','" . OBJECT_FILES_TABLE . "')" :
							"top.doClickWithParameters('" . $GLOBALS['we_doc']->ID . "','text/webedition','" . FILE_TABLE . "', '" . $theParameters . "')");
				} else{
					//  we cant save data so we neednt make object
					//	not recognized change of document
					$code = "top.doExtClick('" . $linkArray[5][$i] . $linkArray[3][$i] . "');";
				}
			}
		}
		return $code;
	}

	/**
	 * @return array
	 * @param array $_docIDArray
	 * @desc Searchs the contentTypes of the gibven document ids - saves them in an array and returns them
	 */
	static function getDocContentTypesByID($_docIDArray){

		$_docContentTypes = array();
		$db = new DB_WE();
		foreach($_docIDArray as $i => $cur){
			$_docContentTypes[$i] = ($cur != -1 ? self::getDocContentTypeByID($cur, $db) : '');
		}
		return $_docContentTypes;
	}

	/**
	 * @return string
	 * @param int $id
	 * @desc Looks for the ContentType of the document with the given id and returns it
	 */
	static function getDocContentTypeByID($id, $db = ''){
		return f('SELECT ContentType FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'ContentType', ($db ? $db : new DB_WE()));
	}

	/**
	 * we_SEEM::addEditButtonToTag
	 *
	 * @desc    This function adds an edit-button to a tag, when in SEEM-Mode.
	  The button has the same effect, like switching the tab at the top of the page.
	  disabled at moment
	 *
	 * @return  string
	 */
	static function addEditButtonToTag($which = "edit"){
		return '';
		/* 		if($GLOBALS["we_transaction"] != "" && $GLOBALS['we_doc']->EditPageNr == WE_EDITPAGE_PREVIEW && isset($_SESSION['weS']['we_mode']) && $_SESSION['weS']['we_mode'] == "seem"){
		  return "";
		  } else{
		  return "";
		  } */
	}

}
