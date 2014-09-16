function insert(aTag, eTag) {
	var input = document.form.text;
	input.focus();
	var start = input.selectionStart;
	var end = input.selectionEnd;
	var insText = input.value.substring(start, end);
	input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
	var pos;
	if (insText.length == 0) {
		pos = start + aTag.length;
	} else {
		pos = start + aTag.length + insText.length + eTag.length;
	}
	input.selectionStart = pos;
	input.selectionEnd = pos; 
}
function text() {
	var col = prompt("Farbe (englisch oder Hex-Wert):","black");
	if(col != null) {
		insert("<span style=\"color:"+col+"\">", "</span>")
	}
}
function link() {
	var href = prompt("Adresse:","http://");
	if(href != null) {
		insert("<a href=\""+href+"\">", "</a>");
	}
}
function bild() {
	var src = prompt("Adresse:","http://");
	if(src != null) {
		insert("<img src=\""+src+"\" />", "");
	}
}