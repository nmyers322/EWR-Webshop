<!-- Eternal Warfare Records Webstore by Nathanial Myers -->
<!-- These pages were created entirely in Notepad between January 2009 and March 2013 -->
<!-- I release this under the GNU GPL as is. I don't expect it to work for you, but -->
<!--   with some tweaking, you may be able to get some use out of this is a simple shopping -->
<!--   cart and order management system. It can also show you how to use the PayPal integrated cart system. -->

<?php

session_start();

function items_sort(){
	$total_items = count($_SESSION["items"]);
	$i=0;
	foreach($_SESSION["items"] as $key=>$value){
		
		$quantity[$i] = $_SESSION["quantitys"][$key];
		if(isset($_SESSION["size"][$key])&&$_SESSION["size"][$key]!="")
			$size[$i] = $_SESSION["size"][$key];
		$_SESSION["items"][$i] = $_SESSION["items"][$key];
		$i++;
	}
	ksort($_SESSION["items"]);
	$i=0;
	foreach($_SESSION["items"] as $key=>$value){
		$_SESSION["quantitys"][$key] = $quantity[$i];
		if(isset($size[$i])&&$size[$i]!="")
			$_SESSION["size"][$key] = $size[$i];
		$i++;
	}
	foreach($_SESSION["items"] as $key=>$value){
		
		if($key >= $total_items){
			
			unset($_SESSION["items"][$key]);
			unset($_SESSION["quantitys"][$key]);
			unset($_SESSION["size"][$key]);
		}
	}

	
}

function check_quantity($n) {

if (preg_match("/[^0-^9]+/",$n) > 0 || $n < 1) { 
	return false; 
} 
return true;

} 

function convertCountry($code){
	$convertCountryQuery = "SELECT * from country WHERE iso='".$code."'";
	$convertCountryResult = mysql_query($convertCountryQuery) or die("Country code: ".$code);
	while($crow = mysql_fetch_assoc($convertCountryResult)){
		return $crow['printable_name'];
	}
}


mysql_connect("localhost", "USERNAME", "PASSWORD") or die("couldnt connect");
mysql_select_db("eternalw_eternalshop") or die("Unable to select database");


?>




