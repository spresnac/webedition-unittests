<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_choiceAttribute('browser', array(new weTagDataOption('ie', false, ''), new weTagDataOption('nn', false, ''), new weTagDataOption('mozilla', false, ''), new weTagDataOption('safari', false, ''), new weTagDataOption('opera', false, ''), new weTagDataOption('lynx', false, ''), new weTagDataOption('konqueror', false, ''),new weTagDataOption('firefox', false, ''),new weTagDataOption('chrome', false, ''), new weTagDataOption('unknown', false, '')), false,false, '');
$this->Attributes[] = new weTagData_choiceAttribute('version', array(new weTagDataOption('eq1', false, ''), new weTagDataOption('eq2', false, ''), new weTagDataOption('eq3', false, ''), new weTagDataOption('eq4', false, ''), new weTagDataOption('eq5', false, ''), new weTagDataOption('eq6', false, ''), new weTagDataOption('eq7', false, ''), new weTagDataOption('eq8', false, ''), new weTagDataOption('eq9', false, ''), new weTagDataOption('up2', false, ''), new weTagDataOption('up3', false, ''), new weTagDataOption('up4', false, ''), new weTagDataOption('up5', false, ''), new weTagDataOption('up6', false, ''), new weTagDataOption('up7', false, ''), new weTagDataOption('up8', false, ''), new weTagDataOption('up9', false, ''), new weTagDataOption('down1', false, ''), new weTagDataOption('down2', false, ''), new weTagDataOption('down3', false, ''), new weTagDataOption('down4', false, ''), new weTagDataOption('down5', false, ''), new weTagDataOption('down6', false, ''), new weTagDataOption('down7', false, ''), new weTagDataOption('down8', false, ''), new weTagDataOption('down9', false, '')), false,true, '');
$this->Attributes[] = new weTagData_choiceAttribute('system', array(new weTagDataOption('win', false, ''), new weTagDataOption('mac', false, ''), new weTagDataOption('unix', false, ''), new weTagDataOption('unknown', false, '')), false,false, '');
