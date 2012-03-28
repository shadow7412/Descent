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
		<button onclick="update()" oncontextmenu="window.open('update.php');return false">Refresh</button>
		<div class="main" id="newload"><center>Select a campaign:
			<table class="campaignselect">
				<tr>
					<th><input type="submit" value="Create"></th>
					<th><input name="overlord" value="overlord"/></th>
					<th><input name="plot" value="plot"/></th>
					<th><input name="avatar" value="avatar"/></th>
				</tr>
				<tr>
					<td><input name="hero1" value="hero1"/></td>
					<td><input name="hero2" value="hero2"/></td>
					<td><input name="hero3" value="hero3"/></td>
					<td><input name="hero4" value="hero4"/></td>
				</tr>
				<tr>
					<td><input name="player1" value="player1"/></td>
					<td><input name="player2" value="player2"/></td>
					<td><input name="player3" value="player3"/></td>
					<td><input name="player4" value="player4"/></td>
				</tr>
			</table>
			<?php 
				$db->query("SELECT * FROM `campaign` ORDER BY `id` DESC");
				while($row = $db->get()){
					echo "<table onclick=\"selectCampaign({$row['id']})\" class=\"campaignselect\">
						<tr>
							<th>ID: {$row['id']}</th>
							<th>{$row['overlord']}</th>
							<th>{$row['plot']}</th>
							<th>{$row['avatar']}</th>
						</tr>
						<tr>
							<td>{$row['hero1']}</td>
							<td>{$row['hero2']}</td>
							<td>{$row['hero3']}</td>
							<td>{$row['hero4']}</td>
						</tr>
						<tr>
							<td>{$row['player1']}</td>
							<td>{$row['player2']}</td>
							<td>{$row['player3']}</td>
							<td>{$row['player4']}</td>
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
		<div class="main invisible" id="oloverworld">Overlord - overworld</div>
		<div class="main invisible" id="ploverworld">Heroes - overworld</div>
		<div class="main invisible" id="olinstance">Overlord - instance</div>
		<div class="main invisible" id="plinstance">Heroes - instance</div>
	</body>
</html>