<?php require_once('Connections/connDBA.php'); ?>
<?php login(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php title("Login"); ?>
<?php validate(); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("public"); ?>
<h4><a href="index.php">Home</a> &#9658 Login</h4>
      <form method="post" action="login.php<?php if (isset($_GET['accesscheck'])) {echo "?accesscheck=" .  urlencode($_GET['accesscheck']);} ?>" name="login" id="validate">
        <h1>Login</h1>
        <p>Login with your username and password to access your account.</p>
          <?php
		//Display a login failed alert
			if (isset($_GET['alert']) && isset($_GET['remaining'])) {
				errorMessage("Your user name or password is incorrect. You may try logging in <strong>" . $_GET['remaining'] . "</strong> more time(s).");
			}
			
			if (isset($_GET['expired'])) {
				errorMessage("You have excedded the maxmium number of failed logins allowed for a 24 hour period. Please wait 24 hours before trying again.");
			}
			
			if (isset($_GET['accesscheck']) && !isset($_GET['expired']) && !isset($_GET['alert']) && !isset($_GET['remaining'])) {
				errorMessage("Either you are not logged in, or do not have the appropriate privileges to perform this action.");
			}
		?>
        <p>&nbsp; </p>
        <blockquote>
          <p>User name<span class="require">*</span>: </p>
          <blockquote>
            <p>
              <input name="username" id="username" size="50" autocomplete="off" class="validate[required]" type="text" />
            </p>
          </blockquote>
          <p>Password<span class="require">*</span>: </p>
          <blockquote>
            <p>
              <input name="password" id="password" size="50" autocomplete="off" class="validate[required]" type="password" />
            </p>
          </blockquote>
          <input name="submit" id="submit" value="Login" onclick="tinyMCE.triggerSave();" type="submit" />
          <p><a href="forgot_password.php">Forgot your password?</a></p>
        </blockquote>
        <p>&nbsp;</p>
</form>
<?php footer("public"); ?>
</body>
</html>