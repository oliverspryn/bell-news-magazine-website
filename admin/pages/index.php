<?php
//Header functions
	require_once('../../Connections/connDBA.php');
	
//Check access to this page
	if (privileges("viewStaffPage") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
	
//Reorder pages	
	reorderItem("StaffPage", "staffpages", "index.php");
	
//Delete a page
	delete("StaffPage", "staffpages", "index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Staff Pages Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script src="../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Staff Pages Control Panel</h2>
<p>Staff pages are simply a protected series of webpages to which only registered users may access. These pages are largely intended for staff collaboration.</p>
<?php
	if (privileges("createStaffPage") == "true") {
		echo "<p>&nbsp;</p><div class=\"toolBar\"><a class=\"toolBarItem new\" href=\"manage_page.php\">Create New Page</a></div>";
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<?php 
//Display message updates
	message("page", "StaffPage", "page");
?>
<?php
//Table header, only displayed if pages exist
	if (exist("staffpages")) {
		$pageGrabber = query("SELECT * FROM `staffpages` ORDER BY `position` ASC", "raw");
		
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"50\" class=\"tableHeader\">Status</th>";
		
		if (privileges("editStaffPage") == "true") {
			echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
		}
			
		echo "<th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th>";
		
		if (privileges("publishStaffPage") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">History</th>";
		}
		
		if (privileges("editStaffPage") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
		}
		
		if (privileges("deleteStaffPage") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		}
	
	//Loop through each page
		while($pageData = mysql_fetch_array($pageGrabber)) {
			$contentData = unserialize($pageData['content' . $pageData['display']]);
			
		//Alternate the color of each row
			echo "<tr";
			if ($pageData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
		//Display the publication status
			status("page", $pageData, "StaffPage");
			
		//Display the order changing command
			reorder("staffpages", $pageData, "StaffPage");
			
		//Display the title
			displayTitle("page", $pageData, $contentData, "StaffPage", "page.php");
			
		//Display the content preview
			displayContent($pageData, $contentData, "StaffPage");
			
		//Display the history icon
			historyIcon("page", $pageData, $contentData, "StaffPage");
			
		//Display the edit icon
			editIcon("page", $pageData, $contentData, "StaffPage", "manage_page.php");
			
		//Display the delete icon
			deleteIcon("page", $pageData, $contentData, "StaffPage", "index.php");
			
			echo "</tr>";
		}
		
		echo "</tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no staff pages.";
		
		if (privileges("createStaffPage") == "true") {
			echo " <a href=\"manage_page.php\">Create one now</a>.</div>";
		} else {
			echo " You do not have the privileges to create a staff page.";
		}
	 } 
?>
<?php footer(); ?>
</body>
</html>