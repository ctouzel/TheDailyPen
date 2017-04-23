<?php


function GetMLBWildcardStandings()
{
    echo MARGIN4."<div id=\"mlbstandings\">".PHP_EOL;
    echo MARGIN5."<h2>WILDCARD STANDINGS</h2>".PHP_EOL;
	$standings = Array();

	libxml_use_internal_errors(true);
	try
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, URL_WILDCARD_STANDINGS);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		$output = curl_exec($ch);
		$doc = new DOMDocument();
		$doc->loadHTML($output);
		$content = $doc->getElementById('pageRow');
		$tables = $content ->getElementsByTagName('table');
		$count = 0;

		foreach($tables as $table)
		{
			if ($count == 2 || $count == 3)
			{
				$standings_division = Array();
				$rows = $table->getElementsByTagName('tr');
				foreach ($rows as $row)
				{
					$standings_team = Array();
					$cols = $row->getElementsByTagName('td');
					foreach ($cols as $col)
					{
						$raw = $col->C14N();
						$rawval = str_replace('<td colspan="15">', '', $raw);
                        $rawval = str_replace('<td colspan="5">', '', $raw);
						$rawval = str_replace('<td align="left" width="20%">', '', $rawval);
						$rawval = str_replace('<td align="left">', '', $rawval);
						$rawval = str_replace('<td align="right" width="6%">', '', $rawval);
                        $rawval = str_replace('<td align="right">', '', $rawval);
						$rawval = str_replace('<td align="right" width="5%">', '', $rawval);
						$rawval = str_replace('<td>', '', $rawval);
						$rawval = str_replace('</td>', '', $rawval);
						array_push($standings_team, $rawval);
					}
					array_push($standings_division, $standings_team);
				}
				array_push($standings, $standings_division);
			}
			$count = $count + 1;
		}
		
		echo MARGIN5."<table style='width:400px'>".PHP_EOL;
		foreach ($standings as $division)
		{
            $count = 0;
			foreach ($division as $team)
			{
				if ($count<7)
				{
					echo MARGIN6."<tr>";
					foreach($team as $col)
					{
						$result = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $col);
						if (strpos($result, 'League') !== false)
						{
							echo "<td><b>".$result."</b></td>";
						}
						else
						{
							echo "<td>".$result."</td>";
						}
					}
					echo "</tr>".PHP_EOL;
				}
				$count = $count + 1;
			}
            echo "<tr><td>&nbsp;</td></tr>";
		}
		echo MARGIN5."</table>".PHP_EOL;
	}
	catch (Exception $e)
	{
        ManageException($e, "Unable to get MLB Standings");
	}
	echo MARGIN4."</div>".PHP_EOL.PHP_EOL;
}

function GetMLBStandings()
{
    echo MARGIN4."<div id=\"mlbstandings\">".PHP_EOL;
    echo MARGIN5."<h2>MLB STANDINGS</h2>".PHP_EOL;
	$standings = Array();

	libxml_use_internal_errors(true);
	try
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, URL_STANDINGS);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		$output = curl_exec($ch);
		$doc = new DOMDocument();
		$doc->loadHTML($output);
		$content = $doc->getElementById('pageRow');
		$tables = $content ->getElementsByTagName('table');
		$count = 0;

		foreach($tables as $table)
		{
			if ($count !== 0 && $count !== 4)
			{
				$standings_division = Array();
				$rows = $table->getElementsByTagName('tr');
				foreach ($rows as $row)
				{
					$standings_team = Array();
					$cols = $row->getElementsByTagName('td');
					foreach ($cols as $col)
					{
						$raw = $col->C14N();
						$rawval = str_replace('<td colspan="15">', '', $raw);
						$rawval = str_replace('<td align="left" width="20%">', '', $rawval);
						$rawval = str_replace('<td align="left">', '', $rawval);
						$rawval = str_replace('<td align="right" width="6%">', '', $rawval);
						$rawval = str_replace('<td align="right" width="5%">', '', $rawval);
						$rawval = str_replace('<td>', '', $rawval);
						$rawval = str_replace('</td>', '', $rawval);
						array_push($standings_team, $rawval);
					}
					array_push($standings_division, $standings_team);
				}
				array_push($standings, $standings_division);
			}
			$count = $count + 1;
		}
		$count = 0;
		echo MARGIN5.'<table style="width:800px">'.PHP_EOL;
		foreach ($standings as $division)
		{
			foreach ($division as $team)
			{
				echo MARGIN6."<tr>";
				$column = 0;
				foreach($team as $col)
				{
					if ($column != 12)
					{
						$result = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $col);
						if (strpos($result, 'League') !== false)
						{
							echo "<td><b>".$result."</b></td>";
						}
						else
						{
							echo "<td>".$result."</td>";
						}
						
					}
					$column = $column + 1;
                }
                echo "</tr>".PHP_EOL;
			}
			if ($count > 7)
			{
				break;
			}
			$count = $count + 1;
                        echo "<tr><td>&nbsp;</td></tr>";
		}
		echo MARGIN5."</table>".PHP_EOL;
	}
	catch (Exception $e)
	{
        ManageException($e, "Unable to get MLB Standings");
	}
	echo MARGIN4."</div>".PHP_EOL.PHP_EOL;
}

