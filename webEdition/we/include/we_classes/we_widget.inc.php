<?php
/**
 * webEdition CMS
 *
 * $Rev: 3663 $
 * $Author: mokraemer $
 * $Date: 2011-12-27 15:20:42 +0100 (Tue, 27 Dec 2011) $
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


/**
* Class we_widget()
*
* Use this class to add a widget to the Cockpit.
*/
abstract class we_widget {

	/**
	 * To add a widget give a unique id ($iId). Currently supported widget types ($sType) are Shortcuts (sct), RSS Reader (rss),
	 * Last modified (mfd), ToDo/Messaging (msg), Users Online (usr), and Unpublished docs and objs (ubp).
	 *
	 * @param      int $iId
	 * @param      string $sType
	 * @param      object $oContent
	 * @param      array $aLabel
	 * @param      string $sCls
	 * @param      int $iRes
	 * @param      string $sCsv
	 * @param      int $w
	 * @param      int $h
	 * @param      bool $resize
	 * @return     object Returns the we_html_table object
	 */
	static function create($iId,$sType,$oContent,$aLabel=array("",""),$sCls="white",$iRes=0,$sCsv="",$w=0,$h=0,$resize=true){
		$w_i0 = 10;
		$w_i1 = 5;
		$w_icon = (3*$w_i0)+(2*$w_i1);
		$h_i0 = 10;
		$show_seizer = false;
		$w_seizer = 30;
		$h_tb = 16;
		$h_title = 32;
		$wh_edge = 11;
		$gap = 10;

		$oDrag = new we_html_table(array("id"=>$iId."_h","style"=>"width:".($w-$w_icon)."px;height:".$h_tb."px;","cellpadding"=>"0","cellspacing"=>"0","border"=>"0"),1,2);
		$oDrag->setCol(0,0,array("width"=>$w_icon,"height"=>$h_tb,"style"=>"background-image:url(".IMAGE_DIR."pd/tb_pixel.gif);background-repeat:repeat-x;"),$show_seizer? we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/tb_seizer.gif","width"=>$w_seizer,"height"=>$h_tb)) : we_html_tools::getPixel($w_seizer,$h_tb));
		$oDrag->setCol(0,1,array("id"=>$iId."_lbl_old","align"=>"center","class"=>"label","style"=>"width:".($w-(2*$w_icon))."px;height:".$h_tb."px;background-image:url(".IMAGE_DIR."pd/tb_pixel.gif);background-repeat:repeat-x;"),"");

		$oIco_prc = new we_html_table(array("width"=>$w_icon,"height"=>$h_tb,"cellpadding"=>"0","cellspacing"=>"0","border"=>"0"),1,5);
		$oIco_prc->setCol(0,0,array("width"=>$w_i0,"height"=>$h_tb,"valign"=>"middle"),
			we_html_element::htmlA(array("id"=>$iId."_props","href"=>"#","onclick"=>"propsWidget('".$sType."','".$iId."',gel('".$iId."_csv').value);this.blur();"),
				we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/tb_props.gif","width"=>$w_i0,"height"=>$h_i0,"border"=>0,"title"=>g_l('cockpit','[properties]')))));
		$oIco_prc->setCol(0,1,array("width"=>$w_i1,"height"=>$h_tb),we_html_tools::getPixel($w_i1,1));
		$oIco_prc->setCol(0,2,array("width"=>$w_i0,"height"=>$h_tb,"valign"=>"middle"),
			we_html_element::htmlA(array("id"=>$iId."_resize","href"=>"#","onclick"=>"resizeWidget('".$iId."');this.blur();"),
				we_html_element::htmlImg(array("id"=>$iId."_icon_resize","src"=>IMAGE_DIR."pd/tb_resize.gif","width"=>$w_i0,"height"=>$h_i0,"border"=>0,"title"=>(($iRes==0)? g_l('cockpit','[increase_size]'):g_l('cockpit','[reduce_size]'))))));
		$oIco_prc->setCol(0,3,array("width"=>$w_i1,"height"=>$h_tb),we_html_tools::getPixel($w_i1,1));
		$oIco_prc->setCol(0,4,array("width"=>$w_i0,"height"=>$h_tb,"valign"=>"middle"),
			we_html_element::htmlA(array("id"=>$iId."_remove","href"=>"#","onclick"=>"removeWidget('".$iId."');this.blur();"),
				we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/tb_close.gif","width"=>$w_i0,"height"=>$h_i0,"border"=>0,"title"=>g_l('cockpit','[close]')))));

		$oIco_pc = new we_html_table(array("width"=>$w_icon,"height"=>$h_tb,"cellpadding"=>"0","cellspacing"=>"0","border"=>"0"),1,4);
		$oIco_pc->setCol(0,0,array("width"=>($w_i0+$w_i1),"height"=>$h_tb),we_html_tools::getPixel(($w_i0+$w_i1),1));
		$oIco_pc->setCol(0,1,array("width"=>$w_i0,"height"=>$h_tb,"valign"=>"middle"),
			we_html_element::htmlA(array("id"=>$iId."_props","href"=>"#","onclick"=>"propsWidget('".$sType."','".$iId."',gel('".$iId."_csv').value);this.blur();"),
				we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/tb_props.gif","width"=>$w_i0,"height"=>$h_i0,"border"=>0,"title"=>g_l('cockpit','[properties]')))));
		$oIco_pc->setCol(0,2,array("width"=>$w_i1,"height"=>$h_tb),we_html_tools::getPixel($w_i1,1));
		$oIco_pc->setCol(0,3,array("width"=>$w_i0,"height"=>$h_tb,"valign"=>"middle"),
			we_html_element::htmlA(array("id"=>$iId."_remove","href"=>"#","onclick"=>"removeWidget('".$iId."');this.blur();"),
				we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/tb_close.gif","width"=>$w_i0,"height"=>$h_i0,"border"=>0,"title"=>g_l('cockpit','[close]')))));

		$ico_obj = ($resize)? 'oIco_prc' : 'oIco_pc';
		$sIco = ($sType != "_reCloneType_")? we_html_element::htmlDiv(null,$$ico_obj->getHtml()) :
			we_html_element::htmlDiv(array("id"=>$iId."_ico_prc","style"=>"display:block;"),$oIco_prc->getHtml()).
			we_html_element::htmlDiv(array("id"=>$iId."_ico_pc","style"=>"display:none;"),$oIco_pc->getHtml());

		$oTb = new we_html_table(array("id"=>$iId."_tb","style"=>"width:".($w+(2*$wh_edge))."px;height:".$h_tb."px;","cellpadding"=>"0","cellspacing"=>"0","border"=>"0"),1,4);
		$oTb->setCol(0,0,array("width"=>$wh_edge,"height"=>$h_tb),we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/tb_corner_left.gif","width"=>$wh_edge,"height"=>$h_tb)));
		$oTb->setCol(0,1,array("width"=>$w-$w_icon,"height"=>$h_tb,"style"=>"background-image:url(".IMAGE_DIR."pd/tb_pixel.gif);background-repeat:repeat-x;"),$oDrag->getHtml());
		$oTb->setCol(0,2,array("width"=>$w_icon,"height"=>$h_tb,"style"=>"background-image:url(".IMAGE_DIR."pd/tb_pixel.gif);background-repeat:repeat-x;"),$sIco);
		$oTb->setCol(0,3,array("width"=>$wh_edge,"height"=>$wh_edge),we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/tb_corner_right.gif","width"=>$wh_edge,"height"=>$h_tb)));

		$oBox = new we_html_table(array("id"=>$iId."_bx","style"=>"width:".($w+(2*$wh_edge))."px;height:".($h+(2*$wh_edge))."px;","cellpadding"=>"0","cellspacing"=>"0","border"=>"0"),4,3);
		$oBox->setCol(0,0,array("colspan"=>3,"width"=>$wh_edge,"height"=>$h_tb),$oTb->getHtml());
		$oBox->setCol(1,0,array("id"=>$iId."_lbl_mgnl","align"=>"left","width"=>$wh_edge,"height"=>$h_title,"style"=>"background-image:url(".IMAGE_DIR."pd/header_".$sCls.".gif);background-repeat:repeat-x;"),we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/line_v.gif","style"=>"width:1px;height:".$h_title."px;")));
		$oBox->setCol(1,1,array("id"=>$iId."_lbl","class"=>"label","style"=>"width:".$w."px;background-image:url(".IMAGE_DIR."pd/header_".$sCls.".gif);background-repeat:repeat-x;"),we_html_element::jsElement("setLabel('".$iId."','".str_replace("'","\'",$aLabel[0])."','".str_replace("'","\'",$aLabel[1])."');"));
		$oBox->setCol(1,2,array("id"=>$iId."_lbl_mgnr","align"=>"right","width"=>$wh_edge,"height"=>$h_title,"style"=>"background-image:url(".IMAGE_DIR."pd/header_".$sCls.".gif);background-repeat:repeat-x;"),we_html_element::htmlNobr(we_html_tools::getPixel(10,1).we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/line_v.gif","style"=>"width:1px;height:".$h_title."px;"))));
		$oBox->setCol(2,0,array("id"=>$iId."_vll","align"=>"left","width"=>$wh_edge,"height"=>$h,"class"=>"bgc_".$sCls),we_html_element::htmlImg(array("id"=>$iId."_vline_l","src"=>IMAGE_DIR."pd/line_v.gif","style"=>"width:1px;height:".$h."px;")));
		$oBox->setCol(2,1,array("id"=>$iId."_wrapper","style"=>"text-align:left;vertical-align:top;","width"=>$w,"height"=>$h,"class"=>"bgc_".$sCls),
			we_html_tools::getPixel(1,$gap).we_html_element::htmlBr().we_html_element::htmlDiv(array("id"=>$iId."_content"),((isset($oContent))? $oContent->getHtml() : "")).
			we_html_element::htmlHidden(array("id"=>$iId."_prefix","value"=>$aLabel[0])).
			we_html_element::htmlHidden(array("id"=>$iId."_postfix","value"=>$aLabel[1])).
			we_html_element::htmlHidden(array("id"=>$iId."_res","value"=>$iRes)).
			we_html_element::htmlHidden(array("id"=>$iId."_type","value"=>$sType)).
			we_html_element::htmlHidden(array("id"=>$iId."_cls","value"=>$sCls)).
			we_html_element::htmlHidden(array("id"=>$iId."_csv","value"=>$sCsv))
		);
		$oBox->setCol(2,2,array("id"=>$iId."_vlr","align"=>"right","width"=>$wh_edge,"height"=>$h,"class"=>"bgc_".$sCls),we_html_element::htmlNobr(we_html_tools::getPixel(10,1).we_html_element::htmlImg(array("id"=>$iId."_vline_r","src"=>IMAGE_DIR."pd/line_v.gif","style"=>"width:1px;height:".$h."px;"))));
		$oBox->setCol(3,0,array("width"=>$wh_edge,"height"=>$wh_edge),we_html_element::htmlImg(array("id"=>$iId."_img_cl","src"=>IMAGE_DIR."pd/bx_corner_left_".$sCls.".gif","width"=>$wh_edge,"height"=>$wh_edge)));
		$oBox->setCol(3,1,array("id"=>$iId."_bottom","valign"=>"bottom","width"=>"100%","height"=>$wh_edge,"class"=>"bgc_".$sCls),we_html_element::htmlImg(array("src"=>IMAGE_DIR."pd/line_h.gif","width"=>"100%","height"=>1)));
		$oBox->setCol(3,2,array("width"=>$wh_edge,"height"=>$wh_edge),we_html_element::htmlImg(array("id"=>$iId."_img_cr","src"=>IMAGE_DIR."pd/bx_corner_right_".$sCls.".gif","width"=>$wh_edge,"height"=>$wh_edge)));

		return $oBox;
	}

}