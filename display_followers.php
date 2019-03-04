<?php
require_once 'param.php';
require_once 'db.php';
require_once 'common.php';

$dbConnectionObj = Connection::getInstance();
$sql = 'SELECT * 
				FROM followers';
		$sth = $dbConnectionObj->prepare($sql);
		$sth->execute();

		$followers = $sth->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
<?php
	foreach ($followers as $follower) {
		echo "Username: ".$follower['insta_username']."<br>";
		echo "Full name: ".$follower['insta_full_name']."<br>";
		echo "Is Verified: ".$follower['insta_is_verified']."<br>";
		echo "<hr>";
	}
 ?>
</body>
</html>