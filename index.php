<?php
ob_start("ob_gzhandler");
require_once "include/db.php";
$db = new db;
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Descent</title>
		<link rel="stylesheet" type="text/css" href="comprise/jquery-ui-1.8.18.custom.css" />
		<link rel="stylesheet" type="text/css" href="comprise/descent.css" />
		<script type="text/javascript" src="comprise/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="comprise/jquery-ui-1.8.18.custom.min.js"></script>
		<script type="text/javascript" src="comprise/descent.js"></script>
	</head>
	<body>
		<div id="loadblock" class="ui-widget-overlay invisible" style="z-index: 1001; width: 100%; height: 100%; "></div>
		<div class="control invisible">
			<button onclick="update()" oncontextmenu="window.open('operations/update.php');return false">Refresh</button>
			<button onclick="pause(this)">Pause</button>
			<div id="graphtotier"></div>
		</div>
		<div class="main" id="newload"><center>Select a campaign:
			<table onclick="createCampaign()" class="campaignselect">
				<tr><td>New Campaign</td></tr>
			</table>
			<form id="newcampaign">
				<div id="setup1" class="invisible" title="New Campaign - Overlord">
				<h2>Overlord Setup</h2>
					<table>
					<tr><td>Overlord's Name</td><td><input name="oname"/></td></tr>
					<tr><td>Plot</td><td><input name="oplot"/></td></tr>
					<tr><td>Avatar</td><td><input name="oavatar"/></td></tr>
					<tr><td>Starting conquest</td><td><input name="oconquest"/></td></tr>
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup2" class="invisible" title="New Campaign - Starting stats">
				<h2>Intial Stats Setup</h2>
					<table>
					<tr><td>Starting Conquest</td><td><input name="hconquest"/></td></tr>
					<tr><td>Starting Experience</td><td><input name="hxp"/></td></tr>
					<tr><td>Starting Gold</td><td><input name="hgold"/></td></tr>
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup3" class="invisible" title="New Campaign - Player 1">
				<h2>Player 1 Setup</h2>
					<table>
					<tr><td>Player's Name</td><td><input name="h1player"/></td></tr>
					<tr><td>Hero's Name</td><td><input name="h1name"/></td></tr>
					<tr><td>Hero's Level</td><td><input name="h1level"/></td></tr>	
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup4" class="invisible" title="New Campaign - Player 2">
				<h2>Player 2 Setup</h2>
					<table>
					<tr><td>Player's Name</td><td><input name="h2player"/></td></tr>
					<tr><td>Hero's Name</td><td><input name="h2name"/></td></tr>
					<tr><td>Hero's Level</td><td><input name="h2level"/></td></tr>
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup5" class="invisible" title="New Campaign - Player 3">
				<h2>Player 3 Setup</h2>
					<table>
					<tr><td>Player's Name</td><td><input name="h3player"/></td></tr>
					<tr><td>Hero's Name</td><td><input name="h3name"/></td></tr>
					<tr><td>Hero's Level</td><td><input name="h3level"/></td></tr>	
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup6" class="invisible" title="New Campaign - Player 4">
				<h2>Player 4 Setup</h2>
					<table>
					<tr><td>Player's Name</td><td><input name="h4player"/></td></tr>
					<tr><td>Hero's Name</td><td><input name="h4name"/></td></tr>
					<tr><td>Hero's Level</td><td><input name="h4level"/></td></tr>	
					<tr><td><button onclick="$($(this).parents('div')[0]).addClass('invisible');completeSetup(this.form);return false">Begin Campaign!</button></td></tr>
					</table>
				</div>
			</form>
			<?php 
				$db->query("SELECT * FROM `campaign` WHERE `deleted`=0 ORDER BY `id` DESC");
				while($row = $db->get()){
					echo "<table onclick=\"selectCampaign({$row['id']})\" oncontextmenu=\"if(confirm('Do you REALLY want to delete this campaign?')) deleteCampaign({$row['id']},this); return false;\" class=\"campaignselect\">
						<tr>
							<td>last played: {$row['played']}</td>
						</tr>
					</table>";
				}
			?>
			</center>
		</div>
		<div class="main invisible" id="whichside">
			<div style="float:left" onclick="setPlayer(true)">Overlord</div>
			<div style="float:right" onclick="setPlayer(true)">Heroes</div>
		</div>
		<div class="main invisible" id="oloverworld">
			Overlord - overworld
		</div>
		<div class="main invisible" id="ploverworld">
			Heroes - overworld
		</div>
		<div class="main invisible" id="olinstance">
			Overlord - instance
		</div>
		<div class="main invisible" id="plinstance">
			Heroes - instance
		</div>
	</body>
</html>