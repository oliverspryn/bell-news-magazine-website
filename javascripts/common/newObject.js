function addAgenda(tableID, cellOneStart, cellOneEnd, cellTwoStart, cellTwoEnd, cellThreeStart, cellThreeEnd, cellFourStart, cellFourEnd) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var tbl = document.getElementById(tableID);
	var newRow = tbl.insertRow(tbl.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[tbl.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = cellOneStart + currentID + cellOneEnd;
	
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = cellTwoStart + currentID + cellTwoEnd;
	
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = cellThreeStart + currentID + cellThreeEnd;
	
	var newCell4 = newRow.insertCell(3);
	newCell4.innerHTML = cellFourStart + currentID + cellFourEnd;
	
	var newCell5 = newRow.insertCell(4);
	newCell5.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('agenda', '" + currentID + "')\">";
}

function addCategory(tableID, startHTML, middle1HTML, middle2HTML, endHTML, type) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var tbl = document.getElementById(tableID);
	var newRow = tbl.insertRow(tbl.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[tbl.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 25;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	var directory = randomstring.toLowerCase();
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = startHTML + currentID + middle1HTML + directory + middle2HTML + currentID + endHTML;
	
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('files', '" + currentID + "')\">";
}

function addQuestion(tableID, startHTML, middle1HTML, middle2HTML, endHTML) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var tbl = document.getElementById(tableID);
	var newRow = tbl.insertRow(tbl.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[tbl.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = startHTML + currentID + middle1HTML + currentID +  middle2HTML + currentID + endHTML;
	
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('questions', '" + currentID + "')\">";
}

function deleteObject(tableID, rowID, input) {
	var tbl = document.getElementById(tableID);
	var row = document.getElementById(rowID);
	
	if (tableID != "questions") {
		var minRows = 2;
	} else {
		var minRows = 3;
	}
	
	if (tbl.rows.length > minRows) {
		if (tableID != "files") {
			row.parentNode.removeChild(row);
			
			if (input == parseFloat(input) || input == "0") {
				var field = document.getElementById('removeData');
				var values = field.value;
				
				if (values == "") {
					field.value = input;
				} else {
					field.value = field.value + "," + input;
				}
			}
		} else {
			var removeConfirm = confirm("Warning: Removing this category will remove all files within this category. Continue?");
			
			if (removeConfirm) {
				row.parentNode.removeChild(row);
			}
		}
	} else {
		var text = minRows - 1;
		
		alert("You must have at least " + text + " item(s) in this list");
	}
}