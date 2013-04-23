var menuActive = false;

function topMenuClick(elem){
	var itemsState = (menuActive) ? "top_div" : "top_div click";
	menuActive = (menuActive) ? false : true;
	var liElems = elem.parentNode.parentNode.childNodes;
	for (var i = 0; i < liElems.length; i++){
		liElems[i].firstChild.className = itemsState;
	}
}

function topMenuHover(elem){ //<li top
	var left = elem.firstChild.childNodes[1].offsetLeft;
	var liElems = elem.parentNode.childNodes;	
	if(left < -1000) {
		for (var i = 0; i < liElems.length; i++){
			liElems[i].firstChild.className = "top_div";
		}
		menuActive = false;
	}
}