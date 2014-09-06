<?php

$action = $_GET['action'];
$item = $_GET['item'];
$newquantity = $_POST['quantity'];
$country = $_GET['country'];
//shirt size
$size = $_GET['size'];


if($action == 'addtocart'){
	$foundit = 0;
	for($i = 0; $i<count($_SESSION["items"]); $i++){
		if($size != ""){
			if($_SESSION["items"][$i] == $item && $size == $_SESSION["size"][$i]){
				$_SESSION["quantitys"][$i] += 1;
				$foundit = 1;
			}
		} else {
			if($_SESSION["items"][$i] == $item){
				$_SESSION["quantitys"][$i] += 1;
				$foundit = 1;
			}
		}
	}
	if($foundit == 0){
		if(isset($_SESSION["items"])){
			$newestitem = count($_SESSION["items"]);
		} else {
			$newestitem = 0;
		}
	
		$_SESSION["items"][$newestitem] = $item;
		if($size!=""){
			$_SESSION["size"][$newestitem] = $size;}

		
			$_SESSION["quantitys"][$newestitem] = 1;
		
	}


}

if($action == 'clearcart'){

	session_destroy();
	echo "Your cart was emptied. You are being redirected to the shop.<br>\n";
	echo "<meta http-equiv=\"refresh\" content=\"2;url=?page=shop\">";
} else {

	if($action == 'updatecart'){
		if(isset($_SESSION["size"][$item]))
			$checksize = $_SESSION["size"][$item];
			
		else
			$checksize = "";
		$continueupdate = true;

		$query = "select * from items where id=" . $_SESSION["items"][$item];
		$numresults = mysql_query($query) or die ("invalid query1");
		
		if(check_quantity($newquantity)){

			while($row3 = mysql_fetch_assoc($numresults)){
				if($checksize != ""){
					if($newquantity > $row3[$checksize]){
						$continueupdate = false;
						?>
						<script language="javascript" type="text/javascript">
						alert('Our inventory is too small for that amount. You may order up to <?php
						echo $row3[$checksize];
						?>. Please visit the Bulk Orders section for more.');
						</script>
						<?php
					}
				} else {
					if($newquantity > $row3['left']){
						$continueupdate = false;
						?>
						<script language="javascript" type="text/javascript">
						alert('Our inventory is too small for that amount. You may order up to <?php
						echo $row3['left'];
						?>. Please visit the Bulk Orders section for more.');
						</script>
						<?php
					}
				}
			}
			
                        if($continueupdate == true) {
				$_SESSION["quantitys"][$item] = $newquantity;
			}
		} else {
			?>
			<script language="javascript" type="text/javascript">
			alert('That is not a valid amount.');
			</script>
			<?php
		}
	}

	if($action == 'delete'){
		
		for($i = 0; $i < count($_SESSION["items"]); $i++){
			if($size != ""){
				if($_SESSION["items"][$i] == $item && $size == $_SESSION["size"][$i]){
					unset($_SESSION["items"][$i]);
					unset($_SESSION["quantitys"][$i]);
					unset($_SESSION["size"][$i]);
				}
			} else { 
				if($_SESSION["items"][$i] == $item){
					unset($_SESSION["items"][$i]);
					unset($_SESSION["quantitys"][$i]);
				}
			}
		}
		items_sort();
	}


	if(isset($_SESSION["items"])){	
		
		
		$totalprice = 0.0;
		$totalshipping = 0.0;
		
		
		
		
		echo "<table class=\"cart\" width=100%>";
		echo "<tr><td width=55%><b><u>Item</u></b></td><td width=5%><font size=1><u>Delete</u></font></td>";
		echo "<td width=15%><b><u>Quantity</u></b></td><td width=25%><b><u>Price</u></b></td></tr>";
		$length = count($_SESSION["items"]);
                $vinyl = 0;

		for($i=0; $i < count($_SESSION["items"]); $i++){
			if(isset($_SESSION["items"][$i])){
				$query = "select * from items where id=" . $_SESSION["items"][$i];
				$numresults = mysql_query($query) or die ("invalid query2");
				while($row = mysql_fetch_assoc($numresults)){
					if($row["format"] == "Vinyl"){
						$vinyl = 1;
                                        }
					echo "<tr>";
					echo "<td>" . $row["name"]; 
					if(isset($_SESSION["size"][$i]) && $_SESSION["size"][$i] != "")
						echo " - Size ". strtoupper($_SESSION["size"][$i]);
					echo "</td>";
					echo "<td>";
					if(count($_SESSION["items"]) < 2){
						echo "<input type=\"checkbox\" onClick=\"javascript:parent.location='?page=cart&action=clearcart'\">";
					} else {
						echo "<input type=\"checkbox\" onClick=\"javascript:parent.location='?page=cart&action=delete&item=". $row["id"] . "&size=".$_SESSION["size"][$i]."'\">";
					}
					echo "</td>";
					echo "<td><div align=\"center\" valign=\"bottom\">";

					echo "<form action=\"?page=cart&action=updatecart&item=". $i ."\" method=\"post\"><input type=\"text\" name=\"quantity\" size=\"2\" value=\"". $_SESSION["quantitys"][$i] ."\">";
					echo " <input type=\"image\" src=\"images/quantitysubmit.gif\" border=0 alt=\"update\"></form>";
					echo "</div></td>";
					echo "<td>$". number_format(($row["price"] * $_SESSION["quantitys"][$i]), 2) ."</td>";
					$totalprice = $totalprice + ($row["price"] * $_SESSION["quantitys"][$i]);
					$totalshipping = $totalshipping + ($row["weight"] * $_SESSION["quantitys"][$i]);
                                        
					echo "</tr>\n";
				}
			}
			
		}
//------------------------------------------------------------------SHIPPING--------------------------------------------------------
if ($vinyl == 1){
      $totalshipping = $totalshipping + 5;
}
		if($country != ""){
			$queryc = "select * from internationalgroups where id = '" . $country . "'";
			$numresultc = mysql_query($queryc) or die(mysql_error());
			while($rowc = mysql_fetch_assoc($numresultc)){
				$_SESSION["countrygroup"] = $rowc['group'];
				$_SESSION["countryselected"] = $rowc['id'];
			}
		} else 
		if(!isset($_SESSION["countryselected"])){
			$_SESSION["countrygroup"] = 0;
			$_SESSION["countryselected"] = 1;
		}

		if($_SESSION["countrygroup"] > 0){
			$groupnum = $_SESSION["countrygroup"];
			$queryn = "SELECT * FROM internationalprices ORDER BY weight ASC";
			$numresultn = mysql_query($queryn) or die(mysql_error());
			while($rown = mysql_fetch_assoc($numresultn)){
				if($rown['id'] == 1){ 
					if($groupnum == 1){
						$shippingprice = $rown['group1'];
					} else
					if($groupnum == 2){
						$shippingprice = $rown['group2'];
					} else
					if($groupnum <= 5){
						$shippingprice = $rown['group5'];
					} else
					if($groupnum <= 9){
						$shippingprice = $rown['group9'];
					}
					$nextone = false;
				}
				if($nextone == true){
					if($groupnum == 1){
						$shippingprice = $rown['group1'];
					} else
					if($groupnum == 2){
						$shippingprice = $rown['group2'];
					} else
					if($groupnum <= 5){
						$shippingprice = $rown['group5'];
					} else
					if($groupnum <= 9){
						$shippingprice = $rown['group9'];
					}
					$nextone = false;
				}
				if($totalshipping > $rown['weight']){
					$nextone = true;
				}
			}
		} else {
			
			$queryn = "SELECT * FROM domesticprices ORDER BY weight ASC";
			$numresultn = mysql_query($queryn) or die(mysql_error());
			while($rown = mysql_fetch_assoc($numresultn)){


				if($rown['id'] == 1){ 
					$shippingprice = $rown['cost']; 
					$nextone = false;
				}
				if($nextone == true){
					$shippingprice = $rown['cost'];
					$nextone=false;
				}
				if($totalshipping > $rown['weight']){
					$nextone = true;
				}
			}
		}

		echo "<tr><td valign=center><form id=\"selectform\" action=\"#\"><SELECT id=\"mymenu\">";
		$queryselect = "select * from internationalgroups order by `country` ASC";
		$numresultsselect = mysql_query($queryselect) or die(mysql_error());
		while($rows = mysql_fetch_assoc($numresultsselect)){
			echo "<option value=\"" . $rows['id'] . "\" ";
			if($rows['id'] == $_SESSION["countryselected"]){
				echo "selected";
			}
			echo ">" . $rows['country'] . "</option>\n";
		}
		echo "</SELECT></form></td><td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td>&nbsp;</td></tr>";

		?>

		<script type="text/javascript">

		var selectmenu=document.getElementById("mymenu")
		selectmenu.onchange=function(){ 
		 var chosenoption=this.options[this.selectedIndex] 
		 if (chosenoption.value!="nothing"){
		  window.open("?page=cart&country=" + chosenoption.value, "_parent") 
		 }
		}

		</script>



		<?php


		if($totalshipping == 0){ $shippingprice = 0; }

		echo "<tr><td><b>Shipping</b></td><td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td><b>$". number_format($shippingprice, 2) . "</b></td></tr>\n";

		$totalprice += $shippingprice;
		echo "<tr><td><font size=4><b>Total</b></font></td><td>&nbsp;</td><td>&nbsp;</td>";
		echo "<td><font size=4><b>$". number_format($totalprice, 2) . "</b></font></td></tr>\n";

		echo "</table>";
		echo "<div align=center>";

		$_SESSION["shippingprice"] = $shippingprice;
		$_SESSION["totalprice"] = $totalprice;
		$_SESSION["Payment_Amount"] = $totalprice;


    $enabled = "true";

    if($enabled == "true"){
		
			    ?><br><br>
			    <form action='expresscheckout.php' METHOD='POST'>
			    <input type='image' name='submit' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' border='0' align='top' alt='Check out with PayPal'/>
			    </form>
			    <?php

			    echo "<div align=center><a href=\"?page=shop\">Continue shopping</a></div><br>";
			    echo "<div align=center><a href=\"?page=cart&action=clearcart\">Empty your cart</a></div>";
    } else {
		
		    echo "<br><br><b>CHECKOUT IS CURRENTLY DISABLED!<br><br>";
		
    }
		
		

	} else { 
		echo "Your cart is empty.";
	}
}

?>

