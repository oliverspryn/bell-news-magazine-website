<?php
//Header functions
	require_once('../../Connections/connDBA.php');
	
//Check access to this page
	if (privileges("createPage") == "true" || privileges("editPage") == "true" || privileges("deletePage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}

//Reorder pages	
	reorderItem("Page", "pages", "index.php");
	
//Set page avaliability
	avaliability("Page", "pages", "index.php", "../../index.php");
	
//Delete a page
	delete("Page", "pages", "index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Pages Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script src="../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Pages Control Panel</h2>
<p>This is the pages control panel, where you can add, edit, delete, and reorder pages.</p>
<p>&nbsp;</p>
<?php
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	$pageAccessCheck = mysql_query("SELECT * FROM `pages` WHERE `published` != '0'", $connDBA);
	$pageAccess = mysql_fetch_array($pageAccessCheck);
	
	if (privileges("createPage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true" || $pageAccess) {
		echo "<div class=\"toolBar\">";
	}

	if (privileges("createPage") == "true") {
		echo "<a class=\"toolBarItem new\" href=\"manage_page.php\">Create New Page</a>";
	}
	
	if (privileges("siteSettings") == "true") {
		echo "<a class=\"toolBarItem settings\" href=\"site_settings.php\">Manage Site Settings</a>";
	}
	
	if (privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true") {
		echo "<a class=\"toolBarItem sideBar\" href=\"sidebar/index.php\">Manage Sidebar</a>";
	}
	
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		echo "<a class=\"toolBarItem external\" href=\"external/index.php\">External Content</a>";
	}
	
	if (privileges("viewStatistics") == "true") {
		echo "<a class=\"toolBarItem statistics\" href=\"../statistics/index.php\">Statistics</a>";
	}
	
	if ($pageAccess) {
		echo "<a class=\"toolBarItem search\" href=\"../../index.php\">Preview this Site</a>";
	}
	
	if (privileges("createPage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true" || $pageAccess) {
		echo "</div>";
	}

//Display message updates
	message("page", "Page", "page");
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "logo") {
		successMessage("The logo was successfully updated. It may take a few moments to update system wide.");
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "icon") {
		successMessage("The browser icon was successfully updated. It may take a few moments to update system wide.");
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "siteInfo") {
		successMessage("The site information was successfully updated");
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "theme") {
		successMessage("The theme was successfully updated");
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "security") {
		successMessage("The security settings were successfully updated");
	}
	
//Display a link back to the higher-level pages
	if (isset($_GET['category']) && exist("pages", "id", $_GET['category']) && exist("pages", "parentPage", $_GET['category'])) {
		$parentPage = query("SELECT * FROM `pages` WHERE `id` = '{$_GET['category']}'");
		
		if ($parentPage['parentPage'] != "0") {
			echo "<a href=\"index.php?category=" . $parentPage['parentPage'] . "\">&lt;&lt; Up one Level</a><br /><br />";
		} else {
			echo "<a href=\"index.php\">&lt;&lt; Up one Level</a><br /><br />";
		}
	} else {
		if (!isset($_GET['added']) && !isset($_GET['updated'])) {
			echo "<br />";
		}
	}

//Table header, only displayed if pages exist
	if (exist("pages")) {
		if (isset($_GET['category']) && exist("pages", "id", $_GET['category']) && exist("pages", "parentPage", $_GET['category'])) {
			$pageGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '{$_GET['category']}' ORDER BY `subPosition` ASC", "raw");
		} else {
			$pageGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '0' ORDER BY `position` ASC", "raw");
		}
		
		$count = 1;
		
		echo "<table class=\"dataTable\"><tbody><tr>";
		
		if (privileges("editPage") == "true") {
			echo "<th width=\"25\" class=\"tableHeader\"></th>";
		}
		
		echo "<th width=\"50\" class=\"tableHeader\">Status</th>";
		
		if (privileges("editPage") == "true") {
			echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
		}
			
		echo "<th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th>";
		
		if (privileges("publishPage") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">History</th>";
		}
		
		if (privileges("editPage") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
		}
		
		if (privileges("deletePage") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		}
	
	//Loop through each page
		while($pageData = mysql_fetch_array($pageGrabber)) {
			$contentData = unserialize($pageData['content' . $pageData['display']]);
			
		//Alternate the color of each row
			echo "<tr";
			if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
		//Display the show/hide command
			toggleAvaliability("page", $pageData, "Page");
			
		//Display the publication status
			status("page", $pageData, "Page");
			
		//Display the order changing command
			reorder("pages", $pageData, "Page");
			
		//Display the title
			displayTitle("page", $pageData, $contentData, "Page", "../../index.php");
			
		//Display the content preview
			displayContent($pageData, $contentData, "Page");
			
		//Display the history icon
			historyIcon("page", $pageData, $contentData, "Page");
			
		//Display the edit icon
			editIcon("page", $pageData, $contentData, "Page", "manage_page.php");
			
		//Display the delete icon
			deleteIcon("page", $pageData, $contentData, "Page", "index.php");
			
		//Display the sub-pages link
			$rows = 0;
			
			if (privileges("editPage") == "true") {
				$rows++;
				$rows++;
				$rows++;
			}
			
			if (privileges("publishPage") == "true") {
				$rows++;
			}
			
			if (privileges("deletePage") == "true") {
				$rows++;
			}
			
			if (exist("pages", "parentPage", $pageData['id'])) {
				echo "</tr>";
				echo "<tr";
				if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<td colspan=\"" . sprintf($rows + 3) . "\"><div align=\"left\"><a href=\"index.php?category=" . $pageData['id'] . "\">View Sub-pages</a></div></td>";
			}
			
			echo "</tr>";
			
			$count++;
		}
		
		echo "</tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no pages.";
		
		if (privileges("createPage") == "true") {
			echo " <a href=\"manage_page.php\">Create one now</a>.</div>";
		} else {
			echo " You do not have the privileges to create a page.";
		}
	 }
?>
<?php footer(); ?>
</body>
</html>