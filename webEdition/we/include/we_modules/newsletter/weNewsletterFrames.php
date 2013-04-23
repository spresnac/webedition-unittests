<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
class weNewsletterFrames extends weModuleFrames{

	var $multibox_width = 950;
	var $def_width = 450;
	var $weAutoColpleter;

	function __construct(){
		parent::__construct(WE_NEWSLETTER_MODULE_DIR . "edit_newsletter_frameset.php");
		$this->View = new weNewsletterView();
		$this->View->setFrames("top.content", "top.content.resize.left.tree", "top.content.cmd");

		$this->Tree = new weNewsletterTree();
		$this->setupTree(NEWSLETTER_TABLE, "top.content", "top.content.resize.left.tree", "top.content.cmd");

		$this->module = "newsletter";
		$this->weAutoColpleter = & weSuggest::getInstance();
	}

	function getHTMLFrameset(){
		$js = we_html_element::jsElement('
			var hot = 0;
			var scrollToVal = 0;
		');

		$frameset = weModuleFrames::getHTMLFrameset();

		$body = we_html_element::htmlBody(array("bgcolor" => "#bfbfbf", "background" => IMAGE_DIR . "backgrounds/aquaBackground.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0"), "");

		return $this->getHTMLDocument($frameset . $body, $js);
	}

	function getJSCmdCode(){
		print $this->View->getJSTopCode();
	}

	/**
	 * Modul Header
	 *
	 * @package weModules
	 * @subpackage Newsletter
	 * @param Integer $mode
	 * @return String
	 */
	function getHTMLEditorHeader($mode = 0){
		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$group = (isset($_REQUEST["group"]) ? $_REQUEST["group"] : 0);

		$page = ($group ? 0 : (isset($_REQUEST["page"]) ? $_REQUEST["page"] : 0));


		$textPre = g_l('modules_newsletter', ($group ? '[group]' : '[newsletter]'));

		$textPost = (isset($_REQUEST["txt"]) ?
				$_REQUEST["txt"] :
				g_l('modules_newsletter', ($group ? '[new_newsletter_group]' : '[new_newsletter]'))
			);

		$js = we_html_element::jsElement('
				function setTab(tab) {
					switch (tab) {
						case 0:
							top.content.resize.right.editor.edbody.we_cmd("switchPage",0);
							break;

						case 1:
							top.content.resize.right.editor.edbody.we_cmd("switchPage",1);
							break;

						case 2:
							top.content.resize.right.editor.edbody.we_cmd("switchPage",2);
							break;
					}

				}
				top.content.hloaded = 1;
		');

		$we_tabs = new we_tabs();

		$we_tabs->addTab(new we_tab("#", g_l('modules_newsletter', '[property]'), (($page == 0) ? "TAB_ACTIVE" : "TAB_NORMAL"), "self.setTab(0);"));

		if(!$group){
			$we_tabs->addTab(new we_tab("#", sprintf(g_l('modules_newsletter', '[mailing_list]'), ""), (($page == 1) ? "TAB_ACTIVE" : "TAB_NORMAL"), "self.setTab(1);"));
			$we_tabs->addTab(new we_tab("#", g_l('modules_newsletter', '[edit]'), (($page == 2) ? "TAB_ACTIVE" : "TAB_NORMAL"), "self.setTab(2);"));
		}

		$we_tabs->onResize('header');
		$tabHead = $we_tabs->getHeader() . $js;
		$tabBody = $we_tabs->getJS();

		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "backgrounds/header_with_black_line.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0", "onload" => "setFrameSize()", "onresize" => "setFrameSize()"), '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . oldHtmlspecialchars($textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) .
				$we_tabs->getHTML() .
				'</div>'
		);

		return $this->getHTMLDocument($body, $tabHead);
	}

	/**
	 * Modul Body
	 *
	 * @package weModules
	 * @subpackage Newsletter
	 * @return String
	 */
	function getHTMLEditorBody(){
		return $this->getHTMLProperties();
	}

	/**
	 * Modul Footer
	 *
	 * @package weModules
	 * @subpackage Newsletter
	 * @param Integer $mode
	 * @return String
	 */
	function getHTMLEditorFooter($mode = 0){
		if(isset($_REQUEST["home"])){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		$group = 0;
		if(isset($_REQUEST["group"])){
			$group = $_REQUEST["group"];
		}

		$js = $this->View->getJSFooterCode() .
			we_html_element::jsElement('
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

			function addGroup(text, val) {
			   ' . ($group ? '' : 'document.we_form.gview[document.we_form.gview.length] = new Option(text,val);' ) . '
			}

			function delGroup(val) {
			   document.we_form.gview[val] = null;
			}

			function populateGroups() {
				if (top.content.resize.right.editor.edbody.getGroupsNum) {

					if (top.content.resize.right.editor.edbody.loaded) {
						var num=top.content.resize.right.editor.edbody.getGroupsNum();

							if (!num) {
								num = 1;
							} else {
								num++;
							}

							addGroup(sprintf("' . g_l('modules_newsletter', '[all_list]') . '",0),0);

							for (i = 1; i < num; i++) {
								addGroup(sprintf("' . g_l('modules_newsletter', '[mailing_list]') . '",i),i);
							}
					} else {
						setTimeout("populateGroups()",100);
					}
				} else {
					setTimeout("populateGroups()",100);
				}
			}

			function we_save() {
			    setTimeout(\'top.content.we_cmd("save_newsletter")\',100);

			}');

		$select = new we_html_select(array("name" => "gview"));


		$table1 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "3000"), 1, 1);
		$table1->setCol(0, 0, array("nowrap" => null, "valign" => "top"), we_html_tools::getPixel(1600, 10));

		$table2 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "300"), 1, 10);
		if($mode == 0){
			$table2->setRow(0, array("valign" => "middle"));

			$table2->setCol(0, 0, array("nowrap" => null), we_html_tools::getPixel(15, 5));

			$table2->setCol(0, 1, array("nowrap" => null), ((we_hasPerm("NEW_NEWSLETTER") || we_hasPerm("EDIT_NEWSLETTER")) ?
					we_button::create_button("save", "javascript:we_save()") :
					""
				)
			);

			if(!$group){
				$table2->setCol(0, 2, array("nowrap" => null), we_html_tools::getPixel(70, 5));
				$table2->setCol(0, 3, array("nowrap" => null), $select->getHtml());
				$table2->setCol(0, 4, array("nowrap" => null), we_html_tools::getPixel(5, 5));
				$table2->setCol(0, 5, array("nowrap" => null), we_forms::checkbox(0, false, "htmlmail_check", g_l('modules_newsletter', '[html_preview]'), false, "defaultfont", "if(document.we_form.htmlmail_check.checked) { document.we_form.hm.value=1;top.opener.top.nlHTMLMail=1; } else { document.we_form.hm.value=0;top.opener.top.nlHTMLMail=0; }"));
				$table2->setCol(0, 6, array("nowrap" => null), we_html_tools::getPixel(5, 5));
				$table2->setCol(0, 7, array("nowrap" => null), we_button::create_button("preview", "javascript:we_cmd('popPreview')"));
				$table2->setCol(0, 8, array("nowrap" => null), we_html_tools::getPixel(5, 5));
				$table2->setCol(0, 9, array("nowrap" => null), (we_hasPerm("SEND_NEWSLETTER") ?
						we_button::create_button("send", "javascript:we_cmd('popSend')") :
						""
					)
				);
			}
		}

