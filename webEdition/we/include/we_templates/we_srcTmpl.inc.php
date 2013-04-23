<?php
/**
 * webEdition CMS
 *
 * $Rev: 5945 $
 * $Author: mokraemer $
 * $Date: 2013-03-13 00:10:50 +0100 (Wed, 13 Mar 2013) $
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
include_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

$parts = array();

if(isset($we_doc->elements["Charset"]["dat"])){ //	send charset which might be determined in template
	we_html_tools::headerCtCharset('text/html', $we_doc->elements["Charset"]["dat"]);
}

if(!$GLOBALS['we_editmode']){
	exit();
}
we_html_tools::htmlTop('', isset($we_doc->elements["Charset"]["dat"]) ? $we_doc->elements["Charset"]["dat"] : '');
echo we_html_element::jsScript(JS_DIR . 'windows.js');
include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
print STYLESHEET;

$_useJavaEditor = ($_SESSION['prefs']['editorMode'] == 'java');
?>
<script  type="text/javascript"><!--
	var weIsTextEditor = true;
	try{
		top.we_setEditorWasLoaded(false);
	}catch(e){
		;
	}
	var countJEditorInitAttempts = 0;
	var wizardHeight={
		"open" : 305,
		"closed" : 140
	}

	function sizeEditor() { // to be fixed (on 12.12.11)
		var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
		var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
		w = Math.max(w,350);
		var editorWidth = w - <?php echo ($_SESSION['prefs']['editorMode'] == 'codemirror2' ? 60 : 37); ?>;
		var wizardOpen = weGetCookieVariable("but_weTMPLDocEdit") == "right";

		var editarea = document.getElementById("editarea");

		var wizardTable = document.getElementById("wizardTable");
		var tagAreaCol = document.getElementById("tagAreaCol");
		var tagSelectCol = document.getElementById("tagSelectCol");
		var spacerCol = document.getElementById("spacerCol");
		var tag_edit_area = document.getElementById("tag_edit_area");

		if (editarea) {
			editarea.style.width=editorWidth + "px";
			if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
				editarea.nextSibling.style.width=editorWidth + "px";
		}

		if (document.weEditorApplet && typeof(document.weEditorApplet.width) != "undefined") {
			document.weEditorApplet.width = editorWidth;
		}

		if(window.editor && window.editor.frame) {
			if(window.editor.frame.nextSibling!=undefined) {
				editorWidth-=window.editor.frame.nextSibling.offsetWidth;
				document.getElementById("reindentButton").style.marginRight= (window.editor.frame.nextSibling.offsetWidth-3) + "px";
			}
			window.editor.frame.style.width = editorWidth + "px";
		}

		if (h) { // h must be set (h!=0), if several documents are opened very fast -> editors are not loaded then => h = 0

<?php
if(we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 9){
	echo 'h=document.body.offsetHeight; h=Math.max(h,600);';
}
?>
			if (wizardTable != null) {
				var editorHeight = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open));

				if (editarea) {
					editarea.style.height= (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
					if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
						editarea.nextSibling.style.height= (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
				}

				if(window.editor && window.editor.frame) {
					window.editor.frame.style.height = (h - (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
				}

				if (document.weEditorApplet && typeof(document.weEditorApplet.setSize) != "undefined") {
					try{
						document.weEditorApplet.height = editorHeight;
						//document.weEditorApplet.setSize(editorWidth,editorHeight);
					}catch(err){/*nothing*/}

				}


				wizardTable.style.width=editorWidth+"px";
				//wizardTableButtons.style.width=editorWidth+"px"; // causes problems with codemirror2
				tagAreaCol.style.width=(editorWidth-300)+"px";
				tag_edit_area.style.width=(editorWidth-300)+"px";
				tagSelectCol.style.width = "250px";
				spacerCol.style.width = "50px";

			} else {
				if (editarea) {
					editarea.style.height = (h - wizardHeight.closed) + "px";
					if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
						editarea.nextSibling.style.height = (h - wizardHeight.closed) + "px";
				}

				if(window.editor && window.editor.frame) {
					window.editor.frame.style.height = (h - wizardHeight.closed) + "px";
				}

				if (document.weEditorApplet && typeof(document.weEditorApplet.setSize) != "undefined") {
					try{
						document.weEditorApplet.height = h - wizardHeight.closed;
						//document.weEditorApplet.setSize(editorWidth,h - wizardHeight.closed);
					}catch(err){/*nothing*/}
				}
			}
		}
		window.scroll(0,0);
	}

	var editor=null;

	function javaEditorSetCode() {// imi: console.log("javaEditorSetCode() called");
		if (document.weEditorApplet.height != 3000) {
			try {
				document.weEditorApplet.setCode(document.forms['we_form'].elements["<?php print 'we_' . $we_doc->Name . '_txt[data]'; ?>"].value);
				countJEditorInitAttempts = 0;
			}catch(err){
				setTimeout(javaEditorSetCode, 1000);
			}
		} else { // change size not yet finished
			setTimeout(javaEditorSetCode, 1000);
		}
	}


	var editor=null;
	var hlLine = null;
	function initEditor() {
<?php
switch($_SESSION['prefs']['editorMode']){
	case 'codemirror2':
		?>
						try{
							document.getElementById("bodydiv").style.display="block";
							editor = CodeMirror.fromTextArea(document.getElementById("editarea"), CMoptions);
							sizeEditor();
							//highlight current line
							hlLine = editor.addLineClass(0, "background", "activeline");
							editor.on("cursorActivity", function() {
								var cur = editor.getLineHandle(editor.getCursor().line);
								if (cur != hlLine) {
									editor.removeLineClass(hlLine, "background", "activeline");
									hlLine = editor.addLineClass(cur, "background", "activeline");
								}
							});
							editor.on("change", function() {
								//this wil save content from CodeMirror2 to our original <textarea>.
								var currentTemplateCode=editor.getValue().replace(/\r/g,"\n");
								if(window.orignalTemplateContent!=currentTemplateCode) {
									window.orignalTemplateContent=currentTemplateCode;
									document.getElementById("editarea").value=currentTemplateCode;
									_EditorFrame.setEditorIsHot(true);
								}
							});
							var foldHtml=CodeMirror.newFoldFunction(CodeMirror.tagRangeFinder);
							var foldOther=CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);
							editor.on("gutterClick", function(cm,n) {
								foldHtml(cm,n);
								foldOther(cm,n);
							});

						}catch(e){
							//console.log("CM init error");
						}
						return;
		<?php
		break;
	case 'java':
		?>
						countJEditorInitAttempts++;
						// imi: console.log("init: " + countJEditorInitAttempts);
						if(countJEditorInitAttempts < 10){
							if (document.weEditorApplet && top.weEditorWasLoaded && typeof(document.weEditorApplet.setCode) != "undefined" && typeof(document.weEditorApplet.initUndoManager)!="undefined") {
								try{
									sizeEditor();
									document.getElementById("weEditorApplet").style.left="0";
									javaEditorSetCode();
									checkAndSetHot();
								}catch(err){
									setTimeout(initEditor, 500);
								}
							} else {// imi: console.log("weEditorWasLoaded == false");
								setTimeout(initEditor, 500);
							}
						} else {
							alert("JavaEditor could not be loaded. Please close this Template and try again."); // TODO: make regular we-Alaert
						}
		<?php
		break;
	default:
		?>
						sizeEditor();
						document.getElementById("bodydiv").style.display="block";
						window.setTimeout('scrollToPosition();',50);
		<?php
		break;
}
?>

	}

	function toggleTagWizard() {
		var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
		w = Math.max(w,350);
		var editorWidth = w - 37;
		var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
		var wizardOpen = weGetCookieVariable("but_weTMPLDocEdit") == "down";
		if (document.weEditorApplet) {
			var editorHeight = h- (wizardOpen ? wizardHeight.closed : wizardHeight.open);
			document.weEditorApplet.height = editorHeight;
		} else {
			var editarea = document.getElementById("editarea");
			editarea.style.height= (h- (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
			if(editarea.nextSibling!=undefined && editarea.nextSibling.style)
				editarea.nextSibling.style.height= (h- (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";

			if(window.editor && window.editor.frame) {
				window.editor.frame.style.height = (h- (wizardOpen ? wizardHeight.closed : wizardHeight.open)) + "px";
			}
		}
	}

	// ################ Java Editor specific Functions

	function weEditorSetHiddenText() {
		if (document.weEditorApplet && typeof(document.weEditorApplet.getCode) != "undefined") {
			if (document.weEditorApplet.isHot()) {
				_EditorFrame.setEditorIsHot(true);
				document.weEditorApplet.setHot(false);
			}
			document.forms['we_form'].elements["<?php print 'we_' . $we_doc->Name . '_txt[data]'; ?>"].value = document.weEditorApplet.getCode();
		}
	}


	function checkAndSetHot() {
		if (document.weEditorApplet && typeof(document.weEditorApplet.isHot) != "undefined") {
			if (document.weEditorApplet.isHot()) {
				_EditorFrame.setEditorIsHot(true);
			} else {
				setTimeout("checkAndSetHot()", 1000);
			}
		}
	}

	function setCode() {
		if (document.weEditorApplet && typeof(document.weEditorApplet.setCode) != "undefined") {
			document.weEditorApplet.setCode(document.forms['we_form'].elements["<?php print 'we_' . $we_doc->Name . '_txt[data]'; ?>"].value);
		}
	}

	// ################## Textarea specific functions #############

	function getScrollPosTop () {
		var elem = document.getElementById("editarea");
		if (elem) {
			return elem.scrollTop;
		}
		return 0;

	}

	function getScrollPosLeft () {
		var elem = document.getElementById("editarea");
		if (elem) {
			return elem.scrollLeft;
		}
		return 0;
	}

	function scrollToPosition () {
		var elem = document.getElementById("editarea");
		if (elem) {
			elem.scrollTop=parent.editorScrollPosTop;
			elem.scrollLeft=parent.editorScrollPosLeft;
		}
	}

	function wedoKeyDown(ta,keycode){
		modifiers = (event.altKey || event.ctrlKey || event.shiftKey);
		if (!modifiers && keycode == 9) { // TAB
			if (ta.setSelectionRange) {
				var selectionStart = ta.selectionStart;
				var selectionEnd = ta.selectionEnd;
				ta.value = ta.value.substring(0, selectionStart)
					+ "\t"
					+ ta.value.substring(selectionEnd);
				ta.focus();
				ta.setSelectionRange(selectionEnd+1, selectionEnd+1);
				ta.focus();
				return false;

			} else if (document.selection) {
				var selection = document.selection;
				var range = selection.createRange();
				range.text = "\t";
				return false;
			}
		}

		return true;
	}
	// ############ EDITOR PLUGIN ################

	function setSource(source){
		document.forms['we_form'].elements['we_<?php print $we_doc->Name; ?>_txt[data]'].value=source;
		//Codemirror
		if(editor!="undefined" && editor !=null && typeof editor =='object'){
			editor.setValue(source);
		}
		// for Applet
		setCode(source);
	}

	function getSource(){
		if (document.weEditorApplet && typeof(document.weEditorApplet.getCode) != "undefined") {
			return document.weEditorApplet.getCode();
		} else {
			return document.forms['we_form'].elements['we_<?php print $we_doc->Name; ?>_txt[data]'].value;
		}
	}

	function getCharset(){
		return "<?php print !empty($we_doc->elements['Charset']['dat']) ? $we_doc->elements['Charset']['dat'] : $GLOBALS['WE_BACKENDCHARSET']; ?>";
	}

	// ############ CodeMirror Functions ################

	function reindent() { // reindents code of CodeMirror2
		if(editor.somethingSelected()){
			start=editor.getCursor(true).line;
			end=editor.getCursor(false).line;
		}else{
			start=0;
			end=editor.lineCount();
		}
		for(i=start;i<end;++i){
			editor.indentLine(i,'smart');
		}
	}
	var lastPos = null, lastQuery = null, marked = [];
	function unmark() {
		for (var i = 0; i < marked.length; ++i){
			marked[i].clear();
		}
		marked.length = 0;
	}
	function search(text,caseIns) {
		unmark();
		if (!text){
			return;
		}
		for (var cursor = editor.getSearchCursor(text,0,caseIns); cursor.findNext();){
			marked.push(editor.markText(cursor.from(), cursor.to(), {className:"searched"}));
		}
		if (lastQuery != text){
			lastPos = null;
		}
		var cursor = editor.getSearchCursor(text, lastPos || editor.getCursor());
		if (!cursor.findNext()) {
			cursor = editor.getSearchCursor(text);
			if (!cursor.findNext()){
				return;
			}
		}
		editor.setSelection(cursor.from(), cursor.to());
		lastQuery = text;
		lastPos = cursor.to();
	}

	function myReplace(text, replaceby, caseIns) {
		if(!text|| !replaceby){
			return;
		}
		if(editor.getSelection()!=text){
			search(text,caseIns);
		}
		if(editor.getSelection()!=text){
			return;
		}
		editor.replaceSelection(replaceby);
		search(text, caseIns);
	}
	//-->
</script>
</head>
<body class="weEditorBody" style="overflow:hidden;" onLoad="setTimeout('initEditor()',200);" onUnload="doUnload(); parent.editorScrollPosTop = getScrollPosTop(); parent.editorScrollPosLeft = getScrollPosLeft();" <?php
//FIXME: no resize for IE!
echo (we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 9 ? '' : 'onResize="sizeEditor();"');
?>>
	<form name="we_form" method="post" onsubmit="return false;" style="margin:0px;"><?php
			echo we_class::hiddenTrans();

			function we_getJavaEditorCode($code){
				global $we_doc;
				$params = array(
					'phpext' => '.php',
					'serverUrl' => getServerUrl(true),
					'editorPath' => 'webEdition/editors/template/editor',
				);
				if($_SESSION["prefs"]["editorFont"] == 1){
					// translate html font names into java font names
					switch($_SESSION["prefs"]["editorFontname"]){
						case "mono":
							$fontname = 'monospaced';
							break;
						case "sans-serif":
							$fontname = 'sansserif';
							break;
						default:
							$fontname = $_SESSION['prefs']['editorFontname'];
							break;
					}
					$params['fontName'] = $fontname;
					$params['fontSize'] = $_SESSION["prefs"]["editorFontsize"];
				}

				if($_SESSION["prefs"]["specify_jeditor_colors"] == 1){
					$params['normalColor'] = $_SESSION["prefs"]["editorFontcolor"];
					$params['weTagColor'] = $_SESSION["prefs"]["editorWeTagFontcolor"];
					$params['weAttributeColor'] = $_SESSION["prefs"]["editorWeAttributeFontcolor"];
					$params['HTMLTagColor'] = $_SESSION["prefs"]["editorHTMLTagFontcolor"];
					$params['HTMLAttributeColor'] = $_SESSION["prefs"]["editorHTMLAttributeFontcolor"];
					$params['piColor'] = $_SESSION["prefs"]["editorPiTagFontcolor"];
					$params['commentColor'] = $_SESSION["prefs"]["editorCommentFontcolor"];
				}

				return
					'<input type="hidden" name="we_' . $we_doc->Name . '_txt[data]" value="' . oldHtmlspecialchars($code) . '" />' .
					we_html_element::htmlApplet(array(
						'id' => 'weEditorApplet',
						'style' => 'position:relative;left:-4000px;',
						'name' => 'weEditorApplet',
						'code' => 'Editor.class',
						'archive' => 'editor.jar',
						'width' => 3000,
						'height' => 3000, // important! function javaEditorSetCode() uses this value as condition
						'codebase' => getServerUrl(true) . WEBEDITION_DIR . 'editors/template/editor',
						), '', $params);
			}

			function we_getCodeMirror2Tags($css){
				//FIXME: this should only be loaded once! not for every document opened!
				$ret = '';
				$allTags = array();
				$setting = @unserialize($_SESSION['prefs']['editorCodecompletion']);
				if($css || $setting['WE']){
					$allWeTags = weTagWizard::getExistingWeTags();
					foreach($allWeTags as $tagName){
						$weTag = weTagData::getTagData($tagName);
						if($css){
							$ret.='.cm-weTag_' . $tagName . ':hover:after {content: "' . str_replace('"', '\'', html_entity_decode($weTag->getDescription(), null, $GLOBALS['WE_BACKENDCHARSET'])) . '";}';
						} else{
							$allTags['we:' . $tagName] = array('we' => $weTag->getAttributesForCM());
						}
					}
				}
				if($css){
					return $ret;
				}

				include($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_templates/htmlTags.inc.php');
				$allTags = array_merge($allTags, ($setting['htmlTag'] ? $html : array()), ($setting['html5Tag'] ? $html5 : array()));
				if(empty($allTags)){
					return '';
				}
				$ret.='CodeMirror.weHints["<"] = ["' . implode('","', array_keys($allTags)) . '"];' . "\n";
				foreach($allTags as $tagName => $cur){
					$attribs = array();
					foreach($cur as $type => $attribList){
						switch($type){
							case 'we':
								$ok = true;
								break;
							case 'default':
								$ok = ($setting['htmlDefAttr']);
								break;
							case 'js':
								$ok = ($setting['htmlJSAttr']);
								break;
							case 'norm':
								$ok = ($setting['htmlAttr']);
								break;
							case 'default_html5':
								$ok = ($setting['html5Tag'] && $setting['htmlDefAttr']);
								break;
							case 'html5':
								$ok = ($setting['html5Tag'] && $setting['html5Attr']);
								break;
							default:
								$ok = false;
						}
						if($ok){
							foreach($attribList as $attr){
								$attribs[] = '\'' . $attr . (strstr($attr, '"') === false ? '=""' : '') . '\'';
							}
						}
					}
					if(!empty($attribs)){
						$attribs = array_unique($attribs);
						sort($attribs);
						$ret.='CodeMirror.weHints["<' . $tagName . ' "] = [' . implode(',', $attribs) . '];' . "\n";
					}
				}
				return $ret;
			}

			function we_getCodeMirror2Code($code){
				$maineditor = '';
				$parser_js = array();
				$parser_css = array('theme/elegant.css', 'theme/cobalt.css');
				$toolTip = false;
				switch($GLOBALS['we_doc']->ContentType){ // Depending on content type we use different parsers and css files
					case 'text/css':
						$parser_js[] = 'mode/css/css.js';
						$parser_js[] = 'addon/fold/foldcode.js';
						$parser_js[] = 'addon/edit/matchbrackets.js';
						$mode = 'text/css';
						break;
					case 'text/js':
						$parser_js[] = 'mode/javascript/javascript.js';
						$parser_js[] = 'addon/fold/foldcode.js';
						$parser_js[] = 'addon/edit/matchbrackets.js';
						$mode = 'text/javascript';
						break;
					case 'text/weTmpl':
						$parser_js[] = 'addon/mode/overlay.js';
						$parser_js[] = 'mode/webEdition/webEdition.js';
						$parser_js[] = 'addon/edit/closetag.js';
						$parser_js[] = 'addon/fold/foldcode.js';
						$parser_js[] = 'addon/edit/matchbrackets.js';
						if(true || $_SESSION['prefs']['editorCodecompletion']){
							$parser_js[] = 'addon/hint/simple-hint.js';
							$parser_js[] = 'addon/we/we-hint.js';
						}
						$parser_css[] = 'lib/util/simple-hint.css';
						$toolTip = $_SESSION['prefs']['editorTooltips'];
						$mode = 'text/weTmpl';
					case 'text/html':
						$parser_js[] = 'mode/xml/xml.js';
						$parser_js[] = 'mode/javascript/javascript.js';
						$parser_js[] = 'mode/css/css.js';
						$parser_js[] = 'mode/htmlmixed/htmlmixed.js';
						$parser_js[] = 'mode/clike/clike.js';
						$parser_js[] = 'mode/php/php.js';
						$parser_js[] = 'addon/edit/closetag.js';
						$parser_js[] = 'addon/fold/foldcode.js';
						$parser_js[] = 'addon/edit/matchbrackets.js';
						$mode = (isset($mode) ? $mode : 'application/x-httpd-php');
						break;
					case 'text/xml':
						$parser_js[] = 'mode/xml/xml.js';
						$parser_js[] = 'addon/edit/matchbrackets.js';
						$mode = 'application/xml';
						break;
					default:
						//don't use CodeMirror
						return '';
				}

				$parser_css[] = 'mode/webEdition/webEdition.css';

				if(!empty($parser_js)){ // CodeMirror will be used
					$parser_js[] = 'addon/search/searchcursor.js';
					$maineditor = we_html_element::cssLink(WEBEDITION_DIR . 'editors/template/CodeMirror2/lib/codemirror.css') .
						we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror2/lib/codemirror.js');
					foreach($parser_css as $css){
						$maineditor.=we_html_element::cssLink(WEBEDITION_DIR . 'editors/template/CodeMirror2/' . $css);
					}
					foreach($parser_js as $js){
						$maineditor.=we_html_element::jsScript(WEBEDITION_DIR . 'editors/template/CodeMirror2/' . $js);
					}

					$tmp = @unserialize($_SESSION['prefs']['editorCodecompletion']);
					$hasCompletion = is_array($tmp) ? array_sum($tmp) : false;
					$maineditor.=we_html_element::cssElement(($toolTip && $GLOBALS['we_doc']->ContentType == 'text/weTmpl' ? we_getCodeMirror2Tags(true) : '') . '
.weSelfClose:hover:after, .cm-weSelfClose:hover:after, .weOpenTag:hover:after, .cm-weOpenTag:hover:after, .weTagAttribute:hover:after, .cm-weTagAttribute:hover:after {
	font-family: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontname'] ? $_SESSION['prefs']['editorTooltipFontname'] : 'sans-serif') . ';
	font-size: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontsize'] ? $_SESSION['prefs']['editorTooltipFontsize'] : '12') . 'px;
	line-height: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontsize'] ? $_SESSION['prefs']['editorTooltipFontsize'] * 1.5 : '18') . 'px;
}

.CodeMirror{
	font-family: ' . ($_SESSION['prefs']['editorFont'] && $_SESSION['prefs']['editorFontname'] ? $_SESSION['prefs']['editorFontname'] : 'monospace') . ';
	font-size: ' . ($_SESSION['prefs']['editorFont'] && $_SESSION['prefs']['editorFontsize'] ? $_SESSION['prefs']['editorFontsize'] : '12') . 'px;
	line-height: ' . ($_SESSION['prefs']['editorTooltipFont'] && $_SESSION['prefs']['editorTooltipFontsize'] ? $_SESSION['prefs']['editorTooltipFontsize'] * 1.5 : '18') . 'px;
}

' . ($_SESSION['prefs']['editorShowTab'] ? '
.cm-tab {
	background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAMCAYAAAAkuj5RAAAAAXNSR0IArs4c6QAAAGFJREFUSMft1LsRQFAQheHPowAKoACx3IgEKtaEHujDjORSgWTH/ZOdnZOcM/sgk/kFFWY0qV8foQwS4MKBCS3qR6ixBJvElOobYAtivseIE120FaowJPN75GMu8j/LfMwNjh4HUpwg4LUAAAAASUVORK5CYII=);
	background-position: right;
	background-repeat: no-repeat;
}' : '')
						) . we_html_element::jsElement('
var CMoptions = { //these are the CodeMirror options
	mode: "' . $mode . '",
	enterMode: "indent",
	electricChars: true,
	theme: "elegant",
	lineNumbers: ' . ($_SESSION['prefs']['editorLinenumbers'] ? 'true' : 'false') . ',
	gutter: true,
	indentWithTabs: true,
	tabSize: ' . intval($_SESSION['prefs']['editorTabSize']) . ',
	indentUnit: ' . intval($_SESSION['prefs']['editorTabSize']) . ',
	matchBrackets: true,
	workTime: 300,
	workDelay: 800,
	height: ' . intval(($_SESSION["prefs"]["editorHeight"] != 0) ? $_SESSION["prefs"]["editorHeight"] : 320) . ',
	lineWrapping:' . ((isset($_SESSION["we_wrapcheck"]) && $_SESSION["we_wrapcheck"]) ? 'true' : 'false') . ',
	autoCloseTags: ' . ($_SESSION['prefs']['editorDocuintegration'] ? 'true' : 'false') . ', // use object with indentTags to indent these tags
	autofocus: true,
	extraKeys: {' . ($hasCompletion ? '
							  "\' \'": function(cm) { CodeMirror.weHint(cm, \' \'); },
							  "\'<\'": function(cm) { CodeMirror.weHint(cm, \'<\'); },
							  "Ctrl-Space": function(cm) { CodeMirror.weHint(cm, \'\'); }' : ''
							) . '
	}
};' . ($hasCompletion && $GLOBALS['we_doc']->ContentType == 'text/weTmpl' ? we_getCodeMirror2Tags(false) : '') . '
window.orignalTemplateContent=document.getElementById("editarea").value.replace(/\r/g,""); //this is our reference of the original content to compare with current content
');
				}
				return $maineditor;
			}

			$wrap = (isset($_SESSION["we_wrapcheck"]) && $_SESSION["we_wrapcheck"]) ? "virtual" : "off";

			$code = $we_doc->getElement("data");

			if($we_doc->ClassName == "we_htmlDocument"){
				$code = $we_doc->getDocumentCode();
			}

			$maineditor = '<table border="0" cellpadding="0" cellspacing="0" width="95%"><tr><td>';

			if($_useJavaEditor){
				$maineditor .= we_getJavaEditorCode($code);
			} else{
				$maineditor .= '<textarea id="editarea" style="width: 100%; height: ' . (($_SESSION["prefs"]["editorHeight"] != 0) ? $_SESSION["prefs"]["editorHeight"] : "320") . 'px;' . (($_SESSION["prefs"]["editorFont"] == 1) ? " font-family: " . $_SESSION["prefs"]["editorFontname"] . "; font-size: " . $_SESSION["prefs"]["editorFontsize"] . "px;" : "") . '" id="data" name="we_' . $we_doc->Name . '_txt[data]" wrap="' . $wrap . '" ' . ((!we_base_browserDetect::isGecko() && (!isset($_SESSION["we_wrapcheck"]) || !$_SESSION["we_wrapcheck"] )) ? '' : ' rows="20" cols="80"') . ' onhange="_EditorFrame.setEditorIsHot(true);" ' . ($_SESSION['prefs']['editorMode'] == 'codemirror2' ? '' : (we_base_browserDetect::isIE() || we_base_browserDetect::isOpera() ? 'onkeydown="return wedoKeyDown(this,event.keyCode);"' : 'onkeypress="return wedoKeyDown(this,event.keyCode);"')) . '>'
					. oldHtmlspecialchars($code) . '</textarea>';
				if($_SESSION['prefs']['editorMode'] == 'codemirror2'){ //Syntax-Highlighting
					$maineditor .= we_getCodeMirror2Code($code);
				}
			}
			$maineditor .= '</td>
         </tr>
         <tr>
            <td align="left">' .
				we_html_tools::getPixel(2, 10) . '<br><table cellpadding="0" cellspacing="0" border="0" width="100%">
	    <tr>
<td align="left" class="defaultfont">' .
				(substr($_SESSION['prefs']['editorMode'], 0, 10) == 'codemirror' ? '
<input type="text" style="width: 10em;float:left;" id="query"/><div style="float:left;">' . we_button::create_button("search", 'javascript:search(document.getElementById("query").value,!document.getElementById("caseSens").checked);') . '</div>
<input type="text" style="margin-left:2em;width: 10em;float:left;" id="replace"/><div style="float:left;">' . we_button::create_button("replace", 'javascript:myReplace(document.getElementById("query").value,document.getElementById("replace").value,!document.getElementById("caseSens").checked);') . '</div>
<input type="checkbox" style="margin-left:2em;float:left;" id="caseSens"/><div style="float:left;">' . g_l('weClass', '[caseSensitive]') . '</div>' : ''
				) . '
					</td>
					<td align="right" class="defaultfont">' .
				(substr($_SESSION['prefs']['editorMode'], 0, 10) == 'codemirror' ? '
<div id="reindentButton" style="float:right;margin-left:10px;margin-top:-3px;">' . we_button::create_button("reindent", 'javascript:reindent();') . '</div>' : '') .
				($_useJavaEditor ? "" : we_forms::checkbox("1", ( isset($_SESSION["we_wrapcheck"]) && $_SESSION["we_wrapcheck"] == "1"), "we_wrapcheck_tmp", g_l('global', '[wrapcheck]'), false, "defaultfont", "we_cmd('wrap_on_off',this.checked)")) . '</td>	</tr>
        </table></td></tr></table>';
			$znr = -1;
			$wepos = "";
			$parts[] = array("headline" => "", "html" => $maineditor, "space" => 0);


			if($we_doc->ContentType == "text/weTmpl"){
				// Code Wizard
				$CodeWizard = new weCodeWizard();
				$allWeTags = weTagWizard::getExistingWeTags();

				$tagGroups = weTagWizard::getWeTagGroups($allWeTags);

				$groupJs = "tagGroups = new Array();\n";
				$selectedGroup = isset($we_doc->TagWizardSelection) && !empty($we_doc->TagWizardSelection) ? $we_doc->TagWizardSelection : "alltags";
				$groupselect = '<select class="weSelect" style="width: 250px;" id="weTagGroupSelect" name="we_' . $we_doc->Name . '_TagWizardSelection" onchange="selectTagGroup(this.value);">
<optgroup label="' . g_l('weCodeWizard', '[snippets]') . '">
<option value="snippet_standard" ' . ($selectedGroup == "snippet_standard" ? "selected" : "") . '>' . g_l('weCodeWizard', '[standard_snippets]') . '</option>
		<option value="snippet_custom" ' . ($selectedGroup == "snippet_custom" ? "selected" : "") . '>' . g_l('weCodeWizard', '[custom_snippets]') . '</option>
		</optgroup>
		<optgroup label="we:tags">';

				foreach($tagGroups as $tagGroupName => $tags){

					if($tagGroupName == 'custom'){
						$groupselect .= '<option value="-1" disabled="disabled">----------</option>';
					}
					$groupselect .= '<option value="' . $tagGroupName . '"' . ($tagGroupName == $selectedGroup ? ' selected="selected"' : '') . '">' . (in_array($tagGroupName, $GLOBALS['_we_active_integrated_modules']) ? g_l('javaMenu_moduleInformation', '[' . $tagGroupName . '][text]') : g_l('weTagGroups', '[' . $tagGroupName . ']')) . '</option>';
					if($tagGroupName == 'alltags'){
						$groupselect .= '<option value="-1" disabled="disabled">----------</option>';
					}
					$groupJs .= "tagGroups['" . $tagGroupName . "'] = new Array('" . implode("', '", $tags) . "');\n";
				}
				$groupselect .= '</optgroup></select>';

				$tagselect = '<select onkeydown="evt=event?event:window.event; return openTagWizWithReturn(evt)" class="defaultfont" style="width: 250px; height: 100px;" size="7" ondblclick="edit_wetag(this.value);" name="tagSelection" id="tagSelection" onChange="weButton.enable(\'btn_direction_right_applyCode\')">';

				for($i = 0; $i < count($allWeTags); $i++){
					$tagselect .= '
	<option value="' . $allWeTags[$i] . '">' . $allWeTags[$i] . '</option>';
				}


				$tagselect .= '</select>';

				// buttons
				$editTagbut = we_button::create_button("image:btn_direction_right", "javascript:executeEditButton();", true, 100, 22, "", "", false, false, "_applyCode");
				$selectallbut = we_button::create_button("selectAll", "javascript:document.getElementById(\"tag_edit_area\").focus(); document.getElementById(\"tag_edit_area\").select();");
				$prependbut = we_button::create_button("prepend", 'javascript:insertAtStart(document.getElementById("tag_edit_area").value);');
				$appendbut = we_button::create_button("append", 'javascript:insertAtEnd(document.getElementById("tag_edit_area").value);');
				$addCursorPositionbut = we_button::create_button("addCursorPosition", 'javascript:addCursorPosition(document.getElementById("tag_edit_area").value);_EditorFrame.setEditorIsHot(true);');

				$tagWizardHtml = $CodeWizard->getJavascript() .
					we_html_element::jsElement(
						'function executeEditButton() {
				if(document.getElementById(\'weTagGroupSelect\').value == \'snippet_custom\') {
					YUIdoAjax(document.getElementById(\'codesnippet_custom\').value);

				} else if(document.getElementById(\'weTagGroupSelect\').value == \'snippet_standard\') {
					YUIdoAjax(document.getElementById(\'codesnippet_standard\').value);

				} else {
					var _sel=document.getElementById(\'tagSelection\');
					if(_sel.selectedIndex > -1) {
						edit_wetag(_sel.value);
					}
				}
		 	}

		 	function openTagWizardPrompt( _wrongTag ) {


		 		var _prompttext = "' . g_l('weTagWizard', '[insert_tagname]') . '";
		 		if ( _wrongTag ) {
		 			_prompttext = "' . sprintf(g_l('weTagWizard', '[insert_tagname_not_exist]'), '\"" + _wrongTag + "\"') . '\n\n" + _prompttext;
		 		}

		 		var _tagName = prompt(_prompttext);
		 		var _tagExists = false;

		 		if ( typeof(_tagName) == "string") {

			 		for ( i=0; i < tagGroups["alltags"].length && !_tagExists; i++ ) {
			 			if ( tagGroups["alltags"][i] == _tagName ) {
			 				_tagExists = true;

			 			}
			 		}

			 		if ( _tagExists ) {
			 			edit_wetag(_tagName, 1);

			 		} else {
			 			openTagWizardPrompt( _tagName );

			 		}
			 	}
		 	}

			function edit_wetag(tagname, insertAtCursor) {
				if (!insertAtCursor) {
					insertAtCursor = 0;
				}
				we_cmd("open_tag_wizzard", tagname, insertAtCursor);

			}

			function insertAtStart(tagText) {
				if (document.weEditorApplet && typeof(document.weEditorApplet.insertAtStart) != "undefined") {
					document.weEditorApplet.insertAtStart(tagText);
				} else if(window.editor && window.editor.frame) {
					window.editor.insertIntoLine(window.editor.firstLine(), 0, tagText + "\n");
				} else {
				 	document.we_form["we_' . $we_doc->Name . '_txt[data]"].value = tagText + "\n" + document.we_form["we_' . $we_doc->Name . '_txt[data]"].value;
				}
				_EditorFrame.setEditorIsHot(true);
			}

			function insertAtEnd(tagText) {
				if (document.weEditorApplet && typeof(document.weEditorApplet.insertAtEnd) != "undefined") {
					document.weEditorApplet.insertAtEnd(tagText);
				} else if(window.editor && window.editor.frame) {
					window.editor.insertIntoLine(window.editor.lastLine(), "end", "\n" + tagText);
				} else {
					document.we_form["we_' . $we_doc->Name . '_txt[data]"].value += "\n" + tagText;
				}
				_EditorFrame.setEditorIsHot(true);

			}

			function addCursorPosition ( tagText ) {

				if (document.weEditorApplet && typeof(document.weEditorApplet.replaceSelection) != "undefined") {
					document.weEditorApplet.replaceSelection(tagText);
				} else if(window.editor && window.editor.frame) {
					window.editor.replaceSelection(tagText);
				} else {
					var weForm = document.we_form["we_' . $we_doc->Name . '_txt[data]"];
					if(document.selection){
					        weForm.focus();
					        document.selection.createRange().text=tagText;
					        document.selection.createRange().select();
					}else if (weForm.selectionStart || weForm.selectionStart == "0"){
							intStart = weForm.selectionStart;
							intEnd = weForm.selectionEnd;
							weForm.value = (weForm.value).substring(0, intStart) + tagText + (weForm.value).substring(intEnd, weForm.value.length);
						    window.setTimeout("scrollToPosition();",50);
							weForm.focus();
						    weForm.selectionStart = eval(intStart+tagText.length);
						    weForm.selectionEnd = eval(intStart+tagText.length);
						}else{
							weForm.value += tagText;
						}
				}
			}

			function selectTagGroup(groupname) {

				if(groupname == "snippet_custom") {
					document.getElementById(\'codesnippet_standard\').style.display = \'none\';
					document.getElementById(\'tagSelection\').style.display = \'none\';
					document.getElementById(\'codesnippet_custom\').style.display = \'block\';

				} else if(groupname == "snippet_standard") {
					document.getElementById(\'codesnippet_custom\').style.display = \'none\';
					document.getElementById(\'tagSelection\').style.display = \'none\';
					document.getElementById(\'codesnippet_standard\').style.display = \'block\';

				} else if (groupname != "-1") {
					document.getElementById(\'codesnippet_custom\').style.display = \'none\';
					document.getElementById(\'codesnippet_standard\').style.display = \'none\';
					document.getElementById(\'tagSelection\').style.display = \'block\';
					elem = document.getElementById("tagSelection");

					for(var i=(elem.options.length-1); i>=0;i--) {
						elem.options[i] = null;
					}

					for (var i=0; i<tagGroups[groupname].length; i++) {
						elem.options[i] = new Option(tagGroups[groupname][i],tagGroups[groupname][i]);
					}
				}
			}

			' . $groupJs . '
			function openTagWizWithReturn (Ereignis) {
				if (!Ereignis)
				Ereignis = window.event;
				if (Ereignis.which) {
				Tastencode = Ereignis.which;
				} else if (Ereignis.keyCode) {
				Tastencode = Ereignis.keyCode;
				}
				if (Tastencode==13) edit_wetag(document.getElementById("tagSelection").value);
				//return false;
			}') .
					'
		<table id="wizardTable" style="width: 700px;" class="defaultfont" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="right">' . $groupselect . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(5, 5) . '</td>
		</tr>
		<tr>
			<td id="tagSelectCol" style="width: 250px;">' . $tagselect . $CodeWizard->getSelect() . $CodeWizard->getSelect('custom') . '</td>
			<td id="spacerCol" style="width: 50px;" align="center">' . $editTagbut . '</td>
			<td id="tagAreaCol" style="width: 100%;" align="right">' . we_html_element::htmlTextArea(array('name' => 'we_' . $we_doc->Name . '_TagWizardCode', 'id' => 'tag_edit_area', 'style' => 'width:400px; height:100px;' . (($_SESSION["prefs"]["editorFont"] == 1) ? " font-family: " . $_SESSION["prefs"]["editorFontname"] . "; font-size: " . $_SESSION["prefs"]["editorFontsize"] . "px;" : ""), 'class' => 'defaultfont'), $we_doc->TagWizardCode) . '</td>
		</tr>
		<tr>
			<td>' . we_html_tools::getPixel(5, 5) . '</td>
		</tr>
	</table>
	<table id="wizardTableButtons" class="defaultfont" border="0" cellpadding="0" cellspacing="0" >
		<tr>
			<td id="tagSelectColButtons" style="width: 250px;"></td>
			<td id="spacerColButtons" style="width: 50px;"></td>
			<td id="tagAreaColButtons" style="width: 100%;" align="right">
				<table border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td style="padding-right:10px;">' . $selectallbut . '</td>
					<td style="padding-right:10px;">' . $prependbut . '</td>
					<td style="padding-right:10px;">' . $appendbut . '</td>
					<td>' . $addCursorPositionbut . '</td>
				</table>
			</td>
		</tr>
	</table>';

				$parts[] = array("headline" => "", "html" => $tagWizardHtml, "space" => 0);
				$wepos = weGetCookieVariable("but_weTMPLDocEdit");
				$znr = 1;
			}
			print we_multiIconBox::getJS() .
				'<div id="bodydiv"' . ($_SESSION['prefs']['editorMode'] == 'java' ? '' : 'style="display:none;"') . '>' . we_multiIconBox::getHTML("weTMPLDocEdit", "100%", $parts, 20, "", $znr, g_l('weClass', "[showTagwizard]"), g_l('weClass', "[hideTagwizard]"), ($wepos == "down"), "", 'toggleTagWizard();') . '</div>';
?></body>

<?php
if(isset($selectedGroup)){
	echo we_html_element::jsElement("selectTagGroup('$selectedGroup');");
}
?>
</html>