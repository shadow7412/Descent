<?php
header("Content-Type: application/json");
require_once "../include/db.php";
$db = new db;
function error($a){
	$bones['message'] = $a;
	die(json_encode($bones));
}
function action($action,$player = 0,$gold = 0,$overlord = 0){
	global $week,$cid;
	$db = new db;
	$aid = 0;
	$db->query("SELECT MAX(`actionid`) AS 'aid' FROM `log` WHERE `campaign`='$cid'");
	if($row = $db->get()) $aid = $row['aid']+1;
	$db->query("INSERT INTO `log` (`campaign`,`actionid`,`week`,`summary`,`player`,`gold`,`overlord`) VALUES ('$cid','$aid','$week','$action','$player','$gold','$overlord')");
}
if(!isset($_GET['cid'])){
	error("Campaign ID not supplied.");
}
//Forced all output into the message variable. That way the JSON won't get crumpled and messages (errors) are readable
//Also allows us to use echo for debugging


/* Measure how long it takes to do everything */
$start = explode(' ', microtime());
$start = $start[1] + $start[0];

/* Get cached state */
$cid = $_GET['cid'];
$db->query("SELECT `state` FROM `campaign` WHERE `id`='$cid'");
if(!$row = $db->get()) error("Campaign not found in database");
$state = $row['state'];
/* Do any actions that just took place */
//TAKING INPUT
///var_dump($_POST);
@$do = isset($_POST['action'])?$_POST['action']:$_GET['action'];
@$to = isset($_POST['to'])?$_POST['to']:$_GET['to'];
if(isset($do)){
	$bones = json_decode($state,true); //pull apart json so it can be worked with in php
	$week = $bones['week'];
	switch ($do){
		case("timepasses"):
			//A week passes.
			//update tier
			//increment gameweek
			$bones['week']++;
			break;
		case("enter"):
			//Enter an instance
			$bones['heroes']['location'] = $to;
		case("discover"):
			//Mark as discovered (if entering, we are also discovering if not already)
			if($bones['instances'][$to]['discovered'] == false){
				action("Discovered ".$to,50);
				$bones['instances'][$to]['discovered'] = true;
			}
			break;
		case("death"):
			//player
		case("curse"):
			//player
		case("chest"):
			//roll
		case("glyph"):
			action("Unlocked Glyph",3);
		case("kill"):
		//master/boss/finalboss/lieutenant
			switch ($to){
				case("master"):
					$bones['heroes']['gold'] += 50;
					action("Killed Master",0,50);
					break;
				case("boss"):
					$bones['heroes']['gold'] += 50;
					action("Killed Boss",0,100);
					break;
				case("fboss"):
					$bones['heroes']['gold'] += 200;
					action("Killed Final Boss",0,50);
					break;
				case("lieutenant"):
					$bones['heroes']['conquest'] += 5;
					$bones['heroes']['money'] += 200;
					action("Killed Lieutentant",5,200);
					break;
			}
		break;
		case("finish"):
		//level,flee,dungeon
			switch ($to){
				case ("level"):
					//CONQUEST/MONEY HERE
					break;
				case ("dungeon"):
					$bones['instances'][$bones['heroes']['location']]['completed'] = true;
					//CONQUEST/MONEY HERE
					$bones['heroes']['location'] = "overworld";
					break;
				case ("flee"):
					$bones['instances'][$bones['heroes']['location']]['fled'] = true;
					$bones['heroes']['location'] = "overworld";
					break;
			}
			//update tier
			//set location to overworld
		default:
		
		}
	/*Work out how long it actually took and pump out json*/
	$time = explode(' ', microtime());
	$time = $time[1] + $time[0];
	$bones['loadtime'] = round(($time - $start), 4);
	//put it backtogether
	$json = json_encode($bones);
	//update database
	$db->query("UPDATE `campaign` SET `state`='".mysql_real_escape_string($json)."' WHERE `id`='$cid'");
	$db->commit();
	echo $json;
} else {
	die($state);
}
?>