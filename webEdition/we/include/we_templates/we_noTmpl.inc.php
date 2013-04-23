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
include_once(WE_INCLUDES_PATH . 'we_tag.inc.php');
echo we_html_element::htmlDocType();
?><html>

	<head>
		<?php
		if(isset($we_editmode) && $we_editmode){
			print STYLESHEET;
		}
		if($we_doc->getElement("Charset")){
			print we_html_tools::htmlMetaCtCharset('text/html', $we_doc->getElement("Charset"));
		}
		if($we_doc->getElement("Keywords")){
			?>
			<meta name="keywords" content="<?php print $we_doc->getElement("Keywords") ?>">
		<?php
		}
		if($we_doc->getElement("Description")){
			?>
			<meta name="description" content="<?php print $we_doc->getElement("Description") ?>">
		<?php } ?>
		<title><?php print $we_doc->getElement("Title") ?></title>
		<?php
		if(isset($we_editmode) && $we_editmode){
			echo we_html_element::jsScript(JS_DIR . 'windows.js');
			include_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
		} else{
			print we_tag("textarea", array("name" => "HEAD"));
		}
		?>
	</head>
<?php if(isset($we_editmode) && $we_editmode){ ?>
		<body bgcolor="white" marginwidth="15" marginheight="15" leftmargin="15" topmargin="15">
			<form name="we_form" method="post"><?php
	echo we_class::hiddenTrans();
	$foo = '<html><head>' .
		($we_doc->getElement("Keywords") ?
			we_html_element::htmlMeta(array('name' => 'keywords', 'content' => $we_doc->getElement("Keywords"))) . "\n" : '') .
		($we_doc->getElement("Charset") ?
			we_html_tools::htmlMetaCtCharset('text/html', $we_doc->getElement("Charset")) . "\n" : '') .
		($we_doc->getElement("Description") ?
			we_html_element::htmlMeta(array('name' => 'description', 'content' => $we_doc->getElement("Description"))) . "\n" : '');

	$foo = '<pre class="defaultfont">' . oldHtmlspecialchars($foo . we_html_element::htmlTitle($we_doc->getElement("Title"))) . '
</pre>
	' . we_tag("textarea", array("name" => "HEAD", "rows" => "8", "cols" => 80, "wrap" => "virtual", "style" => "width: 600px;")) . '<br>
<pre class="defaultfont">	&lt;/head&gt;
	&lt;body ' . we_tag("input", array("type" => "text", "size" => "60", "name" => "BODYTAG", "style" => "width: 480px;")) . '&gt;</pre>
' . we_tag("textarea", array("name" => "BODY", "rows" => "15", "cols" => 80, "wrap" => "virtual", "style" => "width: 600px;")) . '
<pre class="defaultfont">	&lt;/body&gt;
&lt;/html&gt;</pre>';
	print we_html_tools::htmlMessageBox(667, 650, $foo);
	?>
			</form>
		</body>
		<?php } else{ ?>
		<body<?php print " " . we_tag("input", array("name" => "BODYTAG")); ?>>
		<?php printElement(we_tag("textarea", array("name" => "BODY"), "")); ?>
		</body>
<?php } ?>
</html>
