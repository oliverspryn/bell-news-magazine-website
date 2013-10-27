<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("viewStaffPage") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Check to see if any pages exist
	$pagesExistGrabber = mysql_query("SELECT * FROM staffpages WHERE position = '1'", $connDBA);
	$pagesExistArray = mysql_fetch_array($pagesExistGrabber);
	$pagesExistResult = $pagesExistArray['position'];
	
	if ($pagesExistGrabber) {
		$pagesExist = 1;
	} else {
		$pagesExist = 0;
	}
	
//If no page URL variable is defined, then choose the home page
	if (!isset ($_GET['page']) || $_GET['page'] == "") {
	//Grab the page data
		$pageInfoPrep = mysql_fetch_array(mysql_query("SELECT * FROM staffpages WHERE position = '1' AND `published` != '0'", $connDBA));
		$pageInfo = unserialize($pageInfoPrep['content' . $pageInfoPrep['display']]);
		
	//Hide the admin menu if an incorrect page displays		
		if ($pagesExist == "1") {
			$privilegesCheckGrabber = mysql_query("SELECT * FROM privileges WHERE id = '1'", $connDBA);
			$privilegesCheck = mysql_fetch_array($privilegesCheckGrabber);
			
			if ($pageInfoPrep['published'] == "0") {
				$pageCheck = 0;
			} else {
				$pageCheck = 1;
			}
		} else {
			$pageCheck = 0;
		}
	} else {		
	//Grab the page data
		$getPageID = $_GET['page'];
		$pageInfoPrep = mysql_fetch_array(mysql_query("SELECT * FROM staffpages WHERE id = {$getPageID}", $connDBA));
		$pageInfo = unserialize($pageInfoPrep['content' . $pageInfoPrep['display']]);
		
	//Hide the admin menu if an incorrect page displays
		$pageCheckGrabber = mysql_query("SELECT * FROM staffpages WHERE id = {$getPageID}", $connDBA);
		$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		$pageCheckResult = $pageCheckArray['position'];
		
		if (isset ($pageCheckResult)) {
			$privilegesCheckGrabber = mysql_query("SELECT * FROM privileges WHERE id = '1'", $connDBA);
			$privilegesCheck = mysql_fetch_array($privilegesCheckGrabber);
			
			if ($pageCheckArray['published'] == "0") {
				$pageCheck = 0;
			} else {
				$pageCheck = 1;
			}
		} else {
			redirect("index.php");
		}	
	}
	
//Process the comments
	if (privileges("addStaffComments") == "true") {
		if (isset($_POST['submit']) && !empty($_POST['id']) && !empty($_POST['comment'])) {
			$pageID = $_GET['page'];
			$id = $_POST['id'];
			$comment = $_POST['comment'];
			$date = time();
			
			if ($pageID == "") {
				$oldDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `position` = '1'", $connDBA);
			} else {
				$oldDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `id` = '{$pageID}'", $connDBA);
			}
			
			$oldData = mysql_fetch_array($oldDataGrabber);
			$oldComments = unserialize($oldData['comment']);
			$oldNames = unserialize($oldData['name']);
			$oldDates = unserialize($oldData['date']);
	
			if (is_array($oldComments)) {
				array_push($oldComments, $comment);
				array_push($oldNames, $id);
				array_push($oldDates, $date);
					
				$comments = mysql_real_escape_string(serialize($oldComments));
				$names = mysql_real_escape_string(serialize($oldNames));
				$dates = mysql_real_escape_string(serialize($oldDates));
			} else {
				$comments = mysql_real_escape_string(serialize(array($comment)));
				$names = mysql_real_escape_string(serialize(array($id)));
				$dates = mysql_real_escape_string(serialize(array($date)));
			}
			
			if ($pageID == "") {
				mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
			} else {
				mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
			}
			
			header("Location: page.php?page=" . $pageID . "&message=added");
			exit;
		}
	}
	
