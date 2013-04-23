/**
 * webEdition CMS
 *
 * $Rev: 5313 $
 * $Author: lukasimhof $
 * $Date: 2012-12-05 14:14:43 +0100 (Wed, 05 Dec 2012) $
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

//		tinyMCEPopup.requireLangPack();

		if(url = tinyMCEPopup.getParam("external_image_list_url")){
			document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
		}
	},

	init : function(ed) {
		var f = document.forms["we_form"];
		var nl = f.elements;
		var ed = tinyMCEPopup.editor;
		var dom = ed.dom;
		var n = ed.selection.getNode();
		var fl = tinyMCEPopup.getParam('external_image_list', 'tinyMCEImageList');
		tinyMCEPopup.resizeToInnerSize();
		TinyMCE_EditableSelects.init();

		if(n.nodeName == 'IMG' && !ed.isWeDataInitialized){
			var imgWidth, imgHeight, longdesc, src_arr;

			// load attributes into form
			imgWidth = dom.getAttrib(n, 'width');
			imgHeight = dom.getAttrib(n, 'height');
			nl["we_dialog_args[width]"].value = imgWidth;
			nl["we_dialog_args[height]"].value = imgHeight;
			nl["we_dialog_args[vspace]"].value = dom.getAttrib(n, 'vspace');
			nl["we_dialog_args[hspace]"].value = dom.getAttrib(n, 'hspace');
			nl["we_dialog_args[border]"].value = dom.getAttrib(n, 'border');
			nl["we_dialog_args[alt]"].value = dom.getAttrib(n, 'alt');
			nl["we_dialog_args[title]"].value = dom.getAttrib(n, 'title');
			nl["we_dialog_args[name]"].value = dom.getAttrib(n, 'name');
			this.selectOptionByValue(f, "we_dialog_args[align]", dom.getAttrib(n, 'align'));
			longdesc = dom.getAttrib(n, 'longdesc');
			nl["we_dialog_args[longdescsrc]"].value = longdesc.split('?id=',2)[1] ? longdesc.split('?id=',2)[0] : '';
			nl["we_dialog_args[longdescid]"].value = longdesc.split('?id=',2)[1] ? longdesc.split('?id=',2)[1] : '';
			this.selectOptionByValue(f, "we_dialog_args[class]", dom.getAttrib(n, 'class'));

			// parse and insert src
			/*
			src_arr = this.analyseSrc(dom.getAttrib(n, 'src'));

			nl["we_dialog_args[type]"][0].checked = src_arr[0]; // type = ext
			nl["we_dialog_args[extSrc]"].value = src_arr[1];
			nl["we_dialog_args[type]"][1].checked = src_arr[2];
			nl["we_dialog_args[fileID]"].value = src_arr[3];
			nl["we_dialog_args[fileSrc]"].value = src_arr[4];
			*/

			// set some flags
			ed.isWeDataInitialized = true;
			f.isTinyMCEInitialization.value="0";

			// reload when image is thumbnail to get path and filename of original image
			/*
			if(src_arr[5] !== 0){
				var thSelect = nl['we_dialog_args[thumbnail]'];
				for (var i=0; i < thSelect.options.length; i++){
					if (thSelect.options[i].value == src_arr[5]){
						thSelect.options[i].selected = true;
					} else{
						thSelect.options[i].selected = false;
					}
				}
				f.target="we_weImageDialog_edit_area";
				f.we_what.value="dialog";
				f.imgChangedCmd.value="1";
				f.isTinyMCEInitialization.value="1";
				f.submit();
			}
			*/

			// no reload, so we need to set values for ratioh und ratiow
			if(!(isNaN(imgWidth * imgHeight) || imgHeight === 0 || imgWidth === 0)){
				nl["tinyMCEInitRatioH"].value = imgWidth / imgHeight;
				nl["tinyMCEInitRatioW"].value = imgHeight / imgWidth;
			}
		}

		// add options to css-Pulldown
		/*
		if(typeof(ed.settings.theme_advanced_styles) !== 'undefined' && ed.settings.theme_advanced_styles != ''){
			var cl = '';
			for(var i=0; i < ed.settings.theme_advanced_styles.split(/;/).length; i++){
				cl = ed.settings.theme_advanced_styles.split(/;/)[i].split(/=/)[0];
				nl["we_dialog_args[class]"].options[nl["we_dialog_args[class]"].length] = new Option('.' + cl, cl);
			}
		}
		*/
	},

	analyseSrc : function(src) {
		src_vars = Array();
		if(src.split('?id=',2)[1] || src.split('document:',2)[1]){//internal Document, no thumbnail
			src_vars[0] = false; // type = ext
			src_vars[1] = ''; // external src
			src_vars[2] = true; // type = int
			src_vars[3] = src.split("?id=",2)[1] ? src.split("?id=",2)[1] : src.split('document:',2)[1]; // internal id
			src_vars[4] = src.split('?id=',2)[1] ? src.split('?id=',2)[0] : src; // internal src
			src_vars[5] = 0;

		} else if(src.split('we_thumbs__',2)[1]){
			var docId = (src.split('?thumb=',2)[1]).split(',')[0];
			var thumbId = (src.split('?thumb=',2)[1]).split(',')[1];
			src_vars[0] = false; // type = ext
			src_vars[1] = '' // external src
			src_vars[2] = true; // type = int
			src_vars[3] = docId; // internal id
			src_vars[4] = 'document:' + docId; // internal src
			src_vars[5] = thumbId;
		} else{
			src_vars[0] = true; // type = ext
			src_vars[1] = src.split('?id=',2)[0] // external src
			src_vars[2] = false; // type = int
			src_vars[3] = 0; // internal id
			src_vars[4] = ''; // internal src
			src_vars[5] = 0;

		}
		return src_vars;
	},

	selectOptionByValue : function(form, selName, val){
		for(var i=1; i < form.elements[selName].options.length; i++){
			if(form.elements[selName].options[i].value == val){
				form.elements[selName].options[i].selected = true;
			} else{
				form.elements[selName].options[i].selected = false;
			}
		}
	},

	getAttrib : function(e, at) {
		var ed = tinyMCEPopup.editor, dom = ed.dom, v, v2;

		if(ed.settings.inline_styles){
			switch (at) {
				case 'align':
					if(v = dom.getStyle(e, 'float')){
						return v;
					}

					if(v = dom.getStyle(e, 'vertical-align')){
						return v;
					}

					break;

				case 'hspace':
					v = dom.getStyle(e, 'margin-left')
					v2 = dom.getStyle(e, 'margin-right');

					if(v && v == v2){
						return parseInt(v.replace(/[^0-9]/g, ''));
					}

					break;

				case 'vspace':
					v = dom.getStyle(e, 'margin-top')
					v2 = dom.getStyle(e, 'margin-bottom');
					if(v && v == v2){
						return parseInt(v.replace(/[^0-9]/g, ''));
					}

					break;

				case 'border':
					v = 0;

					tinymce.each(['top', 'right', 'bottom', 'left'], function(sv){
						sv = dom.getStyle(e, 'border-' + sv + '-width');

						// False or not the same as prev
						if(!sv || (sv != v && v !== 0)){
							v = 0;
							return false;
						}

						if(sv){
							v = sv;
						}
					});

					if(v){
						return parseInt(v.replace(/[^0-9]/g, ''));
					}

					break;
			}
		}

		if(v = dom.getAttrib(e, at)){
			return v;
		}

		return '';
	}

	// lots of tinyMCE-functions removed

};

ImageDialog.preInit();
tinyMCEPopup.onInit.add(ImageDialog.init, ImageDialog);