<?php
//
// THE DAILY PEN UNIT TEST PAGE
// By Christian Touzel
//

header("Content-Type: text/html; charset=utf-8");

require_once('autoloader.php');
require_once('PHPMailer-master/class.phpmailer.php');
include("dailypenfunctions.php");
include("PHPMailer-master/class.smtp.php");

$GLOBALS['errors'] = "";
$GLOBALS['content'] = "";
$GLOBALS['testnumber'] = 1;
$GLOBALS['pagenumber'] = 2;
$GLOBALS['version'] = "0.1.0.10003";

echo "<h1>The DailyPen Test Application</h1>";

// THE TESTS...
TEST_settings();
TEST_title();
TEST_random_quote();
TEST_weather_short();
TEST_headlines();

// GLOBAL TESTS
TEST_email_content();
TEST_global_error_log();

/**
 *  HERE COME THE TESTS...
 */

// TEST SETTINGS
function TEST_settings()
{
    echo "<hr><h2>TEST ".$GLOBALS['testnumber']++." - Application Settings</h2>";
    try
    {
        echo '<p>Test Application Version '.$GLOBALS['version'].' | ';
        echo 'Current Host: '.$_SERVER["HTTP_HOST"];
        echo ' | PHP Version: '.phpversion().'</p>';
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST TITLE
function TEST_title()
{
    echo "<hr><h2>TEST ".$GLOBALS['testnumber']++." - Application Title</h2>";
    try
    {
        $apptitle = "<p><img src='http://www.touzel.com/logo2.png' style='border:0px'></p>";
        echo $apptitle;
        $GLOBALS['content'] = $GLOBALS['content'] . $apptitle;
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST RANDOM QUOTE
function TEST_random_quote()
{
    echo "<hr><h2>TEST ".$GLOBALS['testnumber']++." - Random Quote</h2>";
    try
    {
        $quote = GetRandomQuote();
        echo $quote;
        $GLOBALS['content'] = $GLOBALS['content'] . $quote;
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST WEATHER SHORT
function TEST_weather_short()
{
    echo "<hr><h2>TEST ".$GLOBALS['testnumber']++." - Weather Short</h2>";
    try
    {
        $weather = "<h3>". GetWeather(true) . "</h3>";
        echo $weather;
        $GLOBALS['content'] = $GLOBALS['content'] . $weather;
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST GLOBAL ERROR LOG
function TEST_global_error_log()
{
    echo "<hr><h2>TEST ".$GLOBALS['testnumber']++." - Global Error Log</h2>";
    try
    {
        echo $GLOBALS['errors'];
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST EMAIL CONTENT
function TEST_email_content()
{
    echo "<hr><h2>TEST ".$GLOBALS['testnumber']++." - Email Content</h2>";
    try
    {
        echo $GLOBALS['content'];
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST HEADLINES
function TEST_headlines()
{
    echo "<hr><h2>TEST ".$GLOBALS['testnumber']++." -Headlines</h2>";
    try
    {
        $headlinesContent = "<h3>News</h3>";
        $headlines = array(
            "http://rss.radio-canada.ca/fils/regions/quebec.xml",
            "http://rss.radio-canada.ca/fils/regions/estduquebec.xml",
            "http://rss.radio-canada.ca/fils/nouvelles/international.xml",
            "http://www.cyberpresse.ca/rss/225.xml",
            "http://www.cyberpresse.ca/rss/501.xml");
        $headlinesContent = $headlinesContent . GetHeadlinesFromRSS($headlines, 8, 0);
        echo $headlinesContent;
        $GLOBALS['content'] = $GLOBALS['content'] . $headlinesContent;
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}
?>