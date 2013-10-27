<?php
//Header functions
	require_once('../../../Connections/connDBA.php');
	
//Check access to this page
	$tab = access("external", "External", "index.php");
	
//Process the form
	process("external", "External", "index.php", "tab"); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($tab)) {
		$title = "Edit the " . prepare($tab['title']) . " Tab";
	} else {
		$title =  "Create a New Tab";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
      
    <h2>
      <?php if (isset ($tab)) {echo "Edit the \"" . $tab['title'] . "\" Tab";} else {echo "Create New Tab";} ?>
    </h2>
<p>Use this page to <?php if (isset ($tab)) {echo "edit the content of &quot;<strong>" . $tab['title'] . "</strong>&quot;";} else {echo "create a new tab";} ?>.</p>
<?php
//Let users know an update is pending if one is pending
	if (isset ($tab) && !isset($_GET['content'])) {
		if (($tab['published'] == "1" || $item['published'] == "0") && privileges("publishExternal") != "true" && empty($tab['message'])) {
			alert("<p>This tab is awaiting approval.</p>");
		} elseif (($tab['published'] == "1" || $tab['published'] == "0") && privileges("publishExternal") == "true" && empty($tab['message'])) {
			alert("<p>This tab is awaiting approval. You may <a href=\"index.php\" onclick=\"MM_openBrWindow('approve.php?id=" . $_GET['id'] . "','','status=yes,scrollbars=yes,resizable=yes,fullscreen=yes')\">approve this version</a> if you do not have any changes to make. Changes made to this version will be published.</p>");
		} else {
			echo "<p>&nbsp;</p>";
		}
		
		if (!empty($tab['message'])) {
			alert($tab['message']);
		}
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="manageTab" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The text that will display in big letters on the top-left of each tab')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($tab)) {
					echo " value=\"" . htmlentities($tab['title']) . "\"";
				}
			?> />
          </p>
        </blockquote>
        <p>Content<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the tab')" onmouseout="UnTip()" /> </p>
        <blockquote>
        <p>
            <textarea name="content" id="content1" cols="45" rows="5" style="width:640px; height:320px;" class="validate[required]" /><?php 
				if (isset ($tab)) {
					echo stripslashes($tab['content']);
				}
			?></textarea>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Finish</div>
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
<?php footer(); ?>
</body>
</html>
