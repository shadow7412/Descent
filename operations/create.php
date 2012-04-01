<?php
include "../include/db.php";
$db = new db;
//Some generic variables to save writing it out 4038 times.
$instances = array(
	"discovered"=>false,
	"completed"=>false,
	"fled"=>false,
	"island"=>false,
	"numberoflevels"=>3,
	"level"=>1,
	"gold"=>0,
	"deaths"=>array(0,0,0,0),
	"hero"=>0,
	"overlord"=>0,
	"physical"=>true,
);
$islands = $encounter = $lieu = $final = $instances;
$islands['island'] = true;
$encounter['island'] = true;
$encounter['numberoflevels'] = $encounter['lieu'] = 1;
$final['numberoflevels'] = 5;
$final['physical'] = $encounter['physical'] = $lieu['physical'] = false;
$cities = array("razed"=>false);

//Create new campaign
$bones = array(
	"loadtime"=>0,
	"tier"=>0,
	"week"=>0,
	"overlord"=>array(
		"player"=>$_POST['oname'],
		"plot"=>$_POST['oplot'],
		"avatar"=>$_POST['oavatar'],
		"conquest"=>intVal($_POST['oconquest']),
		"xp"=>0,
		"keep"=>$_POST['okeep'],
	),
	"heroes"=>array(
		"conquest"=>intVal($_POST['hconquest']),
		"gold"=>intVal($_POST['hgold']),
		"location"=>"overworld",
		"homeport"=>$_POST['hport'],
		"rumour"=>null,
	),
	"hero"=>array(
		array(
			"player"=>$_POST['h1player'],
			"hero"=>$_POST['h1name'],
			"level"=>intVal($_POST['h1level']),
			"curses"=>0,
			"xp"=>0,
			"deaths"=>0,
		),
		array(
			"player"=>$_POST['h2player'],
			"hero"=>$_POST['h2name'],
			"level"=>intVal($_POST['h2level']),
			"curses"=>0,
			"xp"=>0,
			"deaths"=>0,
		),
		array(
			"player"=>$_POST['h3player'],
			"hero"=>$_POST['h3name'],
			"level"=>intVal($_POST['h3level']),
			"curses"=>0,
			"xp"=>0,
			"deaths"=>0,
		),
		array(
			"player"=>$_POST['h4player'],
			"hero"=>$_POST['h4name'],
			"level"=>intVal($_POST['h4level']),
			"curses"=>0,
			"xp"=>0,
			"deaths"=>0,
		),
	),
	"cities"=>array(
		"Dallak"=>$cities,
		"Gafford"=>$cities,
		"Garnott"=>$cities,
		"Hardell"=>$cities,
		"Orris"=>$cities,
		"Shellport"=>$cities,
		"Tarianor"=>$cities,
		"Trelton"=>$cities,
	),
	"instances"=>array(
		//Situational
		"Lieutenant Battle"=>$lieu,
		"Encounter"=>$encounter,
		"Final Battle"=>$final,
		//Places
		"Azure Peaks"=>$instances,
		"Barren Moors"=>$instances,
		"Bog of Vipers"=>$instances,
		"Crystal Caves"=>$instances,
		"Firecloud Range"=>$instances,
		"Forgotten Mire, The"=>$instances,
		"Hollow Woods, The"=>$instances,
		"Moonraven Heights"=>$instances,
		"Mount Arrik"=>$instances,
		"Mount Orrik"=>$instances,
		"Mountains of Ash"=>$instances,
		"Plains of Red Ice"=>$instances,
		"Quelsdone Flood"=>$instances,
		"River Red Marsh"=>$instances,
		"Shadow Hall"=>$instances,
		"Shivering Hills"=>$instances,
		"Solace Mountain"=>$instances,
		"Stagwood Forest"=>$instances,
		"Sunset Hills"=>$instances,
		"Withered Plains"=>$instances,
		"Bright Sea"=>$islands,
		"Burning Bay"=>$islands,
		"Cerridor Sea"=>$islands,
		"Midnight Cove"=>$islands,
		"Narrows of Gracor"=>$islands,
		"Seda of the Redtyde"=>$islands,
		"Shrouded Gulf"=>$islands,
		"Terrents of Dreadpeace"=>$islands,
		"Weeping Reach"=>$islands,
		"Winnowing Straits"=>$islands,
	),
	"level"=>array(
		"deck"=>0,
		"bossdead"=>false,
	),
);
//Try to convert to json
$json = json_encode($bones);
if($json===false) die("failed");
//Place state into db.
$dbjson = mysql_real_escape_string($json);
$password = md5($_POST['password']);
if(@$db->query("INSERT INTO `campaign` (`created`,`state`,`password`) VALUES (CURRENT_TIMESTAMP, '$dbjson','$password')")){
	$db->commit();
	$db->query("SELECT `id` FROM `campaign` ORDER BY `id` DESC LIMIT 1");
	$row = $db->get();
	$cid = $row['id'];
	//Create initial log
	$db->query("INSERT INTO `log` (`campaign`,`actionid`,`week`,`summary`,`player`,`gold`,`overlord`) VALUES ('$cid','0','0','Campaign begins','{$_POST['hconquest']}','{$_POST['hgold']}','{$_POST['oconquest']}')");
	//Reply with campaign id
	echo $cid;
} else {
	echo "failed";
}
?>