<?php
	$option = $_GET["option"];
	$catch = mysql_query("SELECT * FROM `vote` WHERE `id`='".$option."' ORDER BY `id` DESC");
	while($crow = mysql_fetch_assoc($catch)){
		$count = $crow["votes"];
	}
	$query = "UPDATE `vote` SET `votes`='".($count+1)."' WHERE `id`='".$option."'";
	$result = mysql_query($query) or die(mysql_error());
	echo "Your vote was added. Vote again:<br><br>";
	echo "<b>Name    - Votes</b><br>\n";
	$view = mysql_query("SELECT * FROM `vote`");
	while($row = mysql_fetch_assoc($view)){
		echo "<a href=\"?page=vote&option=".$row["id"]."\">";
		echo $row["name"] . "</a> - ".$row["votes"]."<br>\n";
	}
?>
