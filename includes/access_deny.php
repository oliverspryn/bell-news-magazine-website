<?php require_once('../Connections/connDBA.php'); ?>
<?php
//Grab the sidebar
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' AND published != '0'", $connDBA);
	$sideBarResult = mysql_fetch_array($sideBarCheck);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Access Denied"); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<h4>
<?php
	if (loggedIn()) {
		echo "<a href=\"" . $root . "admin/index.php\">Home</a> ";
	} else {
		echo "<a href=\"" . $root . "index.php\">Home</a> ";
	}
?>
&#9658 Error Page</h4>
<?php
//Use the layout control if the page is displaying a sidebar
	$sideBarLocationGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
	$sideBarLocation = mysql_fetch_array($sideBarLocationGrabber);
		
	if ($sideBarResult) {
		echo "<div class=\"layoutControl\"><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "contentRight";
		} else {
			echo "contentLeft";
		}
		echo "\">";
	}

//Display the error content
	echo "<h2>Access Denied</h2>";
	
	if (isset($_GET['error']) && $_GET['error'] == "403") {
		echo "<p>You do not have premission to access this content</p>";
	} elseif (isset($_GET['error']) && $_GET['error'] == "404") {
		echo "<p>The page you are looking for was not found on our system</p>";
	} else {
		echo "<p>You do not have premission to access this content</p>";
	}
	
	echo "<p>&nbsp;</p><p align=\"center\"><input type=\"button\" name=\"continue\" id=\"continue\" value=\"Continue\" onclick=\"history.go(-1)\" /></p>";

//Display the sidebar
	if ($sideBarResult) {
		$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' AND published != '0' ORDER BY `position` ASC", $connDBA);
		
		echo "</div><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "dataLeft";
		} else {
			echo "dataRight";
		}
		
		echo "\"><br /><br /><br />";
		
		while ($sideBarPrep = mysql_fetch_array($sideBarCheck)) {
			$sideBar = unserialize($sideBarPrep['content' . $sideBarPrep['display']]);
			
			switch ($sideBarPrep['type']) {
			//If this is a custom content box
				case "Custom Content" : 				
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "</h2></div></div><div class=\"content\">" . stripslashes($sideBar['content']) . "</div></div>";
					} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") != "true") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "</h2></div></div><div class=\"content\">" . stripslashes($sideBar['content']) . "</div></div>";
					} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") == "true") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "&nbsp;<a class=\"smallEdit\" href=\"admin/cms/sidebar/manage_sidebar.php?id=" . $sideBarPrep['id'] . "\"></a></h2></div></div><div class=\"content\">" . stripslashes($sideBar['content']) . "</div></div>";
					} break;
			//If this is a login box	
				case "Login" : 
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "</h2></div></div><div class=\"content\">" . stripslashes($sideBar['content']) . "<form id=\"login\" name=\"login\" method=\"post\" action=\"index.php\"><div align=\"center\"><div style=\"width:75%;\"><p>User name: <input type=\"text\" name=\"username\" id=\"username\" autocomplete=\"off\" /><br />Password: <input type=\"password\" name=\"password\" id=\"password\" autocomplete=\"off\" /></p><p><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Login\" /></p></div></div></form></div></div>";
					} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") == "true") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "&nbsp;<a class=\"smallEdit\" href=\"admin/cms/sidebar/manage_sidebar.php?id=" . $sideBarPrep['id'] . "\"></a></h2></div></div><div class=\"content\">" . stripslashes($sideBar['content']) . "<p><strong>You are already logged in.</strong></p></div></div>";
					} break;
			  }
		}
		
		echo "</div></div>";
	}
?>
<?php footer(); ?>
</body>
</html>