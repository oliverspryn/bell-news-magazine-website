<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Check to see if the forum is being edited
	if (isset ($_GET['id'])) {
		$forum = $_GET['id'];
		$forumCheck = mysql_query("SELECT * FROM `collaboration` WHERE `id` = '{$forum}'", $connDBA);
		if ($forum = mysql_fetch_array($forumCheck)) {
			//Do nothing
		} else {
			header ("Location: index.php");
			exit;
		}
	}
	
//Ensure this is not editing another type than it is intended to handle
	if (isset($forum)) {
		if ($forum['type'] != "Forum") {
			header("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['content'])) {	
		if (!isset ($forum)) {
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
				$fromTimeArray = explode(":", $fromTime);
				$toTimeArray = explode(":", $toTime);
				
				if ($fromTime == $toTime) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_forum.php?message=inferior";
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_forum.php?message=inferior";
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {					
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_forum.php?message=inferior";
					}
				} else {
					$redirect = "Location: index.php?added=forum";
				}
			} else {
				$redirect = "Location: index.php?added=forum";
			}
			
			$positionGrabber = mysql_query ("SELECT * FROM `collaboration` ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
			
			$newForumQuery = "INSERT INTO collaboration (
								`id`, `position`, `visible`, `type`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`, `assignee`, `task`, `dueDate`, `priority`, `completed`, `directories`, `name`, `date`, `comment`
							) VALUES (
								NULL, '{$position}', 'on', 'Forum', '{$fromDate}', '{$fromTime}', '{$toDate}', '{$toTime}', '{$title}', '{$content}', '', '', '', '', '', '', '', '', ''
							)";
							
			mysql_query($newForumQuery, $connDBA);
			
			if ($redirect == "Location: manage_forum.php?message=inferior") {
				$redirectIDGrabber = mysql_query("SELECT * FROM `collaboration` WHERE `title` = '{$title}' AND `content` = '{$content}' AND `type` = 'Forum' LIMIT 1", $connDBA);
				$redirectID = mysql_fetch_array($redirectIDGrabber);
				$redirect .= "&id=" . $redirectID['id'];
			}
			
			header ($redirect);
			exit;
		} else {
			$forum = $_GET['id'];
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
					$redirect = "Location: manage_forum.php?message=inferior&id=" . $id;
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "Location: manage_forum.php?message=inferior&id=" . $id;
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						$fromDate = "";
						$fromTime = "";
						$toDate = "";
						$toTime = "";
						$redirect = "Location: manage_forum.php?message=inferior&id=" . $id;
					}
				} else {
					$redirect = "Location: index.php?updated=forum";
				}
			} else {
				$redirect = "Location: index.php?updated=forum";
			}
				
			$editForumQuery = "UPDATE collaboration SET `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}' WHERE `id` = '{$forum}'";
			
			mysql_query($editForumQuery, $connDBA);
			header ($redirect);
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($forum)) {
		$title = "Edit the " . stripslashes(htmlentities($forum['title'])) . " Forum";
	} else {
		$title =  "Create a New Forum";
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
<link rel="stylesheet" type="text/css" href="../../styles/common/datePicker.css" />
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
    <h2>
      <?php if (isset ($forum)) {echo "Edit the &quot;" . stripslashes($forum['title']) . "&quot; Forum";} else {echo "Create a New Forum";} ?>
    </h2>
<p>Use this page to <?php if (isset ($forum)) {echo "edit the content of \"<strong>" . stripslashes(htmlentities($forum['title'])) . "</strong>\"";} else {echo "create a new forum";} ?>.</p>
<?php
//Display error messages
	if (isset($_GET['message']) && $_GET['message'] == "inferior") {
		errorMessage("The start time can not be inferior to or the same as the end time");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <form action="manage_forum.php<?php 
		if (isset ($forum)) {
			echo "?id=" . $forum['id'];
		}
	?>" method="post" name="manageForum" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: </p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($forum)) {
					echo " value=\"" . stripslashes(htmlentities($forum['title'])) . "\"";
				}
			?> />
          </p>
        </blockquote>
<p>Availability:</p>
        <blockquote>
          <p>
            <input name="from" type="text" id="from" readonly="readonly"<?php
            	if (isset ($forum)) {
					echo " value=\"" . stripslashes(htmlentities($forum['fromDate'])) . "\"";
				}
				
				if (isset ($forum) && $forum['fromDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($forum)) {
					echo " disabled=\"disabled\"";
				}
			?> />
            <select name="fromTime" id="fromTime"<?php if (isset ($forum) && $forum['fromTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($forum)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($forum) && $forum['fromTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($forum) && $forum['fromTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($forum) && $forum['fromTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($forum) && $forum['fromTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($forum) && $forum['fromTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($forum) && $forum['fromTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($forum) && $forum['fromTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($forum) && $forum['fromTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($forum) && $forum['fromTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($forum) && $forum['fromTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($forum) && $forum['fromTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($forum) && $forum['fromTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($forum) && $forum['fromTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($forum) && $forum['fromTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($forum) && $forum['fromTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($forum) && $forum['fromTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($forum) && $forum['fromTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($forum) && $forum['fromTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($forum) && $forum['fromTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($forum) && $forum['fromTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($forum) && $forum['fromTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($forum) && $forum['fromTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($forum) && $forum['fromTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($forum) && $forum['fromTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($forum) && $forum['fromTime'] == "12:00") {echo " selected=\"selected\"";} elseif (!isset ($forum)) {echo " selected=\"selected\"";} elseif ($forum['fromTime'] == "") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($forum) && $forum['fromTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($forum) && $forum['fromTime'] == "13:00") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($forum) && $forum['fromTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($forum) && $forum['fromTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($forum) && $forum['fromTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($forum) && $forum['fromTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($forum) && $forum['fromTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($forum) && $forum['fromTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($forum) && $forum['fromTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($forum) && $forum['fromTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($forum) && $forum['fromTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($forum) && $forum['fromTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($forum) && $forum['fromTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($forum) && $forum['fromTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($forum) && $forum['fromTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($forum) && $forum['fromTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($forum) && $forum['fromTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($forum) && $forum['fromTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($forum) && $forum['fromTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($forum) && $forum['fromTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($forum) && $forum['fromTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($forum) && $forum['fromTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($forum) && $forum['fromTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          to 
          <input type="text" name="to" id="to" readonly="readonly"<?php
            	if (isset ($forum)) {
					echo " value=\"" . stripslashes(htmlentities($forum['toDate'])) . "\"";
				}
				
				if (isset ($forum) && $forum['toDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($forum)) {
					echo " disabled=\"disabled\"";
				}
			?> />
          <select name="toTime" id="toTime"<?php if (isset ($forum) && $forum['toTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($forum)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($forum) && $forum['toTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($forum) && $forum['toTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($forum) && $forum['toTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($forum) && $forum['toTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($forum) && $forum['toTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($forum) && $forum['toTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($forum) && $forum['toTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($forum) && $forum['toTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($forum) && $forum['toTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($forum) && $forum['toTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($forum) && $forum['toTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($forum) && $forum['toTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($forum) && $forum['toTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($forum) && $forum['toTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($forum) && $forum['toTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($forum) && $forum['toTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($forum) && $forum['toTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($forum) && $forum['toTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($forum) && $forum['toTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($forum) && $forum['toTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($forum) && $forum['toTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($forum) && $forum['toTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($forum) && $forum['toTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($forum) && $forum['toTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($forum) && $forum['toTime'] == "12:00") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($forum) && $forum['toTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($forum) && $forum['toTime'] == "13:00") {echo " selected=\"selected\"";} elseif (!isset ($forum)) {echo " selected=\"selected\"";} elseif ($forum['toTime'] == "") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($forum) && $forum['toTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($forum) && $forum['toTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($forum) && $forum['toTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($forum) && $forum['toTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($forum) && $forum['toTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($forum) && $forum['toTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($forum) && $forum['toTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($forum) && $forum['toTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($forum) && $forum['toTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($forum) && $forum['toTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($forum) && $forum['toTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($forum) && $forum['toTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($forum) && $forum['toTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($forum) && $forum['toTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($forum) && $forum['toTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($forum) && $forum['toTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($forum) && $forum['toTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($forum) && $forum['toTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($forum) && $forum['toTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($forum) && $forum['toTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($forum) && $forum['toTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          <label><input type="checkbox" name="toggleAvailability" id="toggleAvailability" onclick="flvFTFO1('manageForum','from,t','fromTime,t','to,t','toTime,t')"<?php
            	if (isset ($forum) && $forum['toDate'] != "") {
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
        <p>Content<span class="require">*</span>: </p>
        <blockquote>
        <p>
            <textarea name="content" id="content1" cols="45" rows="5" style="width:450px;" class="validate[required]" /><?php 
				if (isset ($forum)) {
					echo stripslashes($forum['content']);
				}
			?></textarea>
          </p>
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
