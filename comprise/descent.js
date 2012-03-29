var pollRate = 5000; //How often (in milliseconds) should the game state be refreshed?
var isHero = null;   //Is the player on the hero side or the overlord side?
var campaign = null; //The id of the campaign
var timer = null;    //The timer reference - so it can be cancelled
var d = null;        //The big d. Contains the full game state.
function update(){
	$.getJSON("operations/update.php","cid="+campaign,function(data,textStatus,xhr){
		updateJSON(data);
	}).error(function(data,handle){console.error("Updating failed - ",handle)});
}
//The big update function.
function updateJSON(de){
	if(de==null){
		console.error("Invalid JSON... maybe.");
		return;
	}
	d = de; //pump it out into global space
	$('#loadblock').addClass('invisible');//remove pane if it was showing
	if(d.message != null && d.message != "") alert(d.message);
	$("#graphtotier").progressbar({value: (((d.heroes.conquest + d.overlord.conquest)-(d.tier*200))/2)});//progress bar will autocap at 100%
	$("#ticker").html("Hero Conquest: "+d.heroes.conquest+", Hero Gold: "+d.heroes.gold+", Overlord Conquest: "+d.overlord.conquest);
	//update the applicable view:
	if(d.heroes.location=="overworld")
		updateOverworld();
	else 
		updateInstance();	
}
function updateOverworld(){
	//Make sure overworld is visible
	$("#"+(isHero?"pl":"ol")+"overworld").removeClass("invisible");
	$("#"+(isHero?"pl":"ol")+"instance").addClass("invisible");
	var instances = "";
	for(instance in d.instances){
		instances += "<li onclick=\"startInstance('"+instance+"')\">"+instance+" - ";
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
	$(".instances").html(instances);
}
function updateInstance(){
	//make sure instance is visible
	$("#"+(isHero?"pl":"ol")+"overworld").addClass("invisible");
	$("#"+(isHero?"pl":"ol")+"instance").removeClass("invisible");
}
function createCampaign(){
	//Open first campaign creation div
	$("#setup1").removeClass("invisible");
}
function completeSetup(form){
	$("#loadblock").removeClass("invisible");
	$.post("operations/create.php",
		$(form).serialize(),
		function(data,textStatus,xhr){
			$("#loadblock").addClass("invisible");
			if(data=="failed"){
				alert("Creating this campaign failed :(")
				console.log(xhr);
			} else {
				selectCampaign(data);
			}
		}
	);
}
function deleteCampaign(id,element){
	$.post("operations/delete.php","id="+id);
	element.outerHTML = "";
}
function selectCampaign(id){
	campaign = id;
	$("#newload").addClass("invisible");
	$("#whichside").removeClass("invisible");
}
function setPlayer(p){ //true = heroes
	isHero = p;
	$("#whichside").addClass('invisible');
	$(".control").removeClass('invisible');
	$('#loadblock').removeClass('invisible');
	location.hash = (isHero?"p":"o")+campaign;
	update(function(){$('#loadblock').addClass('invisible')});
	timer = setInterval("update()",pollRate);
}
function startInstance(name){
	if(d.instances[name].fled||d.instances[name].completed){
		alert("You enter an instance twice.");
	} else {
		if(confirm("Do you want to enter "+name+"?"))
			event("enter",name);
		else
			event("discover",name);
	}
}
function event(type,to){
	clearInterval(timer);
	timer = null;
	$('#loadblock').removeClass('invisible');
	$.post("operations/update.php?cid="+campaign,"action="+type+"&to="+to,function(a){
															timer = setInterval("update()",pollRate);
															console.log(a);
															updateJSON(a);
														});
}
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
function refresh(){
	clearInterval(timer);
	timer = null;
	$('#loadblock').removeClass('invisible');
	update();
	timer = setInterval("update()",pollRate);
}
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
function exit(){
	clearInterval(timer);
	timer = null;
	location.hash = '';
	$(".main").addClass('invisible');
	$(".control").addClass('invisible');
	$("#newload").removeClass('invisible');
}