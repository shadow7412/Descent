var isHero = null;
var campaign = null;
var timer = null
function update(obj){
	$.getJSON("update.php?cid="+campaign,null,function(data,textStatus,xhr){
		updateJSON(data);
	}).error(function(){console.error("Updating failed.")});
}
function updateJSON(d){
	if(d.message!==null) alert(d.message);
	var totalConquest = d.hero.conquest + d.overlord.conquest;
	var tier = parseInt(totalConquest/200);
	var percentThroughTier = ((totalConquest)-(tier*200))/200;
	console.log("Updated with", d);
}
function selectCampaign(id){
	campaign = id;
	$("#newload").addClass("invisible");
	$("#whichside").removeClass("invisible");
}
function setPlayer(p){ //true = heroes
	isHero = p;
	$("#whichside").addClass('invisible');
	if(p){
		$("#ploverworld").removeClass('invisible')
	} else {
		$("#oloverworld").removeClass('invisible')
	}
	update();
	timer = setInterval("update()",5000);
}
function stopUpdating(){
	clearInterval(timer);
	timer = null;
}