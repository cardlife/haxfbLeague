<?php


require_once('Matchup.php');
require_once('Team.php');
require_once('Player.php');
require_once('Stats.php');
require_once('Util.php');
//print('<head>');
//print('<link rel="stylesheet" type="text/css" href="woodenly/default.css"/>');
//print('</head> <body>');

class Schedule {
	/** @var  wpdb $db */
    private $db;

    private $week;

    private $schedulePage;

    public function __construct($curWeek = null) {
		global $wpdb;
        $this->db = &$wpdb;
        $this->schedulePage = Util::getCurrentPageUrl();
        if(null != $curWeek) {
            $this->main($curWeek);
        }
    }

    public function main($curWeek)
    {
        print('<div id = "leagueContent">');

        print('<div id = "tabs">');
        $this->displayWeekList();
        print('</div>');
        if (isset($_GET['week'])) {
            $this->displayWeek($_GET['week']);
        } else {
            if (null == $curWeek) {
                $curWeek = Util::getCurrWeek();
            }
            $this->displayWeek($curWeek);
        }
        print('</div>');

        //Config::printNav();
    }
	
	public function displayWeek($curWeek) {
		//$curWeek = $arr['week'];
		print("<div id = 'weekTitle'>");
		print("<h3> Week {$curWeek} </h3>");
		print("</div>");
		$matchQuery = $this->db->prepare("Select * from matchup where week = %d", $curWeek);
		$matchList = $this->db->get_results($matchQuery, ARRAY_A);
        $this->displayMatchList($matchList);
	}
	
	public function displayWeekList() {
		$maxWeek = $this->db->get_var("Select MAX(week) as maxweek from matchup") or print("Error Getting week count!");
		print('<div id = "menu">');
		print('<table><thead>');
		for($i=1; $i <= $maxWeek; $i++) {
            $url = Util::buildCurrentURL(array("displayWeek" => "true", "week" => $i));
			print("<th><a href = \"$url\">Week {$i}</a></th>");
		}
		print('</thead></table></div>');
	}
	
	public function displayTeamList($teamId) {
		//$teamId = $arr['teamId'];
		$query = $this->db->prepare("Select * from matchup where team1id = %d OR team2id = %d",$teamId, $teamId);
		$matchList = $this->db->get_results($query, ARRAY_A);
        $this->displayMatchList($matchList);
	}
	
	public function displayPlayerMatches($playerId) {
		//$playerId = $arr['playerId'];
		$query = $this->db->prepare("SELECT matchup.* FROM stats, matchup WHERE stats.playerid = %s and stats.matchupid = matchup.id", $playerId);
		$matchList = $this->db->get_results($query, ARRAY_A);
        $this->displayMatchList($matchList);
	}
	
	
	public function displayMatchList($matchList) {
		echo('<div class="matchList"> </div><table>');
		echo('<thead><th>Team1</th><th>Team2</th><th>Result</th></thead>');
		foreach($matchList as $row) {
			echo('<tr>');
			$m = new Matchup($row);
			echo("<td>");
			echo($m->getTeam1()->getNameWithLink());
			echo("</td>");
			echo("<td>");
			echo($m->getTeam2()->getNameWithLink());
			echo("</td>");
			echo("<td>");
			if($row['gameplayed'] == '1') { 
				echo($m->getResultWithLink());
			} else {
				echo("N/A");
			}
			echo("</td>");
			echo("</tr>");
		}
		echo("</table></div>");
	}
}
//new Schedule();
//print('</body>');
add_shortcode("display_schedule", "display_schedule");

function display_schedule($args = null) {
    $week = null;
    if(isset($args["week"])) {
        $week = $args["week"];
    }
    ob_start();
    new Schedule($week);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
