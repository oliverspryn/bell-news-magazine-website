<?php require_once('Connections/connDBA.php'); ?>
<?php
//Logout the user
	session_destroy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Logout"); ?>
<?php headers(); ?>
<?php
	if (isset($_GET['action']) && $_GET['action'] == "relogin") {
		echo "<meta http-equiv=\"refresh\" content=\"8; url=login.php\">";
	} else {
		echo "<meta http-equiv=\"refresh\" content=\"3; url=index.php\">";
	}
?>
<script src="javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("public"); ?>
<h4><a href="index.php">Home</a> &#9658 Logout</h4>
<h2>Logout</h2>
<?php
	if (isset($_GET['action']) && $_GET['action'] == "relogin") {
		echo "<p align=\"center\">&nbsp;</p>
		<div align=\"center\">Your profile has been updated. Since your role in this site has changed, you must login again.</div>
		<br />
		<div align=\"center\">
		   <input name=\"continue\" type=\"button\" id=\"continue\" onclick=\"MM_goToURL('parent','login.php');return document.MM_returnValue\" value=\"Continue\" />
		 </div>
		 <p align=\"center\">&nbsp;</p>
		 <p align=\"center\">&nbsp;</p>
		 <p align=\"center\">&nbsp;</p>
		 <p>&nbsp;</p>";
	} else {
		echo "<p align=\"center\">&nbsp;</p>
		<div align=\"center\">You have successfully logged out</div>
		<br />
		<div align=\"center\">
		   <input name=\"continue\" type=\"button\" id=\"continue\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Continue\" />
		 </div>
		 <p align=\"center\">&nbsp;</p>
		 <p align=\"center\">&nbsp;</p>
		 <p align=\"center\">&nbsp;</p>
		 <p>&nbsp;</p>";
	}
?>
<?php footer("public"); ?>
</body>
</html>