<?php
/**
 * webEdition CMS
 *
 * $Rev: 5321 $
 * $Author: mokraemer $
 * $Date: 2012-12-05 19:24:10 +0100 (Wed, 05 Dec 2012) $
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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

we_html_tools::protect();

// generate ContentType JS-String
$_contentTypes = 'var _Contentypes = new Object();
	_Contentypes["cockpit"] = "icon_cockpit.gif";';
$ct = we_base_ContentTypes::inst();
foreach($ct->getContentTypes() as $ctype){

	$_contentTypes .= '_Contentypes["' . $ctype . '"] = "' . $ct->getIcon($ctype) . '";';
}

/*
 * Browser dependences
 */
$tabContainerMargin = "0px";
$browser = we_base_browserDetect::inst();
switch($browser->getBrowser()){
	case we_base_browserDetect::SAFARI:
		$heightPlus = "";
		$textvalign = "top";
		$imgmargintop = 2;
		$imgvalign = "top";
		$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)"><nobr><span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" width="16" height="16" id="###loadId###" title="" class="status" style="background-position:0px -1px" />&nbsp;</span><span id="###tabTextId###" class="text"></span><span class="spacer"><img src="' . IMAGE_DIR . 'pixel.gif" width="5" height="16" id="###modId###" class="status" /><img src="' . IMAGE_DIR . 'multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span><img src="' . IMAGE_DIR . 'multiTabs/tabBorder.gif" height="21" style="vertical-align:bottom;" /></nobr><span><img src="' . IMAGE_DIR . 'pixel.gif" height="0" /></span></div>';
		$tabBorder = "border:0px;";
		$tabBG = "";
		break;
	case we_base_browserDetect::IE:
		$heightPlus = "";
		$textvalign = "middle";
		$imgmargintop = 0;
		$imgvalign = "middle";
		$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)"><nobr>&nbsp;<span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" width="16" height="16" id="###loadId###" title="" class="status" style="background-position:0px -1px" />&nbsp;</span><span id="###tabTextId###" class="text"></span><span class="spacer"><img src="' . IMAGE_DIR . 'pixel.gif" width="5" height="16" id="###modId###" class="status" /><img src="' . IMAGE_DIR . 'multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span><img src="' . IMAGE_DIR . 'multiTabs/tabBorder.gif" height="21" style="vertical-align:bottom;" /></nobr></div>';
		$tabBorder = "border:0px;";
		$tabBG = "background-position:bottom";
		break;
	default:
		$heightPlus = "+1";
		$textvalign = "top";
		$imgmargintop = 2;
		$imgvalign = "top";
		$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)"><nobr>&nbsp;<span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" width="16" height="16" id="###loadId###" title="" class="status" style="background-position:0px -1px" />&nbsp;</span><span id="###tabTextId###" class="text"></span><span class="spacer"><img src="' . IMAGE_DIR . 'pixel.gif" width="5" height="16" id="###modId###" class="status" /><img src="/webEdition/images/multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span></nobr>
		</div>';
		$tabBorder = "border: 0px; border-bottom: 1px solid #888888; border-right: 1px solid #888888;";
		$tabBG = "";
		switch($browser->getSystem()){
			case we_base_browserDetect::SYS_MAC:
				if($browser->isFF() && $browser->getBrowserVersion() < 3){
					$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" ondblclick=";" onclick="top.weMultiTabs.selectFrame(this)"><nobr><span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" width="16" height="16" id="###loadId###" title="" class="status" style="background-position:0px -1px" />&nbsp;</span><span id="###tabTextId###" class="text"></span><span class="spacer"><img src="' . IMAGE_DIR . 'pixel.gif" width="5" height="16" id="###modId###" class="status" /><img src="/webEdition/images/multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span><img src="' . IMAGE_DIR . 'multiTabs/tabBorder.gif" height="21" style="vertical-align:bottom;" /></nobr></div>';
				} else{
					$tabContainerMargin = "0px";
				}
				$tabBorder = "border: 0px; border-bottom: 0px solid #888888; border-right: 1px solid #888888;";
				break;
			case we_base_browserDetect::SYS_UNIX:
				if($browser->isFF() && $browser->getBrowserVersion() < 3){
					$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)"><nobr><span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" width="16" height="16" id="###loadId###" title="" class="status" />&nbsp;</span><span id="###tabTextId###" class="text" style="background-position:0px -1px"></span><span class="spacer"><img src="' . IMAGE_DIR . 'pixel.gif" width="5" height="16" id="###modId###" class="status" /><img src="/webEdition/images/multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span><img src="' . IMAGE_DIR . 'multiTabs/tabBorder.gif" height="21" style="vertical-align:bottom;" /></nobr></div>';
				} else{
					$tabContainerMargin = "0px";
				}
				$tabBorder = "border:0px;";
				break;
			default:
				if($browser->isFF() && $browser->getBrowserVersion() < 3){

				} else{
					$tabContainerMargin = "-1px";
				}
		}
}
$frameDefaultHeight = 22;

