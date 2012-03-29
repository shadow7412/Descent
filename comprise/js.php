<?php
/* To compress, and send all JS files in one transaction */
ob_start("ob_gzhandler"); //Gzip page
header("Content-Type: text/javascript");
$file = array("jquery-1.7.2.min.js","jquery-ui-1.8.18.custom.min.js","jquery.dataTables.min.js","descent.js");
foreach($file as $f){
	fpassthru($h=fopen($f,"r"));
	fclose($h);
}
?>