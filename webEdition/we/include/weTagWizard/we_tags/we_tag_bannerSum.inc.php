<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->Module = 'banner';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('views', false, ''), new weTagDataOption('clicks', false, ''), new weTagDataOption('rate', false, '')), true, '');
