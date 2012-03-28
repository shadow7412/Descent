var isHero = null;
var campaign = null;
var timer = null
var pollRate = 5000;
function update(obj){
	$.getJSON("update.php?cid="+campaign,null,function(data,textStatus,xhr){
		updateJSON(data);
	}).error(function(){console.error("Updating failed.")});
}
function updateJSON(d){
	if(d.message != "") alert(d.message);
	var totalConquest = d.hero.conquest + d.overlord.conquest;
	var tier = parseInt(totalConquest/200);
	var percentThroughTier = ((totalConquest)-(tier*200))/2;
	$("#graphtotier").progressbar({value: percentThroughTier});
	console.log("Updated in "+d.loadtime+" with", d);
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
	if(p) $("#ploverworld").removeClass('invisible')
	else $("#oloverworld").removeClass('invisible')
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