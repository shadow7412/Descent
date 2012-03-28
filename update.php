<?php
//Give us the default structure, so it can be altered as we go through the database
$bones = array (
		  'message' => NULL,
		  'campaignid' => 0,
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
			  'name' => 'Unnamed',
			  'xp' => 0,
			  'level' => 1,
			  'curses' => 0,
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
		  ),
	);
//Update the database with all of the events that have taken place
if(!isset($_GET['cid'])){
	$bones['message'] = "A campaign ID was not supplied.";
	die(json_encode($bones));
}

echo json_encode($bones);
?>