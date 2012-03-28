var isHero = null;
var campaign = null;
var timer = null
var pollRate = 5000;
function update(obj){
	$.getJSON("operations/update.php","cid="+campaign,function(data,textStatus,xhr){
		updateJSON(data);
	}).error(function(){console.error("Updating failed.")});
}
//The big update function.
function updateJSON(d){
	if(d==null){
		console.error("Invalid JSON... maybe.");
		return;
	}
	if(d.message != null && d.message != "") alert(d.message);
	console.log("Updated in "+d.loadtime+" with", d);
	var totalConquest = d.hero.conquest + d.overlord.conquest;
	var tier = parseInt(totalConquest/200);
	var percentThroughTier = ((totalConquest)-(tier*200))/2;
	$("#graphtotier").progressbar({value: percentThroughTier});
	if(d.location="overworld")
		updateOverworld(d);
	else 
		updateInstance(d);	
}
function updateOverworld(d){
	//Make sure overworld is visible
	$("#"+(isHero?"pl":"ol")+"overworld").removeClass("invisible");
	$("#"+(isHero?"pl":"ol")+"instance").addClass("invisible");
}
function updateInstance(d){
	//make sure instance is visible
	$("#"+(isHero?"pl":"ol")+"overworld").addClass("invisible");
	$("#"+(isHero?"pl":"ol")+"instance").removeClass("invisible");
}
function createCampaign(){
	//Open all creation dialogs, in reverse order so correct one is on top
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
	$.post("operations/delete.php","id="+id,function(){console.log("success")});
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
	update();
	timer = setInterval("update()",pollRate);
}
function startInstance(town){
	if(isHero){
		$("#ploverworld").addClass('invisible');
		$("#plinstance").removeClass('invisible');
	} else {
		$("#oloverworld").addClass('invisible');
		$("#olinstance").removeClass('invisible');
	}
	
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