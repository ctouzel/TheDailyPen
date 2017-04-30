<?php

//
// THE DAILY PEN
// By Christian Touzel
//

// GLOBAL TIMEZONE SET
date_default_timezone_set('America/Montreal');

// INCLUDED PHP FILES
include("constants.php");
include("dailypenfunctions.php");
include("rss.php");
include("twitter.php");
include("mlb.php");

// GLOBAL VARIABLES
$GLOBALS['errors'] = "";

// GLOBAL ERROR HANDLER
set_error_handler("ManageDailyPenErrors");

// HTML HEAD & META TAGS
echo GetDailyPenHTMLHead(VERSION, TITLE);

// HEADER
echo GetDailyPenHeader(TITLE);

// ENTRY-CONTENT
echo MARGIN3 . "<div class=\"entry-content\">".PHP_EOL;
echo MARGIN3 . "<section>".PHP_EOL;

echo '<table border="0" cellspacing="20">';

// ROW 1
echo '<tr>';
// WEATHER
echo '<td width="45%"  style="vertical-align:top">';
echo GetCompleteWeather();
echo '</td>';
echo '<td width="55%"></td>';
echo '</tr>';

// ROW 2
echo '<tr>';
echo '<td width="45%" style="vertical-align:top">';

// NEWS HEADLINES
$headlines = array("http://www.cyberpresse.ca/rss/225.xml",
	"http://www.cyberpresse.ca/rss/501.xml");
echo GetHeadlines("latestnewsheadlines2", $headlines, "DERNIERE HEURE");

// NEWS HEADLINES
$headlines = array(
	"http://feeds.washingtonpost.com/rss/national");
echo GetHeadlines("latestnewsheadlines1", $headlines, "INTERNATIONAL");

// NEWS HEADLINES
$headlines = array("http://uproxx.com/feed/");
echo GetHeadlinesMax("latestnewsheadlines3", $headlines, "CULTURAL", 5);

echo MARGIN3 . "</section>".PHP_EOL;
echo '</td>';
echo '<td width="55%" style="vertical-align:top">';
// TWITTER
echo GetTwitterTimeline();
echo '</td>';
echo '</tr>';

echo "<tr>";
echo '<td width="45%"  style="vertical-align:top">';
// MLB NEWS
echo GetNews("baseballnews", "http://mlb.mlb.com/partnerxml/gen/news/rss/mlb.xml", "BASEBALL NEWS");
echo "</td>";
echo '<td width="55%"  style="vertical-align:top">';
// NFL NEWS
echo GetHeadlines("nflnews", "https://sports.yahoo.com/nfl/rss.xml", "NFL NEWS");
echo "</tr>";


echo "<tr>";
echo '<td width="45%"  style="vertical-align:top">';
// MLB SCORES
GetMLBScoresTables();
echo "</td>";
echo '<td width="55%"  style="vertical-align:top">';
// MLB STANDINGS
GetMLBStandings();
// MLB WILDCARD STANDINGS
GetMLBWildcardStandings();
echo "</tr>";

echo "<tr>";
echo '<td width="45%"  style="vertical-align:top">';
// MLB STATS
GetMLBStats();
echo "</td>";
echo '<td width="55%"  style="vertical-align:top">';
echo "</tr>";

echo '</table>';


// LOG REPORTS
if ($GLOBALS['errors']!=="")
{
    echo MARGIN4 . "<div id=\"errorreports\">".PHP_EOL;
    echo MARGIN5 . "<h2>ERRORS REPORT</h2>".PHP_EOL;
    echo MARGIN5 . $GLOBALS['errors'].PHP_EOL;
    echo MARGIN4 . "</div>".PHP_EOL.PHP_EOL;
}

// END OF ARTICLE CONTENT
echo MARGIN3 . "</div>".PHP_EOL;

// FOOTER
$now = new DateTime();
echo MARGIN3 . "<footer>".PHP_EOL;
echo MARGIN4 . "<p style='font-size: 80%;'>The Daily Pen ".VERSION." is a creation of Christian Touzel. ";
echo "Page generated on ".$now->format('F j, Y H:i:s').". PHP version is ".phpversion()."</p>".PHP_EOL;
echo MARGIN3 . "</footer>".PHP_EOL.PHP_EOL;


// END OF WEBPAGE
echo MARGIN2 . "</article>".PHP_EOL;
echo MARGIN1 . "</body>".PHP_EOL;
echo "</html>";
?>