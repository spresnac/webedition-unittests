<?php
/**
 * webEdition CMS
 *
 * $Rev: 3953 $
 * $Author: mokraemer $
 * $Date: 2012-02-07 19:12:45 +0100 (Tue, 07 Feb 2012) $
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
we_html_tools::protect();
we_html_tools::htmlTop(g_l('global', '[question]'));

$yesCmd = "yes_cmd_pressed();";
$cancelCmd = "self.close();";

$nextCmd = $_REQUEST['we_cmd'][1];

$allowedCmds = array("dologout", "close_all_documents");
if(!in_array($nextCmd, $allowedCmds)){
	$nextCmd = "";
}


$ctLngs = '
var ctLngs = new Object();';

foreach(g_l('contentTypes', '') as $key => $lng){
	$ctLngs .= "
	ctLngs[\"$key\"] = \"$lng\";";
}

$untitled = g_l('global', "[untitled]");

print <<< EOFEOF
<script type="text/javascript">

$ctLngs

function yes_cmd_pressed() {

	var allHotDocuments = top.opener.top.weEditorFrameController.getEditorsInUse();
	for (frameId in allHotDocuments) {

		if ( allHotDocuments[frameId].getEditorIsHot() ) {
			allHotDocuments[frameId].setEditorIsHot(false);

		}
	}
	top.opener.top.we_cmd("$nextCmd");
	self.close();
}

function setHotDocuments() {

	var allHotDocuments = top.opener.top.weEditorFrameController.getEditorsInUse();
	var liStr = "";

	var _hotDocumentsOfCt = new Object();

	for (frameId in allHotDocuments) {

		if ( allHotDocuments[frameId].getEditorIsHot() ) {

			if ( !_hotDocumentsOfCt[allHotDocuments[frameId].getEditorContentType()] ) {
				_hotDocumentsOfCt[allHotDocuments[frameId].getEditorContentType()] = new Array();

			}
			_hotDocumentsOfCt[allHotDocuments[frameId].getEditorContentType()].push( allHotDocuments[frameId] );
		}
	}

	for ( ct in _hotDocumentsOfCt ) {

		var liCtElem = document.createElement("li");
		liCtElem.innerHTML = ctLngs[ct];

		var ulCtElem = document.createElement("ul");
		for (var i=0; i<_hotDocumentsOfCt[ct].length; i++) {

			var liPathElem = document.createElement("li");

			if ( _hotDocumentsOfCt[ct][i].getEditorDocumentText() ) {
				liPathElem.innerHTML = _hotDocumentsOfCt[ct][i].getEditorDocumentPath();
			} else {
				liPathElem.innerHTML = "<em>$untitled</em>";
			}

			ulCtElem.appendChild(liPathElem);
		}
		liCtElem.appendChild( ulCtElem );
		document.getElementById("ulHotDocuments").appendChild( liCtElem );
	}
}
</script>
<style type="text/css">
ul {
	list-style-type		: none;
	margin				: 0;
}
#ulHotDocuments {
	font-weight			: bold;
	padding				: 0 0 1px 2px;

}
#ulHotDocuments li {
	padding-top			: 3px;
}
#ulHotDocuments li ul {
	margin				: 0;
	padding				: 0 0 1px 10px;
}
#ulHotDocuments li ul li {
	font-weight			: normal;
}
</style>
EOFEOF;

$content = '
<div>
	' . g_l('alert', "[exit_multi_doc_question]") . '
	<br />
	<br />
	<div style="width: 350px; height: 150px; background: white; overflow: auto;">
		<ul id="ulHotDocuments">

		</ul>
	</div>
</div>
';

print STYLESHEET;
?>
</head>

<body class="weEditorBody" onload="setHotDocuments();" onBlur="self.focus();">
<?php print we_html_tools::htmlYesNoCancelDialog($content, IMAGE_DIR . "alert.gif", true, false, true, $yesCmd, "", $cancelCmd); ?>
</body>

</html>