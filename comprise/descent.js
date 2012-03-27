function update(obj){
	$.getJSON("update.php",null,function(data,textStatus,xhr){
		updateJSON(data);
	}).error(function(){console.error("Updating failed.")});
}
function updateJSON(json){
	var totalConquest = json.hero.conquest+json.overlord.conquest;
	var tier = parseInt(totalConquest/200);
	var percentThroughTier = ((totalConquest)-(tier*200))/200;
	console.log("Updated");
}