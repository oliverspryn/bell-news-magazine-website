<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("User,Administrator"); ?>
<?php
//Grant access to this page an it is defined and the user exists
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$userGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
		if ($userCheck = mysql_fetch_array($userGrabber)) {
			if ($_SESSION['MM_UserGroup'] != "Administrator" && $userCheck['userName'] != $_SESSION['MM_Username']) {
				header ("Location: ../index.php");
				exit;
			} else {
				$user = $userCheck;
			}
		} else {
			$user = false;
			
			if ($_SESSION['MM_UserGroup'] != "Administrator") {
				header ("Location: ../index.php");
				exit;
			} else {
				header("Location: index.php");
				exit;
			}
		}
	} else {
		if ($_SESSION['MM_UserGroup'] != "Administrator") {
			header ("Location: ../index.php");
			exit;
		} else {
			header("Location: index.php");
			exit;
		}
	}
	
//Change the user's password
	if (($user['userName'] === $_SESSION['MM_Username'] || $_SESSION['MM_UserGroup'] == "Administrator") && isset($_GET['action']) && $_GET['action'] == "changePassword") {
		$id = $_GET['id'];
		
		if ($_SESSION['MM_UserGroup'] == "Administrator") {
			header("Location: manage_user.php?id=" . $id . "#password");
			exit;
		} else {
			mysql_query ("UPDATE `users` SET `changePassword` = 'on' WHERE `id` = '{$id}'", $connDBA);
			header("Location: ../index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title($user['firstName'] . " " . $user['lastName']); ?>
<?php headers(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body>
<?php topPage(); ?>
<h2><?php echo $user['firstName'] . " " . $user['lastName']; ?></h2>
<p>&nbsp;</p>
<?php 
	if ($_SESSION['MM_UserGroup'] != "User") {
		echo "<div class=\"toolBar\">";
		
		echo "<a class=\"toolBarItem editTool\" href=\"manage_user.php?id=" . $user['id'] . "\">Edit this User</a>";
		
		if ($user['userName'] != $_SESSION['MM_Username']) {
			echo "<a class=\"toolBarItem deleteTool\" href=\"index.php?action=delete&id=" . $user['id'] . "\" onclick=\"return confirm('This action cannot be undone. Continue?')\">Delete this User</a>";
		}
		
		echo "</div>";
	}
?>
<br />
<div class="catDivider one">User Information</div>
<div class="stepContent">
<table width="100%">
  <tr>
    <td width="200"><div align="right">First Name:</div></td>
    <td><?php echo $user['firstName']; ?></td>
  </tr>
  <tr>
    <td width="200"><div align="right">Last Name:</div></td>
    <td><?php echo $user['lastName']; ?></td>
  </tr>
  <tr>
    <td width="200"><div align="right">User Name:</div></td>
    <td><?php echo $user['userName']; ?></td>
  </tr>
  <?php
  //If the user is allowed to change their password
		if ($user['userName'] === $_SESSION['MM_Username'] || $_SESSION['MM_UserGroup'] == "Administrator") {
			echo "<tr>
				<td><div align=\"right\">Password:</div></td>
				<td><a href=\"profile.php?id=" . $user['id'] . "&action=changePassword\">Change Password</a></td>
			</tr>";
		}
  ?>
  <tr>
    <td><div align="right">Role:</div></td>
    <td><?php echo $user['role']; ?></td>
  </tr>
  <tr>
    <td><div align="right">Last Active:</div></td>
    <td><?php echo date("l, M j, Y \\a\\t h:i:s A", $user['active']); ?></td>
  </tr>
</table>
</div>
<div class="catDivider two">Contact Information</div>
<div class="stepContent">
    <table width="100%">
    <tr>
        <td width="200"><div align="right">
          <?php if ($user['emailAddress2'] == "" && $user['emailAddress3'] == "") {echo "Email Address:";} else {echo "Primary Email Address:";} ?>
      </div></td>
      <td>
	  <?php 
	  	if (privileges("sendEmail") == "true") {
		  	echo "<a href=\"../collaboration/send_email.php?type=user&id=" . $user['id'] . "&address=1\">" . $user['emailAddress1'] . "</a>";
		} else {
			echo $user['emailAddress1'];
		}
	  ?>
      </td>
      </tr>
      <?php
      //If a second email address is configured
            if ($user['emailAddress2'] != "") {
                echo "<tr>
                    <td><div align=\"right\">Secondary Email Address:</div></td>
                    <td>";
					
					if (privileges("sendEmail") == "true") {
						echo "<a href=\"../collaboration/send_email.php?type=user&id=" . $user['id'] . "&address=2\">" . $user['emailAddress2'] . "</a>";
					} else {
						echo $user['emailAddress2'];
					}
					
					echo "</td>
                </tr>";
            }
      ?>
  	  <?php
      //If a tertiary email address is configured
            if ($user['emailAddress3'] != "") {
                echo "<tr>
                    <td><div align=\"right\">Tertiary Email Address:</div></td>
                    <td>";
					
					if (privileges("sendEmail") == "true") {
						echo "<a href=\"../collaboration/send_email.php?type=user&id=" . $user['id'] . "&address=3\">" . $user['emailAddress3'] . "</a>";
					} else {
						echo $user['emailAddress3'];
					}
					
					echo "</td>
                </tr>";
            }
      ?>
  </table>
</div>
<div class="catDivider three">Finish</div>
<div class="stepContent">
  <blockquote>
    <p><input name="finish" id="finish" onclick="MM_goToURL('parent','<?php if ($_SESSION['MM_UserGroup'] == "User") {echo "../index.php";} else {echo "index.php";} ?>');return document.MM_returnValue" value="Finish" type="button"></p>
  </blockquote>
</div>
<?php footer(); ?>
</body>
</html>
