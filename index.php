<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<?php

if(!empty($_POST['submit'])) {
	exec('php insta.php '.$_POST['username'].' '.$_POST['password']);
	?>
	<script type="text/javascript">
		alert("Data has been scrapped successfully");
	</script>	
	<?php
}
?>
<form method="post">
	<input type="text" name="username" placeholder="Enter email" required="required">
	<input type="text" name="password" placeholder="Enter password" required="required">
	<input type="submit" name="submit" value="Submit">
</form>
<br>
<br>
<a href="display_followers.php" target="_blank">View Followers</a>
<br>
<br>
<a href="display_inbox.php" target="_blank">View Inbox</a>
</body>
</html>