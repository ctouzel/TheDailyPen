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
$version = "0.8.0.10004";
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
$emailcontent = $emailcontent ."<h3>". ReadWeather(true) . "</h3>";

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


// -----------------------------------------
// SECTION TWITTER
// -----------------------------------------

// TWITTER
$twitcontent = GetTwitterTimeline();

if ($twitcontent != "")
{
	$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/tweets.png' style='border:0px'></p>";
	$emailcontent = $emailcontent . $twitcontent;

	// SECTION TWITTER FOOTNOTE
	$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
	$pagenumber = $pagenumber + 1;
}

// -----------------------------------------
// SECTION NEWS
// -----------------------------------------

$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/dailynews.png' style='border:0px'></p>";

// NEWS
$emailcontent = $emailcontent . ReadFromCyberpresse("http://www.cyberpresse.ca/rss/225.xml", "LA PRESSE", 4, true, 0);
$emailcontent = $emailcontent . ReadFromRSS("http://rss.radio-canada.ca/fils/regions/quebec.xml", "LA CAPITALE", 4, false, 0);
$emailcontent = $emailcontent . ReadFromRSS("http://rss.radio-canada.ca/fils/regions/estduquebec.xml", "EST DU QUEBEC", 4, false, 0);
$emailcontent = $emailcontent . ReadFromCyberpresse("http://www.cyberpresse.ca/rss/275.xml", "POLITIQUE CANADIENNE", 4, true, 0);
$emailcontent = $emailcontent . ReadFromRSS("http://rss.radio-canada.ca/fils/nouvelles/international.xml", "INTERNATIONAL", 4, false, 0);
$emailcontent = $emailcontent . ReadFromCyberpresse("http://www.cyberpresse.ca/rss/501.xml", "LE SOLEIL", 4, true, 0);

// SECTION NEWS FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;

// -----------------------------------------
// SECTION WEATHER
// -----------------------------------------

$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/theweather.png' style='border:0px'></p>";

// THE WEATHER
$emailcontent = $emailcontent .ReadWeather(false);

// SECTION WEATHER FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;

// -----------------------------------------
// SECTION SPORTS
// -----------------------------------------

$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/RenderedImage.png' style='border:0px'></p>";

// SCORES

// STATS

// STANDINGS

// MLB NEWS
$emailcontent = $emailcontent . ReadFromRSS("http://mlb.mlb.com/partnerxml/gen/news/rss/mlb.xml", "MLB NEWS", 4, false, 0);

// NFL NEWS
$emailcontent = $emailcontent . ReadFromRSS("http://www.nfl.com/rss/rsslanding?searchString=home", "NFL NEWS", 4, false, 0);

// JOE POSNANSKI - 2 SOURCES
$emailcontent = $emailcontent . ReadFromRSS(array('http://joeposnanski.com/joeblogs/feed/','http://www.nbcsports.com/rss/author/joe-posnanski/feed/'), "JOE POSNANSKI", 1, true, 0);

// DON BANKS - THE MMQB
$emailcontent = $emailcontent . ReadFromRSS("http://mmqb.si.com/author/donmmqb/feed/", "DON BANKS", 1, true, 0);

// KEVIN KADUK
$emailcontent = $emailcontent . ReadFromRSS("http://sports.yahoo.com/mlb/blog/big_league_stew/rss.xml", "KEVIN KADUK", 1, true, 0);

// JON HEYMAN
$emailcontent = $emailcontent . GetHeymanFeed();

// WRESTLIN'
$emailcontent = $emailcontent . ReadFromRSS("http://bleacherreport.com/articles;feed?tag_id=2181", "WRESTLIN'", 1, false, 0);

// SECTION FRONT PAGE FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;

// -----------------------------------------
// SECTION COMICS
// -----------------------------------------

$comicscontent = "";

// CALVIN AND HOBBES
$comicscontent = $comicscontent . ReadFromComicsRSS("http://calvinhobbesdaily.tumblr.com/rss", "CALVIN AND HOBBES", 1, false, 0);

// A SOFTER WORLD
$comicscontent = $comicscontent . ReadFromComicsRSS("http://www.asofterworld.com/rssfeed.php", "A SOFTER WORLD", 1, false, 0);

// XKCD
$comicscontent = $comicscontent . ReadFromComicsRSS("http://xkcd.com/rss.xml", "XKCD", 1, false, 0);

// BLOOM COUNTY
//$comicscontent = $comicscontent . ReadFromComicsRSS("http://feeds.feedburner.com/uclick/bloomcounty", "BLOOM COUNTY", 1, true, -1);

