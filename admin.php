<?php



function loggedin()

{

	$user = $_SESSION['user'];



	$query = "SELECT * FROM `users` WHERE `user`='" . $user . "'";

	$result = mysql_query($query) or die(mysql_error());

	while($row = mysql_fetch_assoc($result)){

		if($_SERVER['REMOTE_ADDR'] == $row['ip']){

			return true;

		}

	}

	return false;

}



function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth )

{

  // open the directory

  $dir = opendir( $pathToImages );



  // loop through it, looking for any/all JPG files:

  while (false !== ($fname = readdir( $dir ))) {

    // parse path for the extension

    $info = pathinfo($pathToImages . $fname);

    // continue only if this is a JPEG image

    if ( strtolower($info['extension']) == 'jpg' )

    {

      echo "Creating thumbnail for {$fname} <br />";



      // load image and get image size

      $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );

      $width = imagesx( $img );

      $height = imagesy( $img );



      // calculate thumbnail size

      $new_width = $thumbWidth;

      $new_height = floor( $height * ( $thumbWidth / $width ) );



      // create a new temporary image

      $tmp_img = imagecreatetruecolor( $new_width, $new_height );



      // copy and resize old image into new image

      imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );



      // save thumbnail into a file

      imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );

    }

  }

  // close the directory

  closedir( $dir );

}





function sort_table($table){



	$query = "SELECT * FROM `" . $table . "` ORDER BY name ASC";



	$numresults = mysql_query($query) or die("query invalid");

	$numrows = mysql_num_rows($numresults);



	$i = 0;



	while($row = mysql_fetch_assoc($numresults)){

	

		$tablearray[$i]['idee'] = mysql_real_escape_string($row['id']);

		$tablearray[$i]['name'] = mysql_real_escape_string($row['name']);

		$i++;



	}



	//-------------Now the array is set.--------------





	$currentcount = count($tablearray);



	for($k = 0; $k < $currentcount; $k++){

		$idsetk = ($k + $currentcount + 1);

		$queryk  = "UPDATE `" . $table . "` SET ";

		$queryk .= "`id`=". $idsetk . " ";

		$queryk .= "WHERE `id`=". $tablearray[$k]['idee'] ." ";

	

		$numresultsk = mysql_query($queryk) or die(mysql_error() . $queryk);

	}



	for($j = 0; $j < $currentcount; $j++){

		$idset = ($j + 1);

		$query2  = "UPDATE `" . $table . "` SET ";

		$query2 .= "`id`=". $idset . " ";

		$oldid = $idset + $currentcount;

		$query2 .= "WHERE `id`=". $oldid . " ";





		$num_results2 = mysql_query($query2) or die(mysql_error() . $query);

	}







	$tablearray3 = array();



	$query3 = "SELECT * FROM `" . $table . "` ORDER BY id";



	$numresults3 = mysql_query($query3) or die("query invalid");

	$numrows3 = mysql_num_rows($numresults3);



	$i = 0;



	while($row = mysql_fetch_assoc($numresults3)){

	

		$tablearray3[$i]['idee'] = mysql_real_escape_string($row['id']);

		$tablearray3[$i]['name'] = mysql_real_escape_string($row['name']);

		$i++;



	}





}









$action = $_GET['action'];



$sessiondestroyed = "false";



















if($action == "login"){



	$logged = "false";

	$user = mysql_real_escape_string($_POST['user']);

	$password = mysql_real_escape_string($_POST['password']);

	$query = "SELECT * FROM `users` WHERE `user`='" . $user . "'";

	$result = mysql_query($query) or die(mysql_error());

	while($row = mysql_fetch_assoc($result)){



		if($row['password'] == md5($password)){

			$logged = "true";

		}

	}



	if($logged == "true"){

		$_SESSION['user'] = $user;

		$updatequery = "UPDATE `users` SET `ip`='" . 

				$_SERVER['REMOTE_ADDR'] . 

				"' WHERE `user`='" . 

				$user . "'";

		$updateresult = mysql_query($updatequery) or die(mysql_error());

	} else {

		echo "You have entered the wrong username or password.<br><br>";

	}

} else if($action == "logout"){

	

	session_destroy();

	$sessiondestroyed = "true";

	echo "You have been logged out.<br><br>";

	

}





