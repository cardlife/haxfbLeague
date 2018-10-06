<?php

require_once('Schedule.php');
require_once('Util.php');


class Teamdisp {

    /** @var  wpdb $db */
    private $db;

    private $teamId = null;

	public function __construct($teamId = null) {
		global $wpdb;
        $this->db = &$wpdb;
        Util::updatePageUrl(Constants::PAGE_TEAM_DISP.$teamId, get_permalink());
        if(null != $teamId) {
           $this->teamId = $teamId;
        } else if(isset($_GET['tId'])){
           $this->teamId = $_GET['tId'];
        }
        $this->main();

	}
	
	public function main() {
        /** @view Stats $statsInstance */
        $statsInstance = Stats::getInstance();
        $viewList = $statsInstance->getViewList();
        $standardView = $statsInstance->getRecordedView();

        print("<body onLoad = \"hideAllDivs('statView'); toggleVisibility('{$standardView->getDisplayName()}','statView'); \">");
        print("<div id = 'leagueContent'>");
		if(null != $this->teamId) {

            print("<div id = 'viewTabs'>");
            print("<table> <thead>");
            //display a toggle for each item
            /** @var View $view */
            foreach($viewList as $view) {
                print("<th> <a onclick=\"toggleVisibility('{$view->getDisplayName()}', 'statView')\"
                        class = \"statMenu\"> {$view->getDisplayName()} </a> </th>");
            }

            print("</thead></table>");
            print("</div>");


            /** @var View $view */
            foreach($viewList as $view) {
                print("<div id='{$view->getDisplayName()}' class= 'statView'>");
                $this->dispTeamTotals($this->teamId, $view);
			    print("<br><br>");
			    $this->dispTeam($this->teamId, $view);
			    print("<br><br>");
                print("</div>");
            }
			$this->dispTeamSched($this->teamId);
//			Util::printNav();
		}
        print("</div>");
    }
	
	public function dispTeamSched($teamId) {
		$s = new Schedule();
		$s->displayTeamList($teamId);
	}
	public function dispTeamTotals($teamId, $view) {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", ".$stat->getSelectPartWithLabel();
        }

        $queryString = $this->db->prepare("Select team.id as teamid, team.name as teamname {$statQuery} from
        team left join stats on (team.id = stats.teamid)
		where team.id = %d group by team.id", $teamId);


        $teamTotalStats = $this->db->get_results($queryString, ARRAY_A) or print("Error Retrieving Team Totals! <br>");
		print("<h2> Team Totals:</h2> <br>");
		Util::dispStats($teamTotalStats, $view);

	}
	
	
	public function dispTeam($teamId, $view) {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", ".$stat->getSelectPartWithLabel();
        }

        $query = $this->db->prepare("Select player.id as playerid, player.name as playername {$statQuery}
                                from player left join stats on (player.id = stats.playerid) where player.teamid = %d  group by player.id", $teamId);
        $teamStatList =  $this->db->get_results($query, ARRAY_A)
		or print("Error retrieving team stats! <br>");
		$teamName = $this->db->get_var($this->db->prepare("Select name from team where id = %d", $teamId));
		print("<h2>{$teamName}</h2><br>");
		Util::dispStats($teamStatList, $view);
	}


}
//new Teamdisp();

add_shortcode("display_team", "display_team");

function display_team($args = null) {
    $id = null;
    if(isset($args["id"])) {
        $id = $args["id"];
    }
    ob_start();
    new Teamdisp($id);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
