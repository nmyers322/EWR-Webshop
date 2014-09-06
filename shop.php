<?php
//which products are the customer viewing?
$view = $_GET['view'];

//which view was clicked recently?
$clicked = $_GET['clicked'];

//view as list, or as images?
if(isset($_GET['list']))
	$_SESSION['list'] = $_GET['list'];
$list = $_SESSION['list'];

//now create the query that will select the items the user wants to see
$query = "select * from items WHERE ";

if(isset($clicked) && $clicked == "yes"){
	if($view == "instock"){			

		$_SESSION["views"] = "instock";
		$query .= " (`left`>'0' OR `s`>'0' OR `m`>'0' OR `l`>'0' OR `xl`>'0') AND";	

	} else 
	if($view == "yesrestock"){

		$_SESSION["views"] = "yesrestock";
		$query .= " `left`<'1' AND `s`<'1' AND `m`<'1' AND `l`<'1' AND `xl`<'1' AND `restock`='true' AND";

	} else 
	if($view == "norestock"){

		$_SESSION["views"] = "norestock";
		$query .= " `left`<'1' AND `s`<'1' AND `m`<'1' AND `l`<'1' AND `xl`<'1' AND `restock`='false' AND";
	} else 
	if($view == "all"){
	$_SESSION["views"] = "all";
		$view = "all";
	}
} else { 
	if(isset($_SESSION["views"]) && $_SESSION["views"] != ""){
		$view = $_SESSION["views"];
	} else {
		$view = "all";
	}
	if($view == "instock"){			

		$_SESSION["views"] = "instock";
		$query .= " (`left`>'0' OR `s`>'0' OR `m`>'0' OR `l`>'0' OR `xl`>'0') AND";	

	} else 
	if($view == "yesrestock"){

		$_SESSION["views"] = "yesrestock";
		$query .= " `left`<'1' AND `s`<'1' AND `m`<'1' AND `l`<'1' AND `xl`<'1' AND `restock`='true' AND";

	} else 
	if($view == "norestock"){

		$_SESSION["views"] = "norestock";
		$query .= " `left`<'1' AND `s`<'1' AND `m`<'1' AND `l`<'1' AND `xl`<'1' AND `restock`='false' AND";
	} else 
	if($view == "all"){
	$_SESSION["views"] = "all";
		$view = "all";
	}
}

$query .= " `show`='true' ORDER BY `NAME` ASC";

?>

<div align="center">
  <table cellpadding="3" cellspacing="2" valign="center" align="center" width="100%" height="20">
    <tr>
      <td <?php
        if($view == "all"){ 

	        echo "class=\"cartactivetd\""; 
        } else { 
	        echo "class=\"cartviewtd\""; 
        }
        ?> valign="center" align="center"><a href="?page=shop&view=all&clicked=yes">View all</a></td>


      <td <?php
        if($view == 'instock'){ 
	        echo "class=\"cartactivetd\""; 
        } else { 
	        echo "class=\"cartviewtd\""; 
        }
        ?> valign="center" align="center"><a href="?page=shop&view=instock&clicked=yes">In stock</a></td>


      <td <?php
        if($view == 'yesrestock'){ 
	        echo "class=\"cartactivetd\""; 
        } else { 
	        echo "class=\"cartviewtd\""; 
        }
        ?> valign="center" align="center"><a href="?page=shop&view=yesrestock&clicked=yes">Out of stock temporarily</a></td>


      <td <?php
        if($view == 'norestock'){ 
	        echo "class=\"cartactivetd\""; 
        } else { 
	        echo "class=\"cartviewtd\""; 
        }
        ?> valign="center" align="center"><a href="?page=shop&view=norestock&clicked=yes">Out of stock permanently</a></td>

    </tr>
  </table>
</div>
<br><br>
  
<?php
if($list != "true"){
	?>
	<div align="center"><a href="?page=shop&list=true">View as list</a></div>

<?php
}else{
	?> 
    <div align="center"><a href="?page=shop&list=false">View as images</a></div>
    <?php
}
?>

<div align="center">
  <a href="?page=cart"><font size="3">View Cart / Checkout | 
    <?php

    if(isset($_SESSION["items"])){
	    $numberofitems = 0;
	    for($i=0; $i < count($_SESSION["items"]); $i++){
		    $numberofitems+= $_SESSION["quantitys"][$i];
	    }
	    echo "(". $numberofitems . " items)";
    } else { 
	    echo "(0 items)";
    }
    ?>
  </font></a>
</div>


<?php
//Now display the items

$numtds = 3;

$tdpercent = (100/$numtds);

$numresults = mysql_query($query) or die("query invalid");
$numrows = mysql_num_rows($numresults);

$tr = 0;
$td = 0;

if(empty($s)) { $s = 0; }

$count = 1 + $s; 

if($numrows < 1){
	echo "<div align=center><br><br><br><br><b>No items meet the criteria.</b><br><br><br><br></div>";
} else {
	?>
<table cellpadding="3" cellspacing="3" border="0" width="100%">
<?php
	if($list == "true"){
		while($row = mysql_fetch_assoc($numresults)) {
			echo "<a href=\"?page=viewitem&item=" . $row["id"] . "&view=". $view ."\">";
			echo "<font size=3 color=red><b>" . $row["name"] . "</b> - $"; 
			echo $row["price"];
			echo " - ";
			echo $row["format"];
			echo ".</a> <br>";
		}
	} else {
		while($row = mysql_fetch_array($numresults)) {
			if($tr < 1){
				echo "<tr>";
				$tr = 1;
			}
			if($td < $numtds){
				echo "<td width=\"" . $tdpercent . "%\" valign=\"bottom\">";
				$td++;
			}
			echo "<div align=center><table width=150><tr><td><font size=1 color=red><b>";
			echo $row["name"];
			echo "</b></font><br>";
			echo "<a href=\"?page=viewitem&item=";
			echo $row["id"];
			echo "&view=". $view ."\"><img src=\"thumbs/images/";
			echo $row["picture"];
			$imagesize = getimagesize("thumbs/images/".$row["picture"].".jpg");
			$width = $imagesize[0];
			$height = $imagesize[1];
			if($width > $height){
				$percentage = 150 / $width;
			} else {
				$percentage = 150 / $height;
			}
			$width = $width * $percentage;
			$height = $height * $percentage;
			echo ".jpg\" width=\"" . $width . "\" height=\"" . $height . "\"></a><br>";
			echo "<font size=1> $"; 
			echo $row["price"];
			echo " - ";
			echo $row["format"];
			echo ". </td></tr></table></div></td>";
			if($td == $numtds){
				echo "</tr>\n";
				$tr = 0;
				$td = 0;
			}
		}
	
		if($td != 0){
			while($td < $numtds){
				echo "<td width=\"" . $tdpercent . "%\" valign=\"bottom\">&nbsp;</td>";
				$td++;
			}
			echo "</tr>\n" ;
			$tr = 0;
			$td = 0;
		}
	}
}
?>
</table>

  <?php
if($list != "true"){
	?>
	<div align=center><a href="?page=shop&list=true">View as list</a></div>

<?php
}else{
	?> 
    <div align=center><a href="?page=shop&list=false">View as images</a></div>
    <?php
}
?>
<div align=center><a href="?page=cart"><font size=3>View Cart / Checkout | 
<?php
if(isset($_SESSION["items"])){
	$numberofitems = 0;
	for($i=0; $i < count($_SESSION["items"]); $i++){
		$numberofitems+= $_SESSION["quantitys"][$i];
	}
	echo "(". $numberofitems . " items)";
} else { 
	echo "(0 items)";
}
?></font></a></div>


