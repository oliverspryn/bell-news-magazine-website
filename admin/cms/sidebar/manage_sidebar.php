<?php
//Header functions
	require_once('../../../Connections/connDBA.php');

//Check access to this page
	$item = access("sidebar", "SideBar", "index.php");
	
//Process the form
	process("sidebar", "SideBar", "index.php", "item");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($item)) {
		$title = "Edit the " . $item['title'] . " Box";
	} else {
		$title =  "Create a New Box";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/common/showHide.js" type="text/javascript"></script>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
      
    <h2>
      <?php if (isset ($item)) {echo "Edit the \"" . $item['title'] . "\" Box";} else {echo "Create New Box";} ?>
    </h2>
<p>Use this page to <?php if (isset ($item)) {echo "edit the content of the \"<strong>" . $item['title'] . "</strong>\" box";} else {echo "create a new box";} ?>.</p>
<?php
//Let users know an update is pending if one is pending
	if (isset ($item) && !isset($_GET['content'])) {
		if (($item['published'] == "1" || $item['published'] == "0") && privileges("publishSideBar") != "true" && empty($item['message'])) {
			alert("<p>This box is awaiting approval.</p>");
		} elseif (($item['published'] == "1" || $item['published'] == "0") && privileges("publishSideBar") == "true" && empty($item['message'])) {
			alert("<p>This box is awaiting approval. You may <a href=\"index.php\" onclick=\"MM_openBrWindow('approve.php?id=" . $_GET['id'] . "','','status=yes,scrollbars=yes,resizable=yes,fullscreen=yes')\">approve this version</a> if you do not have any changes to make. Changes made to this version will be published.</p>");
		} else {
			echo "<p>&nbsp;</p>";
		}
		
		if (!empty($item['message'])) {
			alert($item['message']);
		}
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="manageItem" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The text that will display on the top-left of each box')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($item)) {
					echo " value=\"" . $item['title'] . "\"";
				}
			?> />
          </p>
        </blockquote>
        <p>Type<?php if (!isset($item)) {echo "<span class=\"require\">*</span>";} ?>: <img src="../../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The type of content that will be displayed in the text box.<br />Different ones will be avaliable at different times, <br />depending on their current use.<br /><br /><strong>Custom Content</strong> - A box which can contain any desired content.<br /><strong>Login</strong> - A box with a pre-built form to log in a user.')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
          <?php
		  //Grab the item type
			  if (isset($_GET['id'])) {
				  $itemType = query("SELECT * FROM `sidebar` WHERE `id` = {$_GET['id']}");
			  }
		  ?>
          
              <select name="type" id="type">
                <option value="Custom Content"<?php if (isset($_GET['id']) && $itemType['type'] == "Custom Content") {echo " selected=\"selected\"";} ?>>Custom Content</option>
                <?php
					if (!exist("sidebar", "type", "Login") || (isset($_GET['id']) && $itemType['type'] == "Login")) {
                		echo "<option value=\"Login\"";
						
						if (isset($_GET['id']) && $itemType['type'] == "Login") {
							echo " selected=\"selected\"";
						}
						
						echo ">Login</option>";
					}
				?>
              </select>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Content</div>
      	<div class="stepContent">
        <blockquote>
        <p>Content<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the box')" onmouseout="UnTip()" /> </p>
        <blockquote>
        <p><textarea name="content" id="content1" cols="45" rows="5" style="width:450px;" class="validate[required]" /><?php 
				if (isset ($item)) {
					echo stripslashes($item['content']);
				}
			?></textarea></p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider three">Finish</div>
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
