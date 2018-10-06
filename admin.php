<?php
//print('<title> League </title>');
//print('<head>');
//including other files, including ajax
require_once('Team.php');
require_once('Player.php');
require_once('Matchup.php');
require_once('Stats.php');
require_once('Beans/Constants.php');
//print('<script type="text/javascript" src="./ajax.js"></script>');
//print('</head>');
class Admin
{

    private $adminPage;

    //constructor
    public function __construct()
    {
        $this->adminPage = Util::getCurrentPageUrl();
    }

    public function ajaxRequests()
    {
        if (isset($_POST['editTeamSelect'])) {
            //A team was selected to edit...
            $this->displayEditTeamNewName($_POST['tId']);
            return true;
        }

        if (isset($_POST['playerRemove'])) {
            //remove a player
            $this->displayRemovePlayer($_POST['tId']);
            return true;
        }
        if (isset($_POST['playerEdit'])) {
            //edit a player
            $this->displayEditPlayerSelect($_POST['tId']);
            return true;
        }
        if (isset($_POST['editPlayerValues'])) {
            //Show player edit values
            $this->displayEditPlayer($_POST['pId']);
            return true;
        }
        return null;
    }

    //main function
    public function main()
    {
        if (isset($_POST['addTeam'])) {
            //add a team
            $this->addTeam($_POST);
        }
        if (isset($_POST['addPlayer'])) {
            //add a player
            $this->addPlayer($_POST);
        }
        if (isset($_POST['editTeam'])) {
            //edit a team
            $this->editTeam($_POST);
        }
        if (isset($_POST['editPlayer'])) {
            //remove a player
            $this->editPlayer($_POST);
        }
        if (isset($_POST['removePlayer'])) {
            //remove a player
            $this->removePlayer($_POST);
        }
        if (isset($_POST['removeTeam'])) {
            //remove a team
            $this->removeTeam($_POST);
        }


        if (isset($_POST['addMatch'])) {
            //add a match
            $this->addMatch($_POST);
        }
        if (isset($_POST['editMatch'])) {
            //edit a match
            $this->editMatch($_POST);
        }
        if (isset($_POST['editMatchList'])) {
            //Edit matchup
            return $this->displayEditMatch($_POST['matchupId']);
        }
        if (isset($_POST['removeMatchup'])) {
            //Remove matchup
            $this->removeMatchup($_POST);
        }
        if (isset($_POST['displayAddStats'])) {
            //add stats to a match
            $this->displayAddStats($_POST);
            return null;
        }
        if (isset($_POST['displayEditStats'])) {
            //edit stats to a match
            $this->displayEditStats($_POST);
            return null;
        }
        if (isset($_POST['addStats'])) {
            //add new stats
            $this->addStats($_POST);
        }
        if (isset($_POST['editStats'])) {
            //update a match's stats
            $this->editStats($_POST);
        }

        //hiding functions
        print("<body onLoad = \"hideAllDivs('dispForm')\">");
        //print("<div id = 'wrapper'>");
        //print("<div id = 'header'> <div id = 'Logo'>");
        print("<div id = 'title'>");
        print("Admin Page! </div><br><br>");
        //Print the navigation menu:
//		Util::printNav();

        print("<div id = 'tabs'>");
        print("<table> <tr>");
        //display a toggle for each item
        print("<td> <a onclick=\"toggleVisibility('addTeam', 'dispForm')\" class = \"adminMenu\"> Add Team </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('addPlayer', 'dispForm')\" class = \"adminMenu\"> Add Player </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('editTeam', 'dispForm')\" class = \"adminMenu\"> Edit Team </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('editPlayerTeam', 'dispForm')\" class = \"adminMenu\"> Edit Player </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('removePlayerTeam', 'dispForm')\" class = \"adminMenu\"> Remove Player </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('removeTeam', 'dispForm')\" class = \"adminMenu\"> Remove Team </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('addMatch', 'dispForm')\" class = \"adminMenu\"> Add Matchup </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('editMatchList', 'dispForm')\" class = \"adminMenu\"> Edit Matchup </a> </td>");
        print("<td> <a onclick=\"toggleVisibility('removeMatchup', 'dispForm')\" class = \"adminMenu\"> Remove Matchup </a> </td>");
        print("</tr></table>");
        print("</div>");

        //print("<div id = 'page'>");
        //print("<div id = 'page-bgtop'>");
        print("<div id = 'leagueContent'>");
        //display all forms for the user interface
        $this->displayAddTeam();
        $this->displayAddPlayer();
        $this->displayEditTeam();
        $this->displayEditPlayerTeam();
        $this->displayRemovePlayerTeam();
        $this->displayRemoveTeam();
        $this->displayAddMatch();
        $this->displayEditMatchList();
        $this->displayRemoveMatchup();
        print("</div>");
        //print("</div>");
        //print("</div>");
        //print("</div>");
        //print("</div>");
    }

