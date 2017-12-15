
function changeLeaderboard(str) {
	if(window.XMLHttpRequest){
		xmlhttp = new XMLHttpRequest();
	} 
	else{
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if(this.readyState == 4 && this.status == 200){
			document.getElementById("leaderboardDiv").innerHTML = this.responseText;
			console.log(this.responseText);	
		}
	};
	xmlhttp.open("GET","leaderboard.php?t="+str,true);
	xmlhttp.send();
}
