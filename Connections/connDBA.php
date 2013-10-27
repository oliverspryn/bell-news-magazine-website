<?php
session_start();
ob_start();
error_reporting(0);

/* Begin core functions */
	//Root address for entire site
	$root = "http://" . $_SERVER['HTTP_HOST'] . "/";
	$strippedRoot = str_replace("http://" . $_SERVER['HTTP_HOST'], "", $root);

	//Database connection
	$databaseName = "pavcsbel_bellmagazine";
	$connDBA = mysql_connect("localhost", "pavcsbel_spryno", "Oliver99");
	$dbSelect = mysql_select_db($databaseName, $connDBA);
	
	//Define time zone
	$timeZoneGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
	$timeZone = mysql_fetch_array($timeZoneGrabber);
	date_default_timezone_set($timeZone['timeZone']);
	
	//Grab the user's data
	function userData() {
		global $connDBA;
		
		$userInfoGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$_SESSION['MM_Username']}'", $connDBA);
		$userInfo = mysql_fetch_array($userInfoGrabber);
		return $userInfo;
	}
	
	//Check to see if a user is logged in 
	function loggedIn() {
		if (isset($_SESSION['MM_Username']) && isset($_SESSION['MM_UserGroup'])) {
			return true;
		} else {
			return false;
		}
	}
	
	if (loggedIn()) {
		$userData = userData();
	} else {
		$userData = "";
	}
/* End core functions */	

/* Begin messages functions */
	//Alerts
	function alert($errorContent = NULL) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"toolBar noPadding toolBarAlert\">$errorContent</div></div></p><br />";
	}
	
	//Response for errors
	function errorMessage($errorContent = NULL) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"error\">$errorContent</div></div></p><br />";
	}

	//Response for secuess
	function successMessage($successContent) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"success\">$successContent</div></div></p><br />";
	}
	
	//A centrally located div
	function centerDiv($divContent) {
		echo "<p><div align=\"center\">" . $divContent . "</div></p><br />";
	}
/* End messages functions */

