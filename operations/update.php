<?php
if(!isset($_GET['cid'])){
	$bones['message'] = "A campaign ID was not supplied.";
	die(json_encode($bones));
}
//Forced all output into the message variable. That way the JSON won't get crumpled and messages (errors) are readable
//Also allows us to use echo for debugging
ob_start(function($a){
	global $bones;
	$bones['message']+=$a;
	});//To stop any 'wrong' input from getting in there.

/* Measure how long it takes to do everything */
$start = explode(' ', microtime());
$start = $start[1] + $start[0];

require_once "include/db.php";
$db = new db;
/* Get cached state */
$db->query("SELECT `state` FROM `campaign` WHERE `id`='{$_GET['cid']}'");
if(!$row = $db->get()) {
	$bones['message'] = "The campaign couldn't be found.";
	ob_end_clear();
	die(json_encode($bones));
}
/* Do any actions that just took place */
//TAKING INPUT
if(isset($_GET['action'])){
	//?action=[]&to=[]
	switch ($_GET['action']){
		case("death"):
			//player
		case("curse"):
			//player
		case("chest"):
			//roll
		case("kill"):
			//master/boss/finalboss/lieutenant	
		default:
			
	}
}
$json = json_encode($bones);
$db->query("UPDATE `campaign` SET `state`='".mysql_real_escape_string($json);."'");
/*Work out how long it actually took and pump out json*/
$time = explode(' ', microtime());
$time = $time[1] + $time[0];
$bones['loadtime'] = round(($time - $start), 4);
ob_end_flush();
echo $json;
?>