we_html_tools::htmlTop();
print we_html_element::jsElement($content = $_contentTypes);
?>
<script type="text/javascript"><!--
	function _getIcon(contentType, extension) {
		if (contentType == "application/*") {
			switch(extension.toLowerCase()){
				case '.pdf' :
					return 'pdf.gif';
				case '.zip' :
				case '.sit' :
				case '.hqx' :
				case '.bin' :
					return 'zip.gif';
				case '.odt':
				case '.ott':
				case '.dot' :
				case '.doc' :
					return 'word.gif';
				case '.ods':
				case '.ots':
				case '.xlt' :
				case '.xls' :
					return 'excel.gif';
				case '.odp':
				case '.otp':
				case '.ppt' :
					return 'powerpoint.gif';
				case '.odg':
				case '.otg':
					return 'odg.gif';
			}
			return "prog.gif";

		} else {
			tmp=_Contentypes[contentType];
			if(tmp==undefined){
				return "prog.gif";
			}
			return _Contentypes[contentType];
		}
	}

	// fits the frame height on resize, add or remove tabs if the tabs wrap
	function setFrameSize() {
		tabsHeight = (document.getElementById('tabContainer').clientHeight ? (document.getElementById('tabContainer').clientHeight <?php echo $heightPlus; ?>) : (document.body.clientHeight <?php echo $heightPlus; ?> ) );
		tabsHeight = tabsHeight < <?php echo $frameDefaultHeight; ?> ? <?php echo $frameDefaultHeight; ?> : tabsHeight;
		parent.document.getElementById('multiEditorDocumentTabsFrameDiv').style.height = tabsHeight+"px";
		parent.document.getElementById('multiEditorEditorFramesetsDiv').style.top = tabsHeight+"px";
	}

	/**
	 * class declaration
	 * the class TabView controls the behaviort of the tabs
	 * onload a instance of this class is created
	 */
	TabView = function(myDoc) {
		this.myDoc = myDoc;
		this.init();
	}
	/**
	 * class TabView methods and properties
	 */
	TabView.prototype = {
		/**
		 * if a tab for the given frameId exists, it will be selected
		 * if not if will be added
		 */
		openTab: function(frameId,text,title) {
			if(this.myDoc.getElementById("tab_"+frameId)==undefined) {
				this.addTab(frameId,text,title);
			} else {
				this.selectTab(frameId);
			}
		},
		/**
		 * adds an new tab to the tab view
		 */
		addTab: function(frameId,text,title){
			newtab = this.tabDummy.cloneNode(true);
			newtab.innerHTML = newtab.innerHTML.replace(/###tabTextId###/g, "text_"+frameId);
			newtab.innerHTML = newtab.innerHTML.replace(/###modId###/g, "mod_"+frameId);
			newtab.innerHTML = newtab.innerHTML.replace(/###loadId###/g, "load_"+frameId);
			newtab.innerHTML = newtab.innerHTML.replace(/###closeId###/g, "close_"+frameId);
			newtab.id        = "tab_" + frameId;
			newtab.name      = "tab";
			newtab.title     = title;
			newtab.className = "tabActive";
			this.tabContainer.appendChild(newtab);
			this.setText(frameId, text);
			this.setTitle(frameId, title);
			this.selectTab(frameId);
			setTimeout("setFrameSize()",100);
		},
		/**
		 * controls the click on the close button
		 */
		onCloseTab : function(val) {
			frameId = (typeof val) == "object" ? val.id.replace(/close_/g, "") : val;
			top.weEditorFrameController.closeDocument(frameId);

		},
		/**
		 * removes a tab from the tab view
		 */
		closeTab : function(frameId) {
			this.tabContainer.removeChild(this.myDoc.getElementById('tab_'+frameId));
			if (this.activeTab == frameId) this.activeTab = null;
			setFrameSize();
			this.contentType[frameId] = "";
		},
		/**
		 * selects a tab (set style for selected tabs)
		 */
		selectTab: function(frameId) {
			this.deselectAll();
			if(this.activeTab != null) {
				this.deselectTab(this.activeTab);
			}
			if( this.myDoc.getElementById('tab_' + frameId) && typeof(this.myDoc.getElementById('tab_' + frameId)) == "object" ) {
				this.myDoc.getElementById('tab_' + frameId).className = 'tabActive';
			}
			this.activeTab = frameId;
		},
		/**
		 * deselects a tab (set style for deselected tabs)
		 */
		deselectTab: function(frameId) {
			if (this.myDoc.getElementById('tab_' + frameId)) {
				this.myDoc.getElementById('tab_' + frameId).className = "tab";
			}
		},
		/**
		 * deselects all tab (set style for deselected tabs to all tabs)
		 */
		deselectAll: function() {
			tabs = this.myDoc.getElementsByName("tab");
			for(i=0; tabs.length; i++) {
				tabs[i].className = "tab";
			}
		},
		/**
		 * sets the tab label
		 */
		setText: function(frameId, val) {
			this.myDoc.getElementById('text_' + frameId).innerHTML = val;
			setTimeout("setFrameSize()",50);
		},
		/**
		 * sets the tab title
		 */
		setTitle: function(frameId, val) {
			this.myDoc.getElementById('tab_' + frameId).title = val;
		},
		/**
		 * sets the id to the icon
		 */
		setId: function(frameId, val) {
			this.myDoc.getElementById('load_' + frameId).title = val;
		},
		/**
		 * marks a tab as modified an not safed
		 */
		setModified: function(frameId, modified) {
			if(modified) {
				this.myDoc.getElementById('mod_' + frameId).src = "<?php echo IMAGE_DIR; ?>multiTabs/modified.gif";
			} else {
				this.myDoc.getElementById('mod_' + frameId).src = "<?php echo IMAGE_DIR; ?>pixel.gif";
			}
		},
		/**
		 * displays the loading loading icon
		 */
		setLoading: function(frameId, loading) {
			if(loading) {
				this.myDoc.getElementById('load_' + frameId).style.backgroundImage = "url(<?php echo IMAGE_DIR; ?>spinner.gif)";
			} else {



				if ( _Contentypes[this.contentType[frameId]]) {
					var _text = this.myDoc.getElementById('text_' + frameId).innerHTML;
					var _ext = _text ? _text.replace(/^.*\./,".") : "";
					this.myDoc.getElementById('load_' + frameId).style.backgroundImage = "url(<?php echo ICON_DIR; ?>" + _getIcon(this.contentType[frameId], _ext) + ")";
				} else {
					this.myDoc.getElementById('load_' + frameId).style.backgroundImage = "url(<?php echo IMAGE_DIR; ?>pixel.gif)";
				}
			}
		},
		/**
		 * displays the content type icon
		 */
		setContentType: function(frameId,contentType) {
			this.contentType[frameId] = contentType;
			this.setLoading(frameId,false);
		},
		/**
		 * controls the click on a tab
		 */
		selectFrame: function(val) {
			frameId = (typeof val) == "object" ? val.id.replace(/tab_/g, "") : val;
			top.weEditorFrameController.showEditor(frameId);
			//this.selectTab(frameId);
		},
		/**
		 * inits some vars
		 */
		init: function() {
			this.tabs = new Array();
			this.frames = new Array();
			this.activeTab = null;
			this.tabContainer = this.myDoc.getElementById('tabContainer');
			this.tabDummy = this.myDoc.getElementById('tabDummy');
			this.contentType = new Array();
		}
	}
	/**
	 * document init
	 */
	function init() {
		top.weMultiTabs = new TabView(document);
	}
	//-->
</script>
<style type="text/css">
	body {
		margin:0px; padding:0px;
		border: 0px; border-top: 1px solid #000000;
		font-family: Verdana, Arial, sans-serif;
		font-size: 10px;
		color: #000000;
		background-color: silver;
		background-image: url(<?php echo IMAGE_DIR; ?>backgrounds/multitabBG.gif);
	}
	#tabContainer{
		width: 100%;
		margin: <?php echo $tabContainerMargin; ?>; padding: 0px;
		border: 0px;
		overflow:hidden;
	}
	div.tab{
		margin: 0px; padding: 0px;
		<?php echo $tabBorder; ?>
		display:inline-block;
		background-image:url(<?php echo IMAGE_DIR; ?>multiTabs/tabsBG_normal.gif);
		background-repeat: repeat-x;
		line-height:21px;
		font-size:17px;
		cursor:pointer;
	}
	div.tabOver{
		margin: 0px; padding: 0px;
		<?php echo $tabBorder; ?>
		display:inline-block;
		background-image:url(<?php echo IMAGE_DIR; ?>multiTabs/tabsBG_over.gif);
		background-repeat: repeat-x;
		<?php echo $tabBG; ?>
		line-height:21px;
		font-size:17px;
		cursor:pointer;
	}
	div.tabActive{
		margin: 0px; padding: 0px;
		<?php echo $tabBorder; ?>
		display: inline-block;
		background-image:url(<?php echo IMAGE_DIR; ?>multiTabs/tabsBG_active.gif);
		background-repeat: repeat-x;
		<?php echo $tabBG; ?>
		line-height:21px;
		font-size:17px;
		cursor:pointer;
	}
	span.text{
		margin:0px; padding:0px;
		font-size: 10px;
		vertical-align:<?php echo $textvalign; ?>;
	}
	span.spacer{
		font-size: 4px;
		vertical-align:<?php echo $textvalign; ?>;
	}
	img.close{
		vertical-align:<?php echo $imgvalign; ?>;
		cursor:pointer;
		margin:<?php echo $imgmargintop; ?>px;
	}
	span.status{
		vertical-align:<?php echo $imgvalign; ?>;
		padding:0px;
	}
	img.status{
		vertical-align:<?php echo $imgvalign; ?>;
		padding:0px;
		margin:<?php echo $imgmargintop; ?>px;
	}
	.hidden{
		display: none;
	}
	.visible{
		display: inline;
	}
</style>
</head>
<body style="background-color: Silver;" onresize="setFrameSize()" onload="init()">
	<div id="tabContainer" name="tabContainer">
	</div>
	<?php echo $tabDummy; ?>
</body>
</html>