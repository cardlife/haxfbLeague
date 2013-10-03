<?php

class Leadersdisp {
    /** @var  wpdb $db */
    private $db;


    public function __construct() {
        global $wpdb;
        $this->db = &$wpdb;
        //Util::updatePageUrl(Constants::PAGE_LEADERS_DISP, get_permalink());
        $this->main();

    }

    public function main() {
        /** @view Stats $statsInstance */
        $statsInstance = Stats::getInstance();
        $viewList = $statsInstance->getViewList();
        $standardView = $statsInstance->getRecordedView();

        print("<body onLoad = \"hideAllDivs('statView'); toggleVisibility('{$standardView->getDisplayName()}','statView'); \">");
        print("<div id = 'leagueContent'>");

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
                $this->dispTeamLeaders($view);
                print("<br><br>");
                $this->dispPlayerLeaders($view);
                print("<br><br>");
                print("</div>");
            }
//			Util::printNav();

        print("</div>");
    }


    public function dispTeamLeaders($view) {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", ".$stat->getSelectPartWithLabel();
        }

        $queryString = "Select team.id as teamid, team.name as teamname {$statQuery} from
        team left join stats on (team.id = stats.teamid)
		group by team.id";


        $teamTotalStats = $this->db->get_results($queryString, ARRAY_A) or print("Error Retrieving Team Totals! <br>");
        print("<h2> Team Leaders:</h2> <br>");
        Util::dispStats($teamTotalStats, $view);

    }


    public function dispPlayerLeaders($view) {
        /** @var View $view */
        $dummyStats = $view->getStatList();
        $statQuery = "";
        /**
         * @var Statistic $stat
         */
        foreach ($dummyStats as $stat) {
            $statQuery .= ", ".$stat->getSelectPartWithLabel();
        }

        $query = "Select player.id as playerid, player.username as playername {$statQuery}
                                from player left join stats on (player.id = stats.playerid)
                                where player.username != '' group by player.id";
        $teamStatList =  $this->db->get_results($query, ARRAY_A)
        or print("Error retrieving player stats! <br>");
        print("<h2> Player Leaders:</h2> <br>");
        Util::dispStats($teamStatList, $view, true);
    }
}

add_shortcode("display_leaders", "display_leaders");

function display_leaders() {
    ob_start();
    new Leadersdisp();
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}