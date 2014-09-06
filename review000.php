<?php
/*==================================================================
 PayPal Express Checkout Call
 ===================================================================
*/
// Check to see if the Request object contains a variable named 'token'	
$token = "";
if (isset($_REQUEST['token']))
{
	$token = $_REQUEST['token'];
}

// If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.	
if ( $token != "" )
{


	require_once ("paypalfunctions.php");

	/*
	'------------------------------------
	' Calls the GetExpressCheckoutDetails API call
	'
	' The GetShippingDetails function is defined in PayPalFunctions.jsp
	' included at the top of this file.
	'-------------------------------------------------
	*/
	

	$resArray = GetShippingDetails( $token );
	$ack = strtoupper($resArray["ACK"]);
	if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING") 
	{
	   if(count($_SESSION["items"]) < 1){
		echo "The items in your cart were somehow lost due to server malfunction. Sorry, please try again. <a href=\"?page=shop\">Click here to return to shop.</a>";
	   } else {		/*
		' The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review 
		' page		
		*/
		$email 				= $resArray["EMAIL"]; // ' Email address of payer.
		$payerId 			= $resArray["PAYERID"]; // ' Unique PayPal customer account identification number.
		$payerStatus		= $resArray["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
		$salutation			= $resArray["SALUTATION"]; // ' Payer's salutation.
		$firstName			= $resArray["FIRSTNAME"]; // ' Payer's first name.
		$middleName			= $resArray["MIDDLENAME"]; // ' Payer's middle name.
		$lastName			= $resArray["LASTNAME"]; // ' Payer's last name.
		$suffix				= $resArray["SUFFIX"]; // ' Payer's suffix.
		$cntryCode			= $resArray["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
		$business			= $resArray["BUSINESS"]; // ' Payer's business name.
		$shipToName			= $resArray["SHIPTONAME"]; // ' Person's name associated with this address.
		$shipToStreet		= $resArray["SHIPTOSTREET"]; // ' First street address.
		$shipToStreet2		= $resArray["SHIPTOSTREET2"]; // ' Second street address.
		$shipToCity			= $resArray["SHIPTOCITY"]; // ' Name of city.
		$shipToState		= $resArray["SHIPTOSTATE"]; // ' State or province
		$shipToCntryCode	= $resArray["SHIPTOCOUNTRYCODE"]; // ' Country code. 
		$shipToZip			= $resArray["SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
		$addressStatus 		= $resArray["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal   
		$invoiceNumber		= $resArray["INVNUM"]; // ' Your own invoice or tracking number, as set by you in the element of the same name in SetExpressCheckout request .
		$phonNumber			= $resArray["PHONENUM"]; // ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one. 

//-----------------------------------------ADD CONTENT HERE-----------------------------------------------------------

echo "<b><u><font size=3>Review Order</font></u></b><br><br>";
$_SESSION["previous-cust"] = "false";
$customerq = "SELECT * from customers where email='".$email."'";
$customerr = mysql_query($customerq) or die("failed query. contact eternalwarfarerecords@gmail.com");
while($customer = mysql_fetch_assoc($customerr)){
	//$dbEmail = $customer["email"];
	$dbAltEmail = $customer["alt-email"];
	$_SESSION["cust-alt-email"] = $dbAltEmail;
	$dbMailingList = $customer["mailing-list"];
	$_SESSION["cust-mailing-list"] = $dbMailingList;
	//$dbAddress = $customer["address"];
	$_SESSION["previous-cust"] = "true";
}
$_SESSION["cust-email"] = $email;
echo "<form action=\"?page=confirm\" method=post>";
echo "<table class=\"cart\" width=100%>";
echo "<tr><td width=60%><u><b>Shipping address:</b></u></td><td><b><u>Bill to:</b></u></td></tr>";
echo "<tr><td><br><div align=center><b>";
echo $shipToName."<br>".$shipToStreet;
if($shipToStreet2 != "")
	echo "<br>".$shipToStreet2;
echo "<br>". $shipToCity .", ".$shipToState." ".$shipToZip;
echo "<br>". convertCountry($shipToCntryCode)."</b>";


$_SESSION["cust-address"] = $shipToName."\n".$shipToStreet;
if($shipToStreet2 != "")
	$_SESSION["cust-address"] .= "\n".$shipToStreet2;
$_SESSION["cust-address"] .= "\n". $shipToCity .", ".$shipToState." ".$shipToZip;
$_SESSION["cust-address"] .= "\n". convertCountry($shipToCntryCode);


echo "<br><br><font size=1>If you'd like to enter an alternative shipping address, enter it below:</font><br>";
echo "<table border=0 cellpadding=0 cellspacing=0><tr><td width=50%>";
echo "Name<br>Address<br>City/State/Postal Code<br>Country</td>";
echo "<td><textarea name=\"altshipping\" rows=4 cols=27></textarea></td></tr></table>";
echo "</div></td><td width=50%><div align=center>";
echo "<b>".$email."</b><br><br>";
echo "<input type=checkbox name=\"addmailing\" ";
if(!isset($dbMailingList) || $dbMailingList == 0 || $dbMailingList == 2)
	echo "checked";
echo "><font size=1>Check here if you want this email added to the mailing list.</font><br><br><br>";
echo "<font size=1>To add a different email to the mailing list, enter it below:</font><br>";
echo "<input type=text name=\"altemail\" size=40 ";
if(isset($dbMailingList) && ($dbMailingList == 1 || $dbMailingList == 2))
	echo "value=\"".$dbAltEmail."\"";
echo "><br><br>";
echo "</div></td></tr></table>";
echo "<br><br>";
echo "<table class=\"cart\" width=100%>";
echo "<tr><td><b><u>Item</b></u></td><td><b><u>Quantity</b></u></td><td><b><u>Price</b></u></td></tr>";

$length = count($_SESSION["items"]);
for($i=0; $i < count($_SESSION["items"]); $i++){
	if(isset($_SESSION["items"][$i])){
		$query = "select * from items where id=" . $_SESSION["items"][$i];
		$numresults = mysql_query($query) or die ("invalid query2");
		while($row = mysql_fetch_assoc($numresults)){
			echo "<tr>";
			echo "<td cellpadding=3>" . $row["name"]; 
			$_SESSION["order-details"] .= $row["name"];
			if(isset($_SESSION["size"][$i]) && $_SESSION["size"][$i] != ""){
				echo " - Size ". strtoupper($_SESSION["size"][$i]);
				$_SESSION["order-details"] .= " - Size ". strtoupper($_SESSION["size"][$i]);
			}
			$_SESSION["order-details"] .= "              ";
			echo "</td>";
			echo "<td>".$_SESSION["quantitys"][$i]."</td>";
			$_SESSION["order-details"] .= $_SESSION["quantitys"][$i] . "        ";
			echo "<td>$". number_format(($row["price"] * $_SESSION["quantitys"][$i]), 2) ."</td>";
			$_SESSION["order-details"] .= number_format(($row["price"] * $_SESSION["quantitys"][$i]), 2) . "\n";
			//$totalprice = $totalprice + ($row["price"] * $_SESSION["quantitys"][$i]);
			//$totalshipping = $totalshipping + ($row["weight"] * $_SESSION["quantitys"][$i]);
			echo "<tr>\n";
		}
	}
	
}

//shipping:
echo "<tr><td><b>Shipping</b></td><td>&nbsp;</td><td><b>";
echo $_SESSION["shippingprice"];
$_SESSION["order-details"] .= "Shipping                        ".$_SESSION["shippingprice"]."\n\n";
echo "</b></td></tr>";
echo "<tr><td><font size=4><b>Total</b></font></td><td>&nbsp;</td><td><font size=4><b>";
echo $_SESSION["totalprice"];
$_SESSION["order-details"] .= "Total                        ".$_SESSION["totalprice"]."\n";
echo "</b></font></td></tr></table>";

echo "<br><div align=center><input type=submit value=\"Confirm Order\"></div></form>";
/*?>
<br><p align=center><a href="?page=confirm"><font size=3><b>Confirm Order</b><font></a></p>
<?php*/






	   }
//-------------------------------------END ADD CONTENT-------------------------------------------------------------------
	} 
	else  
	{
		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);
		
		echo "GetExpressCheckoutDetails API call failed. ";
		echo "Detailed Error Message: " . $ErrorLongMsg;
		echo "Short Error Message: " . $ErrorShortMsg;
		echo "Error Code: " . $ErrorCode;
		echo "Error Severity Code: " . $ErrorSeverityCode;
	}
}
		
?>