/* Begin site layout functions */	
	//Call site title
	function title($title) {
		global $connDBA;
		global $root;
		
		$strippedTitle = stripslashes($title);
		$siteNameGrabber = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		$siteName = stripslashes($siteNameGrabber['siteName']);
		$value = "<title>{$siteName} | {$strippedTitle}</title>";
		echo $value;
	}
	
	//Include a stylesheet and basic javascripts
	function headers() {
		global $connDBA;
		global $root;
		
		$siteStyleGrabber = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		$siteStyle = $siteStyleGrabber['style'];
		
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "styles/common/universal.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "styles/themes/" . $siteStyle . "\" /><link type=\"";
		
		$iconExtensionGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
		$iconExtension = mysql_fetch_array($iconExtensionGrabber);
		
		switch ($iconExtension['iconType']) {
			case "ico" : echo "image/x-icon"; break;
			case "jpg" : echo "image/jpeg"; break;
			case "gif" : echo "image/gif"; break;
			case "png" : echo "image/png"; break;
		}
		
		echo "\" rel=\"shortcut icon\" href=\"" . $root . "images/icon." . $iconExtension['iconType'] . "\" />";
		
		$requestURL = $_SERVER['REQUEST_URI'];
		if (strstr($requestURL, "enable_javascript.php")) {
			//Do nothing
		} else {
			echo "<noscript><meta http-equiv=\"refresh\" content=\"0; url=" . $root . "enable_javascript.php\"></noscript>";
		}
		$requestURL = $_SERVER['REQUEST_URI'];
		if (strstr($requestURL, "enable_javascript.php")) {
			echo "<script type=\"text/javascript\">window.location = \"index.php\"</script>
";
		}
	}
	
	//Include user login status
	function loginStatus() {
		global $connDBA;
		global $root;
			
		if (isset ($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			$nameGrabber = mysql_query ("SELECT * FROM users WHERE userName = '{$userName}'", $connDBA);
			$name = mysql_fetch_array($nameGrabber);
			$firstName = $name['firstName'];
			$lastName = $name['lastName'];
			
			switch($_SESSION['MM_UserGroup']) {
				case "User" : $profileURL = "<a href=\"" . $root . "admin/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Administrator" : $profileURL = "<a href=\"" . $root . "admin/users/profile.php?id=" . $name['id'] . "\">"; break;
			}
			
			echo "You are logged in as " . $profileURL . $firstName . " " . $lastName . "</a> <a href=\"" . $root . "logout.php\">(Logout)</a>";
		}
	}
	
	//Include the logo
	function logo() {
		global $connDBA;
		global $root;
		
		$imageInfoGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);	
		$imageInfo = mysql_fetch_array($imageInfoGrabber);
	
		echo "<div style=\"padding-top:" . $imageInfo['paddingTop'] . "px; padding-bottom:" . $imageInfo['paddingBottom'] . "px; padding-left:" .  $imageInfo['paddingLeft'] . "px; padding-right:" . $imageInfo['paddingRight'] . "px;\">";
		if (isset ($_SESSION['MM_UserGroup'])) {
			 echo "<a href=\"" . $root . "admin/index.php\">";
		} else {
			echo "<a href=\"" . $root . "index.php\">";
		}
		
		echo "<img src=\"" . "" . $root . "images/banner.png\"";
		if ($imageInfo['auto'] !== "on") {
			echo " width=\"" . $imageInfo['width'] . "\" height=\"" . $imageInfo['height'] . "\"";
		} 
		
		echo " alt=\"" . $imageInfo['siteName'] . "\" title=\"" . $imageInfo['siteName'] . "\"></a></div>";
	}
	
	//Meta information
	function meta($description = "", $additionalKeywords = "") {
		global $connDBA;
		global $root;
		
		$meta = mysql_fetch_array(mysql_query ("SELECT * FROM siteprofiles", $connDBA));
	
		echo "<meta name=\"author\" content=\"" . stripslashes($meta['author']) . "\" />
		<meta http-equiv=\"content-language\" content=\"" . stripslashes($meta['language']) . "\" />
		<meta name=\"copyright\" content=\"" . stripslashes($meta['copyright']) . "\" />";
		
		if ($description == "") {
			echo "<meta name=\"description\" content=\"" . stripslashes($meta['description']) . "\" />";
		} else {
			echo "<meta name=\"description\" content=\"" . stripslashes(strip_tags($description)) . "\" />";
		}
		
		if ($additionalKeywords == "") {
			echo "<meta name=\"keywords\" content=\"" . stripslashes($meta['meta']) . "\" />";
		} else {
			echo "<meta name=\"keywords\" content=\"" . stripslashes($meta['meta']) . ", " . $additionalKeywords . "\" />";
		}
			
		echo "<meta name=\"generator\" content=\"Ensigma Pro\" />
		<meta name=\"robots\" content=\"index,follow\">";
	}

	//Include a navigation bar
	function navigation($URL, $linkBack) {
		global $connDBA;
		global $root;
		
		$requestURL = $_SERVER['REQUEST_URI'];
		echo "<div id=\"navbar_bg\"><div class=\"navbar clearfix\"><div class=\"breadcrumb\"><div class=\"menu\"><ul class=\"headerNavigation\">";
		
		switch ($URL) {
		//If this is the public website navigation bar
			case "public" :
				$pageData = mysql_query("SELECT * FROM pages WHERE visible = 'on' AND `published` != '0' AND `parentPage` = '0' ORDER BY position ASC", $connDBA);	
				$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE visible = 'on' AND `published` != '0' ORDER BY position DESC LIMIT 1", $connDBA));
				$count = 1;
				
				if (isset ($_GET['page']) && !empty($_GET['page'])) {
					$currentPage = $_GET['page'];
				}
				
				while ($pageInfoPrep = mysql_fetch_array($pageData)) {
					$pageInfo = unserialize($pageInfoPrep['content' . $pageInfoPrep['display']]);
					
					if (isset ($currentPage)) {
						if ($currentPage == $pageInfoPrep['id']) {
							if ($count++ != "1") {
								echo "<li><a class=\"topCurrentPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							} else {
								echo "<li><a class=\"topCurrentPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							}
						} else {
							if ($count++ != "1") {
								echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							} else {
								echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							}
						}
					} else {
						if ($count++ == "1") {
							if ($pageInfoPrep['position'] == "1") {
								$currentPage = $pageInfoPrep['id'];
								
								echo "<li><a class=\"topCurrentPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							} else {
								echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							}
						} else {
							if ($pageInfoPrep['position'] != "1") {
								$currentPage = $pageInfoPrep['id'];
								
								echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							} else {
								echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a></li>";
							} 
						}
					}
				}
				
				break;
				
		//If this is the administrator navigation bar
			case "administrator" : 
				echo "<li><a class=\"";
				if (!strstr($requestURL, "admin/collaboration") && !strstr($requestURL, "admin/pages") && !strstr($requestURL, "admin/users") && !strstr($requestURL, "admin/cms") && !strstr($requestURL, "admin/statistics")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "admin/index.php";
				echo "\">Home</a></li>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "admin/collaboration")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "admin/collaboration/index.php";
				echo "\">Collaboration</a></li>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "admin/pages")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "admin/pages/index.php";
				echo "\">Staff Pages</a></li>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "admin/users")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "admin/users/index.php";
				echo "\">Users</a></li>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "admin/cms") || strstr($requestURL, "admin/statistics")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "admin/cms/index.php";
				echo "\">Public Website</a></li>";
				
				echo "<li><a class=\"topPageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a></li>";
				break;
		
	//If this is the user navigation bar
			case "user" : 
				echo "<li><a class=\"";
				if (!strstr($requestURL, "admin/pages") && !strstr($requestURL, "admin/collaboration") && !strstr($requestURL, "admin/cms") && !strstr($requestURL, "admin/statistics")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
				echo "\" href=\"";
				echo $root . "admin/index.php";
				echo "\">Home</a></li>";
				
				if (privileges("sendEmail") == "true") {
					echo "<li><a class=\"";
					if (strstr($requestURL, "admin/collaboration")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "admin/collaboration/index.php";
					echo "\">Collaboration</a></li>";
				}
				
				if (privileges("viewStaffPage") == "true") {
					echo "<li><a class=\"";
					if (strstr($requestURL, "admin/pages")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "admin/pages/index.php";
					echo "\">Staff Pages</a></li>";
				}
				
				if (privileges("createPage") == "true" || privileges("editPage") == "true" || privileges("deletePage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true") {
					echo "<li><a class=\"";
					if (strstr($requestURL, "admin/cms") || strstr($requestURL, "admin/statistics")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "admin/cms/index.php";
					echo "\">Public Website</a></li>";
				}
				
				echo "<li><a class=\"topPageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a></li>";
				break;
		}
		
		echo "</ul></div></div></div></div>";
		
		if ($URL == "public" && $linkBack == true) {
			$backTrack = array();
			
			function backTrack($id, &$backTrack) {
				if (exist("pages", "id", $id) && $id != "0") {
					 $currentPage = query("SELECT * FROM `pages` WHERE `id` = '{$id}'");
					 $nextPage = query("SELECT * FROM `pages` WHERE `id` = '{$currentPage['parentPage']}'");
					 $title = unserialize($nextPage['content' . $nextPage['display']]);
					 $backTrack[] = array($title['title'], $nextPage['id']);
					 backTrack($nextPage['id'], $backTrack);
				}
			}
			
			backTrack($currentPage, $backTrack);
			
			for($count = sizeof($backTrack) - 1; $count >= 0; $count--) {
				if (!empty($backTrack[$count]['1']) && !empty($backTrack[$count]['0'])) {
					$return .= "<a href=\"index.php?page=" . $backTrack[$count]['1'] . "\">" . prepare($backTrack[$count]['0']) . "</a> &#9658 ";
				}
			}
			
			$currentTitle = query("SELECT * FROM `pages` WHERE `id` = '{$currentPage}'");
			$title = unserialize($currentTitle['content' . $currentTitle['display']]);
			$returnPrep = trim($return, " &#9658 ");
			
			if (!empty($returnPrep)) {
				echo "<div style=\"padding-left:12px\"><h4>" . $returnPrep . " &#9658 " . $title['title'] . "</h4></div>";
			}
		}
		
	//Display back tracking
		if ($URL != "public") {
			$URL = $_SERVER['PHP_SELF'];
			$backTrack = "<div style=\"padding-left:12px;\" align=\"left\">";
			
			if (strstr($URL, $strippedRoot . "admin/index.php")) {
				$backTrack .= "Home";
			}
			
			//Collaboration
			if (strstr($URL, $strippedRoot . "admin/collaboration/index.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 Collaboration";
			}
			
			if (strstr($URL, $strippedRoot . "admin/collaboration/manage_announcement.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/collaboration/index.php\">Collaboration</a> &#9658 Manage Announcement";
			}
			
			if (strstr($URL, $strippedRoot . "admin/collaboration/manage_agenda.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/collaboration/index.php\">Collaboration</a> &#9658 Manage Agenda";
			}
			
			if (strstr($URL, $strippedRoot . "admin/collaboration/manage_files.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/collaboration/index.php\">Collaboration</a> &#9658 Manage File Share";
			}
			
			if (strstr($URL, $strippedRoot . "admin/collaboration/manage_poll.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/collaboration/index.php\">Collaboration</a> &#9658 Manage Polling";
			}
			
			if (strstr($URL, $strippedRoot . "admin/collaboration/manage_forum.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/collaboration/index.php\">Collaboration</a> &#9658 Manage Forum";
			}
			
			if (strstr($URL, $strippedRoot . "admin/collaboration/send_email.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/collaboration/index.php\">Collaboration</a> &#9658 Send Mass Email";
			}
			
			//Staff Pages
			if (strstr($URL, $strippedRoot . "admin/pages/index.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 Staff Pages";
			}
			
			if (strstr($URL, $strippedRoot . "admin/pages/manage_page.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/pages/index.php\">Staff Pages</a> &#9658 Manage Page";
			}
			
			if (strstr($URL, $strippedRoot . "admin/pages/page.php")) {
				if (isset($_GET['page']) && !empty($_GET['page'])) {
					$id = $_GET['page'];
				} else {
					$pageData = query("SELECT * FROM `staffpages` WHERE `position` = '1'");
					$id = $pageData['id'];
				}
				
				$pageNameGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `id` = '{$id}'", $connDBA);
				$pageNamePrep = mysql_fetch_array($pageNameGrabber);
				$pageName = unserialize($pageNamePrep['content' . $pageNamePrep['display']]);
				
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/pages/index.php\">Staff Pages</a> &#9658 " . stripslashes($pageName['title']);
			}
			
			//Users
			if (strstr($URL, $strippedRoot . "admin/users/index.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 Users";
			}
			
			if (strstr($URL, $strippedRoot . "admin/users/manage_user.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/users/index.php\">Users</a> &#9658 Manage User";
			}
			
			if (strstr($URL, $strippedRoot . "admin/users/privileges.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/users/index.php\">Users</a> &#9658 User Privileges";
			}
			
			if (strstr($URL, $strippedRoot . "admin/users/failed.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/users/index.php\">Users</a> &#9658 Failed Logins";
			}
			
			if (strstr($URL, $strippedRoot . "admin/users/search.php")) {
				if (!isset($_GET['keywords'])) {
					$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/users/index.php\">Users</a> &#9658 Search for Users";
				} else {
					$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/users/index.php\">Users</a> &#9658 <a href=\"" . $root . "admin/users/search.php\">Search for Users</a> &#9658 Search Results";
				}
			}
			
			//CMS
			//Public Website
			if (strstr($URL, $strippedRoot . "admin/cms/index.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 Public Website";
			}
			
			if (strstr($URL, $strippedRoot . "admin/cms/manage_page.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 Manage Page";
			}
			
			if (strstr($URL, $strippedRoot . "admin/cms/site_settings.php")) {
				if (!isset($_GET['type'])) {
					$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 Site Settings";
				} else {
					switch($_GET['type']) {
						case "logo" : 
							$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/site_settings.php\">Site Settings</a> &#9658 Site Logo";
							break;
							
						case "icon" : 
							$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/site_settings.php\">Site Settings</a> &#9658 Browser Icon";
							break;
							
						case "meta" : 
							$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/site_settings.php\">Site Settings</a> &#9658 Site Information";
							break;
							
						case "theme" : 
							$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/site_settings.php\">Site Settings</a> &#9658 Site Theme";
							break;
							
						case "security" : 
							$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/site_settings.php\">Site Settings</a> &#9658 Security";
							break;
					}
				}
			}
			
			//Sidebar
			if (strstr($URL, $strippedRoot . "admin/cms/sidebar/index.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 Sidebar";
			}
			
			if (strstr($URL, $strippedRoot . "admin/cms/sidebar/manage_sidebar.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/sidebar/index.php\">Sidebar</a> &#9658 Manage Box";
			}
			
			if (strstr($URL, $strippedRoot . "admin/cms/sidebar/sidebar_settings.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/sidebar/index.php\">Sidebar</a> &#9658 Sidebar Settings";
			}
			
			//External Content
			if (strstr($URL, $strippedRoot . "admin/cms/external/index.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 External Content";
			}
			
			if (strstr($URL, $strippedRoot . "admin/cms/external/manage_external.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 <a href=\"" . $root . "admin/cms/external/index.php\">External Content</a> &#9658 Manage Tab";
			}
			
			//Statistics
			if (strstr($URL, $strippedRoot . "admin/statistics/index.php")) {
				$backTrack .= "<a href=\"" . $root . "admin/index.php\">Home</a> &#9658 <a href=\"" . $root . "admin/cms/index.php\">Public Website</a> &#9658 Statistics";
			}
			
			$backTrack .= "</div>";
			
			echo "<h4>" . $backTrack . "</h4>";
		}
	}
	
	//Include all top-page items
	function topPage($URL = "false", $linkBack = false) {
		global $connDBA;
		global $root;
		
		$siteName = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		
		if ($URL == "false") {
			if (isset($_SESSION['MM_UserGroup'])) {
				switch ($_SESSION['MM_UserGroup']) {
					case "User" : $URL = "user"; break;
					case "Administrator" : $URL = "administrator"; break;
				}
			} else {
				$URL = "public";
			}
		}
		
		echo "<div id=\"page\">
		<div id=\"header_bg\">
		<div id=\"header\" class=\"clearfix\"><h1 class=\"headermain\">";
		echo $siteName['siteName'];
		echo "</h1><div class=\"headermenu\"><div class=\"logininfo\">";
		loginStatus();
		echo "</div></div></div><div id=\"banner_bg\"><div id=\"banner\">";
		logo();
		echo "</div></div>";
		navigation($URL, $linkBack);
		echo "</div>";
		echo "<div id=\"content\"><div class=\"box generalboxcontent boxaligncenter\">";		
	}
	
	//Include a footer
	function footer($URL = "false") {
		global $connDBA;
		global $root;
		$requestURL = $_SERVER['REQUEST_URI'];
		
		echo "</div></div><div id=\"footer\"><div>&nbsp;</div><div class=\"breadcrumb\">";
		
		if ($URL == "false") {
			if (isset($_SESSION['MM_UserGroup'])) {
				switch ($_SESSION['MM_UserGroup']) {
					case "User" : $URL = "user"; break;
					case "Administrator" : $URL = "administrator"; break;
				}
			} else {
				$URL = "public";
			}
		}
		
		switch ($URL) {
		//If this is the public website footer bar
			case "public" :
				$pageData = mysql_query("SELECT * FROM pages WHERE visible = 'on' AND `published` != '0' AND `parentPage` = '0' ORDER BY position ASC", $connDBA);	
				$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE visible = 'on' AND `published` != '0' ORDER BY position DESC LIMIT 1", $connDBA));
				$count = 1;
				
				if (isset ($_GET['page']) && !empty($_GET['page'])) {
					$currentPage = $_GET['page'];
				}
			
				while ($pageInfoPrep = mysql_fetch_array($pageData)) {
					$pageInfo = unserialize($pageInfoPrep['content' . $pageInfoPrep['display']]);
					
					if (isset ($currentPage)) {
						if ($currentPage == $pageInfoPrep['id']) {
							if ($count++ != "1") {
								echo "<span class=\"arrow sep\">&bull;</span><a class=\"bottomCurrentPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"bottomCurrentPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
						} else {
							if ($count++ != "1") {
								echo "<span class=\"arrow sep\">&bull;</span><a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
						}
					} else {
						if ($count++ == "1") {
							if ($pageInfoPrep['position'] == "1") {
								echo "<a class=\"bottomCurrentPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
						} else {
							if ($count++ != "1") {
								echo "<span class=\"arrow sep\">&bull;</span><a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfoPrep['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
						}
					}
				}
				
				break;
			
		//If this is the administrator footer bar
			case "administrator" : 
				echo "<a class=\"";
				if (!strstr($requestURL, "admin/collaboration") && !strstr($requestURL, "admin/pages") && !strstr($requestURL, "admin/users") && !strstr($requestURL, "admin/cms") && !strstr($requestURL, "admin/statistics")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "admin/index.php";
				echo "\">Home</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "admin/collaboration")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "admin/collaboration/index.php";
				echo "\">Collaboration</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "admin/pages")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "admin/pages/index.php";
				echo "\">Staff Pages</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "admin/users")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "admin/users/index.php";
				echo "\">Users</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "admin/cms") || strstr($requestURL, "admin/statistics")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "admin/cms/index.php";
				echo "\">Public Website</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"bottomPageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a>";
				break;
				
		//If this is the user footer bar
			case "user" : 
				echo "<a class=\"";
				if (!strstr($requestURL, "admin/collaboration") && !strstr($requestURL, "admin/pages") && !strstr($requestURL, "admin/cms") && !strstr($requestURL, "admin/statistics")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
				echo "\" href=\"";
				echo $root . "admin/index.php";
				echo "\">Home</a><span class=\"arrow sep\">&bull;</span>";
				
				if (privileges("sendEmail") == "true") {
					echo "<a class=\"";
					if (strstr($requestURL, "admin/collaboration")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
					echo "\" href=\"";
					echo $root . "admin/collaboration/index.php";
					echo "\">Collaboration</a><span class=\"arrow sep\">&bull;</span>";
				}
				
				if (privileges("viewStaffPage") == "true") {
					echo "<a class=\"";
					if (strstr($requestURL, "admin/pages")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
					echo "\" href=\"";
					echo $root . "admin/pages/index.php";
					echo "\">Staff Pages</a><span class=\"arrow sep\">&bull;</span>";
				}
				
				if (privileges("createPage") == "true" || privileges("editPage") == "true" || privileges("deletePage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true") {
					echo "<a class=\"";
					if (strstr($requestURL, "admin/cms") || strstr($requestURL, "admin/statistics")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
					echo "\" href=\"";
					echo $root . "admin/cms/index.php";
					echo "\">Public Website</a><span class=\"arrow sep\">&bull;</span>";
				}
				
				echo "<a class=\"bottomPageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a>";
				break;
		}
		
		echo "</div><div class=\"footer\">";
		
		$footerGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);	
		$footer= mysql_fetch_array($footerGrabber);
		
		echo stripslashes($footer['siteFooter']) . "</div></div></div>";
		echo "<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-11478926-14']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>";
		activity("true");
	}
/* End site layout functions */
	
/* Begin login management functions */
//A function to encrypt a string
	function encrypt($string) {
		$search = str_split(" ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890`~!@#$%^&*()-_=+[{]}|;:',<.>/?\\\"");
		$replace = str_split(" B)3Z/~8tr;`y%oJ{X(Mx}2kDc=7<AaSCzNh&5n\"[Il!@gRP]\\$mwb?#4p*0eK6QLHdEv^,Uj:-|9O'qsufY>1iFTGVW.+_");
		$encrypt = "";
		
		foreach(str_split($string) as $segement) {
			if ($segement == "") {
				$encrypt .= " ";
			} else {
				$key = array_keys($search, $segement);
				$encrypt .= $replace[$key['0']];
			}
		}
		
		return base64_encode(gzdeflate($encrypt));
	}
	
//A function to decrypt a string
	function decrypt($string) {
		$search = str_split(" B)3Z/~8tr;`y%oJ{X(Mx}2kDc=7<AaSCzNh&5n\"[Il!@gRP]\\$mwb?#4p*0eK6QLHdEv^,Uj:-|9O'qsufY>1iFTGVW.+_");
		$replace = str_split(" ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890`~!@#$%^&*()-_=+[{]}|;:',<.>/?\\\"");
		$decrypt = "";
		
		foreach(str_split(gzinflate(base64_decode($string))) as $segement) {
			if ($segement == "") {
				$decrypt .= " ";
			} else {
				$key = array_keys($search, $segement);
				$decrypt .= $replace[$key['0']];
			}
		}
		
		return $decrypt;
	}
	
	//Login a user
	function login() {
		global $connDBA;
		global $root;
		
		if (isset ($_SESSION['MM_Username'])) {
			$requestedURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$homePageCheck = str_replace($root, "", $requestedURL);
			
			if ($homePageCheck !== "index.php") {
				$userRole = $_SESSION['MM_UserGroup'];
				
				header ("Location: " . $root . "admin/index.php");
				exit;
			}
		} else {
			if (!function_exists("GetSQLValueString")) {
				function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
		  			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
					$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		
					switch ($theType) {
					  case "text" : $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;    
					  case "long":
					  case "int": $theValue = ($theValue != "") ? intval($theValue) : "NULL"; break;
					  case "double": $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL"; break;
					  case "date": $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;
					  case "defined": $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue; break;
					}
					
					return $theValue;
				}
			}
		
			$loginFormAction = $_SERVER['PHP_SELF'];
			
			if (isset($_GET['accesscheck'])) {
				$_SESSION['PrevUrl'] = urlencode(urldecode($_GET['accesscheck']));
			}
			
			if (isset($_POST['username'])) {
				$loginUsername=$_POST['username'];
				$password=encrypt($_POST['password']);
				$MM_fldUserAuthorization = "role";
				
				$userRoleGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$loginUsername}' AND `passWord` = '{$password}'");
				
				if ($userRole = mysql_fetch_array($userRoleGrabber)) {
					$success = "";
					$failure = "";
					
					if (isset($_GET['accesscheck'])) {
						$success .= "http://" . $_SERVER['HTTP_HOST'] . urldecode($_GET['accesscheck']);
					} else {
						$success .= $root . "admin/index.php";
					}
					
					$IPAddress = $_SERVER['REMOTE_ADDR'];
					$timeStamp = strtotime("-1 day");
					
					if (mysql_query("SELECT * FROM `failedlogins` WHERE `IPAddress` = '{$IPAddress}' AND `timeStamp` > '{$timeStamp}'", $connDBA)) {
						$numberPrep = mysql_query("SELECT * FROM `failedlogins` WHERE `IPAddress` = '{$IPAddress}' AND `timeStamp` > '{$timeStamp}'", $connDBA);
						$number = mysql_num_rows($numberPrep);
						$secuirtyGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
						$secuirty = mysql_fetch_array($secuirtyGrabber);
						
						if (intval($secuirty['failedLogins']) <= $number) {
							if (isset($_GET['accesscheck'])) {
								redirect($root . "login.php?expired=true&accesscheck=" . urlencode(urldecode($_GET['accesscheck'])));
							} else {
								redirect($root . "login.php?expired=true");
							}
						}
					}
				} else {
					$success = "";
					$failure = $root . "login.php?alert=true";	
					$timeStamp = strtotime("now");
					$IPAddress = $_SERVER['REMOTE_ADDR'];
					
					mysql_query("INSERT INTO `failedlogins` (
								`id`, `timeStamp`, `IPAddress`, `userName`
								) VALUES (
								NULL, '{$timeStamp}', '{$IPAddress}', '{$loginUsername}'
								)", $connDBA);
								
					$IPAddress = $_SERVER['REMOTE_ADDR'];
					$timeStamp = strtotime("-1 day");
					
					if (mysql_query("SELECT * FROM `failedlogins` WHERE `IPAddress` = '{$IPAddress}' AND `timeStamp` > '{$timeStamp}'", $connDBA)) {
						$numberPrep = mysql_query("SELECT * FROM `failedlogins` WHERE `IPAddress` = '{$IPAddress}' AND `timeStamp` > '{$timeStamp}'", $connDBA);
						$number = mysql_num_rows($numberPrep);
						$secuirtyGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
						$secuirty = mysql_fetch_array($secuirtyGrabber);
						
						if (intval($secuirty['failedLogins']) <= $number) {
							if (isset($_GET['accesscheck'])) {
								redirect($root . "login.php?expired=true&accesscheck=" . urlencode(urldecode($_GET['accesscheck'])));
							} else {
								redirect($root . "login.php?expired=true");
							}
							
							exit;
						} else {
							if (isset($_GET['accesscheck'])) {
								$failure .= "&remaining=" . sprintf(intval($secuirty['failedLogins']) - $number) . "&accesscheck=" . urlencode(urldecode($_GET['accesscheck']));
							} else {
								$failure .= "&remaining=" . sprintf(intval($secuirty['failedLogins']) - $number);
							}
						}
					}
				}
			  
				$MM_redirectLoginSuccess = $success;
				$MM_redirectLoginFailed = $failure;
				$MM_redirecttoReferrer = false;
				  
				$LoginRS__query=sprintf("SELECT userName, passWord, role FROM users WHERE userName=%s AND passWord=%s",
				GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
				 
				$LoginRS = mysql_query($LoginRS__query, $connDBA) or die(mysql_error());
				$loginFoundUser = mysql_num_rows($LoginRS);
				
				if ($loginFoundUser) {
					$loginStrGroup  = mysql_result($LoginRS,0,'role');
					
					$_SESSION['MM_Username'] = $loginUsername;
					$_SESSION['MM_UserGroup'] = $loginStrGroup;	
					
					$userIDGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$loginUsername}' AND `passWord` = '{$password}' LIMIT 1");
					$userID = mysql_fetch_array($userIDGrabber);
					setcookie("userStatus", $userID['sysID'], time()+1000000000); 
					
					$cookie = $userID['sysID'];
					mysql_query("UPDATE `users` SET `active` = '1' WHERE `sysID` = '{$cookie}'", $connDBA);
					
			  
				  if (isset($_SESSION['PrevUrl']) && false) {
					  $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
				  }
				  
				  header ("Location: " . $success);
				  exit;
				} else {
				  header("Location: " . $failure);
				  exit;
				}
			}
		}
	}
	
	//Maintain login status
	function loginCheck($role) {
		global $connDBA;
		global $root;
		
		$MM_authorizedUsers = $role;
		$MM_donotCheckaccess = "false";
		
		function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
		  $isValid = False; 
		  
		  if (!empty($UserName)) { 
			$arrUsers = Explode(",", $strUsers); 
			$arrGroups = Explode(",", $strGroups); 
			if (in_array($UserName, $arrUsers)) { 
			  $isValid = true; 
			} 
			
			if (in_array($UserGroup, $arrGroups)) { 
			  $isValid = true; 
			} 
			if (($strUsers == "") && false) { 
			  $isValid = true; 
			} 
		  } 
		  return $isValid; 
		}
		
		//$MM_restrictGoTo = $root . "login.php";
		if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) { 
		  /*$MM_qsChar = "?";
		  $MM_referrer = $_SERVER['REQUEST_URI'];
		  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
		  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
		  $MM_referrer .= "?" . $QUERY_STRING;
		  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);*/
		  header("Location: " . $root . "admin/index.php"); 
		  exit;
		}
	}
/* End login management functions */
	
/* Begin page scripting functions */
	//Include the tiny_mce simple widget
	function tinyMCESimple () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "tiny_mce/plugins/AtD/editor_plugin.js\"></script><script type=\"text/javascript\" src=\"" . $root . "javascripts/common/tiny_mce_simple.php\"></script>";
	}
	
	//Include the tiny_mce advanced widget
	function tinyMCEAdvanced () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "tiny_mce/plugins/AtD/editor_plugin.js\"></script><script type=\"text/javascript\" src=\"" . $root . "javascripts/common/tiny_mce_advanced.php\"></script>";
	}
	
	//Include a form validator
	function validate () {
		global $connDBA;
		global $root;
		
		echo "<link rel=\"stylesheet\" href=\"" . $root . "styles/common/validatorStyle.css\" type=\"text/css\">";
		echo "<script src=\"" . $root . "javascripts/validation/validatorCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/validatorOptions.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/runValidator.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/formErrors.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a life updater script
	function liveSubmit() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/liveSubmit/submitterCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/liveSubmit/runSubmitter.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the custom checkbox script
	function customCheckbox($type) {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/customCheckbox/checkboxCore.js\" type=\"text/javascript\"></script>";
		if ($type == "checkbox") {
			echo "<script src=\"" . $root . "javascripts/customCheckbox/runCheckbox.js\" type=\"text/javascript\"></script>";
		} elseif ($type == "visible") {
			echo "<script src=\"" . $root . "javascripts/customCheckbox/runVisible.js\" type=\"text/javascript\"></script>";
		}
	}
	
	//Insert live error script
	function liveError() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/liveError/errorCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "javascripts/liveError/runNameError.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the tiny_mce advanced widget
	function menuBar () {
		global $connDBA;
		global $root;
		
		echo "<link rel=\"stylesheet\" href=\"" . $root . "styles/menuBar/elementAdjustment.css\" type=\"text/css\"><link rel=\"stylesheet\" href=\"" . $root . "styles/menuBar/cssCore.css\" type=\"text/css\"><!--[if lt IE 7]>
<link rel=\"stylesheet\" href=\"" . $root . "styles/menuBar/menuIE6.css\" type=\"text/css\"><![endif]-->";
	}
	
	//Include the body class
	function bodyClass() {
		global $connDBA;
		global $root;
		
		echo " class=\"theme course-1 dir-ltr lang-en_utf8\"";
	}

	//Include a tooltip	
	function tooltip() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/common/tooltip.js\" type=\"text/javascript\"></script>";
	}
/* End page scripting functions */
	
/* Begin form visual functions */		
	//Insert an error window, which will report errors live
	function errorWindow($type, $message, $phpGet = false, $phpError = false, $liveError = false) {
		global $connDBA;
		global $root;
		
		if ($type == "database") {
			if ($liveError == true) {
				if (isset($_GET[$phpGet]) && $_GET[$phpGet] == $phpError) {
						echo "<div align=\"center\" id=\"errorWindow\">" . errorMessage($message) . "</div>";
				} else {
					echo "<div align=\"center\" id=\"errorWindow\"><p>&nbsp;</p></div>";
				}
			} else {
				if ($_GET[$phpGet] == $phpError) {
						echo errorMessage($message);
				} else {
					echo "<p>&nbsp;</p>";
				}
			}
		}
		
		if ($type == "extension") {
			echo "<div align=\"center\"><div id=\"errorWindow\" class=\"error\" style=\"display:none;\">" .$message . "</div></div>";
		}
	}
	
	//Submit a form and toggle the tinyMCE to save its content
	function submit($id, $value) {
		global $connDBA;
		global $root;
		
		echo "<input type=\"submit\" name=\"" . $id . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"tinyMCE.triggerSave();\" />";
	}
	
	//Insert a form errors box, which will report any form errors on submit
	function formErrors () {
		global $connDBA;
		global $root;
		
		echo "<div id=\"errorBox\" style=\"display:none;\">Some fields are incomplete, please scroll up to correct them.</div><div id=\"progress\" style=\"display:none;\"><p><span class=\"require\">Uploading in progress... </span><img src=\"" . $root . "images/common/loading.gif\" alt=\"Uploading\" width=\"16\" height=\"16\" /></p></div>";
	}
/* End form visual functions */
	
/* Begin system functions */
	//Generate a random string
	function randomValue($length = 8, $seeds = 'alphanum') {
		global $connDBA;
		global $root;
		
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] = '0123456789';
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['hexidec'] = '0123456789abcdef';
		
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}
		
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		$string = '';
		$seeds_count = strlen($seeds);
		
		for ($i = 0; $length > $i; $i++) {
			$string .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $string;
	}
	
	//A function to limit the length of the directions
	function commentTrim ($length, $value, $imagesOnly = false) {
		global $connDBA;
		global $root;
		
		$commentsStrip = preg_replace("/<img[^>]+\>/i", "(image)", $value);
	   
		if ($imagesOnly == false) {
		   $comments = strip_tags($commentsStrip);
		   $maxLength = $length;
		   $countValue = html_entity_decode($comments);
		   if (strlen($countValue) <= $maxLength) {
			  return stripslashes($comments);
		   }
		   
		   $shortenedValue = substr($countValue, 0, $maxLength - 3) . "...";
		   return stripslashes($shortenedValue);
		} else {
		   return stripslashes($commentsStrip);  
		}
	}
	
	//A function to check the extension of a file
	function extension ($targetFile) {
		$entension = explode(".", $targetFile);
		$value = count($entension)-1;
		$entension = $entension[$value];
		$output = strtolower($entension);
		
		if($output == "php" || $output == "php3" || $output == "php4" || $output == "php5" || $output == "tpl" || $output == "php-dist" || $output == "phtml" || $output == "htaccess" || $output == "htpassword") {
			die(errorMessage("Your file is a potential threat to this system, in which case, it was not uploaded"));
			return false;
			exit;
		} else {
			return $output;
		}
	}
	
	//A function to delete a folder and all of its contents
	function deleteAll($directory, $empty = false) {
		if(substr($directory,-1) == "/") {
			$directory = substr($directory,0,-1);
		}
	
		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$directoryHandle = opendir($directory);
			
			while ($contents = readdir($directoryHandle)) {
				if($contents != '.' && $contents != '..') {
					$path = $directory . "/" . $contents;
					
					if(is_dir($path)) {
						deleteAll($path);
					} else {
						unlink($path);
					}
				}
			}
			
			closedir($directoryHandle);
	
			if($empty == false) {
				if(!rmdir($directory)) {
					return false;
				}
			}
			
			return true;
		}
	}
	
	//A function to check if a name exists
	function validateName($table, $column) {
		if (isset($_POST['validateValue']) && isset($_POST['validateId']) && isset($_POST['validateError'])) {
			$value = $_POST['validateValue'];
			$id = $_POST['validateId'];
			$message = $_POST['validateError'];
			
			$return = array();
			$return[0] = $id;
			$return[1] = $message;
		
			if (!query("SELECT * FROM `{$table}` WHERE `{$column}` = '{$value}'", "raw")) {
				$return[2] = "true";
				echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
			} else {
				$userInfo = userData();
				
				if (isset($_GET['id'])) {
					$data = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
					
					if ($data[$column] === $value) {
						$return[2] = "true";
						echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
					} else {
						$return[2] = "false";
						echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
					}
				} else {
					if ($table == "organizations" && !isset($_GET['id']) && $userInfo['organization'] != "0") {
						$data = query("SELECT * FROM `{$table}` WHERE `id` = '{$userInfo['organization']}'");
						
						if ($data[$column] === $value) {
							$return[2] = "true";
							echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
						} else {
							$return[2] = "false";
							echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
						}
					} else {
						$return[2] = "false";
						echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
					}
				}
			}
			
			exit;
		}
	}
	
	//A function to return the mime type of a file
	function getMimeType($filename, $debug = false) {
		if ( function_exists( 'finfo_open' ) && function_exists( 'finfo_file' ) && function_exists( 'finfo_close' ) ) {
			$fileinfo = finfo_open( FILEINFO_MIME );
			$mime_type = finfo_file( $fileinfo, $filename );
			finfo_close( $fileinfo );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'fileinfo' );
				return $mime_type;
			}
		}
		if ( function_exists( 'mime_content_type' ) ) {
			$mime_type = mime_content_type( $filename );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'mime_content_type' );
				return $mime_type;
			}
		}
		
		$mime_types = array(
			'ai'      => 'application/postscript',
			'aif'     => 'audio/x-aiff',
			'aifc'    => 'audio/x-aiff',
			'aiff'    => 'audio/x-aiff',
			'asc'     => 'text/plain',
			'asf'     => 'video/x-ms-asf',
			'asx'     => 'video/x-ms-asf',
			'au'      => 'audio/basic',
			'avi'     => 'video/x-msvideo',
			'bcpio'   => 'application/x-bcpio',
			'bin'     => 'application/octet-stream',
			'bmp'     => 'image/bmp',
			'bz2'     => 'application/x-bzip2',
			'cdf'     => 'application/x-netcdf',
			'chrt'    => 'application/x-kchart',
			'class'   => 'application/octet-stream',
			'cpio'    => 'application/x-cpio',
			'cpt'     => 'application/mac-compactpro',
			'csh'     => 'application/x-csh',
			'css'     => 'text/css',
			'dcr'     => 'application/x-director',
			'dir'     => 'application/x-director',
			'djv'     => 'image/vnd.djvu',
			'djvu'    => 'image/vnd.djvu',
			'dll'     => 'application/octet-stream',
			'dms'     => 'application/octet-stream',
			'dvi'     => 'application/x-dvi',
			'dxr'     => 'application/x-director',
			'eps'     => 'application/postscript',
			'etx'     => 'text/x-setext',
			'exe'     => 'application/octet-stream',
			'ez'      => 'application/andrew-inset',
			'flv'     => 'video/x-flv',
			'gif'     => 'image/gif',
			'gtar'    => 'application/x-gtar',
			'gz'      => 'application/x-gzip',
			'hdf'     => 'application/x-hdf',
			'hqx'     => 'application/mac-binhex40',
			'htm'     => 'text/html',
			'html'    => 'text/html',
			'ice'     => 'x-conference/x-cooltalk',
			'ief'     => 'image/ief',
			'iges'    => 'model/iges',
			'igs'     => 'model/iges',
			'img'     => 'application/octet-stream',
			'iso'     => 'application/octet-stream',
			'jad'     => 'text/vnd.sun.j2me.app-descriptor',
			'jar'     => 'application/x-java-archive',
			'jnlp'    => 'application/x-java-jnlp-file',
			'jpe'     => 'image/jpeg',
			'jpeg'    => 'image/jpeg',
			'jpg'     => 'image/jpeg',
			'js'      => 'application/x-javascript',
			'kar'     => 'audio/midi',
			'kil'     => 'application/x-killustrator',
			'kpr'     => 'application/x-kpresenter',
			'kpt'     => 'application/x-kpresenter',
			'ksp'     => 'application/x-kspread',
			'kwd'     => 'application/x-kword',
			'kwt'     => 'application/x-kword',
			'latex'   => 'application/x-latex',
			'lha'     => 'application/octet-stream',
			'lzh'     => 'application/octet-stream',
			'm3u'     => 'audio/x-mpegurl',
			'man'     => 'application/x-troff-man',
			'me'      => 'application/x-troff-me',
			'mesh'    => 'model/mesh',
			'mid'     => 'audio/midi',
			'midi'    => 'audio/midi',
			'mif'     => 'application/vnd.mif',
			'mov'     => 'video/quicktime',
			'movie'   => 'video/x-sgi-movie',
			'mp2'     => 'audio/mpeg',
			'mp3'     => 'audio/mpeg',
			'mp4'     => 'video/mp4',
			'mpe'     => 'video/mpeg',
			'mpeg'    => 'video/mpeg',
			'mpg'     => 'video/mpeg',
			'mpga'    => 'audio/mpeg',
			'ms'      => 'application/x-troff-ms',
			'msh'     => 'model/mesh',
			'mxu'     => 'video/vnd.mpegurl',
			'nc'      => 'application/x-netcdf',
			'odb'     => 'application/vnd.oasis.opendocument.database',
			'odc'     => 'application/vnd.oasis.opendocument.chart',
			'odf'     => 'application/vnd.oasis.opendocument.formula',
			'odg'     => 'application/vnd.oasis.opendocument.graphics',
			'odi'     => 'application/vnd.oasis.opendocument.image',
			'odm'     => 'application/vnd.oasis.opendocument.text-master',
			'odp'     => 'application/vnd.oasis.opendocument.presentation',
			'ods'     => 'application/vnd.oasis.opendocument.spreadsheet',
			'odt'     => 'application/vnd.oasis.opendocument.text',
			'ogg'     => 'application/ogg',
			'otg'     => 'application/vnd.oasis.opendocument.graphics-template',
			'oth'     => 'application/vnd.oasis.opendocument.text-web',
			'otp'     => 'application/vnd.oasis.opendocument.presentation-template',
			'ots'     => 'application/vnd.oasis.opendocument.spreadsheet-template',
			'ott'     => 'application/vnd.oasis.opendocument.text-template',
			'pbm'     => 'image/x-portable-bitmap',
			'pdb'     => 'chemical/x-pdb',
			'pdf'     => 'application/pdf',
			'pgm'     => 'image/x-portable-graymap',
			'pgn'     => 'application/x-chess-pgn',
			'png'     => 'image/png',
			'pnm'     => 'image/x-portable-anymap',
			'ppm'     => 'image/x-portable-pixmap',
			'ps'      => 'application/postscript',
			'qt'      => 'video/quicktime',
			'ra'      => 'audio/x-realaudio',
			'ram'     => 'audio/x-pn-realaudio',
			'ras'     => 'image/x-cmu-raster',
			'rgb'     => 'image/x-rgb',
			'rm'      => 'audio/x-pn-realaudio',
			'roff'    => 'application/x-troff',
			'rpm'     => 'application/x-rpm',
			'rtf'     => 'text/rtf',
			'rtx'     => 'text/richtext',
			'sgm'     => 'text/sgml',
			'sgml'    => 'text/sgml',
			'sh'      => 'application/x-sh',
			'shar'    => 'application/x-shar',
			'silo'    => 'model/mesh',
			'sis'     => 'application/vnd.symbian.install',
			'sit'     => 'application/x-stuffit',
			'skd'     => 'application/x-koan',
			'skm'     => 'application/x-koan',
			'skp'     => 'application/x-koan',
			'skt'     => 'application/x-koan',
			'smi'     => 'application/smil',
			'smil'    => 'application/smil',
			'snd'     => 'audio/basic',
			'so'      => 'application/octet-stream',
			'spl'     => 'application/x-futuresplash',
			'src'     => 'application/x-wais-source',
			'stc'     => 'application/vnd.sun.xml.calc.template',
			'std'     => 'application/vnd.sun.xml.draw.template',
			'sti'     => 'application/vnd.sun.xml.impress.template',
			'stw'     => 'application/vnd.sun.xml.writer.template',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc'  => 'application/x-sv4crc',
			'swf'     => 'application/x-shockwave-flash',
			'sxc'     => 'application/vnd.sun.xml.calc',
			'sxd'     => 'application/vnd.sun.xml.draw',
			'sxg'     => 'application/vnd.sun.xml.writer.global',
			'sxi'     => 'application/vnd.sun.xml.impress',
			'sxm'     => 'application/vnd.sun.xml.math',
			'sxw'     => 'application/vnd.sun.xml.writer',
			't'       => 'application/x-troff',
			'tar'     => 'application/x-tar',
			'tcl'     => 'application/x-tcl',
			'tex'     => 'application/x-tex',
			'texi'    => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tgz'     => 'application/x-gzip',
			'tif'     => 'image/tiff',
			'tiff'    => 'image/tiff',
			'torrent' => 'application/x-bittorrent',
			'tr'      => 'application/x-troff',
			'tsv'     => 'text/tab-separated-values',
			'txt'     => 'text/plain',
			'ustar'   => 'application/x-ustar',
			'vcd'     => 'application/x-cdlink',
			'vrml'    => 'model/vrml',
			'wav'     => 'audio/x-wav',
			'wax'     => 'audio/x-ms-wax',
			'wbmp'    => 'image/vnd.wap.wbmp',
			'wbxml'   => 'application/vnd.wap.wbxml',
			'wm'      => 'video/x-ms-wm',
			'wma'     => 'audio/x-ms-wma',
			'wml'     => 'text/vnd.wap.wml',
			'wmlc'    => 'application/vnd.wap.wmlc',
			'wmls'    => 'text/vnd.wap.wmlscript',
			'wmlsc'   => 'application/vnd.wap.wmlscriptc',
			'wmv'     => 'video/x-ms-wmv',
			'wmx'     => 'video/x-ms-wmx',
			'wrl'     => 'model/vrml',
			'wvx'     => 'video/x-ms-wvx',
			'xbm'     => 'image/x-xbitmap',
			'xht'     => 'application/xhtml+xml',
			'xhtml'   => 'application/xhtml+xml',
			'xml'     => 'text/xml',
			'xpm'     => 'image/x-xpixmap',
			'xsl'     => 'text/xml',
			'xwd'     => 'image/x-xwindowdump',
			'xyz'     => 'chemical/x-xyz',
			'zip'     => 'application/zip',
			'doc'     => 'application/msword',
			'dot'     => 'application/msword',
			'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'docm'    => 'application/vnd.ms-word.document.macroEnabled.12',
			'dotm'    => 'application/vnd.ms-word.template.macroEnabled.12',
			'xls'     => 'application/vnd.ms-excel',
			'xlt'     => 'application/vnd.ms-excel',
			'xla'     => 'application/vnd.ms-excel',
			'xlsx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'xlsm'    => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'xltm'    => 'application/vnd.ms-excel.template.macroEnabled.12',
			'xlam'    => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'xlsb'    => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'ppt'     => 'application/vnd.ms-powerpoint',
			'pot'     => 'application/vnd.ms-powerpoint',
			'pps'     => 'application/vnd.ms-powerpoint',
			'ppa'     => 'application/vnd.ms-powerpoint',
			'pptx'    => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'potx'    => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'ppsx'    => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'ppam'    => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'pptm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'potm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'ppsm'    => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
		);
		
		$ext = strtolower( array_pop( explode( '.', $filename ) ) );
		
		if ( ! empty( $mime_types[$ext] ) ) {
			if ( true === $debug )
				return array( 'mime_type' => $mime_types[$ext], 'method' => 'from_array' );
			return $mime_types[$ext];
		}
		
		if ( true === $debug )
			return array( 'mime_type' => 'application/octet-stream', 'method' => 'last_resort' );
		return 'application/octet-stream';
	}
	
	//Redirect to page
	function redirect($URL) {
		header("Location: " . $URL);
		exit;
	}
	
	//A function to flatten a nested array
	function flatten($array) {
		if (!is_array($array)) {
			return array($array);
		}
	
		$result = array();
		
		foreach ($array as $value) {
			$result = array_merge($result, flatten($value));
		}
	
		return array_unique($result);
	}
	
	//A function to remove an array value by the element
	function removeElement ($array, $element) {
		$return = array();
		
		for($count = 0; $count <= sizeof($array); $count ++) {
			if ($array[$count] === $element) {
				unset($array[$count]);
			} else {
				array_push($return, $array[$count]);
			}
		}
		
		return $return;
	}
	
	//A function to prepare to display values from a database
	function prepare($item, $htmlEncode = false, $stripSlashes = true) {
		if ($stripSlashes == true) {
			if ($htmlEncode == true) {
				return htmlentities(stripslashes($item));
			} else {
				return stripslashes($item);
			}
		} else {
			if ($htmlEncode == true) {
				return htmlentities($item);
			} else {
				return $item;
			}
		}
	}
	
	//Alias of mysql_real_escape_string
	function escape($string) {
		if (is_string($string)) {
			return mysql_real_escape_string($string);
		} else {
			errorMessage("The provided variable was not a string.");
		}
	}
	
	//Run a mysql_query
	function query($query, $returnType = false, $showError = true) {
		global $connDBA;
		
		$action = mysql_query($query, $connDBA);
		
		if (!$action) {
			if ($showError == true) {
				$error = debug_backtrace();
				die(errorMessage("There is an error with your query: " . $query . "<br /><br />" . mysql_error() . "<br /><br />Error on line: " . $error['0']['line'] . "<br />Error in file: " . $error['0']['file']));
			} else {
				return false;
			}
		} else {
			if (!strstr($query, "INSERT INTO") && !strstr($query, "UPDATE") && !strstr($query, "SET") && !strstr($query, "DELETE FROM") && !strstr($query, "CREATE TABLE") && !strstr($query, "ALTER TABLE")) {
				if ($returnType == false || $returnType == "array") {
					$result = mysql_fetch_array($action);
					
					if (is_array($result) && !empty($result)) {
						array_merge_recursive($result);
						$return = array();
						
						foreach ($result as $key => $value) {
							$return[$key] = prepare($value, false, true);
						}
						
						return $result;
					} else {
						return false;
					}
					
					unset($query, $action, $result);
					exit;
				} elseif ($returnType == "raw") {
					$actionTest = mysql_query($query, $connDBA);
					$result = mysql_fetch_array($actionTest);
					
					if ($result) {
						return $action;
					} else {
						return false;
					}
					
					unset($query, $action, $result);
					exit;
				} elseif ($returnType == "num") {
					$result = mysql_num_rows($action);
					return $result;
					unset($query, $action, $result);
					exit;
				} elseif ($returnType == "selected") {
					$return = array();
					
					while ($result = mysql_fetch_array($action)) {
						array_push($return, $result);
					} 
					
					return flatten($return,array());
					unset($query, $action, $result);
					exit;
				} elseif ($returnType == "assoc") {
					$result = mysql_fetch_assoc($action);
					
					if (is_array($result) && !empty($result)) {
						array_merge_recursive($result);
						$return = array();
						
						foreach ($result as $key => $value) {
							$return[$key] = prepare($value, false, true);
						}
						
						return $result;
					} else {
						return false;
					}
					
					unset($query, $action, $result);
					exit;
				}
			}
		}
	}
	
	//Check item existance
	function exist($table, $column = false, $value = false) {
		global $connDBA;
		
		if ($column == true) {
			$additionalCheck = " WHERE `{$column}` = '{$value}'";
		} else {
			$additionalCheck = "";
		}
		
		$itemCheckGrabber = mysql_query("SELECT * FROM {$table}{$additionalCheck}", $connDBA);
		
		if ($itemCheckGrabber) {
			$itemCheck = mysql_num_rows($itemCheckGrabber);
			
			if ($itemCheck >= 1) {
				$itemGrabber = mysql_query("SELECT * FROM {$table}{$additionalCheck}", $connDBA);
				$item = mysql_fetch_array($itemGrabber);
				
				return $item;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	//A function to grab grab the previous item in the database
	function lastItem($table, $whereColumn = false, $whereValue = false, $column = false) {
		global $connDBA;
		
		if ($column == false) {
			$column = "position";
		} else {
			$column = $column;
		}
		
		if ($whereColumn == true && $whereValue == true) {
			$where = " WHERE `{$whereColumn}` = '{$whereValue}' ";
		} else {
			$where = "";
		}
		
		$lastItemGrabber = query("SELECT * FROM {$table}{$where} ORDER BY {$column} DESC", "raw", false);
		
		if ($lastItemGrabber) {
			$lastItem = mysql_fetch_array($lastItemGrabber);
			return $lastItem[$column] + 1;
		} else {
			return "1";
		}
	}
	
	//A function to process the differences between two strings
	function diff($old, $new){
        foreach($old as $oindex => $ovalue){
                $nkeys = array_keys($new, $ovalue);
                foreach($nkeys as $nindex){
                        $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                        if($matrix[$oindex][$nindex] > $maxlen){
                                $maxlen = $matrix[$oindex][$nindex];
                                $omax = $oindex + 1 - $maxlen;
                                $nmax = $nindex + 1 - $maxlen;
                        }
                }       
        }
        if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
        return array_merge(
                diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
                array_slice($new, $nmax, $maxlen),
                diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
	}
	
	function htmlDiff($old, $new){
			//$oldString = str_ireplace("><", "> <", $old);
			//$newString = str_ireplace("><", "> <", $new);
			$oldString = strip_tags($old, "<p><a><b><strong><i><u><em><ul><ol><li><h1><h2><h3><h4><h5><br>");
			$newString = strip_tags($new, "<p><a><b><strong><i><u><em><ul><ol><li><h1><h2><h3><h4><h5><br>");
			$diff = diff(explode(' ', $oldString), explode(' ', $newString));
			foreach($diff as $k){
					if(is_array($k)) {
						$deleteArray = array();
						$insertArray = array();
						$findArray = array("<p>", "</p>");
						
						if (!empty($k['d'])) {
							foreach($k['d'] as $delete) {
								array_push($deleteArray, str_ireplace($findArray, "<br />" . $include, $delete));
							}
						}
						
						if (!empty($k['i'])) {
							foreach($k['i'] as $insert) {								
								array_push($insertArray, str_ireplace($findArray, "<br />" . $include, $insert));
							}
						}
						
						$ret .= (!empty($k['d'])?"<p><del>".implode(" ", $deleteArray)."</del></p> ":'').
								(!empty($k['i'])?"<p><ins>".implode(" ", $insertArray)."</ins></p> ":'');
								
					} else {
						$ret .= $k . ' ';
					}
			}
			return $ret;
	}
	
	//Check to see if a column exists
	function columnExists($table, $column, $id, $empty = false) {
		$query = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'");
		
		if (array_key_exists($column, $query)) {
			if ($empty == true) {				
				if (empty($query[$column])) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	//Generate the next avaliable comlum for MySQL
	function nextLog($table, $id) {
		$query = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'", "assoc");
		unset($query['id'], $query['position'], $query['parentPage'], $query['subPosition'], $query['visible'], $query['published'], $query['display'], $query['name'], $query['date'], $query['comment'], $query['type']);
		
		for($count = 1; $count <= sizeof($query); $count ++) {
			if (columnExists($table, "content" . $count, $id, true)) {
				return "content" . $count;
				break;
			}
			
			$free = $count;
		}
		
		return "content" . sprintf($free + 1);
	}
	
	//Process a CMS content form
	function process($tableName, $privilegeType, $redirect, $redirectType) {
		global $userData;
		
		if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['content'])) {	
			$title = $_POST['title'];
			$content = $_POST['content'];
			$comments = $_POST['comments'];
			$user = $userData['id'];
			$time = strtotime("now");
			
			if (!isset ($_GET['id'])) {
				$position = lastItem($tableName);
				
				if (privileges("publish" . $privilegeType) == "true") {
					$published = "2";
				} else {
					$published = "0";
				}
				
				if ($tableName == "pages") {
					$parentPage = $_POST['parentPage'];
					
					if ($parentPage !== "0") {
						$lastSubPositionGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '{$parentPage}' ORDER BY `subPosition` DESC LIMIT 1", false, false);
						
						if ($lastSubPositionGrabber) {
							$lastSubPosition = sprintf($lastSubPositionGrabber['subPosition'] + 1);
						} else {
							$lastSubPosition = "1";
						}
					} else {
						$lastSubPosition = "0";
					}
					
					if ($parentPage != "0") {
						$position = "0";
					}
					
					$additionalColumn = ", `name`, `date`, `comment`";
					$additionalValue = ", '', '', ''";
					$hierarchyColumn = " `parentPage`, `subPosition`,";
					$hierarchyValue = " '{$parentPage}', '{$lastSubPosition}',";
				} else {
					$additionalColumn = "";
					$additionalValue = "";
					$hierarchyColumn = "";
					$hierarchyValue = "";
				}
				
				if ($tableName == "sidebar") {
					$type = $_POST['type'];
					$typeColumn = " `type`,";
					$typeValue = " '{$type}',";
				} else {
					$typeColumn = "";
					$typeValue = "";
				}
				
				if ($tableName != "staffpages") {
					$visibleColumn = " `visible`,";
					$visibleValue = " 'on',";
				} else {
					$visibleColumn = "";
					$visibleValue = "";
				}
				
				$contentBundle = escape(serialize(array("title" => $title, "content" => $content, "comments" => $comments, "message" => "", "user" => $user, "time" => $time, "changes" => "1")));
				
				query("INSERT INTO `{$tableName}` (
						  `id`, `position`,{$visibleColumn}{$typeColumn}{$hierarchyColumn} `published`, `display`, `content1`{$additionalColumn}
					  ) VALUES (
						  NULL, '{$position}',{$visibleValue}{$typeValue}{$hierarchyValue} '{$published}', '1', '{$contentBundle}'{$additionalValue}
					  )");
				
				if (!isset($parentPage)) {
					redirect($redirect . "?added=" . $redirectType);
				} else {
					redirect($redirect . "?category=" . $parentPage . "&added=" . $redirectType);
				}
			} else {				
				$id = $_GET['id'];
				$pageDataPrep = query("SELECT * FROM `{$tableName}` WHERE `id` = '{$id}' LIMIT 1");
				$nextLog = nextLog($tableName, $id);
				
				if ($pageDataPrep['published'] != "0") {
					if (!columnExists($tableName, $nextLog, $id)) {
						query("ALTER TABLE `{$tableName}` ADD `{$nextLog}` LONGTEXT NOT NULL");
					}
					
					if (columnExists($tableName, $nextLog, $id) && empty($pageDataPrep['content' . sprintf($pageDataPrep['display'] + 1)])) {
						$contentEditor = "content" . $pageDataPrep['display'];
					} else {
						$contentEditor = "content" . sprintf($pageDataPrep['display'] + 1);
					}
				} else {
					$contentEditor = "content" . $pageDataPrep['display'];
				}
				
				$pageData = unserialize($pageDataPrep[$contentEditor]);
				
			//Sidebar only, if the type of sidebar hasn't changed
				if ($tableName == "sidebar") {
					$type = $_POST['type'];
					$typeUpdate = " `type` = '{$type}',";
				} else {
					$typeUpdate = "";
				}
				
			//Check to see if the parent page has changed
				if ($tableName == "pages") {					
					$parentPage = $_POST['parentPage'];
					$oldData = query("SELECT * FROM `{$tableName}` WHERE `id` = '{$id}'");
					
					if ($parentPage !== $oldData['parentPage']) {
						query("UPDATE `{$tableName}` SET `subPosition` = subPosition-1 WHERE `parentPage` = '{$oldData['parentPage']}' AND `position` = '0'");
						
						if ($oldData['parentPage'] == "0") {
							query("UPDATE `{$tableName}` SET `position` = '0' WHERE `id` = '{$id}'");
							query("UPDATE `{$tableName}` SET `position` = position-1 WHERE `position` > '{$oldData['position']}' AND `parentPage` = '0'");
						}
						
						if ($parentPage == "0") {
							$criteria = "position";
						} else {
							$criteria = "subPosition";
						}
						
						$lastPositionGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '{$parentPage}' ORDER BY `{$criteria}` DESC LIMIT 1", false, false);
						
						if ($lastPositionGrabber) {
							$lastPosition = sprintf($lastPositionGrabber[$criteria] + 1);
						} else {
							$lastPosition = "1";
						}
						
						$hierarchyUpdate = " `parentPage` = '{$parentPage}', `{$criteria}` = '{$lastPosition}',";
					} else {
						$hierarchyUpdate = "";
					}
				} else {
					$hierarchyUpdate = "";
				}
				
			//Check to see if the comments need a comparison
				if ($tableName == "pages" || $tableName == "staffpages") {
					if (prepare($pageData['comments']) === $comments) {
						$oldComments = true;
						$newComments = true;
					} else {
						$oldComments = false;
						$newComments = true;
					}
				} else {
					$oldComments = true;
					$newComments = true;
				}
				
			//No changes were made, then simply log the update
				if (prepare($pageData['title']) === prepare($_POST['title']) && prepare($pageData['content']) === prepare($_POST['content']) && $oldComments === $newComments) {
					$oldCommentsPrep = query("SELECT * FROM `{$tableName}` WHERE `id` = '{$id}' LIMIT 1");
					$oldComments = unserialize($oldCommentsPrep['content' . $oldCommentsPrep['display']]);
					
					if (privileges("publish" . $privilegeType) == "true") {
						$published = "2";
						$display = ereg_replace("[^0-9]", "", $nextLog);
						$column = $nextLog;
						$contentBundle = escape(serialize(array("title" => $title, "content" => $content, "comments" => $comments, "message" => "", "user" => $user, "time" => $time, "changes" => "0")));
					} else {
						$contentBundle = escape(serialize(array("title" => $title, "content" => $content, "comments" => $comments, "message" => $oldComments['message'], "user" => $user, "time" => $time, "changes" => "0")));
						
						if ($pageDataPrep['published'] == "0" || $pageDataPrep['published'] == "1") {
							$published = $pageDataPrep['published'];
							$display = $pageDataPrep['display'];
							$column = "content" . $pageDataPrep['display'];
						} else {
							$published = "2";
							$display = ereg_replace("[^0-9]", "", $nextLog);
							$column = $nextLog;
						}
					}
					
					if (isset($parentPage)) {
						$redirect = $redirect . "?category=" . $parentPage;
					}
			//Changes were made
				} else {
					if (privileges("publish" . $privilegeType) == "true") {
						$published = "2";
						$display = ereg_replace("[^0-9]", "", $nextLog);
						$column = $nextLog;
					} else {
						if ($pageDataPrep['published'] == "0") {
							$published = $pageDataPrep['published'];
							$column = "content" . $pageDataPrep['display'];
						} else {
							$published = "1";
							$column = "content" . sprintf($pageDataPrep['display'] + 1);
						}
						
						$display = $pageDataPrep['display'];
					}
					
					$contentBundle = escape(serialize(array("title" => $title, "content" => $content, "comments" => $comments, "message" => "", "user" => $user, "time" => $time, "changes" => "1")));
					
					if (!isset($parentPage)) {
						$redirect = $redirect . "?updated=" . $redirectType;
					} else {
						$redirect = $redirect . "?category=" . $parentPage . "&updated=" . $redirectType;
					}
					
				//Remove the old rejection message
					if ($pageDataPrep['published'] == "1" || $pageDataPrep['published'] == "0") {
						$remove = query("SELECT * FROM `{$tableName}` WHERE `id` = '{$id}'");
						$refreshColumn = "content" . $remove['display'];
						$refreshContentPrep = unserialize($remove['content' . $remove['display']]);
						$refreshContent =  escape(serialize(array("title" => $refreshContentPrep['title'], "content" => $refreshContentPrep['content'], "comments" => $refreshContentPrep['comments'], "message" => "", "user" => $refreshContentPrep['user'], "time" => $refreshContentPrep['time'], "changes" => $refreshContentPrep['changes'])));
						query("UPDATE `{$tableName}` SET `{$refreshColumn}` = '{$refreshContent}' WHERE `id` = '{$id}'");
					}
				}
				
				query("UPDATE `{$tableName}` SET{$hierarchyUpdate} `published` = '{$published}',{$typeUpdate} `display` = '{$display}', `{$column}` = '{$contentBundle}' WHERE `id` = '{$id}'");
				
				redirect($redirect);
			}
		}
	}
	
	//Detirmine the user's privileges
	function privileges($checkType, $global = "false") {
		global $connDBA;
		global $root;
		
		$privileges = $_SESSION['MM_UserGroup'];
		$privilegesCheckGrabber = mysql_query("SELECT {$checkType} FROM `privileges` WHERE `id` = '1'", $connDBA);
		$privilegesCheck = mysql_fetch_array($privilegesCheckGrabber);
		
		if ($global == "false") {
			if (($privilegesCheck[$checkType] == "1" || $_SESSION['MM_UserGroup'] == "Administrator") || ($privilegesCheck[$checkType] == "1" && $_SESSION['MM_UserGroup'] == "Administrator")) {
				return "true";
			} else {
				return "false";
			}
		} else {
			if ($privilegesCheck[$checkType] == "1") {
				return "true";
			} else {
				return "false";
			}
		}
	}
	
	//Detirmine the user's access to a page
	function access($tableName, $privilegeType, $redirect) {
		global $databaseName;
		
		if (!isset ($_GET['id'])) {
			if (privileges("create" . $privilegeType) == "true") {
				loginCheck("User,Administrator");
			} else {
				loginCheck("Administrator");
			}
		} else {		
			$id = $_GET['id'];
			
			if (exist($tableName, "id", $id)) {
				$item = query("SELECT * FROM `{$tableName}` WHERE `id` = '{$id}'");
				$message = unserialize($item['content' . $item['display']]);
				$published = $item['published'];
			
				if (isset($_GET['content'])) {
					$content = $_GET['content'];
					$contentPrep = unserialize($item['content' . $content]);
					
					if (!columnExists($tableName, "content" . $content, $id)) {
						redirect($redirect);
					}
					
					if (privileges("edit" . $privilegeType) == "true" && privileges("publish" . $privilegeType) == "true") {
						loginCheck("User,Administrator");
					} else {
						loginCheck("Administrator");
					}
					
					$message = $contentPrep['message'];
				} else {
					if ($item['published'] == "1") {
						$contentPrep = unserialize($item['content' . sprintf($item['display'] + 1)]);
					} else {
						$contentPrep = unserialize($item['content' . $item['display']]);
					}
					
					if (privileges("edit" . $privilegeType) == "true") {
						loginCheck("User,Administrator");
					} else {
						loginCheck("Administrator");
					}
					
					$message = $message['message'];
				}
				
				$title = $contentPrep['title'];
				$content = $contentPrep['content'];
				$comments = $contentPrep['comments'];
			} else {
				redirect($redirect);
			}
			
			return array("title" => prepare($title), "content" => prepare($content), "comments" => $comments, "message" => prepare($message), "published" => $published);
		}
	}
	
	//Reorder items	
	function reorderItem($privilegesType, $table, $redirect) {
		global $connDBA;
		
		if (privileges("edit" . $privilegesType) == "true") {
			if (isset ($_GET['action']) && $_GET['action'] == "modifySettings" && isset($_GET['id']) && isset($_GET['position']) && isset($_GET['currentPosition'])) {
				$id = $_GET['id'];
				$newPosition = $_GET['position'];
				$currentPosition = $_GET['currentPosition'];
			    
				if ($table == "pages") {
					$itemData = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'");
					
					if ($itemData['parentPage'] == "0") {
						$position = "position";
						$parentPage = " `parentPage` = '0' AND";
					} else {
						$position = "subPosition";
						$parentPage = " `parentPage` = '{$itemData['parentPage']}' AND";
						$redirect .= "?category=" . $itemData['parentPage'];
					}
				} else {
					$position = "position";
					$parentPage = "";
				}
				
				if ($currentPosition > $newPosition) {
					mysql_query("UPDATE {$table} SET {$position} = {$position} + 1 WHERE{$parentPage} {$position} >= '{$newPosition}' AND {$position} <= '{$currentPosition}'", $connDBA);
					mysql_query ("UPDATE {$table} SET {$position} = '{$newPosition}' WHERE id = '{$id}'", $connDBA);
				} elseif ($currentPosition < $newPosition) {
					mysql_query("UPDATE {$table} SET {$position} = {$position} - 1 WHERE{$parentPage} {$position} <= '{$newPosition}' AND {$position} >= '{$currentPosition}'", $connDBA);
					mysql_query("UPDATE {$table} SET {$position} = '{$newPosition}' WHERE id = '{$id}'", $connDBA);
				}
				
				header ("Location: " . $redirect);
			}
		}
	}
	
	//Set item avaliability
	function avaliability($privilegesType, $table, $regularRedirect, $requestedRedirect = false) {
		if (privileges("edit" . $privilegesType) == "true") {
			if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
				$id = $_POST['id'];
				
				if (!$_POST['option']) {
					$option = "";
				} else {
					$option = $_POST['option'];
				}
				
				query("UPDATE `{$table}` SET `visible` = '{$option}' WHERE id = '{$id}'");
				
				if (isset($_POST['redirect'])) {
					redirect($requestedRedirect . "?page=" . $id);
				}
				
				redirect($regularRedirect);
			}
		}
	}
	
	//Delete an item
	function delete($privilegesType, $table, $redirect) {
		if (privileges("delete" . $privilegesType) == "true") {
			if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {			 
				if ($_GET['id'] == 0 || !exist($table, "id", $_GET['id'])) {
					redirect($redirect);
				} else {
					$deleteItem = $_GET['id'];
					$itemPositionPrep = query("SELECT * FROM `{$table}` WHERE `id` = '{$deleteItem}'");
					$itemPosition = $itemPositionPrep['position'];
					
					if ($table == "pages" && exist($table, "parentPage", $id)) {
						$itemSubPosition = $itemPositionPrep['subPosition'];
						$parentPage = $itemPositionPrep['parentPage'];
						$redirect .= "?category=" . $parentPage;
							
						if ($itemSubPosition !== "0") {					
							query("UPDATE `{$table}` SET `subPosition` = subPosition-1 WHERE `subPosition` > '{$itemSubPosition}'");
						}
						  
						//Check it see if the current page is a child of the parent
						function isChild($input) {
							if (exist("pages", "id", $input)) {						
								$childCheck = query("SELECT * FROM `pages` WHERE `id` = '{$input}'");
								
								if ($childCheck['id'] == $_GET['id']) {
									return true;
								} else {
									return isChild($childCheck['parentPage']);
								}
							}
						}
						
						$delete = array();
							
						//Recursively loop through the pages
						function pagesDirectory($level, &$delete) {
							if ($level == "0") {
								$pagesGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '{$level}'", "raw");
							} else {
								$pagesGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '{$level}'", "raw");
							}
							
							while ($pages = mysql_fetch_array($pagesGrabber)) {
								if (isset($_GET['id'])) {
								   $parentPage = query("SELECT * FROM `pages` WHERE `id` = '{$_GET['id']}'");
								}
								
								if (isset($_GET['id']) && isChild($pages['parentPage'])) {
									$delete[] = $pages['id'];
								}
								
								if (exist("pages", "parentPage", $pages['id'])) {
									pagesDirectory($pages['id'], $delete);
								}
							}
						}
						
						pagesDirectory($parentPage, $delete);
						
						foreach($delete as $deletePage) {
							query("DELETE FROM `pages` WHERE `id` = '{$deletePage}'");
							query("DELETE FROM `pagehits` WHERE `page` = {$deletePage}");
						}
					}
					
					if ($itemPosition !== "0") {
						query("UPDATE `{$table}` SET `position` = position-1 WHERE `position` > '{$itemPosition}'");
					}
					
					query("DELETE FROM `{$table}` WHERE `id` = {$deleteItem}");
					
					if ($table == "pages") {
						query("DELETE FROM `pagehits` WHERE `page` = {$deleteItem}");
					}
					
					redirect($redirect);
				}
			}
		}
	}
	
	//Approve an item or check its history
	function approve($table, $itemType, $editURL, $privilegeType) {
		global $root;
		
		//Allow or deny access
		if (privileges("publish" . $privilegeType) == "true") {
			loginCheck("User,Administrator");
		} else {
			loginCheck("Administrator");
		}
		
		//Grab the item data
		if (isset($_GET['id'])) {
			if (exist($table, "id", $_GET['id'])) {
				$itemData = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
				$oldData = unserialize($itemData['content' . $itemData['display']]);
				$newData = unserialize($itemData['content' . sprintf($itemData['display'] + 1)]);
				
				if ($itemData['published'] == "2") {
					die(successMessage("This " . $itemType . " is already published."));
				}
			} else {
				die(errorMessage("This " . $itemType . " does not exist."));
			}
		} else {
			die(errorMessage("The " . $itemType . " ID was not provided."));
		}
		
		//Process the form
		if (isset($_GET['id']) && isset($_GET['accepted'])) {			
			if ($itemData['published'] == "1") {
				$display = sprintf(ereg_replace("[^0-9]", "", nextLog($table, $_GET['id'])) - 1);
			} else {
				$display = "1";
			}
			
			query("UPDATE `{$table}` SET `published` = '2', `display` = '{$display}' WHERE `id` = '{$_GET['id']}'");
			die("<script type=\"text/javascript\">window.opener.location.reload(); window.close();</script>");
		}
		
		if (isset($_POST['submit']) && !empty($_POST['comments'])) {
			$dataGrabberPrep = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
			$dataGrabber = unserialize($dataGrabberPrep['content' . $dataGrabberPrep['display']]);
			$comments = $_POST['comments'];
			$contentBundle = escape(serialize(array("title" => $dataGrabber['title'], "content" => $dataGrabber['content'], "comments" => $dataGrabber['comments'], "message" => $comments, "user" => $dataGrabber['user'], "time" => $dataGrabber['time'], "changes" => $dataGrabber['changes'])));
			
			query("UPDATE `{$table}` SET `content{$dataGrabberPrep['display']}` = '{$contentBundle}' WHERE `id` = '{$_GET['id']}'");
			die("<script type=\"text/javascript\">window.opener.location.reload(); window.close();</script>");
		}
		
		//Begin HTML
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head>";
		title("Approve " . ucfirst($itemType));
		headers();
		tinyMCESimple();
		validate();
		echo "<script type=\"text/javascript\" src=\""  . $root . "javascripts/common/showHide.js\"></script>";
		echo "<style type=\"text/css\">
		ins {background-color:#0C0;}
		del {background-color:#903;}
		</style></head><body class=\"overrideBackground\">";
		toolTip();
		echo "<h2>" . "Approve " . ucfirst($itemType) . "</h2><p>&nbsp;</p><div class=\"toolBar\"><a class=\"toolBarItem accept\" href=\"" . $_SERVER['PHP_SELF'] . "?id=" .  $itemData['id'] . "&accepted=true\">Accept Pending Version</a><a class=\"toolBarItem reject\" href=\"javascript:void()\" onclick=\"reject()\">Reject Pending Version</a><div class=\"contentHide\" id=\"reject\"><form name=\"rejectReason\" id=\"validate\" method=\"post\" action=\"" . $_SERVER['REQUEST_URI'] . "\"><p>Why are you rejecting this version?</p><p><textarea name=\"comments\" id=\"comments\" cols=\"45\" rows=\"5\" style=\"width:450px;\" class=\"validate[required]\"></textarea></p><p>";
		submit("submit", "Submit");
		echo "</p></form></div></div><br />";
		
		//Create a function to supply specific version information
		function versionInfo($inputData, $itemType, $display) {
			echo "<h6>";
			
			if ($inputData['comments'] == "1") {
				echo "Comments: <strong style=\"color:#00FF00\">On</strong><br />";
			} else {
				echo "Comments: <strong style=\"color:#FF0000\">Off</strong><br />";
			}
			
			if (exist("users", "id", $inputData['user'])) {
				$userInfo = query("SELECT * FROM `users` WHERE `id` = '{$inputData['user']}'");
				$user = prepare(ucfirst($userInfo['firstName']) . " " . ucfirst($userInfo['lastName'])) . " ";
			} else {
				$user = "An unknown user ";
			}
			
			if ($display != "1") {
				switch ($inputData['changes']) {
					case "0" : 
						$change = "viewed, but made no changes to this " . $itemType;
						break;
						
					case "1" : 
						$change = "edited this " . $itemType;
						break;
						
					default :
						$revert = unserialize($inputData['changes']);
						$change = "reverted this " . $itemType . " to version " . $revert['0'];
						break;
				}
			} else {
				$change = "created this " . $itemType;
			}
			
			echo $user . $change . " on " . date("l, M j, Y \\a\\t h:i:s A", $inputData['time']) . ".<br />Version: ";
		}
		
		//Display the comparison layout only if an edited page is being approved
		if ($itemData['published'] == "1") { 
			echo "<div class=\"layoutControl\"><div class=\"halfLeft\"><div><p>Pending Approval";
			
			if (privileges("edit" . $privilegeType) == "true") {
				echo " <a class=\"smallEdit\" target=\"_blank\" href=\"" . $editURL . "?id=" . $itemData['id'] . "&content=" . sprintf($itemData['display'] + 1) . "\" onmouseover=\"Tip('Edit version <strong>" . sprintf($itemData['display'] + 1) . "</strong>')\" onmouseout=\"UnTip()\"></a>";
			}
			
			echo "</p>";
			
			versionInfo($newData, $itemType, sprintf($itemData['display'] + 1));
			echo sprintf($itemData['display'] + 1);
			echo "</h6>";
			
			echo "</div><div><h2>" . prepare($newData['title']) . "</h2>" . prepare($newData['content']) . "</div></div><div class=\"halfRight\"><div><p>Curently Published";
			
			if (privileges("edit" . $privilegeType) == "true") {
				echo " <a class=\"smallEdit\" target=\"_blank\" href=\"" . $editURL . "?id=" . $itemData['id'] . "&content=" . $itemData['display'] . "\" onmouseover=\"Tip('Edit version <strong>" . $itemData['display'] . "</strong>')\" onmouseout=\"UnTip()\"></a><br />";
			}
			
			echo "</p>";
			
			versionInfo($oldData, $itemType, $itemData['display']);
			echo $itemData['display'];
			echo "</h6>";
			
			echo "</div><div><h2>" . prepare($oldData['title']) . "</h2>" . prepare($oldData['content']) . "</div></div></div>";
			
			echo "<div class=\"toolBar noPadding\">Below is the comparison between the pending version and the existing version. Please note that this only a text comparison. The differences regarding images, tables, etc... will not be displayed.</div><br />" . htmlDiff(stripslashes($oldData['content']), stripslashes($newData['content']));
	//Display the a single column for approving a new page
		} else {
			echo "<div>Pending Approval";
			
			if (privileges("edit" . $privilegeType) == "true") {
				echo " <a class=\"smallEdit\" target=\"_blank\" href=\"" . $editURL . "?id=" . $itemData['id'] . "&content=1\" onmouseover=\"Tip('Edit version <strong>1</strong>')\" onmouseout=\"UnTip()\"></a>";
			}
			
			echo "<h6>";
			
			if ($oldData['comments'] == "1") {
				echo "Comments: <strong style=\"color:#00FF00\">On</strong><br />";
			} else {
				echo "Comments: <strong style=\"color:#FF0000\">Off</strong><br />";
			}
			
			if (exist("users", "id", $oldData['user'])) {
				$userInfo = query("SELECT * FROM `users` WHERE `id` = '{$oldData['user']}'");
				$user = prepare(ucfirst($userInfo['firstName']) . " " . ucfirst($userInfo['lastName'])) . " ";
			} else {
				$user = "An unknown user ";
			}			
			
			echo $user . "created this " . $itemType . " on " . date("l, M j, Y \\a\\t h:i:s A", $oldData['time']) . ".<br />Version: 1</h6>";
			
		  	echo "<h2>" . prepare($oldData['title']) . "</h2>" . prepare($oldData['content']);
			
			echo "</div>";
		}
		
		echo "</body></html>";
	}
	
	//Display the history of a certain page
	function history($table, $itemType, $editURL, $privilegeType) {
		global $root;
		
		//Allow or deny access
		if (privileges("publish" . $privilegeType) == "true") {
			loginCheck("User,Administrator");
		} else {
			loginCheck("Administrator");
		}
		
		//Grab the item data
		if (isset($_GET['id'])) {
			if (exist($table, "id", $_GET['id'])) {
				$itemData = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
				$oldData = unserialize($itemData['content' . $itemData['display']]);
				$newData = unserialize($itemData['content' . sprintf($itemData['display'] + 1)]);
			} else {
				die(errorMessage("This " . $itemType . " does not exist."));
			}
		} else {
			die(errorMessage("The " . $itemType . " ID was not provided."));
		}
		
		//Process the form
		if (isset($_GET['history']) && isset($_GET['revert']) && $_GET['revert'] == "true") {
			if (array_key_exists("content" . $_GET['history'], $itemData) && !empty($itemData["content" . $_GET['history']]) && $_GET['revert'] != $itemData['display']) {
				$revertDataPrep = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
				$revertData = unserialize($revertDataPrep["content" . $_GET['history']]);
				$userData = userData();
				$time = strtotime("now");
				$changes = serialize(array($_GET['history']));
				$contentBundle = escape(serialize(array("title" => $revertData['title'], "content" => $revertData['content'], "comments" => $revertData['comments'], "message" => "", "user" => $userData['id'], "time" => $time, "changes" => $changes)));
				$contentEditor = $itemData['display'];
				$nextLog = nextLog($table, $_GET['id']);
				$display = ereg_replace("[^0-9]", "", $nextLog);
					
				if (!columnExists($table, $nextLog, $_GET['id'])) {
					query("ALTER TABLE `{$table}` ADD `{$nextLog}` LONGTEXT NOT NULL");
				}
				
				query("UPDATE `{$table}` SET `published` = '2', `display` = '{$display}', `{$nextLog}` = '{$contentBundle}' WHERE `id` = '{$_GET['id']}'");
				die("<script type=\"text/javascript\">window.opener.location.reload(); window.close();</script>");
			} else {
				redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
			}
		}
		
		//Clean-up old records
		if (isset($_GET['id']) && isset($_GET['cleanUp']) && $_GET['cleanUp'] == "true") {
			if (exist($table, "id", $_GET['id'])) {
				$recordBuilder = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
				$currentRecord = escape($recordBuilder['content' . $recordBuilder['display']]);
				$updateSQL = "UPDATE `{$table}` SET `content1` = '{$currentRecord}'";
				$start = 1;
				$end = $recordBuilder['display'];
				
				if (!empty($recordBuilder['content' . sprintf($recordBuilder['display'] + 1)])) {
					$nextRecord = escape($recordBuilder['content' . sprintf($recordBuilder['display'] + 1)]);
					$updateSQL .= ", `content2` = '{$nextRecord}'";
					$start = 2;
					$end = sprintf($recordBuilder['display'] + 1);
				}
				
				for($count = sprintf($start + 1); $count <= $end; $count++) {
					$updateSQL .= ", `content{$count}` = ''";
				}
				
				$updateSQL .= ", `display` = '1' WHERE `id` = '{$_GET['id']}'";
				
				query($updateSQL);
				redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
			}
		}
		
		//Begin HTML
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head>";
		title(ucfirst($itemType) . " History");
		headers();
		echo "</head><body class=\"overrideBackground\">";
		tooltip();
		echo "<div class=\"box generalboxcontent boxaligncenter\" style=\"margin-top:0px;\">";
		
		//Display the history overview
		if (!isset($_GET['preview'])) {
			echo "<h2>" . ucfirst($itemType) . " History</h2><p>Below is a list of versions for this " . $itemType .". These versions can be edited and reverted to as needed.</p>";
			
		//Admin toolbar
			if ($itemData["display"] != "1") {
				echo "<div class=\"toolBar\"><a href=\"" . $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&cleanUp=true\" class=\"toolBarItem deleteTool\" onclick=\"return confirm('This action will remove all records prior to the published version. This action cannot be undone. Continue?')\">Clean-up Old Records</a></div><br />";
			} else {
				echo "<br />";
			}
			
			$count = 1;
			
			//Display all history
			echo "<table class=\"dataTable\"><tbody><tr>";
			echo "<th width=\"50\" class=\"tableHeader\">Version</th>";
			echo "<th width=\"50\" class=\"tableHeader\">Change</th>";
			echo "<th width=\"150\" class=\"tableHeader\">Editor</th>";
			echo "<th width=\"150\" class=\"tableHeader\">Date</th>";
			echo "<th width=\"250\" class=\"tableHeader\">Title</th>";
			echo "<th class=\"tableHeader\">Content</th>";
			echo "<th width=\"50\" class=\"tableHeader\">Revert</th>";
			echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
			echo "</tr>";
			
			for ($i = sprintf($itemData["display"] + 1); $i >= 1; $i --) {
				if (!empty($itemData["content" . $i])) {
					$item = unserialize($itemData["content" . $i]);
					
					if ($i != "1") {
						switch ($item['changes']) {
							case "0" : 
								$change = "None";
								break;
								
							case "1" : 
								$change = "Edited";
								break;
								
							default : 
								$change = "Reverted";
								break;
						}
					} else {
						$change = "Created";
					}
					
					if (exist("users", "id", $item['user'])) {
						$userInfo = query("SELECT * FROM `users` WHERE `id` = '{$item['user']}'");
						$user = prepare(ucfirst($userInfo['firstName']) . " " . ucfirst($userInfo['lastName']));
					} else {
						$user = "Unknown";
					}
					
					echo "<tr";
					if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"50\">" . $i . "</td>";
					echo "<td width=\"50\">" . $change . "</td>";
					echo "<td width=\"150\">" . commentTrim(20, $user) . "</td>";
					echo "<td width=\"150\">" . commentTrim(20, date("M j, Y", $item['time'])) . "</td>";
					echo "<td width=\"250\"><a href=\"" . $_SERVER['REQUEST_URI'] . "&preview=" . $i . "\" onmouseover=\"Tip('Preview version <strong>" . $i . "</strong>')\" onmouseout=\"UnTip()\">" . commentTrim(30, $item['title']) . "</a></td>";
					echo "<td>" . commentTrim(50, $item['content']) . "</td>";
					echo "<td width=\"50\">";
					
					if ($i != $itemData['display']) {
						echo "<a href=\"" . $_SERVER['REQUEST_URI'] . "&history=" . $i . "&revert=true\" class=\"action revert\" onmouseover=\"Tip('Revert to version <strong>" . $i . "</strong>')\" onmouseout=\"UnTip()\" onclick=\"return confirm('This action will revert this version to the current version. Continue?')\"></a></td>";
					} else {
						echo "<span class=\"action noRevert\" onmouseover=\"Tip('This is the current version')\" onmouseout=\"UnTip()\"></span>";
					}
					
					echo "<td width=\"50\"><a href=\"" . $editURL . "?id=" . $_GET['id'] . "&content=" . $i . "\" class=\"action edit\" onmouseover=\"Tip('Edit version <strong>" . $i . "</strong>')\" onmouseout=\"UnTip()\" target=\"_blank\"></a></td>";
					echo "</tr>";
					
					$count ++;
				}
			}
			
			echo "</tbody></table>";
		//Display individual details
		} else {
			if (array_key_exists("content" . $_GET['preview'], $itemData) && !empty($itemData["content" . $_GET['preview']])) {
				$item = unserialize($itemData["content" . $_GET['preview']]);
				
				if ($_GET['preview'] != "1") {
					switch ($item['changes']) {
						case "0" : 
							$change = "viewed, but made no changes to this version";
							break;
							
						case "1" : 
							$change = "edited this version";
							break;
							
						default : 
							$revert = unserialize($item['changes']);
							$change = "reverted this " . $itemType . " to version " . $revert['0'];
							break;
					}
				} else {
					$change = "created this " . $itemType;
				}
				
				if (exist("users", "id", $item['user'])) {
					$userInfo = query("SELECT * FROM `users` WHERE `id` = '{$item['user']}'");
					$user = prepare(ucfirst($userInfo['firstName']) . " " . ucfirst($userInfo['lastName'])) . " ";
				} else {
					$user = "An unknown user ";
				}
				
				echo "<h2>" . prepare($item['title']) . "</h2>";
				echo "<h5>" . $user . $change . " on " . date("l, M j, Y \\a\\t h:i:s A", $item['time']) . ".</h5>";
				echo "<p>&nbsp;</p>";
				echo "<div class=\"toolBar\"><a href=\"" . $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "\" class=\"toolBarItem search\">Back to Overview</a>";
				
				if ($_GET['preview'] != $itemData['display']) {
					echo "<a href=\"" . $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&history=" . $_GET['preview'] . "&revert=true\" class=\"toolBarItem back\" onclick=\"return confirm('This action will revert this version to the current version. Continue?')\">Revert to this Version</a>";
				}
				
				echo "<a href=\"" . $editURL . "?id=" . $_GET['id'] . "&content=" . $_GET['preview'] . "\" class=\"toolBarItem editTool\" target=\"_blank\">Edit this Version</a></div>";
				echo prepare($item['content']);
			} else {
				redirect("history.php?id=" . $_GET['id']);
			}
		}
		
		echo "</div></body></html>";
	}
/* End system functions */

/* Begin table looping functions */
	//Toggle Avaliability
	function toggleAvaliability($itemType, $itemLoopData, $privilegeType) {
		if (privileges("edit" . $privilegeType) == "true") {
			if ($itemLoopData['published'] == "0") {
				echo "<td width=\"25\"><div align=\"center\"><span class=\"noShow\" onmouseover=\"Tip('This " . $itemType . " must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\"></span></div></div></td>";
			} else {
				echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"" . $_SERVER['REQUEST_URI'] . "\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $itemLoopData['id'] . "\" class=\"visible"; if ($itemLoopData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $itemLoopData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $itemLoopData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($itemLoopData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
			}
		}
	}
	
	//Display the publication status
	function status($itemType, $itemLoopData, $privilegeType) {
		echo "<td width=\"50\">";
		
		if (privileges("publish" . $privilegeType) == "true") {
			switch ($itemLoopData['published']) {
				case "0" : echo "<a href=\"javascript:void\" class=\"notPublished\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve.php?id=" . $itemLoopData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,fullscreen=yes')\" onmouseover=\"Tip('This " . $itemType . " must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
				case "1" : echo "<a href=\"javascript:void\" class=\"updatePending\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve.php?id=" . $itemLoopData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,fullscreen=yes')\" onmouseover=\"Tip('This update must be approved <br />before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
				case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This " . $itemType . " is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
			}
		} else {
			switch ($itemLoopData['published']) {
				case "0" : echo "<span class=\"notPublished\" style=\"text-decoration:none;\" onmouseover=\"Tip('This " . $itemType . " must be approved by an <br />administrator before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
				case "1" : echo "<span class=\"updatePending\" style=\"text-decoration:none;\" onmouseover=\"Tip('This update must be approved by an <br />administrator before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
				case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This " . $itemType . " is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
			}
		}
		
		echo "</td>";
	}
	
	//Display the order changing command
	function reorder($table, $itemLoopData, $privilegeType) {
		if (privileges("edit" . $privilegeType) == "true") {
			echo "<td width=\"75\"><form name=\"reorder\" action=\"" . $_SERVER['REQUEST_URI'] . "\"><input type=\"hidden\" name=\"id\" value=\"" . $itemLoopData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"";
			
			if ($table == "pages" && isset($_GET['category']) && $_GET['category'] !== "0" && exist("pages", "parentPage", $_GET['category'])) {
				echo $itemLoopData['subPosition'];
			} else {
				echo $itemLoopData['position'];
			}
				
			echo "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
			
			if ($table != "pages") {
				$itemCount = query("SELECT * FROM `{$table}`", "num");
			} else {
				if (isset($_GET['category']) && exist("pages", "parentPage", $_GET['category'])) {
					$itemCount = query("SELECT * FROM `{$table}` WHERE `parentPage` = '{$_GET['category']}'", "num");
				} else {
					$itemCount = query("SELECT * FROM `{$table}` WHERE `parentPage` = '0'", "num");
				}
			}
			
			for ($count = 1; $count <= $itemCount; $count++) {
				echo "<option value=\"{$count}\"";
				
				if ($table != "pages" || ($table == "pages" && (!isset($_GET['category'])) || $_GET['category'] == "0") || ($table == "pages" && isset($_GET['category']) && !exist("pages", "parentPage", $_GET['category']))) {
					if ($itemLoopData['position'] == $count) {
						echo " selected=\"selected\"";
					}
				} else {
					if ($itemLoopData['subPosition'] == $count) {
						echo " selected=\"selected\"";
					}
				}
				
				echo ">" . $count . "</option>";
			}
			
			echo "</select></form></td>";
		}
	}
	
	//Display the title
	function displayTitle($itemType, $itemLoopData, $contentData, $privilegeType, $previewURLPrefix) {
		if ($itemLoopData['published'] == "0") {
			echo "<td width=\"200\">";
			
			if ($itemLoopData['position'] == "1" && ($privilegeType == "Page" || $privilegeType == "External")) {
				echo "<span class=\"homePage\">" . commentTrim(25, $contentData['title']) . "</span>";
			} else {
				echo commentTrim(25, $contentData['title']);
			}
			
			echo "</td>";
		} else {
			echo "<td width=\"200\">";
			
			if ($itemLoopData['position'] == "1" && ($privilegeType == "Page" || $privilegeType == "External")) {
				echo "<span class=\"homePage\"><a href=\"";
				
				if ($itemType != "tab") {
					echo $previewURLPrefix . "?" . $itemType . "=" . $itemLoopData['id'] . "\"";
				} else {
					echo "javascript:void\" onclick=\"MM_openBrWindow('" . $previewURLPrefix . "?" . $itemType . "=" . sprintf($itemLoopData['position'] - 1) . "','','status=yes,scrollbars=yes,resizable=yes,width=320,height=240')\"";
				}
				
				echo " onmouseover=\"Tip('Preview the <strong>" . htmlentities($contentData['title']) . "</strong> " . $itemType . "')\" onmouseout=\"UnTip()\">" . commentTrim(25, $contentData['title']) . "</a></span>";
			} else {
				echo "<a href=\"";
				
				if ($itemType != "tab") {
					echo $previewURLPrefix . "?" . $itemType . "=" . $itemLoopData['id'] . "\"";
				} else {
					echo "javascript:void\" onclick=\"MM_openBrWindow('" . $previewURLPrefix . "?" . $itemType . "=" . sprintf($itemLoopData['position'] - 1) . "','','status=yes,scrollbars=yes,resizable=yes,width=320,height=240')\"";
				}
				
				echo " onmouseover=\"Tip('Preview the <strong>" . htmlentities($contentData['title']) . "</strong> " . $itemType . "')\" onmouseout=\"UnTip()\">" . commentTrim(25, $contentData['title']) . "</a>";
			}
			
			echo "</td>";
		}
		
	}
	
	//Display the content preview
	function displayContent($itemLoopData, $contentData, $privilegeType) {
		echo "<td>";
		
		if (empty($contentData['message'])) {
			echo commentTrim(75, $contentData['content']);
		} else {
			echo "<span class=\"alertNotAssigned\">" . commentTrim(75, $contentData['message']) . "</span>";
		}
		
		echo "</td>";
	}
	
	//Display the history icon
	function historyIcon($itemType, $itemLoopData, $contentData, $privilegeType) {
		if (privileges("publish" . $privilegeType) == "true") {
			echo "<th width=\"50\"><a class=\"action history\" href=\"javascript:void;\" onclick=\"MM_openBrWindow('history.php?id=" . $itemLoopData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,fullscreen=yes')\" onmouseover=\"Tip('View the <strong>" . htmlentities($contentData['title']) . "</strong> " . $itemType . "\'s history')\" onmouseout=\"UnTip()\"></a></th>";
		}
	}
	
	//Display the edit icon
	function editIcon($itemType, $itemLoopData, $contentData, $privilegeType, $editURL) {
		if (privileges("edit" . $privilegeType) == "true") {
			echo "<td width=\"50\"><a class=\"action edit\" href=\"" . $editURL . "?id=" . $itemLoopData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($contentData['title']) . "</strong> " . $itemType . "')\" onmouseout=\"UnTip()\"></a></td>";
		}
	}
	
	//Display the delete icon
	function deleteIcon($itemType, $itemLoopData, $contentData, $privilegeType, $editURL) {
		if (privileges("delete" . $privilegeType) == "true") {
			echo "<td width=\"50\"><a class=\"action delete\" href=\"" . $editURL . "?action=delete&id=" . $itemLoopData['id'] . "\" onclick=\"return confirm ('";
			
			if ($itemType == "page" && exist("pages", "parentPage", $itemLoopData['id'])) {
				echo "This action will delete this page, and all of its sub-pages. To preserve its subpages, please move them to a different parent page, then delete this page. This action cannot be undone. Continue?";
			} else {
				echo "This action cannot be undone. Continue?";
			}
			
			echo "');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($contentData['title']) . "</strong> " . $itemType . "')\" onmouseout=\"UnTip()\"></a></td>";
		}
	}
	
	//Display message updates
	function message($getParameter, $privilegeType, $messageItem) {
		if (isset ($_GET['added']) && $_GET['added'] == $getParameter) {
			if (privileges("publish" . $privilegeType) == "true" && $_SESSION['MM_UserGroup'] == "User") {
				successMessage("The " . $messageItem . " was successfully added");
			} elseif (privileges("publish" . $privilegeType) != "true" && $_SESSION['MM_UserGroup'] == "User") {
				successMessage("The " . $messageItem . " was successfully added. An administrator must approve the " . $messageItem . " before it can be displayed.");
			} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
				successMessage("The " . $messageItem . " was successfully added");
			}
		}
		
		if (isset ($_GET['updated']) && $_GET['updated'] == $getParameter) {
			if (privileges("publish" . $privilegeType) == "true" && $_SESSION['MM_UserGroup'] == "User") {
				successMessage("The " . $messageItem . " was successfully updated");
			} elseif (privileges("publish" . $privilegeType) != "true" && $_SESSION['MM_UserGroup'] == "User") {
				successMessage("The " . $messageItem . " was successfully updated. An administrator must approve the update before the " . $messageItem . " can be publicly displayed.");
			} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
				successMessage("The " . $messageItem . " was successfully updated");
			}
		}
		
		if (!isset($_GET['added']) && !isset($_GET['updated'])) {
			echo "<br />";
		}
	}
/* End table looping function */
	
/* Begin statistics tracker */
	//Set the activity meter
	function activity($setActivity = "false") {
		global $root;
		global $connDBA;
		
		if ($setActivity == "true" && isset($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			
			$activityTimestamp = time();
			mysql_query("UPDATE `users` SET `active` = '{$activityTimestamp}' WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		}
	}
	
	//Overall statistics
	function stats($doAction = "false", $publicSpace = "true") {
		global $root;
		global $connDBA;
		
		if ($doAction == "true") {
			$date = date("M-d-Y");
			
			if (exist("dailyhits", "date", $date)) {
				mysql_query("UPDATE `dailyhits` SET `hits` = `hits`+1 WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			} else {
				mysql_query("INSERT INTO `dailyhits` (
							`id`, `date`, `hits`
							) VALUES (
							NULL, '{$date}', '1'
							)", $connDBA);
			}
			
			if ($publicSpace == "true") {
				if (isset($_GET['page'])) {
					$page = $_GET['page'];
				} else {
					$pageDataGrabber = mysql_query("SELECT * FROM `pages` WHERE `position` = '1' LIMIT 1", $connDBA);
					
					if ($pageData = mysql_fetch_array($pageDataGrabber)) {
						$page = $pageData['id'];
					}
				}
				
				if (isset($page)) {
					$settings = query("SELECT * FROM `privileges`");
					$pagePublished = query("SELECT * FROM `pages` WHERE `id` = '{$page}' LIMIT 1");
					
					if ($pagePublished['published'] != "0") {
						if (exist("pages", "id", $page)) {
							if (exist("pagehits", "page", $page)) {
								mysql_query("UPDATE `pagehits` SET `hits` = `hits`+1 WHERE `page` = '{$page}' LIMIT 1", $connDBA);
							} else {
								mysql_query("INSERT INTO `pagehits` (
											`id`, `page`, `hits`
											) VALUES (
											NULL, '{$page}', '1'
											)", $connDBA);
							}
						}
					}
				}
			}
		}
	}
/* End statistics tracker */

//Force user to change password if required
	if (isset($_SESSION['MM_Username'])) {
		$userName = $_SESSION['MM_Username'];
		
		$userDataGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}'", $connDBA);
		$userData = mysql_fetch_array($userDataGrabber);
		$URL = $_SERVER['REQUEST_URI'];
		
		if ($userData['changePassword'] == "on" && !strstr($URL, "logout.php")) {
		//Process the form
			if (isset ($_POST['submitPassword']) && !empty($_POST['oldPassword']) && !empty($_POST['newPassword']) && !empty($_POST['confirmPassword'])) {
				$oldPassword = encrypt($_POST['oldPassword']);
				$newPassword = encrypt($_POST['newPassword']);
				$confirmPassword = encrypt($_POST['confirmPassword']);
				$passwordGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' AND `passWord` = '{$oldPassword}'", $connDBA);
				$password = mysql_fetch_array($passwordGrabber);
				
				if ($password && $newPassword === $confirmPassword) {
					if ($password['passWord'] != $newPassword) {
						mysql_query("UPDATE `users` SET `passWord` = '{$newPassword}', `changePassword` = '' WHERE `userName` = '{$userName}' AND `passWord` = '{$oldPassword}'", $connDBA);
						
						header("Location: " . $root . "admin/index.php");
						exit;
					} else {
						header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?password=identical");
						exit;
					}
				} else {
					header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "?password=error");
					exit;
				}
			}
			
		//Display the content	
			echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head>";
			title("Change Password");
			headers();
			validate();
			echo "</head><body>";
			topPage();
			
			echo "<form name=\"updatePassword\" id=\"validate\" action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\" onsubmit=\"return errorsOnSubmit(this)\"><h2>Change Password</h2><p>You are required to change your password before using this site.</p>";
			
			if (isset($_GET['password']) && $_GET['password'] == "error") {
				errorMessage("Either your old password is incorrect, or your new password does not match.");
			} elseif (isset($_GET['password']) && $_GET['password'] == "identical") { 
				errorMessage("Your old password may not be the same as your new password.");
			} else {
				echo "<p>&nbsp;</p>";
			}
			
			echo "<blockquote><p>Current password<span class=\"require\">*</span>:</p><blockquote><input type=\"password\" name=\"oldPassword\" id=\"oldPassword\" size=\"50\" autocomplete=\"off\" class=\"validate[required]\" /></blockquote><p>New password<span class=\"require\">*</span>:</p><blockquote><input type=\"password\" name=\"newPassword\" id=\"newPassword\" size=\"50\" autocomplete=\"off\" class=\"validate[required,length[6,30]]\" /></blockquote><p>Confirm new password<span class=\"require\">*</span>:</p><blockquote><input type=\"password\" name=\"confirmPassword\" id=\"confirmPassword\" size=\"50\" autocomplete=\"off\" class=\"validate[required,length[6,30],confirm[newPassword]]\" /><p>&nbsp;</p><p></blockquote><input type=\"submit\" name=\"submitPassword\" id=\"submitPassword\" value=\"Submit\" /></p>";
			formErrors();
			echo "</blockquote></form>";
			footer();
			echo "</body></html>";
			exit;
		}
	}
?>