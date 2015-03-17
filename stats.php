<?php 
require_once('autoloader.php');
include("dailypenfunctions.php"); 

// GLOBAL VARS
$version = "0.9.2.10003";
$GLOBALS['errors'] = "";
$now = new DateTime();
$now->setTimezone(new DateTimeZone('America/Montreal'));
$formatted = $now->format('Y-m-d')."T".$now->format('H:i:s').".000Z";
$headlines = array(
	"http://rss.radio-canada.ca/fils/regions/quebec.xml",
	"http://rss.radio-canada.ca/fils/regions/estduquebec.xml",
	"http://rss.radio-canada.ca/fils/nouvelles/international.xml",
	"http://www.cyberpresse.ca/rss/225.xml",
	"http://www.cyberpresse.ca/rss/501.xml");
	
echo "<html>\r\n";
echo "<head>\r\n";
echo "<title>The Daily Pen, ".$now->format('F j')."</title>\r\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"UTF-8\" />\r\n";
echo "<meta property=\"og:title\" content=\"The Daily Pen\" />\r\n";
echo "<meta property=\"og:type\" content=\"article\" />\r\n";
echo "<meta property=\"og:url\" content=\"http://www.touzel.com/daily.php\" />\r\n";
echo "<meta HTTP-EQUIV=\"CACHE-CONTROL\" content=\"NO-CACHE\" />\r\n";
echo "<meta HTTP-EQUIV=\"PRAGMA\" content=\"NO-CACHE\" />\r\n";
echo "<meta HTTP-EQUIV=\"Expires\" content=\"-1\" />\r\n";
echo "<meta HTTP-EQUIV=\"cache-control\" content=\"max-age=0\" />\r\n";
echo "<meta property=\"og:image\" content=\"http://www.touzel.com/logo2.png\" />\r\n";
echo "<meta property=\"author\" content=\"Christian Touzel\">\r\n";
echo "<meta property=\"version\" content=\"".$version."\">\r\n";
echo "<meta property=\"article:published_time\" content=\"".$formatted."\" />\r\n";
echo "<meta property=\"article:modified_time\" content=\"".$formatted."\" />\r\n";
echo "</head>\r\n";
echo "<body>\r\n";
echo "<article>\r\n";
echo "<h1>The Daily Pen, ".$now->format('F j')."</h1>\r\n";

// MLB STANDINGS
echo "<h3>MLB STATS</h3>\r\n";
GetMLBStats();

?>
</body>
</html>