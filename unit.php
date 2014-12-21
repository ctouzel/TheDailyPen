<?php
include("dailypenfunctions.php");
$GLOBALS['errors'] = "";

echo "<h1>The DailyPen Test Application</h1>";
TEST_title();
TEST_random_quote();
TEST_global_error_log();

/**
 *  HERE COME THE TESTS...
 */

// TEST TITLE
function TEST_title()
{
    echo "<hr><h2>TEST - Application Title</h2>";
    try
    {
        $emailcontent = "<p><img src='http://www.touzel.com/logo2.png' style='border:0px'></p>";
        echo $emailcontent;
        echo "<p>RESULT - Success</p>";
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST RANDOM QUOTE
function TEST_random_quote()
{
    echo "<hr><h2>TEST - Random Quote</h2>";
    try
    {
        $quote = GetRandomQuote();
        echo $quote;
        echo "<p>RESULT - Success</p>";
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}

// TEST GLOBAL ERROR LOG
function TEST_global_error_log()
{
    echo "<hr><h2>TEST - Global Error Log</h2>";
    try
    {
        echo $GLOBALS['errors'];
        echo "<p>RESULT - Success</p>";
    }
    catch (Exception $e)
    {
        echo "<p>RESULT - Failure (".$e->getMessage().")</p>";
    }
}
?>