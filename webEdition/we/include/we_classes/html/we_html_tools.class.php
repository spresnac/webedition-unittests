<?php

/**
 * webEdition CMS
 *
 * $Rev: 5807 $
 * $Author: mokraemer $
 * $Date: 2013-02-13 19:33:33 +0100 (Wed, 13 Feb 2013) $
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
abstract class we_html_tools{

	const OPTGROUP = '<!--we_optgroup-->';

	###### protect #################################################################
### we_html_tools::protect()
### protects a page. Guests can not see this page

	static function protect(array $perms = null){
		$allow = false;
		if($perms && is_array($perms)){
			foreach($perms as $perm){
				$allow|=isset($_SESSION['perms'][$perm]) && $_SESSION['perms'][$perm];
			}
		} else{
			$allow = true;
		}
		if(!$allow || !isset($_SESSION["user"]) || !isset($_SESSION["user"]["Username"]) || $_SESSION["user"]["Username"] == ''){
			print self::htmlTop() .
				we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('alert', '[perms_no_permissions]'), we_message_reporting::WE_MESSAGE_ERROR) . 'top.close();') .
				'</body></html>';
			exit();
		}
	}

	/**
	 * This function creates a table.
	 *
	 * @param          string                                  $element
	 * @param          string                                  $text
	 * @param          string                                  $textalign          (optional)
	 * @param          string                                  $textclass          (optional)
	 * @param          string                                  $col2               (optional)
	 * @param          string                                  $col3               (optional)
	 * @param          string                                  $col4               (optional)
	 * @param          string                                  $col5               (optional)
	 * @param          string                                  $col6               (optional)
	 * @param          int                                     $abstand            (optional)
	 *
	 * @return         string
	 */
	static function htmlFormElementTable($col1, $text, $textalign = "left", $textclass = "defaultfont", $col2 = "", $col3 = "", $col4 = "", $col5 = "", $col6 = "", $abstand = 1){
		$colspan = 0;
		$elemOut = '';

		for($i = 1; $i < 7; ++$i){
			$var = ${'col' . $i};
			if($var){
				$tmp = '<td';
				if(is_array($var)){
					foreach($var as $key => $val){
						$key == 'text' ? $colText = $val : $tmp .= ' ' . $key . '=\'' . $val . '\'';
					}
				} else{
					$colText = $var;
				}
				$tmp .= '>' . $colText . '</td>';
				$elemOut.=$tmp;
				$colspan++;
			}
		}
		return '<table cellpadding="0" cellspacing="0" border="0">' . ($text ? '<tr><td class="' . trim($textclass) . '" align="' . trim(
					$textalign) . '" colspan="' . $colspan . '">' . $text . '</td></tr>' : '') . ($abstand ? ('<tr><td colspan="' . $colspan . '">' . we_html_tools::getPixel(
					2, $abstand) . '</td></tr>') : '') . '<tr>' . $elemOut . '</tr></table>';
	}

	static function targetBox($name, $size, $width = "", $id = "", $value = "", $onChange = "", $abstand = 8, $selectboxWidth = "", $disabled = false){
		$jsvarname = str_replace(array("[", "]"), "_", $name);
		$_inputs = array(
			"class" => "weSelect",
			"name" => "sel_" . $name,
			"onfocus" => "change$jsvarname=1;",
			"onchange" => "if(change$jsvarname) this.form.elements['$name'].value = this.options[this.selectedIndex].text; change$jsvarname=0; this.selectedIndex = 0;" . $onChange,
			"style" => (($selectboxWidth != "") ? ("width: " . $selectboxWidth . "px;") : "")
		);

		if($disabled){
			$_inputs["disabled"] = "true";
		}

		$_target_box = new we_html_select($_inputs, 0);
		$_target_box->addOptions(5, array(
			"", "_top", "_parent", "_self", "_blank"
			), array(
			"", "_top", "_parent", "_self", "_blank"
		));

		$_table = new we_html_table(array(
				"cellpadding" => 0, "cellspacing" => 0, "border" => 0
				), 1, 3);

		$_inputs = array(
			"name" => $name,
			"class" => "defaultfont"
		);

		if($width){
			$_inputs ["style"] = 'width: ' . $width . 'px;';
		}

		if($id){
			$_inputs["id"] = $id;
		}

		if($value){
			$_inputs["value"] = oldHtmlspecialchars($value);
		}

		if($onChange){
			$_inputs["onchange"] = $onChange;
		}

		$_table->setCol(0, 0, array(
			"class" => "defaultfont"
			), we_html_tools::htmlTextInput(
				$name, $size, $value, "", (!empty($onChange) ? 'onchange="' . $onChange . '"' : ''), "text", $width, 0, "", $disabled));

		$_table->setCol(0, 1, null, we_html_tools::getPixel($abstand, 1));

		$_table->setCol(0, 2, array(
			"class" => "defaultfont"
			), $_target_box->getHtml());

		return $_table->getHtml();
	}

	static function htmlTextInput($name, $size = 24, $value = "", $maxlength = "", $attribs = "", $type = "text", $width = "0", $height = "0", $markHot = "", $disabled = false){
		$style = ($width || $height) ? (' style="' . ($width ? ('width: ' . $width . (is_numeric($width) ? 'px' : '') . ';') : '') .
			($height ? ('height: ' . $height . (is_numeric($height) ? 'px' : '') . ';') : '') . '"') : '';
		return '<input' . ($markHot ? ' onchange="if(_EditorFrame){_EditorFrame.setEditorIsHot(true);}' . $markHot . '.hot=1;"' : '') .
			(strstr($attribs, "class=") ? "" : ' class="wetextinput"') . ' type="' . trim($type) . '" name="' . trim($name) .
			'" size="' . intval($size) . '" value="' . oldHtmlspecialchars($value) . '"' . ($maxlength ? (' maxlength="' . intval($maxlength) . '"') : '') . ($attribs ? " $attribs" : '') . $style . ' onblur="this.className=\'wetextinput\';" onfocus="this.className=\'wetextinputselected\'"' . ($disabled ? (' disabled="true"') : '') . ' />';
	}

	static function htmlMessageBox($w, $h, $content, $headline = "", $buttons = ""){
		return '<div style="width:' . $w . 'px;height:' . $h . 'px;background-color:#F7F5F5;border: 2px solid #D7D7D7;padding:20px;">' .
			($headline ? '<h1 class="header">' . $headline . '</h1>' : '') .
			'<div>' . $content . '</div><div style="margin-top:20px;">' . $buttons . '</div></div>';
	}

	static function htmlDialogLayout($content, $headline, $buttons = "", $width = "100%", $marginLeft = "30", $height = "", $overflow = "auto"){
		$parts = array(
			array(
				"html" => $content, "headline" => "", "space" => 0
			)
		);

		if($buttons){
			$buttons = '<div align="right" style="margin-left:10px;">' . $buttons . '</div>';
		}
		return we_multiIconBox::getHTML('', $width, $parts, $marginLeft, $buttons, -1, "", "", false, $headline, "", $height, $overflow);
	}

	static function htmlDialogBorder3($w, $h, $content, $headline, $class = "middlefont", $bgColor = "", $buttons = "", $id = "", $style = ""){ //content && headline are arrays
		$anz = count($headline);
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . ($style ? ' style="' . $style . '"' : '') . ' border="0" cellpadding="0" cellspacing="0" width="' . $w . '">
		<tr>
		<td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_ol2.gif);">' . we_html_tools::getPixel(8, 21) . '</td>';
		// HEADLINE
		for($f = 0; $f < $anz; $f++){
			$out .= '<td class="' . $class . '" style="padding:1px 5px 1px 5px;background-image:url(' . IMAGE_DIR . 'box/box_header_bg2.gif);">' . $headline[$f]["dat"] . '</td>';
		}
		$out .= '<td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_or2.gif);">' . we_html_tools::getPixel(8, 21) . '</td>
				</tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr>' . self::htmlDialogBorder4Row($c, $class, $bgColor) . '</tr>';
		}

		$out .= '</table>';

		if($buttons){
			$attribs = array(
				"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
			);
			$_table = new we_html_table($attribs, 3, 1);
			$_table->setCol(0, 0, array(
				"colspan" => "2"
				), $out);
			$_table->setCol(1, 0, null, we_html_tools::getPixel($w, 5)); // row for gap between buttons and dialogborder
			$_table->setCol(2, 0, array(
				"align" => "right"
				), $buttons);
			return $_table->getHtml();
		} else{
			return $out;
		}
	}

	static function htmlDialogBorder4Row($content, $class = "middlefont", $bgColor = ""){
		$anz = count($content);
		$out = '<td style="border-bottom: 1px solid silver;background-image:url(' . IMAGE_DIR . 'box/shaddowBox3_l.gif);">' .
			we_html_tools::getPixel(8, isset($content[0]["height"]) ? $content[0]["height"] : 1) . '</td>';

		for($f = 0; $f < $anz; $f++){
			$bgcol = $bgColor ? $bgColor : ((isset($content[$f]["bgcolor"]) && $content[$f]["bgcolor"]) ? $content[$f]["bgcolor"] : "white");
			$out .= '<td class="' . $class . '" style="padding:2px 5px 2px 5px;' . (($f != 0) ? "border-left:1px solid silver;" : "") . 'border-bottom: 1px solid silver;background-color:' . $bgcol . ';" ' .
				((isset($content[$f]["align"])) ? 'align="' . $content[$f]["align"] . '"' : "") . ' ' .
				((isset($content[$f]["height"])) ? 'height="' . $content[$f]["height"] . '"' : "") . '>' .
				((isset($content[$f]["dat"]) && $content[$f]["dat"]) ? $content[$f]["dat"] : "&nbsp;") .
				'</td>';
		}
		$out .= '<td style="border-bottom: 1px solid silver;background-image:url(' . IMAGE_DIR . 'box/shaddowBox3_r.gif);">' .
			we_html_tools::getPixel(8, isset($content[0]["height"]) ? $content[0]["height"] : 1) . '</td>';
		return $out;
	}

	static function htmlDialogBorder4($w, $h, $content, $headline, $class = "middlefont", $bgColor = "", $buttons = "", $id = "", $style = ""){ //content && headline are arrays
		$out = '<table' . ($id ? ' id="' . $id . '"' : '') . ($style ? ' style="' . $style . '"' : '') . ' border="0" cellpadding="0" cellspacing="0" width="' . $w . '">
		<tr><td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_ol2.gif);">' . we_html_tools::getPixel(8, 21) . '</td>';
		// HEADLINE
		foreach($headline as $h){
			$out .= '<td class="' . $class . '" style="padding:1px 5px 1px 5px;background-image:url(' . IMAGE_DIR . 'box/box_header_bg2.gif);">' . $h["dat"] . '</td>';
		}
		$out .= '<td width="8" style="background-image:url(' . IMAGE_DIR . 'box/box_header_or2.gif);">' . we_html_tools::getPixel(8, 21) . '</td></tr>';

		//CONTENT
		foreach($content as $c){
			$out .= '<tr>' . self::htmlDialogBorder4Row($c, $class, $bgColor) . '</tr>';
		}

		$out .= '</table>';

		if($buttons){
			$attribs = array(
				"border" => "0", "cellpadding" => "0", "cellspacing" => "0"
			);
			$_table = new we_html_table($attribs, 3, 1);
			$_table->setCol(0, 0, array("colspan" => "2"), $out);
			$_table->setCol(1, 0, null, we_html_tools::getPixel($w, 5)); // row for gap between buttons and dialogborder
			$_table->setCol(2, 0, array("align" => "right"), $buttons);
			return $_table->getHtml();
		} else{
			return $out;
		}
	}

	static function html_select($name, $size, $vals, $value = "", $onchange = ""){
		$out = '<select class="weSelect" name="' . $name . '" size="' . $size . '"' . ($onchange ? ' onchange="' . $onchange . '"' : '') . ">\n";
		reset($vals);
		foreach($vals as $v => $t){
			$out .= '<option value="' . oldHtmlspecialchars($v) . '"' . (($v == $value) ? ' selected' : '') . '>' . $t . '</option>';
		}
		return $out . '</select>';
	}

	static function htmlInputChoiceField($name, $value, $values, $atts, $mode, $valuesIsHash = false){
		//  This function replaced we_getChoiceField
		//  we need input="text" and select-box
		//  First input='text'
		$textField = getHtmlTag('input', array_merge($atts, array('type' => 'text', 'name' => $name, 'value' => oldHtmlspecialchars($value))));

		$opts = getHtmlTag('option', array('value' => ''), '', true) . "\n";
		$attsOpts = array();

		if($valuesIsHash){
			foreach($values as $_val => $_text){
				$attsOpts['value'] = oldHtmlspecialchars($_val);
				$opts .= getHtmlTag('option', $attsOpts, oldHtmlspecialchars($_text)) . "\n";
			}
		} else{
			// options of select Menu
			$options = makeArrayFromCSV($values);
			if(isset($atts['xml'])){
				$attsOpts['xml'] = $atts['xml'];
			}

			foreach($options as $option){
				$attsOpts['value'] = oldHtmlspecialchars($option);
				$opts .= getHtmlTag('option', $attsOpts, oldHtmlspecialchars($option)) . "\n";
			}
		}

		// select menu
		$onchange = ($mode == 'add' ? 'this.form.elements[\'' . $name . '\'].value += ((this.form.elements[\'' . $name . '\'].value ? \' \' : \'\') + this.options[this.selectedIndex].value);' : 'this.form.elements[\'' . $name . '\'].value=this.options[this.selectedIndex].value;');

		if(isset($atts['id'])){ //  use another ID!!!!
			$atts['id'] = 'tmp_' . $atts['id'];
		}
		$atts['onchange'] = $onchange . 'this.selectedIndex=0;';
		$atts['name'] = 'tmp_' . $name;
		$atts['size'] = isset($atts['size']) ? $atts['size'] : 1;
		$atts = removeAttribs($atts, array('size')); //  remove size for choice
		$selectMenue = getHtmlTag('select', $atts, $opts, true);
		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $textField . '</td><td>' . $selectMenue . '</td></tr></table>';
	}

	static function gifButton($name, $href, $language = "Deutsch", $alt = "", $width = "", $height = "", $onClick = "", $bname = "", $target = "", $disabled = false){
		$img = '<img src="' . IMAGE_DIR . 'buttons/' . $name . ($disabled ? "_d" : "") . ($language ? '_' : '') . $language . '.gif"' . ($width ? ' width="' . $width . '"' : '') . ($height ? ' height="' . $height . '"' : '') . ($bname ? ' name="' . $bname . '"' : '') . ' border="0" alt="' . $alt . '">';

		return ($disabled ?
				$img : ($href ?
					'<a href="' . $href . '" onMouseOver="window.status=\'' . $alt . '\';return true;" onMouseOut="window.status=\'\';return true;"' . ($onClick ? ' onClick="' . $onClick . '"' : '') . ($target ? (' target="' . $target . '"') : '') . '>' . $img . '</a>' :
					'<input type="image" src="' . IMAGE_DIR . 'buttons/' . $name . ($language ? '_' : '') . $language . '.gif"' . ($width ? ' width="' . $width . '"' : '') . ($height ? ' height="' . $height . '"' : '') . ' border="0" alt="' . $alt . '"' . ($onClick ? ' onClick="' . $onClick . '"' : '') . ($bname ? ' name="' . $bname . '"' : '') . ' />'
				));
	}

	static function getExtensionPopup($name, $selected, $extensions, $width = "", $attribs = "", $permission = true){
		if((isset($extensions)) && (count($extensions) > 1)){
			if(!$permission){
				$disabled = ' disabled="disabled "';
				$attribs .= $disabled;
			} else{
				$disabled = '';
			}
			$out = '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' .
				we_html_tools::htmlTextInput($name, 5, $selected, "", $attribs, "text", $width / 2, "0", "top") .
				'</td><td><select class="weSelect" name="wetmp_' . $name . '" size=1' . $disabled . ($width ? ' style="width: ' . ($width / 2) . 'px"' : '') . ' onchange="if(typeof(_EditorFrame) != \'undefined\'){_EditorFrame.setEditorIsHot(true);}if(this.options[this.selectedIndex].text){this.form.elements[\'' . $name . '\'].value=this.options[this.selectedIndex].text;};this.selectedIndex=0"><option>';
			foreach($extensions as $extension){
				$out .= '<option>' . $extension . '</option>';
			}
			$out .= '</select></td></tr></table>';
			return $out;
		} else{
			$_ext = $extensions[0];
			return we_html_tools::hidden($name, $_ext) . '<b class="defaultfont">' . $_ext . '</b>';
		}
	}

	static function pExtensionPopup($name, $selected, $extensions){
		print we_html_tools::getExtensionPopup($name, $selected, $extensions);
	}

	static function getPixel($w, $h, $border = 0){
		if($w == ''){
			$w = 0;
		}
		if($h == ''){
			$h = 0;
		}
		/* 		if(!is_numeric($w) && $h == 1){
		  t_e('x');
		  } */
		return '<span style="display:inline-block;width:' . $w . (is_numeric($w) ? 'px' : '') . ';height:' . $h . (is_numeric($h) ? 'px' : '') . ';' . ($border ? 'border:' . $border . 'px solid black;' : '') . '"></span>';
	}

	static function pPixel($w, $h){
		print self::getPixel($w, $h);
	}

	static function hidden($name, $value, $attribs = null){
		$attribute = "";
		if(isset($attribs) && is_array($attribs)){
			foreach($attribs as $key => $val){
				$attribute .= $key . '="' . $val . '" ';
			}
		} if(XHTML_DEFAULT){
			$tagende = '/>';
		} else{
			$tagende = '>';
		}
		return '<input type="hidden" value="' . $value . '" name="' . $name . '" ' . $attribute . $tagende;
	}

	static function we_getDayPos($format){
		return max(self::findChar($format, "d"), self::findChar($format, "D"), self::findChar($format, "j"));
	}

	static function we_getMonthPos($format){
		return max(self::findChar($format, "m"), self::findChar($format, "M"), self::findChar($format, "n"), self::findChar($format, "F"));
	}

	static function we_getYearPos($format){
		return max(self::findChar($format, "y"), self::findChar($format, "Y"));
	}

	static function we_getHourPos($format){
		return max(self::findChar($format, "g"), self::findChar($format, "G"), self::findChar($format, "h"), self::findChar($format, "H"));
	}

	static function we_getMinutePos($format){
		return self::findChar($format, "i");
	}

	static function findChar($in, $searchChar){
		$pos = 0;
		while(($pos = strpos($in, $searchChar, $pos)) !== FALSE) {
			if(substr($in, $pos - 1, 1) != '\\'){
				return $pos;
			}
			++$pos;
		}
		return -1;
	}

	static function getDateInput2($name, $time = "", $setHot = false, $format = "", $onchange = "", $class = "weSelect", $xml = "", $minyear = "", $maxyear = "", $style = ""){
		$_attsSelect = array();
		$_attsOption = array();
		$_attsHidden = array();

		if(!empty($xml)){
			$_attsSelect['xml'] = $xml;
			$_attsOption['xml'] = $xml;
			$_attsHidden['xml'] = $xml;
		}
		if(!empty($class)){
			$_attsSelect['class'] = $class;
		}
		if(!empty($style)){
			$_attsSelect['style'] = $style;
		}
		$_attsSelect['size'] = '1';

		if($onchange || $setHot){
			$_attsSelect['onchange'] = (($setHot ? '_EditorFrame.setEditorIsHot(true);' : '') . $onchange);
		}

		if(is_object($time)){
			$day = $time->format('j');
			$month = $time->format('n');
			$year = $time->format('Y');
			$hour = $time->format('G');
			$minute = $time->format('i');
		} else if($time){
			$time = intval($time);
			$day = intval(date("j", $time));
			$month = intval(date("n", $time));
			$year = intval(date("Y", $time));
			$hour = intval(date("G", $time));
			$minute = intval(date("i", $time));
		}

		$_dayPos = self::we_getDayPos($format);
		$_monthPos = self::we_getMonthPos($format);
		$_yearPos = self::we_getYearPos($format);
		$_hourPos = self::we_getHourPos($format);
		$_minutePos = self::we_getMinutePos($format);

		$_showDay = true;
		$_showMonth = true;
		$_showYear = true;
		$_showHour = true;
		$_showMinute = true;

		$name = preg_replace('/^(.+)]$/', '\1%s]', $name);
		if(($format == "") || $_dayPos > -1){
			$days = '';
			for($i = 1; $i <= 31; $i++){
				$_atts2 = ($time && $day == $i) ? array('selected' => 'selected') : array();
				$days .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf("%02d", $i));
			}
			$daySelect = getHtmlTag(
					'select', array_merge($_attsSelect, array(
						'name' => sprintf($name, "_day"), 'id' => sprintf($name, "_day")
					)), $days, true) . '&nbsp;';
		} else{
			$daySelect = getHtmlTag(
				'input', array_merge(
					$_attsHidden, array(
					'type' => 'hidden',
					'name' => sprintf($name, "_day"),
					'id' => sprintf($name, "_day"),
					'value' => $day
				)));
			$_showDay = false;
		}

		if(($format == "") || $_monthPos > -1){
			$months = '';
			for($i = 1; $i <= 12; $i++){
				$_atts2 = ($time && $month == $i) ? array('selected' => 'selected') : array();

				$months .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf("%02d", $i));
			}
			$monthSelect = getHtmlTag(
					'select', array_merge($_attsSelect, array(
						'name' => sprintf($name, "_month"), 'id' => sprintf($name, "_month")
					)), $months, true) . '&nbsp;';
		} else{
			$monthSelect = getHtmlTag(
				'input', array_merge(
					$_attsHidden, array(
					'type' => 'hidden',
					'name' => sprintf($name, "_month"),
					'id' => sprintf($name, "_month"),
					'value' => $month
				)));
			$_showMonth = false;
		}
		if(($format == "") || $_yearPos > -1){
			$years = '';
			if($minyear == ""){
				$minyear = 1970;
			}
			if($maxyear == ""){
				$maxyear = abs(date("Y") + 100);
			}
			for($i = $minyear; $i <= $maxyear; $i++){
				$_atts2 = ($time && $year == $i) ? array('selected' => 'selected') : array();
				$years .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf("%02d", $i));
			}
			$yearSelect = getHtmlTag(
					'select', array_merge($_attsSelect, array(
						'name' => sprintf($name, "_year"), 'id' => sprintf($name, "_year")
					)), $years, true) . '&nbsp;';
		} else{
			$yearSelect = getHtmlTag(
				'input', array_merge(
					$_attsHidden, array(
					'type' => 'hidden',
					'name' => sprintf($name, "_year"),
					'id' => sprintf($name, "_year"),
					'value' => $year
				)));
			$_showYear = false;
		}

		if(($format == "") || $_hourPos > -1){
			$hours = '';
			for($i = 0; $i <= 23; $i++){
				$_atts2 = ($time && $hour == $i) ? array('selected' => 'selected') : array();
				$hours .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf("%02d", $i));
			}
			$hourSelect = getHtmlTag(
					'select', array_merge($_attsSelect, array(
						'name' => sprintf($name, "_hour"), 'id' => sprintf($name, "_hour")
					)), $hours, true) . '&nbsp;';
		} else{
			$hourSelect = getHtmlTag(
				'input', array_merge(
					$_attsHidden, array(
					'type' => 'hidden',
					'name' => sprintf($name, "_hour"),
					'id' => sprintf($name, "_hour"),
					'value' => $hour
				)));
			$_showHour = false;
		}

		if(($format == "") || $_minutePos > -1){
			$minutes = '';
			for($i = 0; $i <= 59; $i++){
				$_atts2 = ($time && $minute == $i) ? array('selected' => 'selected') : array();
				$minutes .= getHtmlTag('option', array_merge($_attsOption, $_atts2), sprintf("%02d", $i));
			}
			$minSelect = getHtmlTag(
					'select', array_merge($_attsSelect, array(
						'name' => sprintf($name, "_minute"), 'id' => sprintf($name, "_minute")
					)), $minutes, true) . '&nbsp;';
		} else{
			$minSelect = getHtmlTag(
				'input', array_merge(
					$_attsHidden, array(
					'type' => 'hidden',
					'name' => sprintf($name, "_minute"),
					'id' => sprintf($name, "_minute"),
					'value' => $minute
				)));
			$_showMinute = false;
		}

		$_datePosArray = array(
			($_dayPos == -1) ? "d" : $_dayPos => $daySelect,
			($_monthPos == -1) ? "m" : $_monthPos => $monthSelect,
			($_yearPos == -1) ? "y" : $_yearPos => $yearSelect
		);

		$_timePosArray = array(
			($_hourPos == -1) ? "h" : $_hourPos => $hourSelect, ($_minutePos == -1) ? "i" : $_minutePos => $minSelect
		);

		ksort($_datePosArray);
		ksort($_timePosArray);

		$retVal = '<table cellpadding="0" cellspacing="0" border="0">';
		if($_showDay || $_showMonth || $_showYear){

			$retVal .= '<tr><td>';
			foreach($_datePosArray as $foo){
				$retVal .= $foo;
			}
			$retVal .= '</td></tr>';
		} else{
			foreach($_datePosArray as $foo){
				$retVal .= $foo;
			}
		}
		if($_showHour || $_showMinute){
			$retVal .= '<tr><td>';
			foreach($_timePosArray as $foo){
				$retVal .= $foo;
			}
			$retVal .= '</td></tr>';
		} else{
			foreach($_timePosArray as $foo){
				$retVal .= $foo;
			}
		}
		$retVal .= '</table>';
		return $retVal;
	}

	public static function htmlTop($title = 'webEdition', $charset = '', $doctype = ''){
		self::headerCtCharset('text/html', ($charset ? $charset : $GLOBALS['WE_BACKENDCHARSET']));
		print self::getHtmlTop($title, $charset, $doctype);
	}

	public static function getHtmlTop($title = 'webEdition', $charset = '', $doctype = '', $expand = false){
		return we_html_element::htmlDocType($doctype) .
			we_html_element::htmlhtml(we_html_element::htmlHead(
					self::getHtmlInnerHead($title, $charset, $expand), false)
				, false);
	}

	public static function getHtmlInnerHead($title = 'webEdition', $charset = '', $expand = false){
		return we_html_element::htmlTitle($_SERVER['SERVER_NAME'] . ' ' . $title) .
			we_html_element::htmlMeta(array(
				"http-equiv" => "Expires", "content" => gmdate("D, d M Y H:i:s") . " GMT"
			)) .
			we_html_element::htmlMeta(array(
				"http-equiv" => "Cache-Control", "content" => 'no-cache'
			)) .
			we_html_element::htmlMeta(array(
				"http-equiv" => "pragma", "content" => "no-cache"
			)) .
			self::htmlMetaCtCharset('text/html', ($charset ? $charset : $GLOBALS['WE_BACKENDCHARSET'])) .
			we_html_element::htmlMeta(array(
				"http-equiv" => "imagetoolbar", "content" => "no"
			)) .
			we_html_element::htmlMeta(array(
				"name" => "generator", "content" => 'webEdition'
			)) .
			we_html_element::linkElement(array('rel' => 'SHORTCUT ICON', 'href' => IMAGE_DIR . 'webedition.ico')) .
			($expand ?
				we_html_element::jsElement(weFile::load(JS_PATH . "we_showMessage.js")) .
				we_html_element::jsElement(weFile::load(JS_PATH . "attachKeyListener.js")) :
				we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
				we_html_element::jsScript(JS_DIR . "attachKeyListener.js")
			);
	}

	static function htmlMetaCtCharset($content, $charset){
		$GLOBALS['we']['PageCharset'] = $charset;
		return we_html_element::htmlMeta(array(
				"http-equiv" => "content-type",
				"content" => $content . '; charset=' . $charset
			));
	}

	static function headerCtCharset($content, $charset){
		$GLOBALS['we']['PageCharset'] = $charset;
		header('Content-Type: ' . $content . '; charset=' . $charset);
	}

	/**
	 *
	 * @param string $text
	 * @param string $img
	 * @param bool $yes
	 * @param bool $no
	 * @param bool $cancel
	 * @param string $yesHandler
	 * @param string $noHandler
	 * @param string $cancelHandler
	 * @param string $script
	 * @return string
	 */
	static function htmlYesNoCancelDialog($text = "", $img = "", $yes = "", $no = "", $cancel = "", $yesHandler = "", $noHandler = "", $cancelHandler = "", $script = ""){
		$cancelButton = ($cancel != "" ? we_button::create_button("cancel", "javascript:$cancelHandler") : "");
		$noButton = ($no != "" ? we_button::create_button("no", "javascript:$noHandler") : "");
		$yesButton = ($yes != "" ? we_button::create_button("yes", "javascript:$yesHandler") : "");


		$content = new we_html_table(array(
				"cellpadding" => 10, "cellspacing" => 0, "border" => 0
				), 1, ($img != "" ? 2 : 1));

		if($img != "" && file_exists($_SERVER['DOCUMENT_ROOT'] . $img)){
			$size = getimagesize($_SERVER['DOCUMENT_ROOT'] . $img);
			$content->setCol(
				0, 0, array(
				"valign" => "top"
				), we_html_element::htmlImg(
					array(
						"src" => $img, "border" => 0, "width" => $size[0], "height" => $size[1]
				)));
		}

		$content->setCol(0, ($img != "" ? 1 : 0), array(
			"class" => "defaultfont"
			), $text);

		return we_html_tools::htmlDialogLayout(
				(empty($script) ? '' : we_html_element::jsElement($script)) . $content->getHtml()
				, "", we_button::position_yes_no_cancel($yesButton, $noButton, $cancelButton), "99%", "0");
	}

	static function groupArray(array $arr, $sort = true, $len = 1){
		$tmp = array();
		if($sort){
			asort($arr, SORT_STRING);
		}
		$pre = '';
		foreach($arr as $key => $value){
			$newPre = strtoupper(substr($value, 0, $len));
			if($pre != $newPre){
				$tmp[$newPre] = self::OPTGROUP;
				$pre = $newPre;
			}
			$tmp[$key] = $value;
		}
		return $tmp;
	}

	static function htmlSelect($name, $values, $size = 1, $selectedIndex = "", $multiple = false, $attribs = "", $compare = "value", $width = "", $cls = "defaultfont", $oldHtmlspecialchars = true){
		$ret = '<select class="weSelect ' . $cls . '" name="' . trim($name) . '" size="' . abs($size) . '"' . ($multiple ? ' multiple="multiple"' : '') . ($attribs ? " $attribs" : "") . ($width ? ' style="width: ' . $width . 'px"' : '') . '>' . "\n";
		$selIndex = makeArrayFromCSV($selectedIndex);
		$optgroup = false;
		foreach($values as $value => $text){
			if($text == self::OPTGROUP){
				if($optgroup){
					$ret .= '</optgroup>';
				}
				$optgroup = true;
				$ret .= '<optgroup label="' . ($oldHtmlspecialchars ? oldHtmlspecialchars($value) : $value) . '">';
				continue;
			}
			$ret .= '<option value="' . ($oldHtmlspecialchars ? oldHtmlspecialchars($value) : $value) . '"' . (in_array(
					(($compare == "value") ? $value : $text), $selIndex) ? ' selected="selected"' : '') . '>' . ($oldHtmlspecialchars ? oldHtmlspecialchars($text) : $text) . '</option>';
		}
		$ret .= ($optgroup ? '</optgroup>' : '') . '</select>';
		return $ret;
	}

	/* displays a grey box with text and an icon

	  $text: Text to display
	  $type: 0=no icon
	  1=Alert icon
	  2=Info icon
	  3=Question icon
	  $width: width of box
	  $useHtmlSpecialChars: true or false
	 */

	static function htmlAlertAttentionBox($text, $type = 0, $width = 0, $useHtmlSpecialChars = true, $clip = 0){
		switch($type){
			case 1 :
				$icon = "alert";
				break;
			case 2 :
				$icon = "info";
				break;
			case 3 :
				$icon = "question";
				break;
			default :
				$icon = "";
		}

		$text = ($useHtmlSpecialChars) ? oldHtmlspecialchars($text, ENT_COMPAT, 'ISO-8859-1', false) : $text;
		$js = '';

		if($clip > 0){
			$unique = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(microtime())
			$smalltext = substr($text, 0, $clip) . ' ... ';
			$js = we_html_element::jsElement('
		var state_' . $unique . '=0;
			function clip_' . $unique . '(){
					var text = document.getElementById("td_' . $unique . '");
					var btn = document.getElementById("btn_' . $unique . '");

					if(state_' . $unique . '==0){
						text.innerHTML = "' . addslashes($text) . '";
						btn.innerHTML = "<a href=\'javascript:clip_' . $unique . '();\'><img src=\'' . BUTTONS_DIR . 'btn_direction_down.gif\' alt=\'down\' border=\'0\'></a>";
						state_' . $unique . '=1;
					}else {
						text.innerHTML = "' . addslashes($smalltext) . '";
						btn.innerHTML = "<a href=\'javascript:clip_' . $unique . '();\'><img src=\'' . BUTTONS_DIR . 'btn_direction_right.gif\' alt=\'right\' border=\'0\'></a>";
						state_' . $unique . '=0;
					}
			}');
			$text = $smalltext;
		}

		if(strpos($width, "%") === false){
			$width = intval($width);
			if(!we_base_browserDetect::isIE() && $width > 10){
				$width -= 10;
			}
		}

		return $js . '<div style="background-color:#dddddd;padding:5px;white-space:normal;' . ($width ? ' width:' . $width . (is_numeric($width) ? 'px' : '') . ';' : '') . '"><table border="0" cellpadding="2" width="100%"><tr>' . ($icon ? '<td width="30" style="padding-right:10px;" valign="top"><img src="' . IMAGE_DIR . $icon . '_small.gif" width="20" height="22" /></td>' : '') . '<td class="middlefont" ' . ($clip > 0 ? 'id="td_' . $unique . '"' : '') . '>' . $text . '</td>' . ($clip > 0 ? '<td valign="top" align="right" id="btn_' . $unique . '"><a href="javascript:clip_' . $unique . '();"><img src="' . BUTTONS_DIR . 'btn_direction_right.gif" alt="right" border="0" /></a><td>' : '') . '</tr></table></div>';
	}

	public static function setHttpCode($status){
		switch($status){
			case 200:
				header('HTTP/1.0 200 OK', false, 200);
				header('Status: 200 OK', false, 200);
				break;
			case 303:
				header('HTTP/1.1 ' . $status . ' See Other', true, $status);
				header('Status: ' . $status . ' See Other', true, $status);
				break;
			case 400:
				header('HTTP/1.1 ' . $status . ' Bad Request', true, $status);
				header('Status: ' . $status . ' Bad Request', true, $status);
				break;
			case 401:
				header('HTTP/1.1 ' . $status . ' Unauthorized', true, $status);
				header('Status: ' . $status . ' Unauthorized', true, $status);
				break;
			case 403:
				header('HTTP/1.1 ' . $status . ' Forbidden', true, $status);
				header('Status: ' . $status . ' Forbidden', true, $status);
				break;
			case 408:
				header('HTTP/1.1 ' . $status . ' Request Time-out', true, $status);
				header('Status: ' . $status . ' Request Time-out', true, $status);
				break;
			case 503:
				header('HTTP/1.1 ' . $status . ' Service Unavailable', true, $status);
				header('Status: ' . $status . ' Service Unavailable', true, $status);
				break;
		}
	}

}
