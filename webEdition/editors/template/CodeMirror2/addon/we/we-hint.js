
(function() {

	CodeMirror.weHints = [];

	CodeMirror.weHint = function(cm, simbol) {

		if(simbol.length > 0) {
			var cursor = cm.getCursor();
			cm.replaceSelection(simbol);
			cursor = {
				line: cursor.line,
				ch: cursor.ch + 1
			};
			cm.setCursor(cursor);
		}

		CodeMirror.simpleHint(cm, getHint);
	};

	var getHint = function(cm) {
		var cursor = cm.getCursor();
		if (cursor.ch > 0) {
			var text = cm.getRange({
				line: 0,
				ch: 0
			}, cursor);
			var typed = '';
			var simbol = '';
			for(var i = text.length - 1; i >= 0; i--) {
				if(text[i] == ' ' || text[i] == '<') {
					simbol = text[i];
					break;
				}	else {
					typed = text[i] + typed;
				}
			}

			text = text.slice(0, text.length - typed.length);

			var path = getOpenTag(text)+simbol;
			var hints = CodeMirror.weHints[path];

			//console.log(path+" "+hints);

			if(typeof hints === 'undefined'){
				hints = [''];
			}else {
				hints = hints.slice(0);
				for (var i = hints.length - 1; i >= 0; i--) {
					if(hints[i].indexOf(typed) != 0){
						hints.splice(i, 1);
					}
				}
			}

			return {
				list: hints,
				from: {
					line: cursor.line,
					ch: cursor.ch - typed.length
				},
				to: cursor
			};
		};
	};


	var getOpenTag = function(text) {

		var open = text.lastIndexOf('<');
		var close = text.lastIndexOf('>');

		if (close < open){
			text = text.slice(open);

			if(text != '<') {
				var space = text.indexOf(' ');
				if(space < 0){
					space = text.indexOf('\t');
				}
				if(space < 0){
					space = text.indexOf('\n');
				}
				if (space < 0){
					space = text.length;
				}
				return text.slice(0, space);
			}
		}

		return '';
	};

})();
