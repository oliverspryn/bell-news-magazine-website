<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("sendEmail") == "true") {
		if ($_SESSION['MM_UserGroup'] == "User") {
			header("Location: send_email.php");
			exit;
		}
		
		loginCheck("Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Check to see if items exist
	$itemCheck = mysql_query("SELECT * FROM collaboration WHERE `position` = 1", $connDBA);
	if (mysql_fetch_array($itemCheck)) {
		$itemGrabber = mysql_query("SELECT * FROM collaboration ORDER BY position ASC", $connDBA);
	} else {
		$itemGrabber = 0;
	}

//Reorder items	
	if (isset ($_GET['action']) && $_GET['action'] == "modifySettings" && isset($_GET['id']) && isset($_GET['position']) && isset($_GET['currentPosition'])) {
	//Grab all necessary data	
	  //Grab the id of the moving item
	  $id = $_GET['id'];
	  //Grab the new position of the item
	  $newPosition = $_GET['position'];
	  //Grab the old position of the item
	  $currentPosition = $_GET['currentPosition'];
		  
	  //Do not process if item does not exist
	  //Get item name by URL variable
	  $getItemID = $_GET['position'];
  
	  $itemCheckGrabber = mysql_query("SELECT * FROM items WHERE position = {$getItemID}", $connDBA);
	  $itemCheckArray = mysql_fetch_array($itemCheckGrabber);
	  $itemCheckResult = $itemCheckArray['position'];
		   if (isset ($itemCheckResult)) {
			   $itemCheck = 1;
		   } else {
			  $itemCheck = 0;
		   }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE collaboration SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE collaboration SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE collaboration SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE collaboration SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
		}
	}

//Set item avaliability
	if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
		$id = $_POST['id'];
		
		if (!$_POST['option']) {
			$option = "";
		} else {
			$option = $_POST['option'];
		}
		
		$setAvaliability = "UPDATE collaboration SET `visible` = '{$option}' WHERE id = '{$id}'";
		mysql_query($setAvaliability, $connDBA);
		
		header ("Location: index.php");
		exit;
	}
	
