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
		<div class="control invisible">
			<button onclick="update()" oncontextmenu="window.open('operations/update.php');return false">Refresh</button>
			<button onclick="pause(this)">Pause</button>
			<div id="graphtotier"></div>
		</div>
		<div class="main" id="newload"><center>Select a campaign:
			<table onclick="createCampaign()" class="campaignselect">
				<tr><td>New Campaign</td></tr>
			</table>
			<form id="newcampaign" title="New Campaign Setup" class="invisible">
				NOT YET IMPLEMENTED
			</form>
			<?php 
				$db->query("SELECT * FROM `campaign` ORDER BY `id` DESC");
				while($row = $db->get()){
					echo "<table onclick=\"selectCampaign({$row['id']})\" oncontextmenu=\"if(confirm('Do you REALLY want to delete this campaign?')) deleteCampaign({$row['id']},this); return false;\" class=\"campaignselect\">
						<tr>
							<td>{$row['overlord']}</td>
							<td>{$row['plot']}</td>
							<td>{$row['avatar']}</td>
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