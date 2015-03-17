<?php 
//
// THE DAILY PEN
// The Best Offline Newspaper Still Alive!
// By Christian Touzel
//

header("Content-Type: text/html; charset=utf-8");
require_once('autoloader.php');
require_once('PHPMailer-master/class.phpmailer.php');
include("PHPMailer-master/class.smtp.php"); 
include("dailypenfunctions.php"); 

// GLOBAL VARS
$version = "0.9.0.10001";
$emailEnabled = true;
$cleanEnabled = false;
$emailcontent = "";
$pagenumber = 2;
$GLOBALS['errors'] = "";

// CONDITIONAL STATES
if ($_SERVER["HTTP_HOST"] == "localhost")
{
	$emailEnabled = false;
}
if (isset($_GET['clean']))
{
	$cleanEnabled = true;
}

// -----------------------------------------
// SECTION FRONT PAGE - NEWS
// -----------------------------------------

// TITLE
$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/logo2.png' style='border:0px'></p>";

// QUOTES
$emailcontent = $emailcontent .GetRandomQuote();

// THE WEATHER
$emailcontent = $emailcontent ."<h3>". GetWeather(true) . "</h3>";

$emailcontent = $emailcontent . "<h3>News</h3>";
$headlines = array(
"http://rss.radio-canada.ca/fils/regions/quebec.xml",
"http://rss.radio-canada.ca/fils/regions/estduquebec.xml",
"http://rss.radio-canada.ca/fils/nouvelles/international.xml",
"http://www.cyberpresse.ca/rss/225.xml",
"http://www.cyberpresse.ca/rss/501.xml");
$emailcontent = $emailcontent . GetHeadlinesFromRSS($headlines, 8, 0);

$emailcontent = $emailcontent . "<h3>Baseball</h3>";
$emailcontent = $emailcontent . GetHeadlinesFromRSS("http://mlb.mlb.com/partnerxml/gen/news/rss/mlb.xml", 5, 0);
$emailcontent = $emailcontent . "<h3>Football</h3>";
$emailcontent = $emailcontent . GetHeadlinesFromRSS("http://www.nfl.com/rss/rsslanding?searchString=home", 5, 0);

// TODOIST
$emailcontent = $emailcontent . GetTodoistTasks();

$emailcontent = $emailcontent ."<h1>&nbsp;</h1>";

// SECTION FRONT PAGE FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;

// FINAL FOOTER NOTES
$now = new DateTime();
$now->setTimezone(new DateTimeZone('America/Montreal'));
$emailcontent = $emailcontent . "<p style='font-size: 80%;'>The Daily Pen ".$version." is a creation of Christian Touzel. ";
$emailcontent = $emailcontent . "Page generated on ".$now->format('F j, Y H:i:s').". PHP version is ".phpversion()."</p>";

// ECHO OF THE EMAIL CONTENT
echo '<html><head><title>The Daily Pen</title>';
echo '<link rel="icon" type="image/png" href="../logo.png" />';
echo '<link rel="apple-touch-icon" href="../logo.png"/>';
echo '<link rel="apple-touch-icon" sizes="72x72" href="../logo.png"/>';
echo '<link rel="apple-touch-icon" sizes="114x114" href="../logo.png"/>';
echo '<meta name="author" content="Christian Touzel"><meta name="version" content="'.$version.'">';
echo '</head><body>';
echo '<h3>Daily Pen Application Version '.$version.' | ';
echo 'Current Host: '.$_SERVER["HTTP_HOST"];
echo ' | PHP Version: '.phpversion().'</h3>';
echo $emailcontent;

// SENDING THE EMAIL
// SendEmail($emailcontent, $emailEnabled);

// LOG REPORTS
echo "<h3>Errors Report</h3>";
echo $GLOBALS['errors'];

echo "</body></html>";
?>