		$post_js = we_html_element::jsElement('
		if(typeof(self.document.we_form.htmlmail_check)!="undefined") {
			if(top.opener.top.nlHTMLMail) {
				self.document.we_form.htmlmail_check.checked = true;
				document.we_form.hm.value=1;
			} else {
				self.document.we_form.htmlmail_check.checked = false;
				document.we_form.hm.value=0;
			}
		}');

		$body = we_html_element::htmlBody(array("bgcolor" => "white", "background" => IMAGE_DIR . "edit/editfooterback.gif", "marginwidth" => "0", "marginheight" => "0", "leftmargin" => "0", "topmargin" => "0", "onload" => "setTimeout('populateGroups()',100)"), we_html_element::htmlForm(array(), we_html_element::htmlHidden(array("name" => "hm", "value" => "0")) .
					$table1->getHtml() .
					$table2->getHtml() .
					$post_js
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLLog(){
		$content = "";
		$this->View->db->query('SELECT * FROM ' . NEWSLETTER_LOG_TABLE . ' WHERE NewsletterID=' . $this->View->newsletter->ID . ' ORDER BY LogTime DESC');

		while($this->View->db->next_record()) {
			$log = g_l('modules_newsletter', '[' . $this->View->db->f("Log") . ']');
			$param = $this->View->db->f("Param");
			$content.=we_html_element::htmlDiv(array("class" => "defaultfont"), date(g_l('weEditorInfo', "[date_format_sec]"), $this->View->db->f("LogTime")) . '&nbsp;' . ($param ? sprintf($log, $param) : $log));
		}

		$js = we_html_element::jsElement("self.focus();");
		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_tools::htmlDialogLayout(
						we_html_element::htmlDiv(null, we_html_tools::getPixel(10, 5)) .
						we_html_element::htmlDiv(array("class" => "blockwrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;"), $content) .
						we_html_element::htmlDiv(null, we_html_tools::getPixel(10, 15)), g_l('modules_newsletter', '[show_log]'), we_button::create_button("close", "javascript:self.close();")
					)
				)
		);

		//we_html_element::htmlTextarea(array("cols"=>"65","rows"=>"30","name"=>"check_report"),
		//	$content
		//).

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLCmd(){
		if(isset($_REQUEST["pid"])){
			$pid = $_REQUEST["pid"];
		} else{
			exit;
		}

		$rootjs = "";
		if(!$pid){
			$rootjs.=
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(new ' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));';
		}

		$hiddens =
			we_html_element::htmlHidden(array("name" => "pnt", "value" => "cmd")) .
			we_html_element::htmlHidden(array("name" => "ncmd", "value" => "")) .
			we_html_element::htmlHidden(array("name" => "nopt", "value" => ""));

		return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "white", "marginwidth" => "10", "marginheight" => "10", "leftmargin" => "10", "topmargin" => "10"), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree(weNewsletterTreeLoader::getItems($pid)))
					)
				));
	}

	function getHTMLSendQuestion(){
		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onblur" => "self.focus", "onunload" => "doUnload()"), we_html_tools::htmlYesNoCancelDialog(g_l('modules_newsletter', '[continue_camp]'), IMAGE_DIR . "alert.gif", "ja", "nein", "abbrechen", "opener.yes();self.close();", "opener.no();self.close();", "opener.cancel();self.close();")
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLSaveQuestion1(){
		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onblur" => "self.focus", "onunload" => "doUnload()"), we_html_tools::htmlYesNoCancelDialog(g_l('modules_newsletter', '[ask_to_preserve]'), IMAGE_DIR . "alert.gif", "ja", "nein", "", "opener.document.we_form.ask.value=0;opener.we_cmd('save_newsletter');self.close();", "self.close();")
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLPrintLists(){
		print we_html_element::jsElement("self.focus();");

		$emails = array();
		$out = '';
		$count = count($this->View->newsletter->groups) + 1;

		$tab1 = "&nbsp;&nbsp;&nbsp;";
		$tab2 = $tab1 . $tab1;
		$tab3 = $tab1 . $tab1 . $tab1;
		$c = 0;
		for($k = 1; $k < $count; $k++){
			$out.=we_html_element::htmlBr() .
				we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(sprintf(g_l('modules_newsletter', '[mailing_list]'), $k)));
			$gc = 0;
			if(defined("CUSTOMER_TABLE")){
				$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . g_l('modules_newsletter', '[customers]'));
				$emails = $this->View->getEmails($k, 1, 1);

				foreach($emails as $email){
					$gc++;
					$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . $email);
				}
			}

			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . g_l('modules_newsletter', '[emails]'));

			$emails = $this->View->getEmails($k, 2, 1);
			foreach($emails as $email){
				$gc++;
				$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . $email);
			}

			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . g_l('modules_newsletter', '[file_email]'));

			$emails = $this->View->getEmails($k, 3, 1);
			foreach($emails as $email){
				$gc++;
				$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . $email);
			}
			$c+=$gc;
			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(sprintf(g_l('modules_newsletter', '[sum_group]'), $k) . ":" . $gc));
		}

		$out.=we_html_element::htmlBr() .
			we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[sum_all]') . ":" . $c)) .
			we_html_element::htmlBr();
		print '</head><body class="weDialogBody">' .
			we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "onload" => "self.focus()"), we_html_tools::htmlDialogLayout(
					we_html_element::htmlBr() .
					we_html_element::htmlDiv(array("class" => "blockwrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;"), $out) .
					we_html_element::htmlBr(), g_l('modules_newsletter', '[lists_overview]'), we_button::create_button("close", "javascript:self.close();")
				)
			) .
			'</body></html>';
		flush();
	}

	function getHTMLDCheck(){
		print we_html_element::jsElement("self.focus();");

		$tab1 = "&nbsp;&nbsp;&nbsp;";
		$tab2 = $tab1 . $tab1;
		$tab3 = $tab1 . $tab1 . $tab1;

		$emails = array();
		$count = count($this->View->newsletter->groups) + 1;

		$out = we_html_element::htmlBr() .
			we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[domain_check_begins]'))) .
			we_html_element::htmlBr();

		for($k = 1; $k < $count; $k++){

			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . sprintf(g_l('modules_newsletter', '[domain_check_list]'), $k));

			$emails = $this->View->getEmails($k, 0, 1);

			foreach($emails as $email){
				if($this->View->newsletter->check_email($email)){
					$domain = "";

					if(!$this->View->newsletter->check_domain($email, $domain)){
						$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . sprintf(g_l('modules_newsletter', '[domain_nok]'), $domain));
					}
				} else{
					$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . sprintf(g_l('modules_newsletter', '[email_malformed]'), $email));
				}
			}
		}
		$out.=we_html_element::htmlBr() .
			we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[domain_check_ends]'))) .
			we_html_element::htmlBr();
		print '</head><body class="weDialogBody">' .
			we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "onload" => "self.focus()"), we_html_tools::htmlDialogLayout(
					we_html_element::htmlBr() .
					we_html_element::htmlDiv(array("class" => "blockwrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;"), $out) .
					we_html_element::htmlBr(), g_l('modules_newsletter', '[lists_overview]'), we_button::create_button("close", "javascript:self.close();")
				)
			) .
			we_html_element::jsElement("self.focus();") .
			'</body></html>';
		flush();
	}

	function getHTMLSettings(){
		$settings = weNewsletterView::getSettings();

		$closeflag = false;

		if(isset($_REQUEST["ncmd"])){

			if($_REQUEST["ncmd"] == "save_settings"){
				$this->View->processCommands();
				$closeflag = true;
			}
		}


		$js = we_html_element::jsElement('
			self.focus();
		') . $this->View->getJSProperty();

		$texts = array('send_step', 'send_wait', 'test_account', 'default_sender', 'default_reply', weNewsletter::FEMALE_SALUTATION_FIELD, weNewsletter::MALE_SALUTATION_FIELD);
		$radios = array('reject_malformed', 'reject_not_verified', 'reject_save_malformed', 'log_sending', 'default_htmlmail', 'isEmbedImages', 'title_or_salutation', 'use_base_href', 'use_https_refer', 'use_port');
		$extra_radio_text = array('use_port');
		$defaults = array('reject_save_malformed' => '1', 'use_https_refer' => '0', 'send_wait' => '0', 'use_port' => '0', 'use_port_check' => '80', 'isEmbedImages' => '0', 'use_base_href' => '1');

		$table = new we_html_table(array('border' => '0', 'cellpadding' => '0', 'cellspacing' => '0'), 1, 3);
		$c = 0;

		foreach($texts as $text){

			if(!isset($settings[$text])){
				$this->View->putSetting($text, (isset($defaults[$text]) ? $defaults[$text] : "0"));
				$settings = weNewsletterView::getSettings();
			}

			$table->setCol($c, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[' . $text . ']') . ":&nbsp;");
			$table->setCol($c, 1, array(), we_html_tools::getPixel(5, 5));
			$table->setCol($c, 2, array("class" => "defaultfont"), we_html_tools::htmlTextInput($text, 40, $settings[$text], "", "", "text", "308"));

			$table->addRow();
			$c++;
			if($text == 'default_reply' || $text == weNewsletter::MALE_SALUTATION_FIELD){
				$table->setCol($c, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 10));
			} else{
				$table->setCol($c, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 3));
			}
			$c++;
			$table->addRow();
		}

		if(defined('CUSTOMER_TABLE')){
			$custfields = array();

			foreach($this->View->customers_fields as $fk => $fv){
				$custfields[$fv] = $fv;
			}

			$table->addRow(11);

			$table->setCol($c, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[customer_email_field]') . ":&nbsp;");
			$table->setCol($c, 1, array("class" => "defaultfont"), we_html_tools::getPixel(5, 5));
			$table->setCol($c, 2, array("class" => "defaultfont"), we_html_tools::htmlSelect("customer_email_field", $custfields, 1, $settings["customer_email_field"], false, '', "value", "308"));

			$table->setCol($c + 1, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 3));

			$table->setCol($c + 2, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_html_field]') . ':&nbsp;');
			$table->setCol($c + 2, 1, array('class' => 'defaultfont'), we_html_tools::getPixel(5, 5));
			$table->setCol($c + 2, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_html_field', $custfields, 1, $settings['customer_html_field'], false, '', 'value', '308'));

			$table->setCol($c + 3, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 3));

			$table->setCol($c + 4, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_salutation_field]') . ':&nbsp;');
			$table->setCol($c + 4, 1, array('class' => 'defaultfont'), we_html_tools::getPixel(5, 5));
			$table->setCol($c + 4, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_salutation_field', $custfields, 1, $settings['customer_salutation_field'], false, '', 'value', '308'));

			$table->setCol($c + 5, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 3));

			$table->setCol($c + 6, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_title_field]') . ':&nbsp;');
			$table->setCol($c + 6, 1, array('class' => 'defaultfont'), we_html_tools::getPixel(5, 5));
			$table->setCol($c + 6, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_title_field', $custfields, 1, $settings['customer_title_field'], false, '', 'value', '308'));

			$table->setCol($c + 7, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 3));

			$table->setCol($c + 8, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_firstname_field]') . ':&nbsp;');
			$table->setCol($c + 8, 1, array('class' => 'defaultfont'), we_html_tools::getPixel(5, 5));
			$table->setCol($c + 8, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_firstname_field', $custfields, 1, $settings['customer_firstname_field'], false, '', 'value', '308'));

			$table->setCol($c + 9, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 3));

			$table->setCol($c + 10, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_lastname_field]') . ':&nbsp;');
			$table->setCol($c + 10, 1, array('class' => 'defaultfont'), we_html_tools::getPixel(5, 5));
			$table->setCol($c + 10, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_lastname_field', $custfields, 1, $settings['customer_lastname_field'], false, '', 'value', '308'));

			$table->setCol($c + 11, 0, array('colspan' => '3'), we_html_tools::getPixel(5, 3));
		}

		$close = we_button::create_button('close', 'javascript:self.close();');
		$save = we_button::create_button('save', "javascript:we_cmd('save_settings')");

		$radios_code = '';
		foreach($radios as $radio){
			if(!isset($settings[$radio])){

				$this->View->putSetting($radio, (isset($defaults[$radio]) ? $defaults[$radio] : "1"));
				$settings = weNewsletterView::getSettings();
			}
			if(in_array($radio, $extra_radio_text)){
				$radios_code.= we_forms::checkbox($settings[$radio], (($settings[$radio] > 0) ? true : false), $radio . "_check", g_l('modules_newsletter', '[' . $radio . "_check]"), false, "defaultfont", "if(document.we_form." . $radio . "_check.checked) document.we_form." . $radio . ".value=" . (isset($defaults[$radio . "_check"]) ? $defaults[$radio . "_check"] : "0") . "; else document.we_form." . $radio . ".value=0;");

				$radio_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 1, 4);
				$radio_table->setCol(0, 0, array("class" => "defaultfont"), we_html_tools::getPixel(25, 5));
				$radio_table->setCol(0, 1, array("class" => "defaultfont"), oldHtmlspecialchars(g_l('modules_newsletter', '[' . $radio . ']')) . ":&nbsp;");
				$radio_table->setCol(0, 2, array(), we_html_tools::getPixel(5, 5));
				$radio_table->setCol(0, 3, array("class" => "defaultfont"), we_html_tools::htmlTextInput($radio, 5, $settings[$radio], "", "OnChange='if(document.we_form." . $radio . ".value!=0) document.we_form." . $radio . "_check.checked=true; else document.we_form." . $radio . "_check.checked=false;'"));
				$radios_code.=$radio_table->getHtml();
			} else{
				$radios_code.=we_forms::checkbox($settings[$radio], (($settings[$radio] == 1) ? true : false), $radio, oldHtmlspecialchars(g_l('modules_newsletter', '[' . $radio . ']')), false, "defaultfont", "if(document.we_form." . $radio . ".checked) document.we_form." . $radio . ".value=1; else document.we_form." . $radio . ".value=0;");
			}
		}

		$deselect = we_button::create_button("image:btn_function_trash", "javascript:document.we_form.global_mailing_list.value=''");

		$gml_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => "538"), 4, 2);
		$gml_table->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[global_mailing_list]'));
		$gml_table->setCol(1, 0, array(), we_html_tools::getPixel(5, 5));
		$gml_table->setCol(2, 0, array(), $this->View->formFileChooser("380", "global_mailing_list", $settings["global_mailing_list"]));
		$gml_table->setCol(2, 1, array('align' => 'right'), $deselect);
		$gml_table->setCol(3, 0, array(), we_html_tools::getPixel(5, 5));

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form"), $this->View->getHiddens() .
					we_html_tools::htmlDialogLayout(
						$table->getHtml() .
						we_html_tools::getPixel(5, 10) .
						$radios_code .
						we_html_tools::getPixel(5, 15) .
						$gml_table->getHtml() .
						we_html_tools::getPixel(5, 10), g_l('modules_newsletter', '[settings]'), we_button::position_yes_no_cancel($save, $close)
					)
				)
				. ($closeflag ? we_html_element::jsElement('top.close();') : "")
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLBlockType($name, $selected = 1){
		$values = array(
			weNewsletterBlock::DOCUMENT => g_l('modules_newsletter', '[newsletter_type_0]'),
			weNewsletterBlock::DOCUMENT_FIELD => g_l('modules_newsletter', '[newsletter_type_1]'),
		);

		if(defined("OBJECT_TABLE")){
			$values[weNewsletterBlock::OBJECT] = g_l('modules_newsletter', '[newsletter_type_2]');
			$values[weNewsletterBlock::OBJECT_FIELD] = g_l('modules_newsletter', '[newsletter_type_3]');
		}

		if(we_hasPerm("NEWSLETTER_FILES")){
			$values[weNewsletterBlock::FILE] = g_l('modules_newsletter', '[newsletter_type_4]');
		}
		$values[weNewsletterBlock::TEXT] = g_l('modules_newsletter', '[newsletter_type_5]');
		$values[weNewsletterBlock::ATTACHMENT] = g_l('modules_newsletter', '[newsletter_type_6]');
		$values[weNewsletterBlock::URL] = g_l('modules_newsletter', '[newsletter_type_7]');

		return we_html_tools::htmlSelect($name, $values, 1, $selected, false, 'style="width:440;" onChange="we_cmd(\'switchPage\',2);"', "value", "315", "defaultfont");
	}

	function getHTMLBox($w, $h, $content, $headline = "", $width = 120, $height = 2){
		$headline = str_replace(" ", "&nbsp;", $headline);

		return ($headline ?
				'<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td>' . we_html_tools::getPixel(24, $height) . '</td>
						<td>' . we_html_tools::getPixel($width, $height) . '</td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td valign="top" class="defaultgray">' . $headline . '</td>
						<td>' . $content . '</td>
					</tr>
					<tr>
						<td>' . we_html_tools::getPixel(24, $height) . '</td>
						<td>' . we_html_tools::getPixel($width, $height) . '</td>
						<td></td>
					</tr>
				</table>' :
				'<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td>' . we_html_tools::getPixel(24, $height) . '</td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td>' . $content . '</td>
					</tr>
					<tr>
						<td>' . we_html_tools::getPixel(24, $height) . '</td>
						<td></td>
					</tr></table>'
			);
	}

	function getHTMLCopy(){
		$IDName = "copyid";
		$Pathname = "copyid_text";

		//javascript:we_cmd('openSelector',document.we_form.elements['$IDName'].value,'".NEWSLETTER_TABLE."','document.we_form.elements[\\'$IDName\\'].value','document.we_form.elements[\\'$Pathname\\'].value','opener.we_cmd(\\'copy_newsletter\\');','".session_id()."','".get_ws(NEWSLETTER_TABLE)."')
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$IDName'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$Pathname'].value");
		$wecmdenc3 = we_cmd_enc("opener.we_cmd('copy_newsletter');");

		return $this->View->htmlHidden($IDName, 0) .
			$this->View->htmlHidden($Pathname, "") .
			we_button::create_button("select", "javascript:we_cmd('openSelector',document.we_form.elements['$IDName'].value,'" . NEWSLETTER_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','" . get_ws(NEWSLETTER_TABLE) . "')");
	}

	function getHTMLCustomer($group){
		$out = we_forms::checkbox($this->View->newsletter->groups[$group]->SendAll, (($this->View->newsletter->groups[$group]->SendAll == 0) ? false : true), "sendallcheck_$group", g_l('modules_newsletter', '[send_all]'), false, "defaultfont", "we_cmd('switch_sendall',$group);");

		if($this->View->newsletter->groups[$group]->SendAll == 0){

			$delallbut = we_button::create_button("delete_all", "javascript:we_cmd('del_all_customers'," . $group . ")");
			$addbut = we_button::create_button("add", "javascript:we_cmd('openSelector','','" . CUSTOMER_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_customer\\',top.allIDs," . $group . ");','','','',1)");

			$cats = new MultiDirChooser($this->def_width, $this->View->newsletter->groups[$group]->Customers, "del_customer", we_button::create_button_table(array($delallbut, $addbut)), "", "Icon,Path", CUSTOMER_TABLE);
			$cats->extraDelFn = "document.we_form.ngroup.value=$group";
			$out.=$cats->get();
		}

		$out.=$this->getHTMLCustomerFilter($group);

		return $out;
	}

	function getHTMLExtern($group){
		$delallbut = we_button::create_button("delete_all", "javascript:we_cmd('del_all_files'," . $group . ")");
		$wecmdenc4 = we_cmd_enc("opener.we_cmd('add_file',top.currentID,$group);");
		$addbut = we_button::create_button("add", "javascript:we_cmd('browse_server','fileselect','','/','" . $wecmdenc4 . "');");


		$buttons = (we_hasPerm("CAN_SELECT_EXTERNAL_FILES")) ?
			array($delallbut, $addbut) :
			array($delallbut);
		$cats = new MultiFileChooser($this->def_width, $this->View->newsletter->groups[$group]->Extern, "del_file", we_button::create_button_table($buttons), "edit_file");

		$cats->extraDelFn = "document.we_form.ngroup.value=$group";
		return $this->View->htmlHidden("fileselect", "") .
			$cats->get();
	}

	function getHTMLCustomerFilter($group){
		$custfields = array();

		foreach($this->View->customers_fields as $fk => $fv){
			if($fv != "ParentID" && $fv != "IsFolder" && $fv != "Path" && $fv != "Text" && $fv != "Icon"){
				$custfields[$fv] = $fv;
			}
		}

		$operators = array("0" => "=", "1" => "<>", "2" => "<", "3" => "<=", "4" => ">", "5" => ">=", "7" => g_l('modules_newsletter', '[operator][contains]'), "8" => g_l('modules_newsletter', '[operator][startWith]'), "9" => g_l('modules_newsletter', '[operator][endsWith]'), "6" => "LIKE",);
		$logic = array("AND" => g_l('modules_newsletter', '[logic][and]'), "OR" => g_l('modules_newsletter', '[logic][or]'));
		$hours = array();
		for($i = 0; $i < 24; $i++){
			$hours[] = ($i <= 9 ? '0' : '') . $i;
		}
		$minutes = array();
		for($i = 0; $i < 60; $i++){
			$minutes[] = ($i <= 9 ? '0' : '') . $i;
		}

		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 1, 7);
		$colspan = "7";
		$table->setCol(0, 0, ((count($this->View->newsletter->groups[$group]->aFilter) && is_array($this->View->newsletter->groups[$group]->aFilter)) ? array("colspan" => $colspan) : array()), we_forms::checkbox(((count($this->View->newsletter->groups[$group]->aFilter) && is_array($this->View->newsletter->groups[$group]->aFilter)) ? "1" : "0"), ((count($this->View->newsletter->groups[$group]->aFilter) && is_array($this->View->newsletter->groups[$group]->aFilter)) ? true : false), "filtercheck_$group", g_l('modules_newsletter', '[filter]'), false, "defaultfont", "if(document.we_form.filtercheck_$group.checked) we_cmd('add_filter',$group); else we_cmd('del_all_filters',$group);")
		);

		$k = 0;
		$c = 1;
		if(is_array($this->View->newsletter->groups[$group]->aFilter)){
			foreach($this->View->newsletter->groups[$group]->aFilter as $k => $v){
				if($k != 0){
					$table->addRow();
					$table->setCol($c, 0, array("colspan" => $colspan), we_html_tools::htmlSelect("filter_logic_" . $group . "_" . $k, $logic, 1, $v["logic"], false, '', "value", "70"));
					$c++;
				}

				$table->addRow();
				$table->setCol($c, 0, array(), we_html_tools::htmlSelect("filter_fieldname_" . $group . "_" . $k, $custfields, 1, $v["fieldname"], false, 'onChange="top.content.hot=1;changeFieldValue(this.val,\'filter_fieldvalue_' . $group . '_' . $k . '\');"', "value", "170"));
				$table->setCol($c, 1, array(), we_html_tools::htmlSelect("filter_operator_" . $group . "_" . $k, $operators, 1, $v["operator"], false, 'onChange="top.content.hot=1;"', "value", "80"));
				if($v['fieldname'] == "MemberSince" || $v['fieldname'] == "LastLogin" || $v['fieldname'] == "LastAccess"){
					$table->setCol($c, 2, array("id" => "td_value_fields_" . $group . "_" . $k), $this->getDateSelector("", "filter_fieldvalue_" . $group . "_" . $k, "_from_" . $group . "_" . $k, isset($v["fieldvalue"]) && $v["fieldvalue"] != "" ? !stristr($v["fieldvalue"], ".") ? @date("d.m.Y", $v["fieldvalue"]) : $v["fieldvalue"]  : ""));
					$table->setCol($c, 3, array(), we_html_tools::htmlSelect("filter_hours_" . $group . "_" . $k, $hours, 1, isset($v["hours"]) ? $v["hours"] : "", false, 'onChange="top.content.hot=1;"'));
					$table->setCol($c, 4, array("class" => "defaultfont"), "&nbsp;h :");
					$table->setCol($c, 5, array(), we_html_tools::htmlSelect("filter_minutes_" . $group . "_" . $k, $minutes, 1, isset($v["minutes"]) ? $v["minutes"] : "", false, 'onChange="top.content.hot=1;"'));
					$table->setCol($c, 6, array("class" => "defaultfont"), "&nbsp;m");
				} else{
					$table->setCol($c, 2, array("colspan" => $colspan, "id" => "td_value_fields_" . $group . "_" . $k), we_html_tools::htmlTextInput("filter_fieldvalue_" . $group . "_" . $k, 16, isset($v["fieldvalue"]) ? $v["fieldvalue"] : "", "", 'onKeyUp="top.content.hot=1;"', "text", "200"));
				}

				$c++;
			}
		}

		if(is_array($this->View->newsletter->groups[$group]->aFilter) && count($this->View->newsletter->groups[$group]->aFilter)){
			$table->addRow();
			$table->setCol($c, 0, array("colspan" => $colspan), we_html_tools::getPixel(5, 5));

			$plus = we_button::create_button("image:btn_function_plus", "javascript:we_cmd('add_filter',$group)");
			$trash = we_button::create_button("image:btn_function_trash", "javascript:we_cmd('del_filter',$group)");

			$c++;
			$table->addRow();
			$table->setCol($c, 0, array("colspan" => $colspan), we_button::create_button_table(array($plus, $trash)));
		}

		$js = we_html_element::jsElement("calendarSetup(" . $group . "," . $k . ");");

		return $this->View->htmlHidden("filter_" . $group, count($this->View->newsletter->groups[$group]->aFilter)) .
			$table->getHtml() . $js;
	}

	function getDateSelector($_label, $_name, $_btn, $value){
		$btnDatePicker = we_button::create_button(
				"image:date_picker", "javascript:", null, null, null, null, null, null, false, $_btn);
		$oSelector = new we_html_table(
				array(
					"cellpadding" => "0", "cellspacing" => "0", "border" => "0", "id" => $_name . "_cell"
				),
				1,
				5);
		$oSelector->setCol(
			0, 2, null, we_html_tools::htmlTextInput(
				$name = $_name, $size = 55, $value, $maxlength = 10, $attribs = 'id="' . $_name . '" class="wetextinput" readonly="1"', $type = "text", $width = 100));
		$oSelector->setCol(0, 3, null, "&nbsp;");
		$oSelector->setCol(0, 4, null, we_html_element::htmlA(array(
				"href" => "#"
				), $btnDatePicker));

		return $oSelector->getHTML();
	}

	/**
	 * Mailing list - block Emails
	 *
	 * @package weModules
	 * @subpackage Newsletter
	 *
	 * @param unknown_type $group
	 * @return unknown
	 */
	function getHTMLEmails($group){
		$arr = $this->View->newsletter->getEmailsFromList(oldHtmlspecialchars($this->View->newsletter->groups[$group]->Emails), 1);
		// Buttons to handle the emails in  the email list
		$buttons_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 7, 1);
		$buttons_table->setCol(0, 0, array(), we_button::create_button("add", "javascript:we_cmd('add_email', " . $group . ");"));
		$buttons_table->setCol(1, 0, array(), we_html_tools::getPixel(1, 5));
		$buttons_table->setCol(2, 0, array(), we_button::create_button("edit", "javascript:we_cmd('edit_email', " . $group . ");"));
		$buttons_table->setCol(3, 0, array(), we_html_tools::getPixel(1, 5));
		$buttons_table->setCol(4, 0, array(), we_button::create_button("delete", "javascript:deleteit(" . $group . ")"));
		$buttons_table->setCol(5, 0, array(), we_html_tools::getPixel(1, 5));
		$buttons_table->setCol(6, 0, array(), we_button::create_button("delete_all", "javascript:deleteall(" . $group . ")"));

		// Dialog table for the email block
		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 6, 3);

		// 1. ROW: select status
		$selectStatus = we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", (isset($_REQUEST['weEmailStatus']) ? $_REQUEST['weEmailStatus'] : "0"), "", "onchange='weShowMailsByStatus(this.value, $group);' id='weViewByStatus'", "value", "150");
		$table->setCol(0, 0, array("valign" => "middle", "colspan" => "3", "class" => "defaultfont"), $selectStatus);
		$table->setCol(1, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 10));

		// 2. ROW: Mail list with handling buttons
		$table->setCol(2, 0, array("valign" => "top"), $this->View->newsletter->htmlSelectEmailList("we_recipient" . $group, $arr, 10, "", false, 'style="width:' . ($this->def_width - 110) . 'px; height:140px" id="we_recipient' . $group . '"', "value", "600"));
		$table->setCol(2, 1, array("valign" => "middle"), we_html_tools::getPixel(10, 12));
		$table->setCol(2, 2, array("valign" => "top"), $buttons_table->getHtml());
		$table->setCol(3, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 10));

		// 3. ROW: Buttons for email import and export
		$importbut = we_button::create_button("import", "javascript:we_cmd('set_import'," . $group . ")");
		$exportbut = we_button::create_button("export", "javascript:we_cmd('set_export'," . $group . ")");

		$table->setCol(4, 0, array("colspan" => "3"), we_button::create_button_table(array($importbut, $exportbut)));

		// Import dialog
		if($this->View->show_import_box == $group){
			$ok = we_button::create_button("ok", "javascript:we_cmd('import_csv')");
			$cancel = we_button::create_button("cancel", "javascript:we_cmd('reset_import');");

			$import_options = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 14, 3);

			$import_options->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_delimiter]') . ":&nbsp;");
			$import_options->setCol(0, 1, array(), we_html_tools::htmlTextInput("csv_delimiter" . $group, 1, ","));
			$import_options->setCol(2, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));
			$import_options->setCol(3, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_col]') . ":&nbsp;");
			$import_options->setCol(3, 1, array(), we_html_tools::htmlTextInput("csv_col" . $group, 2, "1"));
			$import_options->setCol(4, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));
			$import_options->setCol(5, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_hmcol]') . ":&nbsp;");
			$import_options->setCol(5, 1, array(), we_html_tools::htmlTextInput("csv_hmcol" . $group, 2, "2"));
			$import_options->setCol(5, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_html_explain]'));
			$import_options->setCol(6, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));
			$import_options->setCol(7, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_salutationcol]') . ":&nbsp;");
			$import_options->setCol(7, 1, array(), we_html_tools::htmlTextInput("csv_salutationcol" . $group, 2, "3"));
			$import_options->setCol(7, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_salutation_explain]'));
			$import_options->setCol(8, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));
			$import_options->setCol(9, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_titlecol]') . ":&nbsp;");
			$import_options->setCol(9, 1, array(), we_html_tools::htmlTextInput("csv_titlecol" . $group, 2, "4"));
			$import_options->setCol(9, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_title_explain]'));
			$import_options->setCol(10, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));
			$import_options->setCol(11, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_firstnamecol]') . ":&nbsp;");
			$import_options->setCol(11, 1, array(), we_html_tools::htmlTextInput("csv_firstnamecol" . $group, 2, "5"));
			$import_options->setCol(11, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_firstname_explain]'));
			$import_options->setCol(12, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));
			$import_options->setCol(13, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_lastnamecol]') . ":&nbsp;");
			$import_options->setCol(13, 1, array(), we_html_tools::htmlTextInput("csv_lastnamecol" . $group, 2, "6"));
			$import_options->setCol(13, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_lastname_explain]'));


			$import_box = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 8, 1);

			$import_box->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
			$import_box->setCol(1, 0, array(), $this->View->formFileChooser(200, "csv_file" . $group, "/", ""));
			$import_box->setCol(2, 0, array(), we_html_tools::getPixel(5, 5));
			$import_box->setCol(3, 0, array(), we_button::create_button("upload", "javascript:we_cmd('upload_csv',$group)"));
			$import_box->setCol(4, 0, array(), we_html_tools::getPixel(5, 5));
			$import_box->setCol(5, 0, array(), $import_options->getHtml());
			$import_box->setCol(6, 0, array(), we_html_tools::getPixel(10, 10));
			$import_box->setCol(7, 0, array("nowrap" => null), we_button::create_button_table(array($ok, $cancel)));

			$table->setCol(5, 0, array("colspan" => "3"), $this->View->htmlHidden("csv_import", $group) . $import_box->getHtml());
		}

		// Export dialog
		if($this->View->show_export_box == $group){
			$ok = we_button::create_button("ok", "javascript:we_cmd('export_csv')");
			$cancel = we_button::create_button("cancel", "javascript:we_cmd('reset_import');");

			$export_box = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 4, 1);

			$export_box->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
			$export_box->setCol(1, 0, array(), $this->View->formFileChooser(200, "csv_dir" . $group, "/", "", "folder"));
			$export_box->setCol(2, 0, array(), we_html_tools::getPixel(5, 5));
			$export_box->setCol(3, 0, array("nowrap" => null), we_button::create_button_table(array($ok, $cancel)));

			$table->setCol(5, 0, array("colspan" => "3"), $this->View->htmlHidden("csv_export", $group) . $export_box->getHtml());
		}

		return $table->getHtml();
	}

	function getHTMLNewsletterBlocks(){
		$out = "";
		$counter = 0;

		$parts = array();
		array_push($parts, array("headline" => "", "html" => $this->View->htmlHidden("blocks", count($this->View->newsletter->blocks)), "space" => 140, "noline" => 1));


		foreach($this->View->newsletter->blocks as $block){

			$content = we_html_tools::htmlFormElementTable($this->getHTMLBlockType("block" . $counter . "_Type", $block->Type), g_l('modules_newsletter', '[name]'));

			$values = array();
			$count = count($this->View->newsletter->groups) + 1;

			for($i = 1; $i < $count; $i++){
				$values[$i] = sprintf(g_l('modules_newsletter', '[mailing_list]'), $i);
			}

			$selected = $block->Groups ? $block->Groups : "1";
			$content.=$this->View->htmlHidden("block" . $counter . "_Groups", $selected) .
				$this->View->htmlHidden("block" . $counter . "_Pack", $block->Pack) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("block" . $counter . "_GroupsSel", $values, 5, $selected, true, "style='width:440' onChange='PopulateMultipleVar(document.we_form.block" . $counter . "_GroupsSel,document.we_form.block" . $counter . "_Groups);top.content.hot=1'"), g_l('modules_newsletter', '[block_lists]'));

			switch($block->Type){
				case weNewsletterBlock::DOCUMENT:
					$content.=we_html_tools::htmlFormElementTable($this->View->formWeDocChooser(FILE_TABLE, "320", 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.top.content.hot=1;", "text/webedition", $this->weAutoColpleter), g_l('modules_newsletter', '[block_document]')) .
						we_html_tools::htmlFormElementTable(we_forms::checkbox((($block->Field) ? "0" : "1"), (($block->Field) ? false : true), "block" . $counter . "_use_def_template", g_l('modules_newsletter', '[use_default]'), false, "defaultfont", "top.content.hot=1;if(document.we_form.block" . $counter . "_use_def_template.checked){ document.we_form.block" . $counter . "_Field.value=0; document.we_form.block" . $counter . "_FieldPath.value='';}"), "&nbsp;&nbsp;&nbsp;") .
						we_html_tools::htmlFormElementTable($this->View->formWeChooser(TEMPLATES_TABLE, "320", 0, "block" . $counter . "_Field", (!is_numeric($block->Field) ? 0 : $block->Field), "block" . $counter . "_FieldPath", "", "if(opener.document.we_form.block" . $counter . "_use_def_template.checked) opener.document.we_form.block" . $counter . "_use_def_template.checked=false;opener.top.content.hot=1;", "", $this->weAutoColpleter, "folder,text/weTmpl"), g_l('modules_newsletter', '[block_template]'));
					break;

				case weNewsletterBlock::DOCUMENT_FIELD:
					$content.=we_html_tools::htmlFormElementTable($this->View->formWeChooser(FILE_TABLE, "320", 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.we_cmd(\'switchPage\',2);opener.top.content.hot=1;", "", $this->weAutoColpleter, "folder,text/webedition"), g_l('modules_newsletter', '[block_document]'));

					if($block->LinkID){
						$values = $this->View->getFields($block->LinkID, FILE_TABLE);

						$content.=(count($values) ?
								we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("block" . $counter . "_Field", $values, 1, $block->Field, "", "style='width:440' OnKeyUp='top.content.hot=1;'"), g_l('modules_newsletter', '[block_document_field]')) :
								we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array("class" => "defaultgray"), g_l('modules_newsletter', '[none]')), g_l('modules_newsletter', '[block_document_field]'))
							);
					}
					break;

				case weNewsletterBlock::OBJECT:
					$content.=we_html_tools::htmlFormElementTable($this->View->formWeChooser(OBJECT_FILES_TABLE, "320", 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.top.content.hot=1;", (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1), $this->weAutoColpleter, "folder,objectFile"), g_l('modules_newsletter', '[block_object]')) .
						we_html_tools::htmlFormElementTable($this->View->formWeChooser(TEMPLATES_TABLE, "320", 0, "block" . $counter . "_Field", (!is_numeric($block->Field) ? 0 : $block->Field), "block" . $counter . "_FieldPath", "", "opener.top.content.hot=1;", "", $this->weAutoColpleter, "folder,text/weTmpl"), g_l('modules_newsletter', '[block_template]'));
					break;

				case weNewsletterBlock::OBJECT_FIELD:
					$content.=we_html_tools::htmlFormElementTable($this->View->formWeChooser(OBJECT_FILES_TABLE, "320", 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.we_cmd(\'switchPage\',2);opener.top.content.hot=1;", (we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1), $this->weAutoColpleter, "folder,objectFile"), g_l('modules_newsletter', '[block_object]'));

					if($block->LinkID){
						$values = $this->View->getFields($block->LinkID, OBJECT_FILES_TABLE);

						$content.=(count($values) ?
								we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("block" . $counter . "_Field", $values, 1, $block->Field, false, 'OnChange="top.content.hot=1;"'), g_l('modules_newsletter', '[block_object_field]')) :
								we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array("class" => "defaultgray"), g_l('modules_newsletter', '[none]')), g_l('modules_newsletter', '[block_document_field]'))
							);
					}
					break;

				case weNewsletterBlock::FILE:
					$content.=we_html_tools::htmlFormElementTable($this->View->formFileChooser("320", "block" . $counter . "_Field", (is_numeric($block->Field) ? "" : ((substr($block->Field, 0, 1) != "/") ? "" : $block->Field))), g_l('modules_newsletter', '[block_file]'));
					break;

				case weNewsletterBlock::TEXT:
					$attribs = array(
						"wysiwyg" => "on",
						"width" => 430,
						"height" => 200,
						"rows" => 10,
						"cols" => 40,
						"cols" => 40,
						"style" => "width:440",
						"inlineedit" => "true",
						"bgcolor" => "white",
					);
					$content.=we_html_tools::htmlFormElementTable(we_html_element::htmlTextArea(array("cols" => "40", "rows" => "10", "name" => "block" . $counter . "_Source", "onChange" => "top.content.hot=1;", "style" => "width:440"), oldHtmlspecialchars($block->Source)), g_l('modules_newsletter', '[block_plain]')) .
						we_html_element::jsScript(JS_DIR . "we_textarea.js") .
						we_html_tools::htmlFormElementTable(we_forms::weTextarea("block" . $counter . "_Html", $block->Html, $attribs, "", "", true, "", true, true, false, true, $this->View->newsletter->Charset), g_l('modules_newsletter', '[block_html]')) .
						we_html_element::jsElement('
					function extraInit(){
							if(typeof weWysiwygInitializeIt == "function"){
								weWysiwygInitializeIt();
							}
							loaded = 1;
						}
						window.onload=extraInit;');
					break;

				case weNewsletterBlock::ATTACHMENT:
					$content.=we_html_tools::htmlFormElementTable($this->View->formWeChooser(FILE_TABLE, "320", 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "", "", $this->weAutoColpleter, "folder,text/xml,text/webedition,image/*,text/html,application/*,application/x-shockwave-flash,video/quicktime"), g_l('modules_newsletter', '[block_attachment]'));
					break;

				case weNewsletterBlock::URL:
					$content.=we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("block" . $counter . "_Field", 49, (is_numeric($block->Field) ? "" : $block->Field), "", "style='width:440'", "text", "0", "0", "top.content"), g_l('modules_newsletter', '[block_url]'));
					break;
			}

			$buttons = we_html_tools::getPixel(440, 1);

			$plus = we_button::create_button("image:btn_function_plus", "javascript:we_cmd('addBlock','" . $counter . "')");
			$trash = we_button::create_button("image:btn_function_trash", "javascript:we_cmd('delBlock','" . $counter . "')");

			$buttons.=(count($this->View->newsletter->blocks) > 1 ?
					we_button::position_yes_no_cancel($plus, $trash) :
					we_button::position_yes_no_cancel($plus)
				);

			$parts[] = array("headline" => sprintf(g_l('modules_newsletter', '[block]'), ($counter + 1)), "html" => $content, "space" => 140);
			$parts[] = array("headline" => "", "html" => $buttons, "space" => 140);

			$counter++;
		}

		return we_multiIconBox::getHTML("newsletter_header", "100%", $parts, 30, "", -1, "", "", false);
	}

	function getHTMLNewsletterGroups(){
		$count = count($this->View->newsletter->groups);

		$out = we_multiIconBox::getJS();

		for($i = 0; $i < $count; $i++){
			$parts = array();

			if(defined("CUSTOMER_TABLE")){
				$parts[] = array("headline" => g_l('modules_newsletter', '[customers]'), "html" => $this->getHTMLCustomer($i), "space" => 140);
			}

			$parts[] = array("headline" => g_l('modules_newsletter', '[file_email]'), "html" => $this->getHTMLExtern($i), "space" => 140);
			$parts[] = array("headline" => g_l('modules_newsletter', '[emails]'), "html" => $this->getHTMLEmails($i), "space" => 140);


			$plus = ($i == $count - 1 ? we_button::create_button("image:btn_function_plus", "javascript:we_cmd('addGroup')") : null);
			$trash = ($count > 1 ? we_button::create_button("image:btn_function_trash", "javascript:we_cmd('delGroup'," . $i . ")") : null);

			$buttons = we_button::create_button_table(array($plus, $trash), "10", array("align" => "right"));

			$wepos = weGetCookieVariable("but_newsletter_group_box_$i");

			$out.= we_multiIconBox::getHTML("newsletter_group_box_$i", "100%", $parts, 30, "", 0, "", "", (($wepos == "down") || ($count < 2 ? true : false)), sprintf(g_l('modules_newsletter', '[mailing_list]'), ($i + 1))) .
				we_html_element::htmlBr() . '<div style="margin-right:30px;">' . $buttons . '</div>';
		}

		return $out;
	}

	function getHTMLNewsletterHeader(){
		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 3, 1);
		$table->setCol(0, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Text", 37, stripslashes($this->View->newsletter->Text), "", 'onKeyUp="top.content.hot=1;" id="yuiAcInputPathName" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()"', 'text', $this->def_width), g_l('modules_newsletter', '[name]')));
		$table->setCol(1, 0, array(), we_html_tools::getPixel(10, 10));
		$table->setCol(2, 0, array(), we_html_tools::htmlFormElementTable($this->View->formNewsletterDirChooser(($this->def_width - 120), 0, "ParentID", $this->View->newsletter->ParentID, "Path", dirname($this->View->newsletter->Path), "opener.top.content.hot=1;", $this->weAutoColpleter), g_l('modules_newsletter', '[dir]')));

		//$table->setCol(2,0,array(),we_html_tools::htmlFormElementTable($this->View->formWeDocChooser(NEWSLETTER_TABLE,"320",0,"ParentID",$this->View->newsletter->ParentID,"Path",dirname($this->View->newsletter->Path),"opener.top.content.hot=1;","folder"),g_l('modules_newsletter','[dir]')));
		$parts = array(
			array("headline" => "", "html" => "", "space" => 140, "noline" => 1),
			array("headline" => g_l('modules_newsletter', '[path]'), "html" => $table->getHtml(), "space" => 140),
		);

		if(!$this->View->newsletter->IsFolder){
			$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 9, 1);
			$table->setCol(0, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Subject", 37, stripslashes($this->View->newsletter->Subject), "", "onKeyUp='top.content.hot=1;'", 'text', $this->def_width), g_l('modules_newsletter', '[subject]')));
			$table->setCol(1, 0, array(), we_html_tools::getPixel(10, 10));
			$table->setCol(2, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Sender", 37, $this->View->newsletter->Sender, "", "onKeyUp='top.content.hot=1;'", 'text', $this->def_width), g_l('modules_newsletter', '[sender]')));
			$table->setCol(3, 0, array(), we_html_tools::getPixel(10, 10));

			$chk = ($this->View->newsletter->Sender == $this->View->newsletter->Reply ?
					we_html_element::htmlInput(array("type" => "checkbox", "value" => "1", "checked" => null, "name" => "reply_same", "onClick" => $this->topFrame . ".hot=1;if(document.we_form.reply_same.checked) document.we_form.Reply.value=document.we_form.Sender.value")) :
					we_html_element::htmlInput(array("type" => "checkbox", "value" => "0", "name" => "reply_same", "onClick" => $this->topFrame . ".hot=1;if(document.we_form.reply_same.checked) document.we_form.Reply.value=document.we_form.Sender.value"))
				);
			$table->setCol(4, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Reply", 37, $this->View->newsletter->Reply, "", "onKeyUp='top.content.hot=1;'") . "&nbsp;&nbsp;" . $chk . "&nbsp;" . we_html_element::htmlLabel(array("class" => "defaultfont", "onClick" => $this->topFrame . ".hot=1;if(document.we_form.reply_same.checked){document.we_form.reply_same.checked=false;}else{document.we_form.Reply.value=document.we_form.Sender.value;document.we_form.reply_same.checked=true;}"), g_l('modules_newsletter', '[reply_same]')), g_l('modules_newsletter', '[reply]')));
			$table->setCol(5, 0, array(), we_html_tools::getPixel(10, 10));
			$table->setCol(6, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Test", 37, $this->View->newsletter->Test, "", "onKeyUp='top.content.hot=1;'"), g_l('modules_newsletter', '[test_email]')));
			$table->setCol(7, 0, array(), we_html_tools::getPixel(10, 10));

			$_embedImagesChk = ($this->View->newsletter->isEmbedImages ?
					we_html_element::htmlInput(array("type" => "checkbox", "value" => "1", "name" => "isEmbedImagesChk", "onClick" => $this->topFrame . ".hot=1;if(document.we_form.isEmbedImagesChk.checked){document.we_form.isEmbedImages.value=1;}else{document.we_form.isEmbedImages.value=0;}", "checked" => null), g_l('modules_newsletter', '[isEmbedImages]')) :
					we_html_element::htmlInput(array("type" => "checkbox", "value" => "1", "name" => "isEmbedImagesChk", "onClick" => $this->topFrame . ".hot=1;if(document.we_form.isEmbedImagesChk.checked){document.we_form.isEmbedImages.value=1;}else{document.we_form.isEmbedImages.value=0;}"), g_l('modules_newsletter', '[isEmbedImages]'))
				);
			$_embedImagesHid = we_html_element::htmlHidden(array("name" => "isEmbedImages", "value" => $this->View->newsletter->isEmbedImages));
			//$_embedImagesChk = we_html_element::htmlInput(array("type"=>"checkbox", "value"=>"1", "name"=>"_isEmbedImages" ,"onClick"=>$this->topFrame.".hot=1;","checked"=>($this->View->newsletter->isEmbedImages?"true":"false")),g_l('modules_newsletter','[isEmbedImages]'));
			$_embedImagesLab = we_html_element::htmlLabel(array("class" => "defaultfont", "onClick" => $this->topFrame . ".hot=1;if(document.we_form.isEmbedImagesChk.checked){ document.we_form.isEmbedImagesChk.checked=false; document.we_form.isEmbedImages.value=0; }else{document.we_form.isEmbedImagesChk.checked=true;document.we_form.isEmbedImages.value=1;}"), g_l('modules_newsletter', '[isEmbedImages]'));

			$table->setCol(8, 0, array(), we_html_tools::htmlFormElementTable($_embedImagesHid . $_embedImagesChk . "&nbsp;" . $_embedImagesLab, ""));

			$parts[] = array("headline" => g_l('modules_newsletter', '[newsletter]'), "html" => $table->getHtml(), "space" => 140);
			$parts[] = array("headline" => g_l('modules_newsletter', '[charset]'), "html" => $this->getHTMLCharsetTable(), "space" => 140);
			$parts[] = array("headline" => g_l('modules_newsletter', '[copy_newsletter]'), "html" => $this->getHTMLCopy(), "space" => 140, "noline" => 1);
		}

		return we_multiIconBox::getHTML("newsletter_header", "100%", $parts, 30, "", -1, "", "", false) .
			we_html_element::htmlBr();
	}

	/**
	 * Generates the body for modul frame
	 *
	 * @package weModules
	 * @subpackage Newsletter
	 * @return unknown
	 */
	function getHTMLProperties(){
		if(isset($_REQUEST["home"]) && $_REQUEST["home"]){
			$GLOBALS["we_print_not_htmltop"] = true;
			$GLOBALS["we_head_insert"] = $this->View->getJSProperty();
			$GLOBALS["we_body_insert"] = we_html_element::htmlForm(array("name" => "we_form"), $this->View->getHiddens(array("ncmd" => "home")) . $this->View->htmlHidden("home", "0")
			);
			$GLOBALS["mod"] = "newsletter";
			ob_start();
			include(WE_MODULES_PATH . 'home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}

		$js = $this->View->getJSProperty() .
			we_html_element::jsScript(JS_DIR . "jscalendar/calendar.js") .
			we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . "/calendar.js") .
			we_html_element::jsScript(JS_DIR . "jscalendar/calendar-setup.js") .
			we_html_element::jsElement('
					if (top.content.get_focus) {
						self.focus();
					} else {
						top.content.get_focus=1;
					}

					var countSetTitle = 0;
					function setHeaderTitle() {
						if(parent.edheader && parent.edheader.setTitlePath) {
							if(preObj  = document.getElementById("yuiAcInputPathGroup")) {
								parent.edheader.hasPathGroup = true;
								parent.edheader.setPathGroup(preObj.value);
							} else {
								parent.edheader.hasPathGroup = false;
							}

							if(postObj = document.getElementById("yuiAcInputPathName")) {
								parent.edheader.hasPathName = true;
								parent.edheader.setPathName(postObj.value);
							} else {
								parent.edheader.hasPathName = false;
							}
							parent.edheader.setTitlePath();
							countSetTitle = 0;
						} else {
							if(countSetTitle < 30) {
								setTimeout("setHeaderTitle()",100);
								countSetTitle++;
							/* @dd: code from version 5.0.0.7, generated on bugfix merge: */
							/* please remove if not needed any more */
							/*
							var elem1 = document.getElementById("fieldPathGroup");
							var elem2 = document.getElementById("fieldPathName");
							if (elem1 && elem2) {
								pre = document.getElementById("fieldPathGroup").value;
								post = document.getElementById("fieldPathName").value;
								if(parent.edheader && parent.edheader.setTitlePath) {
									parent.edheader.hasPathGroup = true;
									parent.edheader.setPathGroup(pre);
									parent.edheader.hasPathName = true;
									parent.edheader.setPathName(post);
									parent.edheader.setTitlePath();
									countSetTitle = 0;
								} else {
									if(countSetTitle < 30) {
										setTimeout("setHeaderTitle()",100);
										countSetTitle++;
									}
								}
							*/
							}
						}
					}

					function weShowMailsByStatus(status, group) {
						var maillist = document.getElementById("we_recipient"+group).options;
						switch(status) {
							case "0":
								for(var i=0; i<maillist.length; i++) {
									maillist[i].style.display="";
								}
								break;
							case "1":
								for(var i=0; i<maillist.length; i++) {
									if (maillist[i].className == "markValid") {
										maillist[i].style.display="none";
									}
								}
								break;
							default :
								//alert(status);
						}
					}

			function calendarSetup(group, x){
		    for(i=0;i<=x;i++) {
		     if(document.getElementById("date_picker_from_"+group+"_"+i+"") != null) {
		      Calendar.setup({inputField:"filter_fieldvalue_"+group+"_"+i+"",ifFormat:"%d.%m.%Y",button:"date_picker_from_"+group+"_"+i+"",align:"Tl",singleClick:true});
		     }
		    }
		   }

		  function changeFieldValue(val,valueField) {


		  	top.content.hot=1;
			document.we_form.ncmd.value=arguments[0];
			document.we_form.ngroup.value=arguments[1];

			if(val=="MemberSince" || val=="LastLogin" || val=="LastAccess") {
				document.getElementById(valueField).value = "";
			}
			submitForm();


		   }

		');




		$css = we_html_element::cssElement("
	.markNotValid { background: #FFCCCC }
	.markValid { background: #FFFFFF }
") .
			we_html_element::linkElement(
				array(
					"rel" => "stylesheet",
					"type" => "text/css",
					"href" => JS_DIR . "jscalendar/skins/aqua/theme.css",
					"title" => "Aqua"
			));


		$out = $this->View->getHiddens() .
			$this->View->newsletterHiddens() .
			$this->View->getHiddensProperty();

		if($this->View->page == 0){
			$out.=$this->weAutoColpleter->getYuiJsFiles() .
				$this->View->htmlHidden("home", "0") .
				$this->View->htmlHidden("fromPage", "0");

			if($this->View->newsletter->IsFolder == 0){
				$out.=$this->View->getHiddensMailingPage() .
					$this->View->getHiddensContentPage();
			}

			$out.=$this->getHTMLNewsletterHeader() .
				$this->weAutoColpleter->getYuiCss() .
				$this->weAutoColpleter->getYuiJs();
		} else if($this->View->page == 1){
			$out.=$this->View->getHiddensPropertyPage() .
				$this->View->getHiddensContentPage() .
				$this->View->htmlHidden("fromPage", "1") .
				$this->View->htmlHidden("ncustomer", "") .
				$this->View->htmlHidden("nfile", "") .
				$this->View->htmlHidden("ngroup", "") .
				$this->getHTMLNewsletterGroups();
		} else{
			$out.=$this->weAutoColpleter->getYuiJsFiles() .
				$this->View->getHiddensMailingPage() .
				$this->View->getHiddensPropertyPage() .
				$this->View->htmlHidden("fromPage", "2") .
				$this->View->htmlHidden("blockid", 0) .
				$this->getHTMLNewsletterBlocks() .
				$this->weAutoColpleter->getYuiCss() .
				$this->weAutoColpleter->getYuiJs();
		}

		$body = we_html_element::htmlBody(array("onload" => "self.loaded=1;if(self.doScrollTo){self.doScrollTo();}; setHeaderTitle();", "class" => "weEditorBody", "onunload" => "doUnload()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "onsubmit" => "return false;"), $out
				)
		);

		return $this->getHTMLDocument($body, $js . $css);
	}

	function getHTMLEmailEdit(){
		$vars = array("grp" => "group", "email" => "email", "htmlmail" => "htmlmail", "salutation" => "salutation", "title" => "title", "firstname" => "firstname", "lastname" => "lastname", "etyp" => "type", "eid" => "id");

		foreach($vars as $k => $v){
			if(isset($_REQUEST[$k])){
				$$v = $_REQUEST[$k];
			} else if($v == "htmlmail"){
				$$v = f("SELECT  pref_value FROM " . NEWSLETTER_PREFS_TABLE . " WHERE pref_name='default_htmlmail'", "pref_value", $this->db);
			} else{
				$$v = "";
			}
		}

		$salutation = rawurldecode(str_replace("[:plus:]", "+", $salutation));
		$title = rawurldecode(str_replace("[:plus:]", "+", $title));
		$firstname = rawurldecode(str_replace("[:plus:]", "+", $firstname));
		$lastname = rawurldecode(str_replace("[:plus:]", "+", $lastname));

		$js = 'function save(){';

		switch($type){
			case 2:
				$js.='opener.setAndSave(document.we_form.id.value,document.we_form.emailfield.value,document.we_form.htmlmail.value,document.we_form.salutation.value,document.we_form.title.value,document.we_form.firstname.value,document.we_form.lastname.value);
					close();';
				break;
			case 1:
				$js.='opener.editIt(document.we_form.group.value,document.we_form.id.value,document.we_form.emailfield.value,document.we_form.htmlmail.value,document.we_form.salutation.value,document.we_form.title.value,document.we_form.firstname.value,document.we_form.lastname.value);
				close();';
				break;
			default:
				$js.='opener.add(document.we_form.group.value,document.we_form.emailfield.value,document.we_form.htmlmail.value,document.we_form.salutation.value,document.we_form.title.value,document.we_form.firstname.value,document.we_form.lastname.value);
				close();';
		}
		$js.='}';

		$js = we_html_element::jsElement($js);

		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 12, 3);

		$table->setCol(0, 0, array("class" => "defaultgray"), g_l('modules_newsletter', '[email]'));
		$table->setCol(0, 1, array(), we_html_tools::getPixel(15, 10));
		$table->setCol(0, 2, array(), we_html_tools::htmlTextInput("emailfield", 32, $email, "", "", "text", 310));

		$table->setCol(1, 2, array(), we_html_tools::getPixel(2, 3));

		$table->setCol(2, 2, array(), we_forms::checkbox($htmlmail, (($htmlmail) ? true : false), "htmlmail", g_l('modules_newsletter', '[edit_htmlmail]'), false, "defaultfont", "if(document.we_form.htmlmail.checked) document.we_form.htmlmail.value=1; else document.we_form.htmlmail.value=0;"));

		$table->setCol(3, 2, array(), we_html_tools::getPixel(2, 3));

		$salut_select = new we_html_select(array("name" => "salutation", "style" => "width: 310px"));
		$salut_select->addOption("", "");
		if(!empty($this->View->settings[weNewsletter::FEMALE_SALUTATION_FIELD])){
			$salut_select->addOption($this->View->settings[weNewsletter::FEMALE_SALUTATION_FIELD], $this->View->settings[weNewsletter::FEMALE_SALUTATION_FIELD]);
		}
		if(!empty($this->View->settings[weNewsletter::MALE_SALUTATION_FIELD])){
			$salut_select->addOption($this->View->settings[weNewsletter::MALE_SALUTATION_FIELD], $this->View->settings[weNewsletter::MALE_SALUTATION_FIELD]);
		}
		$salut_select->selectOption($salutation);

		$table->setCol(4, 0, array("class" => "defaultgray"), g_l('modules_newsletter', '[salutation]'));
		$table->setCol(4, 1, array(), we_html_tools::getPixel(15, 10));
		$table->setCol(4, 2, array(), $salut_select->getHtml());

		$table->setCol(5, 2, array(), we_html_tools::getPixel(2, 3));

		$table->setCol(6, 0, array("class" => "defaultgray"), g_l('modules_newsletter', '[title]'));
		$table->setCol(6, 1, array(), we_html_tools::getPixel(15, 10));
		$table->setCol(6, 2, array(), we_html_tools::htmlTextInput("title", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($title) : $title), "", "", "text", 310));

		$table->setCol(7, 2, array(), we_html_tools::getPixel(2, 3));

		$table->setCol(8, 0, array("class" => "defaultgray"), g_l('modules_newsletter', '[firstname]'));
		$table->setCol(8, 1, array(), we_html_tools::getPixel(15, 10));
		$table->setCol(8, 2, array(), we_html_tools::htmlTextInput("firstname", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($firstname) : $firstname), "", "", "text", 310));

		$table->setCol(9, 2, array(), we_html_tools::getPixel(2, 3));

		$table->setCol(10, 0, array("class" => "defaultgray"), g_l('modules_newsletter', '[lastname]'));
		$table->setCol(10, 1, array(), we_html_tools::getPixel(15, 10));
		$table->setCol(10, 2, array(), we_html_tools::htmlTextInput("lastname", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($lastname) : $lastname), "", "", "text", 310));

		$table->setCol(11, 2, array(), we_html_tools::getPixel(2, 3));

		$close = we_button::create_button("close", "javascript:self.close();");
		$save = we_button::create_button("save", "javascript:save();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "document.we_form.emailfield.select();document.we_form.emailfield.focus();"), we_html_element::htmlForm(array("name" => "we_form", "onsubmit" => "save();return false;"), we_html_element::htmlHidden(array("name" => "group", "value" => $group)) .
					($type ?
						we_html_element::htmlHidden(array("name" => "id", "value" => $id)) :
						""
					) .
					we_html_tools::htmlDialogLayout(
						$table->getHtml(), $type ? g_l('modules_newsletter', '[edit_email]') : g_l('modules_newsletter', '[add_email]'), we_button::position_yes_no_cancel($save, $close)
					)
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLPreview(){
		$gview = 0;

		if(isset($_REQUEST["gview"])){
			$gview = $_REQUEST["gview"];
		}

		$hm = 0;

		if(isset($_REQUEST["hm"])){
			$hm = $_REQUEST["hm"];
		}

		$content = '';
		$count = count($this->View->newsletter->blocks);
		for($i = 0; $i < $count; $i++){
			$content.=$this->View->getContent($i, $gview, $hm);
		}

		header("Pragma: no-cache;");
		header("Cache-Control: post-check=0, post-check=0, false");
		we_html_tools::headerCtCharset('text/html', ($this->View->newsletter->Charset != "" ? $this->View->newsletter->Charset : $GLOBALS['WE_BACKENDCHARSET']));


		if(!$hm){
			print '
				<html>
					<head>
					</head>

					<body>
						<form>
							<textarea name="foo" style="width:100%;height:95%" cols="80" rows="40">' .
				oldHtmlspecialchars(trim($content)) .
				'</textarea>
						</form>
					</body>

				</html>';
		} else{
			print $content;
		}
	}

	function getHTMLBlackList(){
		$arr = array();

		if(isset($_REQUEST["black_list"])){
			$this->View->settings["black_list"] = $_REQUEST["black_list"];
		}

		if(isset($_REQUEST["ncmd"])){
			if($_REQUEST["ncmd"] == "save_black"){
				$this->View->processCommands();
			}
			$close = true;
		}

		$js = $this->View->getJSProperty() .
			we_html_element::jsElement('
			function addBlack() {
				var p=document.forms[0].elements["blacklist_sel"];
				var newRecipient=prompt("' . g_l('modules_newsletter', '[add_email]') . '","");

				if (newRecipient != null) {
					if (newRecipient.length > 0) {
						if (newRecipient.length > 255 ) {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							return;
						}

						if (!inSelectBox(p,newRecipient)) {
							addElement(p,"#",newRecipient,true);
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_exists]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}
				}
			}

			function deleteBlack() {
				var p=document.forms[0].elements["blacklist_sel"];

				if (p.selectedIndex >= 0) {
					if (confirm("' . g_l('modules_newsletter', '[email_delete]') . '")) {
						p.options[p.selectedIndex] = null;
					}
				}
			}

			function deleteallBlack() {
				var p=document.forms[0].elements["blacklist_sel"];

				if (confirm("' . g_l('modules_newsletter', '[email_delete_all]') . '")) {
					p.options.length = 0;
				}
			}

			function editBlack() {
				var p=document.forms[0].elements["blacklist_sel"];
				var index=p.selectedIndex;

				if (index >= 0) {
					var editRecipient=prompt("' . g_l('modules_newsletter', '[edit_email]') . '",p.options[index].text);

					if (editRecipient != null) {
						if (editRecipient != "") {
							if (editRecipient.length > 255 ) {
								' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
								return;
							}
							p.options[index].text = editRecipient;
						} else {
							' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
						}
					}
				}
			}

			function set_import(val) {
				document.we_form.sib.value=val;

				if (val == 1) {
					document.we_form.seb.value=0;
				}

				PopulateVar(document.we_form.blacklist_sel,document.we_form.black_list);
				submitForm("black_list");
			}

			function set_export(val) {
				document.we_form.seb.value=val;

				if (val == 1) {
					document.we_form.sib.value=0;
				}

				PopulateVar(document.we_form.blacklist_sel,document.we_form.black_list);
				submitForm("black_list");
			}

			self.focus();
		');

		if(isset($_REQUEST["ncmd"])){
			if($_REQUEST["ncmd"] == "import_black"){
				$filepath = $_REQUEST["csv_file"];
				$delimiter = $_REQUEST["csv_delimiter"];
				$col = $_REQUEST["csv_col"];

				if($col){
					$col--;
				}

				if(strpos($filepath, '..') !== false){
					print we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
				} else{
					$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $filepath, "rb");
					if($fh){
						while(($dat = fgetcsv($fh, 1000, $delimiter))) {
							$_alldat = implode("", $dat);
							if(str_replace(" ", "", $_alldat) == ""){
								continue;
							}
							$row[] = $dat[$col];
						}

						fclose($fh);

						if(!empty($row)){
							if($this->View->settings["black_list"] == ''){
								$this->View->settings["black_list"] = makeCSVFromArray($row);
							} else{
								$this->View->settings["black_list"].="," . makeCSVFromArray($row);
							}
						}
					} else{
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
							);
					}
				}
			}
		}

		if(isset($_REQUEST["ncmd"])){
			if($_REQUEST["ncmd"] == "export_black"){
				$fname = ($_REQUEST["csv_dir"] == "/" ? '' : $_REQUEST["csv_dir"]) . "/blacklist_export_" . time() . ".csv";
				weFile::save($_SERVER['DOCUMENT_ROOT'] . $fname, str_replace(",", "\n", $this->View->settings["black_list"]));

				$js.=we_html_element::jsScript(JS_DIR . "windows.js") .
					we_html_element::jsElement('new jsWindow("' . $this->frameset . '?pnt=export_csv_mes&lnk=' . $fname . '","edit_email",-1,-1,440,250,true,true,true,true);');
			}
		}

		$arr = makeArrayFromCSV($this->View->settings["black_list"]);


		$buttons_table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 7, 1);
		$buttons_table->setCol(0, 0, array(), we_button::create_button("add", "javascript:addBlack();"));
		$buttons_table->setCol(1, 0, array(), we_html_tools::getPixel(1, 5));
		$buttons_table->setCol(2, 0, array(), we_button::create_button("edit", "javascript:editBlack();"));
		$buttons_table->setCol(3, 0, array(), we_html_tools::getPixel(1, 5));
		$buttons_table->setCol(4, 0, array(), we_button::create_button("delete", "javascript:deleteBlack()"));
		$buttons_table->setCol(5, 0, array(), we_html_tools::getPixel(1, 5));
		$buttons_table->setCol(6, 0, array(), we_button::create_button("delete_all", "javascript:deleteallBlack()"));

		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 5, 3);
		$table->setCol(0, 0, array("valign" => "middle"), we_html_tools::htmlSelect("blacklist_sel", $arr, 10, "", false, 'style="width:388px"', "value", "600"));
		$table->setCol(0, 1, array("valign" => "middle"), we_html_tools::getPixel(10, 12));
		$table->setCol(0, 2, array("valign" => "top"), $buttons_table->getHtml());

		$table->setCol(1, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 10));

		$importbut = we_button::create_button("import", "javascript:set_import(1)");
		$exportbut = we_button::create_button("export", "javascript:set_export(1)");

		$table->setCol(2, 0, array("colspan" => "3"), we_button::create_button_table(array($importbut, $exportbut)));

		$sib = (isset($_REQUEST["sib"]) ? $_REQUEST["sib"] : 0);
		$seb = (isset($_REQUEST["seb"]) ? $_REQUEST["seb"] : 0);

		if($sib){
			$ok = we_button::create_button("ok", "javascript:document.we_form.sib.value=0;we_cmd('import_black');");
			$cancel = we_button::create_button("cancel", "javascript:set_import(0);");

			$import_options = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 5, 2);

			$import_options->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_delimiter]') . ":&nbsp;");
			$import_options->setCol(0, 1, array(), we_html_tools::htmlTextInput("csv_delimiter", 1, ","));
			$import_options->setCol(2, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));
			$import_options->setCol(3, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_col]') . ":&nbsp;");
			$import_options->setCol(3, 1, array(), we_html_tools::htmlTextInput("csv_col", 2, "1"));
			$import_options->setCol(4, 0, array("colspan" => "3"), we_html_tools::getPixel(5, 5));

			$import_box = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 8, 1);

			$import_box->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
			$import_box->setCol(1, 0, array(), $this->View->formFileChooser(200, "csv_file", "/", ""));
			$import_box->setCol(2, 0, array(), we_html_tools::getPixel(5, 5));
			$import_box->setCol(3, 0, array(), we_button::create_button("upload", "javascript:we_cmd('upload_black')"));
			$import_box->setCol(4, 0, array(), we_html_tools::getPixel(5, 5));
			$import_box->setCol(5, 0, array(), $import_options->getHtml());
			$import_box->setCol(6, 0, array(), we_html_tools::getPixel(10, 10));
			$import_box->setCol(7, 0, array("nowrap" => null), we_button::create_button_table(array($ok, $cancel)));

			$table->setCol(3, 0, array("colspan" => "3"), $this->View->htmlHidden("csv_import", "1") .
				$import_box->getHtml()
			);
		} elseif($seb){
			$ok = we_button::create_button("ok", "javascript:document.we_form.seb.value=0;we_cmd('export_black');");
			$cancel = we_button::create_button("cancel", "javascript:set_export(0);");
			$export_box = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 4, 1);

			$export_box->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
			$export_box->setCol(1, 0, array(), $this->View->formFileChooser(200, "csv_dir", "/", "", "folder"));
			$export_box->setCol(2, 0, array(), we_html_tools::getPixel(5, 5));
			$export_box->setCol(3, 0, array("nowrap" => null), we_button::create_button_table(array($ok, $cancel)));

			$table->setCol(3, 0, array("colspan" => "3"), $this->View->htmlHidden("csv_export", "1") .
				$export_box->getHtml()
			);
		}


		$cancel = we_button::create_button("cancel", "javascript:self.close();");
		$save = we_button::create_button("save", "javascript:we_cmd('save_black')");


		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "onsubmit" => "save();return false;"), $this->View->getHiddens() .
					$this->View->htmlHidden("black_list", $this->View->settings["black_list"]) .
					$this->View->htmlHidden("sib", $sib) .
					$this->View->htmlHidden("seb", $seb) .
					we_html_tools::htmlDialogLayout(
						$table->getHtml(), g_l('modules_newsletter', '[black_list]'), we_button::position_yes_no_cancel($save, null, $cancel)
					)
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLUploadCsv($js = "javascript:we_cmd('do_upload_csv');"){
		$cancel = we_button::create_button("cancel", "javascript:self.close();");
		$upload = we_button::create_button("upload", $js);

		$buttons = we_button::create_button_table(array($cancel, $upload));

		$js = $this->View->getJSProperty() .
			we_html_element::jsElement('
					self.focus();
		');

		$maxsize = getUploadMaxFilesize(true);

		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 4, 1);
		if($maxsize){
			$table->setCol(0, 0, array("style" => "padding-right:30px"), we_html_tools::htmlAlertAttentionBox(sprintf(g_l('newFile', "[max_possible_size]"), round($maxsize / (1024 * 1024), 3) . "MB"), 1));
		} else{
			$table->setCol(0, 0, array(), we_html_tools::getPixel(2, 10));
		}
		$table->setCol(1, 0, array(), we_html_tools::getPixel(2, 10));
		$table->setCol(2, 0, array("valign" => "middle"), we_html_element::htmlInput(array("name" => "we_File", "TYPE" => "file", "size" => "35")));

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "enctype" => "multipart/form-data"), we_html_element::htmlCenter(
						$this->View->getHiddens() .
						(isset($_REQUEST["grp"]) ? $this->View->htmlHidden("group", $_REQUEST["grp"]) : "") .
						$this->View->htmlHidden("MAX_FILE_SIZE", "8388608") .
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_newsletter', '[csv_upload]'), $buttons, "100%", "30", "", "hidden")
					)
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLExportCsvMessage($mode = 0){
		if(isset($_REQUEST["lnk"])){
			$link = $_REQUEST["lnk"];
		}

		if(isset($link)){
			$down = getServerUrl() . $link;

			$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 7, 1);

			$table->setCol(0, 0, array(), we_html_tools::getPixel(5, 5));

			$table->setCol(1, 0, array("class" => "defaultfont"), sprintf(g_l('modules_newsletter', '[csv_export]'), $link));

			$table->setCol(2, 0, array(), we_html_tools::getPixel(5, 10));

			$table->setCol(3, 0, array("class" => "defaultfont"), weBackupWizard::getDownloadLinkText());
			$table->setCol(4, 0, array(), we_html_tools::getPixel(5, 10));
			$table->setCol(5, 0, array("class" => "defaultfont"), we_html_element::htmlA(array("href" => $down), g_l('modules_newsletter', '[csv_download]')
				)
			);
			$table->setCol(6, 0, array(), we_html_tools::getPixel(100, 5));

			if($mode == 1){

				$table->addRow(3);
				$table->setCol(7, 0, array(), we_html_tools::getPixel(100, 10));
				$table->setCol(8, 0, array("class" => "defaultfont"), we_html_element::htmlB(g_l('modules_newsletter', '[clearlog_note]')));
				$table->setCol(9, 0, array(), we_html_tools::getPixel(100, 15));
				$cancel = we_button::create_button("cancel", "javascript:self.close();");
				$ok = we_button::create_button("ok", "javascript:clearLog();");
			} else{
				$close = we_button::create_button("close", "javascript:self.close();");
			}


			$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "group", "value" => (isset($group) ? $group : ""))) .
						(($mode == 1) ?
							$this->View->htmlHidden("pnt", "clear_log") .
							$this->View->htmlHidden("ncmd", "do_clear_log") .
							we_html_tools::htmlDialogLayout(
								$table->getHtml(), g_l('modules_newsletter', '[clear_log]'), we_button::position_yes_no_cancel($ok, null, $cancel), "100%", "30", "", "hidden") :
							we_html_tools::htmlDialogLayout(
								$table->getHtml(), g_l('modules_newsletter', '[csv_download]'), we_button::position_yes_no_cancel(null, $close, null), "100%", "30", "", "hidden")
						) .
						we_html_element::jsElement("self.focus();")
					)
			);

			return ($mode == 1 ? $body : $this->getHTMLDocument($body));
		}
	}

	/**
	 * Edit csv mail list
	 *
	 * @package weModules
	 * @subpackage Newsletter
	 * @param String $open_file
	 * @return String
	 */
	function getHTMLEditFile($open_file = ""){
		$out = "";
		$content = array();

		$order = isset($_REQUEST["order"]) ? $_REQUEST["order"] : "";
		for($i = 0; $i < 14; $i = $i + 2){
			$sorter_code[$i] = "<br/>" . ($order == $i ?
					we_html_element::htmlInput(array("type" => "radio", "value" => $i, "name" => "order", "checked" => true, "onclick" => "submitForm('edit_file')")) . "&darr;" :
					we_html_element::htmlInput(array("type" => "radio", "value" => $i, "name" => "order", "onclick" => "submitForm('edit_file')")) . "&darr;"
				);
			$sorter_code[$i + 1] = ($order == $i + 1 ?
					we_html_element::htmlInput(array("type" => "radio", "value" => $i + 1, "name" => "order", "checked" => true, "onclick" => "submitForm('edit_file')")) . "&uarr;" :
					we_html_element::htmlInput(array("type" => "radio", "value" => $i + 1, "name" => "order", "onclick" => "submitForm('edit_file')")) . "&uarr;"
				);
		}

		$headlines = array(
			array("dat" => 'ID' . $sorter_code[0] . $sorter_code[1], "width" => 20),
			array("dat" => g_l('modules_newsletter', '[email]') . $sorter_code[2] . $sorter_code[3], "width" => 50),
			array("dat" => g_l('modules_newsletter', '[edit_htmlmail]') . $sorter_code[4] . $sorter_code[5], "width" => "50"),
			array("dat" => g_l('modules_newsletter', '[salutation]') . $sorter_code[6] . $sorter_code[7]),
			array("dat" => g_l('modules_newsletter', '[title]') . $sorter_code[8] . $sorter_code[9]),
			array("dat" => g_l('modules_newsletter', '[firstname]') . $sorter_code[10] . $sorter_code[11]),
			array("dat" => g_l('modules_newsletter', '[lastname]') . $sorter_code[12] . $sorter_code[13]),
			array("dat" => g_l('modules_newsletter', '[edit]')),
			array("dat" => g_l('modules_newsletter', '[status]')),
		);


		$csv_file = isset($_REQUEST["csv_file"]) ? $_REQUEST["csv_file"] : "";
		$emails = array();
		$emailkey = array();
		if(strpos($csv_file, '..') === false){
			if($csv_file){
				$emails = weNewsletter::getEmailsFromExtern2($csv_file, null, null, null, (isset($_REQUEST['weEmailStatus']) ? $_REQUEST['weEmailStatus'] : 0), $emailkey);
			}
		} else{
			print we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR));
		}

		$offset = isset($_REQUEST["offset"]) ? $_REQUEST["offset"] : 0;
		$art = isset($_REQUEST["art"]) ? $_REQUEST["art"] : "";

		$numRows = isset($_REQUEST["numRows"]) ? $_REQUEST["numRows"] : 15;

		$anz = count($emails);
		$offset = ($offset < 0 ? 0 : $offset);
		$endRow = $offset + $numRows;
		$endRow = ($endRow > $anz ? $anz : $endRow);

		function cmp0($a, $b){
			return strnatcasecmp($a[0], $b[0]);
		}

		function cmp1($a, $b){
			return strnatcasecmp($a[1], $b[1]);
		}

		function cmp2($a, $b){
			return strnatcasecmp($a[2], $b[2]);
		}

		function cmp3($a, $b){
			return strnatcasecmp($a[3], $b[3]);
		}

		function cmp4($a, $b){
			return strnatcasecmp($a[4], $b[4]);
		}

		function cmp5($a, $b){
			return strnatcasecmp($a[5], $b[5]);
		}

		switch($order){
			case 2:
			case 3:
				uasort($emails, "cmp0");
				break;
			case 4:
			case 5:
				uasort($emails, "cmp1");
				break;
			case 6:
			case 7:
				uasort($emails, "cmp2");
				break;
			case 8:
			case 9:
				uasort($emails, "cmp3");
				break;
			case 10:
			case 11:
				uasort($emails, "cmp4");
				break;
			case 12:
			case 13:
				uasort($emails, "cmp5");
				break;
		}

		switch($order){
			case 0:
			case 2:
			case 4:
			case 6:
			case 8:
			case 10:
			case 12:
				$emails = array_reverse($emails, true);
			default:
				;
		}
		$counter = 0;
		foreach($emails as $k => $cols){
			if($k >= $offset && $k < $endRow){

				$edit = we_button::create_button("image:btn_edit_edit", "javascript:editEmailFile(" . $emailkey[$k] . ",'" . $cols[0] . "','" . $cols[1] . "','" . $cols[2] . "','" . $cols[3] . "','" . $cols[4] . "','" . $cols[5] . "')");
				$trash = we_button::create_button("image:btn_function_trash", "javascript:delEmailFile(" . $emailkey[$k] . ",'" . $cols[0] . "')");

				$content[$counter] = array(
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), $k),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[0] ? $cols[0] : "&nbsp;")),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[1] ? g_l('modules_newsletter', '[yes]') : g_l('modules_newsletter', '[no]'))),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[2] ? $cols[2] : "&nbsp;")),
						"height" => "",
						"align" => "right",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[3] ? $cols[3] : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[4] ? $cols[4] : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[5] ? $cols[5] : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), we_button::create_button_table(array($edit, $trash))),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), we_html_element::htmlImg(array("src" => IMAGE_DIR . "icons/" . (we_check_email($cols[0]) ? "valid.gif" : "invalid.gif")))),
						"height" => "",
						"align" => "center",
					)
				);
				$counter++;
			}
		}

		$js = $this->View->getJSProperty() .
			we_html_element::jsElement('
			self.focus();
			function editEmailFile(eid,email,htmlmail,salutation,title,firstname,lastname){
				new jsWindow("' . $this->frameset . '?pnt=eemail&eid="+eid+"&etyp=2&email="+email+"&htmlmail="+htmlmail+"&salutation="+salutation+"&title="+title+"&firstname="+firstname+"&lastname="+lastname,"edit_email",-1,-1,430,270,true,true,true,true);
			}

			function setAndSave(eid,email,htmlmail,salutation,title,firstname,lastname){

				var fr=document.we_form;
				fr.nrid.value=eid;
				fr.email.value=email;
				fr.htmlmail.value=htmlmail;
				fr.salutation.value=salutation;
				fr.title.value=title;
				fr.firstname.value=firstname;
				fr.lastname.value=lastname;

				fr.ncmd.value="save_email_file";

				submitForm("edit_file");

			}

			function listFile(){
				var fr=document.we_form;
				fr.nrid.value="";
				fr.email.value="";
				fr.htmlmail.value="";
				fr.salutation.value="";
				fr.title.value="";
				fr.firstname.value="";
				fr.lastname.value="";
				fr.offset.value=0;

				submitForm("edit_file");
			}

			function delEmailFile(eid,email){
				var fr=document.we_form;
				if(confirm(sprintf("' . g_l('modules_newsletter', '[del_email_file]') . '",email))){
					fr.nrid.value=eid;
					fr.ncmd.value="delete_email_file";
					submitForm("edit_file");
				}
			}

			function postSelectorSelect(wePssCmd) {
				switch(wePssCmd) {
					case "selectFile":
						listFile();
						break;
				}
			}
		');


		$close = we_button::create_button("close", "javascript:self.close()");
		$edit = we_button::create_button("save", "javascript:listFile()");


		$chooser = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 2, 1);
		$chooser->setCol(0, 0, array(), we_html_tools::getPixel(10, 10));
		$chooser->setCol(1, 0, array(), $this->View->formFileChooser(420, "csv_file", ($open_file != "" ? $open_file : ($csv_file ? $csv_file : "/")), "", "", 'readonly="readonly" onchange="alert(100)"'));
		//$chooser->setCol(2,0,array(),we_html_tools::getPixel(5,15));
		//$chooser->setCol(3,0,array(),we_button::create_button_table(array($close,$edit)));
		//$chooser->setCol(4,0,array(),we_html_tools::getPixel(5,15));


		$nextprev = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0"), 1, 5);

		if($offset){
			$colcontent = we_button::create_button("back", "javascript:document.we_form.offset.value=" . ($offset - $numRows) . ";submitForm('edit_file');");
		} else{
			$colcontent = we_button::create_button("back", "#", false, 100, 22, "", "", true);
		}


		$nextprev->setCol(0, 0, array(), $colcontent);

		$nextprev->setCol(0, 1, array(), we_html_tools::getPixel(10, 5));


		if(($anz - $offset) < $numRows){
			$colcontent = ( $anz ? $offset + 1 : 0 ) . "-" . $anz .
				we_html_tools::getPixel(5, 1) .
				g_l('global', "[from]") .
				we_html_tools::getPixel(5, 1) .
				$anz;
		} else{
			$colcontent = ( $anz ? $offset + 1 : 0 ) . "-" . $offset + $numRows .
				we_html_tools::getPixel(5, 1) .
				g_l('global', "[from]") .
				we_html_tools::getPixel(5, 1) .
				$anz;
		}

		$nextprev->setCol(0, 2, array("class" => "defaultfont"), we_html_element::htmlB($colcontent)
		);

		$nextprev->setCol(0, 3, array(), we_html_tools::getPixel(10, 5));

		$colcontent = (($offset + $numRows) < $anz ?
				we_button::create_button("next", "javascript:document.we_form.offset.value=" . ($offset + $numRows) . ";submitForm('edit_file');") :
				we_button::create_button("next", "#", false, 100, 22, "", "", true)
			);

		$nextprev->setCol(0, 4, array(), $colcontent);

		if(count($emails)){
			$add = we_button::create_button("image:function_plus", "javascript:editEmailFile(" . count($emails) . ",'','','','','','')");
			$end = $nextprev->getHtml();

			$nextprev->addCol(6);

			$nextprev->setCol(0, 5, array(), we_html_tools::getPixel(20, 1));
			$nextprev->setCol(0, 6, array("class" => "defaultfont"), we_html_element::htmlB(g_l('modules_newsletter', '[show]')) . " " . we_html_tools::htmlTextInput("numRows", 5, $numRows)
			);
			$selectStatus = we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", (isset($_REQUEST['weEmailStatus']) ? $_REQUEST['weEmailStatus'] : "0"), "", "onchange='listFile();'", "value", "150");
			$nextprev->setCol(0, 7, array(), we_html_tools::getPixel(20, 1));
			$nextprev->setCol(0, 8, array("class" => "defaultfont"), $selectStatus);
			$nextprev->setCol(0, 9, array(), we_html_tools::getPixel(20, 1));
			$nextprev->setCol(0, 10, array("class" => "defaultfont"), $add
			);

			$out = $nextprev->getHtml() .
				we_html_tools::getPixel(5, 5) .
				we_html_tools::htmlDialogBorder3(850, 300, $content, $headlines) .
				we_html_tools::getPixel(5, 5) .
				$end;
		} else{
			if(!$csv_file && empty($csv_file) && strlen($csv_file) < 4){
				$_nlMessage = g_l('modules_newsletter', '[no_file_selected]');
				$selectStatus2 = '';
			} else{
				if(isset($_REQUEST['weEmailStatus']) && $_REQUEST['weEmailStatus'] == 1){
					$_nlMessage = g_l('modules_newsletter', '[file_all_ok]');
					$selectStatus2 = "<br/>" . we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", (isset($_REQUEST['weEmailStatus']) ? $_REQUEST['weEmailStatus'] : "0"), "", "onchange='listFile();'", "value", "150");
				} else{
					$_nlMessage = g_l('modules_newsletter', '[file_all_ok]');
					$selectStatus2 = '';
				}
			}

			$out = we_html_element::htmlDiv(array("class" => "middlefontgray", "align" => "center"), "--&nbsp;" . $_nlMessage . "&nbsp;--" . $selectStatus2);
			$add = we_button::create_button("image:function_plus", "javascript:editEmailFile(" . count($emails) . ",'','','','','','')");
			$out .= "<br/><br/>" . $add;
		}


		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => ($open_file != "" ? "submitForm('edit_file')" : "" )), we_html_element::htmlForm(array("name" => "we_form"), $this->View->htmlHidden("ncmd", "edit_file") .
					$this->View->htmlHidden("pnt", "edit_file") .
					$this->View->htmlHidden("order", $order) .
					$this->View->htmlHidden("offset", $offset) .
					$this->View->htmlHidden("nrid", "") .
					$this->View->htmlHidden("email", "") .
					$this->View->htmlHidden("htmlmail", "") .
					$this->View->htmlHidden("salutation", "") .
					$this->View->htmlHidden("title", "") .
					$this->View->htmlHidden("firstname", "") .
					$this->View->htmlHidden("lastname", "") .
					$this->View->htmlHidden("etyp", "") .
					$this->View->htmlHidden("eid", "") .
					//we_button::create_button_table(array($close,$edit)).

					we_html_tools::htmlDialogLayout($chooser->getHtml() . '<br>' . $out, g_l('modules_newsletter', '[select_file]'), we_button::create_button_table(array($close, $edit)), "100%", "30", "597")
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLClearLog(){
		we_html_tools::protect();


		if(isset($_REQUEST["ncmd"])){
			if($_REQUEST["ncmd"] == "do_clear_log"){
				$this->View->db->query("DELETE FROM " . NEWSLETTER_LOG_TABLE);
				return
					we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
					we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[log_is_clear]'), we_message_reporting::WE_MESSAGE_NOTICE)
						. 'self.close();'
				);
			}
		}

		$js = we_html_element::jsElement('
			function clearLog(){
					var f = self.document.we_form;
					f.action = "' . $this->frameset . '";
					f.method = "post";
					f.submit();
			}
		');

		$csv = "";
		$this->View->db->query("SELECT " . NEWSLETTER_TABLE . ".Text as NewsletterName, " . NEWSLETTER_LOG_TABLE . ".* FROM " . NEWSLETTER_TABLE . "," . NEWSLETTER_LOG_TABLE . " WHERE " . NEWSLETTER_TABLE . ".ID=" . NEWSLETTER_LOG_TABLE . ".NewsletterID;");
		while($this->View->db->next_record()) {
			$csv.=$this->View->db->f("NewsletterName") . "," . date(g_l('weEditorInfo', "[date_format]"), $this->View->db->f("LogTime")) . "," . (g_l('modules_newsletter', '[' . $this->View->db->f("Log") . ']') !== false ? (sprintf($lg_l('modules_newsletter', '[' . $this->View->db->f("Log") . ']'), $this->View->db->f("Param"))) : $this->View->db->f("Log")) . "\n";
		}

		$link = BACKUP_DIR . "download/log_" . time() . ".csv";
		if(!weFile::save($_SERVER['DOCUMENT_ROOT'] . $link, $csv))
			$link = "";

		$_REQUEST["lnk"] = $link;

		return $this->getHTMLDocument($this->getHTMLExportCsvMessage(1), $js);
	}

	function getHTMLSendWait(){
		$nid = (isset($_REQUEST["nid"]) ? $_REQUEST["nid"] : 0);
		$test = (isset($_REQUEST["test"]) ? $_REQUEST["test"] : 0);

		$js = we_html_element::jsElement('
			self.focus();
		');
		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "setTimeout('document.we_form.submit()',200)"), we_html_element::htmlForm(array("name" => "we_form"), $this->View->htmlHidden("pnt", "send_frameset") .
					$this->View->htmlHidden("nid", $nid) .
					$this->View->htmlHidden("test", $test) .
					we_html_element::htmlCenter(
						we_html_element::htmlImg(array("src" => IMAGE_DIR . "e_busy.gif")) .
						we_html_element::htmlBr() .
						we_html_element::htmlBr() .
						we_html_element::htmlDiv(array("class" => "header_small"), g_l('modules_newsletter', '[prepare_newsletter]'))
					)
				)
		);
		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLSendFrameset(){
		$nid = (isset($_REQUEST["nid"]) ? $_REQUEST["nid"] : 0);

		$test = (isset($_REQUEST["test"]) ? $_REQUEST["test"] : 0);

		$this->View->newsletter = new weNewsletter($nid);
		$ret = $this->View->cacheNewsletter();


		$_offset = ($this->View->newsletter->Offset != 0) ? ($this->View->newsletter->Offset + 1) : 0;
		$_step = $this->View->newsletter->Step;

		if($this->View->settings['send_step'] <= $_offset){
			$_step++;
			$_offset = 0;
		}


		$head = we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
			function yes(){
				doSend(' . $_offset . ',' . $_step . ');
			}

			function no(){
				doSend(0,0);
			}
			function cancel(){
				self.close();
			}

			function ask(start,group){
				new jsWindow("' . $this->View->frameset . '?pnt=qsend&start="+start+"&grp="+group,"send_question",-1,-1,400,200,true,true,true,false);
			}

			function doSend(start,group){
				self.focus();
				top.send_cmd.location="' . $this->frameset . '?pnt=send_cmd&nid=' . $nid . '&test=' . $test . '&blockcache=' . $ret["blockcache"] . '&emailcache=' . $ret["emailcache"] . '&ecount=' . $ret["ecount"] . '&gcount=' . $ret["gcount"] . '&start="+start+"&egc="+group;
			}
			self.focus();
		');

		$frameset = new we_html_frameset(array("framespacing" => "0", "border" => "0", "frameborder" => "no"));
		$noframeset = new we_baseElement("noframes");

		$frameset->setAttributes(array("rows" => "*,0,0", "onLoad" => (($this->View->newsletter->Step != 0 || $this->View->newsletter->Offset != 0) ? "ask(" . $this->View->newsletter->Step . "," . $this->View->newsletter->Offset . ");" : "no();")));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=send_body&test=$test", "name" => "send_body", "scrolling" => "no", "noresize" => null));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=send_cmd", "name" => "send_cmd", "scrolling" => "no"));
		$frameset->addFrame(array("src" => $this->frameset . "?pnt=send_control&nid=$nid&test=$test&blockcache=" . $ret["blockcache"] . "&emailcache=" . $ret["emailcache"] . "&ecount=" . $ret["ecount"] . "&gcount=" . $ret["gcount"], "name" => "send_control", "scrolling" => "no"));

		$body = $frameset->getHtml() . $noframeset->getHTML();

		return $this->getHTMLDocument($body, $head);
	}

	function getHTMLSendBody(){
		$details = "";
		$pro = (isset($_REQUEST["pro"]) ? $_REQUEST["pro"] : 0);

		$pb = new we_progressBar((int) $pro);
		$pb->setStudLen(400);
		$pb->addText(g_l('modules_newsletter', '[sending]'), 0, "title");

		$_textarea = we_html_element::htmlTextarea(array("name" => "details", "cols" => "60", "rows" => "15", "style" => "width:530px;height:300px;"), oldHtmlspecialchars($details));
		$_footer = '<table width="580" border="0" cellpadding="0" cellspacing="0"><tr><td align="left">' .
			$pb->getHTML() . '</td><td align="right">' .
			we_button::create_button("close", "javascript:top.close();") .
			'</td></tr></table>';

		$_content = we_html_tools::htmlDialogLayout($_textarea, g_l('modules_newsletter', '[details]'), $_footer);


		$details = (isset($_REQUEST["test"]) && $_REQUEST["test"] ? g_l('modules_newsletter', '[test_no_mail]') : g_l('modules_newsletter', '[sending]') );

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), $pb->getJS() .
					$_content
				) .
				we_html_element::jsElement('
									document.we_form.details.value="' . $details . '";
									document.we_form.details.value=document.we_form.details.value+"\n"+"' . g_l('modules_newsletter', '[campaign_starts]') . '";
							')
		);


		return $this->getHTMLDocument($body);
	}

	//---------------------------------------------------------------------------------------


	function getHTMLSendCmd(){
		if(isset($_REQUEST["nid"])){
			$nid = $_REQUEST["nid"];
		} else{
			return;
		}

		$test = (isset($_REQUEST["test"]) ? $_REQUEST["test"] : 0);
		$start = (isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0);

		// to calc progress ------------------
		// total number of emails
		$ecount = (isset($_REQUEST["ecount"]) ? $_REQUEST["ecount"] : 0);
		// counter
		$ecs = (isset($_REQUEST["ecs"]) ? $_REQUEST["ecs"] : 0);
		//-----------------------------------

		$blockcache = (isset($_REQUEST["blockcache"]) ? $_REQUEST["blockcache"] : 0);

		// emails cache -----------------------
		$emailcache = (isset($_REQUEST["emailcache"]) ? $_REQUEST["emailcache"] : 0);
		//
		$egc = (isset($_REQUEST["egc"]) ? $_REQUEST["egc"] : 0);
		//
		$gcount = (isset($_REQUEST["gcount"]) ? $_REQUEST["gcount"] : 0);
		//-----------------------------------

		$reload = (isset($_REQUEST["reload"]) ? $_REQUEST["reload"] : 0);
		$retry = (isset($_REQUEST["retry"]) ? $_REQUEST["retry"] : 0);


		$this->View->newsletter = new weNewsletter($nid);
		if($retry){
			$egc = $this->View->newsletter->Step;
			$start = $this->View->newsletter->Offset;
			if($start){
				$start++;
			}
			$this->View->newsletter->addLog("retry");
			print "RETRY $nid: $egc-$ecs<br>";
			flush();
		}


		$js = we_html_element::jsElement('
			function updateText(text){
				top.send_body.document.we_form.details.value=top.send_body.document.we_form.details.value+"\n"+text;
			}

			function checkTimeout(){
				return document.we_form.ecs.value;
			}

			function initControl(){
				if(top.send_control.init) top.send_control.init();
			}

			self.focus();

		');

		$body = we_html_element::htmlBody(array("marginwidth" => "10", "marginheight" => "10", "leftmargin" => "10", "topmargin" => "10", "onLoad" => "initControl()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden(array("name" => "nid", "value" => $nid)) .
					we_html_element::htmlHidden(array("name" => "pnt", "value" => "send_cmd")) .
					we_html_element::htmlHidden(array("name" => "test", "value" => $test)) .
					we_html_element::htmlHidden(array("name" => "blockcache", "value" => $blockcache)) .
					we_html_element::htmlHidden(array("name" => "emailcache", "value" => $emailcache)) .
					we_html_element::htmlHidden(array("name" => "ecount", "value" => $ecount)) .
					we_html_element::htmlHidden(array("name" => "gcount", "value" => $gcount)) .
					we_html_element::htmlHidden(array("name" => "egc", "value" => $egc + 1)) .
					we_html_element::htmlHidden(array("name" => "ecs", "value" => $ecs)) .
					we_html_element::htmlHidden(array("name" => "reload", "value" => 1))
				)
		);
		print $this->getHTMLDocument($body, $js);
		flush();

		if($gcount <= $egc){
			$cc = 0;
			while(true) {
				if(file_exists(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_p_" . $cc)){
					weFile::delete(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_p_" . $cc);
				} else{
					break;
				}
				if(file_exists(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_h_" . $cc)){
					$_buffer = @unserialize(weFile::load(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_h_" . $cc));
					if(is_array($_buffer) && isset($_buffer['inlines'])){
						foreach($_buffer['inlines'] as $_fn){
							if(file_exists($_fn)){
								weFile::delete($_fn);
							}
						}
					}
					weFile::delete(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_h_" . $cc);
				} else{
					break;
				}
				$cc++;
			}
			print we_html_element::jsElement('
				top.send_control.location="' . HTML_DIR . 'white.html";
				top.send_body.setProgress(100);
				top.send_body.setProgressText("title","<font color=\"#006699\"><b>' . g_l('modules_newsletter', '[finished]') . '</b></font>",2);
				updateText("' . g_l('modules_newsletter', '[campaign_ends]') . '");
			');
			$this->View->db->query("UPDATE " . NEWSLETTER_TABLE . " SET Step='0',Offset='0' WHERE ID=" . $this->View->newsletter->ID);
			if(!$test)
				$this->View->newsletter->addLog("log_end_send");
			return;
		}

		if($start && !$test && !$reload){
			$this->View->newsletter->addLog("log_continue_send");
		} else if(!$test && !$reload){
			$this->View->newsletter->addLog("log_start_send");
		}

		$content = "";

		$emails = $this->View->getFromCache($emailcache . "_" . $egc);
		$end = count($emails);

		for($j = $start; $j < $end; $j++){
			$email = trim($emails[$j][0]);

			$user_groups = explode(",", $emails[$j][6]);
			$user_blocks = $emails[$j][7];

			sort($user_blocks);
			$user_blocks = array_unique($user_blocks);

			$htmlmail = isset($emails[$j][1]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][1])) : "";
			$salutation = isset($emails[$j][2]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][2])) : "";
			$title = isset($emails[$j][3]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][3])) : "";
			$firstname = isset($emails[$j][4]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][4])) : "";
			$lastname = isset($emails[$j][5]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][5])) : "";
			$customerid = isset($emails[$j][8]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][8])) : "";
			if(isset($emails[$j][9]) && $emails[$j][9] == 'customer'){
				$iscustomer = 'C';
			} else{
				$iscustomer = '';
			}

			$contentDefault = "";
			$content_plainDefault = "";
			$contentF = "";
			$contentF_plain = "";
			$contentM = "";
			$contentM_plain = "";
			$contentTFL = "";
			$contentTFL_plain = "";
			$contentTL = "";
			$contentTL_plain = "";
			$contentFL = "";
			$contentFL_plain = "";
			$contentLN = "";
			$contentLN_plain = "";
			$contentFN = "";
			$contentFN_plain = "";

			$atts = array();

			foreach($user_groups as $user_group){
				$atts = array_merge($atts, $this->View->getAttachments($user_group));
			}

			$inlines = array();

			foreach($user_blocks as $user_block){

				$html_block = $this->View->getFromCache($blockcache . "_h_" . $user_block);
				$plain_block = $this->View->getFromCache($blockcache . "_p_" . $user_block);

				$contentDefault.=$html_block["default" . $iscustomer];
				$content_plainDefault.=$plain_block["default" . $iscustomer];

				$contentF.=$html_block["female" . $iscustomer];
				$contentF_plain.=$plain_block["female" . $iscustomer];

				$contentM.=$html_block["male" . $iscustomer];
				$contentM_plain.=$plain_block["male" . $iscustomer];

				$contentTFL.=$html_block["title_firstname_lastname" . $iscustomer];
				$contentTFL_plain.=$plain_block["title_firstname_lastname" . $iscustomer];

				$contentTL.=$html_block["title_lastname" . $iscustomer];
				$contentTL_plain.=$plain_block["title_lastname" . $iscustomer];

				$contentFL.=$html_block["firstname_lastname" . $iscustomer];
				$contentFL_plain.=$plain_block["firstname_lastname" . $iscustomer];

				$contentLN.=$html_block["lastname" . $iscustomer];
				$contentLN_plain.=$plain_block["lastname" . $iscustomer];

				$contentFN.=$html_block["firstname" . $iscustomer];
				$contentFN_plain.=$plain_block["firstname" . $iscustomer];

				foreach($html_block["inlines"] as $k => $v)
					if(!in_array($k, array_keys($inlines)))
						$inlines[$k] = $v;
			}

			if($salutation && $lastname && ($salutation == $this->View->settings[weNewsletter::FEMALE_SALUTATION_FIELD]) && ((!$this->View->settings["title_or_salutation"]) || (!$title))){
				$content = ($title ? preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $contentF) : $contentF);
				$content = str_replace(array('###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###', '###TITLE###',), array($firstname, $lastname, $customerid, $title,), $content);

				$content_plain = ($title ? preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $contentF_plain) : $contentF_plain);
				$content_plain = str_replace(array('###FIRSTNAME###', '###LASTNAME###', '###CUSTOMERID###', '###TITLE###'), array($firstname, $lastname, $customerid, $title), $content_plain);
			} else if($salutation && $lastname && ($salutation == $this->View->settings[weNewsletter::MALE_SALUTATION_FIELD]) && ((!$this->View->settings["title_or_salutation"]) || (!$title))){

				$content = str_replace('###FIRSTNAME###', $firstname, $contentM);
				$content = str_replace('###LASTNAME###', $lastname, $content);
				$content = str_replace('###CUSTOMERID###', $customerid, $content);
				if($title){
					$content = preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $content);
				}
				$content = str_replace('###TITLE###', $title, $content);
				$content_plain = str_replace('###FIRSTNAME###', $firstname, $contentM_plain);
				$content_plain = str_replace('###LASTNAME###', $lastname, $content_plain);
				$content_plain = str_replace('###CUSTOMERID###', $customerid, $content_plain);
				if($title){
					$content_plain = preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $content_plain);
				}
				$content_plain = str_replace('###TITLE###', $title, $content_plain);
			} else if($title && $firstname && $lastname){

				$content = str_replace('###FIRSTNAME###', $firstname, $contentTFL);
				$content = str_replace('###LASTNAME###', $lastname, $content);
				$content = str_replace('###CUSTOMERID###', $customerid, $content);
				$content = preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $content);
				$content = str_replace('###TITLE###', $title, $content);
				$content_plain = str_replace('###FIRSTNAME###', $firstname, $contentTFL_plain);
				$content_plain = str_replace('###LASTNAME###', $lastname, $content_plain);
				$content_plain = str_replace('###CUSTOMERID###', $customerid, $content_plain);
				$content_plain = preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $content_plain);
				$content_plain = str_replace('###TITLE###', $title, $content_plain);
			} else if($title && $lastname){

				$content = str_replace('###FIRSTNAME###', $firstname, $contentTL);
				$content = str_replace('###LASTNAME###', $lastname, $content);
				$content = str_replace('###CUSTOMERID###', $customerid, $content);
				$content = preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $content);
				$content = str_replace('###TITLE###', $title, $content);
				$content_plain = str_replace('###FIRSTNAME###', $firstname, $contentTL_plain);
				$content_plain = str_replace('###LASTNAME###', $lastname, $content_plain);
				$content_plain = str_replace('###CUSTOMERID###', $customerid, $content_plain);
				$content_plain = preg_replace('|([^ ])###TITLE###|', '\1 ' . $title, $content_plain);
				$content_plain = str_replace('###TITLE###', $title, $content_plain);
			} else if($lastname && $firstname){

				$content = str_replace('###FIRSTNAME###', $firstname, $contentFL);
				$content = str_replace('###LASTNAME###', $lastname, $content);
				$content = str_replace('###CUSTOMERID###', $customerid, $content);
				$content_plain = str_replace('###FIRSTNAME###', $firstname, $contentFL_plain);
				$content_plain = str_replace('###LASTNAME###', $lastname, $content_plain);
				$content_plain = str_replace('###CUSTOMERID###', $customerid, $content_plain);
			} else if($firstname){

				$content = str_replace('###FIRSTNAME###', $firstname, $contentFN);
				$content = str_replace('###CUSTOMERID###', $customerid, $content);
				$content_plain = str_replace('###FIRSTNAME###', $firstname, $contentFN_plain);
				$content_plain = str_replace('###CUSTOMERID###', $customerid, $content_plain);
			} else if($lastname){

				$content = str_replace('###LASTNAME###', $lastname, $contentLN);
				$content = str_replace('###CUSTOMERID###', $customerid, $content);
				$content_plain = str_replace('###LASTNAME###', $lastname, $contentLN_plain);
				$content_plain = str_replace('###CUSTOMERID###', $customerid, $content_plain);
			} else{

				$content = $contentDefault;
				$content_plain = $content_plainDefault;
			}

			$content_plain = str_replace('###EMAIL###', $email, $content_plain);
			$content = str_replace('###EMAIL###', $email, $content);

			// damd: Newsletter Platzhalter ersetzten
			$this->replacePlaceholder($content, $content_plain, $emails[$j]);

			$_clean = $this->View->getCleanMail($this->View->newsletter->Reply);

			$not_black = !$this->View->isBlack($email); //Bug #5791 Prüfung muss vor der aufbereitung der Adresse erfolgen
			if($lastname && $firstname || $title && $lastname){
				$emailName = ($title ? $title . ' ' : '') .
					($firstname ? $firstname . ' ' : '') .
					$lastname . '<' . $email . '>';
				//$email = $emailName;
			} else{
				$emailName = $email;
			}
			$phpmail = new we_util_Mailer(
					$emailName,
					$this->View->newsletter->Subject,
					$this->View->newsletter->Sender,
					$this->View->newsletter->Reply,
					$this->View->newsletter->isEmbedImages
			);
			$phpmail->setCharSet($this->View->newsletter->Charset != "" ? $this->View->newsletter->Charset : $GLOBALS["_language"]["charset"]);

			if($htmlmail){
				$phpmail->addHTMLPart($content);
				$phpmail->addTextPart(trim($content_plain));
			} else{
				$phpmail->addTextPart(trim($content_plain));
			}

			if(!$this->View->settings["use_base_href"]){
				$phpmail->setIsUseBaseHref($this->View->settings["use_base_href"]);
			}

			foreach($atts as $att){
				$phpmail->doaddAttachment($att);
			}

			$domain = '';
			$not_malformed = ($this->View->settings["reject_malformed"]) ? $this->View->newsletter->check_email($email) : true;
			$verified = ($this->View->settings["reject_not_verified"]) ? $this->View->newsletter->check_domain($email, $domain) : true;

			if($verified && $not_malformed && $not_black){
				if(!$test){
					$phpmail->buildMessage();
					if($phpmail->Send()){
						if($this->View->settings["log_sending"])
							$this->View->newsletter->addLog("mail_sent", $email);
					} else{
						if($this->View->settings["log_sending"])
							$this->View->newsletter->addLog("mail_failed", $email);
						print we_html_element::jsElement('
										updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[error]') . ": " . g_l('modules_newsletter', '[mail_failed]'), $email)) . '");
									');
						flush();
					}
					$this->View->db->query("UPDATE " . NEWSLETTER_TABLE . " SET Step=" . intval($egc) . ",Offset=" . intval($j) . " WHERE ID=" . $this->View->newsletter->ID);
				}
			}elseif(!$not_malformed){
				if(!$test && $this->View->settings["log_sending"]){
					$this->View->newsletter->addLog("email_malformed", $email);
				}
				print we_html_element::jsElement('
								updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[error]') . ": " . g_l('modules_newsletter', '[email_malformed]'), $email)) . '");
								updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[mail_not_sent]'), $email)) . '");
							');
				flush();
			} elseif(!$verified){
				if(!$test && $this->View->settings["log_sending"]){
					$this->View->newsletter->addLog("domain_nok", $email);
				}
				print we_html_element::jsElement('
								updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[warning]') . ": " . g_l('modules_newsletter', '[domain_nok]'), $domain)) . '");
								updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[mail_not_sent]'), $email)) . '");
							');
				flush();
			} elseif(!$not_black){
				if(!$test && $this->View->settings["log_sending"])
					$this->View->newsletter->addLog("email_is_black", $email);
				print we_html_element::jsElement('
								updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[warning]') . ": " . g_l('modules_newsletter', '[email_is_black]'), $email)) . '");
								updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[mail_not_sent]'), $email)) . '");
							');
				flush();
			}
			$ecs++;

			print we_html_element::jsElement('
				document.we_form.ecs.value=' . $ecs . ';
				top.send_control.document.we_form.ecs.value=' . $ecs . ';
			');

			$pro = ($ecount ? ($ecs / $ecount) * 100 : 0);

			print we_html_element::jsElement('top.send_body.setProgress(' . ((int) $pro) . ');');
			flush();
		}

		weFile::delete(WE_NEWSLETTER_CACHE_DIR . $emailcache . "_" . $egc);
		$laststep = ceil($_REQUEST["ecount"] / $this->View->settings["send_step"]);
		if(isset($this->View->settings["send_wait"]) && is_numeric($this->View->settings["send_wait"]) && $this->View->settings["send_wait"] && $_REQUEST['egc'] > 0 && isset($this->View->settings["send_step"]) && is_numeric($this->View->settings["send_step"]) && $_REQUEST['egc'] < ceil($_REQUEST["ecount"] / $this->View->settings["send_step"])){
			print we_html_element::jsElement('
				setTimeout("document.we_form.submit()",' . $this->View->settings["send_wait"] . ');
			');
		} else{
			print we_html_element::jsElement('
				document.we_form.submit();
			');
		}
		flush();
	}

	function getHTMLSendControl(){
		$nid = (isset($_REQUEST["nid"]) ? $_REQUEST["nid"] : 0);
		$test = (isset($_REQUEST["test"]) ? $_REQUEST["test"] : 0);
		$gcount = (isset($_REQUEST["gcount"]) ? $_REQUEST["gcount"] : 0);
		$ecount = (isset($_REQUEST["ecount"]) ? $_REQUEST["ecount"] : 0);
		$blockcache = (isset($_REQUEST["blockcache"]) ? $_REQUEST["blockcache"] : 0);
		$ecs = (isset($_REQUEST["ecs"]) ? $_REQUEST["ecs"] : 0);
		$emailcache = (isset($_REQUEST["emailcache"]) ? $_REQUEST["emailcache"] : 0);

		$to = is_numeric($this->View->settings["send_wait"]) ? $this->View->settings["send_wait"] : 0;
		$to += 40000;

		$js = we_html_element::jsElement('
			var to=0;
			var param=0;

			function reinit(){
				top.send_body.document.we_form.details.value=top.send_body.document.we_form.details.value+"\n"+"' . g_l('modules_newsletter', '[retry]') . '...";
				document.we_form.submit();
				startTimeout();
			}

			function init(){
				document.we_form.ecs.value=top.send_cmd.document.we_form.ecs.value;
				startTimeout();
			}

			function startTimeout(){
				if(to) stopTimeout();
				to=setTimeout("reload()",' . $to . ');
			}

			function stopTimeout(){
				clearTimeout(to);
			}

			function reload(){
				chk=document.we_form.ecs.value;
				if(parseInt(chk)>parseInt(param) && parseInt(chk)!=0){
					param=chk;
					startTimeout();
				}
				else{
					reinit();
				}
			}

			self.focus();
		');

		$body = we_html_element::htmlBody(array("marginwidth" => "10", "marginheight" => "10", "leftmargin" => "10", "topmargin" => "10", "onLoad" => "startTimeout()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "send_cmd", "action" => $this->frameset), we_html_element::htmlHidden(array("name" => "nid", "value" => $nid)) .
					we_html_element::htmlHidden(array("name" => "pnt", "value" => "send_cmd")) .
					we_html_element::htmlHidden(array("name" => "retry", "value" => "1")) .
					we_html_element::htmlHidden(array("name" => "test", "value" => "0")) .
					we_html_element::htmlHidden(array("name" => "blockcache", "value" => $blockcache)) .
					we_html_element::htmlHidden(array("name" => "emailcache", "value" => $emailcache)) .
					we_html_element::htmlHidden(array("name" => "ecount", "value" => $ecount)) .
					we_html_element::htmlHidden(array("name" => "gcount", "value" => $gcount)) .
					we_html_element::htmlHidden(array("name" => "ecs", "value" => $ecs)) .
					we_html_element::htmlHidden(array("name" => "reload", "value" => "0"))
				)
		);
		print $this->getHTMLDocument($body, $js);
		flush();
	}

	/**
	 * returns	a select menu within a html table. to ATTENTION this function is also used in classes object and objectFile !!!!
	 * 			when $withHeadline is true, a table with headline is returned, default is false
	 *
	 * @package weModules
	 * @subpackage Newsletter
	 * @return	select menue to determine charset
	 * @param	boolean
	 */
	function getHTMLCharsetTable(){
		$value = (isset($this->View->newsletter->Charset) ? $this->View->newsletter->Charset : "");

		$charsetHandler = new charsetHandler();

		$charsets = $charsetHandler->getCharsetsForTagWizzard();
		asort($charsets);
		reset($charsets);

		$table = new we_html_table(array("border" => "0", "cellpadding" => "2", "cellspacing" => "0"), 1, 2);
		$table->setCol(0, 0, null, we_html_tools::htmlTextInput("Charset", 15, $value, '', '', 'text', 100));
		$table->setCol(0, 1, null, we_html_tools::htmlSelect("CharsetSelect", $charsets, 1, $value, false, "onblur='document.forms[0].elements[\"Charset\"].value=this.options[this.selectedIndex].value;' onchange='document.forms[0].elements[\"Charset\"].value=this.options[this.selectedIndex].value;'", 'value', 'text', ($this->def_width - 120), false));

		return $table->getHtml();
	}

	/**
	 * Ersetzt die Newsletter Platzthalter
	 *
	 * @author damd
	 * @package weModules
	 * @subpackage Newsletter
	 * @param String $content
	 * @param String $content_plain
	 * @param Array $customerInfos
	 */
	function replacePlaceholder(&$content, &$content_plain, $customerInfos){
		$pattern = "/####PLACEHOLDER:DB::CUSTOMER_TABLE:(.[^#]{1,200})####/";
		preg_match_all($pattern, $content, $placeholderfieldsmatches);
		$placeholderfields = $placeholderfieldsmatches[1];
		unset($placeholderfieldsmatches);

		$fromCustomer = false;
		$placeholderReplaceValue = "";
		if(is_array($customerInfos) && isset($customerInfos[8]) && isset($customerInfos[9]) && $customerInfos[9] == 'customer'){
			$fromCustomer = true;
			$this->View->db->query("SELECT * FROM " . CUSTOMER_TABLE . " WHERE ID=" . intval($customerInfos[8]));
			$this->View->db->next_record();
		}

		foreach($placeholderfields as $phf){
			$placeholderReplaceValue = $fromCustomer ? $this->View->db->f($phf) : "";
			$content = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $content);
			$content_plain = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $this->View->db->f($phf), $content_plain);
		}
	}

}