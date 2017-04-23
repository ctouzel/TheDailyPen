<?php
define("MARGIN1", "   ");
define("MARGIN2", "     ");
define("MARGIN3", "        ");
define("MARGIN4", "           ");
define("MARGIN5", "              ");
define("MARGIN6", "                 ");
define("MARGIN7", "                    ");
define("MARGIN8", "                       ");   
define("MARGIN9", "                          ");

//
// THE DAILY PEN FUNCTIONS
// By Christian Touzel
//

// GLOBAL APP ERROR HANDLER
// Throw an exception
function ManageDailyPenErrors($errno, $errstr) 
{
    echo "<p><b>The Daily Pen has a small problem:</b>".$errstr."</p>";
    throw new Exception($errstr);
}

// MANAGE EXCEPTION
function ManageException($exception, $additionalInfo)
{
    $lognow = new DateTime();
    $GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')."
            - ERROR - ".$additionalInfo." (".$exception->getMessage().")</p>";
}

// GET RANDOM ITEM FROM XML
function GetRandomItemFromXML($customXML)
{
	$retval = "";
	$xmlcustom=($customXML);
	$xmlcustomDoc = new DOMDocument();
	$xmlcustomDoc->load($xmlcustom);
	$x=$xmlcustomDoc->getElementsByTagName('item');
	$xlen=$xmlcustomDoc->getElementsByTagName('item')->length;
	$retval = $retval . $x->item(rand(0, $xlen-1))->nodeValue;
	return $retval;
}

// GET DAILY PEN HTML HEAD
function GetDailyPenHTMLHead($version, $title)
{
    $retval = "";
    try
    {
        $now = new DateTime();
        $retval = $retval . "<!DOCTYPE html>".PHP_EOL;
        $retval = $retval . "<html lang=\"en\">".PHP_EOL;
        $retval = $retval . MARGIN1 . "<head>".PHP_EOL;
        $retval = $retval . MARGIN2 . "<title>".$title.", ".$now->format('F j')."</title>".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"UTF-8\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta http-equiv=\"PRAGMA\" content=\"NO-CACHE\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta http-equiv=\"Expires\" content=\"-1\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta property=\"og:title\" content=\"".$title."\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta property=\"og:type\" content=\"article\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta property=\"og:url\" content=\"http://www.touzel.com/daily.php\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta property=\"author\" content=\"Christian Touzel\">".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta property=\"version\" content=\"".$version."\">".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta property=\"article:published_time\" content=\"".$now->format('Y-m-d')."T".$now->format('H:i:s').".000Z"."\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<meta property=\"article:modified_time\" content=\"".$now->format('Y-m-d')."T".$now->format('H:i:s').".000Z"."\" />".PHP_EOL;
        $retval = $retval . MARGIN2 . "<link rel=\"apple-touch-icon\" href=\"logo.png\">".PHP_EOL;
        $retval = $retval . MARGIN2 . "<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">".PHP_EOL;
        $retval = $retval . MARGIN2 . "<link rel=\"stylesheet\" type=\"text/css\" href=\"http://fonts.googleapis.com/css?family=Playfair+Display:400,700,900,400italic,700italic,900italic|Droid+Serif:400,700,400italic,700italic\">".PHP_EOL;
        $retval = $retval . MARGIN2 . "<link rel=\"stylesheet\" type=\"text/css\" href=\"http://fonts.googleapis.com/css?family=Cabin\">".PHP_EOL;
        $retval = $retval . MARGIN1 . "</head>".PHP_EOL;
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to generate the daily pen html head");
    }
    return $retval;
}

// GET DAILY PEN HEADER
function GetDailyPenHeader($title)
{
    $retval = "";
    try
    {
        $now = new DateTime();
        $retval = $retval . MARGIN1 . "<body>".PHP_EOL;
        $retval = $retval . MARGIN2 . "<article class=\"hentry\">".PHP_EOL;
        $retval = $retval . MARGIN3 . "<header>".PHP_EOL;
        $retval = $retval . MARGIN4 . "<div class=\"head\">".PHP_EOL;
        $retval = $retval . MARGIN5 . "<div class=\"headerobjectswrapper\">".PHP_EOL;
        $retval = $retval . MARGIN6 . "<span class=\"entry-title\">".$title."</span>".PHP_EOL;
        $retval = $retval . MARGIN5 . "</div>".PHP_EOL;
        $retval = $retval . MARGIN5 . "<div class=\"subhead\">".PHP_EOL;
        $retval = $retval . MARGIN6 . GetRandomStrippedQuote();
        $retval = $retval . MARGIN5 . "</div>".PHP_EOL;
        $retval = $retval . MARGIN4 . "</div>".PHP_EOL;
        $retval = $retval . MARGIN4 . "<time class=\"published\" datetime=\"".$now->format('Y-m-d')."T".$now->format('H:i:s').".000Z\"/>".PHP_EOL;
        $retval = $retval . MARGIN3 . "</header>".PHP_EOL.PHP_EOL;
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to generate the daily pen header");
    }
    return $retval;
}

