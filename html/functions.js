$(document).ready(function() {
	$("#uForm").submit(function(event) {
		first();
		event.preventDefault();

	});
});

function first(){ 
  showUser();
  showProc();
}
function validateNetID() { 
  var netid = document.getElementById('netid').value;
  if (netid=="") { 
    document.getElementById("errorMessage").innerHTML = "Error: Must enter a NetID"; 
	return;
  }
  if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
	if (this.readyState==4 && this.status==200) {
      var response = this.responseText;
      var temp = document.createElement("div");
	  temp.body = response;
	  alert(temp.body);
	  var sanitized = temp.textContent || temp.innerText;
	  alert(sanitized);
	}
  }
  xmlhttp.open("GET","validate_netid.php?netid="+netid,true);
  xmlhttp.send();
}
function showProc() {
  var netid = document.getElementById('netid').value;
  if (netid=="") {
	document.getElementById("userProcTable").innerHTML="";
	return;
  } 
  if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
	if (this.readyState==4 && this.status==200) {
	  document.getElementById("userProcTable").innerHTML=this.responseText;
	}
  }
  xmlhttp.open("GET","get_user_processes.php?netid="+netid,true);
  xmlhttp.send();
}
function showUser() {
  var netid = document.getElementById('netid').value;
  if (netid=="") {
	document.getElementById("userInfoTable").innerHTML="";
	return;
  } 
  if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
	if (this.readyState==4 && this.status==200) {
	  document.getElementById("userInfoTable").innerHTML=this.responseText;
	}
  }
  xmlhttp.open("GET","get_user_info.php?netid="+netid,true);
  xmlhttp.send();
}
