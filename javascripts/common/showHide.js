function toggleTypeDiv(field) {
	if (field && document.getElementById('contentAdvanced') && document.getElementById('contentMessage')) {
		switch (field) {
			case "Custom Content" : document.getElementById('contentAdvanced').className = "contentShow"; 
									document.getElementById('contentMessage').className = "contentHide";
									break;
			case "Login" : document.getElementById('contentAdvanced').className = "contentHide"; 
						   document.getElementById('contentMessage').className = "noResults contentShow";
						   break;
			case "Register" : document.getElementById('contentAdvanced').className = "contentShow"; 
							  document.getElementById('contentMessage').className = "contentHide";
							  break;
		}
	}
}

function reject() {
	var reject = document.getElementById('reject');
	
	if (reject.className == "contentHide") {
		reject.className = "contentShow";
	} else {
		reject.className = "contentHide";
	}
}

function securityFeatures(input) {
	var auto = document.getElementById('auto');
	var custom = document.getElementById('custom');
	var question = document.getElementById('question');
	var answer = document.getElementById('answer');
	
	if (input == "auto") {
		auto.className = "contentShow";
		custom.className = "contentHide";
		question.className = "";
		answer.className = "";
	} else {
		auto.className = "contentHide";
		custom.className = "contentShow";
		question.className = "validate[required]";
		answer.className = "validate[required]";
	}
}