<?php require_once('Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("System Check"); ?>
<?php headers(); ?>
<script src="javascripts/systemCheck/detectPlugins.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php
	if (!isset ($_SESSION['MM_UserGroup'])) {
		topPage("public");
		echo "<h4><a href=\"index.php\">Home</a> &#9658 System Check</h4>";
	} else {
		topPage();
		echo "<h4><a href=\"index.php\">Home</a> &#9658 System Check</h4>";
	}
?>     
<h2>System Check</h2>
<p>The site is checking to see if you have all of the required components installed and running on your computer.</p>
<p>&nbsp;</p>
<table width="100%" border="0">
  <tr>
    <td width="5%">
<script type="text/javascript">
//Internet Explorer detection
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){
 		var ieversion=new Number(RegExp.$1)
 		
		if (ieversion>=8)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
 		else if (ieversion>=7)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
 		else if (ieversion>=6)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
 		else if (ieversion>=5)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
		else
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
	}
	
//Firefox detection
	if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var ffversion=new Number(RegExp.$1)
 		
		if (ffversion>=3)
  			document.write("<img src=\"images/systemCheck/firefox.gif\" />")
 		else if (ffversion>=2)
  			document.write("<img src=\"images/systemCheck/firefox.gif\" />")
 		else if (ffversion>=1)
  			document.write("<img src=\"images/systemCheck/firefox.gif\" />")
		else
  			document.write("<img src=\"images/systemCheck/firefox.gif\" />")
	}

//Opera Detection
	if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		if (oprversion>=10)
  			document.write("<img src=\"images/systemCheck/opera.gif\" />")
 		else if (oprversion>=9)
  			document.write("<img src=\"images/systemCheck/opera.gif\" />")
 		else if (oprversion>=8)
  			document.write("<img src=\"images/systemCheck/opera.gif\" />")
 		else if (oprversion>=7)
  			document.write("<img src=\"images/systemCheck/opera.gif\" />")
 		else
  			document.write("<img src=\"images/systemCheck/opera.gif\" />")
	}
			
//Netscape Detection
	if (/Netscape[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("<img src=\"images/systemCheck/netscape.gif\" />")
	}
		
//Chrome Detection
	if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("<img src=\"images/systemCheck/chrome.gif\" />")
	}
		
//Safari Detection
	if (/Safari[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("<img src=\"images/systemCheck/safari.gif\" />")
	}
	
//Any other browser detection
	var browserName = x.userAgent;
	if (browserName !== 'MSIE' || browserName !== 'Firefox' || browserName !== 'Mozilla' || browserName !== 'Opera' || browserName !== 'Netscape' || browserName !== 'Chrome' || browserName !== 'Safari') {
		document.write("<img src=\"images/systemCheck/unknown.gif\" />")
	}
</script>    </td>
    <td width="2%">
<script type="text/javascript">
//Internet Explorer detection
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){
 		var ieversion=new Number(RegExp.$1)
 		
		if (ieversion>=8)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
 		else if (ieversion>=7)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
 		else if (ieversion>=6)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
 		else if (ieversion>=5)
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
		else
  			document.write("<img src=\"images/systemCheck/internetExplorer.gif\" />")
	}
	
//Firefox detection
	if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var ffversion=new Number(RegExp.$1)
 		
		if (ffversion>=3)
  			document.write("<img src=\"images/common/checkmark.png\" />")
 		else if (ffversion>=2)
  			document.write("<img src=\"images/common/checkmark.png\" />")
 		else if (ffversion>=1)
  			document.write("<img src=\"images/common/checkmark.png\" />")
		else
  			document.write("<img src=\"images/common/x.png\" />")
	}

//Opera Detection
	if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		if (oprversion>=10)
  			document.write("<img src=\"images/common/checkmark.png\" />")
 		else if (oprversion>=9)
  			document.write("<img src=\"images/common/checkmark.png\" />")
 		else if (oprversion>=8)
  			document.write("<img src=\"images/common/checkmark.png\" />")
 		else if (oprversion>=7)
  			document.write("<img src=\"images/common/checkmark.png\" />")
 		else
  			document.write("<img src=\"images/common/x.png\" />")
	}
			
