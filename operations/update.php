<?php
ob_start("ob_gzhandler"); //Gzip page
header("Content-Type: application/json");
require_once "../include/db.php";
$db = new db;
function error($a){
	$bones['error'] = $a;
	die(json_encode($bones));
}
function action($action,$player = 0,$gold = 0,$overlord = 0,$hero = null,$xp = 0){
	global $week,$cid,$bones;
	$db = new db;
	$aid = 0;
	//add to log
	$db->query("SELECT MAX(`actionid`) AS 'aid' FROM `log` WHERE `campaign`='$cid'");
	if($row = $db->get()) $aid = $row['aid']+1;
	$db->query("INSERT INTO `log` (`campaign`,`actionid`,`week`,`summary`,`player`,`gold`,`overlord`,`hero`,`xp`) VALUES ('$cid','$aid','$week','$action','$player','$gold','$overlord','$hero','$xp')");
	//update state
	$bones['heroes']['conquest'] += $player;
	$bones['heroes']['gold'] += $gold;
	$bones['overlord']['conquest'] += $overlord;
}
if(!isset($_GET['cid'])){
	error("Campaign ID not supplied.");
}

/* Measure how long it takes to do everything */
$start = explode(' ', microtime());
$start = $start[1] + $start[0];

/* Get cached state */
$cid = $_GET['cid'];
$db->query("SELECT `state`,`password` FROM `campaign` WHERE `id`='$cid'");
if(!$row = $db->get()) error("Campaign not found in database");

/* Check password (if applicable)*/
if($row['password'] != "d41d8cd98f00b204e9800998ecf8427e"){ //md5("") == d41d8cd98f00b204e9800998ecf8427e
	//if no password is set for campaign - we don't care about this authentication crap.
	if(!isset($_SERVER['PHP_AUTH_PW']) || $row['password'] != md5($_SERVER['PHP_AUTH_PW'])){
		header('WWW-Authenticate: Basic realm="I need your campaign password. Please?"');
		header('HTTP/1.0 401 Unauthorized');
	}
}
/* Do any actions that just took place */
//TAKING INPUT - from either post or get - but favoring post.
@$do = isset($_POST['action'])?$_POST['action']:$_GET['action'];
@$to = isset($_POST['to'])?$_POST['to']:$_GET['to'];

//If there haven't been changes - just give the state as it came from the database
if(!isset($do))die($row['state']);

//There are changes - pull apart json so it can be worked with in php
$bones = json_decode($row['state'],true);
$bones['message']="";
//js will have sent action(action,to);
switch ($do){
	case("timepasses"):
		//A week passes.
		//update tier
		if(($message[$bones['tier']]<3) && ($bones['tier']*200+200) < ($bones['overlord']['conquest']+$bones['heroes']['conquest'])){
			$message = array("Welcome to SILVER tier.","Welcome to GOLD tier.","Prepare for the final battle!",);
			$bones['message'] = $message[$bones['tier']];
			$bones['tier']++;
		}
		//increment gameweek
		$bones['week']++;
		break;
	case("enter"):
		//Enter an instance
		$bones['heroes']['location'] = $to;
	case("discover"):
		//Mark as discovered (if entering, we are also discovering if not already)
		if($bones['instances'][$to]['discovered'] == false){
			$bones['instances'][$to]['discovered'] = true;
			action("Discovered ".$to,5);
		}
		break;
	case("death"):
		//to = array element of player
		$divine = intVal(($bones['heroes']['conquest']-$bones['overlord']['conquest'])/25);
		$cost = $bones['hero'][$to]['level'] + $divine;
		if($cost < 1) $cost = 1; //can't cost less than one
		action("Death of ".$bones['hero'][$to]['hero']." (Divine: $divine)",0,0,$cost+$bones['hero'][$to]['curses']);
		$bones['hero'][$to]['curses'] = 0; //reset number of curses
		$bones['hero'][$to]['deaths']++; //reset number of curses
	case("curse"):
		//player
	case("chest"):
		//roll
	case("deck"):
		//overlord gets through deck
		action("Overlord Deck renewed",0,0,3);
		break;
	case("glyph"):
		action("Unlocked Glyph",3);
		break;
	case("kill"):
		//master/boss/finalboss/lieutenant
		switch ($to){
			case("master"):
				action("Killed Master",0,50);
				break;
			case("boss"):
				action("Killed Boss",5,100);
				break;
			case("fboss"):
				action("Killed Final Boss",0,50);
				break;
			case("lieutenant"):
				action("Killed Lieutentant",5,200);
				break;
		}
		break;
	case("finish"):
	//level,flee,dungeon
		switch ($to){
			case ("level"):
				//CONQUEST/MONEY HERE
				$bones['instance'][$bones['heroes']['location']]['level']++;
				$bones['level']['bossdead'] = false;
				$bones['level']['deck'] = 0;
				break;
			case ("dungeon"):
				$bones['instances'][$bones['heroes']['location']]['completed'] = true;
				//CONQUEST/MONEY HERE
				$bones['heroes']['location'] = "overworld";
				$bones['level']['bossdead'] = false;
				$bones['level']['deck'] = 0;
				break;
			case ("flee"):
				$bones['level']['deck'] = 0;
				$bones['instances'][$bones['heroes']['location']]['fled'] = true;
				$bones['level']['bossdead'] = false;
				$bones['heroes']['location'] = "overworld";
				break;
		}
		break;
	default:
		error("Unknown action given.");
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
?>