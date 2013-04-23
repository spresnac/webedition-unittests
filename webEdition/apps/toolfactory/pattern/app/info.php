

// include autoload function
include_once('../../../lib/we/core/autoload.php');

// include configuration
include_once('../conf/meta.conf.php');

$appName = Zend_Controller_Front::getInstance()->getParam('appName');

$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', 'toolfactory');
we_core_Local::addTranslation('default.xml', $metaInfo['classname']);

$htmlPage = we_ui_layout_Dialog::getInstance();
$htmlPage->addJSFile(JS_DIR . 'windows.js');
$htmlPage->addJSFile(JS_DIR . 'we_showMessage.js');
$htmlPage->addJSFile(JS_DIR . 'images.js');
$htmlPage->addJSFile(JS_DIR . 'libs/yui/yahoo-min.js');
$htmlPage->addJSFile(JS_DIR . 'libs/yui/event-min.js');
$htmlPage->addJSFile(JS_DIR . 'libs/yui/connection-min.js');
$htmlPage->addJSFile(JS_DIR . 'libs/yui/json-min.js');
$htmlPage->addJSFile(LIB_DIR . 'we/core/JsonRpc.js');

$appconfig = we_app_Common::getManifest($metaInfo['classname']);


include_once($GLOBALS['__WE_BASE_PATH__']. DIRECTORY_SEPARATOR .'we'. DIRECTORY_SEPARATOR .'include'. DIRECTORY_SEPARATOR.'we_version.php');
$html = '<h2 style="text-align:center">'.$translate->_($metaInfo['name']).'</h2>';
$htmlPage->addHTML($html);

