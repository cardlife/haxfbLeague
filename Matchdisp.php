<?php
/**
 * Created by IntelliJ IDEA.
 * User: Night
 * Date: 9/11/13
 * Time: 9:07 PM
 * To change this template use File | Settings | File Templates.
 */

class Matchdisp {
    /** @var  wpdb $db */
    private $db;

    /** @var Match $match */
    private $matchId = null;

    public function __construct($matchId = null) {
        global $wpdb;
        $this->db = &$wpdb;

        Util::updatePageUrl(Constants::PAGE_MATCH_DISP, get_permalink());

        if(null != $matchId) {
            $this->matchId = $matchId;
        } else if(isset($_GET['mId'])){
            $this->matchId = $_GET['mId'];
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
        if(null != $this->matchId) {

            $this->dispMatchInfo($this->matchId);
            print("<br> <br> ");
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
                $this->dispTeamTotals($this->matchId, $view);
                print("<br><br>");
                $this->dispTeam($this->matchId, $view);
                print("<br><br>");
                print("</div>");
            }
        }
        print("</div>");

    }

    public function dispMatchInfo($matchId) {
       $match = new Matchup($matchId);
       print("<table><thead><th>Week</th><th>Matchup</th><th>Date</th><th>Score</th></thead>") ;
        print("<tr>");
        print("<td>{$match->getWeek()}</td>");
        print("<td>{$match->getTeam1()->getNameWithLink()} vs {$match->getTeam2()->getNameWithLink()}</td>");
        print("<td>{$match->getDate()}</td>");
        print("<td>{$match->getResultWithLink()}</td>");
        print("</tr> </table>");
    }

    public function dispTeamTotals($matchId, $view) {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", ".$stat->getSelectPartWithLabel();
        }

        $queryString = $this->db->prepare("Select stats.teamid as teamid, team.name as teamname {$statQuery} from stats, player, team
		where stats.matchupid = %d and stats.playerid = player.id and stats.teamid = team.id group by stats.teamid", $matchId);


        $teamTotalStats = $this->db->get_results($queryString, ARRAY_A) or print("Error Retrieving Team Totals! <br>");
        print("<h3> Team Totals: </h3> <br>");
        Util::dispStats($teamTotalStats, $view);

    }


    public function dispTeam($matchId, $view) {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", ".$stat->getSelectPartWithLabel();
        }

        $match = new Matchup($matchId);
        for($i=0; $i < Constants::NUM_TEAMS; $i++) {
            $team =  $match->getTeam($i);
            $query = $this->db->prepare("Select stats.playerid as playerid, player.name as playername {$statQuery}
                                from stats, player where stats.matchupid = %d and stats.teamid = %d
                                and stats.playerid = player.id group by stats.playerid", $matchId, $team->getId());
            $teamStatList =  $this->db->get_results($query, ARRAY_A)
            or print("Error retrieving team stats! <br>");

            print("<h3>{$team->getName()}</h3><br>");
            Util::dispStats($teamStatList, $view);
            print("<br><br>");
        }
    }

}

add_shortcode("display_match", "display_match");

function display_match($args = null) {
    $id = null;
    if(isset($args["id"])) {
        $id = $args["id"];
    }
    ob_start();
    new Matchdisp($id);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