// PEARLS BEFORE SWINE
//$comicscontent = $comicscontent . ReadFromComicsRSS("http://feeds.feedburner.com/uclick/pearlsbeforeswine", "PEARLS BEFORE SWINE", 1, true, -1);

// FRAZZ
//$comicscontent = $comicscontent . ReadFromComicsRSS("http://feeds.feedburner.com/uclick/frazz", "FRAZZ", 1, true, -1);

// PERRY BIBLE FELLOWSHIP
//$comicscontent = $comicscontent . ReadFromComicsRSS("http://pbfcomics.com/feed/feed.xml", "PERRY BIBLE FELLOWSHIP", 1, true, -1);

// QUESTIONABLE CONTENT
$comicscontent = $comicscontent . ReadFromComicsRSS_IMG("http://www.questionablecontent.net/QCRSS.xml", "QUESTIONABLE CONTENT", 1, false, 0, 1);

// FOR BETTER OR FOR WORSE
$comicscontent = $comicscontent . ReadFromComicsRSS("http://fborfw.com/strip_fix/feed/", "FOR BETTER OR FOR WORSE", 1, true, 0);

// MENAGE A TROIS
$comicscontent = $comicscontent . GetMenageATroisFeed();

if ($comicscontent != "")
{
	$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/comics.png' style='border:0px'></p>";
	$emailcontent = $emailcontent . $comicscontent;

	// SECTION COMICS FOOTNOTE
	$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
	$pagenumber = $pagenumber + 1;
}

// -----------------------------------------
// SECTION ARTS & ENTERTAINMENT
// -----------------------------------------

$artscontent = "";

// CYBERPRESSE - ARTS
$artscontent = $artscontent . ReadFromCyberpresse("http://www.cyberpresse.ca/rss/216.xml", "CYBERPRESSE - ARTS", 5, true, 0);

// STEREOGUM
$artscontent = $artscontent . ReadStereogumFeed();

// STEVEN HYDEN
$artscontent = $artscontent . ReadFromRSS("http://grantland.com/contributors/steven-hyden/feed/", "STEVEN HYDEN", 1, true, 0);

// PITCHFORK REVIEW
$artscontent = $artscontent . ReadFromRSS("http://pitchfork.com/rss/reviews/albums/", "PITCHFORK", 1, false, 0);

// ROGER EBERT REVIEWS
$artscontent = $artscontent . ReadFromRSS("http://www.rogerebert.com/reviews/feed", "ROGER EBERT REVIEWS", 2, true, 0);

// KOTTKE
$artscontent = $artscontent . ReadFromRSS("http://feeds.kottke.org/main", "KOTTKE", 1, true, 0);

if ($artscontent != "")
{
	$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/entertainment.png' style='border:0px'></p>";
	$emailcontent = $emailcontent . $artscontent;

	// SECTION ARTS FOOTNOTE
	$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
	$pagenumber = $pagenumber + 1;
}

// -----------------------------------------
// SECTION TECHNO
// -----------------------------------------

$technocontent = "";

// BACK TO BASICS
$technocontent = $technocontent . "<h3>CODING TIPS</h3><p>" . GetRandomItemFromXML("coding.xml") . "</p>";

// CODING HORROR 
$technocontent = $technocontent . ReadFromRSS('http://feeds.feedburner.com/codinghorror', "CODING HORROR", 1, true, 0);

// I CRINGELY
$technocontent = $technocontent . ReadFromRSS("http://www.cringely.com/feed", "I CRINGELY", 1, true, 0);

// CODE BETTER
$technocontent = $technocontent . ReadFromRSS("http://feeds.feedburner.com/CodeBetter", "CODE BETTER", 1, true, 0);

// SLASHDOT
$technocontent = $technocontent . ReadFromRSS_NoImage("http://rss.slashdot.org/Slashdot/slashdot", "SLASHDOT", 5, true, 0);

// SCOTT HANSELMAN
$technocontent = $technocontent . ReadFromRSS("http://feeds.hanselman.com/ScottHanselman", "SCOTT HANSELMAN", 1, true, 0);

if ($technocontent != "")
{
	$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/techno.png' style='border:0px'></p>";
	$emailcontent = $emailcontent . $technocontent;

	// SECTION TECHNO FOOTNOTE
	$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
	$pagenumber = $pagenumber + 1;
}

// -----------------------------------------
// SECTION OPINIONS
// -----------------------------------------

