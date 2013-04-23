	
include_once('define.conf.php');
// additional information should be inserted in manifest.xml, which is important for future update-services
$metaInfo = array(
	'name'=>'<?php print $CLASSNAME; ?>',
	'classname'=>'<?php print $CLASSNAME; ?>',
	'maintable'=><?php print (isset($TABLECONSTANT) && !empty($TABLECONSTANT)) ? $TABLECONSTANT : '""'; ?>,
	'datasource'=><?php if($DATASOURCE=='table:') print "'table:'.$TABLECONSTANT"; else print "'$DATASOURCE'"?>,
	'startpermission'=>'<?php print $PERMISSIONCONDITION; ?>',
    'supportshooks' => 1, //set to 0 if hooks are not supported by the app, important: do also in in manifest.xml
	'use_we_tblprefix' => 1, //set to 0 if the webEdition table prefix should not be used, important: do also in in manifest.xml 
    'appdisabled'=>0 //set to 1 to disable, important: do also in in manifest.xml
    
);