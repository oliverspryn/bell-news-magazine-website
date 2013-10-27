<?php require_once('Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Sitemap"); ?>
<?php headers(); ?>
<?php meta(); ?>
</head>
<body>
<?php topPage("public"); ?>
<h4><a href="index.php">Home</a> &#9658 Sitemap</h4>
<h2>Sitemap</h2>
<p>Below is a map of all of the avaliable pages within this site.</p>
<div class="toolBar"><a class="toolBarItem back" href="javascript:history.go(-1)">Go Back</a></div>
<p>&nbsp;</p>
<?php
//Find the current level of a page
	function level($id) {
		if (exist("pages", "id", $id)) {
			$nextPage = query("SELECT * FROM `pages` WHERE `id` = '{$id}'");
			return  "&nbsp;&nbsp;&nbsp;" . level($nextPage['parentPage']);
		}
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
	
//Recursively loop through the pages
	function pagesDirectory($level) {
		if ($level == "0") {
			$pagesGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '{$level}' AND `visible` = 'on' AND `published` != '0' ORDER BY `position` ASC", "raw");
		} else {
			$pagesGrabber = query("SELECT * FROM `pages` WHERE `parentPage` = '{$level}' AND `visible` = 'on' AND `published` != '0' ORDER BY `subPosition` ASC", "raw");
		}
		
		while ($pages = mysql_fetch_array($pagesGrabber)) {
			if (isset($_GET['id'])) {
			   $parentPage = query("SELECT * FROM `pages` WHERE `id` = '{$_GET['id']}'");
			}
			
			$title = unserialize($pages['content' . $pages['display']]);
			
			echo level($pages['parentPage']);
			
			if ($pages['position'] == "1") {
				echo "<span class=\"homePage\">";
			} else {
				echo "<span class=\"page\">";
			}
			 
			echo "<a href=\"index.php?page=" . $pages['id'] . "\"";
			
			echo ">" . $title['title'] . "</a>";
			
			echo "</span><br /><br />";
			
			if (exist("pages", "parentPage", $pages['id'])) {
				pagesDirectory($pages['id']);
			}
		}
	}
	
	pagesDirectory('0');
?>
<?php
	stats("true", "false");
	footer("public");
?>
</body>
</html>