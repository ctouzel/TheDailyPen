<?php 
require_once('autoloader.php');
include("dailypenfunctions.php"); 

// GLOBAL VARS
$version = "0.9.2.10003";
$GLOBALS['errors'] = "";
$now = new DateTime();
$now->setTimezone(new DateTimeZone('America/Montreal'));
$formatted = $now->format('Y-m-d')."T".$now->format('H:i:s').".000Z";
$headlines = array(
	"http://rss.radio-canada.ca/fils/regions/quebec.xml",
	"http://rss.radio-canada.ca/fils/regions/estduquebec.xml",
	"http://rss.radio-canada.ca/fils/nouvelles/international.xml",
	"http://www.cyberpresse.ca/rss/225.xml",
	"http://www.cyberpresse.ca/rss/501.xml");
	
echo "<html>\r\n";
echo "<head>\r\n";
echo "<title>The Daily Pen, ".$now->format('F j')."</title>\r\n";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"UTF-8\" />\r\n";
echo "<meta property=\"og:title\" content=\"The Daily Pen\" />\r\n";
echo "<meta property=\"og:type\" content=\"article\" />\r\n";
echo "<meta property=\"og:url\" content=\"http://www.touzel.com/daily.php\" />\r\n";
echo "<meta HTTP-EQUIV=\"CACHE-CONTROL\" content=\"NO-CACHE\" />\r\n";
echo "<meta HTTP-EQUIV=\"PRAGMA\" content=\"NO-CACHE\" />\r\n";
echo "<meta HTTP-EQUIV=\"Expires\" content=\"-1\" />\r\n";
echo "<meta HTTP-EQUIV=\"cache-control\" content=\"max-age=0\" />\r\n";
echo "<meta property=\"og:image\" content=\"http://www.touzel.com/logo2.png\" />\r\n";
echo "<meta property=\"author\" content=\"Christian Touzel\">\r\n";
echo "<meta property=\"version\" content=\"".$version."\">\r\n";
echo "<meta property=\"article:published_time\" content=\"".$formatted."\" />\r\n";
echo "<meta property=\"article:modified_time\" content=\"".$formatted."\" />\r\n";
echo "</head>\r\n";
echo "<body>\r\n";
echo "<article>\r\n";
echo "<h1>The Daily Pen, ".$now->format('F j')."</h1>\r\n";
echo "<section>\r\n";
echo GetRandomQuote();
echo "<h3>". GetWeather(true) . "</h3>";
echo GetWeather(false);
echo "<h3>DERNIERE HEURE</h3>".GetHeadlinesFromRSS($headlines, 8, 0);
echo "<h3>BASEBALL</h3>".GetHeadlinesFromRSS("http://mlb.mlb.com/partnerxml/gen/news/rss/mlb.xml", 8, 0);
echo "<h3>FOOTBALL</h3>".GetHeadlinesFromRSS("http://www.nfl.com/rss/rsslanding?searchString=home", 8, 0);
	
// GOOGLE CALENDAR
// ???

// TODOIST
//echo GetTodoistTasks();

// TWITTER
echo "<h3>TWITTER</h3>".GetTwitterTimeline();

// MLB NEWS
echo ReadFromRSS("http://mlb.mlb.com/partnerxml/gen/news/rss/mlb.xml", "MLB NEWS", 7, false, 0);

// MLB SCORES
echo "<h3>MLB SCORES</h3>\r\n";
GetMLBScores();

// MLB STANDINGS
echo "<h3>MLB STANDINGS</h3>\r\n";
//GetMLBStandings()."\r\n\r\n";

// MLB STATS
echo "<h3>MLB STATS</h3>\r\n";
//GetMLBStats()."\r\n\r\n";

// LOG REPORTS
echo "<h3>ERRORS REPORT</h3>";
echo $GLOBALS['errors'];
echo "<p style='font-size: 80%;'>The Daily Pen ".$version." is a creation of Christian Touzel. ";
echo "Page generated on ".$now->format('F j, Y H:i:s').". PHP version is ".phpversion()."</p>";

