/**
 * webEdition CMS
 *
 * $Rev: 5222 $
 * $Author: mokraemer $
 * $Date: 2012-11-24 18:58:04 +0100 (Sat, 24 Nov 2012) $
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

var isTinyMCE = true;
//tinyMCEPopup.requireLangPack();


var WeacronymDialog = { // TODO: clean code by using more vars
	
	sel : '',
	inst : '',
	elm : '',
	isAcronym : false,
	
	init : function() {
		var langValue = '';
		var titleValue = '';
		
		inst = tinyMCEPopup.editor;
		elm = inst.selection.getNode();
		sel = inst.selection.getContent({format : 'text'});

		var printAsSelection = '';
		
		if(sel === ''){ 
			// no selection, but cursor inside ACRONYM (the only case where acronym-Button is active without selection): 
			sel = elm.innerHTML;
			this.isAcronym = true;
		} else{
			if(elm.nodeName === 'ACRONYM' && sel.trim() === elm.innerHTML.trim()){ //exact selection is innerHTML of ACRONYM: we will add or manipulate lang-attrib of existing SPAN
				this.isAcronym = true;
			}
		}

		if(this.isAcronym){
			langValue = elm.getAttribute('lang') ? elm.getAttribute('lang') : '';
			titleValue = elm.getAttribute('title') ? elm.getAttribute('title') : '';
		}
		
		document.forms["we_form"].elements['we_dialog_args[lang]'].value = langValue;
		document.forms["we_form"].elements['we_dialog_args[title]'].value = titleValue;
		document.forms["we_form"].elements['text'].value = sel; //Selected Text to insert into glossary
	},

	insert : function() {
		var langValue = document.forms["we_form"].elements['we_dialog_args[lang]'].value;
		var titleValue = document.forms["we_form"].elements['we_dialog_args[title]'].value;
		
		if(this.isAcronym){//if there is an existing ACRONYM selected: just manipulate lang-Attribute
			if(titleValue !== ''){
				inst.selection.getNode().setAttribute('title', titleValue);
				if(langValue !== ''){
					inst.selection.getNode().setAttribute('lang', langValue);
				} else{
					inst.selection.getNode().removeAttribute('lang');
				}
			} else{
				inst.dom.remove(inst.selection.getNode(), 1);
			}
		} else{//no ACRONYM selected: insert tight and move blanks to the right of ACRONYM
			if(titleValue !== ''){
				var blank = '';
				var isBlank = false;
				while(sel.charAt(sel.length-1) === ' '){
					sel = sel.substr(0,sel.length-1);
					isBlank = true;
					blank += '&nbsp;';
				}
				blank = isBlank ? blank.substr(0,blank.length-6) + ' ' : blank;

				var visual = inst.hasVisual ? ' class="mceItemWeAcronym"' : '';
				var content = '<acronym lang="' + langValue + '" title="' + titleValue + '"' + visual + '>' + sel + '</acronym>' + blank;
				inst.execCommand('mceInsertContent', false, content);
			}
		}
		//tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(WeacronymDialog.init, WeacronymDialog);
