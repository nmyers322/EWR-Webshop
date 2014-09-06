<?php 
$action = $_GET['action'];
if($action == 'send'){	
	$content = $_POST['name'] . "\n" . $_SERVER['REMOTE_ADDR'] . "\n\n" . $_POST['comment'];	

	$currenttime = date('Y-m-d');
	$currenttime .= " at ";
	$currenttime .= date('G:i');

	$headers = "From: " . $_POST['email'];	
	mail("eternalwarfarerecords@gmail.com", "website inquiry $currenttime", $content, $headers);	
	echo "Thanks for the message. We will get back to you shortly.<br><br>";
} else if($action == 'add'){	
	$email = mysql_real_escape_string($_POST['email']);	
	$result1 = mysql_query("SELECT * FROM `customers` WHERE `email`='$email' AND `mailing-list`='0'");	
	$result1B = mysql_query("SELECT * FROM `customers` WHERE `email`='$email' AND `mailing-list`='2'");	
	$result2 = mysql_query("SELECT * FROM `customers` WHERE `email`='$email' AND `mailing-list`='1'");	
	$result3 = mysql_query("SELECT * FROM `customers` WHERE `alt-email`='$email' AND `mailing-list`='1'");	
	$result3B = mysql_query("SELECT * FROM `customers` WHERE `alt-email`='$email' AND `mailing-list`='2'");	
	$result4 = mysql_query("SELECT * FROM `customers` WHERE `alt-email`='$email' AND `mailing-list`='0'");	
	$result5 = mysql_query("SELECT * FROM `customers` WHERE `email`='$email' AND `mailing-list`='3'");	
	$result6 = mysql_query("SELECT * FROM `customers` WHERE `alt-email`='$email' AND `mailing-list`='3'");	
	$result7 = mysql_query("SELECT * FROM `customers` WHERE `email`='$email' OR `alt-email`='$email'");	
	if(mysql_num_rows($result1) > 0){		
		echo "This email is already subscribed. If you would like to unsubscribe, ";		
		echo "<a href=\"?page=contact&action=unsubscribe&list=3&email=$email\">click here</a>.<br><br>";	
	} else if(mysql_num_rows($result1B) > 0){		
		echo "This email is already subscribed. If you would like to unsubscribe, ";
		echo "<a href=\"?page=contact&action=unsubscribe&list=1&email=$email\">click here</a>.<br><br>";
	} else if(mysql_num_rows($result2) > 0){
		$query = "UPDATE `customers` SET `mailing-list`='2' WHERE `email`='$email'";		
		$result = mysql_query($query) or die(mysql_error());		
		echo $email." has been added to the mailing list.<br><br>";	
	} else if(mysql_num_rows($result3) > 0){	
		echo "This email is already subscribed. If you would like to unsubscribe, ";		
		echo "<a href=\"?page=contact&action=unsubscribe&list=3&email=$email\">click here</a>.<br><br>";	
	} else if(mysql_num_rows($result3B) > 0){	
		echo "This email is already subscribed. If you would like to unsubscribe, ";		
		echo "<a href=\"?page=contact&action=unsubscribe&list=0&email=$email\">click here</a>.<br><br>";	
	} else if(mysql_num_rows($result4) > 0){	
		$query = "UPDATE `customers` SET `mailing-list`='2' WHERE `alt-email`='$email'";		
		$result = mysql_query($query) or die(mysql_error());		
		echo $email." has been added to the mailing list.<br><br>";	
	} else if(mysql_num_rows($result5) > 0){	
		$query = "UPDATE `customers` SET `mailing-list`='0' WHERE `email`='$email'";		
		$result = mysql_query($query) or die(mysql_error());		
		echo $email." has been added to the mailing list.<br><br>";	
	} else if(mysql_num_rows($result6) > 0){	
		$query = "UPDATE `customers` SET `mailing-list`='1' WHERE `alt-email`='$email'";		
		$result = mysql_query($query) or die(mysql_error());		
		echo $email." has been added to the mailing list.<br><br>";	
	} else if(mysql_num_rows($result7) < 1){		
		$query = "INSERT INTO `customers` (`email`,`mailing-list`) VALUES ('$email','0')";		
		$result = mysql_query($query) or die(mysql_error());		echo $email." has been added to the mailing list.<br><br>";	
	} else {		
		echo "You were not subscribed to the list.<br><br>";	
	}
} else if($action == 'unsubscribe'){
	$email = mysql_real_escape_string($_GET['email']);	
	$list = mysql_real_escape_string($_GET['list']);	
	$result1 = mysql_query("SELECT * FROM `customers` WHERE `email`='$email' AND `mailing-list`='0'");	
	$result2 = mysql_query("SELECT * FROM `customers` WHERE `alt-email`='$email' AND `mailing-list`='1'");	
	$result3 = mysql_query("SELECT * FROM `customers` WHERE `email`='$email' AND `mailing-list`='2'");	
	$result4 = mysql_query("SELECT * FROM `customers` WHERE `alt-email`='$email' AND `mailing-list`='2'");	
	if(mysql_num_rows($result1) > 0){	
		$query = "UPDATE `customers` SET `mailing-list`='$list' WHERE `email`='$email'";	
		$result = mysql_query($query) or die(mysql_error());	
		echo $email." was successfully unsubscribed from the mailing list.<br><br>";	
	} else if(mysql_num_rows($result2) > 0){	
		$query = "UPDATE `customers` SET `mailing-list`='$list' WHERE `alt-email`='$email'";	
		$result = mysql_query($query) or die(mysql_error());	
		echo $email." was successfully unsubscribed from the mailing list.<br><br>";	
	} else if(mysql_num_rows($result3) > 0){	
		$query = "UPDATE `customers` SET `mailing-list`='$list' WHERE `email`='$email'";	
		$result = mysql_query($query) or die(mysql_error());	
		echo $email." was successfully unsubscribed from the mailing list.<br><br>";	
	} else if(mysql_num_rows($result4) > 0){		
		$query = "UPDATE `customers` SET `mailing-list`='$list' WHERE `alt-email`='$email'";		
		$result = mysql_query($query) or die(mysql_error());		
		echo $email." was successfully unsubscribed from the mailing list.<br><br>";	
	}	
}
?>
Office hours are now 3:00pm - 8:00pm Pacfic standard time (UTC -8) on Fridays only. The office is now 
<?php

