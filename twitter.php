<?php

//
// THE DAILY PEN FUNCTIONS - TWITTER FUNCTIONS
// By Christian Touzel
//

// BUILD TWITTER AUTHORIZATION HEADER
function BuildTwitterAuthorizationHeader($oauth) 
{
    $r = 'Authorization: OAuth ';
    try
    {
        $values = array();
        foreach ($oauth as $key => $value)
        {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
        $r .= implode(', ', $values);
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to build twitter authorization header");
        throw $e;
    }
    return $r;
}

// BUILD TWITTER BASE STRING
function BuildTwitterBaseString($baseURI, $method, $params) 
{
    try
    {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value)
        {
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }
    catch (Exception $e)
    {
        ManageException($e, "Unable to build twitter base string");
        throw $e;
    }
}


// GET TWITTER TIMELINE
function GetTwitterTimeline()
{
    $retval =           MARGIN4. "<div id=\"twitter\">".PHP_EOL;
    $retval = $retval . MARGIN5. "<h2>TWITTER</h2>".PHP_EOL;
    
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
		$count = 0;
		foreach ($twitter_data as $tweet)
		{
			$count = $count + 1;
			$text = $tweet->text;
            $text = str_replace("\r\n","", $text);
            $text = str_replace("\n","", $text);
            $text = str_replace("\r","", $text);
			$author = $tweet->user->name;
			if ($count < 11)
			{
				$retval = $retval . MARGIN5. "<p>".$text . "<br/>-".$author."</p>".PHP_EOL;
			}
		}
        $retval = $retval . MARGIN4. "</div>".PHP_EOL.PHP_EOL;
	}
	catch (Exception $e) 
	{
        ManageException($e, "Unable to retrieve twitter timeline");
	}
	return $retval;
}


// GET TWITTER TIMELINE LIST
function GetTwitterTimelineLIST($list,$count)
{
	$retval = "";
	try
	{
		$url = "https://api.twitter.com/1.1/lists/statuses.json?slug=premium&owner_screen_name=TheDailyPen";
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
		ManageException($e, "Unable to retrieve twitter list");
	}
	return $retval;
}

?>