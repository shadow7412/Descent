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

//Give us the default structure, so it can be altered as we go through the database
$bones = array (
		  'message' => "",
		  'loadtime' => 0,
		  'campaignid' => $_GET['cid'],
		  'phase' => 0,
		  'instances' => 
		  array (
			'azurePeaks' => 
			array (
			  'explored' => false,
			  'fled' => false,
			  'completed' => false,
			),
		  ),
		  'cities' => 
		  array (
			'dallak' => 
			array (
			  'razed' => false,
			  'siege' => 0,
			),
		  ),
		  'heroes' => 
		  array (
			0 => 
			array (
			  'name' => 'Hero1',
			  'xp' => 0,
			  'level' => 1,
			  'curses' => 0,
			  'deaths' => 0,
			  'powerdie' => 3,
			  'skills' => 1,
			  'traininghealth' => 0,
			  'trainingfatigue' => 0,
			),
			array (
			  'name' => 'Hero2',
			  'xp' => 0,
			  'level' => 1,
			  'curses' => 0,
			  'deaths' => 0,
			  'powerdie' => 3,
			  'skills' => 1,
			  'traininghealth' => 0,
			  'trainingfatigue' => 0,
			),
			array (
			  'name' => 'Hero3',
			  'xp' => 0,
			  'level' => 1,
			  'curses' => 0,
			  'deaths' => 0,
			  'powerdie' => 3,
			  'skills' => 1,
			  'traininghealth' => 0,
			  'trainingfatigue' => 0,
			),
			array (
			  'name' => 'Hero4',
			  'xp' => 0,
			  'level' => 1,
			  'curses' => 0,
			  'deaths' => 0,
			  'powerdie' => 3,
			  'skills' => 1,
			  'traininghealth' => 0,
			  'trainingfatigue' => 0,
			),
		  ),
		  'hero' => 
		  array (
			'conquest' => 0,
			'gold' => 0,
		  ),
		  'overlord' => 
		  array (
			'conquest' => 0,
			'spentconquest' => 0,
		  ),
	);
//Update the bones with the campaign info
$db->query("SELECT * FROM `campaign` WHERE `id`={$bones['campaignid']}");
if($row = $db->get()){
	$bones['heroes'][0]['name'] = $row['hero1'];
	$bones['heroes'][1]['name'] = $row['hero2'];
	$bones['heroes'][2]['name'] = $row['hero3'];
	$bones['heroes'][3]['name'] = $row['hero4'];
	$bones['heroes'][0]['level'] = $row['level1'];
	$bones['heroes'][1]['level'] = $row['level2'];
	$bones['heroes'][2]['level'] = $row['level3'];
	$bones['heroes'][3]['level'] = $row['level4'];
} else {
	ob_end_clean();
	$bones['message'] = "Campaign does not exist.";
	die(json_encode($bones));
}
//Update the bones with all of the events that have taken place
$db->query("SELECT * FROM `log` WHERE campaign='{$_GET['cid']}'");
while($row = $db->get()){
	switch($row['actiontype']){
		case("p1skill"):
		case("p2skill"):
		case("p3skill"):
		case("p4skill"):
			$p = substr($row['actiontype'],1,1)-1;//we want the array element
			$bones['hero'][$p]['xp'] += $row['player']; //this will be negative
			$bones['heroes'][$p]['skills']++;
			break;
		case("p1curse"):
		case("p2curse"):
		case("p3curse"):
		case("p4curse"):
			$p = substr($row['actiontype'],1,1)-1;//we want the array element
			$bones['heroes'][$p]['curses']++;
			break;
		case("p1death"):
		case("p2death"):
		case("p3death"):
		case("p4death"):
			$p = substr($row['actiontype'],1,1)-1;//we want the array element
			$bones['heroes'][$p]['deaths']++;
			$bones['overlord']['conquest'] += $row['overlord'];
			$bones['heroes'][$p]['curses'] = 0; //reset no of curses after death
			break;
		default:
			$bones['hero']['conquest'] += $row['player'];
			$bones['hero']['gold'] += $row['gold'];
			$bones['overlord']['conquest'] += $row['overlord'];
	}
}
/* Do any actions that just took place */
//TAKING INPUT
if(!isset($_GET['action'])){
	//skip all of the 'taking input' section
} elseif (preg_match("/p[d]death/",$_GET['action'])){
	//$price = 
	//$db->query("INSERT INTO `log` VALUES ()");
} elseif ($_GET['action'] == ""){
	
} elseif ($_GET['action'] == ""){
	
} elseif ($_GET['action'] == ""){
	
} elseif ($_GET['action'] == ""){
	
}

/*Work out how long it actually took and pump out json*/
$time = explode(' ', microtime());
$time = $time[1] + $time[0];
$bones['loadtime'] = round(($time - $start), 4);
ob_end_flush();
echo json_encode($bones);
?>