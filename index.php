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
		<div id="loadblock" class="ui-widget-overlay invisible" style="z-index: 1001; width: 100%; height: 100%;"></div>
		<div class="control invisible">
			<button onclick="refresh()" oncontextmenu="window.open('operations/update.php?cid='+campaign);return false">Refresh</button>
			<button onclick="pause(this)">Pause</button>
			<button onclick="exit()">Exit</button>
			<div id="graphtotier"></div>
			<div id="ticker"></div>
		</div>
		<div class="main" id="newload"><?php include "pages/newload.php";?></div>
		<div class="main invisible" id="whichside">
			<div style="float:left" onclick="setPlayer(false)">Overlord</div>
			<div style="float:right" onclick="setPlayer(true)">Heroes</div>
		</div>
		<div class="main invisible" id="oloverworld"><?php include "pages/oloverworld.php";?></div>
		<div class="main invisible" id="ploverworld"><?php include "pages/ploverworld.php";?></div>
		<div class="main invisible" id="olinstance"><?php include "pages/olinstance.php";?></div>
		<div class="main invisible" id="plinstance"><?php include "pages/plinstance.php";?></div>
	</body>
</html>