// GET RANDOM QUOTE
function GetRandomQuote()
{
	$retval = "";
    try
    {
        $quotesFilename = "quotes.xml";
        if (file_exists($quotesFilename))
        {
            $xmlquotesDoc = new DOMDocument();
            $xmlquotesDoc->load($quotesFilename);
            $x = $xmlquotesDoc->getElementsByTagName('quote');
            $xlen = $xmlquotesDoc->getElementsByTagName('quote')->length;
            $quote = $x->item(rand(0, $xlen - 1));
            $quotevalue = trim($quote->nodeValue);
            $quotevalue = str_replace("\r\n","", $quotevalue);
            $retval = $retval . MARGIN4. "<div id=\"quote\">".PHP_EOL;
            $retval = $retval . MARGIN5. "<i>".$quotevalue."</i><br/>".PHP_EOL;
            $retval = $retval . MARGIN5. "-" .trim($quote->getAttribute('source')) .PHP_EOL;
            $retval = $retval . MARGIN4. "</div>".PHP_EOL.PHP_EOL;
        }
        else
        {
            throw new Exception('Unable to find file '.$quotesFilename);
        }
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to get random quote");
    }
	return $retval;
}

function GetRandomStrippedQuote()
{
	$retval = "";
    try
    {
        $quotesFilename = "quotes.xml";
        if (file_exists($quotesFilename))
        {
            $xmlquotesDoc = new DOMDocument();
            $xmlquotesDoc->load($quotesFilename);
            $x = $xmlquotesDoc->getElementsByTagName('quote');
            $xlen = $xmlquotesDoc->getElementsByTagName('quote')->length;
            $quote = $x->item(rand(0, $xlen - 1));
            $quotevalue = trim($quote->nodeValue);
            $quotevalue = str_replace("\r\n","", $quotevalue);
            $retval = $retval . MARGIN5. "<i>".$quotevalue."</i>".PHP_EOL;
        }
        else
        {
            throw new Exception('Unable to find file '.$quotesFilename);
        }
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to get random quote");
    }
	return $retval;
}

// GET COMPLETE WEATHER
function GetCompleteWeather()
{
    $retval           = "";
    $final            = "";
    $header           = MARGIN4. "<div id=\"weather\">".PHP_EOL;
    $header = $header . MARGIN5. "<h2>METEO</h2>".PHP_EOL;
    try
	{
        $feed = new SimplePie();
        $feed->set_feed_url(URL_WEATHER);
        $feed->handle_content_type();
        $feed->set_timeout(20);
        $feed->enable_order_by_date(false);
        $feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . '/cache');
        $feed->init();

	$header = $header .MARGIN5. "<h3>". trim(str_replace("Conditions actuelles:", "", $feed->get_item(1)->get_title()))."</h3>".PHP_EOL;
        for ($i = 2; $i < 5; $i++)
		{
			$sub = $feed->get_item($i)->get_title();
			$pos = stripos($sub, ":");
			$sub = substr($sub, 0, $pos);
			$retval = $retval . MARGIN5. "<h3>" . $sub . "</h3>".PHP_EOL;
            $descraw = $feed->get_item($i)->get_description();
            if (stripos($descraw, "Humidit&eacute;") !== false)
			{
                $descraw = substr($descraw, stripos($descraw, "Humidit&eacute;"));
                $descraw = substr($descraw, 0, stripos($descraw, "Cote"));
                $descraw = str_replace("<b>", "", $descraw);
                $descraw = str_replace("<br>", ", ", $descraw);
            }
			if (stripos($descraw, "Pr&eacute;visions") !== false)
			{
				$descraw = substr($descraw, 0, stripos($descraw, "Pr&eacute;visions"));
			}
			$retval = $retval . MARGIN5. $descraw.PHP_EOL;
        }
        $final = $header .$retval. MARGIN4. "</div>".PHP_EOL.PHP_EOL;
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to retrieve weather");
    }
	return $final;
}


// GET HEADLINES
function GetHeadlines($id, $source, $subtitle)
{
    $retval =           MARGIN4. "<div id=\"".$id."\">".PHP_EOL;
    $retval = $retval . MARGIN5. "<h2>".$subtitle."</h2>".PHP_EOL;
    $retval = $retval . GetHeadlinesFromRSS($source, 10, 0);
    $retval = $retval . MARGIN4. "</div>".PHP_EOL.PHP_EOL;
    return $retval;
}

function GetHeadlinesMax($id, $source, $subtitle, $max)
{
    $retval =           MARGIN4. "<div id=\"".$id."\">".PHP_EOL;
    $retval = $retval . MARGIN5. "<h2>".$subtitle."</h2>".PHP_EOL;
    $retval = $retval . GetHeadlinesFromRSS($source, $max, 0);
    $retval = $retval . MARGIN4. "</div>".PHP_EOL.PHP_EOL;
    return $retval;
}

