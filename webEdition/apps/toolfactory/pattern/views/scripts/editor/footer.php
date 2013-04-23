

$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', '<?php print $TOOLNAME;?>');

$page = we_ui_layout_HTMLPage::getInstance();

$saveButton = new we_ui_controls_Button(
	array(
		'text'		=> $translate->_('Save'), 
		'onClick'	=> 'weCmdController.fire({cmdName: "app_<?php print $TOOLNAME;?>_save"})', 
		'type'		=> 'onClick', 
		'width'		=> 110,
		'disabled'	=> !we_core_Permissions::hasPerm('EDIT_APP_<?php print strtoupper($TOOLNAME);?>'),
		'style'		=> 'margin:9px 0 0 15px;'
	)
);
$unpublishButton = new we_ui_controls_Button(
	array(
		'text'		=> $translate->_('Unpublish'), 
		'onClick'	=> 'weCmdController.fire({cmdName: "app_<?php print $TOOLNAME;?>_unpublish", ignoreHot: "1", followCmd : {cmdName: "app_<?php print $TOOLNAME;?>_open",id: "'.$this->model->ID.'", ignoreHot: "1"}})', 
		'type'		=> 'onClick', 
		'width'		=> 110,
		'disabled'	=> !we_core_Permissions::hasPerm('PUBLISH_APP_<?php print strtoupper($TOOLNAME);?>'),
		'style'		=> 'margin:9px 0 0 15px;'
	)
);
$publishButton = new we_ui_controls_Button(
	array(
		'text'		=> $translate->_('Publish'), 
		'onClick'	=> 'weCmdController.fire({cmdName: "app_<?php print $TOOLNAME;?>_publish", ignoreHot: "1", followCmd : {cmdName: "app_<?php print $TOOLNAME;?>_open",id: "'.$this->model->ID.'", ignoreHot: "1"}})', 
		'type'		=> 'onClick', 
		'width'		=> 110,
		'disabled'	=> !we_core_Permissions::hasPerm('PUBLISH_APP_<?php print strtoupper($TOOLNAME);?>'),
		'style'		=> 'margin:9px 0 0 15px;'
	)
);

$table = new we_ui_layout_Table;
$i=0;

if ($this->model->ContentType !='folder'){
	if (isset($this->model->Published)) {
        if ($this->model->Published){
            $table->addElement($unpublishButton,$i,0);
        } else {
            $table->addElement($publishButton,$i,0);
        }
        $i++;
	}
}
$table->addElement($saveButton, $i, 0);
$page->setBodyAttributes(array('class'=>'weEditorFooter'));
$page->addElement($table);

echo $page->getHTML();
