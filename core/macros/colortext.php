<?php
function colortext($string) {
	$colors = Array("#ff0000","#00ff00","#0000ff","#969609","#40A5A7","#ff00ff");
	$newstring = "";
	for ($i=0;$i<strlen($string);$i++) {
	  $buchstabe = substr($string,$i,1);
	  if($buchstabe == " ") {
		$newstring .= " ";
		continue;
	  }
	  $newstring .= '<span style="color:'.current($colors).';">'.$buchstabe.'</span>';
	  if(next($colors) === false) reset($colors);
	}
	echo $newstring;
}
?>