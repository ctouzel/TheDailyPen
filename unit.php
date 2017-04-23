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
// RANDOM QUOTE
echo GetRandomQuote();

// WILDCARD
GetMLBWildcardStandings();

echo MARGIN4 . "</div>".PHP_EOL;
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