<?php
/**
 * webEdition CMS
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

we_html_tools::protect(array('ADMINISTRATOR'));

function getInfoTable($_infoArr){
	//recode data - this data might be different than the rest
	foreach($_infoArr as &$tmp){
		if(!mb_check_encoding($tmp, $GLOBALS['WE_BACKENDCHARSET'])){
			$tmp = mb_convert_encoding($tmp, $GLOBALS['WE_BACKENDCHARSET'], 'UTF-8,ISO-8859-15,ISO-8859-1');
		}
		try{
			@$tmp = htmlentities($tmp, ENT_COMPAT, $GLOBALS['WE_BACKENDCHARSET']);
		} catch (Exception $e){
			//try another encoding since last conversion failed.
			@$tmp = htmlentities($tmp, ENT_COMPAT, $GLOBALS['WE_BACKENDCHARSET'] == 'UTF-8' ? 'ISO-8859-15' : 'UTF-8');
		}
	}
	$trans = array('Error type' => 'Type', 'Error message' => 'Text', 'Script name' => 'File', 'Line number' => 'Line', 'Backtrace' => 'Backtrace',
		'Request' => 'Request', 'Server' => 'Server', 'Session' => 'Session', 'Global' => 'Global');
	return '
			<table align="center" bgcolor="#FFFFFF" cellpadding="4" cellspacing="0" style="border: 1px solid #265da6;" width="610">
  <colgroup>
  <col width="10%"/>
  <col width="90%" />
  </colgroup>
  <tr bgcolor="#f7f7f7" valign="top">
  	<td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>#' . $_infoArr['ID'] . '</b></font></td>
    <td  style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2">' . $_infoArr['Date'] . '</font></td>
  </tr>' . '
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Error type:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>' . $_infoArr['Type'] . '</i></font></td>
  </tr>
  <tr valign="top">
    <td  style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Error message:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre>' . $_infoArr['Text'] . '</pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Script name:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>' . $_infoArr['File'] . '</i></font></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6; border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Line number:</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><i>' . $_infoArr['Line'] . '</i></font></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Backtrace</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre>' . $_infoArr['Backtrace'] . '
      </pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Request</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre>' . $_infoArr['Request'] . '</pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Server</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre>' . $_infoArr['Server'] . '</pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Session</b></font></td>
    <td style="border-bottom: 1px solid #265da6;"><pre>' . $_infoArr['Session'] . '
      </pre></td>
  </tr>
  <tr valign="top">
    <td nowrap="nowrap" style="border-bottom: 1px solid #265da6;border-right: 1px solid #265da6;"><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Global</b></font></td>
    <td ><pre>' . $_infoArr['Global'] . '</pre></td>
  </tr>

</table>';
}

function getNavButtons($size, $start){
	if($size > 0){
		$count = 1;


		$back = $start - $count;
		$back = $back < 0 ? 0 : $back;

		$next = $start + $count;
		$next = $next > $size ? $size : $next;

		$div = intval($size / 10);
		if($div == 0){
			$div = 1;
		}
		$nextDiv = $start + $div;
		$prevDiv = $start - $div;

		return '<table style="margin-top: 10px;" border="0" cellpadding="0" cellspacing="0"><tr><td>' .
			we_button::create_button("first", WEBEDITION_DIR . 'errorlog.php?start=' . ($size - 1), true, we_button::WIDTH, we_button::HEIGHT, "", "", ($next >= $size)) . '</td><td>' .
			we_button::getButton("-" . $div, 'btn', "window.location.href='" . WEBEDITION_DIR . "errorlog.php?start=" . $nextDiv . "';", we_button::WIDTH, '', ($nextDiv >= $size)) . '</td><td>' .
			we_button::create_button("back", WEBEDITION_DIR . 'errorlog.php?start=' . $next, true, we_button::WIDTH, we_button::HEIGHT, "", "", ($next >= $size)) .
			we_html_tools::getPixel(23, 1) . "</td><td align='center' class='defaultfont' width='120'><b>" . ($size - $start) .
			"&nbsp;" . g_l('global', '[from]') . " " . ($size) . "</b></td><td>" . we_html_tools::getPixel(23, 1) .
			we_button::create_button("next", WEBEDITION_DIR . 'errorlog.php?start=' . $back, true, we_button::WIDTH, we_button::HEIGHT, "", "", ($start <= 0)) . '</td><td>' .
			we_button::getButton("+" . $div, 'btn2', "window.location.href='" . WEBEDITION_DIR . "errorlog.php?start=" . $prevDiv . "';", we_button::WIDTH, '', ($prevDiv <= 0)) . '</td><td>' .
			we_button::create_button("last", WEBEDITION_DIR . 'errorlog.php?start=0', true, we_button::WIDTH, we_button::HEIGHT, "", "", ($start <= 0)) .
			"</td></tr></table>";
	}
}

$buttons = we_button::position_yes_no_cancel(
		we_button::create_button("delete_all", WEBEDITION_DIR . 'errorlog.php' . "?delete"), we_button::create_button("refresh", WEBEDITION_DIR . 'errorlog.php'), we_button::create_button("close", "javascript:self.close()")
);




$_parts = array();

$db = new DB_WE();
if(isset($_REQUEST['delete'])){
	$db->query('TRUNCATE TABLE `' . ERROR_LOG_TABLE . '`');
}
$size = f('SELECT COUNT(1) as cnt FROM `' . ERROR_LOG_TABLE . '`', 'cnt', $db);
$start = (isset($_REQUEST['start']) ? abs($_REQUEST['start']) : 0);
$start = $start > $size ? $size : $start;

if($size){
	$record = getHash('SELECT * FROM `' . ERROR_LOG_TABLE . '` ORDER By ID DESC LIMIT ' . intval($start) . ',1', $db);
	$_parts[] = array(
		'html' => getInfoTable($record),
		'space' => 10,
	);
} else{
	$_parts[] = array(
		'html' => g_l('global','[no_entries]'),
		'space' => 10,
	);
}

we_html_tools::htmlTop(g_l('javaMenu_global','[showerrorlog]'));
echo we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsElement('function closeOnEscape() {
		return true;
	}
') .
 STYLESHEET;
?>
</head>

<body class="weDialogBody" style="overflow:hidden;" onLoad="self.focus();">
	<div id="info" style="display: block;">
		<?php
		print we_multiIconBox::getJS() .
			we_html_element::htmlDiv(array('style' => 'position:absolute; top:0px; left:30px;right:0px;height:100px;'), getNavButtons($size, $start)) .
			we_html_element::htmlDiv(array('style' => 'position:absolute;top:40px;bottom:0px;left:0px;right:0px;'), we_multiIconBox::getHTML('', 700, $_parts, 30, $buttons, -1, '', '', false, "", "", "", "auto"));
		?>
	</div>
</body>
</html>
