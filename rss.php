<?php

//
// THE DAILY PEN FUNCTIONS - RSS BETA VERSION
// By Christian Touzel
//

// SIMPLEPLIE AUTOLOADER
spl_autoload_register(array(new SimplePie_Autoloader(), 'autoload'));
if (!class_exists('SimplePie'))
{
	trigger_error('Autoloader not registered properly', E_USER_ERROR);
}
class SimplePie_Autoloader
{
	/**
     * Constructor
     */
	public function __construct()
	{
		$this->path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library';
	}

	/**
     * Autoloader
     * @param string $class The name of the class to attempt to load.
     */
	public function autoload($class)
	{
		// Only load the class if it starts with "SimplePie"
		if (strpos($class, 'SimplePie') !== 0)
		{
			return;
		}

		$filename = $this->path . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		include $filename;
	}
}

// GET ARTICLE
function GetArticle($url, $author, $readability)
{
    $feedobj = GetRSSObject($url);
    $title = CleanText($feedobj->get_item(0)->get_title());
    if ($author == "")
    {
        $author = $feedobj->get_item(0)->get_author()->get_name();
    }

    $article = MARGIN6."<center><h2>".$title."</h2><p><span class=\"headline hl4\">by ".strtoupper($author)."</span></p></center>".PHP_EOL;
    $content = "";
    $id = $feedobj->get_item(0)->get_link();
    if ($readability == TRUE)
    {
        $content = GetReadabilityContent($id);
    }
    else
    {
        $content = $feedobj->get_item(0)->get_content();
    }
    $processed = CleanText($content);
    $article = $article.FormatCleanContent($processed);
    return $article;
}

function CleanText($content)
{
    $processed = str_replace("&amp;apos;", "'", $content);
    $processed = preg_replace("/<img[^>]+\>/i", "", $processed);
    $processed = preg_replace("/<p><strong>(.*?)<\/strong><\/p>/i", "", $processed);
    $processed = preg_replace("/<p><strong>(.*?)<\/a><\/p>/i", "", $processed);
    $processed = preg_replace("/<strong>(.*?)<\/strong>/i", "", $processed);
    $processed = preg_replace("/<figure(.*?)<\/figure>/i", "", $processed);

    $ridarray = array("<p><span><em>- - - - - - -</em></span></p>",
                      "<p><em>- - - - - - -</em></p>",
                      "- - - - - - -", 
                      "\r", "\n", "\t");

    $processed = GetRidOf("<p><span><div>Your browser does not support iframes.</div></span></p>", $processed);
    $processed = GetRidOfArray($ridarray, $processed);
    $processed = GetRidOf("<p>[]</p>", $processed);
    $processed = GetRidOf("<div class=\"entry-content\">", $processed);
    $processed = GetRidOf("<div class=\"article-body\">", $processed);
    $processed = GetRidOf("<div>", $processed);
    $processed = GetRidOf("</div>", $processed);
    $processed = GetRidOf('<p lang="en" dir="ltr">', $processed);
    $processed = GetRidOf("<span></span>", $processed);

    return $processed;
}

// FORMAT CLEAN CONTENT
function FormatCleanContent($cleancontent)
{
    $pieces = explode("</p>", $cleancontent);
    $final = "";
    foreach($pieces as $paragraph)
    {
        if (strpos($paragraph, 'source=') == false 
            && strpos($paragraph, 'MORE:') == false
            && strpos($paragraph, 'article') == false
            && strpos($paragraph, 'aside class') == false
            && strpos($paragraph, 'is a writer for') == false)
        {
            $subpars = explode("<p>", $paragraph);
            if (count($subpars) == 1)
            {
                $final = $final . MARGIN6 . "<p>" . $paragraph. "</p>". PHP_EOL;
            }
            else if (count($subpars) == 2 && $subpars[0] == "")
            {
                $final = $final . MARGIN6 . $paragraph. "</p>". PHP_EOL;
            }
            else
            {
                $final = $final . MARGIN6 . "<p>" . $subpars[0]. "</p>". PHP_EOL;
                $final = $final . MARGIN6 . "<p>" . $subpars[1]. "</p>". PHP_EOL;
            }
            
        }
        
    }
    return $final;
}

// GET RID OF 
function GetRidOf($rid, $content)
{
    return str_replace($rid, "", $content);
}

// GET RID OF ARRAY
function GetRidOfArray($ridarray, $content)
{
    foreach ($ridarray as $rid) 
    {
        $content = GetRidOf($rid, $content);
    }
    return $content;
}


function GetReadabilityContent($url)
{
	$token = "79d3c58d3b2d9c3d80f49eea71f9090311adc948";
	$readurl = "https://www.readability.com/api/content/v1/parser?url=".$url."&token=".$token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $readurl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    $output = curl_exec($ch);
    $parsed_json = json_decode($output);
    $content = $parsed_json->{'content'};
    curl_close($ch);
	return $content;
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
        if ($headerd !== "")
        {
            $retval = $retval. MARGIN5. "<h3>".$headerd."</h3>".PHP_EOL;
        }
        for ($i=0; $i<$max; $i++)
		{
			$retval = $retval . MARGIN5. "<p><b>". $feedobj->get_item($i)->get_title(). "</b><br/>".PHP_EOL;
			if ($content)
			{
				$retval = $retval . MARGIN5. $feedobj->get_item($i)->get_content()."</p>".PHP_EOL;
			}
			else
			{
				$retval = $retval . MARGIN5. $feedobj->get_item($i)->get_description()."</p>".PHP_EOL;
			}
		}
	}
	return $retval;
}

// GET HEADLINES FROM RSS
function GetHeadlinesFromRSS($feedsrc, $max, $dayinterval)
{
    $retval = "";
    try
    {
        $now = new DateTime();
        $feedobj = GetRSSObject($feedsrc);
        $feedstamp = new DateTime($feedobj->get_item(0)->get_date());
        $feedstamp->setTimezone(new DateTimeZone('America/Montreal'));
        $since_start = floor(($now->format('U') - $feedstamp->format('U')) / (60*60*24));
        if ($dayinterval=="-1" || $since_start<=$dayinterval)
        {
            for ($i=0; $i<$max; $i++)
            {
				if (is_null($feedobj->get_item($i)))
				{
					return "";
				}
                $retval = $retval . MARGIN5."<p>". $feedobj->get_item($i)->get_title(). "</p>".PHP_EOL;
            }
        }
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to retrieve headlines from rss feed".($feedsrc));
    }
	return $retval;
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

// READ FROM RSS NO IMAGE
function ReadFromRSS_NoImage($feedsrc, $headerd, $max, $content, $dayinterval)
{
	$output = ReadFromRSS($feedsrc, $headerd, $max, $content, $dayinterval);
    $output = preg_replace("/<img[^>]+\>/i", "", $output);
	return $output;
}



?>