date_default_timezone_set("America/Los_Angeles");
$currentHour = date("h");
$currentDay = date("N");

if($currentHour >= 3 && $currentHour < 8 && $currentDay == 5)
 $officeOpen = true;
else
 $officeOpen = false;
if ($officeOpen) 
 echo "<font color=green>open</font>";
else
 echo "<font color=red>closed</font>"; 
?>
.<br />
The time in Salem, OR is now 
<?php
echo $currentHour . date(":ia");
?>.<br /><br />

You can now skype me by entering the username <b>eternal.warfare</b> . <br><br>


Email: <a href="mailto:eternalwarfarerecords@gmail.com">eternalwarfarerecords(At)gmail(d0t)com</a>.<Br><br>

<table width=100% cellpadding=3 cellspacing=0 border=0>
	<tr>
		<td width=30%>
			<form action="?page=contact&action=send" method="post">Name:
		</td>
		<td>
			<input type="text" name="name" size=30>
		</td>
	</tr>
	<tr>
		<td>
			Email:
		</td>
		<td>
			<input type="text" name="email" size=30></td></tr>
		<td>
			Comment:
		</td>
		<td>
			<textarea rows=6 cols=30 name="comment"></textarea>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="submit" value="Send">
			</form>
		</td>
	</tr>
</table>

<b>Sign up for mailing list:</b><br>
<form action="?page=contact&action=add" method="post">
Email: <input name="email"><br><input type="submit" value="Add">
</form>
<br><br>

If you want to check out some silly pictures of our office, click here: <a href="?page=theoffice">The Office</a><br><br>

<font size=1>All website programming and image design by N.M. using notepad and GIMP except where noted in code.</font>