
function we_tag_<?php print $TOOLNAME;?>($attribs,$content){

	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/apps/<?php print $TOOLNAME;?>/conf/define.conf.php');
    if(<?php print $ACTIVECONSTANT;?>){ //check if application is disabled
		return "Hello <?php print $TOOLNAME;?>!";
    }
			
}