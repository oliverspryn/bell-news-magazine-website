<?php
//Header functions
	require_once('../../../Connections/connDBA.php');
	
//Check access to this page
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}

//Reorder tabs
	reorderItem("External", "external", "index.php");
	
//Set tab avaliability
	avaliability("External", "external", "index.php");
	
//Delete a tab
	delete("External", "external", "index.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("External Content Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>External Content Control Panel</h2>
<p>This is the external content control panel. Content created here can be embedded as a &quot;mini-site&quot; on other sites or blogs. Multiple pages can be embedded in this mini-site. Each of these pages are embedded as a tab. Copy the following HTML into your website or blog to view the mini-site.</p>
<div class="code">
&lt;div align=&quot;center&quot;&gt;&lt;iframe src=&quot;<?php echo $root; ?>external.php&quot; width=&quot;320&quot; height=&quot;240&quot; frameborder=&quot;0&quot;&gt;&lt;/div&gt;</div><br />
<?php
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	$tabAccessCheck = mysql_query("SELECT * FROM `external` WHERE `published` != '0'", $connDBA);
	$tabAccess = mysql_fetch_array($tabAccessCheck);
	
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		echo "<div class=\"toolBar\">";
	}

	if (privileges("createExternal") == "true") {
		echo "<a class=\"toolBarItem new\" href=\"manage_external.php\">Create New Tab</a>";
	}
	
	echo "<a class=\"toolBarItem back\" href=\"../index.php\">Back to Pages</a>";
	
	if ($tabAccess) {
		echo "<a class=\"toolBarItem search\" href=\"javascript:void\"onclick=\"MM_openBrWindow('../../../external.php','','status=yes,scrollbars=yes,resizable=yes,width=320,height=240')\">Preview this Content</a>";
	}
	
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		echo "</div>";
	}

	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
	//Display message updates
		message("tab", "External", "tab");
	
	//Table header, only displayed if tabs exist
		if (exist("external")) {
			$tabGrabber = query("SELECT * FROM `external` ORDER BY `position` ASC", "raw");
			
			echo "<table class=\"dataTable\"><tbody><tr>";
			
			if (privileges("editExternal") == "true") {
				echo "<th width=\"25\" class=\"tableHeader\"></th>";
			}
			
			echo "<th width=\"50\" class=\"tableHeader\">Status</th>";
			
			if (privileges("editExternal") == "true") {
				echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
			}
				
			echo "<th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th>";
			
			if (privileges("publishExternal") == "true") {
				echo "<th width=\"50\" class=\"tableHeader\">History</th>";
			}
			
			if (privileges("editExternal") == "true") {
				echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
			}
			
			if (privileges("deleteExternal") == "true") {
				echo "<th width=\"50\" class=\"tableHeader\">Delete</th>";
			}
			
			echo "</tr>";
		
		//Loop through each tab
			while($tabData = mysql_fetch_array($tabGrabber)) {
				$contentData = unserialize($tabData['content' . $tabData['display']]);
				
			//Alternate the color of each row	
				echo "<tr";
				if ($tabData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				
			//Display the show/hide command
				toggleAvaliability("tab", $tabData, "External");
				
			//Display the publication status
				status("tab", $tabData, "External");
				
			//Display the order changing command
				reorder("external", $tabData, "External");
				
			//Display the title
				displayTitle("tab", $tabData, $contentData, "External", "../../../external.php");
				
			//Display the content preview
				displayContent($tabData, $contentData, "External");
				
			//Display the history icon
				historyIcon("tab", $tabData, $contentData, "External");
				
			//Display the edit icon
				editIcon("tab", $tabData, $contentData, "External", "manage_external.php");
				
			//Display the delete icon
				deleteIcon("tab", $tabData, $contentData, "External", "index.php");
				
				echo "</tr>";
			}
			
			echo "</tbody></table>";
		 } else {
			echo "<div class=\"noResults\">This site has no external content.";
			
			if (privileges("createExternal") == "true") {
				echo " <a href=\"manage_external.php\">Create a new tab now</a>.</div>";
			} else {
				echo " You do not have the privileges to create a tab.";
			}
		 }
	}
?>
<?php footer(); ?>
</body>
</html>