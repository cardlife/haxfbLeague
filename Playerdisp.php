<?php

require_once('Schedule.php');

class Playerdisp
{
    /** @var  wpdb $db */
    private $db;

    /** @var Match $match */
    private $playerId = null;

    public function __construct($playerId = null)
    {
        global $wpdb;
        $this->db = & $wpdb;

        Util::updatePageUrl(Constants::PAGE_PLAYER_DISP, get_permalink());

        if (null != $playerId) {
            $this->playerId = $playerId;
        } else if (isset($_GET['pId'])) {
            $this->playerId = $_GET['pId'];
        }
        $this->main();
    }

    public function main()
    {
        $statsInstance = Stats::getInstance();
        $viewList = $statsInstance->getViewList();
        $standardView = $statsInstance->getRecordedView();

        print("<body onLoad = \"hideAllDivs('statView'); toggleVisibility('{$standardView->getDisplayName()}','statView');  \">");
        print("<div id = 'leagueContent'>");
        if (null != $this->playerId) {


            print("<div id = 'viewTabs'>");
            print("<table> <thead>");
            //display a toggle for each item
            /** @var View $view */
            foreach ($viewList as $view) {
                print("<th> <a onclick=\"toggleVisibility('{$view->getDisplayName()}', 'statView')\"
                        class = \"statMenu\"> {$view->getDisplayName()} </a> </th>");
            }

            print("</thead></table>");
            print("</div>");

            $player = new Player($this->playerId);
            print("<h2>{$player->getName()}:</h2> <br>");


            /** @var View $view */
            foreach ($viewList as $view) {
                print("<div id='{$view->getDisplayName()}' class= 'statView'>");
                $this->dispPlayerTotals($this->playerId, $view);
                print("<br><br>");
                $this->dispPlayer($this->playerId, $view);
                print("<br><br>");
                print("</div>");
            }
            print("</div>");

        }
    }

    public function dispPlayerTotals($playerId, $view)
    {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", " . $stat->getSelectPartWithLabel();
        }

        $queryString = $this->db->prepare("Select player.id as playerid, player.name as playername {$statQuery}
          from player left join stats on (stats.playerid = player.id), team
		  where player.id = %d and player.teamid = team.id group by player.id", $playerId);


        $teamTotalStats = $this->db->get_results($queryString, ARRAY_A) or print("Error Retrieving Player Totals! <br>");
        print("<h3> Player Totals: <br></h3>");
        Util::dispStats($teamTotalStats, $view);

    }


    public function dispPlayer($playerId, $view)
    {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", " . $stat->getSelectPartWithLabel();
        }

        $query = $this->db->prepare("Select IFNULL(stats.matchupid,0) as matchid {$statQuery}
                                from player left join stats on (stats.playerid = player.id) where player.id = %d
            group by matchid ", $this->playerId);
        $playerStatList = $this->db->get_results($query, ARRAY_A)
        or print("Error retrieving player stats! <br>");

        Util::dispStats($playerStatList, $view);

    }

}

add_shortcode("display_player", "display_player");

function display_player($args = null)
{
    $id = null;
    if (isset($args["id"])) {
        $id = $args["id"];
    }
    ob_start();
    new Playerdisp($id);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}