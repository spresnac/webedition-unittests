<?php
/**
 * webEdition CMS
 *
 * $Rev: 5080 $
 * $Author: mokraemer $
 * $Date: 2012-11-06 18:45:46 +0100 (Tue, 06 Nov 2012) $
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
we_html_tools::htmlTop('ping');
if($_SESSION["user"]["ID"]){
	$GLOBALS['DB_WE']->query("UPDATE " . USER_TABLE . " SET Ping=UNIX_TIMESTAMP(NOW()) WHERE ID=" . $_SESSION["user"]["ID"]);
	$GLOBALS['DB_WE']->query('UPDATE ' . LOCK_TABLE . ' SET lockTime=NOW() + INTERVAL ' . (PING_TIME + PING_TOLERANZ) . ' SECOND WHERE UserID=' . intval($_SESSION["user"]["ID"]) . ' AND sessionID="' . session_id() . '"');
}

echo we_html_element::jsScript(JS_DIR . 'libs/yui/yahoo-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/event-min.js') .
 we_html_element::jsScript(JS_DIR . 'libs/yui/connection-min.js');
?>
<script  type="text/javascript">
	<!--

	var ajaxURL = "<?php echo WEBEDITION_DIR; ?>rpc/rpc.php";
	var weRpcFailedCnt = 0;
	var ajaxCallback = {
		success: function(o) {
			if(typeof(o.responseText) != 'undefined' && o.responseText != '') {
				eval("var result=" + o.responseText);
				if (result.Success) {
					var num_users = result.DataArray.num_users;
					weRpcFailedCnt = 0;
					if (top.weEditorFrameController) {
						var _ref = top.weEditorFrameController.getActiveDocumentReference();
						if (_ref && _ref.setUsersOnline && _ref.setUsersListOnline) {
							_ref.setUsersOnline(num_users);
							var usersHTML = result.DataArray.users;
							if (usersHTML) {
								_ref.setUsersListOnline(usersHTML);
							}
						}
					}
<?php if(defined("MESSAGING_SYSTEM")){ ?>
						if (top.header_msg_update) {
							var newmsg_count = result.DataArray.newmsg_count;
							var newtodo_count = result.DataArray.newtodo_count;

							top.header_msg_update(newmsg_count, newtodo_count);
						}

<?php } ?>
				}
			}
		},
		failure: function(o) {
			if(weRpcFailedCnt++ > 5){
				//in this case, rpc failed 5 times, this is severe, user should be in informed!
				alert("<?php echo g_l('global', "[unable_to_call_ping]"); ?>");
			}
		}
	}

	function YUIdoAjax() {
		YAHOO.util.Connect.asyncRequest('POST', ajaxURL, ajaxCallback, 'protocol=json&cmd=Ping');
		setTimeout("YUIdoAjax()",<?php print PING_TIME; ?>*1000);
	}
	//-->
</script>
</head>
<body bgcolor="white" onload="YUIdoAjax();">
</body>
</html>
