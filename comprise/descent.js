/*
Things worth knowing:
action(do,to) <-posts to update function. Most onclicks will use this. Resets the updater after getting the latest info.
pollRate <- how often the state is refreshed
*/
/*Global variables*/
var pollRate = 3000; //How often (in milliseconds) should the game state be refreshed?
var isHero = null;   //Is the player on the hero side or the overlord side?
var campaign = null; //The id of the campaign
var timer = null;    //The timer reference - so it can be cancelled
var d = null;        //The big d. Contains the full game state.
var log = null;		 //The datatables reference - for refreshing ajax info.
var oldmessage = null;//So we don't get messages everytime the page reloads.

//For FF compatability
if(console == null) console={"log":function(){},"error":function(){}};
/* Update functions */
//Callback for update(), recieves the result and makes sure it's valid(ish) before pumping it through the works
//Sets global(ish) placeholers
function updateJSON(de){
	if(de==null){
		console.error("Invalid JSON... maybe.");
		return;
	} else if(de.error){
		console.log(de.error);
		return false;
	}
	d = de; //pump it out into global space
	$('#loadblock').addClass('invisible');//remove pane if it was showing
	if(d.message != null && d.message != ""&& d.message != oldmessage){
		$('#dialog').html(d.message);
		$('#dialog').dialog();
		oldmessage = d.message;
	}
	$("#graphtotier").progressbar({value: (((d.heroes.conquest + d.overlord.conquest)-(d.tier*200))/2)});//progress bar will autocap at 100%
	$("#ticker").html("Week: "+d.week+", Hero Conquest: "+d.heroes.conquest+", Hero Gold: "+d.heroes.gold+", Overlord Conquest: "+d.overlord.conquest+", Divine Favour:"+parseInt((d.heroes.conquest-d.overlord.conquest)/25));
	//update the applicable view:
	if(d.heroes.location=="overworld")
		updateOverworld();
	else 
		updateInstance();	
}
//Updates overworld view for player/overlord
function updateOverworld(){
	//Make sure overworld is visible
	$("#"+(isHero?"pl":"ol")+"overworld").removeClass("invisible");
	$("#"+(isHero?"pl":"ol")+"instance").addClass("invisible");
	
	if(isHero){//hero
		var instances = "";
		var className = "";
		for(instance in d.instances){
			if((d.tier==3)==(instance=="Final Battle")){//show only final battle in plat tier, otherwise only show everything else
				if(instance == d.heroes.rumour)
					className="instancerumour"
				else if(d.instances[instance].completed)
					className="instancecompleted"
				else if(d.instances[instance].fled)
					className="instancefled"
				else if(d.instances[instance].discovered)
					className="instancediscovered"
				else
					className="instanceunknown"
				instances += "<li class=\""+className+"\" onclick=\"doInstance('"+instance+"')\">"+instance+" - "+className;
				instances+="</li>";
			}
		}
		$("#hoinstances").html(instances);
		var stats = "";
		for(p in d.hero){
			stats += "<li class=\""+d.hero[p].hero.cull()+"\" onclick=\"plUpgrade("+p+")\">"+d.hero[p].hero+"<br/>"+(d.heroes.conquest - d.hero[p].xp)+"XP</li>";
		}
		$('#postats').html(stats);
	} else { //overlord
		var deadcities = 0;
		for(city in d.cities) if(d.cities[city].razed == true) deadcities++;
		$('#ootime').html("Time passes<br/>"+deadcities+" razed");
		$('#oocard').html("Play Card<br/>"+(d.overlord.conquest-d.overlord.xp)+"XP");
	}
}
//Updates instance view for player/overlord
function updateInstance(){
	//make sure instance is visible
	//fade(in|out)?
	$("#"+(isHero?"pl":"ol")+"overworld").addClass("invisible");
	$("#"+(isHero?"pl":"ol")+"instance").removeClass("invisible");
	//Overlord
	if(isHero){
		var lastlevel = d.instances[d.heroes.location].numberoflevels == d.instances[d.heroes.location].level;
		$("#piname").html(d.heroes.location + " " + d.instances[d.heroes.location].level+"/"+d.instances[d.heroes.location].numberoflevels);
		$('#plexit').html(lastlevel?"Exit Dungeon":"Enter Portal to Level "+(d.instances[d.heroes.location].level+1));
		if(d.level.bossdead)
			$('#plboss')[0].outerHTML = "<li id=\"plboss\">Boss Already Dead</li>"
		else
			if(d.heroes.location == "Lieutenant Battle")
				$('#plboss')[0].outerHTML = "<li id=\"plboss\" onclick=\"action('kill','lieutenant')\">Kill Lieutenant</li>"
			else
				$('#plboss')[0].outerHTML =	lastlevel?"<li id=\"plboss\" onclick=\"action('kill','fboss')\">Kill Final Boss</li>":"<li id=\"plboss\" onclick=\"action('kill','boss')\">Kill Boss</li>";
	} else { //overlord
		$("#oiname").html(d.heroes.location + " " + d.instances[d.heroes.location].level+"/"+d.instances[d.heroes.location].numberoflevels);
		var players = "";
		var curses = "";
		for(p in d.hero){
			players += "<li class=\""+d.hero[p].hero.cull()+"\" onclick=\"action('death',"+p+")\">"+d.hero[p].hero+"<br/>L:"+d.hero[p].level+"<br/>C:"+d.hero[p].curses+"<br/>K: "+d.instances[d.heroes.location].deaths[p]+"</li>";
			curses += "<li onclick=\"action('curse',"+p+")\">Curse<br/>"+d.hero[p].hero+"</li>";
		}
		$("#olkillplayer").html(players);
		$("#olcurseplayer").html(curses);
		if(d.level.deck>0) $('#oideck')[0].style.background = "red";
		else $('#oideck')[0].style.background = "";
	}
}

