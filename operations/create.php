<?php
include "../include/db.php";
$db = new db;
//Some generic variables to save writing it out 4038 times.
$instances = array("discovered"=>false,"completed"=>false,"fled"=>false);
$cities = array("razed"=>false,"discovered"=>false);

//Create new campaign
$bones = array(
	"loadtime"=>0,
	"tier"=>0,
	"week"=>0,
	"overlord"=>array(
		"player"=>$_POST['oname'],
		"plot"=>$_POST['oplot'],
		"avatar"=>$_POST['oavatar'],
		"conquest"=>$_POST['oconquest'],
		"spentconquest"=>0,
	),
	"heroes"=>array(
		"conquest"=>$_POST['hconquest'],
		"gold"=>$_POST['hgold'],
		"location"=>"overworld",
	),
	"hero"=>array(
		array(
			"player"=>$_POST['h1player'],
			"hero"=>$_POST['h1name'],
			"level"=>$_POST['h1level'],
			"curses"=>0,
			"xp"=>$_POST['hxp'],
		),
		array(
			"player"=>$_POST['h2player'],
			"hero"=>$_POST['h2name'],
			"level"=>$_POST['h2level'],
			"curses"=>0,
			"xp"=>$_POST['hxp'],
		),
		array(
			"player"=>$_POST['h3player'],
			"hero"=>$_POST['h3name'],
			"level"=>$_POST['h3level'],
			"curses"=>0,
			"xp"=>0,
		),
		array(
			"player"=>$_POST['h4player'],
			"hero"=>$_POST['h4name'],
			"level"=>$_POST['h4level'],
			"curses"=>0,
			"xp"=>0,
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
		"Azure Peaks"=>$instances,
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
		"Bright Sea"=>$instances,
		"Burning Bay"=>$instances,
		"Cerridor Sea"=>$instances,
		"Midnight Cove"=>$instances,
		"Narrows of Gracor"=>$instances,
		"Seda of the Redtyde"=>$instances,
		"Shrouded Gulf"=>$instances,
		"Terrents of Dreadpeace"=>$instances,
		"Weeping Reach"=>$instances,
		"Winnowing Straits"=>$instances,
	)
);
//Try to convert to json
$json = json_encode($bones);
if($json===false) die("failed");
//Place state into db.
$dbjson = mysql_real_escape_string($json);
if(@$db->query("INSERT INTO `campaign` (`created`,`state`) VALUES (CURRENT_TIMESTAMP, '$dbjson')")){
	$db->commit();
	$db->query("SELECT `id` FROM `campaign` ORDER BY `id` DESC LIMIT 1");
	$row = $db->get();
	//Reply with campaign id
	echo $row['id'];
} else {
	echo "failed";
}
?>