    //function to add a team
    public function addTeam($arr)
    {
        $team = new Team('', $arr['teamName'], $arr['homePage']);
        $team->addToDb();
    }

    //function to add a player
    public function addPlayer($arr)
    {
        $player = new Player($arr['playerName'], $arr['password'], $arr['email'], $arr['teamId'], '');
        $player->insertToDb();
    }

    //function to edit a team
    public function editTeam($arr)
    {
        $team = new Team($arr['teamId']);
        $team->setName($arr['teamName']);
        $team->setHomePage($arr['teamHomePage']);
        $team->editDb();
    }

    //function to edit a player
    public function editPlayer($arr)
    {
        $player = new Player($arr['playerId']);
        $player->setName($arr['playerName']);
        $player->setTeamid($arr['newTeamId']);
        $player->setAlias($arr['playerAlias']);
        $player->updateDb();
    }

    //function to remove a player
    public function removePlayer($arr)
    {
        $player = new Player($arr['playerId']);
        $player->deleteFromDb();
    }

    //function to remove a team
    public function removeTeam($arr)
    {
        $team = new Team($arr['teamId']);
        $team->removeDb();
    }

    //function to show players in a drop down box
    public function displayDropDownPlayer($teamId = "", $playerId = "")
    {
        echo("<option value = '0'> None </option>");
        //If you want to display all players
        if ($teamId == "") {
            //the list is of all players:
            $playerList = Player::getPlayerList();

        } else {
            $team1 = new Team($teamId);
            $playerList = $team1->getPlayers();
        }

        for ($x = 0; $x < sizeof($playerList); $x++) {
            $pid = $playerList[$x]->getId();
            $pname = $playerList[$x]->getName();
            if ($pid == $playerId) {
                echo("<option value = '$pid' selected = 'true'>$pname</option>");
            } else {
                echo("<option value = '$pid'>$pname</option>");
            }
        }
    }

    //function to display form for adding a team
    public function displayAddTeam()
    {
        echo("<div id = 'addTeam' class = 'dispForm'>");
        echo("<div class = 'title'> Add Team here: </div> <br>");
        //echo("<div class = 'entry'> <p>");
        echo("<form method = 'POST' action = '$this->adminPage'>");
        echo("Team Name: <input type = 'text' name = 'teamName' /> <br>");
        echo("HomePage: <input type = 'text' name = 'homePage' /> <br>");
        echo("<input type = 'submit' name = 'addTeam' value = 'Create Team' /> <br>");
        echo("</form></div>");
    }

    //function to show teams in a drop down box
    public function displayDropDownTeam($defid = 0)
    {
        echo("<option value = '0'> None </option>");
        $teamList = Team::getTeamList();
        foreach ($teamList as $arr) {
            $tid = $arr['id'];
            $tname = $arr['name'];
            if ($tid == $defid) {
                echo("<option value = '$tid' selected = 'true'>$tname</option>");
            } else {
                echo("<option value = '$tid'>$tname</option>");
            }
        }
    }

    //function to display form for adding a player
    public function displayAddPlayer()
    {
        echo("<div id = 'addPlayer' class = 'dispForm'>");
        echo("<div class = 'title'> Add Player here: </div><br>");
        echo("<form method = 'POST' action = '$this->adminPage'>");
        echo("Player Name: <input type = 'text' name = 'playerName' /> <br>");
        echo("Team Name: <select name = 'teamId'>");
        $this->displayDropDownTeam();
        echo("</select> <br>");
        echo("Password: <input type = 'text' name = 'password' /> <br>");
        echo("Email: <input type = 'text' name = 'email' /> <br>");
        echo("<input type = 'submit' name = 'addPlayer' value = 'Add Player!' /> <br>");
        echo("</form></div>");
    }

    //function to display form for editing a team name
    public function displayEditTeamNewName($tid)
    {
        $team1 = new Team($tid);
        $name = $team1->getName();
        $homePage = $team1->getHomePage();
        //echo("<form action = 'admin.php' method = 'post'>");
        echo("Team Id: {$tid} <br>");
        echo("Team Name: <input type = 'text' name = 'teamName' value = '$name'/> <br>");
        echo("Team HomePage: <input type = 'text' name = 'teamHomePage' value='$homePage'/> <br>");
        echo("<input type = 'submit' name = 'editTeam' value = 'Edit Team!' /> <br>");
        //echo("</form>");

    }

