/**
 * webEdition CMS
 *
 * $Rev: 5500 $
 * $Author: lukasimhof $
 * $Date: 2013-01-02 11:06:34 +0100 (Wed, 02 Jan 2013) $
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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * This source is based on tinyMCE-plugin "advimage":
 * Moxiecode Systems AB, http://tinymce.moxiecode.com/license.
 */
var ImageDialog = {
	preInit : function() {
		var url;
		//tinyMCEPopup.requireLangPack();
		if(url = tinyMCEPopup.getParam("external_image_list_url")){
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
		}
	},

	init : function(ed) {
		this.insert('','');
	},

	insert : function(file, title) {
		var ed = tinyMCEPopup.editor, t = this, f = document.forms[0];
		// remove <img> if src=""
		if(f.src.value === '' || f.src.value === 'http://'){
			if (ed.selection.getNode().nodeName == 'IMG') {
				ed.dom.remove(ed.selection.getNode());
				ed.execCommand('mceRepaint');
			}
			top.close();
			return;
		}

		if (tinyMCEPopup.getParam("accessibility_warnings", 1)) {
			if(!f.alt.value){
				//tinyMCEPopup.confirm(tinyMCEPopup.getLang('advimage_dlg.missing_alt'), function(s) {
					//if (s){
						//t.insertAndClose();
					//}
				//});
				return;
			}
		}
		t.insertAndClose();
	},

	insertAndClose : function() {
		var ed = tinyMCEPopup.editor, f = document.forms[0], nl = f.elements, v, args = {}, el;

		tinyMCEPopup.restoreSelection();

		// Fixes crash in Safari
		if(tinymce.isWebKit){
			ed.getWin().focus();
		}

		if(!ed.settings.inline_styles){
			args = {
				vspace : nl.vspace.value,
				hspace : nl.hspace.value,
				border : nl.border.value,
				align : getSelectValue(f, 'align')
			};
		} else{
			// Remove deprecated values
			args = {
				vspace : '',
				hspace : '',
				border : '',
				align : ''
			};
		}

		tinymce.extend(args, {
			src : nl.src.value.replace(/ /g, '%20'),
			width : nl.width.value,
			height : nl.height.value,
			hspace : nl.hspace.value,
			vspace : nl.vspace.value,
			border : nl.border.value,
			alt : nl.alt.value,
			align : nl.align.value,
			name : nl.name.value,
			'class' : nl.class.value,
			title : nl.title.value,
			longdesc : nl.longdesc.value
			//style : nl.style.value,
			//id : nl.id.value,
			//dir : nl.dir.value,
			//lang : nl.lang.value,
			//usemap : nl.usemap.value,
		});

		el = ed.selection.getNode();

		if(el && el.nodeName == 'IMG'){
			ed.dom.setAttribs(el, args);
		} else{
			ed.execCommand('mceInsertContent', false, '<img id="__mce_tmp" />', {skip_undo : 1});
			//ed.execCommand('mceInsertContent', false, '<img />', {skip_undo : 1});
			ed.dom.setAttribs('__mce_tmp', args);
			ed.dom.setAttrib('__mce_tmp', 'id', '');
			ed.undoManager.add();
		}

		tinyMCEPopup.editor.execCommand('mceRepaint');
		tinyMCEPopup.editor.focus();
		//tinyMCEPopup.close();
		top.close();
	}

	// removed lots of original tinyMCE-functions
};

ImageDialog.preInit();
tinyMCEPopup.onInit.add(ImageDialog.init, ImageDialog);
