function atext(txt) {
	document.getElementById('text').innerHTML += txt;
}
var texto = false;
var fetto = false;
var kursivo = false;
var unterstricheno = false;
var linko = false;
var lauftexto = false;
var bunto = false;
function text() {
	if(texto == false) {
		var col = prompt("Farbe (englisch oder Hex-Wert):","black");
		if(col != null) {
			atext("<span style='color:"+col+"'>");
			document.getElementById('textb').innerHTML = "farbiger Text *";
			texto = true;
		}
	} else {
		atext("</span>");
		document.getElementById('textb').innerHTML = "farbiger Text";
		texto = false;
	}
}
function fett() {
	if(fetto == false) {
		atext("<b>");
		document.getElementById('fettb').innerHTML = "F *";
		fetto = true;
	} else {
		atext("</b>");
		document.getElementById('fettb').innerHTML = "F";
		fetto = false;
	}
}
function kursiv() {
	if(kursivo == false) {
		atext("<i>");
		document.getElementById('kursivb').innerHTML = "K *";
		kursivo = true;
	} else {
		atext("</i>");
		document.getElementById('kursivb').innerHTML = "K";
		kursivo = false;
	}
}
function unterstrichen() {
	if(unterstricheno == false) {
		atext("<u>");
		document.getElementById('unterstrichenb').innerHTML = "U *";
		unterstricheno = true;
	} else {
		atext("</u>");
		document.getElementById('unterstrichenb').innerHTML = "U";
		unetrstricheno = false;
	}
}
function link() {
	if(linko == false) {
		var href = prompt("Adresse:","http://");
		if(href != null) {
			atext("<a href='"+href+"'>");
			document.getElementById('linkb').innerHTML = "Link *";
			linko = true;
		}
	} else {
		atext("</a>");
		document.getElementById('linkb').innerHTML = "Link";
		linko = false;
	}
}
function bild() {
	var src = prompt("Adresse:","http://");
	if(src != null) {
		atext("<img src='"+src+"' />");
	}
}
function lauftext() {
	if(lauftexto == false) {
		atext("<marquee>");
		document.getElementById('lauftextb').innerHTML = "Lauftext *";
		lauftexto = true;
	} else {
		atext("</marquee>");
		document.getElementById('lauftextb').innerHTML = "Lauftext";
		lauftexto = false;
	}
}
function newline() {
	atext("<br />");
}
function hr() {
	atext("<hr/>");
}
function bunt() {
	if(bunto == false) {
		atext("[bunt]");
		document.getElementById('bunt').innerHTML = "bunter Text *";
		bunto = true;
	} else {
		atext("[/bunt]");
		document.getElementById('bunt').innerHTML = "bunter Text";
		bunto = false;
	}
}