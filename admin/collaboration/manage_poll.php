<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Check to see if the poll is being edited
	if (isset ($_GET['id'])) {
		$poll = $_GET['id'];
		$pollCheck = mysql_query("SELECT * FROM `collaboration` WHERE `id` = '{$poll}'", $connDBA);
		if ($poll = mysql_fetch_array($pollCheck)) {
			//Do nothing
		} else {
			header ("Location: index.php");
			exit;
		}
	}
	
//Ensure this is not editing another type than it is intended to handle
	if (isset($poll)) {
		if ($poll['type'] != "Poll") {
			header("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['question'])) {	
		$questions = array();
		$count = 0;
		
		$title = mysql_real_escape_string($_POST['title']);
		$fromDate = $_POST['from'];
		$fromTime = $_POST['fromTime'];
		$toDate = $_POST['to'];
		$toTime = $_POST['toTime'];
		$content = mysql_real_escape_string($_POST['content']);
		$questions = mysql_real_escape_string(serialize($_POST['question']));
		
		if (!isset ($poll)) {
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
					$redirect = "Location: manage_poll.php?message=inferior";
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_poll.php?message=inferior";
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {					
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_poll.php?message=inferior";
					}
				} else {
					$redirect = "Location: index.php?added=poll";
				}
			} else {
				$redirect = "Location: index.php?added=poll";
			}
			
			$positionGrabber = mysql_query ("SELECT * FROM `collaboration` ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newPollQuery = "INSERT INTO collaboration (
								`id`, `position`, `visible`, `type`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`, `assignee`, `task`, `dueDate`, `priority`, `completed`, `directories`, `questions`, `responses`, `name`, `date`, `comment`
							) VALUES (
								NULL, '{$position}', 'on', 'Poll', '{$fromDate}', '{$fromTime}', '{$toDate}', '{$toTime}', '{$title}', '{$content}', '', '', '', '', '', '', '{$questions}', '', '', '', ''
							)";
							
			mysql_query($newPollQuery, $connDBA);
			
			if ($redirect == "Location: manage_poll.php?message=inferior") {
				$redirectID = mysql_insert_id();
				$redirect .= "&id=" . $redirectID;
			}
			
			header ($redirect);
			exit;
		} else {
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
					$redirect = "Location: manage_poll.php?message=inferior&id=" . $id;
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_poll.php?message=inferior&id=" . $id;
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_poll.php?message=inferior&id=" . $id;
					}
				} else {
					$redirect = "Location: index.php?updated=poll";
				}
			} else {
				$redirect = "Location: index.php?updated=poll";
			}
			
		//Delete old poll responses
			$oldQuestions = query("SELECT * FROM `collaboration` WHERE `id` = '{$poll['id']}'");
			$responses = unserialize($oldQuestions['responses']);
			
			if (!empty($_POST['removeData']) || is_numeric($_POST['removeData'])) {
				$removeData = explode(",", $_POST['removeData']);
				
				foreach($removeData as $response) {
					if (array_key_exists($response, $responses)) {
						unset($responses[$response]);
					}
				}
				
				$responses = mysql_real_escape_string(serialize(array_merge($responses)));
				$editPollQuery = "UPDATE collaboration SET `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}', `questions` = '{$questions}', `responses` = '{$responses}' WHERE `id` = '{$poll['id']}'";
			} else {
				$editPollQuery = "UPDATE collaboration SET `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}', `questions` = '{$questions}' WHERE `id` = '{$poll['id']}'";
			}
			
			mysql_query($editPollQuery, $connDBA);
			header ($redirect);
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($poll)) {
		$title = "Edit the " . stripslashes(htmlentities($poll['title'])) . " Poll";
	} else {
		$title =  "Create a New Poll";
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
      <?php if (isset ($poll)) {echo "Edit the &quot;" . stripslashes($poll['title']) . "&quot; Poll";} else {echo "Create a New Poll";} ?>
    </h2>
<p>Use this page to <?php if (isset ($poll)) {echo "edit the content of &quot;<strong>" . stripslashes(htmlentities($poll['title'])) . "</strong>&quot;";} else {echo "create a new poll";} ?>.</p>
<?php
//Display error messages
	if (isset($_GET['message']) && $_GET['message'] == "inferior") {
		errorMessage("The start time can not be inferior to or the same as the end time");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <form action="manage_poll.php<?php 
		if (isset ($poll)) {
			echo "?id=" . $poll['id'];
		}
	?>" method="post" name="managePoll" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: </p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($poll)) {
					echo " value=\"" . stripslashes(htmlentities($poll['title'])) . "\"";
				}
			?> />
          </p>
       </blockquote>
       <p>Question<span class="require">*</span>: </p>
       <blockquote>
         <p><textarea name="content" id="content1" cols="45" rows="5" style="width:450px;" class="validate[required]" /><?php 
				if (isset ($poll)) {
					echo stripslashes($poll['content']);
				}
			?></textarea> </p>
       </blockquote>
<p>Availability:</p>
        <blockquote>
          <p>
            <input name="from" type="text" id="from" readonly="readonly"<?php
            	if (isset ($poll)) {
					echo " value=\"" . stripslashes(htmlentities($poll['fromDate'])) . "\"";
				}
				
				if (isset ($poll) && $poll['fromDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($poll)) {
					echo " disabled=\"disabled\"";
				}
			?> />
            <select name="fromTime" id="fromTime"<?php if (isset ($poll) && $poll['fromTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($poll)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($poll) && $poll['fromTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($poll) && $poll['fromTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($poll) && $poll['fromTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($poll) && $poll['fromTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($poll) && $poll['fromTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($poll) && $poll['fromTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($poll) && $poll['fromTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($poll) && $poll['fromTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($poll) && $poll['fromTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($poll) && $poll['fromTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($poll) && $poll['fromTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($poll) && $poll['fromTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($poll) && $poll['fromTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($poll) && $poll['fromTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($poll) && $poll['fromTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($poll) && $poll['fromTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($poll) && $poll['fromTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($poll) && $poll['fromTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($poll) && $poll['fromTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($poll) && $poll['fromTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($poll) && $poll['fromTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($poll) && $poll['fromTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($poll) && $poll['fromTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($poll) && $poll['fromTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($poll) && $poll['fromTime'] == "12:00") {echo " selected=\"selected\"";} elseif (!isset ($poll)) {echo " selected=\"selected\"";} elseif ($poll['fromTime'] == "") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($poll) && $poll['fromTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($poll) && $poll['fromTime'] == "13:00") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($poll) && $poll['fromTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($poll) && $poll['fromTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($poll) && $poll['fromTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($poll) && $poll['fromTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($poll) && $poll['fromTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($poll) && $poll['fromTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($poll) && $poll['fromTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($poll) && $poll['fromTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($poll) && $poll['fromTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($poll) && $poll['fromTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($poll) && $poll['fromTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($poll) && $poll['fromTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($poll) && $poll['fromTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($poll) && $poll['fromTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($poll) && $poll['fromTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($poll) && $poll['fromTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($poll) && $poll['fromTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($poll) && $poll['fromTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($poll) && $poll['fromTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($poll) && $poll['fromTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($poll) && $poll['fromTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          to 
          <input type="text" name="to" id="to" readonly="readonly"<?php
            	if (isset ($poll)) {
					echo " value=\"" . stripslashes(htmlentities($poll['toDate'])) . "\"";
				}
				
				if (isset ($poll) && $poll['toDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($poll)) {
					echo " disabled=\"disabled\"";
				}
			?> />
          <select name="toTime" id="toTime"<?php if (isset ($poll) && $poll['toTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($poll)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($poll) && $poll['toTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($poll) && $poll['toTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($poll) && $poll['toTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($poll) && $poll['toTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($poll) && $poll['toTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($poll) && $poll['toTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($poll) && $poll['toTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($poll) && $poll['toTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($poll) && $poll['toTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($poll) && $poll['toTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($poll) && $poll['toTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($poll) && $poll['toTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($poll) && $poll['toTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($poll) && $poll['toTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($poll) && $poll['toTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($poll) && $poll['toTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($poll) && $poll['toTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($poll) && $poll['toTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($poll) && $poll['toTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($poll) && $poll['toTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($poll) && $poll['toTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($poll) && $poll['toTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($poll) && $poll['toTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($poll) && $poll['toTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($poll) && $poll['toTime'] == "12:00") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($poll) && $poll['toTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($poll) && $poll['toTime'] == "13:00") {echo " selected=\"selected\"";} elseif (!isset ($poll)) {echo " selected=\"selected\"";} elseif ($poll['toTime'] == "") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($poll) && $poll['toTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($poll) && $poll['toTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($poll) && $poll['toTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($poll) && $poll['toTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($poll) && $poll['toTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($poll) && $poll['toTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($poll) && $poll['toTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($poll) && $poll['toTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($poll) && $poll['toTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($poll) && $poll['toTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($poll) && $poll['toTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($poll) && $poll['toTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($poll) && $poll['toTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($poll) && $poll['toTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($poll) && $poll['toTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($poll) && $poll['toTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($poll) && $poll['toTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($poll) && $poll['toTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($poll) && $poll['toTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($poll) && $poll['toTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($poll) && $poll['toTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          <label><input type="checkbox" name="toggleAvailability" id="toggleAvailability" onclick="flvFTFO1('managePoll','from,t','fromTime,t','to,t','toTime,t')"<?php
            	if (isset ($poll) && $poll['toDate'] != "") {
					echo " checked=\"checked\"";
				}
			?> />Enable</label>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Choices</div>
       <div class="stepContent">
        <blockquote>
        <p>Create a series of choices in order to respond to the question above.</p>
        <blockquote>
        <div class="layoutControl">
          <div class="halfLeft">
            <table class="dataTable" id="questions">
              <tr>
                    <th class="tableHeader">Choices</th>
                    <th class="tableHeader" width="50"></th>
              </tr>
                <?php
                //Display table rows according to what is going on			
                    if (!isset ($poll)) {     
                        echo "<tr id=\"1\" align=\"center\"><td><input type=\"text\" name=\"question[]\" id=\"choice1\" class=\"validate[required]\" autocomplete=\"off\" size=\"50\"><input type=\"hidden\" name=\"key[]\" id=\"key1\" value=\"1\" /></td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('questions', '1');\"></span></td></tr>";
						echo "<tr id=\"2\" align=\"center\"><td><input type=\"text\" name=\"question[]\" id=\"choice2\" class=\"validate[required]\" autocomplete=\"off\" size=\"50\"><input type=\"hidden\" name=\"key[]\" id=\"key2\" value=\"2\" /></td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('questions', '2');\"></span></td></tr>";
                    } else {
                        $questions = unserialize($poll['questions']);
						$count = 0;
                        
						while (list($questionsKey, $questionsArray) = each($questions)) {    
							echo "<tr id=\"" . $count . "\" align=\"center\">";
                                echo "<td><input type=\"text\" name=\"question[]\" id=\"choice" . $count . "\" class=\"validate[required]\" autocomplete=\"off\" size=\"50\" value=\"" . htmlentities(stripslashes($questionsArray)) . "\"></td>";
                                echo "<td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('questions', '" . $count . "', '" . $count . "');\"></span>";
                            echo "</tr>";
							
							$count ++;
                        }
						
						echo "<input type=\"hidden\" name=\"removeData\" id=\"removeData\"  value=\"\" />";
                    }
                ?>
               </table>
              <p><span class="smallAdd" onclick="addQuestion('questions', '<input type=\'text\' name=\'question[]\' id=\'question', '\' class=\'validate[required]\' autocomplete=\'off\' size=\'50\'><input type=\'hidden\' name=\'key[]\' id=\'key', '\' value=\'', '\' />');">Add Another Choice</span></p>
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
