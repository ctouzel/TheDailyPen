<?php
//
// THE DAILY PEN FUNCTIONS
// The Best Offline Newspaper Still Alive!
// By Christian Touzel
//

// BUILD TWITTER AUTHORIZATION HEADER
function BuildTwitterAuthorizationHeader($oauth) 
{
    $r = 'Authorization: OAuth ';
    try
    {
        $values = array();
        foreach ($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
        $r .= implode(', ', $values);
    }
    catch (Exception $e)
    {
        $lognow = new DateTime();
        $lognow->setTimezone(new DateTimeZone('America/Montreal'));
        $GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')."
            - ERROR - Unable to build twitter authorization header (".$e->getMessage().")</p>";
    }
    return $r;
}

// BUILD TWITTER BASE STRING
function BuildTwitterBaseString($baseURI, $method, $params) 
{
    $r = array();
    ksort($params);
    foreach($params as $key=>$value)
	{
        $r[] = "$key=" . rawurlencode($value);
    }
    return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}

// GET HEADLINES FROM RSS
function GetHeadlinesFromRSS($feedsrc, $max, $dayinterval)
{
	$now = new DateTime();
	$retval = "";
	$feedobj = GetRSSObject($feedsrc);
	$feedstamp = new DateTime($feedobj->get_item(0)->get_date());
	$feedstamp->setTimezone(new DateTimeZone('America/Montreal'));
	$since_start = floor(($now->format('U') - $feedstamp->format('U')) / (60*60*24));
	if ($dayinterval=="-1" || $since_start<=$dayinterval)
	{
		for ($i=0; $i<$max; $i++) 
		{
			$retval = $retval . "<p>". $feedobj->get_item($i)->get_title(). "</p>";
		}
	}
	return $retval;
}

// GET HEYMAN FEED
function GetHeymanFeed()
{
	libxml_use_internal_errors(true);
	$retval = "<h3>JON HEYMAN</h3>";
	$feedobj = GetRSSObject("http://www.cbssports.com/partners/feeds/rss/mlb_news");
	if (is_null($feedobj) || is_null($feedobj->get_item(0)))
	{
		return "";
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $feedobj->get_item(0)->get_link());
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	$output = curl_exec($ch);
	$doc = new DOMDocument();
	$doc->loadHTML($output);
	$retval = $retval . "<p><b>". $feedobj->get_item(0)->get_title(). "</b><br/>";
	$content = $doc->getElementById('pageContainer');
	$divs = $content ->getElementsByTagName('div');
	foreach($divs as $node) 
	{
		$hoy = $node->getAttribute("class");
		if (stripos($hoy,"storyCopy")!==false)
		{
			$retval = $retval . $node->C14N();
		}
	}
	curl_close($ch);
	$retval = $retval . "</p>";
	return $retval;
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

// GET RANDOM QUOTE
function GetRandomQuote()
{
	$retval = "";
    try
    {
        $filename = "quotes.xml";

        if (file_exists($filename))
        {
            $xmlquotes = ("quotes.xml");
            $xmlquotesDoc = new DOMDocument();
            $xmlquotesDoc->load($xmlquotes);
            $x = $xmlquotesDoc->getElementsByTagName('quote');
            $xlen = $xmlquotesDoc->getElementsByTagName('quote')->length;
            $quote = $x->item(rand(0, $xlen - 1));
            $retval = $retval . "<i>" . $quote->nodeValue . "</i>";
            $retval = $retval . "<br>-" . $quote->getAttribute('source');
        }
        else
        {
            throw new Exception('Unable to find quotes.xml file');
        }
    }
    catch (Exception $e)
    {
        $lognow = new DateTime();
        $lognow->setTimezone(new DateTimeZone('America/Montreal'));
        $GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')."
            - ERROR - Unable to get random quote (".$e->getMessage().")</p>";
    }
	return $retval;
}

// GET RANDOM SPANISH ITEMS
function GetRandomSpanishItems()
{
	$retval = "";
	$retval = $retval . "<h3>Spanish - ";
	$xmlspanish=("items.xml");
	$xmlspanishDoc = new DOMDocument();
	$xmlspanishDoc->load($xmlspanish);
	$x=$xmlspanishDoc->getElementsByTagName('item');
	$xlen=$xmlspanishDoc->getElementsByTagName('item')->length;
	$spanishnode = $x->item(rand(0, $xlen-1));
	$retval = $retval . $spanishnode->childNodes->item(1)->nodeValue." (".$spanishnode->childNodes->item(3)->nodeValue.")</h3>";
	$retval = $retval . $spanishnode->childNodes->item(5)->nodeValue;
	$retval = $retval . "<h3>Spanish - Grammar</h3>";
	$retval = $retval . GetRandomItemFromXML("grammar.xml");
	return $retval;
}

// GET RSS OBJECT
function GetRSSObject($feedsrc)
{
	$feedobj = new SimplePie();
	$feedobj->set_feed_url($feedsrc);
	$feedobj->handle_content_type();
	try
	{
		$feedobj->init();
	}
	catch (Exception $e) 
	{
		return NULL;
	}
	return $feedobj;
}

// READ FROM COMICS RSS 
function ReadFromComicsRSS($feedsrc, $headerd, $max, $content, $dayinterval)
{
	$now = new DateTime();
	$retval = "";
	$feedobj = GetRSSObject($feedsrc);
	if (is_null($feedobj) || is_null($feedobj->get_item(0)))
	{
		return "";
	}
	$feedstamp = new DateTime($feedobj->get_item(0)->get_date());
	$feedstamp->setTimezone(new DateTimeZone('America/Montreal'));
	$since_start = floor(($now->format('U') - $feedstamp->format('U')) / (60*60*24));
	if ($dayinterval=="-1" || $since_start<=$dayinterval)
	{
		$retval = $retval."<h3>".$headerd."</h3>";
		for ($i=0; $i<$max; $i++) 
		{
			$retval = $retval . "<p>";
			if ($content)
			{
				$htmlval = $feedobj->get_item($i)->get_content();
				preg_match('/<img[^>]+>/i',$htmlval, $image);
				$retval = $retval . $image[0]. "</p>";
			}
			else
			{
				$htmlval = $feedobj->get_item($i)->get_description();
				preg_match('/<img[^>]+>/i', $htmlval, $image);
				$retval = $retval . $image[0] . "</p>";
			}
		}
	}
	return $retval;
}

// READ FROM COMICS RSS
// SELECT THE RIGHT IMAGE
function ReadFromComicsRSS_IMG($feedsrc, $headerd, $max, $content, $dayinterval, $imgnum)
{
	$now = new DateTime();
	$retval = "";
	$feedobj = GetRSSObject($feedsrc);
	if (is_null($feedobj) || is_null($feedobj->get_item(0)))
	{
		return "";
	}
	$feedstamp = new DateTime($feedobj->get_item(0)->get_date());
	$feedstamp->setTimezone(new DateTimeZone('America/Montreal'));
	$since_start = floor(($now->format('U') - $feedstamp->format('U')) / (60*60*24));
	if ($dayinterval=="-1" || $since_start<=$dayinterval)
	{
		$retval = $retval."<h3>".$headerd."</h3>";
		for ($i=0; $i<$max; $i++) 
		{
			$retval = $retval . "<p>";
			if ($content)
			{
				$htmlval = $feedobj->get_item($i)->get_content();
				preg_match_all('/<img[^>]+>/i',$htmlval, $image);
				$retval = $retval . $image[0][1]. "</p>";
			}
			else
			{
				$htmlval = $feedobj->get_item($i)->get_description();
				preg_match_all('/<img[^>]+>/i', $htmlval, $image);
				$retval = $retval . $image[0][1] . "</p>";
			}
		}
	}
	return $retval;
}

// READ FROM RSS
function ReadFromRSS($feedsrc, $headerd, $max, $content, $dayinterval)
{
	$now = new DateTime();
	$retval = "";
	$feedobj = GetRSSObject($feedsrc);
	if (is_null($feedobj) || is_null($feedobj->get_item(0)))
	{
		return "";
	}
	$feedstamp = new DateTime($feedobj->get_item(0)->get_date());
	$feedstamp->setTimezone(new DateTimeZone('America/Montreal'));
	$since_start = floor(($now->format('U') - $feedstamp->format('U')) / (60*60*24));
	if ($dayinterval=="-1" || $since_start<=$dayinterval)
	{
		$retval = $retval."<h3>".$headerd."</h3>";
		for ($i=0; $i<$max; $i++) 
		{
			$retval = $retval . "<p><b>". $feedobj->get_item($i)->get_title(). "</b><br/>";
			$enclosure = $feedobj->get_item($i)->get_enclosure();
			if ($content)
			{
				$retval = $retval . $feedobj->get_item($i)->get_content()."</p>";
			}
			else
			{
				$retval = $retval . $feedobj->get_item($i)->get_description()."</p>";
			}
		}
	}
	return $retval;
}

// READ FROM RSS NO IMAGE
function ReadFromRSS_NoImage($feedsrc, $headerd, $max, $content, $dayinterval)
{
	$output = ReadFromRSS($feedsrc, $headerd, $max, $content, $dayinterval);
    $output = preg_replace("/<img[^>]+\>/i", "", $output); 
	return $output;
}

// READ WEATHER
function ReadWeather($current)
{
	$retval = "";
	$currentweather = "";
	$feed = new SimplePie();
	$feed->set_feed_url("http://meteo.gc.ca/rss/city/qc-133_f.xml");
	$feed->handle_content_type();
	$feed->set_timeout(20);
	$feed->enable_order_by_date(false);
	$feed->set_cache_location($_SERVER['DOCUMENT_ROOT'] . '/cache');
	$feed->init();

	for ($i=1; $i<6; $i++) 
	{
		$titleraw = $feed->get_item($i)->get_title();
		if (stripos($titleraw, "Conditions actuelles")!==false)
		{
			$subtitle = str_replace("Conditions actuelles:", "", $titleraw);
			if (strrpos($feed->get_item(0)->get_title(), "Aucune") === false) 
			{ 
			   $retval = "<h3>".$subtitle." / ".$feed->get_item(0)->get_title()."</h3>";
			}
			else
			{
				$retval = "<h3>".$subtitle."</h3>";
			}
			$currentweather = $subtitle;
		}
		else
		{
			$retval = $retval . "<h4>" . $feed->get_item($i)->get_title(). "</h4>";
		}
		$descraw = $feed->get_item($i)->get_description();
		if (stripos($descraw,"Humidit&eacute;")!==false)
		{
			$descraw = substr($descraw,stripos($descraw,"Humidit&eacute;"));
			$descraw = substr($descraw,0, stripos($descraw,"Cote"));
			$descraw = str_replace("<b>", "", $descraw);
			$descraw = str_replace("<br>", ", ", $descraw);
		}

		$finalstr = $descraw;
		if (stripos($descraw,"Pr&eacute;visions")===false)
		{
			$retval = $retval . $descraw;
		}
		else
		{
			$pos = stripos($descraw,"Pr&eacute;visions");
			$desc = substr($descraw,0,$pos);
			$retval = $retval . $desc;
		}
		
		if (stripos($finalstr,"Point de")!==false)
		{
			//$retval = $retval . $descraw;
		}
	}

	if ($current)
	{
		$retval = $currentweather;
	}
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

// READ STEREOGUM FEED
function ReadStereogumFeed()
{
	libxml_use_internal_errors(true);
	$retval = "<h3>STEREOGUM</h3>";
	$feedobj = GetRSSObject("http://feeds.feedburner.com/stereogum/cBYa");
	if (is_null($feedobj) || is_null($feedobj->get_item(0)))
	{
		return "";
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $feedobj->get_item(0)->get_link());
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	$output = curl_exec($ch);
	$doc = new DOMDocument();
	$doc->loadHTML($output);
	$retval = $retval . "<p><b>". $feedobj->get_item(0)->get_title(). "</b><br/>";
	$content = $doc->getElementById('content');
	$divs = $content ->getElementsByTagName('div');
	foreach($divs as $node) 
	{
		$hoy = $node->getAttribute("class");
		if ($hoy == "entry line_top")
		{
			$retval = $retval . str_replace(' align="center"', '',  $node->C14N());
		}
	}
	curl_close($ch);
	$retval = $retval . "</p>";
	return $retval;
}

// GET MENAGE A TROIS FEED
function GetMenageATroisFeed()
{
	try
	{
		libxml_use_internal_errors(true);
		$now = new DateTime();
		$feedobj = GetRSSObject("http://www.menagea3.net/comic.rss");
		if (is_null($feedobj) || is_null($feedobj->get_item(0)))
		{
			return "";
		}
		$feedstamp = new DateTime($feedobj->get_item(0)->get_date());
		$feedstamp->setTimezone(new DateTimeZone('America/Montreal'));
		$since_start = floor(($now->format('U') - $feedstamp->format('U')) / (60*60*24));
		if ($since_start<=0)
		{
			$retval = "<h3>MENAGE A TROIS</h3>";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $feedobj->get_item(0)->get_link());
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
			$output = curl_exec($ch);
			$doc = new DOMDocument();
			$doc->loadHTML($output);
			$content = $doc->getElementById('cc');
			$retval = $retval . $content->C14N();
			curl_close($ch);
			return $retval;
		}
		else
		{
			$lognow = new DateTime();
			$lognow->setTimezone(new DateTimeZone('America/Montreal'));
			$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - WARNING - Menage à Trois feed is too old (".$since_start." days)</p>";
			return "";
		}
	}
	catch (Exception $e) 
	{
		$lognow = new DateTime();
		$lognow->setTimezone(new DateTimeZone('America/Montreal'));
		$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Unable to retrieve Menage a trois web content(".$e->getMessage().")</p>";
		return "";
	}
}

// GET TWITTER TIMELINE
function GetTwitterTimeline()
{
	$retval = "";
	try
	{
		$url = "https://api.twitter.com/1.1/statuses/home_timeline.json";
		$oauth = array( 'oauth_consumer_key' => "rmNNb4TnFb8snThPevnbrGO9a",
						'oauth_nonce' => time(),
						'oauth_signature_method' => 'HMAC-SHA1',
						'oauth_token' => "2882600523-zU7vBzFr3hIXVqLtazZZfswfxBKwG4OVxIe5QrP",
						'oauth_timestamp' => time(),
						'oauth_version' => '1.0');

		$base_info = BuildTwitterBaseString($url, 'GET', $oauth);
		$composite_key = rawurlencode("gp0pb0upsvI3wRVKPUkXfIPqf73fcMwc62eWclznBS9NG4T0d0") . '&' . rawurlencode("psm7Vr65HQ59qBGQC0DNy773Owgjz7rvLhqQ32YA96nAT");
		$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
		$oauth['oauth_signature'] = $oauth_signature;
		$header = array(BuildTwitterAuthorizationHeader($oauth), 'Expect:');
		$options = array( CURLOPT_HTTPHEADER => $header,
						  CURLOPT_HEADER => false,
						  CURLOPT_URL => $url,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_SSL_VERIFYPEER => false);
		$feed = curl_init();
		curl_setopt_array($feed, $options);
		$json = curl_exec($feed);
		curl_close($feed);
		$twitter_data = json_decode($json);
		foreach ($twitter_data as $tweet)
		{
			$text = $tweet->text;
			$author = $tweet->user->name;
			$retval = $retval . "<p>".$text . "<br/>-".$author."</p>";
		}
	}
	catch (Exception $e) 
	{
		$lognow = new DateTime();
		$lognow->setTimezone(new DateTimeZone('America/Montreal'));
		$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Unable to retrieve twitter timeline(".$e->getMessage().")</p>";
	}
	return $retval;
}

// READ FROM CYBERPRESSE
function ReadFromCyberpresse($feedsrc, $headerd, $max, $content, $dayinterval)
{
	$now = new DateTime();
	$retval = "";
	try
	{
		$feedobj = GetRSSObject($feedsrc);
		if (is_null($feedobj) || is_null($feedobj->get_item(0)))
		{
			return "";
		}
		$feedstamp = new DateTime($feedobj->get_item(0)->get_date());
		$feedstamp->setTimezone(new DateTimeZone('America/Montreal'));
		$since_start = floor(($now->format('U') - $feedstamp->format('U')) / (60*60*24));
		if ($dayinterval=="-1" || $since_start<=$dayinterval)
		{
			$retval = $retval."<h3>".$headerd."</h3>";
			for ($i=0; $i<$max; $i++) 
			{
				$retval = $retval . "<p><b>". $feedobj->get_item($i)->get_title(). "</b><br>";
				$desc = $feedobj->get_item($i)->get_content();
				$pos = stripos($desc,"<br clear");
				$desc = substr($desc,0,$pos);
				$retval = $retval. $desc . "</p>";
			}
		}
		$retval = preg_replace("/<img[^>]+\>/i", "", $retval); 
	}
	catch (Exception $e) 
	{
		$lognow = new DateTime();
		$lognow->setTimezone(new DateTimeZone('America/Montreal'));
		$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Unable to retrieve feed from cyberpresse - ".$feedsrc." (".$e->getMessage().")</p>";
	}
	return $retval;
}

// READ OPINIONS FROM CYBERPRESSE
function ReadOpinionsFromCyberpresse($feedsrc, $max)
{
	$retval = "";
	$feedobj = GetRSSObject($feedsrc);
	if (is_null($feedobj) || is_null($feedobj->get_item(0)))
	{
		return "";
	}
	for ($i=0; $i<$max; $i++) 
	{
		try
		{
			$retval = $retval."<h3>LA PRESSE - ".$feedobj->get_title();
			$retval = $retval . $feedobj->get_item($i)->get_title(). "</h3>";
			$desc = GetOpinionContentFromCyberpresse($feedobj->get_item($i)->get_link());
			$retval = $retval. $desc . "</p>";
		}	
		catch (Exception $e) 
		{
			$lognow = new DateTime();
			$lognow->setTimezone(new DateTimeZone('America/Montreal'));
			$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Unable to parse Cybepresse opinions content (".$feedobj->get_item($i)->get_link().")</p>";
		}
	}
	$retval = preg_replace("/<img[^>]+\>/i", "", $retval); 
	return $retval;
}

// GET OPINION CONTENT FROM CYBERPRESSE
function GetOpinionContentFromCyberpresse($link)
{
	$retval = "";
	try
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		$output = curl_exec($ch);
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		if ($output=="")
		{
			$lognow = new DateTime();
			$lognow->setTimezone(new DateTimeZone('America/Montreal'));
			$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Cyberpresse opinion link returned an empty string (".$link.")</p>";
			return "";
		}
		$doc->loadHTML($output);
		$content = $doc->getElementById('maincontent');
		if (is_null($content))
		{
			$lognow = new DateTime();
			$lognow->setTimezone(new DateTimeZone('America/Montreal'));
			$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Cyberpresse opinion link does not have a 'maincontent' div (".$link.")</p>";
			return "";
		}
		$divs = $content->getElementsByTagName('div');
		foreach($divs as $node) 
		{
			$hoy = $node->getAttribute("class");
			if (stripos($hoy,"excerpt")!==false)
			{
				$retval = $retval. $node->C14N();
			}
			if (stripos($hoy,"chapitreTexte")!==false)
			{
				$retval = $retval. $node->C14N();
			}
		}
	}
	catch (Exception $e) 
	{
		$lognow = new DateTime();
		$lognow->setTimezone(new DateTimeZone('America/Montreal'));
		$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Unable to retrieve opinions content from cyberpresse - ".$link." (".$e->getMessage().")</p>";
	}
	return $retval;
}

// GET TODOIST TASKS
function GetTodoistTasks()
{
	$retval = "";
	try
	{
		date_default_timezone_set('America/Montreal');
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		error_reporting(-1);

		$feed = curl_init();
		curl_setopt($feed, CURLOPT_URL, "http://www.todoist.com/API/getProjects?token=a3b4994d3e4525f8e559a8428e2872a90acf4dad");
		curl_setopt($feed, CURLOPT_HEADER, 0);
		curl_setopt($feed, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($feed, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($feed, CURLOPT_MAXREDIRS, 3);
		$projects_all = curl_exec($feed);
		curl_close($feed);
		$todoist_projects = json_decode($projects_all, true);

		$today = new DateTime();
		$today->setTimezone(new DateTimeZone('America/Montreal'));
		$today->setTime( 0, 0, 0 );
		$str_yesterday = "";
		$str_today = "";
		$str_tomorrow = "";

		foreach ($todoist_projects as $project)
		{
			$project_id = $project["id"];
			$feedItems = curl_init();
			curl_setopt($feedItems, CURLOPT_URL, "http://www.todoist.com/API/getUncompletedItems?token=a3b4994d3e4525f8e559a8428e2872a90acf4dad&project_id=".$project_id);
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
			$retval = $retval. "<h3>Yesterday's Tasks</h3>";
			$retval = $retval.  "<ul>".$str_yesterday."</ul>";
		}
		$retval = $retval.  "<h3>Today's Tasks</h3>";
		$retval = $retval.  "<ul>".$str_today."</ul>";
	}
	catch (Exception $e) 
	{
		$lognow = new DateTime();
		$lognow->setTimezone(new DateTimeZone('America/Montreal'));
		$GLOBALS['errors'] = $GLOBALS['errors'] . "<p>".$lognow->format('H:i:s')." - ERROR - Unable to retrieve Todoist tasks (".$e->getMessage().")</p>";
	}
	return $retval;
}
?>