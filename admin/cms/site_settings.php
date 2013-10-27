<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("siteSettings") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//If the site logo page is requested
	if (isset ($_GET['type']) && ($_GET['type'] == "logo")) {
		$logo = "logo is requested";
//If the site logo page is requested
	} else if (isset ($_GET['type']) && ($_GET['type'] == "icon")) {
		$icon = "icon is requested";
//If the theme page is requested
	} elseif (isset ($_GET['type']) && ($_GET['type'] == "theme")) {
		$theme = "theme is requested";
//If the site meta page is requested
	} elseif (isset ($_GET['type']) && ($_GET['type'] == "meta")) {
		$meta = "meta is requested";
//If the site security page is requested
	} elseif (isset($_GET['type']) && ($_GET['type'] == "security")) {
		$security = "security is requested";
//If the list of settings is requested
	} elseif (!isset ($_GET['type'])) {
		$settings = "settings are requested";
	} else {
		header("Location: site_settings.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
//If the site logo page is requested
	if (isset ($logo)) {
		$title = "Modify Site Logo";
//If the site logo page is requested
	} elseif (isset ($icon)) {
		$title = "Browser Icon";
//If the theme page is requested
	} elseif (isset ($theme)) {
		$title = "Modify Site Theme";
//If the site meta page is requested
	} elseif (isset ($meta)) {
		$title = "Modify Site Information";
//If the site security page is requested
	} elseif (isset($security)) {
		$title = "Security Settings";
	} else {
		$title = "Select Settings";
	}
?>
<?php title($title); ?>
<?php headers(); ?>
<?php validate(); ?>
<?php tinyMCESimple(); ?>
<script src="../../javascripts/common/enableDisable.js" type="text/javascript"></script>
<script src="../../javascripts/common/showHide.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<h2><?php echo $title; ?></h2>
<?php 
//If the list of settings is requested
	if (isset($settings)) {
?>
<p>Modify  settings within this site:</p>
<p>&nbsp;</p>
<blockquote>
<ul>
  <li class="homeBullet"><a href="index.php">Back to Home</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=security">Security</a></li>
</ul>
</blockquote>
<?php
//If the site logo page is requested
	} elseif (isset ($logo)) { 
?>
<p>Modify the banner displayed at the of each page.</p>
<?php
//Modify logo
	if (isset($_POST['submitBanner'])) {
		$tempFile = $_FILES['bannerUploader'] ['tmp_name'];
		$targetFile = basename($_FILES['bannerUploader'] ['name']);
		$uploadDir = "../../images";

		if (extension($targetFile) == "png" || extension($targetFile) == "bmp" || extension($targetFile) == "jpg" || extension($targetFile) == "gif") {
			move_uploaded_file($tempFile, $uploadDir . "/" . "banner.png");
			if (isset ($_POST['return'])) {
				header ("Location: site_setup_wizard.php");
				exit;
			} else {
				header ("Location: index.php?updated=logo");
				exit;
			}
		} else {
			errorMessage("This is an unsupported file type. Supported types have one of the following extensions: &quot;.png&quot;, &quot;.bmp&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;.");
		}
	} else {
		echo "<p>&nbsp;</p>";
	}
	
	if (isset ($_POST['updatePlacement']) && !empty($_POST['paddingTopSelect']) && !empty($_POST['paddingBottomSelect']) && !empty($_POST['paddingLeftSelect']) && !empty($_POST['paddingRightSelect'])) {
		$id = $_POST['idHidden'];
		$imagePaddingTopEdit = $_POST['paddingTopSelect'];
		$imagePaddingBottomEdit = $_POST['paddingBottomSelect'];
		$imagePaddingLeftEdit = $_POST['paddingLeftSelect'];
		$imagePaddingRightEdit = $_POST['paddingRightSelect'];
				
		$imagePaddingQuery = "UPDATE siteprofiles SET paddingTop = '{$imagePaddingTopEdit}', paddingBottom = '{$imagePaddingBottomEdit}', paddingLeft = '{$imagePaddingLeftEdit}', paddingRight = '{$imagePaddingRightEdit}' WHERE id = '{$id}'";
		
		$imagePaddingQueryResult = mysql_query($imagePaddingQuery, $connDBA);
		
		if (mysql_affected_rows() == 1) {
			header("Location: site_settings.php?type=logo");
			exit;
		}
	}
	
	if (isset ($_POST['updateSize']) && !empty($_POST['automatic']) || (!empty($_POST['height']) && !empty($_POST['width']))) {		
		if (isset ($_POST['automatic'])) {
			$auto = $_POST['automatic'];
			$imageSizeQuery = "UPDATE siteprofiles SET auto = '{$auto}'";
		} else {
			$imageHeight = $_POST['height'];
			$imageWidth = $_POST['width'];
			$imageSizeQuery = "UPDATE siteprofiles SET height = '{$imageHeight}', width = '{$imageWidth}', auto = '{$auto}'";
		}
		
		mysql_query($imageSizeQuery, $connDBA);
		
		if (mysql_affected_rows() == 1) {
			header("Location: site_settings.php?type=logo");
			exit;
		}
	}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#placement").validationEngine()
});
$(document).ready(function() {
	$("#size").validationEngine()
});
</script>
    <div class="layoutControl"> 
    <div class="dataLeft">
    <div class="block_course_list sideblock">
          <div class="header">
            <div class="title">
              <h2>Navigation</h2>
            </div>
          </div>
          <div class="content">
            <p>Modify other settings within this site:</p>
            <ul>
              <li class="homeBullet"><a href="index.php">Back to Home</a></li>
              <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
              <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
              <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
              <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
              <li class="arrowBullet"><a href="site_settings.php?type=security">Security</a></li>
            </ul>
            </div>
      </div>
    </div>
    <div class="contentRight">
      <div class="catDivider alignLeft">Site Logo</div>
      <div class="stepContent">
      <blockquote>
        <form action="site_settings.php?type=logo" method="post" enctype="multipart/form-data" id="validate" onsubmit="return errorsOnSubmit(this);">
          <div align="left">
            <?php
			//Display current banner if it exists
				$directory = "../../images";
			
				if (file_exists($directory)) {
					$imageDirectory = opendir("../../images");
					$image = readdir($imageDirectory);
					while (false !== ($image = readdir($imageDirectory))) {
						if (($image == "banner.png")) {
							echo "<p>";
								echo "Current file: <a href=\"../../images/banner.png\" target=\"_blank\">banner.png</a>";
							echo "</p>";
						} 
					} 
				}
			?>
            <input name="bannerUploader" type="file" id="validate" class="validate[required,funcCall[bannerUpload]]" size="50" />
            <br />
            Max file size: <?php echo ini_get('upload_max_filesize'); ?><br />
            <br />
            <p>
            <?php submit("submitBanner", "Upload"); ?>
            <input name="cancelBanner" type="button" id="cancelBanner" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
            </p>
          </div>
        </form>
      </blockquote>
      </div>
      <div class="catDivider alignLeft">Banner Placement</div>
      <div class="stepContent">
      <blockquote>
      <form action="site_settings.php?type=logo" method="post" name="padding" id="placement">
        <div align="left">
          <input type="hidden" name="idHidden" value="1" />
          	  <?php
			  //Select the image padding information
			  		$imagePaddingGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
					$imagePadding = mysql_fetch_array($imagePaddingGrabber);
					$imagePaddingTop = $imagePadding['paddingTop'];
					$imagePaddingLeft = $imagePadding['paddingLeft'];
					$imagePaddingRight = $imagePadding['paddingRight'];
					$imagePaddingBottom = $imagePadding['paddingBottom'];
			  ?>
          <p><strong>Top:</strong>
            <input name="paddingTopSelect" type="text" id="paddingTopSelect" class="validate[required,custom[onlyNumber]]" value="<?php echo $imagePaddingTop; ?>" size="3" maxlength="3" autocomplete="off" />
px<br />
<strong>Left:</strong>
<input name="paddingLeftSelect" type="text" id="paddingLeftSelect" class="validate[required,custom[onlyNumber]]" value="<?php echo $imagePaddingLeft; ?>" size="3" maxlength="3"  autocomplete="off" />
px<br />
<strong>Right:</strong>
<input name="paddingRightSelect" type="text" id="paddingRightSelect" class="validate[required,custom[onlyNumber]]" value="<?php echo $imagePaddingRight; ?>" size="3" maxlength="3"  autocomplete="off" />
px<br />
<strong>Bottom:</strong>
<input name="paddingBottomSelect" type="text" id="paddingBottomSelect" class="validate[required,custom[onlyNumber]]" value="<?php echo $imagePaddingBottom; ?>" size="3" maxlength="3" autocomplete="off" />
px</p>
          <p>
            <?php submit("updatePlacement", "Update"); ?>
            <input name="cancelPlacement" type="button" id="cancelPlacement" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
            <br />
          </p>
          <h6>px = Pixels from respective edge</h6>
        </div>
      </form>
      </blockquote>
      </div>
      <div class="catDivider alignLeft">Banner Size</div>
      <div class="stepContent">
      <blockquote>
      <form action="site_settings.php?type=logo" method="post" name="size" id="size">
      <?php
	  //Select the image size information
			$imageSizeGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
			$imageData = mysql_fetch_array($imageSizeGrabber);
	  ?>
      Width:
      <input name="width" type="text" id="width" class="validate[optional,custom[onlyNumber]]" value="<?php echo $imageData['width']; ?>" size="3" maxlength="3" autocomplete="off"<?php if ($imageData['auto'] == "on") {echo " disabled=\"disabled\"";} ?> />
px <br />
Height:
<input name="height" type="text" id="height" class="validate[optional,custom[onlyNumber]]" value="<?php echo $imageData['height']; ?>" size="3" maxlength="3" autocomplete="off"<?php if ($imageData['auto'] == "on") {echo " disabled=\"disabled\"";} ?> />
px
<p>
        <label><input type="checkbox" name="automatic" id="automatic" onclick="flvFTFO1('size','width,t','height,t')"<?php
			if ($imageData['auto'] == "on") {
				echo " checked=\"checked\"";
			}
		?> /> Automatic</label>
      </p>
      <p>
        <?php submit("updateSize", "Update"); ?>
        <input name="cancelSize" type="button" id="cancelSize" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
      </p>
      </form>
      </blockquote>
      </div>
</div>
</div>
<?php
//If the browiser icon page is requested
	} elseif (isset ($icon)) { 
?>
      <p>Modify the browser icon displayed on the top-left of the browser window or the current tab. A browser icon may have one of the following extensions : &quot;.ico&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;. Below is an example of a browser icon:
      <br />
      <br />
      <img src="../../images/admin_icons/faviconExample.jpg" alt="Browser Icon" /></p>
<?php
//Identify the icon extension
	$iconExtensionGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$iconExtension = mysql_fetch_array($iconExtensionGrabber);

//Modify browser icon
	if (isset($_POST['submitIcon'])) {
		$tempFile = $_FILES['iconUploader'] ['tmp_name'];
		$targetFile = basename($_FILES['iconUploader'] ['name']);
		$uploadDir = "../../images";
	
		if (extension($targetFile) == "ico" || extension($targetFile) == "jpg" || extension($targetFile) == "gif") {
			
			$iconType = extension($targetFile);
			unlink("../../images/icon." . $iconExtension['iconType']);
			move_uploaded_file($tempFile, $uploadDir . "/" . "icon." . $iconType);
			mysql_query("UPDATE `siteprofiles` SET `iconType` = '{$iconType}' WHERE id = '1'", $connDBA);
			
			if (isset ($_POST['return'])) {
				header ("Location: site_setup_wizard.php");
				exit;
			} else {
				header ("Location: index.php?updated=icon");
				exit;
			}
		} else {
			errorMessage("This is an unsupported file type. Supported types have one of the following extensions: &quot;.ico&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;.");
		}
	}
?>
<?php errorWindow("extension", "This is an unsupported file type. Supported types have one of the following extensions: &quot;.ico&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;."); ?>
<br />
<div class="layoutControl">
      <div class="dataLeft">
        <div class="block_course_list sideblock">
              <div class="header">
                <div class="title">
                  <h2>Navigation</h2>
                </div>
              </div>
              <div class="content">
                <p>Modify other settings within this site:</p>
                <ul>
                  <li class="homeBullet"><a href="index.php">Back to Home</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=security">Security</a></li>
                </ul>
          </div>
        </div>
  </div>
      <div class="contentRight">
      <form action="site_settings.php?type=icon" method="post" enctype="multipart/form-data" id="validate" onsubmit="return errorsOnSubmit(this, 'iconUploader', 'true', 'ico.jpg.gif');">
      <div class="catDivider one">Upload Icon</div>
      <div class="stepContent">
      <blockquote>
          <p>
            <?php
			//Display current banner if it exists
				$directory = "../../images";
			
				if (file_exists($directory)) {
					$imageDirectory = opendir("../../images");
					$image = readdir($imageDirectory);
					echo "<p>";
						echo "Current file: <a href=\"../../images/icon." . $iconExtension['iconType'] . "\" target=\"_blank\">icon." . $iconExtension['iconType'] . "</a>";
					echo "</p>";
				}
			?>
            <input name="iconUploader" type="file" id="iconUploader" class="validate[required,funcCall[iconUpload]]" size="50" />
            <br />
          Max file size: <?php echo ini_get('upload_max_filesize'); ?> </p>
         </blockquote>
      </div> 
      <div class="catDivider two">Submit</div>    
      <div class="stepContent">
      <blockquote>
      	<p>
          <?php submit("submitIcon", "Submit"); ?>
          <input name="cancelIcon" type="button" id="cancelIcon" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
        </p>
      </blockquote>
      </div>
      </form>
    </div>
	</div>
<?php //If the theme page is requested
	} elseif (isset ($theme)) { 
?>
<?php 
	if (isset ($_GET['action']) && $_GET['action'] == "modifyTheme" && !empty($_GET['theme'])) {
		$theme = $_GET['theme'];
		
		$modifyThemeQuery = "UPDATE siteprofiles SET style = '{$theme}'";
		$modifyThemeQueryResult = mysql_query($modifyThemeQuery, $connDBA);
		header ("Location: index.php?updated=theme");
		exit;
	}
	
	$themeGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$theme = mysql_fetch_array($themeGrabber);
?>
<p>This page will  modify the site colors, text styles, and splash image for this site.</p>
<p>&nbsp;</p>
<div class="layoutControl">
<div class="dataLeft">
<div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Navigation</h2>
        </div>
      </div>
      <div class="content">
        <p>Modify other settings within this site:</p>
        <ul>
          <li class="homeBullet"><a href="index.php">Back to Home</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=security">Security</a></li>
        </ul>
  </div>
</div>
</div>
<div class="contentRight">
<form action="site_settings.php?type=theme" method="post">
    <p><strong>American</strong></p><?php if ($theme['style'] == "american.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    <p><img src="../../images/themes/american/preview.jpg" alt="American Theme Preview" width="315" height="141" />
      <input type="button" name="chooseAmerican" id="chooseAmerican" value="Choose American Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=american.css');return document.MM_returnValue" />
    </p>
    <p><strong>Back to School</strong></p>
    <p>
      <?php if ($theme['style'] == "backToSchool.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    </p>
    <p><img src="../../images/themes/back_to_school/preview.jpg" width="313" height="157" alt="Back to School Theme Preview" /> 
      <input type="button" name="chooseAmerican2" id="chooseAmerican2" value="Choose Back to School Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=backToSchool.css');return document.MM_returnValue" />
    </p>
    <p><strong>Binary</strong></p><?php if ($theme['style'] == "binary.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    <p><img src="../../images/themes/binary/preivew.jpg" alt="Binary Theme Preview" width="315" height="136" />
      <input type="button" name="chooseBinary" id="chooseBinary" value="Choose Binary Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=binary.css');return document.MM_returnValue" />
    </p>
    <p><strong>Business</strong></p><?php if ($theme['style'] == "business.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    <p><img src="../../images/themes/business/preview.jpg" alt="Business Theme Preview" width="315" height="144" />
      <input type="button" name="chooseBusiness" id="chooseBusiness" value="Choose Business Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=business.css');return document.MM_returnValue" />
    </p>
    <p><strong>Chemistry Lessons</strong></p>
    <p>
      <?php if ($theme['style'] == "chemistryLessons.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    </p>
    <p><img src="../../images/themes/chemistry_lessons/preview.jpg" width="315" height="156" alt="Chemistry Lessons Theme Preview" /> 
      <input type="button" name="chooseChemistry" id="chooseChemistry" value="Choose Chemistry Lessons Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=chemistryLessons.css');return document.MM_returnValue" />
    </p>
    <p><strong>Digital University</strong></p><?php if ($theme['style'] == "digitalUniversity.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    <p><img src="../../images/themes/digital_university/preview.jpg" alt="Digitial University Theme Preview" width="314" height="136" />
      <input type="button" name="chooseDigital" id="chooseDigital" value="Choose Digital University Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=digitalUniversity.css');return document.MM_returnValue" />
    </p>
    <p><strong>e-Learning</strong></p><?php if ($theme['style'] == "eLearning.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    <p><img src="../../images/themes/e_learning/preview.jpg" alt="e-Learning Theme Preview" width="314" height="132" />
      <input type="button" name="chooseLearning" id="chooseLearning" value="Choose e-Learning Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=eLearning.css');return document.MM_returnValue" />
    </p>
    <p><strong>Global Network</strong></p>
    <p>
      <?php if ($theme['style'] == "globalNetwork.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    </p>
    <p><img src="../../images/themes/global_network/preview.jpg" width="314" height="133" alt="Global Network Theme Preview" />
      <input type="button" name="chooseLearning2" id="chooseLearning2" value="Choose Global Network Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=globalNetwork.css');return document.MM_returnValue" />
    </p>
    <p><strong>Knowledge Library</strong></p><?php if ($theme['style'] == "knowledgeLibrary.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    <p><img src="../../images/themes/knowledge_library/preview.jpg" alt="Knowledge Library Theme Preview" width="314" height="134" />
      <input type="button" name="chooseLibrary" id="chooseLibrary" value="Choose Knowledge Library Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=knowledgeLibrary.css');return document.MM_returnValue" />
    </p>
    <p><strong>Learning Horizon</strong></p>
    <p>
      <?php if ($theme['style'] == "learningHorizon.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    </p>
    <p><img src="../../images/themes/learning_horizon/preview.jpg" alt="Learning Horizon Theme Preview" width="314" height="152" />
      <input type="button" name="chooseLibrary2" id="chooseLibrary2" value="Choose Learning Horizon Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=learningHorizon.css');return document.MM_returnValue" />
    </p>
    <p><strong>New 2</strong></p>
    <p>
      <?php if ($theme['style'] == "new2.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    </p>
    <p><img src="../../images/themes/new_2/preview.jpg" width="315" height="134" alt="New 2 Theme Preview" />
      <input type="button" name="chooseLibrary3" id="chooseLibrary3" value="Choose New 2 Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=new2.css');return document.MM_returnValue" />
    </p>
    <p><strong>Online University</strong></p>
    <p>
      <?php if ($theme['style'] == "onlineUniversity.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
    </p>
    <p><img src="../../images/themes/online_university/preview.jpg" width="315" height="160" alt="Online University Theme Preview" />
      <input type="button" name="chooseLibrary4" id="chooseLibrary4" value="Choose Online University Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=onlineUniversity.css');return document.MM_returnValue" />
    </p>
  </form>
  </div>
</div>
<?php 
//If the site meta page is requested
	} elseif (isset ($meta)) { 
?>
<?php 
	if (isset ($_POST['modifyMeta']) || isset ($_POST['modifyMeta2']) && !empty($_POST['name'])) {
		if (!empty($_POST['name'])) {
			$name = mysql_real_escape_string($_POST['name']);
			$footer = mysql_real_escape_string($_POST['footer']);
			$author = mysql_real_escape_string($_POST['author']);
			$language = mysql_real_escape_string($_POST['language']);
			$timeZone = mysql_real_escape_string($_POST['timeZone']);
			$copyright = mysql_real_escape_string($_POST['copyright']);
			$description = mysql_real_escape_string($_POST['description']);
			$meta = mysql_real_escape_string($_POST['meta']);
			
			$modifyMetaQuery = "UPDATE siteprofiles SET siteName = '{$name}', siteFooter = '{$footer}', author = '{$author}', language = '{$language}', copyright = '{$copyright}', description = '{$description}', meta = '{$meta}', timeZone = '{$timeZone}'";
			$modifyMetaQueryResult = mysql_query($modifyMetaQuery, $connDBA);
			header ("Location: index.php?updated=siteInfo");
			exit;
		}
	}
	
	$themeGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$theme = mysql_fetch_array($themeGrabber);
?>
<p>Modify the site name and footer, as well as information which will help search engines better locate your site.</p>
<p>&nbsp;</p>
<form action="site_settings.php?type=meta" method="post" name="information" id="validate" onsubmit="return errorsOnSubmit(this);">
<?php
//Select the image padding information
	$siteInfoGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
	$siteInfo = mysql_fetch_array($siteInfoGrabber);
?>
		<div class="layoutControl">
		<div class="dataLeft">
        <div class="block_course_list sideblock">
              <div class="header">
                <div class="title">
                  <h2>Navigation</h2>
                </div>
              </div>
              <div class="content">
                <p>Modify other settings within this site:</p>
                <ul>
                  <li class="homeBullet"><a href="index.php">Back to Home</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
                  <li class="arrowBullet"><a href="site_settings.php?type=security">Security</a></li>
                </ul>
          </div>
        </div>
        </div>
        <div class="contentRight">
      		  <div class="catDivider alignLeft">Site Name &amp; Footer</div>
              <div class="stepContent">
      		  <blockquote>
      		    <p>The site name will appear in the title of your site<span class="require">*</span>:</p>
                <blockquote>
                  <p>
                    <input name="name" type="text" id="name" size="50" value="<?php echo stripslashes($siteInfo['siteName']); ?>" autocomplete="off" class="validate[required]" />
                  </p>
                </blockquote>
                <p>The footer is displayed at the bottom-left of each page:</p>
                <blockquote>
                  <p>
                    <textarea name="footer" id="footer" rows="5" cols="45" style="width:450px;"><?php echo stripslashes($siteInfo['siteFooter']); ?></textarea>
                  </p>
                </blockquote>
                <p>
                  <?php submit("modifyMeta", "Submit"); ?>
                  <input name="cancelMeta" type="button" id="cancelMeta" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
				</p>
	  </blockquote>
      </div>
      <div class="catDivider alignLeft">Search Keywords and Information</div>
      <div class="stepContent">
        <blockquote>
          <p>The author of this site, or the name of this organization or company:</p>
          <blockquote>
            <p>
              <input name="author" type="text" id="author" size="50" value="<?php echo stripslashes($siteInfo['author']); ?>" autocomplete="off" />
            </p>
          </blockquote>
          <p>The language of this site (changing this option will not change the language pack of this system):</p>
          <blockquote>
            <p>
              <select name="language" id="language">
                <option <?php if ($siteInfo['language'] == "en-US") {echo " selected=\"selected\"";} ?> value="en-US">English</option>
              </select>
            </p>
          </blockquote>
          <p>Time zone:</p>
          <blockquote>
            <p>
              <select name="timeZone" id="timeZone">
                <option value="America/New_York"<?php if ($siteInfo['timeZone'] == "America/New_York") {echo " selected=\"selected\"";} ?>>Eastern Time Zone</option>
                <option value="America/Chicago"<?php if ($siteInfo['timeZone'] == "Central Time Zone") {echo " selected=\"selected\"";} ?>>Central Time Zone</option>
                <option value="America/Denver"<?php if ($siteInfo['timeZone'] == "America/Denver") {echo " selected=\"selected\"";} ?>>Mountain Time Zone</option>
                <option value="America/Los_Angeles"<?php if ($siteInfo['timeZone'] == "America/Los_Angeles") {echo " selected=\"selected\"";} ?>>Pacific Time Zone</option>
                <option value="America/Juneau"<?php if ($siteInfo['timeZone'] == "America/Juneau") {echo " selected=\"selected\"";} ?>>Alaskan Time Zone</option>
                <option value="Pacific/Honolulu"<?php if ($siteInfo['timeZone'] == "Pacific/Honolulu") {echo " selected=\"selected\"";} ?>>Hawaii-Aleutian Time Zone</option>
              </select>
            </p>
          </blockquote>
          <p>Copyright statement:</p>
          <blockquote>
            <p>
              <textarea name="copyright" id="copyright" cols="45" rows="5" class="noEditorSimple"><?php echo stripslashes($siteInfo['copyright']); ?></textarea>
            </p>
          </blockquote>
          <p>List keywords in the text box below, and <strong>separate each phrase with a comma and a space (e.g. website, my website, www)</strong>:</p>
          <blockquote>
            <p>
              <textarea name="meta" id="meta" cols="45" rows="5" class="noEditorSimple"><?php echo stripslashes($siteInfo['meta']); ?></textarea>
            </p>
          </blockquote>
          <p>Site description:</p>
          <blockquote>
            <p>
              <textarea name="description" id="description" cols="45" rows="5" class="noEditorSimple"><?php echo stripslashes($siteInfo['description']); ?></textarea>
            </p>
          </blockquote>
          <p>
            <input type="submit" name="modifyMeta2" id="modifyMeta2" value="Submit" />
            <input name="cancelMeta2" type="button" id="cancelMeta2" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
          </p>
        </blockquote>
      </div>
      </div>
      </div>
</form>
<?php 
//If the site security page is requested
	} elseif (isset ($security)) { 
?>
<?php 
//Modify the security settings
	if (isset($_POST['modifySecurity']) && !empty($_POST['securityType'])) {
		$type = $_POST['securityType'];
		$failedLogins = $_POST['logins'];
		
		if ($type == "auto") {
			query("UPDATE `siteprofiles` SET `saptcha` = '{$type}', `failedLogins` = '{$failedLogins}' WHERE `id` = '1'");
			redirect("index.php?updated=security");
		} else {
			if (!empty($_POST['question']) && !empty($_POST['answer'])) {
				$question = mysql_real_escape_string($_POST['question']);
				$answer = mysql_real_escape_string($_POST['answer']);
				
				query("UPDATE `siteprofiles` SET `saptcha` = '{$type}', `question` = '{$question}', `answer` = '{$answer}', `failedLogins` = '{$failedLogins}' WHERE `id` = '1'");
				redirect("index.php?updated=security");
			}
		}
	}
?>
<p>This is the site security page. Most of the security is handled automatically. However, the saptcha for the comments section may need configuration. Saptcha is a section under the comments section for the general public to answer before a comment is posted. This ensures that the commenter is human, not an automatic robot which can excessively spam the comments section. Furthermore, you can ask a question which can only be answered by a select group.</p>
<p>Farther down is a setting which will enable you to set how failed logins a user may have in a 24 hour period. This setting is important as it limits a hacker from trying random user names and passwords.</p>
<p>&nbsp;</p>
<form name="security" id="validate" action="site_settings.php?type=security" method="post">
<?php
//Select form information
	$security = query("SELECT * FROM `siteprofiles` WHERE id = '1'");
?>
<div class="layoutControl">
<div class="dataLeft">
<div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Navigation</h2>
        </div>
      </div>
      <div class="content">
        <p>Modify other settings within this site:</p>
        <ul>
          <li class="homeBullet"><a href="index.php">Back to Home</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=security">Security</a></li>
        </ul>
  </div>
</div>
</div>
<div class="contentRight">
<div class="catDivider one">Comments Security Type</div>
<div class="stepContent">
  <blockquote>
    <p>Select the security type:</p>
    <blockquote>
    	<p>
        <select name="securityType" onchange="securityFeatures(this.value)">
        <option value="auto"<?php
	  	if ($security['saptcha'] == "auto") {
			echo " selected=\"selected\"";
		}
	  ?>>Automatically Generated</option>
      	<option value="custom"<?php
	  	if ($security['saptcha'] == "custom") {
			echo " selected=\"selected\"";
		}
	  ?>>Custom Question</option>
      </select>
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider two"><span class="catDivider one">Comments</span> Security Settings</div>
<div class="stepContent">
  <blockquote>
  	<div id="auto"<?php
	  	if ($security['saptcha'] == "custom") {
			echo " class=\"contentHide\"";
		}
	  ?>>
    <p>This option asks a simple question which can be answered by the general population, such as &quot;What is 1 + 1&quot;. This option is recommended if you wish the comments section to be used by anyone.</p>
    <div class="noResults">No input is required.</div>
    </div>
   </blockquote>
   <blockquote>
   <div id="custom"<?php
	  	if ($security['saptcha'] == "auto") {
			echo " class=\"contentHide\"";
		}
	  ?>>
    <p>This option enables you to create a custom question. This option is recommended if you wish the comments section to be used by a selected group of people. If this is the case, try asking a question that only this group will know the answer.</p>
    <p>Question<span class="require">*</span>:</p>
    <blockquote>
      <p><input type="text" name="question" id="question" size="50" autocomplete="off" class="validate[required]" value="<?php
	  	echo prepare($security['question'], true, true)
	  ?>" /></p>
    </blockquote>
    <p>Answer (case will be ignored)<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <input type="text" name="answer" id="answer" size="50" autocomplete="off" class="validate[required]" value="<?php
	  	echo prepare($security['answer'], true, true)
	  ?>" />
      </p>
    </blockquote>
  </div>
  </blockquote>
</div>
<div class="catDivider three">Failed Login Security</div>
<div class="stepContent">
  <blockquote>
    <p>
      Set number of failed logins:</p>
    <blockquote>
      <p>
        <select name="logins" id="logins">
          <option value="1"<?php if ($security['failedLogins'] == "1") { echo " selected=\"selected\""; } ?>>One</option>
          <option value="2"<?php if ($security['failedLogins'] == "2") { echo " selected=\"selected\""; } ?>>Two</option>
          <option value="3"<?php if ($security['failedLogins'] == "3") { echo " selected=\"selected\""; } ?>>Three</option>
          <option value="4"<?php if ($security['failedLogins'] == "4") { echo " selected=\"selected\""; } ?>>Four</option>
          <option value="5"<?php if ($security['failedLogins'] == "5") { echo " selected=\"selected\""; } ?>>Five</option>
          <option value="6"<?php if ($security['failedLogins'] == "6") { echo " selected=\"selected\""; } ?>>Six</option>
          <option value="7"<?php if ($security['failedLogins'] == "7") { echo " selected=\"selected\""; } ?>>Seven</option>
          <option value="8"<?php if ($security['failedLogins'] == "8") { echo " selected=\"selected\""; } ?>>Eight</option>
          <option value="9"<?php if ($security['failedLogins'] == "9") { echo " selected=\"selected\""; } ?>>Nine</option>
          <option value="10"<?php if ($security['failedLogins'] == "10") { echo " selected=\"selected\""; } ?>>Ten</option>
        </select>
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider four">Submit</div>
<div class="stepContent">
  <blockquote>
    <p>
      <input type="submit" name="modifySecurity" id="modifySecurity" value="Submit" />
      <input name="cancelMeta2" type="button" id="cancelSecurity" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
    </p>
  </blockquote>
</div>
</div>
</div>
</form>
<?php } ?>
<?php footer(); ?>
</body>
</html>