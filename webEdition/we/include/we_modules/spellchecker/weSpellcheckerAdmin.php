<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

include_once(WE_SPELLCHECKER_MODULE_PATH . '/spellchecker.conf.inc.php');

we_html_tools::protect();

if(!we_hasPerm('SPELLCHECKER_ADMIN')){
	print we_html_element::jsElement(
			we_message_reporting::getShowMessageCall(g_l('alert', '[access_denied]'), we_message_reporting::WE_MESSAGE_ERROR) .
			'self.close();
		');
	exit();
}

we_html_tools::htmlTop();

print STYLESHEET;

$_width = 600;
$space = 5;

$l_param['l_dictAdmin'] = g_l('modules_spellchecker', '[dictAdmin]');
$l_param['l_userDictAdmin'] = g_l('modules_spellchecker', '[userDictAdmin]');
$l_param['l_select'] = g_l('modules_spellchecker', '[select]');
$l_param['l_select_words'] = g_l('modules_spellchecker', '[select_words]');
$l_param['l_select_phonetic'] = g_l('modules_spellchecker', '[select_phonetic]');
$l_param['l_build'] = g_l('modules_spellchecker', '[build]');
$l_param['l_close'] = g_l('modules_spellchecker', '[close]');
$l_param['l_encoding'] = g_l('modules_spellchecker', '[encoding]');
$l_param['l_dictname'] = g_l('modules_spellchecker', '[dictname]');
$l_param['l_enc_warning'] = g_l('modules_spellchecker', '[enc_warning]');
$l_param['l_filename_nok'] = g_l('modules_spellchecker', '[filename_nok]');
$l_param['l_filename_warning'] = g_l('modules_spellchecker', '[filename_warning]');
$l_param['l_phonetic_nok'] = g_l('modules_spellchecker', '[phonetic_nok]');
$l_param['l_phonetic_warning'] = g_l('modules_spellchecker', '[phonetic_warning]');
$l_param['l_enc_warning'] = g_l('modules_spellchecker', '[enc_warning]');
$l_param['l_name_warning'] = g_l('modules_spellchecker', '[name_warning]');
$l_param['l_building'] = g_l('modules_spellchecker', '[building]');
$l_param['l_packing'] = g_l('modules_spellchecker', '[packing]');
$l_param['l_uploading'] = g_l('modules_spellchecker', '[uploading]');
$l_param['l_finished'] = g_l('modules_spellchecker', '[end]');

$l_param['upload_size'] = getUploadMaxFilesize();
$l_param['upload_url'] = getServerUrl(true) . WE_SPELLCHECKER_MODULE_DIR . 'weSpellcheckerCmd.php';


// ----------------------
if(we_base_browserDetect::isMAC() && we_base_browserDetect::isGecko()){
	$l_param['scid'] = session_id();
	$_tmp_dir = WE_SPELLCHECKER_MODULE_PATH . '/tmp';
	if(!is_dir($_tmp_dir)){
		we_util_File::createLocalFolder($_tmp_dir);
	}
	$_scid_file = $_tmp_dir . '/' . md5($l_param['scid']);
	if(!file_exists($_scid_file)){
		weFile::save($_scid_file, '');
		we_util_File::insertIntoCleanUp($_scid_file, time() + (24 * 3600));
	}
} else{
	$l_param['scid'] = '';
}
// -------------------------------

$l_params = '';

foreach($l_param as $key => $value){
	$l_params .= '<param name="' . $key . '" value="' . addslashes($value) . '">';
}



$we_tabs = new we_tabs();

$we_tabs->addTab(new we_tab("#", g_l('modules_spellchecker', '[dictAdmin]'), '((activ_tab==1) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('1');", array("id" => "tab_1")));
$we_tabs->addTab(new we_tab("#", g_l('modules_spellchecker', '[userDictAdmin]'), '((activ_tab==2) ? TAB_ACTIVE : TAB_NORMAL)', "setTab('2');", array("id" => "tab_2")));


$js = $we_tabs->getHeader() . we_html_element::jsElement('
			function setTab(tab) {
				toggle("tab"+activ_tab);
				toggle("tab"+tab);
				activ_tab=tab;
			}

	');

$table = new we_html_table(array('width' => '380', 'cellpadding' => '2', 'cellspacing' => '2', 'border' => '0', 'style' => 'margin: 5px;'), 3, 5);

$table->setRow(0, array('style' => 'background-color: silver;font-weight: bold;'), 5);
$table->setCol(0, 0, array('valign' => 'top', 'class' => 'small', 'style' => 'color: white;'), g_l('modules_spellchecker', '[default]'));
$table->setCol(0, 1, array('valign' => 'top', 'class' => 'small'), g_l('modules_spellchecker', '[dictionary]'));
$table->setCol(0, 2, array('valign' => 'top', 'class' => 'small'), g_l('modules_spellchecker', '[active]'));
$table->setCol(0, 3, array('valign' => 'top', 'class' => 'small'), g_l('modules_spellchecker', '[refresh]'));
$table->setCol(0, 4, array('valign' => 'top', 'class' => 'small'), g_l('modules_spellchecker', '[delete]'));

$_dir = dir(WE_SPELLCHECKER_MODULE_PATH . 'dict');

$_i = 0;
while(false !== ($entry = $_dir->read())) {
	if($entry != '.' && $entry != '..' && strpos($entry, '.zip') !== false){
		$_i++;
		$table->addRow();

		$_name = str_replace('.zip', '', $entry);

		$table->setCol($_i, 0, array('valign' => 'top'), we_forms::radiobutton($_name, (($spellcheckerConf['default'] == $_name) ? true : false), 'default', '', true, 'defaultfont', 'document.we_form.enable_' . $_name . '.value=1;document.we_form._enable_' . $_name . '.checked=true;'));
		$table->setCol($_i, 1, array('valign' => 'top', 'class' => 'defaultfont'), $_name);
		$table->setCol($_i, 2, array('valign' => 'top', 'align' => 'right'), we_forms::checkboxWithHidden(in_array($_name, $spellcheckerConf['active']), 'enable_' . $_name, '', false, 'defaultfont', ''));
		$table->setCol($_i, 3, array('valign' => 'top', 'align' => 'right'), we_button::create_button('image:btn_function_reload', 'javascript: updateDict("' . $_name . '");'));
		$table->setCol($_i, 4, array('valign' => 'top', 'align' => 'right'), we_button::create_button('image:btn_function_trash', 'javascript: deleteDict("' . $_name . '");'));
	}
}
$_dir->close();

$_button = we_button::create_button("close", "javascript:self.close();");
$tabsBody = $we_tabs->getHTML() . we_html_element::jsElement('if(!activ_tab) activ_tab = 1; document.getElementById("tab_"+activ_tab).className="tabActive";');

$_tab_1 =
	we_html_tools::htmlDialogLayout('
	 <form name="we_form" target="hiddenCmd" method="post" action="' . WE_SPELLCHECKER_MODULE_DIR . 'weSpellcheckerCmd.php">
	 <input type="hidden" name="cmd[0]" value="saveSettings" />
	 <div id="dictTable">
	 	<div id="selector" class="blockWrapper" style="width: 400px; height: 320px; border: 1px solid #AFB0AF;margin-bottom: 5px;background-color:#f6f6f6 ! important;">
		</div>

		<div id="dictSelector" style="display: none; width: 400px; height: 220px;background-color: silver;">
			<div id="appletPanel"></div>
		</div>

		<div id="addButt">' . we_button::create_button_table(array(we_button::create_button("save", "javascript:document.we_form.submit()"), we_button::create_button("add", "javascript:showDictSelector();"))) . '</div>
	</div>
	 ', '', '');


$_tab_2 =
	we_html_tools::htmlDialogLayout('
					<textarea class="defaultfont" name="defaultDict" style="width: 400px; padding:5px;height: 320px; border: 1px solid #AFB0AF;margin-bottom: 5px;background-color:white ! important;">' . (file_exists(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php') ? ((filesize(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php') > 0) ? weFile::load(WE_SPELLCHECKER_MODULE_PATH . 'dict/default.inc.php') : '') : '') . '</textarea>
					<div>' . we_button::create_button("save", "javascript:document.we_form.submit()") . '</div>
	</form>
	 ', '', '');


$_username = $_SESSION['user']['Username'];
$_replacement = array('\\', '/', ':', '*', '?', '<', '>', '|', '"');
for($_i = 0; $_i < count($_replacement); $_i++){
	$_username = str_replace($_replacement[$_i], 'MASK' . $_i, $_username);
}

$_applet_code = we_html_element::htmlApplet(array(
		'name' => "spellchecker",
		'code' => "com/livinge/spellchecker/swing/DictEditor.class",
		'archive' => "lespellchecker.jar",
		'codebase' => getServerUrl() . WE_SPELLCHECKER_MODULE_DIR,
		'width' => 400,
		'height' => 220,
		), '
<param name="code" value="com/livinge/spellchecker/swing/DictEditor.class"/>
<param name="archive" value="lespellchecker.jar"/>
<param name="type" value="application/x-java-applet;version=1.1"/>
<param name="dictBase" value="' . getServerUrl() . WE_SPELLCHECKER_MODULE_DIR . '/dict/"/>
<param name="dictionary" value="' . (isset($_SESSION['weS']['dictLang']) ? $_SESSION['weS']['dictLang'] : 'Deutsch') . '"/>
<param name="debug" value="off"><param name="user" value="' . $_username . '@' . $_SERVER['SERVER_NAME'] . '"/>
<param name="udSize" value="' . (is_file(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '.dict') ? filesize(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '.dict') : '0') . '"/>' .
		$l_params);
$_applet_code2 = we_html_element::htmlApplet(array(
		'name' => "spellcheckerCmd",
		'code' => "LeSpellchecker.class",
		'archive' => "lespellchecker.jar",
		'codebase' => getServerUrl() . WE_SPELLCHECKER_MODULE_DIR,
		'width' => 20,
		'height' => 20,), '
<param name="scriptable" value="true"/>
<param name="mayscript" value="true"/>
<param name="CODE" value="LeSpellchecker.class"/>
<param name="ARCHIVE" value="lespellchecker.jar"/>
<param name="type" value="application/x-java-applet;version=1.1"/>
<param name="dictBase" value="' . getServerUrl() . WE_SPELLCHECKER_MODULE_DIR . '/dict/"/>
<param name="dictionary" value="' . (isset($_SESSION['weS']['dictLang']) ? $_SESSION['weS']['dictLang'] : 'Deutsch') . '"/>
<param name="debug" value="off"><param name="user" value="' . $_username . '@' . $_SERVER['SERVER_NAME'] . '"/>
<param name="udSize" value="' . (is_file(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict') ? filesize(WE_SPELLCHECKER_MODULE_PATH . '/dict/' . $_username . '@' . $_SERVER['SERVER_NAME'] . '.dict') : '0') . '"/>');
?>

<script type="text/javascript"><!--

	var activ_tab = 1;
	var appletActiv = false;

	function sprintf() {
		if (!arguments || arguments.length < 1) {
			return;
		}

		var argum = arguments[0];
		var regex = /([^%]*)%(%|d|s)(.*)/;
		var arr = new Array();
		var iterator = 0;
		var matches = 0;

		while (arr = regex.exec(argum)) {
			var left = arr[1];
			var type = arr[2];
			var right = arr[3];

			matches++;
			iterator++;

			var replace = arguments[iterator];

			if (type == "d") {
				replace = parseInt(param) ? parseInt(param) : 0;
			} else if (type == "s") {
				replace = arguments[iterator];
			}

			argum = left + replace + right;
		}
		return argum;
	}

	function setVisible(id,visible){
		var elem = document.getElementById(id);
		if(visible==true) elem.style.display = "block";
		else elem.style.display = "none";
	}

	function toggle(id){
		var elem = document.getElementById(id);
		if(elem.style.display == "none") elem.style.display = "block";
		else elem.style.display = "none";
	}

	function showDictSelector() {
		setVisible("addButt",false);
		document.getElementById("selector").style.height = "100px";
		setVisible("dictSelector",true);
		setTimeout("setAppletCode()",1000);
	}

	function hideDictSelector() {
		setVisible("dictSelector",false);
		document.getElementById("selector").style.height = "320px";
		setVisible("addButt",true);
	}

	function setAppletCode() {
		if(!appletActiv) {
			document.getElementById('appletPanel').innerHTML = '<?php print addslashes($_applet_code) ?>';
		}
		appletActiv = true;
		setTimeout("checkApplet()",2000);
	}


	function updateDict(dict){

		setVisible("updateBut_"+dict,false);
		setVisible("updateIcon_"+dict,true);
		document.getElementById('appletPanel2').innerHTML = '<?php print addslashes($_applet_code2) ?>';
		setTimeout("selectDict(\""+dict+"\")",1000);
	}

	function updateDict(dict){

		setVisible("updateBut_"+dict,false);
		setVisible("updateIcon_"+dict,true);
		document.getElementById('appletPanel2').innerHTML = '<?php print addslashes($_applet_code2) ?>';
		setTimeout("selectDict(\""+dict+"\")",1000);
	}

	function selectDict(dict) {
		if(document.spellcheckerCmd.isReady) {
			if(document.spellcheckerCmd.isReady()) {
				document.spellcheckerCmd.setDict(dict);
				setTimeout("setStatusDone(\""+dict+"\")",3000);
			}
		}
	}

	function setStatusDone(dict) {
		if(document.spellcheckerCmd.isDictReady) {
			if(document.spellcheckerCmd.isDictReady()) {
				setVisible("updateBut_"+dict,true);
				setVisible("updateIcon_"+dict,false);
				return;
			}
		}
		setTimeout("setStatusDone()",3000);
	}


	function checkApplet() {

		if(appletActiv) {
			if(document.spellchecker.uploadFinished) {
				if(document.spellchecker.uploadFinished()) {
					if(document.spellchecker.packingFinished()) {
<?php print we_message_reporting::getShowMessageCall(g_l('modules_spellchecker', '[dict_saved]'), we_message_reporting::WE_MESSAGE_NOTICE); ?>
					}
					hideDictSelector();
					appletActiv=false;
					loadTable();
					return;
				}
			}

		}

		setTimeout("checkApplet()",2000);
	}

	function deleteDict(name) {
		if(confirm(sprintf("<?php print g_l('modules_spellchecker', '[ask_dict_del]'); ?>",name))){
			hiddenCmd.dispatch("deleteDict",name);
		}
	}

	function loadTable() {
		if(hiddenCmd.dispatch) {
			hiddenCmd.dispatch("refresh");
		} else {
			setTimeout("loadTable()",1000);
		}
	}

//-->
</script>

<?php print $js; ?>

</head>

<body onLoad="loadTable()" class="weDialogBody">

	<?php print $tabsBody; ?>

	<div id="content" style="margin: 10px; width: 450px;">
		<div id="tab1" style="display:block;">
			<?php print $_tab_1 ?>

		</div>
		<div id="tab2" style="display:none;">
			<?php print $_tab_2 ?>
		</div>

	</div>

	<div style="left:0px;height:40px;background-image: url(<?php echo IMAGE_DIR; ?>edit/editfooterback.gif);position:absolute;bottom:0px;width:100%"><div align="right" style="padding: 10px 10px 0 0;"><?php echo $_button; ?></div></div>


	<iframe name="hiddenCmd" id="hiddenCmd" style="position: absolute; left:0px; top:800px; display: block; border: 0px; width: 0px; height 0px;" src="<?php print WE_SPELLCHECKER_MODULE_DIR . 'weSpellcheckerCmd.php'; ?>"></iframe>

	<div id="appletPanel2" style="position: absolute; left:0px; top:900px; display: block; border: 0px; width: 0px; height 0px;">
	</div>

</body>

</html>