<html>
	<head>
		<title>Eternal Warfare Tape/Vinyl/CD Music Distribution</title>

		<?
		 // -------------------script so you cant submit a form twice 
		?>
		<SCRIPT LANGUAGE="JavaScript">
		function disableForm(theform) {
			if (document.all || document.getElementById) {
				for (i = 0; i < theform.length; i++) {
					var tempobj = theform.elements[i];
					if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset")
						tempobj.disabled = true;
				}
				return true;
			} else {
				alert("The form has been submitted. Please wait; do not hit the submit button again.");
				return false;
   			}
		}

		</script>


		<?# -----------------BEGIN ROLLOVER COMMENT BOX FOR CART------------------------------?>

		<link rel="stylesheet" type="text/css" href="ddpanel/dddropdownpanel.css" />

		<script type="text/javascript" src="ddpanel/dddropdownpanel.js">

		/***********************************************
		* DD Drop Down Panel- (c) Dynamic Drive DHTML code library (www.dynamicdrive.com)
		* This notice MUST stay intact for legal use
		* Visit Dynamic Drive at http://www.dynamicdrive.com/ for this script and 100s more
		***********************************************/

		</script>
		<script type="text/javascript">

		anylinkmenu.init("menuanchorclass")

		</script>

		<link rel="stylesheet" href="main.css" type="text/css"/>

	</head>

	<body bg-color="#6C4F4F">



	<div style="position: absolute; top: 0; left: 15; height:100%" >
	<table border=0 cellpadding=0 cellspacing=0 height="100%" width="978">
	 <tr>
	  <td background="images/left1.gif" width="182" height="167">&nbsp;
   
	  </td>
	  <td background="images/top.gif" width="796" height="167" colspan="2">&nbsp;
   
	  </td>
	 </tr>
	 <tr>

	  <td background="images/left2.gif" width="182">&nbsp;
   
	  </td>
	  <td bgcolor="#FFFFFF" background="images/bg.gif" style="border:solid 2px #000000" valign="top">

	   <!-- content -->

	   <div align="justify" style="padding:5;"><font face="verdana" size="2">

	<?php

	$page = $_GET['page'];

	switch($page) {
		case 'home':
		include 'home.php';
		break;

		case 'shop':
		include 'shop.php';
		break;

		case 'downloads':
		include 'downloads.php';
		break;

		case 'theoffice':
		include 'theoffice.php';
		break;

		case 'bulk':
		include 'bulk.php';
		break;

		case 'links':
		include 'links.php';
		break;

		case 'vote':
		include 'vote.php';
		break;

		case 'contact':
		include 'contact.php';
		break;

		case 'viewitem':
		include 'viewitem.php';
		break;

		case 'cart':
		include 'cart.php';
		break;

		case 'admin':
		include 'backend.php';
		break;

		case 'review';
		include 'review000.php';
		break;

		case 'confirm';
		include 'confirm000.php';
		break;

		case 'return';
		echo "Your order was successfully placed. Thanks for your support.";
		break;

		case 'catalog';
		include 'catalog.php';
		break;

		default:
		include 'home.php';
		break;
	}

	?>
	<br><br><br><div align="center">
	<script type="text/javascript"><!--
	google_ad_client = "ca-pub-6983854745322313";
	/* Eternal Warfare */
	google_ad_slot = "9055834884";
	google_ad_width = 468;
	google_ad_height = 60;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
	</div>
	  </td>
	  <td width="5" background="images/right.gif" ><a href="?page=admin"><img src="images/right.gif" border=0></a>
   
	  </td>
	 </tr>
	 <tr>
	  <td background="images/left1.gif" width="182" height="167">&nbsp;
   
	  </td>
	  <td background="images/bottombanner.gif" width="796" height="167" colspan="2">&nbsp;
   
	  </td>
	 </tr>
	</table>
	</div>



	<!--                              menu                                      -->
	<div id="menuFloat" style="position: absolute;">
	 <table cellpadding="0" cellspacing="2" border="0" width="182">
	  <tr>
	   <td class="menutd">
		<a href="?page=home">HOME&nbsp;</a>
	   </td>
	  </tr>

	  <tr>
	   <td class="menutd">
		<a href="?page=shop">SHOP&nbsp;</a>
	   </td>
	  </tr>

	  <tr>
	   <td class="menutd">
		<a href="?page=cart">VIEW CART&nbsp;</a>
	   </td>
	  </tr>

	  <tr>
	   <td class="menutd">
		<a href="?page=catalog">CATALOG&nbsp;</a>
	   </td>
	  </tr>

	  <tr>
	   <td class="menutd">
		<a href="?page=downloads">FREE DOWNLOADS&nbsp;</a>
	   </td>
	  </tr>

	  <tr>
	   <td class="menutd">
		<a href="http://eternalwarfare.bandcamp.com">LISTEN/BUY DIGITAL&nbsp;</a>
	   </td>
	  </tr>

	  <tr>
	   <td class="menutd">
		<a href="?page=bulk">BULK ORDERS&nbsp;</a>
	   </td>
	  </tr>

	  <tr>
	   <td class="menutd">
		<a href="?page=contact">CONTACT&nbsp;</a>
	   </td>
	  </tr>

	 </table>

	</div>





	<!-- menu float - this script was obtained at http://www.javascript-fx.com -->


	<script type="text/javascript">
	var ns = (navigator.appName.indexOf("Netscape") != -1);
	var d = document;
	var px = document.layers ? "" : "px";
	function JSFX_FloatDiv(id, sx, sy)
	{
		var el=d.getElementById?d.getElementById(id):d.all?d.all[id]:d.layers[id];
		window[id + "_obj"] = el;
		if(d.layers)el.style=el;
		el.cx = el.sx = sx;el.cy = el.sy = sy;
		el.sP=function(x,y){this.style.left=x+px;this.style.top=y+px;};
		el.flt=function()
		{
			var pX, pY;
			pX = (this.sx >= 0) ? 0 : ns ? innerWidth : 
			document.documentElement && document.documentElement.clientWidth ? 
			document.documentElement.clientWidth : document.body.clientWidth;
			pY = ns ? pageYOffset : document.documentElement && document.documentElement.scrollTop ? 
			document.documentElement.scrollTop : document.body.scrollTop;
			if(this.sy<0) 
			pY += ns ? innerHeight : document.documentElement && document.documentElement.clientHeight ? 
			document.documentElement.clientHeight : document.body.clientHeight;
			this.cx += (pX + this.sx - this.cx)/8;this.cy += (pY + this.sy - this.cy)/8;
			this.sP(this.cx, this.cy);
			setTimeout(this.id + "_obj.flt()", 40);
		}
		return el;
	}
	JSFX_FloatDiv("menuFloat", 15, 45).flt();
	</script>


	</body>
</html>

<!--
A Texan, a Californian, and an Oregonian are out riding horses. The Texan pulls out an expensive bottle of whiskey, takes a long swig, then another, and suddenly throws it into the air, pulls out his gun and shoots the bottle in midair. The Californian looks at him and says, "What are you doing? That was a perfectly good bottle of whiskey!" The Texan says, "In Texas, there's plenty of whiskey and bottles are cheap." A while later, not wanting to be outdone, the Californian pulls out a bottle of champagne, takes a few sips, throws the champagne into the air, pulls out his gun and shoots it in midair. The Oregonian can't believe this and says, "What the heck did you do that for??? That was an expensive bottle of champagne!" The Californian says "In California, there's plenty of champagne and bottles are cheap." So a while later, the Oregonian pulls out a bottle of Widmer Hefeweizen. He opens it, takes a sip, takes another sip, then chugs the rest. He then puts the bottle back in his saddlebag, pulls out his gun, turns around and shoots the Californian. The Texan, shocked, says, "Why the hell did you do that?!" The Oregonian replied, "In Oregon we have plenty of Californians, and bottles are worth a nickel." -->


