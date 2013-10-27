<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Grab all data
	$privilegesGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$privileges = mysql_fetch_array($privilegesGrabber);
	
//Process the form
	if (isset($_POST['submit'])) {
		$uploadFile = $_POST['uploadFile'];
		$deleteFile = $_POST['deleteFile'];
		$deleteForumComments = $_POST['deleteForumComments'];
		$sendEmail = $_POST['sendEmail'];
		$viewStaffPage = $_POST['viewStaffPage'];
		$createStaffPage = $_POST['createStaffPage'];
		$editStaffPage = $_POST['editStaffPage'];
		$deleteStaffPage = $_POST['deleteStaffPage'];
		$publishStaffPage = $_POST['publishStaffPage'];
		$addStaffComments = $_POST['addStaffComments'];
		$deleteStaffComments = $_POST['deleteStaffComments'];
		$createPage = $_POST['createPage'];
		$editPage = $_POST['editPage'];
		$deletePage = $_POST['deletePage'];
		$publishPage = $_POST['publishPage'];
		$deleteComments = $_POST['deleteComments'];
		$siteSettings = $_POST['siteSettings'];
		$createSideBar = $_POST['createSideBar'];
		$editSideBar = $_POST['editSideBar'];
		$deleteSideBar = $_POST['deleteSideBar'];
		$publishSideBar = $_POST['publishSideBar'];
		$sideBarSettings = $_POST['sideBarSettings'];
		$createExternal = $_POST['createExternal'];
		$editExternal = $_POST['editExternal'];
		$deleteExternal = $_POST['deleteExternal'];
		$publishExternal = $_POST['publishExternal'];
		$deleteComments = $_POST['deleteComments'];
		$viewStatistics = $_POST['viewStatistics'];
		$autoEmail = $_POST['autoEmail'];
		
		mysql_query("UPDATE `privileges` SET `uploadFile` = '{$uploadFile}', `deleteFile` = '{$deleteFile}', `deleteForumComments` = '{$deleteForumComments}', `sendEmail` = '{$sendEmail}', `viewStaffPage` = '{$viewStaffPage}', `createStaffPage` = '{$createStaffPage}', `editStaffPage` = '{$editStaffPage}', `deleteStaffPage` = '{$deleteStaffPage}', `publishStaffPage` = '{$publishStaffPage}', `addStaffComments` = '{$addStaffComments}', `deleteStaffComments` = '{$deleteStaffComments}', `createPage` = '{$createPage}', `editPage` = '{$editPage}', `deletePage` = '{$deletePage}', `publishPage` = '{$publishPage}', `deleteComments` = '{$deleteComments}', `siteSettings` = '{$siteSettings}', `createSideBar` = '{$createSideBar}', `editSideBar` = '{$editSideBar}', `deleteSideBar` = '{$deleteSideBar}', `publishSideBar` = '{$publishSideBar}', `sideBarSettings` = '{$sideBarSettings}',`createExternal` = '{$createExternal}', `editExternal` = '{$editExternal}', `deleteExternal` = '{$deleteExternal}', `publishExternal` = '{$publishExternal}', `deleteComments` = '{$deleteComments}', `viewStatistics` = '{$viewStatistics}', `autoEmail` = '{$autoEmail}' WHERE `id` = '1'", $connDBA);
		
		header("Location: index.php?updated=privileges");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Assign User Privileges"); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<h2>Assign User Privileges</h2>
<p>Users may be assigned different privileges within this site. Note that changing these settings will not affect the Administrators' privileges.</p>
<p>&nbsp;</p>
<form name="privileges" id="privileges" action="privileges.php" method="post">
<div class="catDivider alignLeft">Collaboration</div>
<div class="stepContent">
  <blockquote>
    <p>Delete files: 
      <label>
      <input type="radio" name="deleteFile" value="1" id="deleteFile_0"<?php
		  if ($privileges['deleteFile'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes</label>
    <label>
      <input type="radio" name="deleteFile" value="0" id="deleteFile_1"<?php
		  if ($privileges['deleteFile'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      No</label>
  </p>
    <p>Upload files:
      <label>
      <input type="radio" name="uploadFile" value="1" id="uploadFile_0"<?php
		  if ($privileges['uploadFile'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
Yes
</label>
<label>
  <input type="radio" name="uploadFile" value="0" id="uploadFile_1"<?php
		  if ($privileges['uploadFile'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
  No</label>
</p>
    <p>Delete forum comments:
      <label>
        <input type="radio" name="deleteForumComments" value="1" id="deleteForumComments_0"<?php
		  if ($privileges['deleteForumComments'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        Yes </label>
      <label>
        <input type="radio" name="deleteForumComments" value="0" id="deleteForumComments_1"<?php
		  if ($privileges['deleteForumComments'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        No</label>
    </p>
    <p>Send email:
      <label>
        <input type="radio" name="sendEmail" value="1" id="sendEmail_0"<?php
		  if ($privileges['sendEmail'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        Yes </label>
      <label>
        <input type="radio" name="sendEmail" value="0" id="sendEmail_1"<?php
		  if ($privileges['sendEmail'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        No</label>
<br />
    </p>
  </blockquote>
</div>
<div class="catDivider alignLeft">Staff Pages</div>
<div class="stepContent">
  <blockquote>
    <p>Users can publish staff pages:
      <label>
        <input type="radio" name="publishStaffPage" value="1" id="publishStaffPage_0"<?php
		  if ($privileges['publishStaffPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        Yes </label>
      <label>
        <input type="radio" name="publishStaffPage" value="0" id="publishStaffPage_1"<?php
		  if ($privileges['publishStaffPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        No</label>
    </p>
    <p>View staff pages:
      <label>
      <input type="radio" name="viewStaffPage" value="1" id="viewStaffPage_0"<?php
		  if ($privileges['viewStaffPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="viewStaffPage" value="0" id="viewStaffPage_1"<?php
		  if ($privileges['viewStaffPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Create staff page:
      <label>
      <input type="radio" name="createStaffPage" value="1" id="createStaffPage_0"<?php
		  if ($privileges['createStaffPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="createStaffPage" value="0" id="createStaffPage_1"<?php
		  if ($privileges['createStaffPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
     <p>Edit staff page:
      <label>
      <input type="radio" name="editStaffPage" value="1" id="editStaffPage_0"<?php
		  if ($privileges['editStaffPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
Yes
</label>
<label>
  <input type="radio" name="editStaffPage" value="0" id="editStaffPage_1"<?php
		  if ($privileges['editStaffPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
  No</label>
    </p>
    <p>Delete staff page:
      <label>
      <input type="radio" name="deleteStaffPage" value="1" id="deleteStaffPage_0"<?php
		  if ($privileges['deleteStaffPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
Yes
</label>
<label>
  <input type="radio" name="deleteStaffPage" value="0" id="deleteStaffPage_1"<?php
		  if ($privileges['deleteStaffPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
  No</label>
    </p>
    <p>Add staff page comments (if page allows):
      <label>
      <input type="radio" name="addStaffComments" value="1" id="addStaffComments_0"<?php
		  if ($privileges['addStaffComments'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
Yes
</label>
<label>
  <input type="radio" name="addStaffComments" value="0" id="addStaffComments_1"<?php
		  if ($privileges['addStaffComments'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
  No</label>
  </p>
    <p>Delete staff page comments:
      <label>
      <input type="radio" name="deleteStaffComments" value="1" id="deleteStaffComments_0"<?php
		  if ($privileges['deleteStaffComments'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
Yes
</label>
<label>
  <input type="radio" name="deleteStaffComments" value="0" id="deleteStaffComments_1"<?php
		  if ($privileges['deleteStaffComments'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
  No</label>
    </p>
</blockquote>
</div>
<div class="catDivider alignLeft">Public Website</div>
<div class="stepContent">
  <blockquote>
    <p>Users can publish pages:
      <label>
        <input type="radio" name="publishPage" value="1" id="publishPage_0"<?php
		  if ($privileges['publishPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        Yes </label>
      <label>
        <input type="radio" name="publishPage" value="0" id="publishPage_1"<?php
		  if ($privileges['publishPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        No</label>
    </p>
    <p>Create page:
      <label>
      <input type="radio" name="createPage" value="1" id="createPage_0"<?php
		  if ($privileges['createPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="createPage" value="0" id="createPage_1"<?php
		  if ($privileges['createPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Edit page:
      <label>
      <input type="radio" name="editPage" value="1" id="editPage_0"<?php
		  if ($privileges['editPage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="editPage" value="0" id="editPage_1"<?php
		  if ($privileges['editPage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Delete page:
      <label>
      <input type="radio" name="deletePage" value="1" id="deletePage_0"<?php
		  if ($privileges['deletePage'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="deletePage" value="0" id="deletePage_1"<?php
		  if ($privileges['deletePage'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Delete page comments:
      <label>
      <input type="radio" name="deleteComments" value="1" id="deleteComments_0"<?php
		  if ($privileges['deleteComments'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="deleteComments" value="0" id="deleteComments_1"<?php
		  if ($privileges['deleteComments'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
</p>
    <p>Manage site settings:
      <label>
      <input type="radio" name="siteSettings" value="1" id="siteSettings_0"<?php
		  if ($privileges['siteSettings'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="siteSettings" value="0" id="siteSettings_1"<?php
		  if ($privileges['siteSettings'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
  </blockquote>
</div>
<div class="catDivider alignLeft">Sidebar</div>
<div class="stepContent">
  <blockquote>
    <p>Users can publish boxes:
      <label>
        <input type="radio" name="publishSideBar" value="1" id="publishSideBar_0"<?php
		  if ($privileges['publishSideBar'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        Yes </label>
      <label>
        <input type="radio" name="publishSideBar" value="0" id="publishSideBar_1"<?php
		  if ($privileges['publishSideBar'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        No</label>
    </p>
    <p>Create box:
      <label>
        <input type="radio" name="createSideBar" value="1" id="createSideBar_0"<?php
		  if ($privileges['createSideBar'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        Yes
      </label>
      <label>
        <input type="radio" name="createSideBar" value="0" id="createSideBar_1"<?php
		  if ($privileges['createSideBar'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        No</label>
    </p>
    <p>Edit box:
      <label>
      <input type="radio" name="editSideBar" value="1" id="editSideBar_0"<?php
		  if ($privileges['editSideBar'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="editSideBar" value="0" id="editSideBar_1"<?php
		  if ($privileges['editSideBar'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Delete box:
      <label>
      <input type="radio" name="deleteSideBar" value="1" id="deleteSideBar_0"<?php
		  if ($privileges['deleteSideBar'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="deleteSideBar" value="0" id="deleteSideBar_1"<?php
		  if ($privileges['deleteSideBar'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Manage sidebar settings:
      <label>
      <input type="radio" name="sideBarSettings" value="1" id="sideBarSettings_0"<?php
		  if ($privileges['sideBarSettings'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="sideBarSettings" value="0" id="sideBarSettings_1"<?php
		  if ($privileges['sideBarSettings'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
</p>
  </blockquote>
</div>
<div class="catDivider alignLeft">External Content</div>
<div class="stepContent">
  <blockquote>
    <p>Users can publish content:
      <label>
        <input type="radio" name="publishExternal" value="1" id="publishExternal_0"<?php
		  if ($privileges['publishExternal'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        Yes </label>
      <label>
        <input type="radio" name="publishExternal" value="0" id="publishExternal_1"<?php
		  if ($privileges['publishExternal'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
        No</label>
    </p>
    <p>Create content:
      <label>
      <input type="radio" name="createExternal" value="1" id="createExternal_0"<?php
		  if ($privileges['createExternal'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="createExternal" value="0" id="createExternal_1"<?php
		  if ($privileges['createExternal'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Edit content:
      <label>
      <input type="radio" name="editExternal" value="1" id="editExternal_0"<?php
		  if ($privileges['editExternal'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="editExternal" value="0" id="editExternal_1"<?php
		  if ($privileges['editExternal'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
    <p>Delete content:
      <label>
      <input type="radio" name="deleteExternal" value="1" id="deleteExternal_0"<?php
		  if ($privileges['deleteExternal'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="deleteExternal" value="0" id="deleteExternal_1"<?php
		  if ($privileges['deleteExternal'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
  </blockquote>
</div>
<div class="catDivider alignLeft">Statistics</div>
<div class="stepContent">
<blockquote>
	<p>View statistics:
      <label>
      <input type="radio" name="viewStatistics" value="1" id="viewStatistics_0"<?php
		  if ($privileges['viewStatistics'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
      Yes
  </label>
  <label>
    <input type="radio" name="viewStatistics" value="0" id="viewStatistics_1"<?php
		  if ($privileges['viewStatistics'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
    </p>
  </blockquote>
</div>
<div class="catDivider alignLeft">Automated Emails</div>
<div class="stepContent">
<blockquote>Administrators recieve system email notifications:
  <label>
    <input type="radio" name="autoEmail" value="1" id="autoEmail_0"<?php
		  if ($privileges['autoEmail'] == "1") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    Yes </label>
  <label>
    <input type="radio" name="autoEmail" value="0" id="autoEmail_1"<?php
		  if ($privileges['autoEmail'] == "0") {
			  echo " checked=\"checked\"";
		  }
	  ?> />
    No</label>
</blockquote>
</div>
<div class="catDivider alignLeft">Submit</div>
<div class="stepContent">
<blockquote>
	<p><?php submit("submit", "Submit"); ?></p>
</blockquote>
</div>
</form>
<?php footer(); ?>
</body>
</html>