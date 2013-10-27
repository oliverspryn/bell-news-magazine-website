<?php
//Header functions
	require_once('../../Connections/connDBA.php');
	
//Check to see if the this script is set to run
	$settings = query("SELECT * FROM `privileges` WHERE `id` = '1'");
	
//Script to email administrators when content requires approval
	if (isset($_GET['cron']) && $_GET['cron'] == "automatic" && $settings['autoEmail'] == "1") {
	//Start building the message of the email
		$message = "<html><head><title>Automated System Update</title><style type=\"text/css\">p.homeDivider {width:100%; border-bottom:medium #999 solid; font-size:15px; font-weight:bolder; padding-bottom:3px;}</style></head><body>";
				
	//Create a function to be used by multiple types of validation
		function checkContent($table, $title, $type, $link) {
		//Count the number of new content to approve
			$count = 0;
			
		//Grab the content information
			$itemGrabber = query("SELECT * FROM `{$table}` WHERE `published` = '0' OR `published` = '1'", "raw");
			
			if (query("SELECT * FROM `{$table}` WHERE `published` = '0' OR `published` = '1'", "raw")) {
				while ($item = mysql_fetch_array($itemGrabber)) {
				//If updated content does not have any messages, then this script must be approved
					$itemDetails = unserialize($item['content' . $item['display']]);
					
					if (empty($itemDetails['message'])) {
						$count++;
					}
				}
			}
			
		//If the $count variable is greater than zero, then there is at least one page to approve
			if ($count > 0) {
				$return = "<p class=\"homeDivider\">" . $title . "</p><p>There are <strong>" . $count . "</strong> " . $type . " that need approval. Please click this link to begin the approval process: <br /><br /><a href=\"" .  $link . "\" target=\"_blank\">" . $link . "</a></p>";
		//If the $count variable is equal to 0, then there is no content in this category to approve
			} else {
				$return = false;
			}
			
		//Return the value
			return $return;
		}
		
	//Check for unapproved staff pages
		$staffPages = checkContent("staffpages", "Staff Pages Overview", "staff pages", $root . "admin/pages/index.php");
		
	//Check for unapproved public pages
		$pages = checkContent("pages", "Public Website Overview", "pages", $root . "admin/cms/index.php");
		
	//Check for unapproved sidebar items
		$sidebar = checkContent("sidebar", "Sidebar Items Overview", "sidebar items", $root . "admin/cms/sidebar/index.php");
		
	//Check for unapproved external content tabs
		$external = checkContent("external", "External Content Overview", "external content tabs", $root . "admin/cms/external/index.php");
		
	//If any of these returned a value, then send the email
		if ($staffPages != false || $pages != false || $sidebar != false || $external != false) {
		//Finish generating the message
			$message .= $staffPages . $pages . $sidebar . $external . "<br /><br /><hr /><p>This is an automated message. Please do not reply to this email.</p></body></html>";
				
		//Grab the site name for use in the $from and $subject variables
			$siteName = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
			$subject = prepare($siteName['siteName']) . " - Content Requires Your Attention";
			$from = prepare($siteName['siteName']) . "<no-reply@" . $_SERVER['HTTP_HOST'] . ">";
		
		//Generate the header
			$random = md5(time());
			$mimeBoundary = "==Multipart_Boundary_x{$random}x";
			
			$header = "From: " . $from . "\n" .
					  "Reply-To: " . $from . "\n"  .
					  "X-Mailer: PHP/" . phpversion() . "\n" .
					  "X-Priority: 3\n" .
					  "MIME-Version: 1.0\n" .
					  "Content-Type: multipart/mixed;\n" .
					  " boundary=\"{$mimeBoundary}\"";
					  
		//Generate the message body of the email
			$body = "--{$mimeBoundary}\n" .
					"Content-Type: text/html; charset=\"iso-8859-1\"\n" .
					"Content-Transfer-Encoding: 7bit\n\n" .
					$message . "\n\n";
			$body .= "--{$mimeBoundary}\n" .
					"Content-Type:  text/html;\n" . 
					" name = \"{$fileName}\"\n" . 
					"Content-Transfer-Encoding: base64\n\n" . 
					chunk_split(base64_encode("<!--placeholder//-->")) . "\n\n" .    
					"--{$mimeBoundary}--\n";
					
		//Send the email to all administrators
			$administrators = query("SELECT * FROM `users` WHERE `role` = 'Administrator'", "raw");
			
			while($to = mysql_fetch_array($administrators)) {
				mail(prepare($to['firstName']) . " " . prepare($to['lastName']) . "<" . $to['emailAddress1'] . ">", $subject, $body, $header);
			}
			
			exit;
		}
	}
?>