if(loggedin() && $sessiondestroyed == "false"){









	if($action == "sendmailinglist"){	



		$subject = $_POST['subject'];

		$message = stripslashes($_POST['message']);

		$headers = "From: existenceis@eternalwarfare.org";

		$query = "SELECT * from `mailing`";

		$result = mysql_query($query) or die (mysql_error());

		while($row = mysql_fetch_assoc($result)){

			if($row["signedup"] == "true"){

				mail($row["email"], $subject, $message, $headers);

				echo "mail sent: " . $row["email"] . "<br>\n";

			}

		}

	} else if($action == 'insert'){

	

	

		//

		// Moving the uploaded file. If needed, let's produce an error.

		//

		$insertpicture = "images/default.jpg";

		if(isset($_FILES['userfile']['name']) && strlen($_FILES['userfile']['name']) > 0)

		{

				$name = basename($_FILES['userfile']['name']);

				if(get_magic_quotes_gpc())

					$name = stripslashes($name);

				$dir = "images";

				$upload_dir = ($basedir?$basedir:dirname($_SERVER['SCRIPT_FILENAME']))."/".$dir."/";

				$upload_file = $upload_dir . $name;	

	

				if(!is_uploaded_file($_FILES['userfile']['tmp_name']))

				{

					$error = "Failed upload.<br>";

				}

				else if(!@move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file))

				{

					$error = "Failed move.<br>";

				}

				else

				{

					chmod($upload_file, 0777);

					$insertpicture = "images/" . $name;

				}

			

		}

	

	

		$name = mysql_real_escape_string($_POST['name']);

		$picture = mysql_real_escape_string($insertpicture);

		$description = mysql_real_escape_string($_POST['description']);

		$price = mysql_real_escape_string($_POST['price']);

		$left = mysql_real_escape_string($_POST['left']);

		$restock = mysql_real_escape_string($_POST['restock']);

		$weight = mysql_real_escape_string($_POST['weight']);

		$format = mysql_real_escape_string($_POST['format']);

		$band = mysql_real_escape_string($_POST['band']);

	

		$querya =  "INSERT into `items` ";

		$querya .= "(`id`, `name`, `picture`, `description`, `price`, `left`, `restock`, `weight`, `format`, `band`) ";

		$querya .= "VALUES(NULL, '$name', '$picture', '$description', '$price', '$left', '$restock', '$weight', '$format', '$band')";

		$num_results = mysql_query($querya) or die(mysql_error() . $query);

	

	

		//----------item added, now sort------------------------

	

		sort_table("items");

	

		// -------------table ITEMS sorted. now thumbs----------

	

		createThumbs("images/","thumbs/images/",150);

	

		echo "Add another.....<br><br><br>";

	

	} else if($action == 'delete' && $_POST['deleteid'] != "NULL"){

	

	

		$deleteid = mysql_real_escape_string($_POST['deleteid']);

	

	

	

		$q1 = "SELECT * FROM `items`";
		$r1 = mysql_query($q1) or die(mysql_error());
		$numrows = mysql_num_rows($r1);


		$querynumber2 = "UPDATE `items` SET id=" . ($numrows+1) . " WHERE id=" . $deleteid . " ";

		$num_resultsnumber2 = mysql_query($querynumber2) or die(mysql_error() . $querynumber2);


		$querynumber3 = "UPDATE `items` SET id=" . $deleteid . " WHERE id=" . $numrows . " ";

		$num_resultsnumber3 = mysql_query($querynumber3) or die(mysql_error() . $querynumber3);


		$query = "DELETE from `items` WHERE `id`='". ($numrows+1) ."'";

		$num_results = mysql_query($query) or die(mysql_error() . $query);

	

		sort_table('items');

	

		echo "Item deleted...<br><br><br>";

	

	

	} else if($action == 'update' && $_POST['updateid'] != "NULL"){

	

		$updateid = mysql_real_escape_string($_POST['updateid']);

		$query = "SELECT * FROM `items` WHERE `id`='" . $updateid . "'";

		$result = mysql_query($query) or die(mysql_error());

	

		while($row = mysql_fetch_assoc($result)){

			

		?>

	

		<table width=100% cellpadding=3 cellspacing=0 border=0>

		<tr>

		<td width=30%>

			<form enctype="multipart/form-data" action="?page=admin&action=updatetwo" method="post">Name:</td>

			<td><input type="text" name="name" size=30 value="<?php echo stripslashes($row['name']); ?>"></td>

		</tr>

		<tr>

			<td>Description:</td>

			<td><textarea rows=6 cols=30 name="description"><?php echo stripslashes($row['description']); ?></textarea>

		</tr>

		<tr>

			<td>Price:</td>

			<td><input type="text" name="price" size=30 value="<?php echo $row['price']; ?>"></td>

		</tr>

		<tr>

			<td>Amount Left:</td>

			<td><input type="text" name="left" size=30 value="<?php echo $row['left']; ?>"></td>

		</tr>

		<tr>

			<td>Able to Restock?:</td>

			<td><SELECT name="restock">

			<option value="false" <?php if($row['restock'] == 'false')

						echo "SELECTED"; ?>>false</option>

			<option value="true" <?php if($row['restock'] == 'true')

						echo "SELECTED"; ?>>true</option></SELECT></td>

		</tr>

		<tr>

			<td>Weight (ounces):</td>

			<td><input type="text" name="weight" size=30 value="<?php echo $row['weight']; ?>"></td>

		</tr>

		<tr>

			<td>Format:</td>

			<td><select name="format">

			<option value="Tape" <?php if($row['format'] == 'Tape')

						echo "SELECTED"; ?>>Tape</option>

			<option value="Vinyl(7in)" <?php if($row['format'] == 'Vinyl(7in)')

						echo "SELECTED"; ?>>Vinyl(7in)</option>

			<option value="Vinyl(10in)" <?php if($row['format'] == 'Vinyl(10in)')

						echo "SELECTED"; ?>>Vinyl(10in)</option>

			<option value="Vinyl(12in)" <?php if($row['format'] == 'Vinyl(12in)')

						echo "SELECTED"; ?>>Vinyl(12in)</option>

			<option value="CD" <?php if($row['format'] == 'CD')

						echo "SELECTED"; ?>>CD</option>

			<option value="CDR" <?php if($row['format'] == 'CDR')

						echo "SELECTED"; ?>>CDR</option>

			<option value="Patch" <?php if($row['format'] == 'Patch')

						echo "SELECTED"; ?>>Patch</option>

			<option value="Shirt" <?php if($row['format'] == 'Shirt')

						echo "SELECTED"; ?>>Shirt</option>

			</select></td>

		</tr>

		<tr>

			<td>Band(s) [Optional]:</td>

			<td><input type="text" name="band" size=30 value="<?php echo $row['band']; ?>"></td>

		</tr>

		<tr>

			<td><a href="?page=admin">...Back</td>

			<td><input type="hidden" name="id" value="<?php echo $updateid; ?>"><input type="submit" value="UPDATE...">

		</form></td>

		</tr>

		</table>

	

		<?php

	

		}

	exit();

	} else if($action == 'updatetwo'){

	

	

	

		$id = mysql_real_escape_string($_POST['id']);

		$name = mysql_real_escape_string(stripslashes($_POST['name']));

		$description = mysql_real_escape_string(stripslashes($_POST['description']));

		$price = mysql_real_escape_string(stripslashes($_POST['price']));

		$left = mysql_real_escape_string(stripslashes($_POST['left']));

		$restock = mysql_real_escape_string(stripslashes($_POST['restock']));

		$weight = mysql_real_escape_string(stripslashes($_POST['weight']));

		$format = mysql_real_escape_string(stripslashes($_POST['format']));

		$band = mysql_real_escape_string(stripslashes($_POST['band']));

	

		$querya =  "UPDATE `items` ";

		$querya .= "SET `name`='$name', `description`='$description', `price`='$price', `left`='$left', `restock`='$restock', `weight`='$weight', `format`='$format', `band`='$band' ";

		$querya .= "WHERE `id`='$id'";

		$num_results = mysql_query($querya) or die(mysql_error() . $query);

	

	

		//----------item updated, now sort------------------------

	

		sort_table("items");

	

		echo "Item updated.<br><br>";

	

	} else if($action == 'sortonly'){

	

		sort_table('items');

	}

	

	//----------------INSERT---------------------------------

	?>

	

	

	

	<b>Add an item:</b><br>

	

	<table width=100% cellpadding=3 cellspacing=0 border=0>

	<tr>

	<td width=30%>

		<form enctype="multipart/form-data" action="?page=admin&action=insert" method="post">Name:</td>

		<td><input type="text" name="name" size=30></td>

	</tr>

	<tr>

		<td>Picture:</td>

		<td><input name="userfile" type="file" /></td>

	</tr>

	<tr>

		<td>Description:</td>

		<td><textarea rows=6 cols=30 name="description"></textarea>

	</tr>

	<tr>

		<td>Price:</td>

		<td><input type="text" name="price" size=30></td>

	</tr>

	<tr>

		<td>Amount Left:</td>

		<td><input type="text" name="left" size=30></td>

	</tr>

	<tr>

		<td>Able to Restock?:</td>

		<td><SELECT name="restock"><option value="false">false</option>

			<option value="true">true</option></SELECT></td>

	</tr>

	<tr>

		<td>Weight (ounces):</td>

		<td><input type="text" name="weight" size=30></td>

	</tr>

	<tr>

		<td>Format:</td>

		<td><select name="format">

		<option value="Tape">Tape</option>

		<option value="Vinyl">Vinyl</option>

		<option value="CD">CD</option>

		<option value="CDR">CDR</option>

		<option value="Patch">Patch</option>

		<option value="Shirt">Shirt</option>

		</select></td>

	</tr>

	<tr>

		<td>Band(s) [Optional]:</td>

		<td><input type="text" name="band" size=30></td>

	</tr>

	<tr>

		<td>&nbsp;</td>

		<td><input type="submit" value="INSERT">

		</form></td>

	</tr>

	</table>

	<?php

	

	//------------------DELETE--------------------------------------

	?>

	

	<b>Delete an item:</b><br>

	<table width=100% cellpadding=3 cellspacing=0 border=0>

	<tr>

	<td width=30%><form enctype="multipart/form-data" action="?page=admin&action=delete" method="post">Delete ID:</td>

	<td><SELECT name="deleteid"><option value="NULL">(None)</option>

	<?php

	

	

	$query = "SELECT * FROM `items` ORDER BY name ASC";

	$numresults = mysql_query($query) or die("query invalid");

	$numrows = mysql_num_rows($numresults);

	

	while($row = mysql_fetch_assoc($numresults)){

		echo "<option value=\"" . $row['id'] . "\">";

		$summary = $row['name'];

		if (strlen($summary) > 20)

			$summary = substr($summary, 0, strrpos(substr($summary, 0, 20), ' ')) . '...';

		echo stripslashes($summary);

		echo "</option>";

	}

	?>

	</select></td>

	</tr>

	<tr>

		<td>&nbsp;</td>

		<td><input type="submit" value="DELETE">

		</form></td>

	</tr>

	</table>

	

	<?php

	//---------------------UPDATE-------------------------

	?>

	

	<b>Update an item:</b><br>

	<table width=100% cellpadding=3 cellspacing=0 border=0>

	<tr>

	<td width=30%><form enctype="multipart/form-data" action="?page=admin&action=update" method="post">Update ID:</td>

	<td><SELECT name="updateid"><option value="NULL">(None)</option>

	<?php

	

	

	$query = "SELECT * FROM `items` ORDER BY name ASC";

	$numresults = mysql_query($query) or die("query invalid");

	$numrows = mysql_num_rows($numresults);

	

	while($row = mysql_fetch_assoc($numresults)){

		echo "<option value=\"" . $row['id'] . "\">";

		$summary = $row['name'];

		if (strlen($summary) > 20)

			$summary = substr($summary, 0, strrpos(substr($summary, 0, 20), ' ')) . '...';

		echo stripslashes($summary);

		echo "</option>";

	}

	?>

	</select></td>

	</tr>

	<tr>

		<td>&nbsp;</td>

		<td><input type="submit" value="UPDATE...">

		</form></td>

	</tr>

	</table>

	

	<b>Send to mailing list:</b>

	<table width=100% cellpadding=3 cellspacing=0 border=0>

	<tr>

	<td width=30%><form action="?page=admin&action=sendmailinglist" method="post">Subject:</td>

	<td><input name="subject"></td>

	</tr><tr>

	<td width=30%>Message:</td>

	<td><textarea rows=5 cols=34 name="message"></textarea></td>

	</tr><tr>

	<td width=30%>&nbsp;</td>

	<td><input type="submit" value="Send"></td>

	</table>

	

	

	

	<br><br><a href="?page=admin&action=sortonly">Sort Items</a>.<br><br>

	

	<a href="?page=admin&action=logout"><b>LOG OUT</b></a>.<br><br>

	<?php

	

	











} else { 

	//not logged in-------------------------------------------

	?>



	<table border=0 cellpadding=2 cellspacing=2 width=50%>

	<form action="?page=admin&action=login" method="post">

	<tr><td>Username:</td><td>

	<input type="text" name="user">

	</td></tr>

	<tr><td>Password:</td><td>

	<input type="password" name="password">

	</td></tr>

	<tr><td>&nbsp;</td><td>

	<input type="submit" value="LOGIN">

	</td></tr>

	</form>

	</table>



	<?php



}



?>