/* Event functions*/
//called when an instance is starting. Checks to see if player wants to just explore or actually enter
//If we've been, just show stats for when we went
//Main function for anything that happens.
//Sends type and to up to update.php for it to read
//Updates display the moment we get a response
function action(type,to){
	clearInterval(timer);
	timer = null;
	$('#loadblock').removeClass('invisible');
	$.post("operations/update.php?cid="+campaign,"action="+type+"&to="+to,function(a){
															timer = setInterval("update()",pollRate);
															console.log("Updated with", a);
															updateJSON(a);
														});
}
function doInstance(name){
	if(d.instances[name].fled || d.instances[name].completed){
		//Fill in all instance details
		$("#instance").html("Got to level "+d.instances[name].level+"/"+d.instances[name].numberoflevels+" and died "+(d.instances[name].deaths[0]+d.instances[name].deaths[1]+d.instances[name].deaths[2]+d.instances[name].deaths[3])+" times");
		$("#instance").dialog({
			title:name,
			buttons:[
				{
				text:"Reopen",
				click:function(){
					if(confirm('This is not usually possible, but some cards or events may trigger this. Are you sure?')){
						action("reopen",$(this).dialog("option", "title"));
						$(this).dialog("close");
					}
				}
			},{
				text:"Close",
				click:function(){$(this).dialog("close");}
			}]});
	} else {
		if(confirm("Do you want to enter "+name+"?"))
			action("enter",name);
		else
			action("discover",name);
	}
}
function olCard(){
	$('#dialog').html("<form><table><tr><td>Name of card:</td><td><input name=\"name\"/></td></tr><tr><td>Cost to play:</td><td><input name=\"cost\" onkeydown=\"return isNumberKey(event)\"/></td></tr></form>");
	$('#dialog').dialog({
		buttons: [
			{
				text:"Play",
				click:function(){action("card",this.children[0].cost.value+this.children[0].name.value);$(this).dialog("close");}
			},{
				text:"Cancel",
				click:function(){$(this).dialog("close")}
			}]
	});
}
function olRaze(){
	var raze="<ul class=\"box\">";
	for(city in d.cities){
		if(d.cities[city].razed)
			raze+="<li>"+city+"<br/>(Already razed)</li>";
		else
			raze+="<li onclick=\"action('raze','"+city+"');$(this).parent().parent().dialog('close');\">"+city+"</li>";
	}
	$('#dialog').html(raze+"</ul>");
	$('#dialog').dialog({
		width:550,
		buttons: [
			{
				text:"Cancel",
				click:function(){$(this).dialog("close")}
			}]
	});
}
function plRumour(){
	var rum = "<ul class=\"box\">";
	for(i in d.instances){
		if(d.instances[i].fled==false && d.instances[i].completed==false && d.instances[i].physical==true)
			rum += "<li onclick=\"plSetRumour('"+i+"');$(this).parent().parent().dialog('close')\">"+i+"</li>";
	}
	$('#dialog').html(rum+"</ul>");
	$('#dialog').dialog({
		width:550,
		buttons: [
			{
				text:"Ignore",
				click:function(){action('rumour');$(this).dialog("close")}
			},{
				text:"Cancel",
				click:function(){$(this).dialog("close")}
			}]
	});
}
function plSetRumour(i){
	action("rumour",i);
}
function plChest(){
	var table = '<form><table><tr><td></td><td>Power</td><td>Surge</td><td>Blank</td></tr>';
	   table += '<tr><td>First Dice</td><td><input type="radio" name="first" value="p" checked></td><td><input type="radio" name="first" value="s"></td><td><input type="radio" name="first" value="b"></td></input>';
	   table += '<tr><td>Second Dice</td><td><input type="radio" name="second" value="p" checked></td><td><input type="radio" name="second" value="s"></td><td><input type="radio" name="second" value="b"></td></input>';
	   table += '<tr><td>Third Dice</td><td><input type="radio" name="third" value="p" checked></td><td><input type="radio" name="third" value="s"></td><td><input type="radio" name="third" value="b"></td></input>';
	   table += '<tr><td>Fourth Dice</td><td><input type="radio" name="fourth" value="p" checked></td><td><input type="radio" name="fourth" value="s"></td><td><input type="radio" name="fourth" value="b"></td></input>';
	   table += "</table></form>";
	$('#dialog').html(table);
	$('#dialog').dialog({
	buttons: [
		{
			text:"Submit",
			click:function(){var data = $(this).children('form').serializeArray(); console.log(data);action("chest",data[0].value+data[1].value+data[2].value+data[3].value);$(this).dialog("close");}
		},{
			text:"Cancel",
			click:function(){$(this).dialog("close")}
		}]
});
}
function plBarrel(){
	
}
function plExit(){
	if(d.instances[d.heroes.location].level == d.instances[d.heroes.location].numberoflevels)
		action("finish","dungeon");
	else
		action("finish","level");
}
/* Low-level functions*/
//if console doesn't work, then just stay quiet instead of breaking javascript
if(console==null) console = {"log":function(){},"error":function(){}};
//Replacement of alert()
function message(message){
	$('#dialog').html(message);
	$('#dialog').dialog();
}
//Checks page hash at start of game.
function startup(){
	//hashes are in the form px - p=player type (1 character, x=campaign id
	if(location.hash != ""){
		if(location.hash.substr(1,1)=='p'){
			campaign = location.hash.substr(2);
			setPlayer(true);
		} else if(location.hash.substr(1,1)=='o'){
			campaign = location.hash.substr(2);
			setPlayer(false);
		}
		else {
			location.hash="";
			return;
		}
		$('#newload').addClass('invisible');
	}
	$(".heroName").autocomplete({
		source: heroNames
	});
}
//Force refresh
function refresh(){
	clearInterval(timer);
	timer = null;
	$('#loadblock').removeClass('invisible');
	update();
	timer = setInterval("update()",pollRate);
}
//Pause/resume interval
function pause(button){
	if(button.innerHTML == "Pause"){
		clearInterval(timer);
		timer = null;
		button.innerHTML = "Resume";
	} else {
		button.innerHTML = "Pause";
		update();
		timer = setInterval("update()",pollRate);
	}
}
//jump back to main menu
function exit(){
	clearInterval(timer);
	timer = null;
	location.hash = '';
	$(".main").addClass('invisible');
	$(".control").addClass('invisible');
	$("#newload").removeClass('invisible');
}
//request update - can contain extra info for anything that has happened
function update(){
	$.getJSON("operations/update.php","cid="+campaign,function(data,textStatus,xhr){
		updateJSON(data);
	}).error(function(data,handle){
		exit();
	});
}
/*Setup functions*/
//Opens setup pane
function createCampaign(){
	//Open first campaign creation div
	$("#setup1").removeClass("invisible");
	$("#setup1").find('input').first().focus();
}
//Moves setup pane to the next one
function setup(that){
	var mum = $(that).parents('div').first();
	mum.addClass('invisible');
	mum.next().removeClass('invisible');
	mum.next().find('input').first().focus();
}
//Posts form to the ethers
function completeSetup(form){
	$(".setup").addClass('invisible');
	$.post("operations/create.php",
		$(form).serialize(),
		function(data,textStatus,xhr){
			$("#loadblock").addClass("invisible");
			if(data=="failed"){
				message("Creating this campaign failed :( Check your values and try again in a few minutes.");
				$("#loadblock").removeClass("invisible");
				$("#setup1").removeClass('invisible');
				console.log(xhr);
			} else {
				$("#newcampaign").after('<table class="campaignselect bronze" onclick="selectCampaign('+data+',this)" oncontextmenu="if(confirm(\'Do you REALLY want to delete this campaign?\')) deleteCampaign('+data+',this); return false;"><tr><td>Campaign info not populated - refresh to do... that</td></tr></table>');
				$("#loadblock").removeClass("invisible");
				cancelSetup(form); //reset form in case another campaign is created
				selectCampaign(data);
			}
		}
	);
}
//Reverts form back to default values, hides it.
function cancelSetup(form){
	form.reset();
	$(".setup").addClass('invisible');
}
//Sends post to have campaign flagged as deleted. Removes campaign from list (silent success/failure)
function deleteCampaign(id,element){
	$.post("operations/delete.php","id="+id);
	element.outerHTML = "";
}
//sets global id, shows player selection screen
function selectCampaign(id){
	campaign = id;
	$("#loadblock").addClass("invisible");
	$("#newload").addClass("invisible");
	$("#whichside").removeClass("invisible");
}
//Sets global variable, shows game screen.
//Sets location.hash so if page is refreshed or bookmarked we'll go straight back to the action
function setPlayer(p){ //true = heroes
	isHero = p;
	$("#whichside").addClass('invisible');
	$(".control").removeClass('invisible');
	$('#loadblock').addClass('invisible');
	location.hash = (isHero?"p":"o")+campaign;
	clearInterval(timer);
	update();
	timer = setInterval("update()",pollRate);
}
//For input values - make sure we only get a number
function isNumberKey(evt){
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57))
		return false;
	else
		return true;
}
String.prototype.cull=function(){
	return this.replace(/ /g,"_").toLowerCase()
}
//add ordinal function to Number object
Number.prototype.ordinal=function(){
    var nModTen = this % 10;
    return (this + ['th','st','nd','rd'][nModTen > 3 ?
        0 :
        ( this % 100 - nModTen != 10) * nModTen]);
}

