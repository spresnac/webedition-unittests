<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once(WE_SPELLCHECKER_MODULE_PATH . '/spellchecker.conf.inc.php');

if(empty($_SESSION["user"]["Username"])){
	if(isset($_REQUEST['scid'])){
		if(!file_exists(WE_SPELLCHECKER_MODULE_PATH . '/tmp/' . md5($_REQUEST['scid']))){
			we_html_tools::protect();
		}
	} else{
		we_html_tools::protect();
	}
} else{
	we_html_tools::protect();
}

we_html_tools::htmlTop();

function saveSettings($default, $active, $langs=array()){
	$_lang = '';

	foreach($langs as $k => $v){
		$_lang .= '\'' . $k . '\'=>\'' . addslashes($v) . '\',';
	}

	if(!empty($_lang)){
		$_lang = '$spellcheckerConf[\'lang\']=array(' . $_lang . ');';
	}

	$_construct = '<?php
				$spellcheckerConf = array(
				\'default\' => \'' . $default . '\',
				\'active\' => array(\'' . implode('\',\'', $active) . '\'),
				);
				' . $_lang ;

	weFile::save(WE_SPELLCHECKER_MODULE_PATH . 'spellchecker.conf.inc.php', $_construct);

	$_SESSION['weS']['dictLang'] = $default;
}

