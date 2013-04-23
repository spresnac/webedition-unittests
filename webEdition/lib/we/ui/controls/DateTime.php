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
 * @see we_ui_abstract_AbstractInputElement
 */
Zend_Loader::loadClass('we_ui_abstract_AbstractInputElement');

/**
 * Class to display a DateTime input field
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_controls_DateTime extends we_ui_abstract_AbstractInputElement{
	/**
	 * Default class name for text input fields
	 */
	const kDateTimeClas = 'we_ui_controls_DateTime';


	/**
	 * class name for disabled Select
	 */
	const kDateTimeClassDisabled = 'we_ui_controls_DateTime_disabled';

	protected $_height = 22;

	/**
	 * type attribute => overwritten
	 * @see we_ui_abstract_AbstractInputElement
	 *
	 * @var string
	 */
	protected $_type = 'datetime';

	/**
	 * maxlength attribute
	 *
	 * @var integer
	 */
	protected $_maxlength = '';

	/**
	 * size attribute
	 *
	 * @var integer
	 */
	protected $_size = '';

	/**
	 * onChange attribute
	 *
	 * @var string
	 */
	protected $_onChange = '';

	/**
	 * onBlur attribute
	 *
	 * @var string
	 */
	protected $_onBlur = '';

	/**
	 * onFocus attribute
	 *
	 * @var string
	 */
	protected $_onFocus = '';

	/**
	 * Format attribute according to php- date() function
	 *
	 * @var string
	 */
	protected $_Format = '';

	/**
	 * InitOnNoon attribute
	 *
	 * @var bool
	 */
	protected $_InitOnNoon = false;

	/**
	 * InitDayBefore attribute
	 *
	 * @var bool
	 */
	protected $_InitDayBefore = false;

	/**
	 * Minimum Year attribute
	 *
	 * @var int
	 */
	protected $_MinYear = 2000;

	/**
	 * Maximum Year attribute
	 *
	 * @var int
	 */
	protected $_MaxYear = 2039;
	protected $_ContainerId = '';
	protected $_YearsId = '';
	protected $_MonthsId = '';
	protected $_DaysId = '';
	protected $_HoursId = '';
	protected $_MinutesId = '';
	protected $_SecondsId = '';

	/**
	 * Constructor
	 *
	 * Sets object properties if set in $properties array
	 *
	 * @param array $properties associative array containing named object properties
	 * @return void
	 */
	public function __construct($properties = null){
		parent::__construct($properties);

		// add needed CSS files
		$this->addCSSFile(we_ui_layout_Themes::computeCSSURL(__CLASS__));

		// add needed JS Files
		$this->addJSFile(we_ui_abstract_AbstractElement::computeJSURL(__CLASS__));
		$this->addJSFile($GLOBALS['__WE_BASE_URL__'] . '/js/libs/yui/yahoo-min.js');
		$this->addJSFile($GLOBALS['__WE_BASE_URL__'] . '/js/libs/yui/dom-min.js');
	}

	/**
	 * Returns the computed onFocus attrib as text to insert into the HTML tag
	 *
	 * @return string
	 */
	protected function _getComputedOnFocusAttrib(){
		$onFocus = 'YAHOO.util.Dom.addClass(this, "' . self::kDateTimeClassFocus . '");';
		if($this->getOnFocus() !== ''){
			$onFocus .= $this->getOnFocus();
		}
		return ' onFocus="' . oldHtmlspecialchars($onFocus) . '"';
	}

	/**
	 * Returns the computed onBlur attrib as text to insert into the HTML tag
	 *
	 * @return string
	 */
	protected function _getComputedOnBlurAttrib(){
		$onBlur = 'YAHOO.util.Dom.removeClass(this, "' . self::kDateTimeClassFocus . '");';
		if($this->getOnBlur() !== ''){
			$onBlur .= $this->getOnBlur();
		}
		return ' onBlur="' . oldHtmlspecialchars($onBlur) . '"';
	}

	/**
	 * Set InitOnNoon attribute
	 *
	 * @param bool $wert if set to false, time() is used to initialise empty or 0 timestamps, set to true, the value is set at mid-day, to ease comparisson
	 * @return void
	 */
	public function setInitOnNoon($wert=false){
		$this->_InitOnNoon = $wert;
	}

	/**
	 * Set InitDayBefore attribute
	 *
	 * @param bool $wert if set to false, time() is used to initialise empty or 0 timestamps, set to true, the day before the current day is used
	 * @return void
	 */
	public function setInitDayBefore($wert=false){
		$this->_InitDayBefore = $wert;
	}

	/**
	 * Returns the set InitOnNoon value
	 *
	 * @return boolean
	 */
	public function getInitOnNoon(){
		return $this->_InitOnNoon;
	}

	/**
	 * Returns the set InitDayBefore value
	 *
	 * @return boolean
	 */
	public function getInitDayBefore(){
		return $this->_InitDayBefore;
	}

	/**
	 * Set Format attribute
	 *
	 * @param string $format supportes standard php- date() notation
	 * @return void
	 */
	public function setFormat($format){
		$this->_Format = $format;
	}

	/**
	 * Returns the set Date format
	 *
	 * @return string
	 */
	public function getFormat(){
		return $this->_Format;
	}

	/**
	 * Set MinimumYear attribute
	 *
	 * @param string|int $year defines the minimum year for year selection
	 * @return void
	 */
	public function setMinYear($year){
		$this->_MinYear = $year;
	}

	/**
	 * Set MaximumYear attribute
	 *
	 * @param string|int $year defines the maximum year for year selection
	 * @return void
	 */
	public function setMaxYear($year){
		$this->_MaxYear = $year;
	}

	/**
	 * Get the MinimumYear attribute
	 *
	 * @return string|int
	 */
	public function getMinYear(){
		return $this->_MinYear;
	}

	/**
	 * Get the Maximum Year attribute
	 *
	 * @return string|int
	 */
	public function getMaxYear(){
		return $this->_MaxYear;
	}

	/**
	 * Retrieve yearsId
	 *
	 * @return string
	 */
	public function getYearsId(){
		return $this->_YearsId;
	}

	/**
	 * Set yearsId
	 *
	 * @param string $id
	 * @return void
	 */
	public function setYearsId($id){
		$this->_YearsId = $id;
	}

	/**
	 * Retrieve monthsId
	 *
	 * @return string
	 */
	public function getMonthsId(){
		return $this->_MonthsId;
	}

	/**
	 * Set monthsId
	 *
	 * @param string $id
	 * @return void
	 */
	public function setMonthsId($id){
		$this->_MonthsId = $id;
	}

	/**
	 * Retrieve daysId
	 *
	 * @return string
	 */
	public function getDaysId(){
		return $this->_DaysId;
	}

	/**
	 * Set daysId
	 *
	 * @param string $id
	 * @return void
	 */
	public function setDaysId($id){
		$this->_DaysId = $id;
	}

	/**
	 * Retrieve hoursId
	 *
	 * @return string
	 */
	public function getHoursId(){
		return $this->_HoursId;
	}

	/**
	 * Set hoursId
	 *
	 * @param string $id
	 * @return void
	 */
	public function setHoursId($id){
		$this->_HoursId = $id;
	}

	/**
	 * Retrieve minutesId
	 *
	 * @return string
	 */
	public function getMinutesId(){
		return $this->_MinutesId;
	}

	/**
	 * Set minutesId
	 *
	 * @param string $id
	 * @return void
	 */
	public function setMinutesId($id){
		$this->_MinutesId = $id;
	}

	/**
	 * Retrieve secondsId
	 *
	 * @return string
	 */
	public function getSecondsId(){
		return $this->_SecondsId;
	}

	/**
	 * Set secondsId
	 *
	 * @param string $id
	 * @return void
	 */
	public function setSecondsId($id){
		$this->_SecondsId = $id;
	}

	/**
	 * Retrieve containerId
	 *
	 * @return string
	 */
	public function getContainerId(){
		return $this->_ContainerId;
	}

	/**
	 * Set containerId
	 *
	 * @param string $id
	 * @return void
	 */
	public function setContainerId($id){
		$this->_ContainerId = $id;
	}

	/**
	 * Determines the position of the year in the format string
	 *
	 * @return int
	 */
	protected function _getYearPos(){
		return max(we_html_tools::findChar("y"), we_html_tools::findChar("Y"), 0);
	}

	/**
	 * Determines the position of the month in the format string
	 *
	 * @return int
	 */
	protected function _getMonthPos(){
		return max(we_html_tools::findChar("m"), we_html_tools::findChar("M"), we_html_tools::findChar("n"), we_html_tools::findChar("F"), 0);
	}

	/**
	 * Determines the position of theday in the format string
	 *
	 * @return int
	 */
	protected function _getDayPos(){
		return max(we_html_tools::findChar("d"), we_html_tools::findChar("D"), we_html_tools::findChar("j"), 0);
	}

	/**
	 * Determines the position of the hour in the format string
	 *
	 * @return int
	 */
	protected function _getHourPos(){
		return max(we_html_tools::findChar("g"), we_html_tools::findChar("G"), we_html_tools::findChar("h"), we_html_tools::findChar("H"), 0);
	}

	/**
	 * Determines the position of the minutes in the format string
	 *
	 * @return int
	 */
	protected function _getMinutePos(){
		return max(we_html_tools::findChar("i"), 0);
	}

	/**
	 * Determines the position of the seconds in the format string
	 *
	 * @return int Position or 0 if not to display
	 */
	protected function _getSecondPos(){
		return max(we_html_tools::findChar("s"), 0);
	}

	/**
	 * called before _renderHTML() is called,
	 * overwrites function from AbstractElement completely
	 *
	 * @return void
	 */
	protected function _willRenderHTML(){
		if($this->getId() === ''){
			$this->setId(we_util_Strings::createUniqueId());
		}
		if($this->getYearsId() === ''){
			$this->setYearsId($this->getId() . "_years");
		}
		if($this->getMonthsId() === ''){
			$this->setMonthsId($this->getId() . "_months");
		}
		if($this->getDaysId() === ''){
			$this->setDaysId($this->getId() . "_days");
		}
		if($this->getHoursId() === ''){
			$this->setHoursId($this->getId() . "_hours");
		}
		if($this->getMinutesId() === ''){
			$this->setMinutesId($this->getId() . "_minutes");
		}
		if($this->getSecondsId() === ''){
			$this->setSecondsId($this->getId() . "_seconds");
		}
		if($this->getContainerId() === ''){
			$this->setContainerId($this->getId() . "_container");
		}
	}

	/**
	 * Renders and returns HTML of text input
	 *
	 * @return string
	 */
	protected function _renderHTML(){


		if($this->getValue() == 0 || $this->getValue() == ''){
			$t = time();
			if($this->getInitDayBefore()){
				$t = $t - 86400;
			}
			$stamp = getdate($t);
			if($this->getInitOnNoon()){
				$stamp['seconds'] = 0;
				$stamp['minutes'] = 0;
				$stamp['hours'] = 12;
			}

			$this->setValue(mktime($stamp['hours'], $stamp['minutes'], $stamp['seconds'], $stamp['mon'], $stamp['mday'], $stamp['year']));
		}
		$stamp = getdate($this->getValue());


		$OnChange = 'we_ui_controls_DateTime.setDateTimeValueOnChange(\'' . $this->getId() . '\');';
		$codes = array();
		$code = '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getId() . '" value="' . $this->getValue() . '"/><table cellpadding="0" cellspacing="0" border="0" id="' . $this->getContainerId() . '"' . $this->_getComputedClassAttrib($class) . $this->_getNonBooleanAttribs('onChange') . $this->_getBooleanAttribs('disabled') . ' ><tr> ';
		if($this->_getYearPos()){
			$codes[$this->_getYearPos()] = '<td><select id="' . $this->getYearsId() . '" onchange="' . $OnChange . '" ' . $this->_getComputedClassAttrib($class) . $this->_getBooleanAttribs('disabled') . ' >';
			for($i = $this->getMinYear(); $i <= $this->getMaxYear(); $i++){
				$codes[$this->_getYearPos()] .='<option value="' . $i . '" ' . ($i == $stamp['year'] ? ' selected="selected" ' : '') . '>' . $i . '</option>';
			}
			$codes[$this->_getYearPos()] .='</select></td>';
		} else{
			$code .= '<td><input type="hidden" id="' . $this->getYearsId() . '" value="' . $stamp['year'] . '" /></td>';
		}
		if($this->_getMonthPos()){
			$codes[$this->_getMonthPos()] = '<td><select id="' . $this->getMonthsId() . '" onchange="' . $OnChange . '" ' . $this->_getComputedClassAttrib($class) . $this->_getBooleanAttribs('disabled') . ' >';
			for($i = 1; $i <= 12; $i++){
				$codes[$this->_getMonthPos()] .='<option value="' . ($i < 10 ? '0' : '') . $i . '" ' . ($i == $stamp['mon'] ? ' selected="selected" ' : '') . '>' . ($i < 10 ? '0' : '') . $i . '</option>';
			}
			$codes[$this->_getMonthPos()] .='</select></td>';
		} else{
			$code .= '<td><input type="hidden" id="' . $this->getMonthsId() . '" value="' . ($stamp['mon'] < 10 ? '0' : '') . $stamp['mon'] . '" /></td>';
		}
		if($this->_getDayPos()){
			$codes[$this->_getDayPos()] = '<td><select id="' . $this->getDaysId() . '" onchange="' . $OnChange . '" ' . $this->_getComputedClassAttrib($class) . $this->_getBooleanAttribs('disabled') . ' >';
			for($i = 1; $i <= 31; $i++){
				$codes[$this->_getDayPos()] .='<option value="' . ($i < 10 ? '0' : '') . $i . '" ' . ($i == $stamp['mday'] ? ' selected="selected" ' : '') . '>' . ($i < 10 ? '0' : '') . $i . '</option>';
			}
			$codes[$this->_getDayPos()] .='</select></td>';
		} else{
			$code .= '<td><input type="hidden" id="' . $this->getDaysId() . '" value="' . ($stamp['mday'] < 10 ? '0' : '') . $stamp['mday'] . '" /></td>';
		}
		if($this->_getHourPos()){
			$codes[$this->_getHourPos()] = '<td>-<select id="' . $this->getHoursId() . '" onchange="' . $OnChange . '" ' . $this->_getComputedClassAttrib($class) . $this->_getBooleanAttribs('disabled') . ' >';
			for($i = 0; $i <= 23; $i++){
				$codes[$this->_getHourPos()] .='<option value="' . ($i < 10 ? '0' : '') . $i . '" ' . ($i == $stamp['hours'] ? ' selected="selected" ' : '') . '>' . ($i < 10 ? '0' : '') . $i . '</option>';
			}
			$codes[$this->_getHourPos()] .='</select></td>';
		} else{
			$code .= '<td><input type="hidden" id="' . $this->getHoursId() . '" value="' . ($stamp['hours'] < 10 ? '0' : '') . $stamp['hours'] . '" /></td>';
		}
		if($this->_getMinutePos()){
			$codes[$this->_getMinutePos()] = '<td>:<select id="' . $this->getMinutesId() . '" onchange="' . $OnChange . '" ' . $this->_getComputedStyleAttrib() . $this->_getComputedClassAttrib($class) . $this->_getBooleanAttribs('disabled') . ' >';
			for($i = 0; $i <= 59; $i++){
				$codes[$this->_getMinutePos()] .='<option value="' . ($i < 10 ? '0' : '') . $i . '" ' . ($i == $stamp['minutes'] ? ' selected="selected" ' : '') . '>' . ($i < 10 ? '0' : '') . $i . '</option>';
			}
			$codes[$this->_getMinutePos()] .='</select></td>';
		} else{
			$code .= '<td><input type="hidden" id="' . $this->getMinutesId() . '" value="' . ($stamp['minutes'] < 10 ? '0' : '') . $stamp['minutes'] . '" /></td>';
		}
		if($this->_getSecondPos()){
			$codes[$this->_getSecondPos()] = '<td>:<select id="' . $this->getSecondsId() . '" onchange="' . $OnChange . '" ' . $this->_getComputedStyleAttrib() . $this->_getComputedClassAttrib($class) . $this->_getBooleanAttribs('disabled') . ' >';
			for($i = 0; $i <= 59; $i++){
				$codes[$this->_getSecondPos()] .='<option value="' . ($i < 10 ? '0' : '') . $i . '" ' . ($i == $stamp['seconds'] ? ' selected="selected" ' : '') . '>' . ($i < 10 ? '0' : '') . $i . '</option>';
			}
			$codes[$this->_getSecondPos()] .='</select></td>';
		} else{
			$code .= '<td><input type="hidden" id="' . $this->getSecondsId() . '" value="' . ($stamp['seconds'] < 10 ? '0' : '') . $stamp['seconds'] . '" /></td>';
		}
		ksort($codes);
		foreach($codes as $value){
			$code .= $value;
		}

		$code .= '</tr></table>';

		return $code;
	}

	/**
	 * Retrieve maxlength attribute
	 *
	 * @return integer
	 */
	public function getMaxlength(){
		return $this->_maxlength;
	}

	/**
	 * Retrieve onBlur attribute
	 *
	 * @return string
	 */
	public function getOnBlur(){
		return $this->_onBlur;
	}

	/**
	 * Retrieve onFocus attribute
	 *
	 * @return string
	 */
	public function getOnFocus(){
		return $this->_onFocus;
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
	 * Retrieve size attribute
	 *
	 * @return string
	 */
	public function getSize(){
		return $this->_size;
	}

	/**
	 * Set maxlength attribute
	 *
	 * @param integer $maxlength
	 * @return void
	 */
	public function setMaxlength($maxlength){
		$this->_maxlength = $maxlength;
	}

	/**
	 * Set onBlur attribute
	 *
	 * @param string $onBlur
	 * @return void
	 */
	public function setOnBlur($onBlur){
		$this->_onBlur = $onBlur;
	}

	/**
	 * Set onFocus attribute
	 *
	 * @param string $onFocus
	 * @return void
	 */
	public function setOnFocus($onFocus){
		$this->_onFocus = $onFocus;
	}

	/**
	 * Set onChange attribute
	 *
	 * @param string $onChange
	 * @return void
	 */
	public function setOnChange($onChange){
		$this->_onChange = $onChange;
	}

	/**
	 * Set size attribute
	 *
	 * @param string $size
	 * @return void
	 */
	public function setSize($size){
		$this->_size = $size;
	}

}
