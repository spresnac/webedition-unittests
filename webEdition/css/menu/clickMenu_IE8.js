var menuActive = false;

function topMenuClick(elem){
	menuActive = (menuActive) ? false : true;
	if(menuActive){ 
		elem.className = "top_div click";
	} else{
		elem.className = "top_div";
	}
}

function topMenuHover(elem){
	var left = elem.firstChild.childNodes[1].offsetLeft;
	var liElems = elem.parentNode.childNodes;	
	if(left < -1000) {
		for (var i = 0; i < liElems.length; i++){
			liElems[i].firstChild.className = "top_div";
		}
		menuActive = false;
	}
}