if(isset($_REQUEST['cmd'][0])){

	switch($_REQUEST['cmd'][0]){
		case 'addWord' :
			if(isset($_REQUEST['cmd'][1])){

				$_username = $_SESSION['user']['Username'];
				$_replacement = array('\\', '/', ':', '*', '?', '<', '>', '|', '"');
				for($_i = 0; $_i < count($_replacement); $_i++){
					$_username = str_replace($_replacement[$_i], 'MASK' . $_i, $_username);
				}

				$_userDict = WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict';
				weFile::save($_userDict, $_REQUEST['cmd'][1] . "\n", 'ab');
			}
			break;
		case 'addWords' :
			if(isset($_REQUEST['cmd'][1])){

				$_words = array();
				$_str = explode(',', $_REQUEST['cmd'][1]);
				foreach($_str as $_s){
					if(!empty($_s)){
						$_words[] = $_s;
					}
				}

				$_username = $_SESSION['user']['Username'];
				$_replacement = array('\\', '/', ':', '*', '?', '<', '>', '|', '"');
				for($_i = 0; $_i < count($_replacement); $_i++){
					$_username = str_replace($_replacement[$_i], 'MASK' . $_i, $_username);
				}

				$_userDict = WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict';
				weFile::save($_userDict, implode("\n", $_words) . "\n", 'ab');
			}
			break;
		case 'setLangDict' :
			if(isset($_REQUEST['cmd'][1])){
				$_SESSION['weS']['dictLang'] = $_REQUEST['cmd'][1];
			}
			print we_html_element::jsElement('
				top.document.we_form.submit();
			');
			break;

		case 'removeDictFile':
			if(strpos($_REQUEST['cmd'][1], '..') === false){

				@unlink(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $_REQUEST['cmd'][1]);
			}
			break;
		case 'uploadPart':
			$_content = '';
			if(isset($_FILES['chunk'])){

				move_uploaded_file($_FILES['chunk']['tmp_name'], WE_SPELLCHECKER_MODULE_PATH . 'chunk');

				$_content = weFile::load(WE_SPELLCHECKER_MODULE_PATH . 'chunk');
				$_checksum = crc32($_content);

				if(sprintf("%u", $_checksum) != $_REQUEST['cmd'][2])
					t_e('Corrupt!!!');

				weFile::save(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $_REQUEST['cmd'][1], $_content, 'ab');

				unlink(WE_SPELLCHECKER_MODULE_PATH . 'chunk');
			} else{

			}

			exit();
			break;

		case 'saveSettings':

			$_default = $_REQUEST['default'];
			$_active = array();
			foreach($_REQUEST as $_key => $_value){
				if(strpos($_key, 'enable_') === 0 && $_value == 1){
					$_active[] = str_replace('enable_', '', $_key);
				}
			}

			if(!in_array($_default, $_active)){
				$_active[] = $_default;
			}

			$_active = array_unique($_active);

			$_langs = (isset($_REQUEST['lang']) && is_array($_REQUEST['lang'])) ? $_REQUEST['lang'] : array();

			saveSettings($_default, $_active, $_langs);

			$_content = $_REQUEST['defaultDict'];
			weFile::save(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php', $_content);

			print we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(g_l('modules_spellchecker', '[save_settings]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);

			break;

		case 'deleteDict':
			if(strpos($_REQUEST['cmd'][1], "..") === false){
				unlink(WE_SPELLCHECKER_MODULE_PATH . 'dict/' . $_REQUEST['cmd'][1] . '.zip');
				$_mess = g_l('modules_spellchecker', '[dict_removed]');
				$_messType = we_message_reporting::WE_MESSAGE_NOTICE;

				if($GLOBALS['spellcheckerConf']['default'] == $_REQUEST['cmd'][1]){ // if the default dict has been deleted
					if(count($GLOBALS['spellcheckerConf']['active']) && isset($GLOBALS['spellcheckerConf']['active'][0])){
						// take the firts active dictionary
						$_new_ac = array();
						foreach($GLOBALS['spellcheckerConf']['active'] as $ac){
							if($ac != $_REQUEST['cmd'][1]){
								$_new_ac[] = $ac;
							}
						}
						saveSettings(isset($_new_ac[0]) ? $_new_ac[0] : '', $_new_ac);
					}
				}
			} else{
				$_mess = g_l('modules_spellchecker', '[name_invalid]');
				$_messType = we_message_reporting::WE_MESSAGE_ERROR;
			}

			print we_html_element::jsElement(
					we_message_reporting::getShowMessageCall($_mess, $_messType) .
					'parent.loadTable();
				');
			break;

		case 'refresh':
			we_loadLanguageConfig();

			$table = new we_html_table(array('width' => '380', 'cellpadding' => '2', 'cellspacing' => '2', 'border' => '0', 'style' => 'margin: 5px;'), 1, 6);

			$table->setRow(0, array('style' => 'background-color: silver;font-weight: bold;'), 6);
			$table->setCol(0, 0, array('valign' => 'top', 'class' => 'small', 'style' => 'color: white;'), g_l('modules_spellchecker', '[default]'));
			$table->setCol(0, 1, array('valign' => 'top', 'class' => 'small', 'style' => 'color: white;'), g_l('modules_spellchecker', '[dictionary]'));
			$table->setCol(0, 2, array('valign' => 'top', 'class' => 'small', 'style' => 'color: white;'), g_l('modules_spellchecker', '[language]'));
			$table->setCol(0, 3, array('valign' => 'top', 'class' => 'small', 'style' => 'color: white;'), g_l('modules_spellchecker', '[active]'));
			$table->setCol(0, 4, array('valign' => 'top', 'class' => 'small', 'style' => 'color: white;'), g_l('modules_spellchecker', '[refresh]'));
			$table->setCol(0, 5, array('valign' => 'top', 'class' => 'small', 'style' => 'color: white;'), g_l('modules_spellchecker', '[delete]'));

			$_lanSelect = new we_html_select(array('size' => 1, 'style' => 'width: 100px;', 'class' => 'weSelect'));
			foreach(getWeFrontendLanguagesForBackend() as $klan => $vlan){
				$_lanSelect->addOption($klan, $vlan);
			}

			$_langs = (isset($spellcheckerConf['lang']) && is_array($spellcheckerConf['lang'])) ? $spellcheckerConf['lang'] : array();

			$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');

			$_i = 0;
			while(false !== ($entry = $_dir->read())) {
				if($entry != '.' && $entry != '..' && strpos($entry, '.zip') !== false){
					$_i++;
					$table->addRow();

					$_name = str_replace('.zip', '', $entry);
					$_display = (strlen($_name) > 10) ? (substr($_name, 0, 10) . '...') : $_name;

					$table->setCol($_i, 0, array('valign' => 'top'), we_forms::radiobutton($_name, (($spellcheckerConf['default'] == $_name) ? true : false), 'default', '', true, 'defaultfont', 'document.we_form.enable_' . $_name . '.value=1;document.we_form._enable_' . $_name . '.checked=true;'));
					$table->setCol($_i, 1, array('valign' => 'top', 'class' => 'defaultfont'), $_display);

					$_lanSelect->setAttribute('name', 'lang[' . $_name . ']');

					if(isset($_langs[$_name])){
						$_lanSelect->selectOption($_langs[$_name]);
					} else{
						$_lanSelect->selectOption($GLOBALS['weDefaultFrontendLanguage']);
					}

					$table->setCol($_i, 2, array('valign' => 'top', 'class' => 'defaultfont'), $_lanSelect->getHtml());

					$table->setCol($_i, 3, array('valign' => 'top', 'align' => 'center'), we_forms::checkboxWithHidden(in_array($_name, $spellcheckerConf['active']), 'enable_' . $_name, '', false, 'defaultfont', ''));
					$table->setCol($_i, 4, array('valign' => 'top', 'align' => 'center'), '<div style="display: none;" id="updateIcon_' . $_name . '"><img src="' . IMAGE_DIR . 'spinner.gif"/></div><div style="display: block;" id="updateBut_' . $_name . '">' . we_button::create_button('image:btn_function_reload', 'javascript: updateDict("' . $_name . '");') . '</div>');
					$table->setCol($_i, 5, array('valign' => 'top', 'align' => 'center'), we_button::create_button('image:btn_function_trash', 'javascript: deleteDict("' . $_name . '");'));
				}
			}
			$_dir->close();

			print we_html_element::jsElement('

				parent.document.getElementById("selector").innerHTML = "' . addslashes(preg_replace("|\r?\n|", '', $table->getHtml())) . '";

			');
			break;

		default:
	}
}
?>
<script type="text/javascript" >

	function dispatch(cmd) {
		document.dispatcherForm.elements["cmd[0]"].value = cmd;
		for(var i = 1; i < arguments.length; i++) {
			document.dispatcherForm.elements["cmd["+i+"]"].value = arguments[i];
		}
		document.dispatcherForm.submit();
	}

</script>
</head>

<body>
	<form name="dispatcherForm" method="post" target="_self" action="<?php print WE_SPELLCHECKER_MODULE_DIR ?>weSpellcheckerCmd.php">
		<input type="hidden" name="cmd[0]" value="" />
		<input type="hidden" name="cmd[1]" value="" />
	</form>
</body>
</html>