var heroNames = Array("Andira Runehand",
						"Arvel Worldwalker",
						"Aurim",
						"Battlemage Jaes",
						"Bogran the Shadow",
						"Brother Gherinn",
						"Brother Glyr",
						"Challara",
						"Corbin",
						"Eliam",
						"Grey Ker",
						"Hugo the Glorious",
						"Ispher",
						"Jonas the Kind",
						"Karnon",
						"Kirga",
						"Krutzbeck",
						"Landrec the Wise",
						"Laughin Buldar",
						"Laurel of Bloodwood",
						"Lindel",
						"Lord Hawthorne",
						"Lyssa",
						"Mad Carthos",
						"Mordrog	JITD",
						"Nanok of the Blade",
						"Nara the Fang",
						"Okaluk and Rakash",
						"One Fist",
						"Red Scorpion",
						"Ronan of the Wild",
						"Runemaster Thorn",
						"Runewitch Astarra",
						"Sahla",
						"Shiver",
						"Silhouette",
						"Sir Valadir",
						"Spiritspeaker Mok",
						"Steelhorns",
						"Tahlia",
						"Tatianna",
						"Tetherys",
						"Tobin Farslayer",
						"Trenloe the Strong",
						"Truthseer Kel",
						"Varikas the Dead",
						"Vyrah the Falconer",
						"Zyla");