function GetSpecificStatsData($URL, $COLNUM)
{
	$STATS_URL = $URL.$COLNUM;
	$stats = Array();
	libxml_use_internal_errors(true);
	try
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $STATS_URL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		$output = curl_exec($ch);
		$doc = new DOMDocument();
		$doc->loadHTML($output);
		$rows = $doc->getElementsByTagName('tr');
		foreach($rows as $row)
		{
			$player_stats = Array();
			$cols = $row->getElementsByTagName('td');
			foreach ($cols as $col)
			{

				$raw = $col->C14N();
				$rawval = str_replace('<td align="left">', '', $raw);
				$rawval = str_replace('<td align="center">', '', $rawval);
				$rawval = str_replace('<td align="right">', '', $rawval);
				$rawval = str_replace('<td align="right" width="6%">', '', $rawval);
				$rawval = str_replace('<td align="right" width="5%">', '', $rawval);
				$rawval = str_replace('</a>', '', $rawval);
				$rawval = str_replace('<td>', '', $rawval);
				$rawval = str_replace('</td>', '', $rawval);
				$pos = stripos($rawval,">");
				$desc = substr($rawval,$pos+1);
				array_push($player_stats, $desc);
			}
			array_push($stats, $player_stats);
		}
		return $stats;
	}
	catch (Exception $e)
	{
		ManageException($e, "Unable to get specific stats data");
        throw $e;
	}
}

function GetSpecificStats($LEAGUE, $COLNUM)
{
    $StatYear = date("Y");
    if(strtotime('now') < mktime(0,0,0,4,1, $StatYear))
    {
        $StatYear = $StatYear - 1;
    }
	$URL = "http://www.cbssports.com/mlb/stats/playersort/sortableTable/".$LEAGUE."/year-". $StatYear."-season-regularseason-category-batting-qualifying-1?:sort_col=";
	try
	{
		$stats = GetSpecificStatsData($URL, $COLNUM);
		return $stats;
	}
	catch (Exception $e)
	{
        ManageException($e, "Unable to get specific stats");
        throw $e;
	}
}

function GetSpecificPitchingStats($LEAGUE, $COLNUM)
{
    $StatYear = date("Y");
    if(strtotime('now') < mktime(0,0,0,4,1, $StatYear))
    {
        $StatYear = $StatYear - 1;
    }
	$URL = "http://www.cbssports.com/mlb/stats/playersort/sortableTable/".$LEAGUE."/year-". $StatYear."-season-regularseason-category-pitching-qualifying-1?:sort_col=";
	try
	{
		$stats = GetSpecificStatsData($URL, $COLNUM);
		return $stats;
	}
	catch (Exception $e)
	{
        ManageException($e, "Unable to get specific pitching stats");
        throw $e;
	}
}

