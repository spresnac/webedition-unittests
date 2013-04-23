<?php

/**
 * webEdition SDK
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
/**
 * @see we_ui_abstract_AbstractElement
 */
Zend_Loader::loadClass('we_ui_abstract_AbstractFormElement');
Zend_Loader::loadClass('we_ui_controls_Button');
Zend_Loader::loadClass('we_ui_controls_Label');
Zend_Loader::loadClass('we_ui_layout_Table');



/**
 * Class to display an webEdition Wysiwyg-Editor
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_controls_WeWysiwygEditor extends we_ui_abstract_AbstractFormElement{

	/**
	 * text content of the textarea
	 *
	 * @var string
	 */
	protected $_text = '';

	/**
	 * object of the we_ui_controls_button class
	 *
	 * @var object
	 */
	protected $_buttonObj;

	/**
	 * object of the we_ui_controls_Label
	 *
	 * @var object
	 */
	protected $_labelObj;

	/**
	 * object of the we_ui_layout_Table
	 *
	 * @var object
	 */
	protected $_layouttableObj;

	/**
	 * Label text
	 *
	 * @var string
	 */
	protected $_labelText = '';

	/**
	 * preset of Label style
	 *
	 * @var string
	 */
	protected $_labelStyle = 'display:block;';

	/**
	 * name of the application
	 *
	 * @var string
	 */
	protected $_appName = '';

	/**
	 * button text
	 *
	 * @var string
	 */
	protected $_buttonText = '';

	/**
	 * button onClick attribute
	 *
	 * @var string
	 */
	protected $_buttonOnClick = '';

	/**
	 * button title
	 *
	 * @var string
	 */
	protected $_buttonTitle = '';

	/**
	 * onChange attribute
	 *
	 * @var string
	 */
	protected $_onChange = '';

	/**
	 * dialogWidth attribute
	 *
	 * @var string
	 */
	protected $_dialogWidth = '700';

	/**
	 * dialogHeight attribute
	 *
	 * @var string
	 */
	protected $_dialogHeight = '400';

	/**
	 * cssClasses attribute
	 *
	 * @var string
	 */
	protected $_cssClasses = '';

	/**
	 * fonts attribute
	 *
	 * @var string
	 */
	protected $_fonts = '';

	/**
	 * commands attribute
	 *
	 * @var string
	 */
	protected $_commands = '';

	/**
	 * preview attribute
	 *
	 * @var string
	 */
	protected $_previewStyle = '';

	/**
	 * Constructor
	 *
	 * Sets object properties if set in $properties array
	 *
	 * @param array $properties associative array containing named object properties
	 * @return void
	 */
	function __construct($properties = null){

		//get object from a button
		$this->_buttonObj = new we_ui_controls_Button();

		//set properties
		parent::__construct($properties);

		// add needed CSS files
		$this->addCSSFile(we_ui_layout_Themes::computeCSSURL(__CLASS__));
		$this->addCSSFile(we_ui_layout_Themes::computeCSSURL('we_ui_controls_WeWysiwygEditor'));
		$this->addCSSFiles($this->_buttonObj->getCSSFiles());

		// add needed JS Files
		$this->addJSFile(we_ui_abstract_AbstractElement::computeJSURL(__CLASS__));
		$this->addJSFile(we_ui_abstract_AbstractElement::computeJSURL('we_ui_controls_WeWysiwygEditor'));
		$this->addJSFile(JS_DIR . "utils/prototypes.js");
		$this->addJSFiles($this->_buttonObj->getJSFiles());
	}

	/**
	 * Set text attribute
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text){
		$this->_text = $text;
	}

	/**
	 * Retrieve text
	 *
	 * @return string
	 */
	public function getText(){
		return $this->_text;
	}

	/**
	 * Set dialogWidth attribute
	 *
	 * @param string $text
	 * @return void
	 */
	public function setDialogWidth($width){
		$this->_dialogWidth = $width;
	}

	/**
	 * Retrieve dialogWidth
	 *
	 * @return string
	 */
	public function getDialogWidth(){
		return $this->_dialogWidth;
	}

	/**
	 * Set dialogWidth attribute
	 *
	 * @param string $text
	 * @return void
	 */
	public function setDialogHeight($height){
		$this->_dialogHeight = $height;
	}

	/**
	 * Retrieve dialogHeight
	 *
	 * @return string
	 */
	public function getDialogHeight(){
		return $this->_dialogHeight;
	}

	/**
	 * Set cssClasses attribute
	 *
	 * @param string $text
	 * @return void
	 */
	public function setCssClasses($classes){
		$this->_cssClasses = $classes;
	}

	/**
	 * Retrieve cssClasses
	 *
	 * @return string
	 */
	public function getCssclasses(){
		return $this->_cssClasses;
	}

	public function setPreviewStyle($style){
		$this->_previewStyle = $style;
	}

	/**
	 * Retrieve cssClasses
	 *
	 * @return string
	 */
	public function getPreviewStyle(){
		return $this->_previewStyle;
	}

	public function setFonts($fonts){
		$this->_fonts = $fonts;
	}

	/**
	 * Retrieve Fonts
	 *
	 * @return string
	 */
	public function getFonts(){
		return $this->_fonts;
	}

	public function setCommands($commands){
		$this->_commands = $commands;
	}

	/**
	 * Retrieve commands
	 *
	 * @return string
	 */
	public function getCommands(){
		return $this->_commands;
	}

	/**
	 * Retrieve name of the application
	 *
	 * @return string
	 */
	public function getAppName(){
		return $this->_appName;
	}

	/**
	 * Retrieve onChange attribute
	 *
	 * @return string
	 */
	public function getOnChange(){
		return $this->_onChange;
	}

	/**
	 * Retrieve button text
	 *
	 * @return string
	 */
	public function getButtonText(){
		return $this->_buttonText;
	}

	/**
	 * Retrieve button title
	 *
	 * @return string
	 */
	public function getButtonTitle(){
		return $this->_buttonTitle;
	}

	/**
	 * Set name of the application
	 *
	 * @param boolean $_appName
	 */
	public function setAppName($_appName){
		$this->_appName = $_appName;
	}

	/**
	 * Set Label text
	 *
	 * @param string $_Text
	 */
	public function setLabelText($_Text){
		$this->_labelText = $_Text;
	}

	/**
	 * Set Label text
	 *
	 * @param string $_Text
	 */
	public function getLabelText(){
		return $this->_labelText;
	}

	/**
	 * Set Label style
	 *
	 * @param string $_Style
	 */
	public function setLabelStyle($_Style){
		$this->_labelStyle .= ' ' . $_Style;
	}

	/**
	 * Get Label style
	 *
	 * @return string
	 */
	public function getLabelStyle(){
		return $this->_labelStyle;
	}

	/**
	 * Set onChange attribute
	 *
	 * @param string $_onChange
	 */
	public function setOnChange($_onChange){
		$this->_onChange = $_onChange;
	}

	/**
	 * Set button text
	 *
	 * @param string $_buttonText
	 */
	public function setButtonText($_buttonText){
		$this->_buttonText = $_buttonText;
	}

	/**
	 * Set button onClick attribute
	 *
	 * @param string $_buttonOnClick
	 */
	public function setButtonOnClick($_buttonOnClick){
		$this->_buttonOnClick = $_buttonOnClick;
	}

	/**
	 * Set button title
	 *
	 * @param string $_buttonTitle
	 */
	public function setButtonTitle($_buttonTitle){
		$this->_buttonTitle = $_buttonTitle;
	}

	/**
	 * Retrieve button onClick attribute
	 *
	 * @return string
	 */
	public function getButtonOnClick(){


		//TODO  auch bei der ausgangsbasis noch nicht gemacht (Fileselektor)
		$onChange = '"opener.weEventController.fire(\'docChanged\')"';
		$onChange = '""';

		if($this->getFonts() != ''){
			$Fonts = '","' . $this->getFonts();
		} else{
			$Fonts = '';
		}
		$Fieldname = $this->getName();
		if($this->getAppName() !== ''){
			$appname = $this->getAppName();
			return 'we_ui_controls_WeWysiwygEditor.openWeWysiwyg("' . $appname . '","' . $Fieldname . '",' . $this->getDialogWidth() . ',' . $this->getDialogHeight() . ',' . $onChange . ',"' . $this->getCommands() . '","' . $this->getCssClasses() . $Fonts . '")';
		}
		$onClick = 'we_ui_controls_WeWysiwygEditor.openWeWysiwyg("' . $appname . '","' . $Fieldname . '",' . $this->getDialogWidth() . ',' . $this->getDialogHeight() . ', ' . $onChange . ',"' . $this->getCommands() . '","' . $this->getCssClasses() . $Fonts . '")';
		return $onClick;
	}

	/**
	 * Get HTML of Button
	 *
	 * @return string
	 */
	public function getButton(){
		$this->_buttonObj->setId('yuiWysiwigButton_' . $this->getId());
		$this->_buttonObj->setText($this->getButtonText());
		$this->_buttonObj->setType('onClick');
		$this->_buttonObj->setTitle($this->getButtonTitle());
		$this->_buttonObj->setDisabled($this->getDisabled());
		$this->_buttonObj->setHidden($this->getHidden());
		$this->_buttonObj->setWidth(120);
		$this->_buttonObj->setOnClick($this->getButtonOnClick());

		return $this->_buttonObj->getHTML();
	}

	/**
	 * Renders and returns HTML
	 *
	 * @return string
	 */
	public function _renderHTML(){

		$this->_layouttableObj = new we_ui_layout_Table();
		$this->_layouttableObj->setWidth($this->getWidth());
		$this->_layouttableObj->setCellAttributes(array('align' => 'right'), 1);
		$this->_labelObj = new we_ui_controls_Label();
		$this->_labelObj->setId($this->getId() . '_Label');
		$this->_labelObj->setText($this->getLabelText());
		$this->_labelObj->setStyle($this->getLabelStyle());

		$this->_layouttableObj->addElement($this->_labelObj, 0, 0);

		$this->_layouttableObj->addHTML($this->getButton(), 1, 0);

		$html = $this->_layouttableObj->getHTML();

		$html .= '<div id="' . $this->getId() . '_View" style="border:1px solid white;width:' . $this->getWidth() . 'px;' . $this->getPreviewStyle() . '" >' . parseInternalLinks($this->getText(), 0) . '</div><textarea style="display:none" id="' . $this->getId() . '" name="' . $this->getName() . '" />' . $this->getText() . '</textarea>';
		if($this->getHidden()){
			$this->_style .= 'display:none;';
		}
		$_SESSION['WEAPP_' . $this->getAppName() . '_' . $this->getName()] = $this->getText();
		return '<div id="' . $this->getId() . '_Container" ' . $this->_getComputedStyleAttrib() . '>' . $html . '</div>';
	}

	/**
	 * Provides the code for the OnSubmit Event
	 *
	 * @return string
	 */
	public function getOnBeforeSubmitJS(){
		return ' document.getElementById("' . $this->getId() . '").value=document.getElementById("' . $this->getId() . '_Daten").innerHTML;';
	}

}