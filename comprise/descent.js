var isHero = null;
var campaign = null;
var timer = null
var pollRate = 5000;
var d = null; //Descent variable. Contains the full game state.
function update(obj){
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
	d = de;
	if(d.message != null && d.message != "") alert(d.message);
	console.log("Updated in "+d.loadtime+" with", d);
	var totalConquest = de.hero.conquest + de.overlord.conquest;
	var tier = parseInt(totalConquest/200);
	var percentThroughTier = ((totalConquest)-(tier*200))/2;
	$("#graphtotier").progressbar({value: percentThroughTier});
	if(d.heroes.location=="overworld")
		updateOverworld(de);
	else 
		updateInstance(de);	
}
function updateOverworld(d){
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
		instances+="</li>"
	}
	$(".instances").html(instances);
}
function updateInstance(d){
	//make sure instance is visible
	$("#"+(isHero?"pl":"ol")+"overworld").addClass("invisible");
	$("#"+(isHero?"pl":"ol")+"instance").removeClass("invisible");
	var stats = "<li>Gold: "+d.heroes.gold;
	   stats += "</li><li>Conquest: "+d.heroes.conquest;
	   stats += "</li>";
	$(".playerstats").html(stats);
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
	location.hash = (isHero?"p":"o")+campaign;
	update();
	timer = setInterval("update()",pollRate);
}
function startInstance(name){
	if(d.instances[name].fled||d.instances[name].completed){
		alert("You cannot enter a completed instance.");
	} else {
		event("enter",name);
	}
}
function event(type,to){
	clearInterval(timer);
	timer = null;
	alert('Done');
	$.post("operations/update.php?cid="+campaign,"action="+type+"&to="+to,function(a){
															timer = setInterval("update()",pollRate);
															console.log(a);
															updateJSON(a);
														});
}
function checkHash(){
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