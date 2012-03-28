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
	<body onload="checkHash()">
		<div id="loadblock" class="ui-widget-overlay invisible" style="z-index: 1001; width: 100%; height: 100%; "></div>
		<div class="control invisible">
			<button onclick="refresh()" oncontextmenu="window.open('operations/update.php');return false">Refresh</button>
			<button onclick="pause(this)">Pause</button>
			<button onclick="exit()">Exit</button>
			<div id="graphtotier">Conquest until next tier</div>
		</div>
		<div class="main" id="newload"><center>Select a campaign:
			<table onclick="createCampaign()" class="campaignselect">
				<tr><td>New Campaign</td></tr>
			</table>
			<form id="newcampaign">
				<div id="setup1" class="invisible setup" title="New Campaign - Overlord">
				<h2>Overlord Setup</h2>
					<table>
					<tr><td>Overlord's Name</td><td><input name="oname"/></td></tr>
					<tr><td>Plot</td><td><input name="oplot"/></td></tr>
					<tr><td>Avatar</td><td><input name="oavatar"/></td></tr>
					<tr><td>Starting conquest</td><td><input name="oconquest"/></td></tr>
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup2" class="invisible setup" title="New Campaign - Starting stats">
				<h2>Intial Stats Setup</h2>
					<table>
					<tr><td>Starting Conquest</td><td><input name="hconquest"/></td></tr>
					<tr><td>Starting Experience</td><td><input name="hxp"/></td></tr>
					<tr><td>Starting Gold</td><td><input name="hgold"/></td></tr>
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup3" class="invisible setup" title="New Campaign - Player 1">
				<h2>Player 1 Setup</h2>
					<table>
					<tr><td>Player's Name</td><td><input name="h1player"/></td></tr>
					<tr><td>Hero's Name</td><td><input name="h1name"/></td></tr>
					<tr><td>Hero's Level</td><td><input name="h1level"/></td></tr>	
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup4" class="invisible setup" title="New Campaign - Player 2">
				<h2>Player 2 Setup</h2>
					<table>
					<tr><td>Player's Name</td><td><input name="h2player"/></td></tr>
					<tr><td>Hero's Name</td><td><input name="h2name"/></td></tr>
					<tr><td>Hero's Level</td><td><input name="h2level"/></td></tr>
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup5" class="invisible setup" title="New Campaign - Player 3">
				<h2>Player 3 Setup</h2>
					<table>
					<tr><td>Player's Name</td><td><input name="h3player"/></td></tr>
					<tr><td>Hero's Name</td><td><input name="h3name"/></td></tr>
					<tr><td>Hero's Level</td><td><input name="h3level"/></td></tr>	
					<tr><td><button onclick="$($(this).parents('div')[0]).next().removeClass('invisible'); $($(this).parents('div')[0]).addClass('invisible');return false">Next</button></td></tr>
					</table>
				</div>
				<div id="setup6" class="invisible setup" title="New Campaign - Player 4">
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
				$tiers = array("bronze","silver","gold","platinum");
				$db->query("SELECT * FROM `campaign` WHERE `deleted`=0 ORDER BY `id` DESC");
				while($row = $db->get()){
					$d = json_decode($row['state'],true);
					echo "<table onclick=\"selectCampaign({$row['id']})\" oncontextmenu=\"if(confirm('Do you REALLY want to delete this campaign?')) deleteCampaign({$row['id']},this); return false;\" class=\"campaignselect {$tiers[$d['tier']]}\">
						<tr>
							<td>Overlord: {$d['overlord']['player']}</td>
							<td>Player 1: {$d['hero'][0]['player']}</td>
							<td>{$d['hero'][0]['hero']}</td>
						</tr>
						<tr>
							<td>Avatar: {$d['overlord']['avatar']}</td>
							<td>Player 2: {$d['hero'][1]['player']}</td>
							<td>{$d['hero'][1]['hero']}</td>
						</tr>
						<tr>
							<td>Plot: {$d['overlord']['plot']}</td>
							<td>Player 3: {$d['hero'][2]['player']}</td>
							<td>{$d['hero'][2]['hero']}</td>
						</tr>
						<tr>
							<td>Plot: {$d['overlord']['plot']}</td>
							<td>Player 4: {$d['hero'][3]['player']}</td>
							<td>{$d['hero'][3]['hero']}</td>
						</tr>
					</table>";
				}
			?>
			</center>
		</div>
		<div class="main invisible" id="whichside">
			<div style="float:left" onclick="setPlayer(false)">Overlord</div>
			<div style="float:right" onclick="setPlayer(true)">Heroes</div>
		</div>
		<div class="main invisible" id="oloverworld">
			Overlord - overworld
		</div>
		<div class="main invisible" id="ploverworld">
			City:
			<ul>
				<li>Train</li>
				<li>Alchemist</li>
				<li>Port</li>
				<li>Pub</li>
			</ul>
			Enter Instance:
			<ul class="instances"></ul>
		</div>
		<div class="main invisible" id="olinstance">
			Overlord - instance
		</div>
		<div class="main invisible" id="plinstance">
			Stats:
			<ul class="playerstats">
			</ul>
			<ul>
				<li onclick="event('kill','master')">Kill Master</li>
				<li onclick="event('kill','boss')">Kill Boss</li>
				<li onclick="event('kill','fboss')">Kill Final Boss</li>
				<li onclick="event('glyph')">Unlock glyph</li>
				<li onclick="event('chest')">Treasure Chest</li>
				<li onclick="event('barrel')">Opened Barrel</li>
				<li onclick="event('finish','flee')">Flee</li>
				<li onclick="event('finish','level')">Finish level</li>
				<li onclick="event('finish','dungeon')">Finish dungeon</li>
			</ul>
		</div>
	</body>
</html>