Here are some pictures of our office, if you're interested:<br><br>
<?php

$tr = 0;
$td = 0;
$tdpercent = 33;
$numtds = 3;
echo "<table border=0 width=100% cellpadding=10>";
$d = opendir('theoffice') or die("Error reading directory");
while (false !== ($file = readdir($d))) {
  if($file != "." && $file != ".."){



	if($td == 0){
		echo "<tr>";
	}
	$td++;
	
	echo "<td width=\"" . $tdpercent . "%\" valign=\"bottom\">";
	echo "<div align=center><font size=1 color=red><b>";
	echo $file;
	echo "</font></b><br>";
	echo "<a href=\"theoffice/". $file ."\"><img src=\"theoffice/";
	echo $file;
	$imagesize = getimagesize("theoffice/".$file);
	$width = $imagesize[0];
	$height = $imagesize[1];
	if($width > $height){
		$percentage = 150 / $width;
	} else {
		$percentage = 150 / $height;
	}
	$width = $width * $percentage;
	$height = $height * $percentage;
	echo "\" width=\"" . $width . "\" height=\"" . $height . "\"></a><br>";
	echo "</div></td>";
	if($td == $numtds){
		echo "</tr>\n";
		$tr = 0;
		$td = 0;
	}
  }
}
echo "</table>\n";
closedir($d);
?>