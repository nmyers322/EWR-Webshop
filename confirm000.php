<?php
	/*==================================================================
	 PayPal Express Checkout Call
	 ===================================================================
	*/
echo "About to process order...<br><br>If nothing happens, the order was NOT placed.<br><Br>";
require_once ("paypalfunctions.php");

	//I set this variable because I wasn't sure what the line below it came from. I may need to change this later.
$PaymentOption = "PayPal";

if ( $PaymentOption == "PayPal" )
{
	/*
	'------------------------------------
	' The paymentAmount is the total value of 
	' the shopping cart, that was set 
	' earlier in a session variable 
	' by the shopping cart page
	'------------------------------------
	*/
	
	$finalPaymentAmount =  $_SESSION["Payment_Amount"];
		
	/*
	'------------------------------------
	' Calls the DoExpressCheckoutPayment API call
	'
	' The ConfirmPayment function is defined in the file PayPalFunctions.jsp,
	' that is included at the top of this file.
	'-------------------------------------------------
	*/


	$resArray = ConfirmPayment ( $finalPaymentAmount );
	$ack = strtoupper($resArray["ACK"]);
	if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
	{
		/*
		'********************************************************************************************************************
		'
		' THE PARTNER SHOULD SAVE THE KEY TRANSACTION RELATED INFORMATION LIKE 
		'                    transactionId & orderTime 
		'  IN THEIR OWN  DATABASE
		' AND THE REST OF THE INFORMATION CAN BE USED TO UNDERSTAND THE STATUS OF THE PAYMENT 
		'
		'********************************************************************************************************************
		*/

		$transactionId		= $resArray["TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs. 
		$transactionType 	= $resArray["TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout 
		$paymentType		= $resArray["PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant 
		$orderTime 			= $resArray["ORDERTIME"];  //' Time/date stamp of payment
		$amt				= $resArray["AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
		$currencyCode		= $resArray["CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD. 
		$feeAmt				= $resArray["FEEAMT"];  //' PayPal fee amount charged for the transaction
		$settleAmt			= $resArray["SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
		$taxAmt				= $resArray["TAXAMT"];  //' Tax charged on the transaction.
		$exchangeRate		= $resArray["EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer’s account.
		
		/*
		' Status of the payment: 
				'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
				'Pending: The payment is pending. See the PendingReason element for more information. 
		*/
		
		$paymentStatus	= $resArray["PAYMENTSTATUS"]; 

		/*
		'The reason the payment is pending:
		'  none: No pending reason 
		'  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile. 
		'  echeck: The payment is pending because it was made by an eCheck that has not yet cleared. 
		'  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview. 		
		'  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment. 
		'  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment. 
		'  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service. 
		*/
		
		$pendingReason	= $resArray["PENDINGREASON"];  

		/*
		'The reason for a reversal if TransactionType is reversal:
		'  none: No reason code 
		'  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer. 
		'  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee. 
		'  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer. 
		'  refund: A reversal has occurred on this transaction because you have given the customer a refund. 
		'  other: A reversal has occurred on this transaction due to a reason not listed above. 
		*/
		
		$reasonCode		= $resArray["REASONCODE"];   

//----------------------------BEGIN CONTENT---------------------------------------

	//use correct shipping address
		if(isset($_POST["altshipping"]) && $_POST["altshipping"] != ""){
			$_SESSION["cust-address"] = $_POST["altshipping"];
		}
	//update address
		$addressQuery = "SELECT * FROM `customers` WHERE `email`='".$_SESSION["cust-email"]."'";
		$addressResult = mysql_query($addressQuery);
		while($addressRow = mysql_fetch_assoc($addressResult)){
			//if they dont have an address yet but do exist as a customer...or if they have a new address...
			if($addressRow["address"] == "" || $addressRow["address"] != $_SESSION["cust-address"]){
				$insertAddress = "UPDATE `customers` SET `address`='".$_SESSION["cust-address"]."' WHERE `email`='".$_SESSION["cust-email"]."'";
				$insertAddressResult = mysql_query($insertAddress);
			}
		}
	//update mailing list
		//if they are a new customer and don't exist as a secondary email elsewhere,
		$check = "SELECT * FROM customers WHERE `alt-email`='".$_SESSION["cust-email"]."'";
		if($_SESSION["previous-cust"] == "false" && mysql_num_rows(mysql_query($check)) < 1){
			//and they checked the "add me to mailing list" box....
			if(isset($_POST["addmailing"])){
				//insert as a new customer with
				$query = "INSERT into `customers` (`email`, `alt-email`, `mailing-list`, `address`) VALUES ";
				$query .="('".$_SESSION["cust-email"]."','";
				//just one email on the list
				$mailinglist = "0";
				//or both if they are there
				if(isset($_POST["altemail"]) && $_POST["altemail"] != ""){
					$query .= $_POST["altemail"];
					$mailinglist = "2";
				}
				$query .="','$mailinglist','".$_SESSION["cust-address"]."')";
			//if they did'nt check the "add me to mailing list" box...
			} else {
				//and they entered a secondary email...
				if(isset($_POST["altemail"]) && $_POST["altemail"] != ""){
					//insert as a new customer but only sign up the second email for mailing list
					$query = "INSERT into `customers` (`email`, `alt-email`, `mailing-list`, `address`) VALUES ";
					$query .="('".$_SESSION["cust-email"]."','";
					$query .= $_POST["altemail"];
					$query .="','1','".$_SESSION["cust-address"]."')";
				//but if they didn't enter a 2nd email...
				} else {
					//insert new cust w status=3
					$query = "INSERT into `customers` (`email`, `alt-email`, `mailing-list`, `address`) VALUES ";
					$query .="('".$_SESSION["cust-email"]."', '', '3', '".$_SESSION["cust-address"]."')";
				}
			}
		//if they exist elsewhere as secondary..
		} else if(mysql_num_rows(mysql_query($check)) > 0){
			//do nothing
			$query = "";
		//or if they are returning cust
		} else {
			//and they have changed their secondary email,
			if(isset($_POST["altemail"]) && $_POST["altemail"] != $_SESSION["cust-alt-email"]){
				//and the box is checked.....
				if(isset($_POST["addmailing"])){
					if($_POST["altemail"] != ""){
						//status=2
						$mailinglist = "2";
					} else {
						$mailinglist = "0";
					}
				//but if box isnt checked....
				} else {
					//status=1
					$mailinglist = "1";
				}
				//query = update customer
				$query = "UPDATE `customers` SET `alt-email`='".$_POST["altemail"]."',`mailing-list`='$mailinglist' WHERE `email`='".$_SESSION["cust-email"]."'";
			//but if they haven't changed their secondary email but it does exist...
			} else if(isset($_SESSION["cust-alt-email"]) && $_SESSION["cust-alt-email"] != ""){
				//and if the box is checked...
				if(isset($_POST["addmailing"])){
					$query = "UPDATE `customers` SET `mailing-list`='2' WHERE `email`='".$_SESSION["cust-email"]."'";
				//but if the box isnt checked...
				} else {
					$query = "UPDATE `customers` SET `mailing-list`='1' WHERE `email`='".$_SESSION["cust-email"]."'";
				}
			//OR if their secondary email doesn't exist...
			} else {
				//and if the box is checked...
				if(isset($_POST["addmailing"])){
					$query = "UPDATE `customers` SET `mailing-list`='0',`alt-email`='' WHERE `email`='".$_SESSION["cust-email"]."'";
				//but if the box isnt checked...
				} else {
					$query = "UPDATE `customers` SET `mailing-list`='3',`alt-email`='' WHERE `email`='".$_SESSION["cust-email"]."'";
				}
			}
		}
		//NOW perform the query
		if($query != ""){
			$result = mysql_query($query) or die("'Mailing list update' query failed! The order was paid for but NOT placed on the site. Please contact eternalwarfarerecords@gmail.com and include your paypal email (".$_SESSION["cust-email"].") and amount paid ($".$_SESSION["totalprice"]."). Error:<br>".mysql_error());
		}

	//protect yo stuff
		$insertorderdetails = mysql_real_escape_string($_SESSION["order-details"]);
		$insertcustaddress = mysql_real_escape_string($_SESSION["cust-address"]);

	//insert order into database
		$insertQuery = "INSERT into `orders` (`transactionId`, `email`, `shipTo`, `orderDetails`, `orderTime`, `otherDetails`, `shipped`) VALUES ('";
		$insertQuery .= $transactionId . "','". $_SESSION["cust-email"] ."','". $insertcustaddress."','".$insertorderdetails."','".date("Y-m-d h:i")."',";
		$insertQuery .= "'Order Time:".$orderTime."\nTransaction type:".$transactionType."\nPayment type:".$paymentType."\n";
		$insertQuery .= "Amount: ".$amt."\nCurrency code: ".$currencyCode."\nFee amount: ".$feeAmt."\nSettle amount: ".$settleAmt."\nExchange rate: ".$exchangeRate."\nTax amount: ".$taxAmt."', 'false')";

	

		$insertResult = mysql_query($insertQuery) or die("Sorry, a database malfunction occurred on the server. The order was paid for but NOT placed on the site's database. Please contact eternalwarfarerecords@gmail.com and include your paypal email (".$_SESSION["cust-email"].") and the list of items bought. Error:<br>".mysql_error());
	//email the customer
		$orderNum = mysql_num_rows(mysql_query("SELECT * FROM `orders`"));
		$emailMessage = "Your order from www.EternalWarfare.org was successfully placed. You will receive a notification when the order is shipped. Please give it up to a week before asking about a non-shipped order. Thanks for the order!\n\n";
		$emailMessage .= "Your order number: " . str_pad($orderNum, 5, "0", STR_PAD_LEFT) . "\n";
		//$emailMessage .= "Paypal Transaction ID: " . $transactionId . "\n";
		$emailMessage .= "Order time: " . date("Y-m-d h:i")."\n";//$orderTime . "\n";
		$emailMessage .= "Customer Email: ". $_SESSION["cust-email"]."\n";
		$emailMessage .= "\n\n";
		
		$emailMessage .= "Shipping address:\n". nl2br(str_replace("&lt;br /&gt;", " ", $_SESSION["cust-address"]));
		$emailMessage .= "\nBill to (Paypal):\n". $_SESSION["cust-email"]."\n\n";

		$emailMessage .= "Item                                 Quantity      Price\n";

		$length = count($_SESSION["items"]);
		for($i=0; $i < count($_SESSION["items"]); $i++){
			if(isset($_SESSION["items"][$i])){
				$query = "select * from items where id=" . $_SESSION["items"][$i];
				$numresults = mysql_query($query) or die ("There was an error in the email query. Your order was otherwise successfully placed. Please contact eternalwarfarerecords@gmail.com and include your paypal email (".$_SESSION["cust-email"].") and amount paid ($".$_SESSION["totalprice"]."). Error:".mysql_error());
				while($row = mysql_fetch_assoc($numresults)){
					$emailMessage .= $row["name"]; 
					if(isset($_SESSION["size"][$i]) && $_SESSION["size"][$i] != ""){
						$emailMessage .= " - Size ". strtoupper($_SESSION["size"][$i]);
					}
					$emailMessage .=  "                 ";
					$emailMessage .= $_SESSION["quantitys"][$i]."           ";
					$emailMessage .= "$". number_format(($row["price"] * $_SESSION["quantitys"][$i]), 2);
					$emailMessage .= "\n";
				}
			}
	
		}

		//shipping:
		$emailMessage .= "Shipping                                          ";
		$emailMessage .= $_SESSION["shippingprice"] ."\n";
		$emailMessage .= "Total                                             ";
		$emailMessage .= $_SESSION["totalprice"] ."\n\n";
		

		$emailMessage .= "http://www.EternalWarfare.org || eternalwarfarerecords@gmail.com";

		$emailHeader = "From: eternalwarfarerecords@gmail.com\r\nReply-To: eternalwarfarerecords@gmail.com";


		mail($_SESSION["cust-email"],"Order #".str_pad($orderNum, 5, "0", STR_PAD_LEFT)." from EternalWarfare.org", $emailMessage, $emailHeader);
	//deplete store stock
		for($i=0; $i < count($_SESSION["items"]); $i++){

			$query = "select * from items where id=" . $_SESSION["items"][$i];
			$numresults = mysql_query($query) or die ("'Selecting items to delete' query failed! Your order was placed and a confirmation email was successfully sent though. Please contact eternalwarfarerecords@gmail.com. Error:".mysql_error());
			while($row = mysql_fetch_assoc($numresults)){
				if(isset($_SESSION["size"][$i]) && $_SESSION["size"][$i] != ""){
					$size = strtolower($_SESSION["size"][$i]);
					$amount = $row[$size] - ($_SESSION["quantitys"][$i]);
					$updateq = "UPDATE `items` SET `".$size."`='". $amount ."' WHERE `id`='".$_SESSION["items"][$i]."'";
				} else {
					$amount = $row["left"] - ($_SESSION["quantitys"][$i]);
					$updateq = "UPDATE `items` SET `left`='". $amount ."' WHERE `id`='".$_SESSION["items"][$i]."'";
				}
				$updater = mysql_query($updateq) or die ("'Updating stock' query failed! Your order was placed and a confirmation email was successfully sent though. Please contact eternalwarfarerecords@gmail.com. Error:".mysql_error());
			}
		}
	//display message
		echo "<font size=3><u><b>Order successful</b></u></font><br><br>";
		echo "Thanks for the order! Your order was successfully placed. A confirmation e-mail was sent to ".$_SESSION["cust-email"].". You will receive a notification when the order is shipped. Please give it up to a week before asking about a non-shipped order.<br><br>";
		echo "Your order number: " . str_pad($orderNum, 5, "0", STR_PAD_LEFT) . "<br>";
		//echo "Paypal Transaction ID: " . $transactionId . "<br>";
		echo "Order time: " . date("Y-m-d h:i") . "<br>";
		echo "Customer Email: ". $_SESSION["cust-email"]."<br>";
		echo "<br><br>";
		echo "<table class=\"cart\" width=100%>";
		echo "<tr><td width=60%><u><b>Shipping address:</b></u></td><td><b><u>Bill to:</b></u></td></tr>";
		echo "<tr><td><br><div align=center>";
		echo nl2br($_SESSION["cust-address"]);

		echo "</div></td><td width=50%><div align=center>";

		echo $_SESSION["cust-email"]."<br><br>";
		
		echo "</div></td></tr></table>";
		echo "<br><br>";
		echo "<table class=\"cart\" width=100%>";
		echo "<tr><td><b><u>Item</b></u></td><td><b><u>Quantity</b></u></td><td><b><u>Price</b></u></td></tr>";

		$length = count($_SESSION["items"]);
		//$_SESSION["order-details"] = "---Name---------------------Quantity-----Price---";
		for($i=0; $i < count($_SESSION["items"]); $i++){
			if(isset($_SESSION["items"][$i])){
				$query = "select * from items where id=" . $_SESSION["items"][$i];
				$numresults = mysql_query($query) or die ("invalid query2");
				while($row = mysql_fetch_assoc($numresults)){
					echo "<tr>";
					echo "<td cellpadding=3>" . $row["name"]; 
					//$_SESSION["order-details"] .= $row["name"];
					if(isset($_SESSION["size"][$i]) && $_SESSION["size"][$i] != ""){
						echo " - Size ". strtoupper($_SESSION["size"][$i]);
						//$_SESSION["order-details"] .= " - Size ". strtoupper($_SESSION["size"][$i]);
					}
					//$_SESSION["order-details"] .= "              ";
					echo "</td>";
					echo "<td>".$_SESSION["quantitys"][$i]."</td>";
					//$_SESSION["order-details"] .= $_SESSION["quantitys"][$i] . "        ";
					echo "<td>$". number_format(($row["price"] * $_SESSION["quantitys"][$i]), 2) ."</td>";
					//$_SESSION["order-details"] .= number_format(($row["price"] * $_SESSION["quantitys"][$i]), 2) . "\n";
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

		echo "<br><p align=center><a href=\"http://www.eternalwarfare.org\">EternalWarfare.org</a> || <a href=\"mailto:eternalwarfarerecords@gmail.com\">eternalwarfarerecords@gmail.com</a></p>";
		session_destroy();
		echo "<br><br><Br><font size=1>Which record would you want to see out next?</font><br>";
		echo "<a href=\"?page=vote&option=7\"><font size=1>Mania/Huldrekall 10\"</font></a><br>";
		echo "<a href=\"?page=vote&option=2\"><font size=1>Leech - Full Length LP</font></a><br>";
		echo "<a href=\"?page=vote&option=3\"><font size=1>Boreal - The Abyss (unreleased material)</font></a><br>";
		echo "<a href=\"?page=vote&option=4\"><font size=1>Mania - Self Titled Double LP</font></a><br>";
		echo "<a href=\"?page=vote&option=6\"><font size=1>River - S/T</font></a><br>";



//----------------END CONTENT--------------------------------------------------------------------
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
		echo "Please contact eternalwarfarerecords@gmail.com";
	}
}		
		
?>
