<?php
ob_start("ob_gzhandler"); //Gzip page
require_once "include/db.php";
$db = new db; //Create database object
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Descent</title>
		<link rel="stylesheet" type="text/css" href="comprise/jquery-ui-1.8.18.custom.css" />
		<link rel="stylesheet" type="text/css" href="comprise/descent.css" />
		<script type="text/javascript" src="comprise/js.php"></script>
	</head>
	<body onload="startup()">
		<div id="loadblock" class="ui-widget-overlay invisible" style="z-index: 1001; width: 100%; height: 100%;"><!-- For display when we don't want to recieve input --></div>
		<div id="dialog" class="invisible" title="Message"></div>
		<div id="instance" class="invisible">
			Information about already completed instance
		</div>
		<div class="control invisible">
			<button onclick="refresh()" oncontextmenu="window.open('operations/update.php?cid='+campaign);return false">Refresh</button>
			<button onclick="pause(this)">Pause</button>
			<button onclick="$('#overview').toggleClass('invisible')">Overview</button>
			<button onclick="$('#log').toggleClass('invisible')">Advanced</button>
			<button onclick="exit()">Exit</button>
			<div id="graphtotier"></div>
			<div id="ticker"></div>
		</div>
		<div class="main" id="newload"><?php include "pages/newload.php";?></div>
		<div class="main invisible" id="whichside">
			<div style="float:left" onclick="setPlayer(false)"><span></span><img alt="Overlord" src="comprise/images/threat-token.png"></div>
			<div style="float:right" onclick="setPlayer(true)"><span></span><img alt="Heroes" src="comprise/images/heroes-figures.png"></div>
		</div>
		<div class="main invisible" style="z-index:2;" id="log"><?php include "pages/log.php";?></div>
		<div class="main invisible" style="z-index:2;" id="overview"><?php include "pages/overview.php";?></div>
		<div class="main invisible" id="oloverworld"><?php include "pages/oloverworld.php";?></div>
		<div class="main invisible" id="ploverworld"><?php include "pages/ploverworld.php";?></div>
		<div class="main invisible" id="olinstance"><?php include "pages/olinstance.php";?></div>
		<div class="main invisible" id="plinstance"><?php include "pages/plinstance.php";?></div>
	</body>
</html>