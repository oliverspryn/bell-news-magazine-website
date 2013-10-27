<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("sendEmail") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Process the form
	if (isset ($_POST['submit']) && !empty ($_POST['from']) && !empty ($_POST['toImport']) && !empty ($_POST['subject']) && !empty ($_POST['priority']) && !empty ($_POST['message'])) {
	//Get all of the form fields
		$from = $_POST['from'];
		$toDetirmine = $_POST['toDetirmine'];
		$toImport = $_POST['toImport'];
		$subject = stripslashes($_POST['subject']);
		$priority = $_POST['priority'];
		$body = "<html><head><title>" . $subject . "</title></head><body>" . stripslashes($_POST['message']) . "</body></html>";
		//$body = str_ireplace("\"" . $strippedRoot, "\"" . $root, $bodyGrabber);
		
	//Select the site name to conceal the "to" list
		$siteNameGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
		$siteName = mysql_fetch_array($siteNameGrabber);
		
	//Detirmine what kind of mass email is being sent
		if ($toDetirmine == "users") {
			$to = $toImport;
		} elseif ($toDetirmine == "all") {
			$toGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
			$to = "";
			
			while($toData = mysql_fetch_array($toGrabber)) {
				$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
			}
		} elseif ($toDetirmine == "roles") {
			$toArray = explode(",", $toImport);
			$toArraySize = sizeof($toArray);
			$to = "";
			
			for ($count = 0; $count <= $toArraySize; $count++) {
				$role = $toArraySize[$count];
				$toGrabber = mysql_query("SELECT * FROM `users` WHERE `role` = '{$role}'", $connDBA);
				
				while($toData = mysql_fetch_array($toGrabber)) {
					$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
				}
			}
		} else {
			redirect($_SERVER['PHP_SELF']);
		}
		
	//Generate the header
		$random = md5(time());
		$mimeBoundary = "==Multipart_Boundary_x{$random}x";
		
		$header = "From: " . $from . "\n" .
					  "Reply-To: " . $from . "\n"  .
					  "X-Mailer: PHP/" . phpversion() . "\n" .
					  "X-Priority: " . $priority . "\n" .
					  "MIME-Version: 1.0\n" .
					  "Content-Type: multipart/mixed;\n" .
					  " boundary=\"{$mimeBoundary}\"";
				  
		//The message of the email
			$message = "--{$mimeBoundary}\n" .
					   "Content-Type: text/html; charset=\"iso-8859-1\"\n" .
					   "Content-Transfer-Encoding: 7bit\n\n" .
					   $body . "\n\n";
		
		if (is_uploaded_file($_FILES['attachment']['tmp_name'])) {
			//Grab the attachment
				$fileTempName = $_FILES['attachment']['tmp_name'];
				$fileType = $_FILES['attachment']['type'];
				$fileName = basename($_FILES['attachment'] ['name']);	
			
			//Grab the attachment info
				$file = fopen($fileTempName, 'rb');
				$data = fread($file, filesize($fileTempName));
				fclose ($file);	
				
			//Processing			
				$data = chunk_split(base64_encode($data));
				$message .= "--{$mimeBoundary}\n" .
							"Content-Type: {$fileType};\n" . 
							" name = \"{$fileName}\"\n" . 
							"Content-Transfer-Encoding: base64\n\n" . 
							$data . "\n\n" .    
							"--{$mimeBoundary}--\n";
		} else {
			$message .= "--{$mimeBoundary}\n" .
						"Content-Type:  text/html;\n" . 
						" name = \"attachment.html\"\n" . 
						"Content-Transfer-Encoding: base64\n\n" . 
						chunk_split(base64_encode("<!--placeholder//-->")) . "\n\n" .    
						"--{$mimeBoundary}--\n";
		}
		
	//Processor
		$mailTo = explode(",", trim($to, ","));
		
		foreach ($mailTo as $to) {
			mail($to, $subject, $message, $header);
		}
		
	//Redirect
		header("Location: index.php?email=success");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
<head>
<?php title("Send an Email"); ?>
<?php headers(); ?>
<?php validate(); ?>
<?php tinyMCEAdvanced(); ?>
<script src="../../javascripts/common/optionTransfer.js" type="text/javascript"></script>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?><?php if (isset($_GET['type']) && !isset($_GET['id'])) {if ($_GET['type'] == "user" || $_GET['type'] == "roles") {echo " onload=\"opt.init(document.forms[0])\"";}} ?>>
<?php topPage(); ?>
<h2>Send an Email</h2>
<p>Send an email to multiple users, or organizations within this system. Please note that this is not an online email system. This is only used to send a mass email.</p>
<p>&nbsp;</p>
<?php
//If the type of user is being selected
	if (!isset($_GET['type']) && !isset($_GET['id'])) {
		echo "<blockquote><p><a href=\"send_email.php?type=user\">Selected Users</a> - Only selected users will recieve this email<br /><a href=\"send_email.php?type=all\">All Users</a> - All registered users will recieve this email<br /><a href=\"send_email.php?type=roles\">Selected Roles</a> - All users with a selected role will recieve this email</p></blockquote>";
	} else {
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" name="sendEmail" id="validate" onsubmit="return errorsOnSubmit(this, true, 'attachment', false);">
<div class="catDivider one">Settings</div>
<div class="stepContent">
<blockquote>
  <p>From:</p>
  <blockquote>
    <?php
	//Select the from email address
		$userName = $_SESSION['MM_Username'];
		$fromGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		$from = mysql_fetch_array($fromGrabber);
		
		if ($from['emailAddress2'] == "" && $from['emailAddress3'] == "") {
			echo "<input type=\"hidden\" name=\"from\" id=\"from\" value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress1'] . ">\" /><p><strong>" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress1'] . "&gt;</strong></p>";
		} else {
			echo "<select name=\"from\" id=\"from\"><option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress1'] . ">\" selected=\"selected\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress1'] . "&gt;</option>";
			
			if ($from['emailAddress2'] != "") {
				echo "<option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress2'] . ">\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress2'] . "&gt;</option>";
			}
			
			if ($from['emailAddress3'] != "") {
				echo "<option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress3'] . ">\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress3'] . "&gt;</option>";
			}
			
			echo "</select>";
		}
	?>
  </blockquote>
  <p>To<?php if (isset($_GET['type']) && !isset($_GET['id'])) {if ($_GET['type'] == "user" || $_GET['type'] == "organization" || $_GET['type'] == "roles") {echo "<span class=\"require\">*</span>";}} ?>:</p>
  <blockquote>
    <?php
    //Grab all required values
		if (isset($_GET['type'])) {
			switch ($_GET['type']) {
				case "user" : 
					if (isset($_GET['id'])) {
						$id = $_GET['id'];
						$emailCheckGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$id}'", $connDBA); 
						
						if ($emailCheck = mysql_fetch_array($emailCheckGrabber)) {
							if ($emailCheck['emailAddress2'] == "" && $emailCheck['emailAddress3'] == "") {
								echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"users\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " <" . $emailCheck['emailAddress1'] . ">\" /><p><strong>" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " &lt;" . $emailCheck['emailAddress1'] . "&gt;</strong></p>";
							} else {
								echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"users\" /><select name=\"toImport\" id=\"toImport\"><option value=\"" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " <" . $emailCheck['emailAddress1'] . ">\""; if (isset($_GET['address']) && $_GET['address'] == "1") {echo " selected=\"selected\"";} echo ">" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " &lt;" . $emailCheck['emailAddress1'] . "&gt;</option>";
								
								if ($emailCheck['emailAddress2'] != "") {
									echo "<option value=\"" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " <" . $emailCheck['emailAddress2'] . ">\""; if (isset($_GET['address']) && $_GET['address'] == "2") {echo " selected=\"selected\"";} echo ">" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " &lt;" . $emailCheck['emailAddress2'] . "&gt;</option>";
								}
								
								if ($emailCheck['emailAddress3'] != "") {
									echo "<option value=\"" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " <" . $emailCheck['emailAddress3'] . ">\""; if (isset($_GET['address']) && $_GET['address'] == "3") {echo " selected=\"selected\"";} echo ">" . $emailCheck['firstName'] . " " . $emailCheck['lastName'] . " &lt;" . $emailCheck['emailAddress3'] . "&gt;</option>";
								}
								
								echo "</select>";
							}
						} else {
							header("Location: send_email.php");
							exit;
						}
					} else {
						$usersGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
						
						echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"users\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential users:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notToList\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\">";
						while($users = mysql_fetch_array($usersGrabber)) {
							echo "<option value=\"" . $users['firstName'] . " " . $users['lastName'] . " <" . $users['emailAddress1'] . ">\">" . $users['firstName'] . " " . $users['lastName'] . "</option>";
						}
						echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected users:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\" readonly=\"readonly\"></div><div align=\"center\"><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\"></select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>";
					}
					break;
					
				case "all" : echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"all\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"all\" /><p><strong>This email will be sent to all registered users.</strong></p>"; break;
				
				case "organization" : 
					if (isset($_GET['id'])) {
						$id = $_GET['id'];
						$emailCheckGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$id}'", $connDBA); 
						
						if ($emailCheck = mysql_fetch_array($emailCheckGrabber)) {
							echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"organizations\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"" . $emailCheck['organization'] . " <" . $emailCheck['billingEmail'] . ">\" /><p><strong>" . $emailCheck['organization'] . " &lt;" . $emailCheck['billingEmail'] . "&gt;</strong></p>";
						} else {
							header("Location: send_email.php");
							exit;
						}
					} else {
						$organizationsGrabber = mysql_query("SELECT * FROM `organizations` ORDER BY `organization` ASC", $connDBA);
						
						echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"organizations\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential organizations:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notToList\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\">";
						while($organizations = mysql_fetch_array($organizationsGrabber)) {
							echo "<option value=\"" . $organizations['organization'] . "\">" . $organizations['organization'] . "</option>";
						}
						echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected organizations:</h3><div align=\"center\"><div class=\"collapseElement\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\" readonly=\"readonly\"></div><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\"></select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>";
					}
					break;
					
				case "allOrganizations" : echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"allOrganizations\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"allOrganizations\" /><p><strong>This email will be sent to all registered organizations.</strong></p>"; break;
				
				case "roles" : 
					echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"roles\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential roles:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notToList\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\"><option value=\"Administrator\">Administrators</option><option value=\"User\">Users</option></select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected roles:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\" readonly=\"readonly\"></div><div align=\"center\"><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\"></select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>"; break;
			}
		}
    ?>
    </blockquote>
  <p>Subject<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <input name="subject" type="text" id="subject" autocomplete="off" size="50" class="validate[required]" />
    </p>
    </blockquote>
  <p>Priority:</p>
  <blockquote>
    <p>
      <select name="priority" id="priority">
        <option value="5">Low</option>
        <option value="3" selected="selected">Normal</option>
        <option value="1">High</option>
        </select>
    </p>
  </blockquote>
</blockquote>
</div>
<div class="catDivider two">Message</div>
<div class="stepContent">
  <blockquote>
    <p>
      Enter the message of the email below<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <textarea name="message" id="message" cols="45" rows="5" style="width:640px; height:320px;" /></textarea>
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider three">Attachment</div>
<div class="stepContent">
  <blockquote>
      <p>Add an attachment:</p>
      <blockquote>
      	<input type="file" name="attachment" id="attachment" size="50" />
      </blockquote>
  </blockquote>
</div>
<div class="catDivider four">Submit</div>
<div class="stepContent">
  <blockquote>
    <p>
      <?php submit("submit", "Submit"); ?>
      <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
      <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
    </p>
    <?php formErrors(); ?>
  </blockquote>
</div>
</form>
<?php
	}
?>
<?php footer(); ?>
</body>
</html>