<?php require_once('Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Enable Javascript"); ?>
<?php headers(); ?>
<?php meta(); ?>
</head>

<body>
<?php
	if (!isset ($_SESSION['MM_UserGroup'])) {
		topPage("public");
		echo "<h4><a href=\"index.php\">Home</a> &#9658 Enable Javascript</h4>";
	} else {
		topPage();
		echo "<h4><a href=\"admin/index.php\">Home</a> &#9658 Enable Javascript</h4>";
	}
?>
<h2>Enable Javascript</h2>
<p>Javascript on your browser is currently disabled. This site needs javascript in order to run, so follow the steps below to enable them.</p>
<p>&nbsp;</p>
<blockquote>
  <table width="100%" border="0">
    <tr>
      <td width="44"><img src="images/systemCheck/internetExplorer.gif" alt="Internet Explorer" width="40" height="40" /></td>
      <td width="812"><p><strong>Internet Explorer 6 and above</strong></p>
        <ol>
          <li>Go to &quot;Tools&quot; from the browser menu, and select &quot;Internet Options&quot;.</li>
          <li>In the pop-up window, click on the tab at the top of the windows that says &quot;Security&quot;.</li>
          <li>Click on a button labeled &quot;Custom Level&quot;.</li>
          <li>In the settings window, scroll down to a section called &quot;Scripting&quot;.</li>
          <li>Underneath the word &quot;Scripting&quot;,  set the &quot;Active Scripting&quot; to &quot;Enabled&quot;.</li>
          <li>Press &quot;Ok&quot;, and &quot;Ok&quot; again to save the changes.</li>
          <li>The button below will work if Javascript is enabled.</li>
        </ol>
        <p><input type="submit" name="IEBack" id="IEBack" value="Go Back" onclick="history.go(-1)" /></p>
      </td>
    </tr>
    <tr>
      <td><p><img src="images/systemCheck/firefox.gif" alt="Firefox" width="40" height="40" /></p>
      <p><img src="images/systemCheck/mozilla.gif" alt="Mozilla" width="40" height="40" /></p></td>
      <td><p><strong>Mozilla Firefox 1.5 and above</strong></p>
        <ol>
          <li>Go to &quot;Tools&quot; from the browser menu, and select &quot;Options&quot;.</li>
          <li>In the pop-up window, click on the tab at the top of the windows that says &quot;Content&quot;.</li>
          <li>Select the checkbox, titled &quot;Enable Javascript&quot;.</li>
          <li>Press &quot;Ok&quot; to save the changes.</li>
          <li>The button below will work if Javascript is enabled.</li>
        </ol>
        <p><input type="submit" name="FFBack" id="FFBack" value="Go Back" onclick="history.go(-1)" /></p>
      </td>
    </tr>
    <tr>
      <td><img src="images/systemCheck/netscape.gif" alt="Netscape" width="40" height="40" /></td>
      <td><p><strong>Netscape 4.8</strong></p>
        <ol>
          <li>Go to &quot;Tools&quot; from the browser menu.</li>
          <li>In the pop-up window, click  &quot;Preferences&quot;, &quot;Advanced&quot;, then &quot;Scripts and Plugins&quot;.</li>
          <li>Select the checkbox, titled &quot;Enable Javascript&quot;.</li>
          <li>Press &quot;Ok&quot; to save the changes.</li>
          <li>The button below will work if Javascript is enabled.</li>
        </ol>
        <p><input type="submit" name="NSBack" id="NSBack" value="Go Back" onclick="history.go(-1)" /></p>
      </td>
    </tr>
    <tr>
      <td><img src="images/systemCheck/safari.gif" alt="Safari" width="40" height="40" /></td>
      <td><p><strong>Apple Safari 1 and above</strong></p>
        <ol>
          <li>Go to &quot;Safari&quot; from the browser menu, and select &quot;Preferences&quot;.</li>
          <li>In the pop-up window, click on &quot;Security&quot;.</li>
          <li>Select the checkbox, titled &quot;Enable Javascript&quot;.</li>
          <li>Press &quot;Ok&quot; to save the changes.</li>
          <li>The button below will work if Javascript is enabled.</li>
        </ol>
      <p><input type="submit" name="ASBack" id="ASBack" value="Go Back" onclick="history.go(-1)" /></p>
      </td>
    </tr>
    <tr>
      <td><img src="images/systemCheck/opera.gif" alt="Opera" width="40" height="40" /></td>
      <td><p><strong>Opera 7 and above</strong></p>
        <ol>
          <li>Go to &quot;Tools&quot; from the browser menu, and select &quot;Preferences&quot;.</li>
          <li>In the pop-up window, click on &quot;Multimedia&quot; from the list of items at left.</li>
          <li>Select the checkbox, titled &quot;Enable Javascript&quot;.</li>
          <li>Press &quot;Ok&quot; to save the changes.</li>
          <li>The button below will work if Javascript is enabled.</li>
        </ol>
      <p><input type="submit" name="OPBack" id="OPBack" value="Go Back" onclick="history.go(-1)" /></p>
     </td>
    </tr>
    <tr>
      <td><img src="images/systemCheck/chrome.gif" alt="Google Chrome" width="40" height="40" /></td>
      <td><p><strong>Google Chrome 4 and above</strong></p>
        <ol>
          <li>Go to &quot;Tools&quot; from the browser menu, and select &quot;Options&quot;.</li>
          <li>In the pop-up window,  click on the tab at the top of the windows that says &quot;Under the Hood&quot;.</li>
          <li>Click on a button labeled &quot;Content Settings&quot;.</li>
          <li>In the second pop-up window, click on the tab at the top of the windows that says &quot;Javascript&quot;.</li>
          <li>Select the bullet, titled &quot;Allow all sites to run JavaScript&quot;.</li>
          <li>Press &quot;Close&quot;, and &quot;Close&quot; again to save the changes.</li>
          <li>The button below will work if Javascript is enabled.</li>
        </ol>
      <p><input type="submit" name="GCBack" id="GCBack" value="Go Back" onclick="history.go(-1)" /></p>     
      </td>
    </tr>
  </table>
</blockquote>
<p>&nbsp;</p>
<?php
	if (!isset ($_SESSION['MM_UserGroup'])) {
		footer("public");
	} else {
		footer();
	}
?>
</body>
</html>
