<?php
/**
 * webEdition CMS
 *
 * $Rev: 5393 $
 * $Author: mokraemer $
 * $Date: 2012-12-20 16:54:28 +0100 (Thu, 20 Dec 2012) $
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
if(!$_SESSION['user']['Username']){
	session_id;
}

we_html_tools::protect(array('BROWSE_SERVER', 'ADMINISTRATOR'));
we_html_tools::htmlTop();

$docroot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
we_cmd_dec(4);
we_cmd_dec(1);

$filter = (isset($_REQUEST['we_cmd'][2]) && $_REQUEST['we_cmd'][2] != '') ? $_REQUEST['we_cmd'][2] : 'all_Types';
$currentDir = ( isset($_REQUEST['we_cmd'][3]) ?
		($_REQUEST['we_cmd'][3] == '/' ? '' :
			( parse_url($_REQUEST['we_cmd'][3]) === FALSE && is_dir($docroot . $_REQUEST['we_cmd'][3]) ?
				$_REQUEST['we_cmd'][3] :
				str_replace('\\', '/', dirname($_REQUEST['we_cmd'][3])))) :
		'');
$currentName = ($filter != 'folder' ? basename(isset($_REQUEST['we_cmd'][3]) ? $_REQUEST['we_cmd'][3] : '') : '');
if(!file_exists($docroot . $currentDir . '/' . $currentName)){
	$currentDir = '';
	$currentName = '';
}

$currentID = $docroot . $currentDir . ($filter == 'folder' || $filter == 'filefolder' ? '' : (($currentDir != '') ? '/' : '') . $currentName);

$currentID = str_replace('\\', '/', $currentID);
$currentDir = str_replace('\\', '/', $currentDir);

$rootDir = ((isset($_REQUEST['we_cmd'][5]) && $_REQUEST['we_cmd'][5] != '') ? $_REQUEST['we_cmd'][5] : '');
?>
<script type="text/javascript"><!--
	var rootDir="<?php print $rootDir; ?>";
	var currentID="<?php print $currentID; ?>";
	var currentDir="<?php print str_replace($rootDir, '', $currentDir); ?>";
	var currentName="<?php print $currentName; ?>";
	var currentFilter="<?php print str_replace(' ', '%20', g_l('contentTypes', '[' . $filter . ']') !== false ? g_l('contentTypes', '[' . $filter . ']') : ""); ?>";
	var filter = '<?php print $filter; ?>';
	var browseServer = <?php print isset($_REQUEST['we_cmd'][1]) ? 'false' : 'true'; ?>

	var currentType="<?php print ($filter == 'folder') ? 'folder' : ''; ?>";
	var sitepath="<?php print $docroot; ?>";
	var dirsel=1;
	var scrollToVal = 0;
	var allentries = new Array();

	function exit_close(){
<?php if(isset($_REQUEST['we_cmd'][1]) && $_REQUEST['we_cmd'][1] != ""){ ?>
			var foo;
			if(currentID){
				if(currentID == sitepath) foo = "/";
				else foo = currentID.substring(sitepath.length);
			}else{
				foo = "/";
			}

			opener.<?php print $_REQUEST['we_cmd'][1] ?>=foo;
			if(!!opener.postSelectorSelect) {
				opener.postSelectorSelect('selectFile');
			}

	<?php
}
if(isset($_REQUEST['we_cmd'][4]) && $_REQUEST['we_cmd'][4] != ""){
	print $_REQUEST['we_cmd'][4] . ";\n";
}
?>
		close();
	}

	self.focus();

	function closeOnEscape() {
		return true;

	}
	//-->
</script>
<?php echo we_html_element::jsScript(JS_DIR . 'keyListener.js'); ?>
</head>

<frameset rows="73,*,<?php print ( (isset($_REQUEST['we_cmd'][2]) && $_REQUEST['we_cmd'][2] ) ? 60 : 90); ?>,0" border="0" onload="top.fscmd.selectDir()">
  <frame src="we_sselector_header.php?ret=<?php print ( (isset($_REQUEST['we_cmd'][1]) && $_REQUEST['we_cmd'][1]) ? 1 : 0); ?>&filter=<?php print $filter; ?>&currentDir=<?php print $currentDir; ?>" name="fsheader" noresize scrolling="no">
	<frame src="<?php print HTML_DIR; ?>white.html" name="fsbody" noresize scrolling="auto">
	<frame  src="we_sselector_footer.php?ret=<?php print ( (isset($_REQUEST['we_cmd'][1]) && $_REQUEST['we_cmd'][1]) ? 1 : 0); ?>&filter=<?php print $filter; ?>&currentName=<?php print $currentName; ?>" name="fsfooter" noresize scrolling="no">
	<frame src="we_sselector_cmd.php?ret=<?php print ( (isset($_REQUEST['we_cmd'][1]) && $_REQUEST['we_cmd'][1]) ? 1 : 0); ?>&filter=<?php print $filter; ?>&currentName=<?php print $currentName; ?>" name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>