function FormatDoubleStats($HEADER, $stats1, $stats2, $COLNUM)
{
    try
    {
        $count = 0;
        echo MARGIN6."<tr><td colspan=7><b>".$HEADER."</b></td></tr>".PHP_EOL;
        foreach ($stats1 as $stat)
        {
            if ($count > 0)
            {
                echo MARGIN6."<tr>";
                $idx = 0;
                foreach($stat as $col)
                {
                    if ($idx == 0 || $idx == 2 || $idx == ($COLNUM-1))
                    {
                        echo "<td>".$col."</td>";
                    }
                    $idx = $idx + 1;
                }
                $idx = 0;
                foreach($stats2[$count] as $col)
                {
                    if ($idx == 0 || $idx == 2 || $idx == ($COLNUM-1))
                    {
                        echo "<td>".$col."</td>";
                    }
                    $idx = $idx + 1;
                }
                echo "</tr>".PHP_EOL;
            }
            if ($count > 7)
            {
                break;
            }
            $count = $count + 1;
        }
        echo MARGIN6."<tr><td colspan=7>&nbsp;</td></tr>".PHP_EOL;
    }
    catch (Exception $e)
	{
        ManageException($e, "Unable to format double stats");
        throw $e;
	}

}

function GetTableStats($statsname,$colnum)
{
	$alstats = GetSpecificStats("al", $colnum);
	$nlstats = GetSpecificStats("nl", $colnum);
	FormatDoubleStats($statsname, $alstats, $nlstats, $colnum);
}

function GetPitchingTableStats($statsname,$colnum)
{
	$alstats = GetSpecificPitchingStats("al", $colnum);
	$nlstats = GetSpecificPitchingStats("nl", $colnum);
	FormatDoubleStats($statsname, $alstats, $nlstats, $colnum);
}

// GET MLB STATS
function GetMLBStats()
{
    echo MARGIN4."<div id=\"mlbstats\">".PHP_EOL;
    echo MARGIN5."<h2>MLB STATS</h2>".PHP_EOL;
    try{
        echo MARGIN5."<table>".PHP_EOL;
        GetTableStats("Homeruns", 10);
        GetTableStats("AVG", 4);
        GetTableStats("Doubles", 8);
        GetTableStats("RBI", 11);
        GetTableStats("OBP", 16);
        GetTableStats("SLG", 17);
        GetTableStats("OPS", 18);

        GetPitchingTableStats("W", 7);
        GetPitchingTableStats("ERA", 15);
        GetPitchingTableStats("K", 17);
        GetPitchingTableStats("WHIP", 19);
        echo MARGIN5."</table>".PHP_EOL;
    }
    catch (Exception $e)
	{
        ManageException($e, "Unable to get MLB stats");
	}
    echo MARGIN4."</div>".PHP_EOL.PHP_EOL;
}

// RETURN A VALUE IF THE PROPERTY EXISTS
function GetValueIfExisting($class, $property)
{
    if (property_exists($class, $property))
    {
        return $class->$property;
    }
    else
    {
        return null;
    }
}