    //function to display form for editing a team
    public function displayEditTeam()
    {
        echo("<div id = 'editTeam' class = 'dispForm'>");
        echo("<div class = 'title'> Edit Team Name here: </div> <br>");
        echo("<form method = 'POST' action = '$this->adminPage'>");
        echo("Team Name: <select name = 'teamId' onchange = 'sendAjax(\"$this->adminPage\",\"editTeamSelect=true&tId=\"+this.value, \"instEditTeam\")'>");
        $this->displayDropDownTeam();
        echo("</select> <br>");
        //echo("<input type = 'submit' name = 'editTeamSelect' value = 'Edit Team!' /> <br>");
        echo("<div id = 'instEditTeam'> Team info here...</div>");
        echo("</form></div>");
        /*
        */
    }

    //function to display form for removing a player
    public function displayRemovePlayerTeam()
    {
        $this->displayActionPlayerTeam("removePlayer", "playerRemove");
    }

    public function displayEditPlayerTeam()
    {
        $this->displayActionPlayerTeam("editPlayer", "playerEdit");
    }

    public function displayActionPlayerTeam($action, $displayAction)
    {
        echo("<div id = '{$action}Team' class = 'dispForm'>");
        echo("<div class = 'title'> Update Player: </div> <br>");
        echo("<form method = 'POST' action = '$this->adminPage'>");
        echo("Team Name: <select name = 'teamId' onchange = 'sendAjax(\"$this->adminPage\",\"{$displayAction}=true&tId=\"+this.value, \"{$action}list\")'>");
        $this->displayDropDownTeam();
        echo("</select>");
        echo("<div id = '{$action}list'> </div>");
        echo("</form></div>");
    }

    //This is called when a team is selected to get the players
    public function displayRemovePlayer($tId)
    {
        echo("Player: <select name = 'playerId'>");
        $this->displayDropDownPlayer($tId);
        echo("</select> <br>");
        echo("<input type = 'submit' name = 'removePlayer' value = 'Remove Player!' /> <br>");

    }

    //This is called when a team is selected to get the players to edit
    public function displayEditPlayerSelect($tId)
    {
        echo("Player: <select name = 'playerId' onchange = 'sendAjax(\"$this->adminPage\",\"editPlayerValues=true&pId=\"+this.value, \"playerEditArea\")'>");
        $this->displayDropDownPlayer($tId);
        echo("</select> <br>");
        print("<div id = 'playerEditArea'> </div>");
        echo("<input type = 'submit' name = 'editPlayer' value = 'Edit Player!' /> <br>");

    }

    public function displayEditPlayer($pId)
    {
        print("New Values: <br>");
        $player = new Player($pId);
        echo("Player Name: <input type = 'text' name = 'playerName' value = '{$player->getName()}'/> <br>");
        echo("Player Alias: <input type = 'text' name = 'playerAlias' value = '{$player->getAlias()}'/> <br>");
        echo("Team Name: <select name = 'newTeamId'>");
        $this->displayDropDownTeam($player->getTeamid());
        echo("</select> <br>");

    }


    //function to display form for removing a team
    public function displayRemoveTeam()
    {
        echo("<div id = 'removeTeam' class = 'dispForm'>");
        echo("<div class = 'title'> Remove Team here: </div> <br>");
        echo("<form method = 'POST' action = '$this->adminPage'>");
        echo("Team Name: <select name = 'teamId'>");
        $this->displayDropDownTeam();
        echo("</select> <br>");
        echo("<input type = 'submit' name = 'removeTeam' value = 'Remove Team!' /> <br>");
        echo("</form></div>");
    }

    //function to display form for adding a matchup
    public function displayAddMatch()
    {
        echo("<div id = 'addMatch' class = 'dispForm'>");
        echo("<div class = 'title'> Add Match to league! </div> <br>");
        echo("<form method = 'POST' action = '$this->adminPage' />");
        echo("Team1: <select name = 'team1Id'>");
        $this->displayDropDownTeam();
        echo("</select> <br>");
        echo("Team2: <select name = 'team2Id'>");
        $this->displayDropDownTeam();
        echo("</select> <br>");
        echo("Week <input type = 'text' name = 'week' /> <br>");
        echo("Date <input type = 'text' name = 'date' /> <br>");
        //echo("Score1: <input type = 'text' name = 's1' size = '3' /> ");
        //echo("Score2: <input type = 'text' name = 's2' size = '3' /> <br>");
        echo("<input type = 'submit' name = 'addMatch' value = 'Add match now!' />");
        echo("</form></div>");
    }

