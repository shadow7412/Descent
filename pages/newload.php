<?php
$cities = array("Dallak","Gafford","Garnott","Hardell","Orris","Shellport","Tarianor","Trelton");
$instances = array("Azure Peaks","Barren Moors","Bog of Vipers","Crystal Caves","Firecloud Range","Forgotten Mire, The","Hollow Woods, The","Moonraven Heights","Mount Arrik","Mount Orrik","Mountains of Ash","Plains of Red Ice","Quelsdone Flood","River Red Marsh","Shadow Hall","Shivering Hills","Solace Mountain","Stagwood Forest","Sunset Hills","Withered Plains","Bright Sea","Burning Bay","Cerridor Sea","Midnight Cove","Narrows of Gracor","Seda of the Redtyde","Shrouded Gulf","Terrents of Dreadpeace","Weeping Reach","Winnowing Straits");
?><center>
<img src="comprise/images/logo.png" alt="Descent"/>
<table onclick="createCampaign()" class="campaignselect">
	<tr><td>New Campaign</td></tr>
</table>
<form id="newcampaign">
	<div id="setup1" class="invisible setup" title="New Campaign - Overlord">
	<h2>Overlord Setup</h2>
		<table>
		<tr><td>Overlord's Name</td><td><input value="John Smith" name="oname"/></td></tr>
		<tr><td>Plot</td><td><input name="oplot" value="Fury of the Storm"/></td></tr>
		<tr><td>Avatar</td><td><input name="oavatar" value="The Count"/></td></tr>
		<tr><td>Keep</td><td><select name="okeep"><?php foreach($instances as $value) echo "<option value=\"$value\">$value</option>";?></select></td></tr>
		<tr><td>Starting conquest</td><td><input name="oconquest" value="0" onkeypress="return isNumberKey(event)"/></td></tr>
		<tr><td><button onclick="setup(this);return false">Next</button><button onclick="cancelSetup(this.form);return false">Cancel</button></td></tr>
		</table>
	</div>
	<div id="setup2" class="invisible setup" title="New Campaign - Starting stats">
	<h2>Intial Stats Setup</h2>
		<table>
		<tr><td>Player's starting Conquest</td><td><input name="hconquest" value="0" onkeypress="return isNumberKey(event)"/></td></tr>
		<tr><td>Initial Homeport</td><td><select name="hport"><?php foreach($cities as $value) echo "<option value=\"$value\">$value</option>";?></select></td></tr>
		<tr><td>Starting Gold</td><td><input name="hgold" value="1200" onkeypress="return isNumberKey(event)"/></td></tr>
		<tr><td><button onclick="setup(this);return false">Next</button><button onclick="cancelSetup(this.form);return false">Cancel</button></td></tr>
		</table>
	</div>
	<div id="setup3" class="invisible setup" title="New Campaign - Player 1">
	<h2>Player 1 Setup</h2>
		<table>
		<tr><td>Player's Name</td><td><input name="h1player" value="Henry McDonald"/></td></tr>
		<tr><td>Hero's Name</td><td><input name="h1name" value="Nanok of the Blade"/></td></tr>
		<tr><td>Hero's Level</td><td><input name="h1level" value="4" onkeypress="return isNumberKey(event)"/></td></tr>	
		<tr><td><button onclick="setup(this);return false">Next</button><button onclick="cancelSetup(this.form);return false">Cancel</button></td></tr>
		</table>
	</div>
	<div id="setup4" class="invisible setup" title="New Campaign - Player 2">
	<h2>Player 2 Setup</h2>
		<table>
		<tr><td>Player's Name</td><td><input name="h2player" value="Doug Flemington"/></td></tr>
		<tr><td>Hero's Name</td><td><input name="h2name" value="Varikas the Dead"/></td></tr>
		<tr><td>Hero's Level</td><td><input name="h2level" value="4" onkeypress="return isNumberKey(event)"/></td></tr>
		<tr><td><button onclick="setup(this);return false">Next</button><button onclick="cancelSetup(this.form);return false">Cancel</button></td></tr>
		</table>
	</div>
	<div id="setup5" class="invisible setup" title="New Campaign - Player 3">
	<h2>Player 3 Setup</h2>
		<table>
		<tr><td>Player's Name</td><td><input name="h3player" value="Charlie Quagmire"/></td></tr>
		<tr><td>Hero's Name</td><td><input name="h3name" value="Runewitch Astarra"/></td></tr>
		<tr><td>Hero's Level</td><td><input name="h3level" value="2" onkeypress="return isNumberKey(event)"/></td></tr>	
		<tr><td><button onclick="setup(this);return false">Next</button><button onclick="cancelSetup(this.form);return false">Cancel</button></td></tr>
		</table>
	</div>
	<div id="setup6" class="invisible setup" title="New Campaign - Player 4">
	<h2>Player 4 Setup</h2>
		<table>
		<tr><td>Player's Name</td><td><input name="h4player" value="Stephanie Stephens"/></td></tr>
		<tr><td>Hero's Name</td><td><input name="h4name" value="Bogran the Shadow"/></td></tr>
		<tr><td>Hero's Level</td><td><input name="h4level" value="2" onkeypress="return isNumberKey(event)"/></td></tr>	
		<tr><td><button onclick="completeSetup(this.form);return false">Begin Campaign!</button><button onclick="cancelSetup(this.form);return false">Cancel</button></td></tr>
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