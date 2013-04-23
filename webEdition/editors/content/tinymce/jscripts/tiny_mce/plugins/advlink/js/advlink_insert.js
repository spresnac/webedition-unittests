/* Functions for the advlink plugin popup */

tinyMCEPopup.requireLangPack();

function preinit() {
	var url;
	if(url = tinyMCEPopup.getParam("external_link_list_url")){
		document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	}
}

function init() {
	this.insertAction();
}

function setAttrib(elm, attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib.toLowerCase()];
	var dom = tinyMCEPopup.editor.dom;

	if(typeof(value) == "undefined" || value == null){
		value = "";

		if(valueElm){
			value = valueElm.value;
		}
	}

	// Clean up the style
	if(attrib == 'style'){
		value = dom.serializeStyle(dom.parseStyle(value), 'a');
	}

	dom.setAttrib(elm, attrib, value);
}

function getAnchorListHTML(id, target) {
	var ed = tinyMCEPopup.editor, nodes = ed.dom.select('a'), name, i, len, html = "";

	for(i=0, len=nodes.length; i<len; i++){
		if((name = ed.dom.getAttrib(nodes[i], "name")) != "")
			html += '<option value="#' + name + '">' + name + '</option>';
	}

	if(html == ""){
		return "";
	}

	html = '<select id="' + id + '" name="' + id + '" class="mceAnchorList"'
		+ ' onchange="this.form.' + target + '.value=this.options[this.selectedIndex].value"'
		+ '>'
		+ '<option value="">---</option>'
		+ html
		+ '</select>';

	return html;
}

function insertAction() {
	var inst = tinyMCEPopup.editor;
	var elm, elementArray, i;

	elm = inst.selection.getNode();
	elm = inst.dom.getParent(elm, "A");

	// Remove element if there is no href
	if(!document.forms['tiny_form'].href.value){
		i = inst.selection.getBookmark();
		inst.dom.remove(elm, 1);
		inst.selection.moveToBookmark(i);
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
		top.close();
		return;
	}

	// Create new anchor elements

	if(elm == null){
		inst.getDoc().execCommand("unlink", false, null);
		tinyMCEPopup.execCommand("mceInsertLink", false, "#mce_temp_url#", {skip_undo : 1});
		elementArray = tinymce.grep(inst.dom.select("a"), function(n) {return inst.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
		for (i=0; i<elementArray.length; i++)
			setAllAttribs(elm = elementArray[i]);
	} else{
		setAllAttribs(elm);
	}

	// Don't move caret if selection was image
	if(elm.childNodes.length != 1 || elm.firstChild.nodeName != 'IMG'){
		inst.focus();
		inst.selection.select(elm);
		inst.selection.collapse(0);
		tinyMCEPopup.storeSelection();
	}

	tinyMCEPopup.execCommand("mceEndUndoLevel");
	tinyMCEPopup.close();
	top.close();
}

function setAllAttribs(elm) {
	var formObj = document.forms['tiny_form'];
	var href = formObj.href.value.replace(/ /g, '%20');
	var target = formObj.target.value;

	setAttrib(elm, 'href', href);  
	setAttrib(elm, 'title');
	setAttrib(elm, 'target', target == '_self' ? '' : target);
	setAttrib(elm, 'id');
	setAttrib(elm, 'style');
	setAttrib(elm, 'class');
	setAttrib(elm, 'rel');
	setAttrib(elm, 'rev');
	//setAttrib(elm, 'charset');
	setAttrib(elm, 'hreflang');
	//setAttrib(elm, 'dir');
	setAttrib(elm, 'lang');
	setAttrib(elm, 'tabindex');
	setAttrib(elm, 'accesskey');
	//setAttrib(elm, 'type');
	//setAttrib(elm, 'onfocus');
	//setAttrib(elm, 'onblur');
	//setAttrib(elm, 'onclick');
	//setAttrib(elm, 'ondblclick');
	//setAttrib(elm, 'onmousedown');
	//setAttrib(elm, 'onmouseup');
	//setAttrib(elm, 'onmouseover');
	//setAttrib(elm, 'onmousemove');
	//setAttrib(elm, 'onmouseout');
	//setAttrib(elm, 'onkeypress');
	//setAttrib(elm, 'onkeydown');
	//setAttrib(elm, 'onkeyup');

	// Refresh in old MSIE
	if (tinyMCE.isMSIE5){
		elm.outerHTML = elm.outerHTML;
	}
}

// While loading
preinit();
tinyMCEPopup.onInit.add(init);