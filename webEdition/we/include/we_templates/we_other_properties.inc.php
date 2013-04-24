<?php
/**
 * webEdition CMS
 *
 * $Rev: 3961 $
 * $Author: mokraemer $
 * $Date: 2012-02-08 13:01:05 +0100 (Wed, 08 Feb 2012) $
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


$parts = array();

array_push($parts,array("icon"=>"path.gif", "headline"=>g_l('weClass',"[path]"),"html"=>$GLOBALS['we_doc']->formPath(),"space"=>140));
array_push($parts,array("icon"=>"doc.gif", "headline"=>g_l('weClass',"[document]"),"html"=>$GLOBALS['we_doc']->formIsSearchable(),"space"=>140));
array_push($parts,array("icon"=>"meta.gif", "headline"=>g_l('weClass',"[metainfo]"),"html"=>$GLOBALS['we_doc']->formMetaInfos(),"space"=>140));
array_push($parts,array("icon"=>"cat.gif", "headline"=>g_l('weClass',"[category]"),"html"=>$GLOBALS['we_doc']->formCategory(),"space"=>140));
array_push($parts,array("icon"=>"user.gif", "headline"=>g_l('weClass',"[owners]"),"html"=>$GLOBALS['we_doc']->formCreatorOwners(),"space"=>140));


print we_multiIconBox::getJS();

print we_multiIconBox::getHTML("weOtherDocProp","100%",$parts,20);