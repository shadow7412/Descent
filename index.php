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
			<div id="graphtotier"></div>
			<div id="ticker"></div>
		</div>
		<div class="main" id="newload"><?php include "pages/newload.php";?>
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
				<li onclick="event('kill','lieutenant')">Kill Lieutenant</li>
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