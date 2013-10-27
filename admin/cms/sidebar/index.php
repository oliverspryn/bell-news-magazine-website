<?php
//Header functions
	require_once('../../../Connections/connDBA.php');
	
//Check access to this page
	if (privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
	
//Reorder pages	
	reorderItem("SideBar", "sidebar", "index.php");
	
//Set page avaliability
	avaliability("SideBar", "sidebar", "index.php");
	
//Delete a page
	delete("SideBar", "sidebar", "index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Sidebar Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Sidebar Control Panel</h2>
<p>This is the sidebar control panel, where you can add, edit, delete, and reorder boxes. These boxes will contain content which can be accessed on a given side of every page on the public website.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem new" href="manage_sidebar.php">Create New Box</a>
<?php
	if (privileges("sideBarSettings") == "true") {
		echo "<a class=\"toolBarItem settings\" href=\"sidebar_settings.php\">Manage Sidebar Settings</a>";
	}
?>
<a class="toolBarItem back" href="../index.php">Back to Pages</a>
<?php
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	$itemAccessCheck = mysql_query("SELECT * FROM `sidebar` WHERE `published` != '0'", $connDBA);
	$itemAccess = mysql_fetch_array($itemAccessCheck);
	
	if ($itemAccess) {
		echo "<a class=\"toolBarItem search\" href=\"../../../index.php\">Preview this Site</a>";
	}
?>
</div>
<?php
//Display message updates
	message("item", "SideBar", "box");
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "settings") {
		successMessage("The sidebar settings were successfully updated.");
	}
?>
<?php
//Table header, only displayed if items exist.
	if (exist("sidebar")) {
		$itemGrabber = query("SELECT * FROM `sidebar` ORDER BY `position` ASC", "raw");
		
		echo "<table class=\"dataTable\"><tbody><tr>";
		
		if (privileges("editSideBar") == "true") {
			echo "<th width=\"25\" class=\"tableHeader\"></th>";
		}
		
		echo "<th width=\"50\" class=\"tableHeader\">Status</th>";
		
		if (privileges("editSideBar") == "true") {
			echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
		}
			
		echo "<th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\" width=\"150\">Type</th><th class=\"tableHeader\">Content</th>";
		
		if (privileges("publishSideBar") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">History</th>";
		}
		
		if (privileges("editSideBar") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
		}
		
		if (privileges("deleteSideBar") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		}
		
	//Loop through each item
		while($itemData = mysql_fetch_array($itemGrabber)) {
			$contentData = unserialize($itemData['content' . $itemData['display']]);
			
			echo "<tr";
		//Alternate the color of each row.
			if ($itemData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
		//Display the show/hide command
			toggleAvaliability("item", $itemData, "SideBar");
			
		//Display the publication status
			status("item", $itemData, "SideBar");
			
		//Display the order changing command
			reorder("sidebar", $itemData, "SideBar");
			
		//Display the title
			displayTitle("item", $itemData, $contentData, "SideBar", "../../../index.php");
			
		//Display the type
			echo "<td width=\"150\">" . $itemData['type'] . "</td>";
			
		//Display the content preview
			displayContent($itemData, $contentData, "SideBar");
			
		//Display the history icon
			historyicon("item", $itemData, $contentData, "SideBar");
			
		//Display the edit icon
			editIcon("item", $itemData, $contentData, "SideBar", "manage_sidebar.php");
			
		//Display the delete icon
			deleteIcon("item", $itemData, $contentData, "SideBar", "index.php");
			
			echo "</tr>";
		}
		
		echo "</tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no items. <a href=\"manage_sidebar.php\">Create one now</a>.</div>";
	 } 
?>
<?php footer(); ?>
</body>
</html>