echo "<h3>FAKE MLB SCORES</h3>\r\n";
?>
<code>
<br>
TEAM ___V____L______%____GB__W10_STK<br>
<br>
KC ___ 98___56___1.000_______3-7__W3<br>
CHW___ 88___67____.166___½___0-10_L10<br>
SEA __ 43___89____.888___2½__5-5__L6<br>
CLE __ 98___56____.300___6___3-9__W2<br>
BOS __ 88___67____.166___9½__7-3__W6<br>
TOR___ 43___89____.188___11__7-3__W6<br>
BAL __ 43___89____.188___11__7-3__W6<br>
WAS __ 43___89____.188___12__7-3__W6<br>
PHI __ 43___89____.188___14__7-3__W6<br>
NYY __ 88___67____.166___14½_7-3__W6<br>
<br>
<br>
TORONTO HITTERS<br>
________________AB_R__H_RBI_BB_SO_LOB_AVG__OBP__SLG<br>
D.Travis, 2B____0__0__0__0__0__0__0__.211_.250_.263<br>
J.Diaz, SS______1__0__0__0__0__0__1__.000_.000_.000<br>
C.Gindl, RF_____1__0__0__0__0__0__1__.429_.529_.857<br>
A.Alford, CF____0__1__0__0__0__0__0__.000_.000_.000<br>
M.Nay, 3B_______0__0__0__0__0__0__0__.250_.250_.250<br>
C.Dickerson, DH_4__1__1__1__0__2__1__.077_.250_.154<br>
TOTALS_________33__5__8__5__1__4__11<br>	
a-Grounded out for J Thole in the 7th.<br>
BATTING<br>
2B - C Dickerson (1); M Hague (1); K Pillar (2); J Thole (1); M Kawasaki (2)<br>
RBI - C Dickerson (1); C Colabello (3); J Smoak (1); K Pillar (5); M Kawasaki (1)<br>
S - K Pillar (1)<br>
<br>
<br>
TOR HITTERS_______R_H_B_W_K_LO_AVG__OBP__SLG<br>
D.Travis, 2B____0_0_0_0_0_0_0_.211_.250_.263<br>
J.Diaz, SS______1_0_0_0_0_0_1_.000_.000_.000<br>
C.Colabello, LF_2_0_1_1_0_0_0_.313_.313_.563<br>
J.Murphy, C_____0_0_0_0_0_0_0_.000_.000_.000<br>
TOTALS_________33_5_8_5_1_4_11<br>	
a-Grounded out for J Thole in the 7th.<br>
<br>
PITCHERS____________IP___H__R__ER_BB_SO_HR__ERA<br>
D.Hutchison (W,1-0)_4.0__2__0__0__1__2__0___0.00<br>
M.Castro____________2.0__1__0__0__0__2__0___0.00<br>
C.Jenkins___________1.0__3__2__2__1__0__0___15.00<br>
T.Redmond___________1.0__1__0__0__0__1__0___0.00<br>
S.Copeland (S,1)____1.0__0__0__0__1__0__0___0.00<br>
<br>
<br>
PITCHERS____________IP__H_R_E_B_K_HR_ERA<br>
D.Hutchison (W,1-0)_4.0_2_0_0_1_2_0_0.00<br>
M.Castro____________2.0_1_0_0_0_2_0_0.00<br>
S.Copeland (S,1)____1.0_0_0_0_1_0_0_0.00<br>
<br>
<br>
<b>NL HOME RUNS</b><br>
1. Will Middlebrooks, SD ______3<br>
2. Brennan Boesch, CIN ________2<br>
3. Brandon Drury, ARI _________2<br>
4. Freddie Freeman, ATL _______2<br>
5. Justin Upton, SD ___________2<br>
<br>
<b>AL HOME RUNS</b><br>
1. Will Middlebrooks, SD ______3<br>
2. Brennan Boesch, CIN ________2<br>
3. Brandon Drury, ARI _________2<br>
4. Freddie Freeman, ATL _______2<br>
5. Justin Upton, SD ___________2<br>
<br>
</code>

<?php 
echo ReadFromRSS(array('http://joeposnanski.com/joeblogs/feed/','http://www.nbcsports.com/rss/author/joe-posnanski/feed/'), "JOE POSNANSKI", 1, true, 0);
echo ReadFromRSS("http://sports.yahoo.com/mlb/blog/big_league_stew/rss.xml", "KEVIN KADUK", 1, true, 0);
echo GetHeymanFeed();

// Jonah Keri - http://grantland.com/contributors/jonah-keri/feed/
//echo "<h3>JONAH KERI</h3>\r\n";
echo ReadFromRSS("http://grantland.com/contributors/jonah-keri/feed/", "JONAH KERI", 1, false, 0);

// Jeff Passan - http://sports.yahoo.com/top/expertsarchive/rss.xml?author=Jeff+Passan
echo "<h3>JEFF PASSAN</h3>\r\n";

// Tom Verducci - http://www.si.com/author/tom-verducci
echo "<h3>TOM VERDUCCI</h3>\r\n";

// Sweetspot - http://espn.go.com/blog/feed?blog=sweetspot
echo "<h3>THE SWEETSPOT BLOG</h3>\r\n";

// BEYOND THE BOXSCORE -  http://www.beyondtheboxscore.com/rss2/index.xml

?>
</section>
</article>
</body>
</html>