$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/opinions.png' style='border:0px'></p>";

// RICHARD MARTINEAU
$emailcontent = $emailcontent . ReadFromRSS("http://blogues.journaldemontreal.com/martineau/feed/", "RICHARD MARTINEAU", 1, true, 0);

// CYBERPRESSE OPINIONS
$opinions = array(
"http://www.cyberpresse.ca/rss/948.xml", // LAGACÉ
"http://www.cyberpresse.ca/rss/933.xml", // CASSIVI
"http://www.cyberpresse.ca/rss/930.xml", // BOURQUE
"http://www.cyberpresse.ca/rss/937.xml", // DUBUC
"http://www.cyberpresse.ca/rss/939.xml", // DUMAS
"http://www.cyberpresse.ca/rss/941.xml", // FOGLIA
"http://www.cyberpresse.ca/rss/955.xml", // MARISSAL
"http://www.cyberpresse.ca/rss/973.xml", // PRATTE
"http://www.cyberpresse.ca/rss/928.xml"); // BOISVERT
$emailcontent = $emailcontent . ReadOpinionsFromCyberpresse($opinions, 3);

// SECTION OPINIONS FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;

// -----------------------------------------
// SECTION LIVING
// -----------------------------------------

$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/artliving.png' style='border:0px'></p>";

// LIFE AND HEALTH TIPS
$emailcontent = $emailcontent . "<h3>Life Tips and Tricks</h3><ul>";
$emailcontent = $emailcontent . "<li>" . GetRandomItemFromXML('life.xml') . "</li>";
$emailcontent = $emailcontent . "<li>" . GetRandomItemFromXML('health.xml') . "</li></ul>";

// LIFE HACKER
$emailcontent = $emailcontent . ReadFromRSS("http://feeds.gawker.com/lifehacker/vip", "LIFE HACKER", 1, true, 0);

// ZEN HABITS
$emailcontent = $emailcontent . ReadFromRSS("http://zenhabits.net/feed/", "ZEN HABITS", 1, true, 0);

// ASKMEN
$emailcontent = $emailcontent . ReadFromRSS("http://ca.askmen.com/homepage.xml", "ASK MEN", 1, true, 0);

// BEACH BODY
$emailcontent = $emailcontent . ReadFromRSS("http://www.beachbody.com/beachbodyblog/feed", "BEACH BODY", 1, true, 0);

// BACK TO BASICS
$emailcontent = $emailcontent . "<h3>BACK TO BASICS : " . GetRandomItemFromXML("basics.xml") . "</h3>";

// SECTION LIVING FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;




// -----------------------------------------
// SECTION SPANISH
// -----------------------------------------

$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/seccion.png' style='border:0px'></p>";

// CNN EN ESPANOL
$emailcontent = $emailcontent . ReadFromRSS("http://cnnespanol.cnn.com/feed/", "CNN ESPANOL", 4, true, 0);

// SPANISH
$emailcontent = $emailcontent . GetRandomSpanishItems();

// SECTION SPANISH FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;

// -----------------------------------------
// SECTION FOOD
// -----------------------------------------

$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/eating.png' style='border:0px'></p>";

// SONOMA
$emailcontent = $emailcontent . ReadFromRSS("http://blog.williams-sonoma.com/feed/", "SONOMA", 1, true, 0);

// SHUTTER BEAN
$emailcontent = $emailcontent . ReadFromRSS("http://www.shutterbean.com/feed/", "SHUTTER BEAN", 1, true, 0);

// SECTION FOOD FOOTNOTE
$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
$pagenumber = $pagenumber + 1;


// -----------------------------------------
// SECTION OTHER STUFF
// -----------------------------------------

// PETER KING - THE MMQB
$othercontent = ReadFromRSS_NoImage("http://mmqb.si.com/column/monday-morning-quarterback/feed/", "MONDAY MORNING QUATERBACK", 1, true, 0);

if ($othercontent != "")
{
	$emailcontent = $emailcontent . "<p><img src='http://www.touzel.com/otherstuff.png' style='border:0px'></p>";
	$emailcontent = $emailcontent . $othercontent;

	// SECTION OTHER STUFF FOOTNOTE
	$emailcontent = $emailcontent . "<center><h4>-------- Page ".$pagenumber." --------</h4></center>";
	$pagenumber = $pagenumber + 1;
}

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
SendEmail($emailcontent, $emailEnabled);

// LOG REPORTS
echo "<h3>Errors Report</h3>";
echo $GLOBALS['errors'];

echo "</body></html>";
?>