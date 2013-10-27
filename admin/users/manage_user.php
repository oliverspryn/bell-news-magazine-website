<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Decide whether a user is being edited or created
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$userGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
		if ($userCheck = mysql_fetch_array($userGrabber)) {
			$user = $userCheck;
		} else {
			header("Location: index.php");
			exit;
		}
	}
	
//Check to see if the user name exists
	validateName("users", "userName");
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['role']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['userName']) && !empty($_POST['primaryEmail'])) {
		$role = $_POST['role'];
		$firstName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['firstName']));
		$lastName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['lastName']));
		$userName =  mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9 \s]/", "", $_POST['userName']));
		
		if (isset ($_POST['id']) && $_POST['passWord'] == "") {
			$id = $_POST['id'];
			$checkPassWordGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
			$checkPassWord = mysql_fetch_array($checkPassWordGrabber);
			$passWord = $checkPassWord['passWord'];
		} elseif (isset ($_POST['id']) && $_POST['passWord'] !== "") {
			$passWord = encrypt($_POST['passWord']);	
		} elseif (!isset ($_POST['id']) && $_POST['passWord'] == "") {
			header ("Location: manage_user.php");
			exit;
		} elseif (!isset ($_POST['id']) && $_POST['passWord'] !== "") {
			$passWord = encrypt($_POST['passWord']);
		}
		
		$userNameCheckGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}'", $connDBA);
		
		if ($userNameCheck = mysql_fetch_array($userNameCheckGrabber)) {
			if (isset($_GET['id'])) {
				$userID = $_GET['id'];
				$currentUserGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$userID}'", $connDBA);
				$currentUser = mysql_fetch_array($currentUserGrabber);
				
				if (strtolower($currentUser['userName']) != strtolower($userName)) {
					header ("Location: manage_user.php?id=" . $id . "&error=identical");
					exit;
				}
			} else {
				header ("Location: manage_user.php?error=identical");
				exit;
			}
		}
	
		$changePassword = $_POST['changePassword'];
		$primaryEmail = $_POST['primaryEmail'];
		$secondayEmail = $_POST['secondaryEmail'];
		$tertiaryEmail = $_POST['tertiaryEmail'];
		
		if (isset ($id)) {
			$adminCheck = mysql_query("SELECT * FROM `users` WHERE `role` = 'Administrator'", $connDBA);
			$idCheckGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$_SESSION['MM_Username']}'", $connDBA);
			$idCheck = mysql_fetch_array($idCheckGrabber);
			
			if (mysql_num_rows($adminCheck) == "1" && $role != "Administrator" && $idCheck['id'] == $id) {
				if (isset($_GET['id'])) {
					header("Location: manage_user.php?id=" . $_GET['id'] . "&error=noAdmin");
				} else {
					header("Location: manage_user.php?error=noAdmin");
				}
				
				exit;
			}
						
			mysql_query("UPDATE `users` SET `firstName` = '{$firstName}', `lastName` = '{$lastName}', `userName` = '{$userName}', `passWord` = '{$passWord}', `changePassword` = '{$changePassword}', `emailAddress1` = '{$primaryEmail}', `emailAddress2` = '{$secondayEmail}', `emailAddress3` = '{$tertiaryEmail}', `role` = '{$role}' WHERE `id` = '{$id}'", $connDBA);
			
			if ($user['userName'] == $_SESSION['MM_Username']) {
				$oldGroup = $_SESSION['MM_UserGroup'];
				
				if ($oldGroup != $role) {
					header("Location: " . $root . "logout.php?action=relogin");
					exit;
				} else {
					header ("Location: index.php?message=userEdited");
					exit;
				}
			} else {
				header ("Location: index.php?message=userEdited");
				exit;
			}
		} else {
			mysql_query("INSERT INTO `users`(
						id, active, firstName, lastName, userName, passWord, changePassword, emailAddress1, emailAddress2, emailAddress3, role
						) VALUES (
							NULL, '', '{$firstName}', '{$lastName}', '{$userName}', '{$passWord}', '{$changePassword}', '{$primaryEmail}', '{$secondayEmail}', '{$tertiaryEmail}', '{$role}'
						)", $connDBA);
			
			header ("Location: index.php?message=userCreated");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
//Grab user information if they are being edited
	if (isset ($id)) {
		$title = "Modify " . $user['firstName'] . " " . $user['lastName'];
	} else {
		$title = "Create New User";
	}
	
	title($title);
?>
<?php headers(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage(); ?>
      
<h2><?php echo $title ?></h2>
<p><?php if (isset($id)) {echo "Modify " . $user['firstName'] . " " . $user['lastName'] . "'s information";} else {echo "Create a new user";} ?> by <?php if (isset($id)) {echo "modifying";} else {echo "filling in";} ?> the information below.</p>
<?php
//Display message updates
	if (isset($_GET['error']) && $_GET['error'] == "identical") {
		errorMessage("This user name is already taken.");
	}
	
	if (isset($_GET['error']) && $_GET['error'] == "noAdmin") {
		errorMessage("You cannot change your role, otherwise, this site would be left with out an administrator.");
	}
?>
<form name="users" method="post" action="manage_user.php<?php if (isset($id)) {echo"?id=" . $id;} ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<?php
//Echo the user id, if the user is being edited
	if (isset($id)) {
		echo"<input type=\"hidden\" name=\"id\" id=\"id\" value =\"" . $id . "\" />";
	}
?>
<div class="catDivider one">Select a Role</div>
<div class="stepContent">
<blockquote>
  <p>Select a role for this user<span class="require">*</span>: </p>
  <blockquote>
    <?php
		echo "<p><select name=\"role\" id=\"role\" class=\"validate[required]\">";
		echo "<option value=\"\"";
			if (!isset($id)) {echo " selected=\"selected\"";}
		echo ">- Select Role -</option>";
		
		echo "<option value=\"User\"";
			if (isset($id)) {if ($user['role'] == "User") {echo " selected=\"selected\"";}} 
		echo ">User</option>";
		
		echo "<option value=\"Administrator\"";
			if (isset($id)) {if ($user['role'] == "Administrator") {echo " selected=\"selected\"";}}
		echo ">Administrator</option>";
		echo "</select></p>";
	?>
    </blockquote>
</blockquote>
</div>
<div class="catDivider two">User Information</div>
<div class="stepContent">
<blockquote>
  <p>First Name<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <input name="firstName" type="text" id="firstName" size="50" autocomplete="off" class="validate[required,custom[noSpecialCharacters]]"<?php if (isset($id)) {echo " value=\"" . $user['firstName'] . "\"";} ?> />
    </p>
  </blockquote>
  <p>Last Name<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <input name="lastName" type="text" id="lastName" size="50" autocomplete="off" class="validate[required,custom[noSpecialCharacters]]"<?php if (isset($id)) {echo " value=\"" . $user['lastName'] . "\"";} ?> />
    </p>
  </blockquote>
  <p>User Name<?php if (isset($user)) {if ($user['userName'] != $_SESSION['MM_Username']) {echo "<span class=\"require\">*</span>";}} else {echo "<span class=\"require\">*</span>";} ?>:</p>
  <blockquote>
    <p>
      <input name="userName" type="text" id="userName" size="50" autocomplete="off" class="validate[required,length[6,30],custom[noSpecialCharactersSpaces],ajax[ajaxUser]]<?php if (isset($user)) {if ($user['userName'] == $_SESSION['MM_Username']) {echo " disabled";}} ?>"<?php if (isset($user)) {if ($user['userName'] == $_SESSION['MM_Username']) {echo " readonly=\"readonly\"";}} if (isset($id)) {echo " value=\"" . $user['userName'] . "\"";} ?> />
    </p>
  </blockquote>
  <a name="password"></a>
  <p>Password<?php if (!isset($id)) {echo "<span class=\"require\">*</span>";} ?>:</p>
  <blockquote>
    <p>
      <input name="passWord" type="password" id="passWord" size="50" autocomplete="off"<?php if (!isset($id)) {echo " class=\"validate[required,length[6,30]]\"";} ?> />
      <?php
		  if (isset($user)) {
			  if ($user['userName'] != $_SESSION['MM_Username']) { 
				echo "<label>
				  <input type=\"checkbox\" name=\"changePassword\" id=\"changePassword\"";
				  if (isset($id)) {
					  if ($user['changePassword'] == "on") {
						  echo " checked=\"checked\"";
					  }
				  }
				  echo " /> Force Password Change
				</label>";
			  }
		  } else {
			  echo "<label>
				<input type=\"checkbox\" name=\"changePassword\" id=\"changePassword\"";
				if (isset($id)) {
					if ($user['changePassword'] == "on") {
						echo " checked=\"checked\"";
					}
				}
				echo " /> Force Password Change
			  </label>";
		  }
      ?>
    </p>
  </blockquote>
</blockquote>
</div>
<div class="catDivider three">Contact Information</div>
<div class="stepContent">
  <blockquote>
    <p>Primary Email Address<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <input name="primaryEmail" type="text" id="primaryEmail" size="50" autocomplete="off" class="validate[required,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress1'] . "\"";} ?> />
      </p>
    </blockquote>
    <p>Secondary Email Address:</p>
    <blockquote>
      <p>
        <input name="secondaryEmail" type="text" id="secondaryEmail" size="50" autocomplete="off" class="validate[optional,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress2'] . "\"";} ?> />
      </p>
    </blockquote>
    <p>Tertiary Email Address:</p>
    <blockquote>
      <p>
        <input name="tertiaryEmail" type="text" id="tertiaryEmail" size="50" autocomplete="off" class="validate[optional,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress3'] . "\"";} ?> />
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider four">Submit</div>
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