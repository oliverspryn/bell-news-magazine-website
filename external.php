<?php require_once('Connections/connDBA.php'); ?>
<?php
//Check the site settings
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	
//Select the tabs
	$query = "SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0'";
	$tabGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0' ORDER BY `position` ASC", $connDBA);
	$contentGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0' ORDER BY `position` ASC", $connDBA);

        if (!isset($_GET['strip'])) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("External Content"); ?>
<?php headers(); ?>
<script src="javascripts/tabbedPanels/tabbedPanels.js" type="text/javascript"></script>
<script src="javascripts/tabbedPanels/getURLParameter.js" type="text/javascript"></script>
<link href="styles/common/tabbedPanels.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var params = Spry.Utils.getLocationParamsAsObject(); 
</script>
</head>

<body class="overrideBackground">
<?php
        }

	if (query($query)) {
		echo "<div id=\"TabbedPanels1\" class=\"TabbedPanels\">";
	
	//Display the titles
		echo "<ul class=\"TabbedPanelsTabGroup\">";
		
		while ($tab = mysql_fetch_array($tabGrabber)) {
			$title = unserialize($tab['content' . $tab['display']]);
			echo "<li class=\"TabbedPanelsTab\" tabindex=\"0\">" . stripslashes($title['title']) . "</li>";
		}
		
	//Display the content
		echo "</ul><div class=\"TabbedPanelsContentGroup\">";
		
		while ($content = mysql_fetch_array($contentGrabber)) {
			$body = unserialize($content['content' . $content['display']]);
			echo "<div class=\"TabbedPanelsContent\">" . stripslashes($body['content']) . "</div>";
		}
		
		echo "</div>";
	} else {
		echo "<div align=\"center\"><p>No content is avaliable. Please check back later.</p></div>";
	}
?>
<script type="text/javascript">
<!--
var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1", {defaultTab: params.tab ? params.tab : 0}); 
//-->
</script>
<?php
        if (!isset($_GET['strip'])) {
?>
</body>
</html>
<?php
        }
?>