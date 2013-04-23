<?php
/**
 * webEdition CMS
 *
 * $Rev: 5661 $
 * $Author: mokraemer $
 * $Date: 2013-01-29 22:17:38 +0100 (Tue, 29 Jan 2013) $
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
we_html_tools::htmlTop();

print STYLESHEET;
echo we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript">
	function revertToPublished() {
		if (confirm("<?php print addslashes(g_l('weEditorInfo', "[revert_publish_question]")); ?>")) {
			top.we_cmd("revert_published");
		}
	}


<?php if(isset($_REQUEST['we_cmd'][0]) && $_REQUEST['we_cmd'][0] == 'revert_published'){ ?>

		var _EditorFrame = top.weEditorFrameController.getEditorFrameByTransaction("<?php print $GLOBALS['we_transaction']; ?>");

		_EditorFrame.setEditorIsHot(false);

	<?php print $GLOBALS['we_doc']->getUpdateTreeScript(true); ?>

			_EditorFrame.getDocumentReference().frames[3].location.reload();


<?php } ?>

</script>
</head>
<body class="weEditorBody">
	<?php
	$_html = '
<div class="weMultiIconBoxHeadline" style="margin-bottom:5px;">ID</div>
<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ID ? $GLOBALS['we_doc']->ID : "-") . '</div>
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', "[content_type]") . '</div>
<div style="margin-bottom:10px;">' . g_l('weEditorInfo', '[' . $GLOBALS['we_doc']->ContentType . ']') . '</div>';


	if($GLOBALS['we_doc']->ContentType != "folder"){
		$fs = $GLOBALS['we_doc']->getFilesize();

		$_html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', "[file_size]") . '</div>' .
			'<div style="margin-bottom:10px;">' . round(($fs / 1024), 2) . "&nbsp;KB&nbsp;(" . $fs . "&nbsp;Byte)" . '</div>';
	}
	$parts = array(
		array("headline" => "",
			"html" => $_html,
			"space" => 140,
			"icon" => "doclist/" . we_base_ContentTypes::inst()->getIcon($GLOBALS['we_doc']->ContentType, '', (isset($GLOBALS['we_doc']->Extension) ? $GLOBALS['we_doc']->Extension : "")),
		)
	);

	if($GLOBALS['we_doc']->ContentType != "folder"){
		$_html = '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', "[creation_date]") . '</div>
<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', "[date_format]"), $GLOBALS['we_doc']->CreationDate) . '</div>';

		if($GLOBALS['we_doc']->CreatorID && ($name = f('SELECT CONCAT(First," ",Second," (",username,")") AS name FROM ' . USER_TABLE . ' WHERE ID=' . intval($GLOBALS['we_doc']->CreatorID), 'name', $GLOBALS['DB_WE']))){
			$_html .= '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', "[created_by]") . '</div>
<div style="margin-bottom:10px;">' . $name . '</div>';
		}

		$_html .= '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', "[changed_date]") . '</div>
<div style="margin-bottom:10px;">' . date(g_l('weEditorInfo', "[date_format]"), $GLOBALS['we_doc']->ModDate) . '</div>';


		if($GLOBALS['we_doc']->ModifierID && $name = f('SELECT CONCAT(First," ",Second," (",username,")") AS name FROM ' . USER_TABLE . ' WHERE ID=' . intval($GLOBALS['we_doc']->ModifierID), 'name', $GLOBALS['DB_WE'])){
			$_html .= '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('modules_users', "[changed_by]") . '</div>
<div style="margin-bottom:10px;">' . $name . '</div>';
		}

		if($GLOBALS['we_doc']->ContentType == "text/html" || $GLOBALS['we_doc']->ContentType == "text/webedition"){
			$_html .= '<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', "[lastLive]") . '</div>' .
				'<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->Published ? date(g_l('weEditorInfo', "[date_format]"), $GLOBALS['we_doc']->Published) : "-") . '</div>';

			if($GLOBALS['we_doc']->Published && $GLOBALS['we_doc']->ModDate > $GLOBALS['we_doc']->Published){
				$_html .= '<div style="margin-bottom:10px;">' . we_button::create_button('revert_published', 'javascript:revertToPublished();', true, 280) . '</div>';
			}
		}

		$parts[] = array("headline" => "",
			"html" => $_html,
			"space" => 140,
			"icon" => "cal.gif"
		);

		if($GLOBALS['we_doc']->Table != TEMPLATES_TABLE){
			$rp = $GLOBALS['we_doc']->getRealPath();
			$http = $GLOBALS['we_doc']->getHttpPath();
			$showlink = ($GLOBALS['we_doc']->ContentType == "text/html" ||
				$GLOBALS['we_doc']->ContentType == "text/webedition" ||
				$GLOBALS['we_doc']->ContentType == "image/*" ||
				$GLOBALS['we_doc']->ContentType == "application/x-shockwave-flash" ||
				$GLOBALS['we_doc']->ContentType == "video/quicktime");

			$published = !(($GLOBALS['we_doc']->ContentType == "text/html" || $GLOBALS['we_doc']->ContentType == "text/webedition") && $GLOBALS['we_doc']->Published == 0);

			$_html = '
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', "[local_path]") . '</div>
<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ID == 0 || !$published ? '-' : '<span title="' . oldHtmlspecialchars($rp) . '">' . oldHtmlspecialchars(shortenPath($rp, 74)) . '</span>') . '</div>
<div class="weMultiIconBoxHeadline" style="padding-bottom:5px;">' . g_l('weEditorInfo', "[http_path]") . '</div>
<div style="margin-bottom:10px;">' . ($GLOBALS['we_doc']->ID == 0 || !$published ? '-' : ($showlink ? '<a href="' . $http . '" target="_blank" title="' . oldHtmlspecialchars($http) . '">' : '') . shortenPath($http, 74) . ($showlink ? '</a>' : '')) . '</div>';

			$parts[] = array("headline" => "",
				"html" => $_html,
				"space" => 140,
				"icon" => "path.gif"
			);
		}

		if(defined("WORKFLOW_TABLE") && $GLOBALS['we_doc']->ContentType == "text/webedition"){
			$anzeige = (we_workflow_utility::inWorkflow($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table) ?
					we_workflow_utility::getDocumentStatusInfo($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table) :
					we_workflow_utility::getLogButton($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->Table));

			$parts[] = array("headline" => g_l('modules_workflow', '[workflow]'),
				"html" => $anzeige,
				"space" => 140,
				"forceRightHeadline" => 1,
				"icon" => "workflow.gif"
			);
		}

		switch($GLOBALS['we_doc']->ContentType){
			case 'image/*':
				$_metaData = $GLOBALS['we_doc']->getMetaData();
				$_metaDataTable = '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td style="padding-bottom: 5px;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_exif_data]') . '</td></tr>';
				if(isset($_metaData["exif"])){
					foreach($_metaData["exif"] as $_key => $_val){
						$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_val) . '</td></tr>';
					}
				}
				if(!isset($_metaData["exif"]) || empty($_metaData["exif"])){
					$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont" colspan="2">' . (is_callable("exif_read_data") ? g_l('metadata', '[no_exif_data]') : g_l('metadata', '[no_exif_installed]')) . '</td></tr>';
				}

				$_metaDataTable .= '<tr><td style="padding:10px 0 5px 0;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_iptc_data]') . '</td></tr>';
				if(isset($_metaData["iptc"])){
					foreach($_metaData["iptc"] as $_key => $_val){
						$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($_val) . '</td></tr>';
					}
				}
				if(!isset($_metaData["iptc"]) || empty($_metaData["iptc"])){
					$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont" colspan="2">' . g_l('metadata', '[no_iptc_data]') . '</td></tr>';
				}
				$_metaDataTable .= '</table>';
				break;
			case 'application/*':
				if($GLOBALS['we_doc']->Extension == '.pdf'){
					$metaData = $GLOBALS['we_doc']->getMetaData();
					$_metaDataTable = '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td style="padding-bottom: 5px;" class="weMultiIconBoxHeadline" colspan="2">' . g_l('metadata', '[info_pdf_data]') . '</td></tr>';
					if(!empty($metaData['pdf'])){
						foreach($metaData['pdf'] as $key => $val){
							$_metaDataTable .= '<tr><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($key) . ':</td><td style="padding:0px 5px 5px 0px;" class="defaultfont">' . oldHtmlspecialchars($val) . '</td></tr>';
						}
					}
					$_metaDataTable .= '</table>';
					break;
				}
			//no break;
			default:
				if($GLOBALS['we_doc']->isBinary()){
					$_metaDataTable = g_l('metadata', '[no_metadata_supported]');
				}
		}
		if(isset($_metaDataTable)){
			$parts[] = array("headline" => "",
				"html" => $_metaDataTable,
				"space" => 140,
				"forceRightHeadline" => 1,
				"icon" => "meta.gif"
			);
		}
	}

	print we_multiIconBox::getJS() .
		we_multiIconBox::getHTML("", "100%", $parts, 20, "", -1, "", "", false);
	?>
</body>
</html>