if(!empty($appconfig->info->version) || !empty($appconfig->dependencies->version)){

		$rowVersion = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('Version')));
		$rowVersion->setLeftWidth(100);

		$html = '';

		if(!empty($appconfig->info->version)){
			if(!empty($appconfig->info->version)){
				$html .= '<strong>'.$translate->_('Version').': '.$appconfig->info->version.'</strong>';
				if(!empty($appconfig->info->copyright) || !empty($appconfig->info->copyrighturl)){
					$html .= ' &copy; ';
					if(!empty($appconfig->info->copyrighturl)){
						$html .= ' <a style="color:#000000" href="http://'.$appconfig->info->copyrighturl.'" target="_blank">';
					}
					$html .=  $appconfig->info->copyright;
					if(!empty($appconfig->info->copyrighturl)){
						$html .= '</a>';
					}
				}
				$html .= '<br/>';
			}

			$html .= '<br/>';

		}

		if(!empty($appconfig->dependencies->version)){
			$we_version = we_util_Strings::version2number(WE_VERSION,false);
			if ($we_version < $appconfig->dependencies->version){
				$html .= $translate->_('MinWeVersion').': <strong><span style="color:red">'.we_util_Strings::number2version($appconfig->dependencies->version,false).'</span></strong> '.$translate->_('AktWeVersion').' <strong>' .WE_VERSION.'</strong>';
			} else {
				$html .= $translate->_('MinWeVersion').': <strong>'.we_util_Strings::number2version($appconfig->dependencies->version,false).'</strong>';
			}
		}
		if(!empty($appconfig->dependencies->sdkversion)){
			$html .= '<br/>'.$translate->_('SdkVersion').': <strong>'.we_util_Strings::number2version($appconfig->dependencies->sdkversion,false).'</strong>';
		}

		if(isset($metaInfo['appdisabled'])){
			$html .= '<br/>'.$translate->_('AppStatus').': <strong>';
			if($metaInfo['appdisabled']){
				$html .= $translate->_('AppStatusDiabled');
			} else {
				$html .= $translate->_('AppStatusActive');
			}
			$html . ='</strong>';
		}

		$rowVersion->addHTML($html);
		$tableVersion = new we_ui_layout_HeadlineIconTable();
		$tableVersion->setId('tabVersion');
		$tableVersion->setMarginLeft(30);
		$tableVersion->setRows(array($rowVersion));
		$htmlPage->addElement($tableVersion);
	}


	if (!empty($appconfig->thirdparty)){
		$tableExTool = new we_ui_layout_HeadlineIconTable();
		$tableExTool->setId('tabExTool');
		$tableExTool->setMarginLeft(30);
		$rowsExTool=array();
		$html = '';
		$rowExTool = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('ExTool')));
		$rowExTool->setLeftWidth(100);
		if(!empty($appconfig->thirdparty->www)){
				$html .= ' <a style="color:#000000" href="'.$appconfig->thirdparty->www.'" target="_blank">';
				if(!empty($appconfig->thirdparty->name)){$html .= $appconfig->thirdparty->name;} else {$html .= $appconfig->thirdparty->www;}
				$html .= '</a>';
		}
		if(!empty($appconfig->thirdparty->version)){
			$html .= ', '.$translate->_('Version'). ' '.$appconfig->thirdparty->version;
		}
		if(!empty($appconfig->thirdparty->license)){
			$html .= '<br/> '.$translate->_('LicenseType').' ';
			if(!empty($appconfig->thirdparty->licenseurl)){
				$html .= ' <a style="color:#000000" href="'.$appconfig->thirdparty->licenseurl.'" target="_blank">';
			}
			if(!empty($appconfig->thirdparty->license)){$html .= $appconfig->thirdparty->license;} else {$html .= $appconfig->thirdparty->licenseurl;}
			if(!empty($appconfig->thirdparty->licenseurl)){
				$html .= '</a>';
			}
		}
		$rowExTool->addHTML($html);
		$tableExTool->setRows(array($rowExTool));
		$htmlPage->addElement($tableExTool);
		}
		if (!empty($appconfig->creator) || !empty($appconfig->maintainer)){
			$tableAuthor = new we_ui_layout_HeadlineIconTable();
			$tableAuthor->setId('tabAuthor');
			$tableAuthor->setMarginLeft(30);
			$rowsAuthor=array();
			if(!empty($appconfig->creator)){
				$cm = $appconfig->creator;
				$rowAuthor = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('Author')));
				$rowAuthor->setLine(0);
				$rowAuthor->setLeftWidth(100);
				$html = '';
				if(!empty($cm->company)){
					$html .= '<strong>'.$cm->company.'</strong><br/>';
				}
				if(!empty($cm->authors->author)){
					if(is_array($cm->authors->author) ){
						$authornames= $cm->authors->author->toArray();
					} else {$authornames = $cm->authors->author;}
					if(!empty($cm->authorlinks->www) && is_array($cm->authorlinks->www) ){
						$authorlinks= $cm->authorlinks->www->toArray();
					} else {$authorlinks= $cm->authorlinks->www;}
					if (is_array($authornames)){
						$authorentry = array();
						for ($i=0; $i < count($authornames);$i++){
							$htmla = '';
							if(isset($authorlinks[$i]) && !empty($authorlinks[$i])){
								$htmla .= '<a style="color:#000000" href="'.$authorlinks[$i].'" target="_blank" >';
							}
							$htmla .= $authornames[$i];
							if(isset($authorlinks[$i]) && !empty($authorlinks[$i])){
								$htmla .= '</a>';
							}
							$authorentry[]=$htmla;
						}
						$html .= implode(', ',$authorentry);
					} else {
						$html .= '';
						if(isset($authorlinks) && !empty($authorlinks)){
								$html .= '<a style="color:#000000" href="'.$authorlinks.'" target="_blank" >';
							}
							$html .= $authornames;
							if(isset($authorlinks) && !empty($authorlinks)){
								$html .= '</a>';
							}
					}
				}
				if(!empty($cm->address)){
					$html .= '<br/>'.$cm->address;
				}
				if(!empty($cm->email)){
					$html .= '<br/><a style="color:#000000" href="mailto'.$cm->email.'">'.$cm->email.'</a>';
				}
				$rowAuthor->addHTML($html);
				$rowsAuthor[] = $rowAuthor;
			}
			if(!empty($appconfig->maintainer)){
				$cm = $appconfig->maintainer;
				$html = '';
				$rowMaintainer = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('Maintainer')));
				$rowMaintainer->setLeftWidth(100);
				$rowMaintainer->setLine(0);
				if(!empty($cm->company)){
					$html .= '<strong>'.$cm->company.'</strong><br/>';
				}
				if(!empty($cm->authors->author)){
					if(is_array($cm->authors->author)){
						$authornames= $cm->authors->author->toArray();
					} else {$authornames= $cm->authors->author;}
					if(!empty($cm->authorlinks->www) && is_array($cm->authorlinks->www)){
						$authorlinks= $cm->authorlinks->www->toArray();
					} else {$authorlinks= $cm->authorlinks->www;}
					if (is_array($authornames)){
						$authorentry = array();
						for ($i=0; $i < count($authornames);$i++){
							$htmla = '';
							if(isset($authorlinks[$i]) && !empty($authorlinks[$i])){
								$htmla .= '<a style="color:#000000" href="'.$authorlinks[$i].'" target="_blank" >';
							}
							$htmla .= $authornames[$i];
							if(isset($authorlinks[$i]) && !empty($authorlinks[$i])){
								$htmla .= '</a>';
							}
							$authorentry[]=$htmla;
						}
						$html .= implode(', ',$authorentry);
					} else {
						$html .= '';
						if(isset($authorlinks) && !empty($authorlinks)){
							$html .= '<a style="color:#000000" href="'.$authorlinks.'" target="_blank" >';
						}
						$html .= $authornames;
						if(isset($authorlinks) && !empty($authorlinks)){
							$html .= '</a>';
						}
					}

				}
				if(!empty($cm->address)){
					$html .= '<br/>'.$cm->address;
				}
				if(!empty($cm->email)){
					$html .= '<br/><a style="color:#000000" href="mailto'.$cm->email.'">'.$cm->email.'</a>';
				}

				$rowMaintainer->addHTML($html);
				$rowsAuthor[] = $rowMaintainer;
			}
			$tableAuthor->setRows($rowsAuthor);
			$htmlPage->addElement($tableAuthor);
		}

$button = new we_ui_controls_Button();
$button->setText($translate->_('Ok'));
$button->setType('onClick');
$button->setOnClick('top.close()');
$button->setStyle('margin-left:auto;margin-right:auto;');
$htmlPage->addElement($button);
echo $htmlPage->getHTML();

