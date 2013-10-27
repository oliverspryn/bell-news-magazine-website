<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Check to see if the file share is being edited
	if (isset ($_GET['id'])) {
		$files = $_GET['id'];
		$filesCheck = mysql_query("SELECT * FROM `collaboration` WHERE `id` = '{$files}'", $connDBA);
		if ($files = mysql_fetch_array($filesCheck)) {
			//Do nothing
		} else {
			header ("Location: index.php");
			exit;
		}
	}
	
//Ensure this is not editing another type than it is intended to handle
	if (isset($files)) {
		if ($files['type'] != "File Share") {
			header("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['category'])) {	
		if (!isset ($files)) {
			$title = mysql_real_escape_string($_POST['title']);
			$fromDate = $_POST['from'];
			$fromTime = $_POST['fromTime'];
			$toDate = $_POST['to'];
			$toTime = $_POST['toTime'];
			$content = mysql_real_escape_string($_POST['content']);
			$category = mysql_real_escape_string(serialize($_POST['category']));
		
		//Ensure times are not inferior, the dates are the same, and all dates are set
			if (empty($fromDate) || empty($toDate) || empty($_POST['toggleAvailability'])) {
				$fromDate = "";
				$fromTime = "";
				$toDate = "";
				$toTime = "";
			}
			
			if ($fromDate == $toDate && !empty($fromDate) && !empty($toDate)) {
				$fromTimeArray = explode(":", $fromTime);
				$toTimeArray = explode(":", $toTime);
				
				if ($fromTime == $toTime) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_files.php?message=inferior";
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_files.php?message=inferior";
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {					
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_files.php?message=inferior";
					}
				} else {
					$redirect = "Location: index.php?added=files";
				}
			} else {
				$redirect = "Location: index.php?added=files";
			}
			
		//Create the directories
			$categoryPrep = array();
			
			foreach($_POST['category'] as $directoryPrep) {
				$directory = randomValue(25, "alphanum");
				mkdir("../files/" . $directory, 0777);
				
				$categoryPrep[$directory] = $directoryPrep;
			}
			
			$category = mysql_real_escape_string(serialize($categoryPrep));
			
			$positionGrabber = mysql_query ("SELECT * FROM `collaboration` ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newFilesQuery = "INSERT INTO collaboration (
								`id`, `position`, `visible`, `type`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`, `assignee`, `task`, `dueDate`, `priority`, `completed`, `directories`, `name`, `date`, `comment`
							) VALUES (
								NULL, '{$position}', 'on', 'File Share', '{$fromDate}', '{$fromTime}', '{$toDate}', '{$toTime}', '{$title}', '{$content}', '', '', '', '', '', '{$category}', '', '', ''
							)";
			
			mysql_query($newFilesQuery, $connDBA);
			
			if ($redirect == "Location: manage_files.php?message=inferior") {
				$redirectIDGrabber = mysql_query("SELECT * FROM `collaboration` WHERE `title` = '{$title}' AND `content` = '{$content}' AND `type` = 'File Share' LIMIT 1", $connDBA);
				$redirectID = mysql_fetch_array($redirectIDGrabber);
				$redirect .= "&id=" . $redirectID['id'];
			}
			
			header ($redirect);
			exit;
		} else {
			$files = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$fromDate = $_POST['from'];
			$fromTime = $_POST['fromTime'];
			$toDate = $_POST['to'];
			$toTime = $_POST['toTime'];
			$content = mysql_real_escape_string($_POST['content']);
			
		//Ensure times are not inferior, the dates are the same, and all dates are set
			if (empty($fromDate) || empty($toDate) || empty($_POST['toggleAvailability'])) {
				$fromDate = "";
				$fromTime = "";
				$toDate = "";
				$toTime = "";
			}
			
			if ($fromDate == $toDate && !empty($fromDate) && !empty($toDate)) {
				$id = $_GET['id'];
				$type = $_GET['type'];
				$fromTimeArray = explode(":", $fromTime);
				$toTimeArray = explode(":", $toTime);
				
				if ($fromTime == $toTime) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_files.php?message=inferior&id=" . $id;
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_files.php?message=inferior&id=" . $id;
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_files.php?message=inferior&id=" . $id;
					}
				} else {
					$redirect = "Location: index.php?updated=files";
				}
			} else {
				$redirect = "Location: index.php?updated=files";
			}
			
		//Update the directories
			$id = $_GET['id'];
			$directoryInfoGrabber = mysql_query("SELECT * FROM `collaboration` WHERE `id` = '{$id}'", $connDBA);
			$directoryInfo = mysql_fetch_array($directoryInfoGrabber);
			$directories = unserialize($directoryInfo['directories']);
			
			$newDirectories = array();
			$categoriesCheck = $_POST['category'];
			$directoriesCheck = $_POST['directory'];
			$values = sizeof($_POST['category']) - 1;
			
			for ($count = 0; $count <= $values; $count++) {
				$newDirectories[$directoriesCheck[$count]] = $categoriesCheck[$count];
			}
			
			while (list($directoriesKey, $directoriesArray) = each($directories)) {
				if (!array_key_exists($directoriesKey, $newDirectories)) { 
					deleteAll("../files/" . $directoriesKey);
				}
			}
			
			while (list($directoriesKey, $directoriesArray) = each($newDirectories)) {
				if (!array_key_exists($directoriesKey, $directories)) {
					mkdir("../files/" . $directoriesKey, 0777);
				}
			}
			
			$category = serialize($newDirectories);
				
			$editFilesQuery = "UPDATE collaboration SET `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}', `directories` = '{$category}' WHERE `id` = '{$files}'";
			
			mysql_query($editFilesQuery, $connDBA);
			header ($redirect);
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($files)) {
		$title = "Edit the " . stripslashes(htmlentities($files['title'])) . " File Share";
	} else {
		$title =  "Create a New File Share";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/datePicker.js" type="text/javascript"></script>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/enableDisable.js" type="text/javascript"></script>
<script src="../../javascripts/common/newObject.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../../styles/common/datePicker.css" />
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
    <h2>
      <?php if (isset ($files)) {echo "Edit the &quot;" . stripslashes($files['title']) . "&quot; File Share";} else {echo "Create a New File Share";} ?>
    </h2>
<p>Use this page to <?php if (isset ($files)) {echo "edit the content of \"<strong>" . stripslashes(htmlentities($files['title'])) . "</strong>\"";} else {echo "create a new file share";} ?>.</p>
<?php
//Display error messages
	if (isset($_GET['message']) && $_GET['message'] == "inferior") {
		errorMessage("The start time can not be inferior to or the same as the end time");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <form action="manage_files.php<?php 
		if (isset ($files)) {
			echo "?id=" . $files['id'];
		}
	?>" method="post" name="manageFiles" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: </p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($files)) {
					echo " value=\"" . stripslashes(htmlentities($files['title'])) . "\"";
				}
			?> />
          </p>
       </blockquote>
       <p>Comments: </p>
       <blockquote>
         <p><textarea name="content" id="content1" cols="45" rows="5" style="width:450px;" /><?php 
				if (isset ($files)) {
					echo stripslashes($files['content']);
				}
			?></textarea> </p>
       </blockquote>
<p>Availability:</p>
        <blockquote>
          <p>
            <input name="from" type="text" id="from" readonly="readonly"<?php
            	if (isset ($files)) {
					echo " value=\"" . stripslashes(htmlentities($files['fromDate'])) . "\"";
				}
				
				if (isset ($files) && $files['fromDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($files)) {
					echo " disabled=\"disabled\"";
				}
			?> />
            <select name="fromTime" id="fromTime"<?php if (isset ($files) && $files['fromTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($files)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($files) && $files['fromTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($files) && $files['fromTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($files) && $files['fromTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($files) && $files['fromTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($files) && $files['fromTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($files) && $files['fromTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($files) && $files['fromTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($files) && $files['fromTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($files) && $files['fromTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($files) && $files['fromTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($files) && $files['fromTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($files) && $files['fromTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($files) && $files['fromTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($files) && $files['fromTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($files) && $files['fromTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($files) && $files['fromTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($files) && $files['fromTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($files) && $files['fromTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($files) && $files['fromTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($files) && $files['fromTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($files) && $files['fromTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($files) && $files['fromTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($files) && $files['fromTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($files) && $files['fromTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($files) && $files['fromTime'] == "12:00") {echo " selected=\"selected\"";} elseif (!isset ($files)) {echo " selected=\"selected\"";} elseif ($files['fromTime'] == "") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($files) && $files['fromTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($files) && $files['fromTime'] == "13:00") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($files) && $files['fromTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($files) && $files['fromTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($files) && $files['fromTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($files) && $files['fromTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($files) && $files['fromTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($files) && $files['fromTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($files) && $files['fromTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($files) && $files['fromTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($files) && $files['fromTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($files) && $files['fromTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($files) && $files['fromTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($files) && $files['fromTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($files) && $files['fromTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($files) && $files['fromTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($files) && $files['fromTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($files) && $files['fromTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($files) && $files['fromTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($files) && $files['fromTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($files) && $files['fromTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($files) && $files['fromTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($files) && $files['fromTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          to 
          <input type="text" name="to" id="to" readonly="readonly"<?php
            	if (isset ($files)) {
					echo " value=\"" . stripslashes(htmlentities($files['toDate'])) . "\"";
				}
				
				if (isset ($files) && $files['toDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($files)) {
					echo " disabled=\"disabled\"";
				}
			?> />
          <select name="toTime" id="toTime"<?php if (isset ($files) && $files['toTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($files)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($files) && $files['toTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($files) && $files['toTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($files) && $files['toTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($files) && $files['toTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($files) && $files['toTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($files) && $files['toTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($files) && $files['toTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($files) && $files['toTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($files) && $files['toTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($files) && $files['toTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($files) && $files['toTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($files) && $files['toTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($files) && $files['toTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($files) && $files['toTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($files) && $files['toTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($files) && $files['toTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($files) && $files['toTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($files) && $files['toTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($files) && $files['toTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($files) && $files['toTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($files) && $files['toTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($files) && $files['toTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($files) && $files['toTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($files) && $files['toTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($files) && $files['toTime'] == "12:00") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($files) && $files['toTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($files) && $files['toTime'] == "13:00") {echo " selected=\"selected\"";} elseif (!isset ($files)) {echo " selected=\"selected\"";} elseif ($files['toTime'] == "") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($files) && $files['toTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($files) && $files['toTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($files) && $files['toTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($files) && $files['toTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($files) && $files['toTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($files) && $files['toTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($files) && $files['toTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($files) && $files['toTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($files) && $files['toTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($files) && $files['toTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($files) && $files['toTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($files) && $files['toTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($files) && $files['toTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($files) && $files['toTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($files) && $files['toTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($files) && $files['toTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($files) && $files['toTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($files) && $files['toTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($files) && $files['toTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($files) && $files['toTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($files) && $files['toTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          <label><input type="checkbox" name="toggleAvailability" id="toggleAvailability" onclick="flvFTFO1('manageFiles','from,t','fromTime,t','to,t','toTime,t')"<?php
            	if (isset ($files) && $files['toDate'] != "") {
					echo " checked=\"checked\"";
				}
			?> />Enable</label>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Categories</div>
       <div class="stepContent">
        <blockquote>
        <p>Categories are different folders that registered users may upload files, usually for files which fall in a similar category.
        <?php
		//Display an alert message if the user is editing this content
			if (isset($files)) {
				echo "<br /><strong>Note:</strong> Deleting a category will remove all files within that category.";
			}
		?>
        </p>
        <blockquote>
        <div class="layoutControl">
          <div class="halfLeft">
            <table class="dataTable" id="files">
              <tr>
                    <th class="tableHeader">Category</th>
                    <th class="tableHeader" width="50"></th>
              </tr>
                <?php
                //Display table rows according to what is going on			
                    if (!isset ($files)) {                        
                        echo "<tr id=\"1\" align=\"center\">";
                            echo "<td><input type=\"hidden\" name=\"directory[]\" value=\"" . randomValue(25, "alphanum") . "\" /><input type=\"text\" name=\"category[]\" id=\"category1\" class=\"validate[required]\" autocomplete=\"off\" size=\"50\"></td>";
                            echo "<td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('files', '1')\"></span>";
                        echo "</tr>";
                    } else {
                        $values = sizeof(unserialize($files['directories']));
                        $categories = unserialize($files['directories']);
						$count = 1;
                        
						while (list($categoriesKey, $categoriesArray) = each($categories)) {
							$rowID = $count++;
							                          
							echo "<tr id=\"" . $rowID . "\" align=\"center\">";
                                echo "<td><input type=\"hidden\" name=\"directory[]\" value=\"" . $categoriesKey . "\" /><input type=\"text\" name=\"category[]\" id=\"category" . $rowID . "\" class=\"validate[required]\" autocomplete=\"off\" size=\"50\" value=\"" . htmlentities(stripslashes($categoriesArray)) . "\"></td>";
                                echo "<td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('files', '" . $rowID . "')\"></span>";
                            echo "</tr>";
                        }
                    }
                ?>
               </table>
              <p><span class="smallAdd" onclick="addCategory('files', '<input type=\'hidden\' name=\'directory[]\' id=\'directory', '\' value=\'', '\' /><input type=\'text\' name=\'category[]\' id=\'category', '\' class=\'validate[required,custom[noSpecialCharacters]]\' autocomplete=\'off\' size=\'50\'>');">Add Another Category</span></p>
            </div>
          </div>
        </blockquote>
        </blockquote>
      </div>
      <div class="catDivider three">Finish</div>
      <div class="stepContent">
	  <blockquote>
      	<p>
          <?php submit("submit", "Submit"); ?>
			<input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
        </p>
          <?php formErrors(); ?>
      </blockquote>
      </div>
    </form>
<?php footer(); ?>
</body>
</html>
