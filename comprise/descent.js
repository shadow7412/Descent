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

/* Update functions */
//Callback for update(), recieves the result and makes sure it's valid(ish) before pumping it through the works
//Sets global(ish) placeholers
function updateJSON(de){
	if(de==null){
		console.error("Invalid JSON... maybe.");
		return;
	} else if(de.error){
		if(de.error=="login"){
			message("The password for this campaign was not recognised.");
			$('.main').addClass("invisible");
			$('#loadstart').removeClass("invisible");
		}
		console.log(de.error);
		return;
	}
	d = de; //pump it out into global space
	$('#loadblock').addClass('invisible');//remove pane if it was showing
	if(d.message != null && d.message != ""){
		$('#dialog').html(d.message);
		$('#dialog').dialog();
	}
	$("#graphtotier").progressbar({value: (((d.heroes.conquest + d.overlord.conquest)-(d.tier*200))/2)});//progress bar will autocap at 100%
	$("#ticker").html("Hero Conquest: "+d.heroes.conquest+", Hero Gold: "+d.heroes.gold+", Overlord Conquest: "+d.overlord.conquest+", Divine Favour:"+parseInt(d.heroes.conquest-d.overlord.conquest)/25);
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
		for(instance in d.instances){
			instances += "<li onclick=\"doInstance('"+instance+"')\">"+instance+" - ";
			if(d.instances[instance].completed)
				instances+="Completed";
			else if(d.instances[instance].fled)
				instances+="Fled";
			else if(d.instances[instance].discovered)
				instances+="Discovered";
			else
				instances+="Not Discovered";
			instances+="</li>";
		}
		$("#hoinstances").html(instances);
	} else { //overlord
	
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
		
	} else { //overlord
		var players = "";
		for(p in d.hero){
			players += "<li onclick=\"action('death',"+p+")\">"+d.hero[p].hero+" L:"+d.hero[p].level+" C:"+d.hero[p].curses+"</li>";
		}
		$("#killplayer").html(players);
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
	$.post("operations/update.php?cid="+campaign,"do="+type+"&to="+to,function(a){
															timer = setInterval("update()",pollRate);
															updateJSON(a);
														});
}
function doInstance(name){
	if(d.instances[name].fled || d.instances[name].completed){
		//Fill in all instance details
		$("#instance").dialog({title:name});
	} else {
		if(confirm("Do you want to enter "+name+"?"))
			action("enter",name);
		else
			action("discover",name);
	}
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
function checkHash(){
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