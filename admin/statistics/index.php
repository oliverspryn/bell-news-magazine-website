<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("viewStatistics") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Site Statistics"); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<h2>Site Statistics</h2>
<p>Detailed statistics are kept to monitor the amount of traffic this site recieves in a day. Rollover the the points in the line graph and the different sections in the pie graph for more detailed information. These statistics are only used to montior the public website.</p>
<p>&nbsp;</p>
<div class="toolBar">
  <a class="toolBarItem back" href="../cms/index.php">Back to Pages</a>
</div>
<blockquote>
  <div align="center"><embed type="application/x-shockwave-flash" src="charts/line.swf" id="chart" name="chart" quality="high" allowscriptaccess="always" flashvars="chartWidth=600&chartHeight=350&debugMode=0&DOMId=overallstats&registerWithJS=0&scaleMode=noScale&lang=EN&dataURL=data/index.php?type=daily" wmode="transparent" width="600" height="350"></embed></div>
    <p>&nbsp;</p>
  <div align="center"><embed type="application/x-shockwave-flash" src="charts/pie3D.swf" id="chart" name="chart" quality="high" allowscriptaccess="always" flashvars="chartWidth=900&chartHeight=600&debugMode=0&DOMId=overallstats&registerWithJS=0&scaleMode=noScale&lang=EN&dataURL=data/index.php?type=page" wmode="transparent" width="900" height="600"></embed></div>
</blockquote>
<?php footer(); ?>
</body>
</html>