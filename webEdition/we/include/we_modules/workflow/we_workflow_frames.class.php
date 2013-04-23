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
class we_workflow_frames extends we_workflow_moduleFrames{

	function __construct(){
		parent::__construct();
		$this->View = new we_workflow_view();
	}

	function getHTMLFrameset(){
		$this->getJSTreeCode();
		$this->getJSCmdCode();
		?>
		</head>
		<frameset rows="32,*,0" framespacing="0" border="0" frameborder="NO" onLoad="start();">
			<frame src="<?php print WE_WORKFLOW_MODULE_DIR; ?>edit_workflow_header.php" name="header" scrolling=no noresize>
			<frame src="<?php print WE_WORKFLOW_MODULE_DIR; ?>edit_workflow_frameset.php?pnt=resize" name="resize" scrolling=no>
			<frame src="<?php print WE_WORKFLOW_MODULE_DIR; ?>edit_workflow_frameset.php?pnt=cmd" name="cmd" scrolling=no noresize>
		</frameset><noframes></noframes>

		<body background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif" style="background-color:#bfbfbf; background-repeat:repeat;margin:0px 0px 0px 0px">
		</body>
		</html>
		<?php
	}

	function getJSTreeCode(){
		$db_tmp = new DB_WE();
		$db_tmp1 = new DB_WE();
		$out = parent::getJSTreeCode();

		$out.='
		 <script  type="text/javascript">
		function loadData(){
			menuDaten.clear();';

		$startloc = 0;

		$out.="startloc=" . $startloc . ";\n";
		$this->db->query("SELECT * FROM " . WORKFLOW_TABLE . " ORDER BY Text ASC");
		while($this->db->next_record()) {
			$this->View->workflowDef = new we_workflow_workflow();
			$this->View->workflowDef->load($this->db->f("ID"));
			$out.="  menuDaten.add(new dirEntry('folder','" . $this->View->workflowDef->ID . "','0','" . oldHtmlspecialchars(addslashes($this->View->workflowDef->Text)) . "',false,'folder','workflowDef','" . $this->View->workflowDef->Status . "'));\n";

			foreach($this->View->workflowDef->documents as $k => $v){
				$out.="  menuDaten.add(new urlEntry('" . $v["Icon"] . "','" . $v["ID"] . "','" . $this->View->workflowDef->ID . "','" . oldHtmlspecialchars(addslashes($v["Text"])) . "','file','" . FILE_TABLE . "',1));\n";
			}
		}

		$out.='}
			</script>';
		print $out;
	}

	function getJSCmdCode(){
		print $this->View->getJSTopCode();
	}

	function getHTMLEditorHeader($mode = 0){
		if(isset($_REQUEST["home"])){
			return '<body bgcolor="#F0EFF0"></body></html>';
		}

		$page = (isset($_GET["page"]) ? $_GET["page"] : 0);

		$text = g_l('modules_workflow', '[new_workflow]');
		if(isset($_GET["txt"])){
			$text = $_GET["txt"];
		}

		$we_tabs = new we_tabs();

		if($mode == 0){

			$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][properties]"), "TAB_NORMAL", "setTab(0);", array("id" => "tab_0")));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', "[module][overview]"), "TAB_NORMAL", "setTab(1);", array("id" => "tab_1")));
		} else{
			$we_tabs->addTab(new we_tab("#", g_l('tabs', "[editor][information]"), "TAB_ACTIVE", "//", array("id" => "tab_0")));
		}

		$we_tabs->onResize();
		$tab_header = $we_tabs->getHeader('', 22);
		$tab_body = $we_tabs->getJS();
		if(empty($page))
			$page = 0;
		$textPre = ($mode == 1 ? g_l('modules_workflow', '[document]') : g_l('modules_workflow', '[workflow]'));
		$textPost = "/" . $text;

		$out = we_html_element::jsElement('
    function setTab(tab){
        	switch(tab){
			case 0:
				top.content.resize.right.editor.edbody.we_cmd("switchPage",0);
			break;
			case 1:
				top.content.resize.right.editor.edbody.we_cmd("switchPage",1);
			break;
		}
	}

   top.content.hloaded=1;') .
			$tab_header . '
   </head>
   <body bgcolor="white" background="' . IMAGE_DIR . 'backgrounds/header_with_black_line.gif" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onload="setFrameSize()", onresize="setFrameSize()">
		<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . oldHtmlspecialchars($textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) .
			$we_tabs->getHTML() .
			'</div>' . we_html_element::jsElement('document.getElementById("tab_' . $page . '").className="tabActive";') . '
	</body>';


		return $out;
	}

	function getHTMLEditorBody(){
		return $this->View->getProperties();
	}

	function getHTMLEditorFooter($mode = 0){
		if(isset($_REQUEST["home"])){
			return '<body bgcolor="#EFF0EF"></body></html>';
		}
		?>

		<script  type="text/javascript">
			function setStatusCheck(){
				var a=document.we_form._status_workflow;
				var b;
				if(top.content.resize.right.editor.edbody.loaded) b=top.content.resize.right.editor.edbody.getStatusContol();
				else setTimeout("setStatusCheck()",100);

				if(b==1) a.checked=true;
				else a.checked=false;

			}
			function we_save() {
				top.content.we_cmd('save_workflow');

			}
		</script>
		</head>
		<body bgcolor="white" background="<?php echo IMAGE_DIR;?>edit/editfooterback.gif" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0"<?php if($mode == 0){ ?> onLoad="setStatusCheck()"<?php } ?>>
			<form name="we_form">
				<table border="0" cellpadding="0" cellspacing="0" width="3000">
					<tr>
						<td valign="top" colspan="2"><?php print we_html_tools::getPixel(1600, 10) ?></td>
					</tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="0" width="300">
					<?php if($mode == 0){ ?>
						<tr>
							<td><?php print we_html_tools::getPixel(15, 5) ?></td>
							<td><?php print we_button::create_button("save", "javascript:we_save();") ?></td>
							<td class="defaultfont"><?php print $this->View->getStatusHTML(); ?></td>
						</tr>
					<?php } ?>
				</table>
			</form>
		</body>
		</html>
		<?php
	}

	function getHTMLLog($docID, $type = 0){
		print we_html_element::jsElement('self.focus();').'
		</head>
		<body class="weDialogBody">'.
		we_workflow_view::getLogForDocument($docID, $type).
		'</body></html>';
	}

	function getHTMLCmd(){
		$this->View->getCmdJS();
		?>
		</head>
		<body>
			<form name="we_form">
				<?php
				print $this->View->htmlHidden("wcmd", "");
				print $this->View->htmlHidden("wopt", "");
				?>
			</form>
		</body>
		</html>
		<?php
	}

	function getHTMLLogQuestion(){
		?>
		</head>
		<body class="weDialogBody">
			<form name="we_form">
				<?php print $this->View->getLogQuestion(); ?>
			</form>
		</body>
		</html>
		<?php
	}

}