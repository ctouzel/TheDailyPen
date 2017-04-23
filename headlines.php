<?php

echo MARGIN3 . "<section>".PHP_EOL;

// RANDOM QUOTE
echo GetRandomQuote();

// WEATHER
echo GetCompleteWeather();

// NEWS HEADLINES
$headlines = array(
	"http://rss.radio-canada.ca/fils/regions/quebec.xml",
	"http://rss.radio-canada.ca/fils/regions/estduquebec.xml",
	"http://rss.radio-canada.ca/fils/nouvelles/international.xml",
	"http://www.cyberpresse.ca/rss/225.xml",
	"http://www.cyberpresse.ca/rss/501.xml");
echo GetHeadlines("latestnewsheadlines", $headlines, "DERNIERE HEURE");

// BASEBALL NEWS HEADLINES
echo GetHeadlines("baseballheadlines", "http://mlb.mlb.com/partnerxml/gen/news/rss/mlb.xml", "BASEBALL");

echo MARGIN3 . "</section>".PHP_EOL;

?>