/**
 * Xenux Basic Functions
 */


/**
 * empty - Check whether a string is empty
 * @param string str: The variable being evaluated
 * @return bool: Returns TRUE if str is empty, FALSE otherwise
 */
function empty (str) {
	return (!str || 0 === str.length);
}


/**
 * escapeHtml - Escapes HTML Characters
 * @param string str: The unencoded string
 * @return string: The encoded string
 */
function escapeHtml(str) {
	return str
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
}


/**
 * FileSizeConvert - Rounds and converts to a proper filesize
 * @param int bytes: The filesize
 * @param int precision: The optional number of decimal digits to round to
 * @return float: The rounded value with unit
 */
function FileSizeConvert (bytes, precision) {
	if (bytes == 0) return '0 Byte';

	var k = 1000;
	var dm = precision || 3;
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	var i = Math.floor(Math.log(bytes) / Math.log(k));

	return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}


/**
 * isInt - Find whether the type of a variable is integer
 * @param mixed var: The variable being evaluated
 * @return bool: Returns TRUE if var is an integer, FALSE otherwise
 */
function isInt (val) {
	return val % 1 === 0;
}


/**
 * isJSON - Find whether the type of a variable is json
 * @param mixed var: The variable being evaluated
 * @return bool: Returns TRUE if var is a JSON string, FALSE otherwise
 */
function isJSON (val) {
	return (/^[\],:{}\s]*$/.test(val.replace(/\\["\\\/bfnrtu]/g, '@').
		replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
		replace(/(?:^|:|,)(?:\s*\[)+/g, '')));
}


/**
 * Math.root - Take the n-th root of an integer
 * @param int base: The value to extract the root
 * @param int n: The n of n-th root
 * @return int: The result
 */
Math['root'] = function(base, n) {
	var n = n || 2;
	return Math.pow(base, 1/n);
};


/**
 * openInNewTab - Opens an url in a blank window
 * @param string url: The url to open
 * @return mixed: The instance of the window
 */
function openInNewTab(url) {
	var win = window.open(url, '_blank');
	win.focus();
	return win;
}


/**
 * round - Rounds a float
 * @param float val: The value to round
 * @param int precision: The optional number of decimal digits to round to
 * @return float: The rounded value
 */
function round (val, precision) {
	var factor = Math.pow(10, precision);
	var result = Math.round(val * factor) / factor ;
	return result;
}
