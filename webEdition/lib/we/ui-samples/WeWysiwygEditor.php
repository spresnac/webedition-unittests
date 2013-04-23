<?php

include_once($_SERVER['DOCUMENT_ROOT'] . LIB_DIR . 'we/core/autoload.php');


$WeWysiwygEditor = new we_ui_controls_WeWysiwygEditor();
$WeWysiwygEditor->setTitle('Title');
$WeWysiwygEditor->setName('WysiwygEditor');
$WeWysiwygEditor->setText('Hallo...');
$WeWysiwygEditor->setDisabled(false);
$WeWysiwygEditor->setHidden(false);
$WeWysiwygEditor->setOnBlur('alert("onBlur");');
$WeWysiwygEditor->setCols(50);
$WeWysiwygEditor->setRows(5);
$WeWysiwygEditor->setAppName('dieApp');
$WeWysiwygEditor->setId('WysiwygEditor'); // Wichtig :die Id sollte identisch sein mit dem Namen

$WeWysiwygEditor2 = new we_ui_controls_Textarea(
		array(
			'title' => 'This is the title of the textarea!',
			'name' => 'WysiwygEditor2',
			'text' => 'Hallo2...',
			'disabled' => true,
			'hidden' => false,
			'width' => 300,
			'height' => 300,
			'id' => 'WysiwygEditor2'
		)
);

$htmlPage = we_ui_layout_HTMLPage::getInstance();

$htmlPage->setTitle('Hallo Welt');

$htmlPage->addElement($WeWysiwygEditor);

$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.setDisabled(&quot;' . $WeWysiwygEditor->getId() . '&quot;, true);">disable</a></div>');
$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.setDisabled(&quot;' . $WeWysiwygEditor->getId() . '&quot;, false);">enable</a></div>');

$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.hide(&quot;' . $WeWysiwygEditor->getId() . '&quot;);">hide</a></div>');
$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.show(&quot;' . $WeWysiwygEditor->getId() . '&quot;);">show</a></div>');

$htmlPage->addHTML("<br/>--------------<br/><br/>");

$htmlPage->addElement($WeWysiwygEditor2);

$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.setDisabled(&quot;' . $WeWysiwygEditor2->getId() . '&quot;, true);">disable</a></div>');
$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.setDisabled(&quot;' . $WeWysiwygEditor2->getId() . '&quot;, false);">enable</a></div>');

$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.hide(&quot;' . $WeWysiwygEditor2->getId() . '&quot;);">hide</a></div>');
$htmlPage->addHTML('<div><a href="javascript:we_ui_controls_Textarea.show(&quot;' . $WeWysiwygEditora2->getId() . '&quot;);">show</a></div>');


print $htmlPage->getHTML();
?>