//Netscape Detection
	if (/Netscape[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("<img src=\"images/common/checkmark.png\" />")
	}
		
//Chrome Detection
	if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("<img src=\"images/common/checkmark.png\" />")
	}
		
//Safari Detection
	if (/Safari[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("<img src=\"images/common/checkmark.png\" />")
	}
	
//Any other browser detection
	var browserName = x.userAgent;
	if (browserName !== 'MSIE' || browserName !== 'Firefox' || browserName !== 'Mozilla' || browserName !== 'Opera' || browserName !== 'Netscape' || browserName !== 'Chrome' || browserName !== 'Safari') {
		document.write("<img src=\"images/common/x.png\" />")
	}
</script>    </td>
    <td>
    <div align="center">
<script type="text/javascript">
//Internet Explorer detection
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){
 		var ieversion=new Number(RegExp.$1)
 		
		if (ieversion>=8)
  			document.write("You are running Internet Explorer, which is supported by this site.")
 		else if (ieversion>=7)
  			document.write("You are running Internet Explorer, which is supported by this site.")
 		else if (ieversion>=6)
  			document.write("You are running Internet Explorer, which is supported by this site.")
 		else if (ieversion>=5)
  			document.write("You are running an old verison of Internet Explorer, please <a href=\'http://www.microsoft.com/windows/internet-explorer/default.aspx\'>update your browser</a>.")
		else
  			document.write("You are running an old verison of Internet Explorer, please <a href=\'http://www.microsoft.com/windows/internet-explorer/default.aspx\'>update your browser</a>.")
	}
	
//Firefox detection
	if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var ffversion=new Number(RegExp.$1)
 		
		if (ffversion>=3)
  			document.write("You are running Mozilla Firefox, which is supported by this site.")
 		else if (ffversion>=2)
  			document.write("You are running Mozilla Firefox, which is supported by this site.")
 		else if (ffversion>=1)
  			document.write("You are running an old verison of Mozilla Firefox, please <a href=\'http://www.mozilla.com/en-US/firefox/\'>update your browser</a>.")
		else
  			document.write("You are running an old verison of Mozilla Firefox, please <a href=\'http://www.mozilla.com/en-US/firefox/\'>update your browser</a>.")
	}

//Opera Detection
	if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		if (oprversion>=10)
  			document.write("You are running Opera, which is supported by this site.")
 		else if (oprversion>=9)
  			document.write("You are running Opera, which is supported by this site.")
 		else if (oprversion>=8)
  			document.write("You are running Opera, which is supported by this site.")
 		else if (oprversion>=7)
  			document.write("You are running Opera, which is supported by this site.")
 		else
  			document.write("You are running an old verison of Opera, please <a href=\'http://www.opera.com/\'>update your browser</a>.")
	}
			
//Netscape Detection
	if (/Netscape[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("You are running Netscape Navigator, which is supported by this site.")
	}
		
//Chrome Detection
	if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("You are running Google Chrome, which is supported by this site.")
	}
		
//Safari Detection
	if (/Safari[\/\s](\d+\.\d+)/.test(navigator.userAgent)){
 		var oprversion=new Number(RegExp.$1)
		
		document.write("You are running Apple Safari, which is supported by this site.")
	}
	
//Any other browser detection
	var browserName = x.userAgent;
	if (browserName !== 'MSIE' || browserName !== 'Firefox' || browserName !== 'Mozilla' || browserName !== 'Opera' || browserName !== 'Netscape' || browserName !== 'Chrome' || browserName !== 'Safari') {
		document.write("Your browser may not be supported, consider using one of the following browsers: <a href=\'http://www.mozilla.com/en-US/firefox/'>Mozilla Firefox</a>, <a href=\'http://www.microsoft.com/windows/internet-explorer/default.aspx'>Internet Explorer</a>, <a href=\'http://www.apple.com/safari/download/'>Apple Safari</a>, or <a href=\'http://www.google.com/chrome'>Google Chrome</a>.")
	}