// GET MLB SCORES Tables
function GetMLBScoresTables()
{

	libxml_use_internal_errors(true);
	try
	{
		$date_score = new DateTime();
		if (date('H') < 11)
		{
			$date_score->modify('-1 day');
		}
		$MLB_ONLINE_BASE_URL = "http://gd2.mlb.com/components/game/mlb/year_";
		$Scores_URL = $MLB_ONLINE_BASE_URL.$date_score->format('Y');
		$Scores_URL = $Scores_URL."/month_".$date_score->format('m');
		$Scores_URL = $Scores_URL."/day_".$date_score->format('d');
		$Scores_URL = $Scores_URL."/master_scoreboard.json";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $Scores_URL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		$output = curl_exec($ch);

		$parsed_json = json_decode($output);
                if( isset( $parsed_json->{'data'}->{'games'}->{'game'} ) == FALSE)
                {
                    return;
                }    
                echo MARGIN4."<div id=\"mlbscores\">".PHP_EOL;
                echo MARGIN5."<h2>MLB SCORES</h2>".PHP_EOL;
		$games = $parsed_json->{'data'}->{'games'}->{'game'};

		echo MARGIN5."<table>".PHP_EOL;
		foreach($games as $game)
		{

			$lines = array();

			$awaystr = "<b>".$game->{'away_team_city'}."</b> (".$game->{'away_win'}."-".$game->{'away_loss'}.")";
			$homestr = "<b>".$game->{'home_team_city'}."</b> (".$game->{'home_win'}."-".$game->{'home_loss'}.")";

			//$awayscore = $game->{'linescore'}->{'r'}->{'away'};
			//$homescore = $game->{'linescore'}->{'r'}->{'home'};

			$status = $game->{'status'}->{'ind'};
			$statusSTR = $status;
			if ($status == "S" || $status == "P")
			{
				$statusSTR = $game->{'time'};
				$awaypitcher = $game->{'away_probable_pitcher'};
				$homepitcher = $game->{'home_probable_pitcher'};
				array_push($lines, $game->{'away_name_abbrev'}.": ".$awaypitcher->{'first'}." ".$awaypitcher->{'last'}." (".$awaypitcher->{'wins'}."-".$awaypitcher->{'losses'}.", ".$awaypitcher->{'era'}.")");
				array_push($lines, $game->{'home_name_abbrev'}.": ".$homepitcher->{'first'}." ".$homepitcher->{'last'}." (".$homepitcher->{'wins'}."-".$homepitcher->{'losses'}.", ".$homepitcher->{'era'}.")");
				array_push($lines, $game->{'venue'}.", ".$game->{'location'});
			}
			if ($status == "PW")
			{
				$statusSTR = "Warmup";
				$awaypitcher = $game->{'away_probable_pitcher'};
				$homepitcher = $game->{'home_probable_pitcher'};
				array_push($lines, $game->{'away_name_abbrev'}.": ".$awaypitcher->{'first'}." ".$awaypitcher->{'last'}." (".$awaypitcher->{'wins'}."-".$awaypitcher->{'losses'}.", ".$awaypitcher->{'era'}.")");
				array_push($lines, $game->{'home_name_abbrev'}.": ".$homepitcher->{'first'}." ".$homepitcher->{'last'}." (".$homepitcher->{'wins'}."-".$homepitcher->{'losses'}.", ".$homepitcher->{'era'}.")");
				array_push($lines, $game->{'venue'}.", ".$game->{'location'});
			}
			if ($status == "I")
			{
				$statusSTR = "BOT".$game->{'status'}->{'inning'};
				if ($game->{'status'}->{'top_inning'} == "Y")
				{
					$statusSTR = "TOP".$game->{'status'}->{'inning'};
				}
				$pitcher = $game->{'pitcher'};
				$batter = $game->{'batter'};
				$outs = $game->{'status'}->{'o'};
				$outsline = $outs." outs";
				if ($outs == "0")
				{
					$outsline = "No outs";
				}
				else if ($outs == "1")
				{
					$outsline = "One out";
				}
				$runners = $game->{'runners_on_base'}->{'status'};
				$runnersStr = "Nobody on base. ";
				if ($runners !== "0")
				{
                    $runner1b = GetValueIfExisting($game->{'runners_on_base'}, 'runner_on_1b');
                    $runner2b = GetValueIfExisting($game->{'runners_on_base'}, 'runner_on_2b');
                    $runner3b = GetValueIfExisting($game->{'runners_on_base'}, 'runner_on_3b');

					$runnersStr = "";
					if (is_null($runner1b)==False && $runner1b->{'last'}!=="")
					{
						$runnersStr = $runner1b->{'last'}." on first. ";
					}
					if (is_null($runner2b)==False && $runner2b->{'last'}!=="")
					{
						$runnersStr = $runnersStr.$runner2b->{'last'}." on second. ";
					}
					if (is_null($runner3b)==False && $runner3b->{'last'}!=="")
					{
						$runnersStr = $runnersStr = $runnersStr.$runner3b->{'last'}." on third. ";
					}
				}

				$current = $runnersStr.$outsline;
				$atbat = "Batter: ". $batter->{'first'}." ".$batter->{'last'}." (".$batter->{'h'}."/".$batter->{'ab'}.", ".$batter->{'avg'}.")";
				$pitching = "Pitching: ". $pitcher->{'first'}." ".$pitcher->{'last'}. " (".$pitcher->{'ip'}." IP, ".$pitcher->{'er'}." ER, ".$pitcher->{'era'}.")";
				array_push($lines, $current);
				array_push($lines, $atbat);
				array_push($lines, $pitching);
			}

            $linescore = GetValueIfExisting($game, 'linescore');
            if (is_null($linescore) == false)
            {
                echo MARGIN6."<tr>";
                echo "<td>".$awaystr."</td>";
                echo "<td>".$game->{'linescore'}->{'r'}->{'away'}."</td>";
                echo "<td>".$game->{'linescore'}->{'h'}->{'away'}."</td>";
                echo "<td>".$game->{'linescore'}->{'e'}->{'away'}."</td>";
                echo "<td>".$statusSTR."</td>";
                echo "</tr>".PHP_EOL;

                echo MARGIN6."<tr>";
                echo "<td>".$homestr."</td>";
                echo "<td>".$game->{'linescore'}->{'r'}->{'home'}."</td>";
                echo "<td>".$game->{'linescore'}->{'h'}->{'home'}."</td>";
                echo "<td>".$game->{'linescore'}->{'e'}->{'home'}."</td>";
                echo "<td>&nbsp;</td>";
                echo "</tr>".PHP_EOL;
            }
            else
            {
                echo MARGIN6."<tr>";
                echo "<td>".$awaystr."</td>";
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
                echo "<td>".$statusSTR."</td>";
                echo "</tr>".PHP_EOL;

                echo MARGIN6."<tr>";
                echo "<td>".$homestr."</td>";
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
                echo "</tr>".PHP_EOL;
            }

			if ($status=="F")
			{
				$line3 = "W: ".$game->{'winning_pitcher'}->{'first'}." ";
				$line3 = $line3.$game->{'winning_pitcher'}->{'last'}." (";
				$line3 = $line3.$game->{'winning_pitcher'}->{'wins'}."-";
				$line3 = $line3.$game->{'winning_pitcher'}->{'losses'}.", ";
				$line3 = $line3.$game->{'winning_pitcher'}->{'era'}.")";

				$line4 = "L: ".$game->{'losing_pitcher'}->{'first'}." ";
				$line4 = $line4.$game->{'losing_pitcher'}->{'last'}." (";
				$line4 = $line4.$game->{'losing_pitcher'}->{'wins'}."-";
				$line4 = $line4.$game->{'losing_pitcher'}->{'losses'}.", ";
				$line4 = $line4.$game->{'losing_pitcher'}->{'era'}.")";
				array_push($lines, $line3);
				array_push($lines, $line4);
				if ($game->{'save_pitcher'}->{'first'}!=="")
				{
					$line5 = "S: ".$game->{'save_pitcher'}->{'first'}." ";
					$line5 = $line5.$game->{'save_pitcher'}->{'last'}." (";
					$line5 = $line5.$game->{'save_pitcher'}->{'saves'}.", ";
					$line5 = $line5.$game->{'save_pitcher'}->{'era'}.")";
					array_push($lines, $line5);
				}
			}

			// HOME RUNS
			if ($status !== "S" && $status !== "P" && $status !== "PW")
			{
				$line6 = "HR: ";
				$first = "0";
                $homeruns = GetValueIfExisting($game, 'home_runs');
				if (is_null($homeruns) ==  False)
				{
					$players = $homeruns->{'player'};
					if (is_array($players) == True)
					{
						foreach ($players as $hr)
						{
							if ($first == "0")
							{
								$first = "1";
							}
							else
							{
								$line6 = $line6 . ", ";
							}
							$line6 = $line6 . $hr->{'name_display_roster'}. " (".$hr->{'std_hr'}.")";
						}
					}
					else
					{
						$line6 = $line6 . $players->{'name_display_roster'}. " (".$players->{'std_hr'}.")";
					}
				}
				array_push($lines, $line6);
			}
			foreach ($lines as $value)
			{
				echo MARGIN6."<tr>";
				echo "<td colspan=5>".$value."</td>";
				echo "</tr>".PHP_EOL;
			}
			echo MARGIN6."<tr><td colspan=5>&nbsp;</td></tr>".PHP_EOL;
		}
		echo MARGIN5."</table>".PHP_EOL;
	}
	catch (Exception $e)
	{
        ManageException($e, "Unable to get mlb scores");
	}
    echo MARGIN4."</div>".PHP_EOL.PHP_EOL;
}

?>