<?php require_once('../../../Connections/connDBA.php'); ?>
<?php
	if (privileges("sideBarSettings") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Grab the form data
	$sideBarGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
	$sideBar = mysql_fetch_array($sideBarGrabber);

//Process the form
	if (isset($_POST['submit']) && isset($_POST['side'])) {
		$side = $_POST['side'];
		
		mysql_query("UPDATE `siteprofiles` SET `sideBar` = '{$side}'", $connDBA);
		header("Location: index.php?updated=settings");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Sidebar Settings"); ?>
<?php headers(); ?>
<?php validate(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body>
<?php topPage(); ?>
<h2>Sidebar Settings</h2>
<p>Set which side the sidebar will display.</p>
<p>&nbsp;</p>
<form action="sidebar_settings.php" method="post" name="settings" id="settings" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider one">Settings</div>
<div class="stepContent">
  <blockquote>
    <p>Sidebar location:</p>
    <blockquote>
      <p>
        <label>
          <input type="radio" name="side" value="Left" id="side_0"<?php if ($sideBar['sideBar'] == "Left") {echo " checked=\"checked\"";} ?> />
        Left</label>
        <label>
          <input type="radio" name="side" value="Right" id="side_1"<?php if ($sideBar['sideBar'] == "Right") {echo " checked=\"checked\"";} ?> />
        Right</label>
        <br />
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider two">Submit</div>
<div class="stepContent">
  <blockquote>
    <p>
      <?php submit("submit", "Submit"); ?>
      <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
    </p>
  </blockquote>
</div>
</form>
<?php footer(); ?>
</body>
</html>