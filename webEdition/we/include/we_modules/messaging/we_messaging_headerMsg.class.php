<?php

/**
 * webEdition CMS
 *
 * $Rev: 5070 $
 * $Author: mokraemer $
 * $Date: 2012-11-04 23:52:42 +0100 (Sun, 04 Nov 2012) $
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
class we_messaging_headerMsg{

	private static $messaging = 0;

	private static function start(){
		if(is_object(self::$messaging)){
			return;
		}
		self::$messaging = new we_messaging($_SESSION['weS']['we_data']["we_transaction"]);
		self::$messaging->set_login_data($_SESSION["user"]["ID"], $_SESSION["user"]["Username"]);
		self::$messaging->add_msgobj('we_message', 1);
		self::$messaging->add_msgobj('we_todo', 1);
	}

	static function pCSS(){
		print we_html_element::cssElement('
			table.msgheadertable {
				margin:2px 0px 1em auto;
				border-spacing:0px;
				border: none;
			}
			table.msgheadertable td {
				padding:0px;
	}
			table.msgheadertable tr {
				height: 12px;
			}
			');
	}

	static function pJS(){
		self::start();
		?>
		<script type="text/javascript"><!--

			function header_msg_update(newmsg_count, newtodo_count) {
				var msgTD = document.getElementById("msgCount");
				var todoTD = document.getElementById("todoCount");
				var changed=(newmsg_count > msgTD.firstChild.innerHTML)||(newtodo_count > todoTD.firstChild.innerHTML);
				var oldMsg=msgTD.firstChild.innerHTML;
				var oldTodo=todoTD.firstChild.innerHTML;
				msgTD.className = "middlefont" + ( (newmsg_count > 0) ? "red" : "" );
				todoTD.className = "middlefont" + ( (newtodo_count > 0) ? "red" : "" );
				msgTD.firstChild.innerHTML = newmsg_count;
				todoTD.firstChild.innerHTML = newtodo_count;
				if(changed){
					new jsWindow("<?php echo WEBEDITION_DIR;?>newMsg.php?msg="+newmsg_count+"&todo="+newtodo_count+"&omsg="+oldMsg+"&otodo="+oldTodo,"we_delinfo",-1,-1,550,200,true,true,true);
				}
			}
		<?php
		if(defined("MESSAGING_SYSTEM")){
			$newmsg_count = self::$messaging->used_msgobjs['we_message']->get_newmsg_count();
			$newtodo_count = self::$messaging->used_msgobjs['we_todo']->get_newmsg_count();
			?>

					if( top.weEditorFrameController && top.weEditorFrameController.getActiveDocumentReference() && top.weEditorFrameController.getActiveDocumentReference().quickstart && typeof(top.weEditorFrameController.getActiveDocumentReference().setMsgCount)=='function'&&typeof(top.weEditorFrameController.getActiveDocumentReference().setTaskCount)=='function'){
						top.weEditorFrameController.getActiveDocumentReference().setMsgCount(<?php print abs($newmsg_count); ?>);
						top.weEditorFrameController.getActiveDocumentReference().setTaskCount(<?php print abs($newtodo_count); ?>);
					}
		<?php } ?>
			//-->
		</script>
		<?php
	}

	static function pbody(){
		self::start();
		//start with 0 to get popup with new count
		$newmsg_count = 0;//self::$messaging->used_msgobjs['we_message']->get_newmsg_count();
		$newtodo_count = 0;//self::$messaging->used_msgobjs['we_todo']->get_newmsg_count();
		$msg_cmd = "javascript:top.we_cmd('messaging_start', 'message');";
		$todo_cmd = "javascript:top.we_cmd('messaging_start', 'todo');";
		?>
		<table class="msgheadertable">
			<?php echo '
<tr>
	<td id="msgCount" align="right" class="middlefont' . ($newmsg_count ? 'red' : '') . '"><a style="text-decoration:none"  href="' . $msg_cmd . '">' . $newmsg_count . '</a></td>
	<td>' . we_html_tools::getPixel(5, 1) . '</td>
	<td valign="bottom"><a href="' . $msg_cmd . '"><img src="' . IMAGE_DIR . 'modules/messaging/launch_messages.gif" border="0" width="16" height="12" alt="" /></a></td>
</tr>
<tr>
	<td id="todoCount" align="right" class="middlefont' . ($newtodo_count ? 'red' : '') . '"><a style="text-decoration:none" href="' . $todo_cmd . '">' . $newtodo_count . '</a></td>
	<td>' . we_html_tools::getPixel(5, 1) . '</td>
	<td valign="bottom"><a href="' . $todo_cmd . '"><img src="' . IMAGE_DIR . 'modules/messaging/launch_tasks.gif" border="0" width="16" height="12" alt="" /></a></td>
</tr>'
			?>
		</table>
		<?php
	}

}