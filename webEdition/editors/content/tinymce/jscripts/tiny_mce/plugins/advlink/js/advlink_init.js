/* Functions for the advlink plugin popup */

//tinyMCEPopup.requireLangPack();

function preinit() {
	var url;

	if(url = tinyMCEPopup.getParam("external_link_list_url")){
		document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
	}
}

function init() {
	tinyMCEPopup.resizeToInnerSize();

	var formObj = document.forms["we_form"];
	var inst = tinyMCEPopup.editor;
	var elm = inst.selection.getNode();
	var action = "insert";
	var html;

	// Anchor list
	html = getAnchorListHTML('anchorlist','we_dialog_args[anchor]');
	if(html != ""){
		document.getElementById("anchorlistcontainer").innerHTML = html;
	}

	elm = inst.dom.getParent(elm, "A");
	if(elm != null && elm.nodeName == "A"){
		action = "update";
	}

	if(action == "update" && inst.isWeLinkInitialized === false && formObj){
		inst.isWeLinkInitialized = true;

		//var href = inst.dom.getAttrib(elm, 'href');
		//var urlParts = this.getUrlParts(href);

		//formObj.elements['we_dialog_args[anchor]'].value = urlParts[1];
		//formObj.elements['we_dialog_args[param]'].value = urlParts[2];

		formObj.elements['we_dialog_args[title]'].value = inst.dom.getAttrib(elm, 'title');
		formObj.elements['we_dialog_args[target]'].value = inst.dom.getAttrib(elm, 'target');
		//formObj.elements['we_dialog_args[class]'].value = inst.dom.getAttrib(elm, 'class');
		formObj.elements['we_dialog_args[rel]'].value = inst.dom.getAttrib(elm, 'rel');
		formObj.elements['we_dialog_args[lang]'].value = inst.dom.getAttrib(elm, 'lang');
		formObj.elements['we_dialog_args[hreflang]'].value = inst.dom.getAttrib(elm, 'hreflang');
		formObj.elements['we_dialog_args[rev]'].value = inst.dom.getAttrib(elm, 'rev');
		formObj.elements['we_dialog_args[accesskey]'].value = inst.dom.getAttrib(elm, 'accesskey', typeof(elm.accesskey) != "undefined" ? elm.accesskey : "");
		formObj.elements['we_dialog_args[tabindex]'].value = inst.dom.getAttrib(elm, 'tabindex', typeof(elm.tabindex) != "undefined" ? elm.tabindex : "");
		this.selectOptionByValue(formObj, "we_dialog_args[class]", inst.dom.getAttrib(elm, 'class'));

		/*
		// analyse linktype, enter link-data and reload
		//var linktype = this.getLinkType(urlParts[0]);
		//this.selectOptionByValue(formObj, 'we_dialog_args[type]', linktype);

		switch(linktype){
			case "int":
				//formObj.elements['we_dialog_args[fileID]'].value = urlParts[0].split('document:')[1];
				break;
			case "obj":
				//formObj.elements['we_dialog_args[objID]'].value = urlParts[0].split('object:')[1];
				break;
			case "ext":
				//formObj.elements['we_dialog_args[extHref]'].value = urlParts[0];
				break;
			case "mail":
				//formObj.elements['we_dialog_args[mailHref]'].value = urlParts[0].split(':')[1];
				break;
			default:
				break;
		}
		//this.doReload(formObj);
		*/
	}
	/*
	if(typeof(inst.settings.theme_advanced_styles) !== 'undefined' && inst.settings.theme_advanced_styles != ''){
		var cl = '';
		for(var i=0; i < inst.settings.theme_advanced_styles.split(/;/).length; i++){
			cl = inst.settings.theme_advanced_styles.split(/;/)[i].split(/=/)[0];
			formObj.elements['we_dialog_args[class]'].options[formObj.elements['we_dialog_args[class]'].length] = new Option('.' + cl, cl);
		}
	}
	*/
}

// we_functions

function doReload(form) {
	form.elements['we_what'].value = "dialog";//verhindert Neuladen
	form.target = 'we_weHyperlinkDialog_edit_area';
	form.submit();
}

function getLinkType(href) {
	var hrefArr = href.split(':');
	return hrefArr[0] === 'document' ? 'int' : (hrefArr[0] === 'object' ? 'obj' : (hrefArr[0] === 'mailto' ? 'mail' : 'ext'));
}

function getUrlParts(url) {
	var u = '', anch = '', param = '';

	var anchArr = url.split('#');
	u = anchArr.shift();
	anch = (anchArr[0]) ? anchArr.join('#') : anch;
/*
	var paramArr = u.split('?');
	u = paramArr.shift();
	param = (paramArr[0]) ? paramArr.join('?') : param;
*/
	return new Array(u, anch, param);
}

function selectOptionByValue(form, selName, val) {
	if(typeof(form)=='undefined' || typeof(form.elements[selName]) == 'undefined' && typeof(val) == 'undefined'){
		return;
	}
	for(var i=1; i < form.elements[selName].options.length; i++){
		if(form.elements[selName].options[i].value == val){
			form.elements[selName].options[i].selected = true;
		} else{
			form.elements[selName].options[i].selected = false;
		}
	}
	
}

// more functions from tinyMCE

function getAnchorListHTML(id, target) {
	var ed = tinyMCEPopup.editor, nodes = ed.dom.select('a'), name, i, len, html = "";

	for(i=0, len=nodes.length; i<len; i++){
		if((name = ed.dom.getAttrib(nodes[i], "name")) != "")
			html += '<option value="' + name + '">' + name + '</option>';
	}

	if(html == ""){
		return "";
	}

	html = '<select id="' + id + '" name="' + id + '" class="defaultfont" style="width:100px"'
	+ ' onchange="this.form.elements[\''+ target +'\'].value=this.options[this.selectedIndex].value;this.selectedIndex=0;"'
	+ '>'
	+ '<option value=""></option>'
	+ html
	+ '</select>';

	return html;
}


// unused functions from tinyMCE

function checkPrefix(n) {

	if(n.value && Validator.isEmail(n) && !/^\s*mailto:/i.test(n.value) && confirm(tinyMCEPopup.getLang('advlink_dlg.is_email'))){
		n.value = 'mailto:' + n.value;
	}

	if(/^\s*www\./i.test(n.value) && confirm(tinyMCEPopup.getLang('advlink_dlg.is_external'))){
		n.value = 'http://' + n.value;
	}
}

function setFormValue(name, value) {
	document.forms[0].elements[name].value = value;
}

// the rest of tiny-functions is deleted

// While loading
preinit();
tinyMCEPopup.onInit.add(init);