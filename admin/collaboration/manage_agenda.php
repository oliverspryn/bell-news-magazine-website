<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Check to see if the agenda is being edited
	if (isset ($_GET['id'])) {
		$agenda = $_GET['id'];
		$agendaCheck = mysql_query("SELECT * FROM `collaboration` WHERE `id` = '{$agenda}'", $connDBA);
		if ($agenda = mysql_fetch_array($agendaCheck)) {
			//Do nothing
		} else {
			header ("Location: index.php");
			exit;
		}
	}
	
//Ensure this is not editing another type than it is intended to handle
	if (isset($agenda)) {
		if ($agenda['type'] != "Agenda") {
			header("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['assignee']) && !empty($_POST['task']) && !empty($_POST['dueDate']) && !empty($_POST['priority'])) {	
		if (!isset ($agenda)) {
			$title = mysql_real_escape_string($_POST['title']);
			$fromDate = $_POST['from'];
			$fromTime = $_POST['fromTime'];
			$toDate = $_POST['to'];
			$toTime = $_POST['toTime'];
			$content = mysql_real_escape_string($_POST['content']);
			$assignee = mysql_real_escape_string(serialize($_POST['assignee']));
			$task = mysql_real_escape_string(serialize($_POST['task']));
			$dueDate = mysql_real_escape_string(serialize($_POST['dueDate']));
			$priority = mysql_real_escape_string(serialize($_POST['priority']));
		
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
					$redirect = "Location: manage_agenda.php?message=inferior";
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_agenda.php?message=inferior";
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {					
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_agenda.php?message=inferior";
					}
				} else {
					$redirect = "Location: index.php?added=agenda";
				}
			} else {
				$redirect = "Location: index.php?added=agenda";
			}
			
			$positionGrabber = mysql_query ("SELECT * FROM `collaboration` ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newAgendaQuery = "INSERT INTO collaboration (
								`id`, `position`, `visible`, `type`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`, `assignee`, `task`, `dueDate`, `priority`, `completed`, `directories`, `name`, `date`, `comment`
							) VALUES (
								NULL, '{$position}', 'on', 'Agenda', '{$fromDate}', '{$fromTime}', '{$toDate}', '{$toTime}', '{$title}', '{$content}', '{$assignee}', '{$task}', '{$dueDate}', '{$priority}', '', '', '', '', ''
							)";
							
			mysql_query($newAgendaQuery, $connDBA);
			
			if ($redirect == "Location: manage_agenda.php?message=inferior") {
				$redirectIDGrabber = mysql_query("SELECT * FROM `collaboration` WHERE `title` = '{$title}' AND `content` = '{$content}' AND `type` = 'Agenda' LIMIT 1", $connDBA);
				$redirectID = mysql_fetch_array($redirectIDGrabber);
				$redirect .= "&id=" . $redirectID['id'];
			}
			
			header ($redirect);
			exit;
		} else {
			$agenda = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$fromDate = $_POST['from'];
			$fromTime = $_POST['fromTime'];
			$toDate = $_POST['to'];
			$toTime = $_POST['toTime'];
			$content = mysql_real_escape_string($_POST['content']);
			$assignee = mysql_real_escape_string(serialize($_POST['assignee']));
			$task = mysql_real_escape_string(serialize($_POST['task']));
			$dueDate = mysql_real_escape_string(serialize($_POST['dueDate']));
			$priority = mysql_real_escape_string(serialize($_POST['priority']));
			
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
					$redirect = "Location: manage_agenda.php?message=inferior&id=" . $id;
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_agenda.php?message=inferior&id=" . $id;
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_agenda.php?message=inferior&id=" . $id;
					}
				} else {
					$redirect = "Location: index.php?updated=agenda";
				}
			} else {
				$redirect = "Location: index.php?updated=agenda";
			}
			
		//Delete old agenda statuses			
			if (!empty($_POST['removeData']) || is_numeric($_POST['removeData'])) {
				$oldAgenda = query("SELECT * FROM `collaboration` WHERE `id` = '{$agenda}'");
				$completed = unserialize($oldAgenda['completed']);
				$removeData = explode(",", $_POST['removeData']);
				sort($removeData);
				
				for($count = 0; $count <= sizeof($removeData) - 1; $count ++) {
					unset($completed[$removeData[$count]]);
				}
				
				$completed = mysql_real_escape_string(serialize(array_merge($completed)));
				$editAgendaQuery = "UPDATE collaboration SET `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}', `assignee` = '{$assignee}', `task` = '{$task}', `dueDate` = '{$dueDate}', `priority` = '{$priority}', `completed` = '{$completed}' WHERE `id` = '{$agenda}'";
			} else {
				$editAgendaQuery = "UPDATE collaboration SET `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}', `assignee` = '{$assignee}', `task` = '{$task}', `dueDate` = '{$dueDate}', `priority` = '{$priority}' WHERE `id` = '{$agenda}'";
			}
			
			mysql_query($editAgendaQuery, $connDBA);
			header ($redirect);
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($agenda)) {
		$title = "Edit the " . stripslashes(htmlentities($agenda['title'])) . " Agenda";
	} else {
		$title =  "Create a New Agenda";
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
      <?php if (isset ($agenda)) {echo "Edit the &quot;" . stripslashes($agenda['title']) . "&quot; Agenda";} else {echo "Create a New Agenda";} ?>
    </h2>
<p>Use this page to <?php if (isset ($agenda)) {echo "edit the content of \"<strong>" . stripslashes(htmlentities($agenda['title'])) . "</strong>\"";} else {echo "create a new agenda";} ?>.</p>
<?php
//Display error messages
	if (isset($_GET['message']) && $_GET['message'] == "inferior") {
		errorMessage("The start time can not be inferior to or the same as the end time");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <form action="manage_agenda.php<?php 
		if (isset ($agenda)) {
			echo "?id=" . $agenda['id'];
		}
	?>" method="post" name="manageAgenda" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: </p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($agenda)) {
					echo " value=\"" . stripslashes(htmlentities($agenda['title'])) . "\"";
				}
			?> />
          </p>
       </blockquote>
       <p>Comments: </p>
       <blockquote>
         <p><textarea name="content" id="content1" cols="45" rows="5" style="width:450px;" /><?php 
				if (isset ($agenda)) {
					echo stripslashes($agenda['content']);
				}
			?></textarea></p>
       </blockquote>
<p>Availability:</p>
        <blockquote>
          <p>
            <input name="from" type="text" id="from" readonly="readonly"<?php
            	if (isset ($agenda)) {
					echo " value=\"" . stripslashes(htmlentities($agenda['fromDate'])) . "\"";
				}
				
				if (isset ($agenda) && $agenda['fromDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($agenda)) {
					echo " disabled=\"disabled\"";
				}
			?> />
            <select name="fromTime" id="fromTime"<?php if (isset ($agenda) && $agenda['fromTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($agenda)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "12:00") {echo " selected=\"selected\"";} elseif (!isset ($agenda)) {echo " selected=\"selected\"";} elseif ($agenda['fromTime'] == "") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "13:00") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($agenda) && $agenda['fromTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($agenda) && $agenda['fromTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          to 
          <input type="text" name="to" id="to" readonly="readonly"<?php
            	if (isset ($agenda)) {
					echo " value=\"" . stripslashes(htmlentities($agenda['toDate'])) . "\"";
				}
				
				if (isset ($agenda) && $agenda['toDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($agenda)) {
					echo " disabled=\"disabled\"";
				}
			?> />
          <select name="toTime" id="toTime"<?php if (isset ($agenda) && $agenda['toTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($agenda)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($agenda) && $agenda['toTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($agenda) && $agenda['toTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($agenda) && $agenda['toTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($agenda) && $agenda['toTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($agenda) && $agenda['toTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($agenda) && $agenda['toTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($agenda) && $agenda['toTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($agenda) && $agenda['toTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($agenda) && $agenda['toTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($agenda) && $agenda['toTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($agenda) && $agenda['toTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($agenda) && $agenda['toTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($agenda) && $agenda['toTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($agenda) && $agenda['toTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($agenda) && $agenda['toTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($agenda) && $agenda['toTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($agenda) && $agenda['toTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($agenda) && $agenda['toTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($agenda) && $agenda['toTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($agenda) && $agenda['toTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($agenda) && $agenda['toTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($agenda) && $agenda['toTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($agenda) && $agenda['toTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($agenda) && $agenda['toTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($agenda) && $agenda['toTime'] == "12:00") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($agenda) && $agenda['toTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($agenda) && $agenda['toTime'] == "13:00") {echo " selected=\"selected\"";} elseif (!isset ($agenda)) {echo " selected=\"selected\"";} elseif ($agenda['toTime'] == "") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($agenda) && $agenda['toTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($agenda) && $agenda['toTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($agenda) && $agenda['toTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($agenda) && $agenda['toTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($agenda) && $agenda['toTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($agenda) && $agenda['toTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($agenda) && $agenda['toTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($agenda) && $agenda['toTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($agenda) && $agenda['toTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($agenda) && $agenda['toTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($agenda) && $agenda['toTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($agenda) && $agenda['toTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($agenda) && $agenda['toTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($agenda) && $agenda['toTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($agenda) && $agenda['toTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($agenda) && $agenda['toTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($agenda) && $agenda['toTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($agenda) && $agenda['toTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($agenda) && $agenda['toTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($agenda) && $agenda['toTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($agenda) && $agenda['toTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          <label><input type="checkbox" name="toggleAvailability" id="toggleAvailability" onclick="flvFTFO1('manageAgenda','from,t','fromTime,t','to,t','toTime,t')"<?php
            	if (isset ($agenda) && $agenda['toDate'] != "") {
					echo " checked=\"checked\"";
				}
			?> />Enable</label>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Content</div>
       <div class="stepContent">
        <blockquote>
        <table class="dataTable" id="agenda">
          <tr>
            	<th class="tableHeader">Task</th>
            	<th class="tableHeader" width="200">Assignee</th>
                <th class="tableHeader" width="200">Due Date</th>
            	<th class="tableHeader" width="100">Priority</th>
                <th class="tableHeader" width="50"></th>
          </tr>
            <?php
			//Display table rows according to what is going on			
				if (!isset ($agenda)) {
					$usersGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
					
					echo "<tr id=\"1\" align=\"center\">";
						echo "<td><input type=\"text\" name=\"task[]\" id=\"task1\" class=\"validate[required]\" autocomplete=\"off\" size=\"40\"></td>";
						echo "<td width=\"200\"><select name=\"assignee[]\" id=\"assignee1\" class=\"validate[required]\"><option value=\"\" selected=\"selected\">- Select -</option><option value=\"anyone\">Anyone</option>";
						
						while ($users = mysql_fetch_array($usersGrabber)) {
							echo "<option value=\"" . $users['id'] . "\">" . $users['firstName'] . " " . $users['lastName'] . "</option>";
						}
							
						echo "</select></td>";
						echo "<td width=\"200\"><input type=\"text\" name=\"dueDate[]\" id=\"dueDate1\" class=\"dueDate\" readonly=\"readonly\" /></td>";
						echo "<td width=\"100\"><select name=\"priority[]\" id=\"priority1\"><option value=\"1\">Low</option><option value=\"2\" selected=\"selected\">Normal</option><option value=\"3\">High</option></select></td>";
						echo "<td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('agenda', '1')\"></span>";
					echo "</tr>";
				} else {
					$values = sizeof(unserialize($agenda['priority']));
					$tasks = unserialize($agenda['task']);
					$assignees = unserialize($agenda['assignee']);
					$dueDates = unserialize($agenda['dueDate']);
					$priorities = unserialize($agenda['priority']);
					
					for ($count = 0; $count <= $values - 1; $count++) {
						$usersGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
						
						$rowID = $count + 1;
						
						echo "<tr id=\"" . $rowID . "\" align=\"center\">";
							echo "<td><input type=\"text\" name=\"task[]\" id=\"task" . $rowID . "\" class=\"validate[required]\" autocomplete=\"off\" size=\"40\" value=\"" . htmlentities(stripslashes($tasks[$count])) . "\"></td>";
							echo "<td width=\"200\"><select name=\"assignee[]\" id=\"assignee" . $rowID . "\" class=\"validate[required]\"><option value=\"\">- Select -</option><option value=\"anyone\"";
							
							if ($assignees[$count] == "anyone") {
								echo " selected=\"selected\"";
							}
							
							echo ">Anyone</option>";
							
							while ($users = mysql_fetch_array($usersGrabber)) {
								echo "<option value=\"" . $users['id'] . "\"";
								
								if ($assignees[$count] == $users['id']) {
									echo " selected=\"selected\"";
								}
								
								echo ">" . $users['firstName'] . " " . $users['lastName'] . "</option>";
							}
								
							echo "</select></td>";
							echo "<td width=\"200\"><input type=\"text\" name=\"dueDate[]\" id=\"dueDate" . $rowID . "\" class=\"dueDate\" value=\"" . htmlentities(stripslashes($dueDates[$count])) . "\" readonly=\"readonly\" /></td>";
							echo "<td width=\"100\"><select name=\"priority[]\" id=\"priority" . $rowID . "\"><option value=\"1\""; if ($priorities[$count] == "1") {echo " selected=\"selected\"";} echo ">Low</option><option value=\"2\""; if ($priorities[$count] == "2") {echo " selected=\"selected\"";} echo ">Normal</option><option value=\"3\""; if ($priorities[$count] == "3") {echo " selected=\"selected\"";} echo ">High</option></select></td>";
							echo "<td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('agenda', '" . $rowID . "', '" . $count . "');\"></span>";
						echo "</tr>";
													
						unset($usersGrabber);
						unset($users);
					}
					
					echo "<input type=\"hidden\" name=\"removeData\" id=\"removeData\"  value=\"\" />";
				}
			?>
        </table>
        <p><span class="smallAdd" onclick="addAgenda('agenda', '<input type=\'text\' name=\'task[]\' id=\'task', '\' class=\'validate[required]\' autocomplete=\'off\' size=\'40\'>', '<select name=\'assignee[]\' id=\'assignee', '\' class=\'validate[required]\'><option value=\'\' selected=\'selected\'>- Select -</option><option value=\'anyone\'>Anyone</option><?php
		//Grab all users
			$usersGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
			
			while ($users = mysql_fetch_array($usersGrabber)) {
				echo "<option value=\'" . $users['id'] . "\'>" . $users['firstName'] . " " . $users['lastName'] . "</option>";
			}
		?></select>', '<input type=\'text\' name=\'dueDate[]\' id=\'dueDate', '\' class=\'dueDate\' readonly=\'readonly\' />', '<select name=\'priority[]\' id=\'priority', '\'><option value=\'1\'>Low</option><option value=\'2\' selected=\'selected\'>Normal</option><option value=\'3\'>High</option></select>'); checkFields();">Add Another Task</span></p>
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