    //function to show matchups in a drop down box
    public function displayDropDownMatchup()
    {
        echo("<option value = '0'> None </option>");
        $matchupList = Matchup::getMatchupList();
        foreach ($matchupList as $arr) {
            $mid = $arr['id'];
            $mteam1 = new Team($arr['team1id']);
            $mteam2 = new Team($arr['team2id']);
            $mweek = $arr['week'];
            $mdate = $arr['date'];
            $mscore1 = $arr['score1'];
            $mscore2 = $arr['score2'];
            $mname = "Week " . $mweek . " " . $mteam1->getName() . " vs " . $mteam2->getName() . " (" . $mscore1 . " - " . $mscore2 . ")";
            $mgamePlayed = $arr['gameplayed'];
            echo("<option value = '$mid'>$mname</option>");
        }
    }

    //function to add a match
    public function addMatch($arr)
    {
        $t1 = new Team($arr['team1Id']);
        $t2 = new Team($arr['team2Id']);
        $m = new Matchup('', $t1, $t2, $arr['week'], $arr['date'], 0, 0, 0);
        $m->addToDb();
    }

    //function to display form to edit the match list
    public function displayEditMatchList()
    {
        echo("<div id = 'editMatchList' class = 'dispForm'>");
        echo("<div class = 'title'> Edit Matchup here: </div> <br>");
        echo("<form method = 'POST' action = '$this->adminPage' />");
        echo("Matchup: <select name = 'matchupId'>");
        $this->displayDropDownMatchup();
        echo("</select> <br>");
        echo("<input type = 'submit' name = 'editMatchList' value = 'Edit Matchup!'>");
        echo("</form></div>");
    }

    //function to display form for editing a match
    public function displayEditMatch($mid)
    {
        $m = new Matchup($mid);
        $t1 = $m->getTeam1();
        $t2 = $m->getTeam2();
        $week = $m->getWeek();
        $date = $m->getDate();
        $score1 = $m->getScore1();
        $score2 = $m->getScore2();
        echo("<div id = 'editMatch' class = 'dispForm'>");
        echo("Edit Match to league!");
        echo("<form method = 'POST' action = '$this->adminPage' />");
        echo("<input type = 'hidden' name = 'mId' value = '$mid' />");
        echo("Team1: <select name = 'team1Id'>");
        $this->displayDropDownTeam($t1->getId());
        echo("</select> <br>");
        echo("Team2: <select name = 'team2Id'>");
        $this->displayDropDownTeam($t2->getId());
        echo("</select> <br>");
        echo("Week <input type = 'text' name = 'week' value = '$week'/> <br>");
        echo("Date <input type = 'text' name = 'date' value = '$date'/> <br>");
        echo("Score1: <input type = 'text' name = 's1' size = '3' value = '$score1'/> ");
        echo("Score2: <input type = 'text' name = 's2' size = '3' value = '$score2'/> <br>");
        if ($m->getGamePlayed() == 1) {
            echo("Game played?: <input type = 'checkbox' name = 'gamePlayed' checked = 'true' value = '1'/>");
        } else {
            echo("Game played?: <input type = 'checkbox' name = 'gamePlayed' value = '1'/>");
        }
        echo("<br>");
        echo("<input type = 'submit' name = 'editMatch' value = 'Edit match now!' /> <br>");
        echo("Add Stats <br> Number of players for team 1: <input type = 'text' size = '3' name = 'numPlayers1' />");
        echo("Number of players for team 2: <input type = 'text' size = '3' name = 'numPlayers2' />");
        echo("<input type = 'submit' name = 'displayAddStats' value = 'Add Stats!' /> <br>");
        echo("<input type = 'submit' name = 'displayEditStats' value = 'Edit Stats!' /> <br>");
        echo("</form></div>");
        echo("<a href = '$this->adminPage'> Go back </a>");
    }

