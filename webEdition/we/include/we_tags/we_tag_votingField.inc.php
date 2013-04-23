<?php

/**
 * webEdition CMS
 *
 * $Rev: 5699 $
 * $Author: mokraemer $
 * $Date: 2013-02-01 15:09:37 +0100 (Fri, 01 Feb 2013) $
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
function we_tag_votingField($attribs){

	if(isset($GLOBALS['_we_voting'])){
		$name = weTag_getAttribute('_name_orig', $attribs);
		$type = weTag_getAttribute('type', $attribs);
		$precision = weTag_getAttribute('precision', $attribs, 0);
		$num_format = weTag_getAttribute('num_format', $attribs);

		switch($name){
			case 'id':
				switch($type){
					case 'answer':
						return $GLOBALS['_we_voting']->answerCount;
					case 'select':
						return '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
					case 'radio':
					case 'checkbox':
					case 'chekbox':
						return '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
					case 'textarea':
					case 'textinput':
						return '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
					case 'voting':
					default:
						return $GLOBALS['_we_voting']->ID;
				}
				break;
			case 'question':
				return stripslashes($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['question']);
			case 'answer':
				switch($type){
					case 'radio':
						$code = '';
						$GLOBALS['_we_voting']->IsRadio = true;
						$countanswers = count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						$subb = ($GLOBALS['_we_voting']->AllowFreeText && $GLOBALS['_we_voting']->answerCount == $countanswers - 1 ? 1 : 0);
						if($GLOBALS['_we_voting']->answerCount < $countanswers - $subb){
							$atts = removeAttribs($attribs, array('name', 'type'));
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
							if($GLOBALS['_we_voting']->IsRequired && $GLOBALS['_we_voting']->answerCount == 0){
								$atts['type'] = 'hidden';
								$code .= getHtmlTag('input', $atts, '');
							}
							$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
							$atts['value'] = $GLOBALS['_we_voting']->answerCount;
							$atts['type'] = 'radio';
							if(isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
								$selItem = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][0];
								if(is_numeric($selItem) && $selItem == $GLOBALS['_we_voting']->answerCount){
									$atts['checked'] = 'checked';
								}
							}
							if($GLOBALS['_we_voting']->AllowFreeText){
								$countanswers--;
								$atts['onclick'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $countanswers . '.value="";';
							}

							$code .= getHtmlTag('input', $atts, '');
						}
						return $code;
						break;
					case 'checkbox':
						$code = '';
						$GLOBALS['_we_voting']->IsCheckbox = true;
						$countanswers = count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						$subb = ($GLOBALS['_we_voting']->AllowFreeText && $GLOBALS['_we_voting']->answerCount == $countanswers - 1 ? 1 : 0);
						if($GLOBALS['_we_voting']->answerCount < $countanswers - $subb){
							$atts = removeAttribs($attribs, array('name', 'type'));
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
							if($GLOBALS['_we_voting']->IsRequired && $GLOBALS['_we_voting']->answerCount == 0){
								$atts['type'] = 'hidden';
								$code .= getHtmlTag('input', $atts, '');
							}
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
							$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
							$atts['value'] = $GLOBALS['_we_voting']->answerCount;
							$atts['type'] = 'checkbox';
							if(isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
								foreach($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'] as $kk => $wert){
									//$selItem = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$kk];
									$selItem = $wert;

									if(is_numeric($selItem) && $selItem == $GLOBALS['_we_voting']->answerCount){
										$atts['checked'] = 'checked';
									}
								}
							}

							$code .= getHtmlTag('input', $atts, '');
						}
						return $code;
					case 'select':
						$code = '';
						if($GLOBALS['_we_voting']->answerCount == 0){

							$atts = removeAttribs($attribs, array('name', 'type'));
							$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;
							$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID;

							$code .= getHtmlTag('select', $atts, '');
						}

						$atts = removeAttribs($attribs, array('name', 'type'));
						$atts['value'] = $GLOBALS['_we_voting']->answerCount;

						$code .= getHtmlTag('option', $atts, addslashes($GLOBALS['_we_voting']->getAnswer()));
						if($GLOBALS['_we_voting']->isLastSet()){
							$code .= '</select>';
						}
						return $code;
					case 'image':
						$countanswers = count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if($GLOBALS['_we_voting']->answerCount < $countanswers){
							$myImageID = stripslashes($GLOBALS['_we_voting']->QASetAdditions[$GLOBALS['_we_voting']->defVersion]['imageID'][$GLOBALS['_we_voting']->answerCount]);
							if(is_numeric($myImageID)){
								$myImage = new we_imageDocument();
								$myImage->initByID($myImageID);

								$atts = removeAttribs($attribs, array('name', 'type', 'precision', 'num_format', 'nameto', 'to'));
								$myImage->initByAttribs($atts);
								return $myImage->getHtml();
							}
						}
						return '';
					case 'media':
						$countanswers = count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if($GLOBALS['_we_voting']->answerCount < $countanswers){
							$myMediaID = stripslashes($GLOBALS['_we_voting']->QASetAdditions[$GLOBALS['_we_voting']->defVersion]['mediaID'][$GLOBALS['_we_voting']->answerCount]);
						}
						return id_to_path($myMediaID);
					case 'textinput':
						$code = '';
						if($GLOBALS['_we_voting']->AllowFreeText){

							$atts = removeAttribs($attribs, array('name', 'type'));
							$countanswers = count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
							if($GLOBALS['_we_voting']->answerCount == $countanswers - 1){
								$atts['type'] = 'text';
								$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$atts['id'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$value = '';
								if(isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
									if($GLOBALS['_we_voting']->IsRadio){
										$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][0];
									} else{
										if($GLOBALS['_we_voting']->IsCheckbox){
											$mycount = count($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value']);
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$mycount - 1];
										} else{
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$GLOBALS['_we_voting']->answerCount];
										}
									}
								}
								if(isset($GLOBALS['_we_voting']->IsRadio) && $GLOBALS['_we_voting']->IsRadio){
									$atts['onkeydown'] = '';
									for($i = 0; $i < $countanswers - 1; $i++){
										$atts['onkeydown'] .= '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $i . '.checked=0;';
									}
								}
								$code .= getHtmlTag('input', $atts, $value);
							}
						}
						return $code;
					case 'textarea':
						if($GLOBALS['_we_voting']->AllowFreeText){
							$atts = removeAttribs($attribs, array('name', 'type'));
							$countanswers = count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
							if($GLOBALS['_we_voting']->answerCount == $countanswers - 1){
								$atts['name'] = '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$atts['id'] = '_we_voting_answerfree_' . $GLOBALS['_we_voting']->ID . '_' . $GLOBALS['_we_voting']->answerCount;
								$value = '';
								if(isset($_SESSION['_we_voting_sessionData']) && isset($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID])){
									if($GLOBALS['_we_voting']->IsRadio){
										$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][0];
									} else{
										if($GLOBALS['_we_voting']->IsCheckbox){
											$mycount = count($_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value']);
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$mycount - 1];
										} else{
											$value = $_SESSION['_we_voting_sessionData'][$GLOBALS['_we_voting']->ID]['value'][$GLOBALS['_we_voting']->answerCount];
										}
									}
								}
								if(isset($GLOBALS['_we_voting']->IsRadio) && $GLOBALS['_we_voting']->IsRadio){
									$atts['onkeydown'] = '';
									for($i = 0; $i < $countanswers - 1; $i++){
										$atts['onkeydown'] .= '_we_voting_answer_' . $GLOBALS['_we_voting']->ID . '_' . $i . '.checked=0;';
									}
								}
								return getHtmlTag('textarea', $atts, $value, true);
							}
						}
						return '';
					case 'text':
					default:
						$countanswers = count($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers']);
						if($GLOBALS['_we_voting']->answerCount < $countanswers){
							return stripslashes($GLOBALS['_we_voting']->QASet[$GLOBALS['_we_voting']->defVersion]['answers'][$GLOBALS['_we_voting']->answerCount]);
						}
						return '';
				}
				break;
			case 'result':
				return $GLOBALS['_we_voting']->getResult($type, $num_format, $precision);
			case 'date':
				$format = weTag_getAttribute('format', $attribs);
				return date(($format != '' ? $format : g_l('weEditorInfo', '[date_format]')), $GLOBALS['_we_voting']->PublishDate);
		}
	}
	return '';
}
