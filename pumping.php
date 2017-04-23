<!DOCTYPE html>
<html lang="en">
<head>
    <title>The Daily Pen Pumping</title>
    <link rel='stylesheet' type='text/css' href="style.css">
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Playfair+Display:400,700,900,400italic,700italic,900italic|Droid+Serif:400,700,400italic,700italic'>
    <link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Cabin'>
    </head>
    <body>

    <header>
        <div class="head">
            <div class="headerobjectswrapper">
                <span class="entry-title">The Daily Pen - Pumping</span>
            </div>
            <div class="subhead">
                Quebec City
            </div>
        </div>
    </header>
    
<?php
include("constants.php");
include("rss.php");
include("email.php");

define("MARGIN1", "   ");
define("MARGIN2", "     ");
define("MARGIN3", "        ");
define("MARGIN4", "           ");
define("MARGIN5", "              ");
define("MARGIN6", "                 ");
define("MARGIN7", "                    ");
define("MARGIN8", "                       ");
define("MARGIN9", "                          ");

$version = "0.0.1.10003";

// GLOBAL ERROR HANDLER
set_error_handler("ManagePumpingErrors");

// CALL UPDATE ARTICLES FUNCTION
UpdateArticles();

// MANAGE PUMPING ERRORS
function ManagePumpingErrors($errno, $errstr)
{
    echo "<p><b>The Daily Pen has a small problem:</b> [$errno] $errstr"."</p>";
    throw new Exception($errstr);
}
// MANAGE EXCEPTION
function ManageException($exception, $additionalInfo)
{
    $errorcontent = "<h2>Unable to retrieve articles</h2>";
    $errorcontent = $errorcontent.$additionalInfo." : ".$exception->getMessage();

    echo $errorcontent;
    $fp = fopen("cache/articles.php","wb");
    fwrite($fp, $errorcontent);
    fclose($fp);
    //sendemail("The Daily Pen","Pumping articles Failure",$errorcontent,"ctouzel@gmail.com","thedailypen@touzel.com");
}

// CREATE ARTICLE SECTION
function CreateArticleSection($number, $source, $author, $descflag)
{
   $content = MARGIN5."<div id=\"article".$number."\">".PHP_EOL;
   $content = $content.GetArticle($source,$author, $descflag);
   $content = $content. MARGIN5."</div>".PHP_EOL;
   
   // SAVE TO FILE
   $fp = fopen("cache/article".$number.".php","wb");
   fwrite($fp,$content);
   fclose($fp);
		
	// ECHO OUTPUT
	echo $content;
}

// UPDATE ARTICLES
function UpdateArticles()
{
    try
    {
		echo MARGIN4."<div id=\"articles\">".PHP_EOL;
	
		// ARTICLE 1 - BIG LEAGUE STEW
		CreateArticleSection(1, "http://sports.yahoo.com/mlb/blog/big_league_stew/rss.xml","", FALSE);
		
		// ARTICLE 2 - JOE POSNANSKI
		CreateArticleSection(2, "http://sportsworld.nbcsports.com/feed/","Joe Posnanski", TRUE);
		
		// ARTICLE 3 - SWEETSPOT
		CreateArticleSection(3, "http://espn.go.com/blog/feed?blog=sweetspot","SweetSpot", TRUE);
		
		// ARTICLE 4 - MLB NEWS     
		
		echo MARGIN4."</div>".PHP_EOL.PHP_EOL;
    }
    catch (Exception $e)
	{
		ManageException($e, "Unable to generate articles php file");
	}
}

// PUMP WEBSITE
function PumpWebsite($url)
{
	$retval = "";
	try
	{
		libxml_use_internal_errors(true);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		$output = curl_exec($ch);
		curl_close($ch);
	}
    catch (Exception $e)
	{
		ManageException($e, "Unable to pump website ".$url);
		throw $e;
	}
	return $retval;
}
// EXTRACT WEBSITE MAIN CONTENT
function ExtractWebsiteMainContent($content)
{
	$retval = "";
	try
	{
		$doc = new DOMDocument();
		$doc->loadHTML($content);
		$pageContainer = $doc->getElementById('pageContainer');
		$divs = $pageContainer->getElementsByTagName('div');
		foreach($divs as $node)
		{
			$nodeClass = $node->getAttribute("class");
			if (stripos($nodeClass,"storyCopy")!==false)
			{
				$retval = $node->C14N();
			}
		}
	}
    catch (Exception $e)
	{
		ManageException($e, "Unable to extract website main content ");
		throw $e;
	}
	return $retval;
}
?>
</body>
</html>