    //function to display form for adding stats
    public function displayAddStats($arr)
    {
        //$m = new Matchup($arr['mId']);
        //die($arr['team1Id']);
        $mId = $arr['mId'];

        $dummyStats = Stats::getInstance()->getStatisticList();

        echo("<div id = 'addStats' class = 'dispForm'>");
        echo("<form action = '$this->adminPage' method = 'Post'>");
        echo("<input type = 'hidden' name = 'mId' value = '$mId' />");

        for ($teamIndex = Constants::TEAM_START_INDEX; $teamIndex <= Constants::NUM_TEAMS; $teamIndex++) {
            $teamId = $arr["team{$teamIndex}Id"];
            $team = new Team($teamId);
            $numPlayers = $arr["numPlayers{$teamIndex}"];


            echo("Team {$teamIndex}: <br>");
            echo("<input type = 'hidden' name = 'team{$teamIndex}id' value = '$teamId' />");
            echo("<input type = 'hidden' name = 'numPlayers{$teamIndex}' value = '$numPlayers' />");
            echo("<table>");


            $tableHeader = "<tr><td> PlayerName </td>";
            /** @var Statistic $stat */
            foreach ($dummyStats as $stat) {
                if ($stat instanceof RecordedStatistic) {
                    $tableHeader .= "<td>" . $stat->getDisplayName() . "</td>";
                }
            }
            $tableHeader .= "</tr>";
            //print table header
            print($tableHeader);

            for ($i = 0; $i < $numPlayers; $i++) {
                echo("<tr> <td>");

                echo("<select name = 'p{$teamIndex}id$i'>");
                $this->displayDropDownPlayer($teamId);
                echo("<option value = '0'></option>");
                echo("<option value = '0'></option>");
                $this->displayDropDownPlayer();
                echo("</select> </td>");
                /** @var Statistic $stat */
                foreach ($dummyStats as $stat) {
                    if ($stat instanceof RecordedStatistic) {
                        $statName = $stat->getLogicalName();
                        echo("<td> <input type = 'text' name = '{$statName}{$teamIndex}{$i}' /> </td>");
                    }
                }
            }
            echo("</table>");

        }


        echo("<input type = 'submit' name = 'addStats' value = 'Add Stats'!/>");
        echo("</form></div>");
        echo("<a href = '$this->adminPage'> Go back </a>");
    }

    //function for adding stats
    public function addStats($arr)
    {
        $dummyStats = Stats::getInstance()->getStatisticList();

        for ($teamIndex = Constants::TEAM_START_INDEX; $teamIndex <= Constants::NUM_TEAMS; $teamIndex++) {
            //Get team's stats
            $statArray = array();

            $statArray[Constants::MATCHUP_ID] = $arr["mId"];
            $statArray[Constants::TEAM_ID] = $arr["team{$teamIndex}id"];

            for ($i = 0; $i < $arr["numPlayers{$teamIndex}"]; $i++) {
                $statArray[Constants::PLAYER_ID] = $arr["p{$teamIndex}id{$i}"];
                /** @var Statistic $stat */
                foreach ($dummyStats as $stat) {
                    if ($stat instanceof RecordedStatistic) {
                        $statName = $stat->getLogicalName();
                        $statArray[$statName] = $arr["{$statName}{$teamIndex}{$i}"];
                    }
                }
                $stats = new Stats($statArray);
                $stats->addToDb();
            }
        }
    }

    //function for displaying form to edit stats
    public function displayEditStats($arr)
    {
        $m = new Matchup($arr['mId']);
        //die($arr['team1Id']);

        $mId = $arr['mId'];
        $dummyStats = Stats::getInstance()->getStatisticList();

        echo("<div id = 'editStats' class = 'dispForm'>");
        echo("<form action = '$this->adminPage' method = 'Post'>");
        echo("<input type = 'hidden' name = 'mId' value = '$mId' />");


        for ($teamIndex = Constants::TEAM_START_INDEX; $teamIndex <= Constants::NUM_TEAMS; $teamIndex++) {
            $team = $m->getTeam($teamIndex);
            echo("Team {$teamIndex}: <br>");

            echo("<input type = 'hidden' name = 'team{$teamIndex}id' value = '' />");
            echo("<input type = 'hidden' name = 'numPlayers{$teamIndex}' value = '' />");

            echo("<table>");

            $tableHeader = "<tr><td> PlayerName </td>";
            /** @var Statistic $stat */
            foreach ($dummyStats as $stat) {
                if ($stat instanceof RecordedStatistic) {
                    $tableHeader .= "<td>" . $stat->getDisplayName() . "</td>";
                }
            }
            $tableHeader .= "<td>Delete</td></tr>";
            //print table header
            print($tableHeader);

            $teamStats = $m->getTeamStats($teamIndex);
            //$t1Stats = $m->getT1Stats();
            for ($i = 0; $i < sizeOf($teamStats); $i++) {
                /** @var Stats $curStat */
                $curStat = $teamStats[$i];
                $curId = $curStat->getId();

                echo("<input type = 'hidden' name = 'stat{$teamIndex}id$i' value = '$curId' />");
                echo("<tr> <td>");

                echo("<select name = 'p{$teamIndex}id$i'>");
                $this->displayDropDownPlayer($team->getId(), $curStat->getPlayerId());
                echo("<option value = '0'></option>");
                echo("<option value = '0'></option>");
                $this->displayDropDownPlayer();
                echo("</select> </td>");


                foreach ($curStat->getStatisticList() as $stat) {
                    if ($stat instanceof RecordedStatistic) {
                        $statName = $stat->getLogicalName();
                        $statValue = $stat->getValue();
                        echo("<td> <input type = 'text' name = '{$statName}{$teamIndex}{$i}' value = '{$statValue}' maxlength='3' /> </td>");
                    }
                }
                echo("<td> <input type = 'checkbox' name = 'deleteStat{$teamIndex}{$i}' value = 'true' </td></tr>");
            }
            echo("</table>");

        }
        echo("<input type = 'submit' name = 'editStats' value = 'Edit Stats'!/>");
        echo("</form></div>");
        echo("<a href = '$this->adminPage'> Go back </a>");
    }