//Delete an item
	if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['item']) && isset($_GET['id'])) {
		//Do not process if item does not exist
		//Get item name by URL variable
		$getItemID = $_GET['item'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM collaboration WHERE position = {$getItemID}", $connDBA);
		$itemCheckArray = mysql_fetch_array($itemCheckGrabber);
		$itemCheckResult = $itemCheckArray['position'];
			 if (isset ($itemCheckResult)) {
				 $itemCheck = 1;
				 $directories = unserialize($itemCheckArray['directories']);
				 
				 while (list($categoryKey, $categoryArray) = each($directories)) {
				 	deleteAll("../files/" . $categoryKey);
				 }
			 } else {
				$itemCheck = 0;
			 }
	 
		if (!isset ($_GET['id']) || $_GET['id'] == 0 || $itemCheck == 0) {
			header ("Location: index.php");
			exit;
		} else {
			$deleteItem = $_GET['id'];
			$itemLift = $_GET['item'];
			
			$itemPositionGrabber = mysql_query("SELECT * FROM collaboration WHERE position = {$itemLift}", $connDBA);
			$itemPositionFetch = mysql_fetch_array($itemPositionGrabber);
			$itemPosition = $itemPositionFetch['position'];
			
			$otherItemsUpdateQuery = "UPDATE collaboration SET position = position-1 WHERE position > '{$itemPosition}'";
			$deleteItemQueryResult = mysql_query($otherItemsUpdateQuery, $connDBA);
			
			$deleteItemQuery = "DELETE FROM collaboration WHERE id = {$deleteItem}";
			$deleteItemQueryResult = mysql_query($deleteItemQuery, $connDBA);
			
			header ("Location: index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Collaboration"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
</head>

<body>
<?php tooltip(); ?>
<?php topPage(); ?>
<h2>Collaboration</h2>
<p>Communication can be established to registered users via announcements and mass emails.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem announcementLink" href="manage_announcement.php">Create Announcement</a><a class="toolBarItem agenda" href="manage_agenda.php">Create Agenda</a><a class="toolBarItem fileShare" href="manage_files.php">Create File Share</a><a class="toolBarItem statistics" href="manage_poll.php">Create a Poll</a><a class="toolBarItem feedback" href="manage_forum.php">Create a Forum</a><a class="toolBarItem email" href="send_email.php">Send Mass Email</a></div>
<?php 
	if (isset ($_GET['added']) && $_GET['added'] == "announcement") {successMessage("The announcement was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "announcement") {successMessage("The announcement was successfully updated");}
	if (isset ($_GET['added']) && $_GET['added'] == "agenda") {successMessage("The agenda was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "agenda") {successMessage("The agenda was successfully updated");}
	if (isset ($_GET['added']) && $_GET['added'] == "files") {successMessage("The file share was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "files") {successMessage("The file share was successfully updated");}
	if (isset ($_GET['added']) && $_GET['added'] == "poll") {successMessage("The poll was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "poll") {successMessage("The poll was successfully updated");}
	if (isset ($_GET['added']) && $_GET['added'] == "forum") {successMessage("The forum was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "forum") {successMessage("The forum was successfully updated");}
	if (isset ($_GET['email']) && $_GET['email'] == "success") {successMessage("The email was successfully sent");}
	if (!isset ($_GET['updated']) && !isset ($_GET['added']) && !isset ($_GET['email'])) {echo "<br />";}
?>
<?php
//Table header, only displayed if items exist.
	if ($itemGrabber !== 0) {
	//Provide some data for the time tracker
		$time = getdate();
		
		if (0 < $time['minutes'] && $time['minutes'] < 9) {
			$minutes = "0" . $time['minutes'];
		} else {
			$minutes = $time['minutes'];
		}
		
		$currentTime = $time['hours'] . ":" . $minutes;
		$currentDate = strtotime($time['mon'] . "/" . $time['mday'] . "/" . $time['year'] . " " . $currentTime);
		
	echo "<table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\" width=\"200\">Type</th><th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
	//Loop through each item
		while($itemData = mysql_fetch_array($itemGrabber)) {
			echo "<tr";
		//Alternate the color of each row
			if ($itemData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
			if ($itemData['fromDate'] != "") {
				$from = strtotime($itemData['fromDate'] . " " . $itemData['fromTime']);
				$to = strtotime($itemData['toDate'] . " " . $itemData['toTime']);
				$fromTimeArray = explode(":", $itemData['fromTime']);
				$toTimeArray = explode(":", $itemData['toTime']);
				
				if ($fromTimeArray['0'] == "00") {
					$showTime = "12:" . $fromTimeArray['1'] . " am";
				} elseif (01 <= $fromTimeArray['0'] &&  $fromTimeArray['0'] <= 11) {
					$showTime = $fromTimeArray['0'] . ":" . $fromTimeArray['1'] . " am";
				} elseif ($fromTimeArray['0'] == "12") {
					$showTime = "12:" . $toTimeArray['1'] . " pm";
				} else {
					$showTime = $fromTimeArray['0'] - 12 . ":" . $fromTimeArray['1'] . " pm";
				}
				
				if ($toTimeArray['0'] == "00") {
					$expiredTime = "12:" . $toTimeArray['1'] . " am";
				} elseif (01 <= $toTimeArray['0'] &&  $toTimeArray['0'] <= 11) {
					$expiredTime = $toTimeArray['0'] . ":" . $toTimeArray['1'] . " am";
				} elseif ($toTimeArray['0'] == "12") {
					$expiredTime = "12:" . $toTimeArray['1'] . " pm";
				} else {
					$expiredTime = $toTimeArray['0'] - 12 . ":" . $toTimeArray['1'] . " pm";
				}
							
				if ($from > $currentDate) {
					echo "<td width=\"25\"><span onmouseover=\"Tip('This item will display on <strong>" . $itemData['fromDate'] . " at " . $showTime . "</strong>')\" onmouseout=\"UnTip()\" class=\"action upcoming\"></span></td>";
				} elseif ($to <= $currentDate) {
					echo "<td width=\"25\"><span onmouseover=\"Tip('This item has expired.<br />It was last visible on <strong>" . $itemData['toDate'] . " at " . $expiredTime . "</strong>.')\" onmouseout=\"UnTip()\" class=\"action expired\"></span></td>";
				} else {
					echo "<td width=\"25\"><span onmouseover=\"Tip('This item is currently being displayed')\" onmouseout=\"UnTip()\" class=\"action current\"></span></td>";
				}
			} else {
				echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $itemData['id'] . "\" class=\"visible"; if ($itemData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $itemData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $itemData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($itemData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
			}
			
			echo "<td width=\"75\"><form name=\"items\" action=\"index.php\"><input type=\"hidden\" name=\"id\" value=\"" . $itemData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $itemData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
			
			$itemCount = mysql_num_rows($itemGrabber);
			for ($count=1; $count <= $itemCount; $count++) {
				echo "<option value=\"{$count}\"";
				if ($itemData ['position'] == $count) {
					echo " selected=\"selected\"";
				}
				echo ">" . $count . "</option>";
			}
			
			echo "</select></form></td><td width=\"200\">" . $itemData['type'] . "</td>";
			echo "<td width=\"200\">" . commentTrim(30, stripslashes($itemData['title'])) . "</td>";
			echo "<td>" . commentTrim(60, stripslashes($itemData['content'])) . "</td>";
			echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_";
			
			switch ($itemData['type']) {
				case "Agenda" : echo "agenda"; break;
				case "Announcement" : echo "announcement"; break;
				case "File Share" : echo "files"; break;
				case "Poll" : echo "poll"; break;
				case "Forum" : echo "forum"; break;
			}
			
			echo ".php?id=" . $itemData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($itemData['title']) . "</strong> item')\" onmouseout=\"UnTip()\"></a></td>"; 
			
			if ($itemData['type'] == "File Share") {
				echo "<td width=\"50\"><a class=\"action delete\" href=\"index.php?action=delete&item=" . $itemData['position'] . "&id=" . $itemData['id'] . "\" onclick=\"return confirm ('This action will delete all files within this file share item. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($itemData['title']) . "</strong> item')\" onmouseout=\"UnTip()\"></a></td>";
			} else {
				echo "<td width=\"50\"><a class=\"action delete\" href=\"index.php?action=delete&item=" . $itemData['position'] . "&id=" . $itemData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($itemData['title']) . "</strong> item')\" onmouseout=\"UnTip()\"></a></td>";
			}
		}
		
		echo "</tr></tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no items. New items can be created by selecting an item from the tool bar above.</div>";
	 } 
?>
<?php footer(); ?>
</body>
</html>
