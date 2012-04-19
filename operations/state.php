<?php
ob_start("ob_gzhandler"); //Gzip page
header("Content-Type: application/json");
require_once("../include/db.php");
$db = new db;
if(!isset($_GET['cid'])) die('{"error":"Campaign id not supplied"}');
$cid = $_GET['cid'];
if(!isset($_POST['state'])){
	//fetch and return state as is.
	$db->query("SELECT `state` FROM `campaign` WHERE `id`='$cid'");
	if(!($row = $db->get())) die ('{"error":"Campaign missing"}');
} else {
	$db->query("SELECT `state` FROM `campaign` WHERE `id`='$cid'");
	if(!($row = $db->get())) die ('{"error":"Campaign missing"}');
	while($row['state'] == $_POST['state']){
		set_time_limit(3); //prevent script from timing out
		usleep(500000); //wait .5 seconds
		$db->query("SELECT `state` FROM `campaign` WHERE `id`='$cid'");
		if(!($row = $db->get())) die ('{"error":"Campaign missing"}');
	}
}
echo $row['state'];

/*do{
	//3 seconds until kill script - make sure the script doesn't die during the potentially infinite loop
	set_time_limit(3); 
	//Wait 0.2 - to prevent cpu saturation
	usleep(500000); 
	//check database
} while($row['played']<$req);
echo $row['state']; //give player the new state
*/
?>