    //function to edit stats
    public function editStats($arr)
    {
        //Update team 1 stats:
        //Loop through the stats for team 1:
        $m = new Matchup($arr['mId']);

        for ($teamIndex = Constants::TEAM_START_INDEX; $teamIndex <= Constants::NUM_TEAMS; $teamIndex++) {

            for ($i = 0; $i < sizeOf($m->getTeamStats($teamIndex)); $i++) {
                $curStat = new Stats($arr["stat{$teamIndex}id{$i}"]);

                if (isset($arr["deleteStat{$teamIndex}{$i}"]) && "true" == $arr["deleteStat{$teamIndex}{$i}"]) {
                    //Delete the stat
                    $curStat->deleteFromDb();
                    continue;
                }

                $curStat->setPlayerId($arr["p{$teamIndex}id{$i}"]);

                foreach ($curStat->getStatisticList() as $stat) {
                    if ($stat instanceof RecordedStatistic) {
                        $statName = $stat->getLogicalName();
                        $stat->setValue($arr["{$statName}{$teamIndex}{$i}"]);
                    }
                }
                $curStat->updateStats();
            }
        }
    }

    //function for editing a match
    public function editMatch($arr)
    {
        $m = new Matchup($arr['mId']);
        $team1 = new Team($arr['team1Id']);
        $team2 = new Team($arr['team2Id']);
        $m->setTeam1($team1);
        $m->setTeam2($team2);
        $m->setWeek($arr['week']);
        $m->setDate($arr['date']);
        $m->setScore1($arr['s1']);
        $m->setScore2($arr['s2']);
        if (!isset($arr['gamePlayed'])) $arr['gamePlayed'] = "0";
        $m->setGamePlayed($arr['gamePlayed']);
        $m->updateDb();
    }

    //function for displaying form to remove a match
    public function displayRemoveMatchup()
    {
        echo("<div id = 'removeMatchup' class = 'dispForm'>");
        echo("<div class = 'title'> Remove Matchup here: </div> <br>");
        echo("<form method = 'POST' action = '$this->adminPage' />");
        echo("Matchup: <select name = 'matchupId'>");
        $this->displayDropDownMatchup();
        echo("</select> <br>");
        echo("<input type = 'submit' name = 'removeMatchup' value = 'Remove Matchup!' /> <br>");
        echo("</form></div>");
    }

    //function for removing a match
    public function removeMatchup($arr)
    {
        $matchup = new Matchup($arr['matchupId']);
        $matchup->removeDb();
    }

}

if (isAdmin()) {
    add_shortcode("display_admin", "display_admin");
    //add_action('plugins_loaded', 'process_admin_ajax');
    process_admin_ajax();
}

function display_admin()
{
    /** @var Admin $admin */
    if (!isset($admin) || null == $admin) {
        $admin = new Admin();
    }

    ob_start();
    $admin->main();
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

function process_admin_ajax()
{
    /** @var Admin $admin */
    if (!isset($admin) || null == $admin) {
        $admin = new Admin();
    }
    if ($admin->ajaxRequests()) {
        //do_action("shutdown");
        exit();
    }
}


//$a1 = new Admin();