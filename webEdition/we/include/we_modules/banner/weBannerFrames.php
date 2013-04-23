<?php

/**
 * webEdition CMS
 *
 * $Rev: 5746 $
 * $Author: mokraemer $
 * $Date: 2013-02-07 01:04:25 +0100 (Thu, 07 Feb 2013) $
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
class weBannerFrames extends weModuleBannerFrames{

	var $edit_cmd = "edit_banner";

	function __construct(){
		parent::__construct("banner/edit_banner_frameset.php");
		$this->View = new weBannerView();
	}

	function getHTMLFrameset(){
		$this->getJSTreeCode();
		$this->getJSCmdCode();
		?>
		</head>
		<frameset rows="32,*,0" framespacing="0" border="0" frameborder="NO" onLoad="start();">
			<frame src="<?php print WEBEDITION_DIR . "we/include/we_modules/banner/"; ?>edit_banner_header.php" name="header" scrolling="NO" noresize>
			<frame src="<?php print WEBEDITION_DIR . "we/include/we_modules/" . $this->frameset; ?>?pnt=resize" name="resize" scrolling="NO" noresize>
			<frame src="<?php print WEBEDITION_DIR . "we/include/we_modules/" . $this->frameset; ?>?pnt=cmd" name="cmd" scrolling="NO" noresize>
		</frameset>

		<body>
		</body>
		</html>
		<?php
	}

	function getJSTreeCode(){
		$startloc = 0;

		$out = '
		function loadData(){
			menuDaten.clear();
			startloc=' . $startloc . ';';

		$this->db->query('SELECT ID,ParentID,Path,Text,Icon,IsFolder,ABS(text) as Nr, (text REGEXP "^[0-9]") as isNr FROM ' . BANNER_TABLE . ' ORDER BY isNr DESC,Nr,Text');
		while($this->db->next_record()) {
			$ID = $this->db->f("ID");
			$ParentID = $this->db->f("ParentID");
			$Path = $this->db->f("Path");
			$Text = addslashes($this->db->f("Text"));
			$Icon = $this->db->f("Icon");
			$IsFolder = $this->db->f("IsFolder");

			$out.=($IsFolder ?
					"  menuDaten.add(new dirEntry('" . $Icon . "','" . $ID . "','" . $ParentID . "','" . $Text . "',0,'folder','" . BANNER_TABLE . "',1));" :
					"  menuDaten.add(new urlEntry('" . $Icon . "','" . $ID . "','" . $ParentID . "','" . $Text . "','file','" . BANNER_TABLE . "',1));");
		}

		$out.='}';
		print parent::getJSTreeCode() . we_html_element::jsElement($out);
	}

	function getJSCmdCode(){
		print $this->View->getJSTopCode();
	}

	function getHTMLEditorHeader($mode = 0){
		if(isset($_REQUEST["home"])){
			return '<body bgcolor="#F0EFF0"></body></html>';
		}
		$isFolder = 0;
		if(isset($_GET["isFolder"]))
			$isFolder = $_GET["isFolder"];

		$page = 0;
		if(isset($_GET["page"]))
			$page = $_GET["page"];


		$headline1 = ($isFolder == 1) ? g_l('modules_banner', '[group]') : g_l('modules_banner', '[banner]');
		$text = "" . ($isFolder == 1) ? g_l('modules_banner', '[newbannergroup]') : g_l('modules_banner', '[newbanner]');
		if(isset($_GET["txt"]))
			$text = $_GET["txt"];


		$we_tabs = new we_tabs();

		if($isFolder == 0){

			$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][properties]"), ($page == 0 ? "TAB_ACTIVE" : "TAB_NORMAL"), "setTab(0);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][placement]"), ($page == 1 ? "TAB_ACTIVE" : "TAB_NORMAL"), "setTab(1);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][statistics]"), ($page == 2 ? "TAB_ACTIVE" : "TAB_NORMAL"), "setTab(2);"));
		} else{

			$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][properties]"), "TAB_ACTIVE", "setTab(0);"));
		}

		$we_tabs->onResize('header');
		$tab_head = $we_tabs->getHeader();

		$tab_body = $we_tabs->getJS();

		$out =
			$tab_head .
			we_html_element::jsElement('
	function setTab(tab){
		switch(tab){
			case ' . weBanner::PAGE_PROPERTY . ':
			case ' . weBanner::PAGE_PLACEMENT . ':
			case ' . weBanner::PAGE_STATISTICS . ':
				top.content.resize.right.editor.edbody.we_cmd("switchPage",tab);
				break;
		}
	}
   top.content.hloaded=1;') . '
	<body bgcolor="white" background="' . IMAGE_DIR . 'backgrounds/header_with_black_line.gif" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onload="setFrameSize()" onresize="setFrameSize()">
		<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", $headline1) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $text) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) .
			$we_tabs->getHTML() .
			'		</div>
	</body>';

		return $out;
	}

	function getHTMLEditorBody(){
		return $this->View->getProperties();
	}

	function getHTMLEditorFooter($mode = 0){
		if(isset($_REQUEST["home"])){
			return '<body bgcolor="#F0EFF0"></body></html>';
		}
		$this->View->getJSFooterCode();
		?>
		<script type="text/javascript"><!--
			function sprintf(){
				if (!arguments || arguments.length < 1) return;

				var argum = arguments[0];
				var regex = /([^%]*)%(%|d|s)(.*)/;
				var arr = new Array();
				var iterator = 0;
				var matches = 0;

				while (arr=regex.exec(argum)){
					var left = arr[1];
					var type = arr[2];
					var right = arr[3];

					matches++;
					iterator++;

					var replace = arguments[iterator];

					if (type=='d') replace = parseInt(param) ? parseInt(param) : 0;
					else if (type=='s') replace = arguments[iterator];
					argum = left + replace + right;
				}
				return argum;
			}

			function we_save() {
				var acLoopCount=0;
				var acIsRunning = false;
				if(!!top.content.resize.right.editor.edbody.YAHOO && !!top.content.resize.right.editor.edbody.YAHOO.autocoml){
					while(acLoopCount<20 && top.content.resize.right.editor.edbody.YAHOO.autocoml.isRunnigProcess()){
						acLoopCount++;
						acIsRunning = true;
						setTimeout('we_save()',100);
					}
					if(!acIsRunning) {
						if(top.content.resize.right.editor.edbody.YAHOO.autocoml.isValid()) {
							_we_save();
						} else {
		<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
						}
					}
				} else {
					_we_save();
				}
			}

			function _we_save() {
				top.content.we_cmd('save_banner');
			}
			//-->
		</script>
		</head>
		<body bgcolor="white" background="<?php echo IMAGE_DIR; ?>edit/editfooterback.gif" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
			<form name="we_form">
				<table border="0" cellpadding="0" cellspacing="0" width="3000">
					<tr>
						<td valign="top" colspan="2"><?php print we_html_tools::getPixel(1600, 10) ?></td>
					</tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="0">
					<tr valign="middle">
						<td nowrap><?php print we_html_tools::getPixel(15, 5); ?></td>
						<td><?php print we_button::create_button("save", "javascript:we_save();"); ?></td>
					</tr>
				</table>
			</form>
		</body>
		</html>
		<?php
	}

	function getHTMLCmd(){
		$this->View->getJSCmd();
		?>
		</head>
		<body>
			<form name="we_form"><?php
		print $this->View->htmlHidden("ncmd", "") .
			$this->View->htmlHidden("nopt", "");
		?>
			</form>
		</body>
		</html>
		<?php
	}

	function getHTMLDCheck(){
		print we_html_element::jsElement('self.focus();') . '
		</head>
		<body>' .
			$this->View->getHTMLDCheck() . '
		</body>
		</html>';
	}

}