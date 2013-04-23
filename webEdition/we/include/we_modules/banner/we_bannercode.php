<?php
/**
 * webEdition CMS
 *
 * $Rev: 5555 $
 * $Author: mokraemer $
 * $Date: 2013-01-11 21:54:58 +0100 (Fri, 11 Jan 2013) $
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
we_html_tools::htmlTop(g_l('modules_banner', '[bannercode]'));
print STYLESHEET;

$code = '';
$ok = isset($_REQUEST["ok"]) ? $_REQUEST["ok"] : "";
$type = isset($_REQUEST["type"]) ? $_REQUEST["type"] : "";
$tagname = isset($_REQUEST["tagname"]) ? $_REQUEST["tagname"] : "";
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : "";
$target = isset($_REQUEST["target"]) ? $_REQUEST["target"] : "";
$width = isset($_REQUEST["width"]) ? $_REQUEST["width"] : 468;
$height = isset($_REQUEST["height"]) ? $_REQUEST["height"] : 60;
$paths = isset($_REQUEST["paths"]) ? $_REQUEST["paths"] : "";
$getscript = isset($_REQUEST["getscript"]) ? $_REQUEST["getscript"] : getServerUrl() . WEBEDITION_DIR . "getBanner.php";
$clickscript = isset($_REQUEST["clickscript"]) ? $_REQUEST["clickscript"] : getServerUrl() . WEBEDITION_DIR . "bannerclick.php";

if($ok){
//FIXME: replace by call of jsScript
	if($type == "js"){
		$code = we_html_element::jsElement('
r = Math.random();
document.write ("<" + "script type=\"text/javascript\" src=\"' . $getscript . '?r="+r+"&amp;bannername=' . rawurlencode($tagname) . '&amp;paths=' . rawurlencode($paths) . '&amp;type=js&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;height=' . rawurlencode($height) . '&amp;width=' . rawurlencode($width) . '&amp;page=' . rawurlencode($page) . '"+(document.referer ? ("&amp;referer="+escape(document.referer)) : "")+"\"><" + "/script>");

') . '<noscript><a href="' . $clickscript . '?u=' . md5(uniqid('', true)) . '&amp;bannername=' . rawurlencode($tagname) . '&amp;page=' . rawurlencode($page) . '" target="' . $target . '"><img src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;paths=' . rawurlencode($paths) . '&amp;page=' . rawurlencode($page) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;c=1" border="0" alt="" width="' . $width . '" height="' . $height . '" /></a></noscript>';
	} else{
		$code = '<iframe
	src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;type=iframe&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;width=' . rawurlencode($width) . '&amp;height=' . rawurlencode($height) . '&amp;page=' . rawurlencode($page) . '"
	width="' . $width . '"
	height="' . $height . '"
	vspace=0
	frameborder=0
	scrolling=no
	align=center
><ilayer
	src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;type=iframe&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($clickscript) . '&amp;width=' . rawurlencode($width) . '&amp;height=' . rawurlencode($height) . '&amp;page=' . rawurlencode($page) . '"
	width="' . $width . '"
	height="' . $height . '"
></ilayer><nolayer><a href="' . $clickscript . '?u=' . md5(uniqid('', true)) . '&amp;bannername=' . rawurlencode($tagname) . '&amp;page=' . rawurlencode($page) . '" target="' . $target . '"><img src="' . $getscript . '?bannername=' . rawurlencode($tagname) . '&amp;paths=' . rawurlencode($paths) . '&amp;page=' . rawurlencode($page) . '&amp;bannerclick=' . rawurlencode($clickscript) . '" border="0" alt="" width="' . $width . '" height="' . $height . '" /></a>
</nolayer>
</iframe>';
	}
}
?>

<script type="text/javascript">

	self.focus();


	function checkForm(f){
		if(f.tagname.value==""){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_tagname_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			f.tagname.focus();
			f.tagname.select();
			return false;
		}
		if(f.page.value==""){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_page_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			f.page.focus();
			f.page.select();
			return false;
		}
		if(f.width.value==""){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_width_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			f.width.focus();
			f.width.select();
			return false;
		}
		if(f.height.value==""){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_height_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			f.height.focus();
			f.height.select();
			return false;
		}
		if(f.getscript.value==""){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_getscript_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			f.getscript.focus();
			f.getscript.select();
			return false;
		}
		if(f.clickscript.value==""){
<?php print we_message_reporting::getShowMessageCall(g_l('modules_banner', '[error_clickscript_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
			f.clickscript.focus();
			f.clickscript.select();
			return false;
		}
		return true;
	}
</script>


</head>
<body class="weDialogBody"<?php if($ok){ ?> onLoad="document.we_form.code.focus();document.we_form.code.select();"<?php } ?>>
	<form onSubmit="return checkForm(this);" name="we_form" action="<?php print $_SERVER["SCRIPT_NAME"]; ?>" method="get"><input type="hidden" name="ok" value="1" /><input type="hidden" name="we_cmd[0]" value="<?php print $_REQUEST['we_cmd'][0]; ?>" />
		<?php
		$typeselect = '<select name="type" size="1">
<option' . (($type == "js") ? " selected" : "") . '>js</option>
<option' . (($type == "iframe") ? " selected" : "") . '>iframe</option>
</select>';

		$content = '<table border="0" cellpadding="0" cellspacing="0">
';
		if(!$ok){
			$content.= '	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[type]') . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . $typeselect . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[tagname]') . '*</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("tagname", 40, $tagname, "", "", "text", 300) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[pageurl]') . '*</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("page", 40, $page, "", "", "text", 300) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[target]') . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("target", 40, $target, "", "", "text", 300) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[width]') . '*</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("width", 40, $width, "", "", "text", 300) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[height]') . '*</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("height", 40, $height, "", "", "text", 300) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[paths]') . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("paths", 40, $paths, "", "", "text", 300) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[getscript]') . '*</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("getscript", 40, $getscript, "", "", "text", 300) . '</td>
	</tr>
	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultfont">' . g_l('modules_banner', '[clickscript]') . '*</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td class="defaultfont">' . we_html_tools::htmlTextInput("clickscript", 40, $clickscript, "", "", "text", 300) . '</td>
	</tr>
';
		}
		if($ok){
			$content .= '	<tr>
		<td colspan="3">' . we_html_tools::getPixel(10, 10) . '</td>
	</tr>
	<tr>
		<td colspan="3" class="defaultfont"><textarea name="code" rows="8" cols="40" style="width:430px;height:300px">' . oldHtmlspecialchars($code) . '</textarea></td>
	</tr>
';
		}
		$content .= '</table>' . (($ok) ? "" : '<p class="defaultfont">*' . g_l('modules_banner', '[required]')) . '</p>';
		$cancel_button = we_button::create_button("cancel", "javascript:top.close();");
		$ok_button = we_button::create_button("ok", "form:submit:we_form");
		$back_button = we_button::create_button("back", "javascript:history.back();");
		$close_button = we_button::create_button("close", "javascript:top.close();");

		$buttons = $ok ? we_button::position_yes_no_cancel($close_button, null, $back_button) : we_button::position_yes_no_cancel($ok_button, null, $cancel_button);

		print we_html_tools::htmlDialogLayout($content, $ok ? g_l('modules_banner', '[bannercode_copy]') : g_l('modules_banner', '[bannercode_ext]'), $buttons);
		?>
	</form>
</body>
</html>