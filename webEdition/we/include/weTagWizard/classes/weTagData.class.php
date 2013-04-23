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
class weTagData{

	private $Exists = false;

	/**
	 * @var string
	 */
	public $Name;

	/**
	 * @var string
	 */
	private $TypeAttribute = null;

	/**
	 * @var array
	 */
	private $Attributes = array();
	private $UsedAttributes = null;

	/**
	 * @var string
	 */
	private $Description;

	/**
	 * @var string
	 */
	private $DefaultValue;

	/**
	 * @var string
	 */
	private $NeedsEndTag = false;
	private $Module = 'basis';
	private $Groups = array();
	private $Deprecated = false;
	private $noDocuLink = false;

	private function __construct($tagName){
		$this->Name = $tagName;
		// include the selected tag, its either normal, or custom tag
		if(file_exists(WE_INCLUDES_PATH . 'weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php')){
			require (WE_INCLUDES_PATH . 'weTagWizard/we_tags/we_tag_' . $tagName . '.inc.php');
			$this->Exists = true;
		} else
		if(file_exists(WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php')){
			require (WE_INCLUDES_PATH . 'weTagWizard/we_tags/custom_tags/we_tag_' . $tagName . '.inc.php');
			$this->Exists = true;
			$this->Groups[] = 'custom';
		} else{
			//Application Tags
			$apptags = array();
			$alltools = weToolLookup::getAllTools(true);
			$allapptags = array();
			$allapptagnames = array();
			foreach($alltools as $tool){
				$apptags = weToolLookup::getAllToolTagWizards($tool['name']);
				$allapptags = array_merge($allapptags, $apptags);
				$apptagnames = array_keys($apptags);
				$allapptagnames = array_merge($allapptagnames, $apptagnames);
			}
			if(in_array($tagName, $allapptagnames)){
				require_once ($allapptags[$tagName]);
				$this->Exists = true;
				$this->Groups[] = 'apptags';
			} else{
				t_e('requested help entry of tag ' . $tagName . ' not found');
				return;
			}
		}

		if($this->TypeAttribute){
			if(!is_array($this->TypeAttribute->Options)){
				t_e('Error in TypeAttribute of we:' . $this->Name);
			} else{
				if(!$this->noDocuLink){
					foreach($this->TypeAttribute->Options as &$value){
						$tmp = new weTagData_cmdAttribute('TagReferenz', false, '', array('open_tagreference', strtolower($tagName) . '-' . $this->TypeAttribute->getName() . '-' . $value->Name), g_l('taged', '[tagreference_linktext]'));
						$value->AllowedAttributes[] = $tmp;
						if($value->Value != '-'){
							$this->Attributes[] = $tmp;
						}
					}
				}
			}
		} else{
			if(!$this->noDocuLink){
				$this->Attributes[] = new weTagData_cmdAttribute('TagReferenz', false, '', array('open_tagreference', strtolower($tagName)), g_l('taged', '[tagreference_linktext]')); // Bug #6341
			}
		}
	}

	private function updateUsedAttributes(){
		$this->UsedAttributes = array();
		if($this->TypeAttribute){
			$this->UsedAttributes[] = $this->TypeAttribute;
		}
		foreach($this->Attributes as $attr){
			if($attr === null){
				continue;
			}
			if(!is_object($attr)){
				t_e('Error in Attributes of we:' . $this->Name, $attr);
			} else if($attr->useAttribute()){
				$this->UsedAttributes[] = $attr;
			}
		}
	}

	/**
	 * @return string
	 */
	function getName(){
		return $this->Name;
	}

	function getModule(){
		return $this->Module;
	}

	function getGroups(){
		return $this->Groups;
	}

	function isDeprecated(){
		return $this->Deprecated;
	}

	/**
	 * @return string
	 */
	function getDescription(){
		return $this->Description;
	}

	/**
	 * @param string $tagName
	 * @return weTagData
	 */
	static function getTagData($tagName){
		static $tags = array();
		if(isset($tags[$tagName])){
			$tag = $tags[$tagName];
		} else{
			$tag = new weTagData($tagName);
			if(!$tag->Exists){
				return null;
			}
			$tags[$tagName] = $tag;
		}
		$tag->updateUsedAttributes();
		return $tag;
	}

	/**
	 * @return boolean
	 */
	function needsEndTag(){
		return $this->NeedsEndTag;
	}

	/**
	 * @return array
	 */
	function getAllAttributes($idPrefix = false){

		$attribs = array();

		foreach($this->UsedAttributes as $attrib){

			if($idPrefix){
				$attribs[] = $attrib->getIdName();
			} else{
				$attribs[] = $attrib->getName();
			}
		}
		return $attribs;
	}

	/**
	 * @return mixed
	 */
	function getTypeAttribute(){
		return $this->TypeAttribute;
	}

	/**
	 * @return array
	 */
	function getRequiredAttributes(){

		$req = array();

		foreach($this->UsedAttributes as $attrib){
			if($attrib->IsRequired()){
				$req[] = $attrib->getIdName();
			}
		}
		return $req;
	}

	/**
	 * @return array
	 */
	function getTypeAttributeOptions(){

		if($this->TypeAttribute){
			return $this->TypeAttribute->getOptions();
		}
		return null;
	}

	function getAttributesForCM(){
		$attr = array();

		foreach($this->UsedAttributes as $attribute){
			$class = get_class($attribute);
			if(!$attribute->IsDeprecated() && $attribute->useAttribute() && $class != 'weTagData_linkAttribute' && $class != 'weTagData_cmdAttribute'){
				$attr[] = $attribute->getName();
			}
		}
		return $attr;;
	}

	/**
	 * @return string
	 */
	function getAttributesCodeForTagWizard(){

		$ret = '';

		$typeAttrib = $this->getTypeAttribute();

		if(count($this->UsedAttributes) > 1 || (count($this->UsedAttributes) && !$typeAttrib)){

			$ret = '<ul>';
			foreach($this->UsedAttributes as $attribute){

				if($attribute != $this->TypeAttribute){
					$ret .= '<li ' . ($typeAttrib ? 'style="display:none;"' : '') . ' id="li_' . $attribute->getIdName() . '">' . $attribute->getCodeForTagWizard() . '</li>';
				}
			}
			$ret .= '</ul>';
		}
		return $ret;
	}

	/**
	 * @return string
	 */
	function getTypeAttributeCodeForTagWizard(){
		if($this->TypeAttribute){
			return '<ul>' .
				'<li>' . $this->TypeAttribute->getCodeForTagWizard() . '</li>' .
				'</ul>';
		}
		return '';
	}

	/**
	 * @return string
	 */
	function getDefaultValueCodeForTagWizard(){

		return we_html_element::htmlTextArea(
				array(
				'name' => 'weTagData_defaultValue',
				'id' => 'weTagData_defaultValue',
				'class' => 'wetextinput wetextarea'
				), $this->DefaultValue);
	}

}