// GET NEWS
function GetNews($id, $source, $subtitle)
{
    $retval =           MARGIN4. "<div id=\"".$id."\">".PHP_EOL;
    $retval = $retval . MARGIN5. "<h2>".$subtitle."</h2>".PHP_EOL;
    $retval = $retval . ReadFromRSS_NoImage($source, "", 10, false, 0);
    $retval = GetRidOf("<br clear=\"all\">", $retval);
    $retval = GetRidOf("(Yahoo Sports)", $retval);
    $retval = $retval . MARGIN4. "</div>".PHP_EOL.PHP_EOL;
    return $retval;
}


// SEND EMAIL
function SendEmail($emailcontent, $emailEnabled)
{
	$now = new DateTime();
	$now->setTimezone(new DateTimeZone('America/Montreal'));
	$date_title = $now->format('F j, Y');
	$mail = new PHPMailer();
	$mail->CharSet = "UTF-8";
	$mail->IsSMTP();
	$mail->SMTPDebug  = 0;
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = "ssl";
	$mail->Host       = "smtp.gmail.com";
	$mail->Port       = 465;
	$mail->Username   = "larryzona966@gmail.com";
	$mail->Password   = "cactus1976";
	$mail->SetFrom('larryzona966@gmail.com');
	$mail->FromName = "Larry Zona";
	$mail->Subject    = $date_title;
	$mail->MsgHTML($emailcontent);
	$mail->AddAddress("readlater.okt7d91epek@instapaper.com", "");
	if ($emailEnabled)
	{
		if(!$mail->Send()) 
		{
			echo "<h3>Cannot send email. Problems encountered...</h3>";
		} 
		else 
		{
			echo "<h3>Message sent!</h3>";
		}
	}
	else
	{
		echo "<h3>Email Sending Disabled</h3>";
	}
}


// GET TODOIST TASKS
function GetTodoistTasks()
{
    $retval           = MARGIN4. "<div id=\"todoist\">".PHP_EOL;
	try
	{
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		error_reporting(-1);

		$feed = curl_init();
		curl_setopt($feed, CURLOPT_URL, URL_TODOIST);
		curl_setopt($feed, CURLOPT_HEADER, 0);
		curl_setopt($feed, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($feed, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($feed, CURLOPT_MAXREDIRS, 3);
		$projects_all = curl_exec($feed);
		curl_close($feed);
		$todoist_projects = json_decode($projects_all, true);

		$today = new DateTime();
		$today->setTime( 0, 0, 0 );
		$str_yesterday = "";
		$str_today = "";
		$str_tomorrow = "";

		foreach ($todoist_projects as $project)
		{
			$project_id = $project["id"];
			$feedItems = curl_init();
			curl_setopt($feedItems, CURLOPT_URL, URL_TODOIST_TASKS.$project_id);
			curl_setopt($feedItems, CURLOPT_HEADER, 0);
			curl_setopt($feedItems, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($feedItems, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($feedItems, CURLOPT_MAXREDIRS, 3);
			curl_setopt($feedItems, CURLOPT_CONNECTTIMEOUT ,0); 
			curl_setopt($feedItems, CURLOPT_TIMEOUT, 400);
			$items_all = curl_exec($feedItems);
			curl_close($feedItems);	
			$todoist_data = json_decode($items_all, true);
			foreach ($todoist_data as $todo_item)
			{
				if (is_null($todo_item["due_date"])==false)
				{
					$time = new DateTime($todo_item["due_date"]);
					$time->setTimezone(new DateTimeZone('America/Montreal'));
					$time->setTime( 0, 0, 0 ); 
					$since_start = floor(($time->format('U') - $today->format('U')) / (60*60*24));
					switch( $since_start ) {
						case 0:
							$str_today = $str_today . "<li>" . $todo_item["content"]."</li>";
							break;
						case -1:
							$str_yesterday = $str_yesterday . "<li>". $todo_item["content"]."</li>";
							break;
						case +1:
							$str_tomorrow = $str_tomorrow . "<li>". $todo_item["content"]."</li>";
							break;
					}
				}
			}
		}
		if ($str_yesterday!="")
		{
			$retval = $retval . MARGIN5. "<h2>YESTERDAY'S TASKS</h2>";
			$retval = $retval . MARGIN5. "<ul>".$str_yesterday."</ul>";
		}
		$retval = $retval . MARGIN5. "<h2>TODAY'S TASKS</h2>";
		$retval = $retval . MARGIN5. "<ul>".$str_today."</ul>";
	}
	catch (Exception $e) 
	{
        ManageException($e, "Unable to retrieve Todoist tasks");
	}
    $retval = $retval. MARGIN4. "</div>".PHP_EOL.PHP_EOL;
	return $retval;
}

?>
