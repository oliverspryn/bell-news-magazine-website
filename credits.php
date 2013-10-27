<?php require_once('Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Credits"); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<?php
	if (!isset ($_SESSION['MM_UserGroup'])) {
		echo "<h4><a href=\"index.php\">Home</a> &#9658 Credits</h4>";
	} else {
		echo "<h4><a href=\"admin/index.php\">Home</a> &#9658 Credits</h4>";
	}
?>
<h2>Credits</h2>
<p>Apex Development thanks all of the third party sources and developers which made this project possible.
<p>&nbsp;</p>
<p>System core:</p>
<blockquote>
  <p><img src="images/common/logo.png" width="270" height="80" alt="Apex Development Logo" /></p>
  <p>&nbsp;</p>
</blockquote>
<p>Third-party modules:</p>
<blockquote>
  <p><a href="http://tinymce.moxiecode.com/" target="_blank">TinyMCE</a></p>
  <p><a href="http://www.lunarvis.com/products/tinymcefilebrowserwithupload.php" target="_blank">TinyBrowser</a></p>
  <p><a href="http://www.afterthedeadline.com/" target="_blank">After the Deadline</a></p>
  <p><a href="http://www.themza.com/moodle/" target="_blank">ThemZa</a></p>
  <p><a href="http://www.fusioncharts.com/free/" target="_blank">FusionCharts</a></p>
  <p><a href="http://jqueryui.com/demos/datepicker/" target="_blank">jQuery DatePicker</a></p>
  <p><a href="http://www.position-absolute.com/articles/jquery-form-validator-because-form-validation-is-a-mess/" target="_blank">jQuery Validator</a></p>
  <p><a href="http://labs.adobe.com/technologies/spry/" target="_blank">Spry Ajax Library</a></p>
  <p>&nbsp;</p>
  <p>Other minor scripts accredited in source code</p>
</blockquote>
<?php footer(); ?>
</body>
</html>