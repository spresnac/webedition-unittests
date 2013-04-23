

$appDir = Zend_Controller_Front::getInstance()->getParam('appDir');
$appName = Zend_Controller_Front::getInstance()->getParam('appName');

$frameset = new we_ui_layout_Frameset();

$frameset->setRows(((isset($_SESSION['prefs']['debug_normal']) && $_SESSION['prefs']['debug_normal'] != 0) ? '32,*,100' : '32,*,0'));
	//$frameset->setOnLoad('start();');

$param = 	($this->tab ?
				'/tab/' . $this->tab :
				'') .
			($this->sid ?
				'/sid/' . $this->sid :
				'') .
			($this->modelId ?
				'/modelId/' . $this->modelId :
				'');

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/header/index',
	'name' => 'header',
	'scrolling' => 'no',

	'noresize' => 'noresize'
));

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/frameset/resize' . $param,
	'name' => 'resize',
	'scrolling' => 'no'
));

$frameset->addFrame(array(
	'src' => 'about:blank',
	'name' => 'cmd_' . $appName,
	'scrolling' => 'no',
	'noresize' => 'noresize'
));

$page = we_ui_layout_HTMLPage::getInstance();
$page->setIsTopFrame(true);
$page->setFrameset($frameset);
$page->addJSFile(JS_DIR . 'windows.js');
$page->addJSFile(JS_DIR . 'we_showMessage.js');
$page->addJSFile(JS_DIR . 'images.js');
$page->addJSFile(JS_DIR . 'libs/yui/yahoo-min.js');
$page->addJSFile(JS_DIR . 'libs/yui/event-min.js');
$page->addJSFile(JS_DIR . 'libs/yui/connection-min.js');
$page->addJSFile(JS_DIR . 'libs/yui/json-min.js');
$page->addJSFile(LIB_DIR . 'we/core/JsonRpc.js');

$page->addInlineJS($this->getJSTop());

echo $page->getHTML();
