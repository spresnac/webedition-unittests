<?php

/**
 * webEdition CMS
 *
 * $Rev: 5829 $
 * $Author: mokraemer $
 * $Date: 2013-02-17 15:45:35 +0100 (Sun, 17 Feb 2013) $
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
class MultiFileChooser extends MultiDirChooser{

	var $diabledDelItems = "";
	var $diabledDelReason = "";

	function __construct($width, $ids, $cmd_del, $addbut, $cmd_edit){

		parent::__construct($width, $ids, $cmd_del, $addbut);
		$this->cmd_edit = $cmd_edit;
	}

	function get(){

		$table = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => abs($this->width - 20)), 1, 4);

		$table->setCol(0, 0, array(), we_html_tools::getPixel(20, 3));
		$table->setCol(0, 1, array(), we_html_tools::getPixel(abs($this->width - 101), 3));
		$table->setCol(0, 2, array(), we_html_tools::getPixel(66, 3));
		$table->setCol(0, 2, array(), we_html_tools::getPixel(15, 3));


		$this->nr = 0;
		$ids = (substr($this->ids, 0, 1) == ",") ? substr($this->ids, 1, strlen($this->ids) - 2) : $this->ids;
		$idArr = makeArrayFromCSV($this->ids);
		$c = 1;
		if(!empty($idArr)){
			foreach($idArr as $id){
				$table->addRow();

				$edit = null;
				$trash = null;

				if($this->isEditable() && $this->cmd_edit)
					$edit = we_button::create_button("image:btn_edit_edit", "javascript:if(typeof(_EditorFrame)!='undefined') _EditorFrame.setEditorIsHot(true);we_cmd('" . $this->cmd_edit . "','$id');");

				if(($this->isEditable() && $this->cmd_del) || $this->CanDelete){

					if($this->diabledDelItems != ''){
						$DisArr = makeArrayFromCSV($this->diabledDelItems);
						if(in_array($id, $DisArr)){
							$trash = we_button::create_button("image:btn_function_trash", "javascript:if(typeof(_EditorFrame)!='undefined')_EditorFrame.setEditorIsHot(true);" . ($this->extraDelFn ? $this->extraDelFn : "") . ";we_cmd('" . $this->cmd_del . "','$id');", true, 100, 22, "", "", true);

							$table->setCol($c, 0, array("title" => $this->diabledDelReason), we_html_element::htmlImg(array("src" => ICON_DIR . (@is_dir($id) ? "folder" : "link") . ".gif", "width" => "16", "height" => "18")));
							$table->setCol($c, 1, array("class" => $this->css, "title" => $this->diabledDelReason), $id);
						} else{
							$trash = we_button::create_button("image:btn_function_trash", "javascript:if(typeof(_EditorFrame)!='undefined')_EditorFrame.setEditorIsHot(true);" . ($this->extraDelFn ? $this->extraDelFn : "") . ";we_cmd('" . $this->cmd_del . "','$id');");

							$table->setCol($c, 0, array(), we_html_element::htmlImg(array("src" => ICON_DIR . (@is_dir($id) ? "folder" : "link") . ".gif", "width" => "16", "height" => "18")));
							$table->setCol($c, 1, array("class" => $this->css), $id);
						}
					} else{
						$trash = we_button::create_button("image:btn_function_trash", "javascript:if(typeof(_EditorFrame)!='undefined')_EditorFrame.setEditorIsHot(true);" . ($this->extraDelFn ? $this->extraDelFn : "") . ";we_cmd('" . $this->cmd_del . "','$id');");

						$table->setCol($c, 0, array(), we_html_element::htmlImg(array("src" => ICON_DIR . (@is_dir($id) ? "folder" : "link") . ".gif", "width" => "16", "height" => "18")));
						$table->setCol($c, 1, array("class" => $this->css), $id);
					}
				}


				$table->setCol($c, 2, array('align' => 'right'), we_button::create_button_table(array($edit, $trash))
				);

				$c++;

				$table->addRow();
				$table->setCol($c, 0, array(), we_html_tools::getPixel(20, 3));
				$table->setCol($c, 1, array(), we_html_tools::getPixel(abs($this->width - 101), 3));
				$table->setCol($c, 2, array(), we_html_tools::getPixel(66, 3));
				$table->setCol($c, 3, array(), we_html_tools::getPixel(15, 3));

				$c++;
			}
		} else{
			$table->addRow();
			$table->setCol(1, 0, array(), we_html_tools::getPixel(20, 12));
		}


		$table2 = new we_html_table(array("border" => "0", "cellpadding" => "0", "cellspacing" => "0", "width" => $this->width), 1, 1);

		$table2->setCol(0, 0, array(), we_html_element::htmlDiv(array("style" => "background-color:white;", "class" => "multichooser", "id" => "multi_selector"), $table->getHtml())
		);
		if($this->addbut){
			$table2->addRow(2);
			$table2->setCol(1, 0, array(), we_html_tools::getPixel(2, 5));
			$table2->setCol(2, 0, array("align" => "right"), $this->addbut);
		}

		return $table2->getHtml();
	}

}