</script> 
</div>    </td>
  </tr>
  <tr>
    <td><img src="images/systemCheck/javascript.gif" alt="Javscript" width="40" height="40" /></td>
    <td><img src="images/common/checkmark.png" /></td>
    <td><div align="center">Javscript is enabled.</div></td>
  </tr>
  <tr>
    <td><img src="images/systemCheck/cookies.gif" alt="Cookies" width="40" height="40" /></td>
    <td>
<script type="text/javascript">
	var cookieEnabled=(navigator.cookieEnabled)? true : false


	if (typeof navigator.cookieEnabled=="undefined" && !cookieEnabled){ 
		document.cookie="testcookie"
		cookieEnabled=(document.cookie.indexOf("testcookie")!=-1)? true : false
	}

	if (cookieEnabled)
	document.write("<img src=\"images/common/checkmark.png\" />")
	else
	document.write("<img src=\"images/common/x.png\" />")
</script>    </td>
    <td><div align="center">
<script type="text/javascript">
	var cookieEnabled=(navigator.cookieEnabled)? true : false


	if (typeof navigator.cookieEnabled=="undefined" && !cookieEnabled){ 
		document.cookie="testcookie"
		cookieEnabled=(document.cookie.indexOf("testcookie")!=-1)? true : false
	}

	if (cookieEnabled)
	document.write("Cookies are enabled.")
	else
	document.write("Cookies are not enabled.")
</script>
    </div></td>
  </tr>
  <tr>
    <td><img src="images/systemCheck/flash.gif" alt="Flash" width="40" height="40" /></td>
    <td>
<script type="text/javascript">
	if (pluginlist.indexOf("Flash")!=-1)
	document.write("<img src=\"images/common/checkmark.png\" />")
	else
	document.write("<img src=\"images/common/x.png\" />")
</script>    </td>
    <td>
      <div align="center">
<script type="text/javascript">
	if (pluginlist.indexOf("Flash")!=-1)
	document.write("You have Adobe Flash player installed.")
	else
	document.write("You do not have Adobe Flash player installed.")
</script>
      </div></td>
  </tr>
  <tr>
    <td><img src="images/systemCheck/windowsMedia.gif" alt="Media Player" width="40" height="40" /></td>
    <td>
<script type="text/javascript">
	if (pluginlist.indexOf("Windows Media Player")!=-1)
	document.write("<img src=\"images/common/checkmark.png\" />")
	else
	document.write("<img src=\"images/common/x.png\" />")
</script>    </td>
    <td>
      <div align="center">
<script type="text/javascript">
	if (pluginlist.indexOf("Windows Media Player")!=-1)
	document.write("You have Windows Media Player installed.")
	else
	document.write("You do not have Windows Media Player installed.")
</script>
      </div></td>
  </tr>
  <tr>
    <td><img src="images/systemCheck/quicktime.gif" alt="Quicktime" width="40" height="40" /></td>
    <td>
<script type="text/javascript">
	if (pluginlist.indexOf("QuickTime")!=-1)
	document.write("<img src=\"images/common/checkmark.png\" />")
	else
	document.write("<img src=\"images/common/x.png\" />")
</script>    </td>
    <td>
      <div align="center">
<script type="text/javascript">
	if (pluginlist.indexOf("QuickTime")!=-1)
	document.write("You have QuickTime installed.")
	else
	document.write("You do not have QuickTime installed.")
</script>
      </div></td>
  </tr>
  <tr>
    <td><img src="images/systemCheck/acrobat.gif" alt="Acrobat" width="40" height="40" /></td>
    <td>
<script type="text/javascript">
	if (pluginlist.indexOf("Acrobat Reader")!=-1)
	document.write("<img src=\"images/common/checkmark.png\" />")
	else
	document.write("<img src=\"images/common/x.png\" />")
</script>    </td>
    <td>
      <div align="center">
<script type="text/javascript">
	if (pluginlist.indexOf("Acrobat Reader")!=-1)
	document.write("You have Adobe Acrobat Reader installed.")
	else
	document.write("You have do not Adobe Acrobat Reader installed.")
</script>
      </div></td>
  </tr>
</table>
<?php
	if (!isset ($_SESSION['MM_UserGroup'])) {
		footer("public");
	} else {
		footer();
	}
?>
</body>
</html>