//Delete a comment
	if (privileges("deleteStaffComments") == "true") {
		if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['comment'])) {
			if (!$_GET['page']) {
				$pageIDGrabber = mysql_query("SELECT * FROM `pages` WHERE `position` = '1'", $connDBA);
				$pageIDArray = mysql_fetch_array($pageIDGrabber);
				$pageID = $pageIDArray['id'];
			} else {
				$pageID = $_GET['page'];
			}
			
			$comment = $_GET['comment'];
			
		//If only a single comment is deleted
			if (is_numeric($comment)) {
				$oldDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `id` = '{$pageID}'", $connDBA);
				$oldData = mysql_fetch_array($oldDataGrabber);
				$values = sizeof(unserialize($oldData['date'])) - 1;
				$oldComments = unserialize($oldData['comment']);
				$oldNames = unserialize($oldData['name']);
				$oldDates = unserialize($oldData['date']);
				
				for ($count = 0; $count <= $values; $count++) {
					if ($count == $comment - 1) {
						unset($oldComments[$count]);
						unset($oldNames[$count]);
						unset($oldDates[$count]);
					}
				}
				
				$comments = mysql_real_escape_string(serialize(array_merge($oldComments)));
				$names = mysql_real_escape_string(serialize(array_merge($oldNames)));
				$dates = mysql_real_escape_string(serialize(array_merge($oldDates)));
				
				if ($pageID == "") {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
				} else {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
				}
				
				header("Location: page.php?page=" . $pageID . "&message=deleted");
				exit;
		//If all comments are deleted
			} else {
				$comments = "";
				$names = "";
				$dates = "";
				
				if ($pageID == "") {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
				} else {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
				}
				
				header("Location: page.php?page=" . $pageID . "&message=deletedAll");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	if (($pageInfo == 0 && $pagesExist == 0) || $pageCheck == 0) {
		header("Location: index.php");
		exit;
	} elseif ($pageInfoPrep == 0 && $pagesExist == 1) {
		$title = "Page Not Found";
		$content = "<p>The page you are looking for was not found on our system</p><p>&nbsp;</p><p align=\"center\"><input type=\"button\" name=\"continue\" id=\"continue\" value=\"Continue\" onclick=\"history.go(-1)\" /></p>";
	} else {
		$title = $pageInfo['title'];
		$content = $pageInfo['content'];
		$commentsDisplay = $pageInfo['comments'];
	}
	
	title(stripslashes(htmlentities($title))); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
</head>
<body>
<?php tooltip(); ?>
<?php topPage(); ?>
<?php
//Display content based on login status
	if (isset($_SESSION['MM_Username']) && isset($pageCheck) && $pageCheck !== 0) {
	//The admin toolbox div
		echo "<div class=\"toolBar\">";
		
		if (privileges("editStaffPage") == "true") {
			echo "<a class=\"toolBarItem editTool\" href=\"manage_page.php?id=" . $pageInfoPrep['id'] . "\">Edit This Page</a>";
		}
		
		echo "<a class=\"toolBarItem back\" href=\"index.php\">Back to Staff Pages</a></div>";
	}
	
//Display message updates
	if (isset($_GET['message'])) {
		if ($_GET['message'] == "added") {
			 successMessage("Your comment was added");
		} elseif ($_GET['message'] == "deleted") {
			successMessage("The comment was deleted");
		} elseif ($_GET['message'] == "deletedAll") {
			successMessage("All comments were deleted");
		}
	}
	
//Display the page content	
	echo "<h2>" . stripslashes($pageInfo['title']) . "</h2>" . stripslashes($content);
	
//Display the comments
	if ($commentsDisplay == "1") {
		$arrayCheck = unserialize($pageInfoPrep['comment']);
		
		if (is_array($arrayCheck) && !empty($arrayCheck)) {
			$values = sizeof(unserialize($pageInfoPrep['date'])) - 1;
			$names = unserialize($pageInfoPrep['name']);
			$dates = unserialize($pageInfoPrep['date']);
			$comments = unserialize($pageInfoPrep['comment']);
			
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Comments";
			
			if (privileges("deleteStaffComments") == "true" && !empty($comments)) {
				if (isset ($_GET['page'])) {
					$processor = "?page=" . $_GET['page'] . "&";
				} else {
					$processor = "?";
				}
				
				echo "<a class=\"action smallDelete\" href=\"page.php" . $processor . "action=delete&comment=all\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete all comments')\" onmouseout=\"UnTip()\"></a>";
			}
			
			echo "</p>";
			
			for ($count = 0; $count <= $values; $count++) {
				echo "<div class=\"commentBox\">";
				
				$userID = $names[$count];
				
				if (exist("users", "id", $userID)) {
					$userGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$userID}'", $connDBA);
					$user = mysql_fetch_array($userGrabber);
					echo "<p class=\"commentTitle\">" . $user['firstName'] . " " . $user['lastName'] . " commented on " .  date("l, M j, Y \\a\\t h:i:s A", $dates[$count]);
				} else {
					echo "<p class=\"commentTitle\">An unknown staff member commented on " .  date("l, M j, Y \\a\\t h:i:s A", $dates[$count]);
				}
				
				if (privileges("deleteStaffComments") == "true") {
					if (isset ($_GET['page'])) {
						$processor = "?page=" . $_GET['page'] . "&";
					} else {
						$processor = "?";
					}
					
					$commentID = $count + 1;
					
					echo "<a class=\"action smallDelete\" href=\"page.php" . $processor . "action=delete&comment=" . $commentID . "\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete this comment')\" onmouseout=\"UnTip()\"></a>";
				}
				
				echo "</p>";
				echo stripslashes($comments[$count]);
				echo "</div>";
				
				unset($userGrabber);
				unset($user);
			}
		} else {
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Comments";
			
			if (privileges("deleteStaffComments") == "true" && !empty($comments)) {
				if (isset ($_GET['page'])) {
					$processor = "?page=" . $_GET['page'] . "&";
				} else {
					$processor = "?";
				}
				
				echo "<a class=\"action smallDelete\" href=\"page.php" . $processor . "action=delete&comment=all\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete all comments')\" onmouseout=\"UnTip()\"></a>";
			}
			
			echo "</p><div class=\"noResults\">No comments yet! Be the first to comment.</div>";
		}
		
		if (privileges("addStaffComments") == "true") {
			$userName = $_SESSION['MM_Username'];
			$userGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}'", $connDBA);
			$user = mysql_fetch_array($userGrabber);
			if (isset ($_GET['page'])) {
				$processor = "?page=" . $_GET['page'];
			} else {
				$processor = "";
			}
			
			echo "<form name=\"comments\" id=\"validate\" action=\"page.php" . $processor . "\" method=\"post\"><input type=\"hidden\" name=\"id\" id=\"id\" value=\"" . $user['id'] . "\" />";
			echo "<blockquote><textarea name=\"comment\" id=\"comment\" style=\"width:450px;\" class=\"validate[required]\"></textarea><br/><p>";
			submit("submit", "Add Comment");
			echo "</p></blockquote></form>";
		}
	}
?>
<?php footer(); ?>
</body>
</html>