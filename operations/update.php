<?php
function error($a){
	$bones['message'] = $a;
	die(json_encode($bones));
}
if(!isset($_GET['cid'])){
	error("Campaign ID not supplied.");
}
//Forced all output into the message variable. That way the JSON won't get crumpled and messages (errors) are readable
//Also allows us to use echo for debugging


/* Measure how long it takes to do everything */
$start = explode(' ', microtime());
$start = $start[1] + $start[0];

require_once "../include/db.php";
$db = new db;
/* Get cached state */
$db->query("SELECT `state` FROM `campaign` WHERE `id`='{$_GET['cid']}'");
if(!$row = $db->get()) error("Campaign not found in database");
$state = $row['state'];
/* Do any actions that just took place */
//TAKING INPUT
if(isset($_GET['action'])){
	$bones = json_decode($state,true); //pull apart json so it can be worked with in php
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
		//put it backtogether
		$json = json_encode($bones);
		//update database
		$db->query("UPDATE `campaign` SET `state`='".mysql_real_escape_string($json)."'");
		$db->commit();
		/*Work out how long it actually took and pump out json*/
		$time = explode(' ', microtime());
		$time = $time[1] + $time[0];
		$bones['loadtime'] = round(($time - $start), 4);
		echo $json;
} else {
	die($state);
}
?>