<?php
//On this page, you can view a specific item from the shop.

$item = $_GET['item'];

$query = "select * from items where id=". $item ." order by id";

$numresults = mysql_query($query) or die("query invalid");
$numrows = mysql_num_rows($numresults);

?>

<?php

while($row = mysql_fetch_array($numresults)) {

	echo "<div align=center><font size=3><b>";
	echo stripslashes($row["name"]);
	echo "</b></font></div>";
	echo "<table border=0 cellpadding=3 cellspacing=3 border=0 width=100%>\n";
	echo "<tr><td align=center valign=center><a href=\"#\" onClick=\"javascript:window.open('";
	echo "../images/".$row["picture"].".jpg" . "', 'View larger image', 'resizable=yes, toolbar=no, location=no, ";
	echo "directories=no, menubar=yes, status=no')\"><img src=\"../thumbs/images/";
	echo $row["picture"].".jpg";
	$imagesize = getimagesize("../images/".$row["picture"].".jpg");
	$width = $imagesize[0];
	$height = $imagesize[1];
	if($width > $height){
		$percentage = 150 / $width;
	} else {
		$percentage = 150 / $height;
	}
	$width = $width * $percentage;
	$height = $height * $percentage;
	echo "\" width=$width height=$height><br><font size=1>Click to enlarge</font></a></td>";
	echo "<td align=justified valign=top><font size=3> $"; 
	echo $row["price"];
	echo " - ";
	echo $row["format"];
	echo ". </font><font size=2><br><br>\n";
	echo nl2br(stripslashes($row["description"]));


	if($row['restock'] == 'true'){
		$restock = "Yes";
	} else {
		$restock = "No";
	}
	$donthaveit = false;


	if($row['left'] > 0 || $row['s'] > 0 || $row['m'] > 0 || $row['l'] > 0 || $row['xl'] > 0){
		//items still in stock
		if($row['left'] > 0){
			echo "<br><br><A href=\"?page=cart&action=addtocart&item=$item\"><strong>Add to cart</strong></a><br><br>";
		}
	} else {
		$donthaveit = true;
		if($row['restock'] == "true"){
			echo "<br><br>This item is currently out of stock, but will be restocked soon.";
		} else {
			echo "<br><br>This item is out of stock permanently.";
		
		}
	}


	echo "</td><td width=20% align=right><font size=2>";

	if($donthaveit){
		//sorry out of stock
	} else {
		echo "<br>";
		if($row['s'] > 0)
			echo "<br><a href=\"?page=cart&action=addtocart&item=$item&size=s\">Size Small</a>";
		if($row['m'] > 0)	
			echo "<br><a href=\"?page=cart&action=addtocart&item=$item&size=m\">Size Medium</a>";
		if($row['l'] > 0)	
			echo "<br><a href=\"?page=cart&action=addtocart&item=$item&size=l\">Size Large</a>";
		if($row['xl'] > 0)	
			echo "<br><a href=\"?page=cart&action=addtocart&item=$item&size=xl\">Size Extra Large</a>";	

	}
	echo "</td>";



}
echo "</tr></table></div></font>";
?>
<div align="center">
<a href="?page=shop">Back to list</a></div>




