<?php

class Matchup
{
    //attributes
    private $id;
    /** @var Team $team1 */
    private $team1;
    /** @var  Team $team2 */
    private $team2;
    private $week;
    private $date;
    private $score1;
    private $score2;
    private $gameplayed;
    private $t1stats;
    private $t2stats;
    /** @var  wpdb $db */
    private $db;

    private $page;

    //creating constructors
    public function __construct($var, $var2 = null, $var3 = null, $var4 = null, $var5 = null, $var6 = null, $var7 = null, $var8 = null)
    {
        //constructor here
        global $wpdb;
        $this->db = & $wpdb;
        $this->page = Util::getPageUrl(Constants::PAGE_MATCH_DISP);

        //Check if $id is an array -> then reparse it
        if (is_array($var)) {
            $this->__construct2($var);
        } else if ($var2 != null) {
            $this->__construct3($var, $var2, $var3, $var4, $var5, $var6, $var7, $var8);
        } else {
            $this->__construct1($var);
        }
    }

    public function __construct1($id)
    {
        $query = $this->db->prepare("Select * from matchup where id = %d", $id);
        $row = $this->db->get_row($query, ARRAY_A);
        $this->__construct2($row);
    }

    public function __construct2($row)
    {
        $this->id = $row["id"];
        $this->team1 = new Team($row["team1id"]);
        $this->team2 = new Team($row["team2id"]);
        $team1id = $row["team1id"];
        $team2id = $row["team2id"];
        $this->week = $row["week"];
        $this->date = $row["date"];
        $this->score1 = $row["score1"];
        $this->score2 = $row["score2"];
        $this->gameplayed = $row["gameplayed"];
        $query2 = $this->db->prepare("Select * from stats where matchupid = %d AND teamid = %d", $this->id, $team1id);
        $statList = $this->db->get_results($query2, ARRAY_A);
        $i = 0;
        foreach ($statList as $row2) {
            $this->t1stats[$i] = new Stats($row2['id']);
            $i++;
        }

        $query2 = $this->db->prepare("Select * from stats where matchupid = %d AND teamid = %d", $this->id, $team2id);
        $statList = $this->db->get_results($query2, ARRAY_A);
        $i = 0;
        foreach ($statList as $row2) {
            $this->t2stats[$i] = new Stats($row2['id']);
            $i++;
        }
    }

    public function __construct3($id, $team1, $team2, $week, $date, $score1, $score2, $gameplayed)
    {
        $this->id = $id;
        $this->team1 = $team1;
        $this->team2 = $team2;
        $this->week = $week;
        $this->date = $date;
        $this->score1 = $score1;
        $this->score2 = $score2;
        $this->gameplayed = $gameplayed;
    }

    //Get matchup id
    public function getId()
    {
        return $this->id;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getTeam($teamIndex)
    {
        if (Constants::TEAM_START_INDEX == $teamIndex) {
            return $this->team1;
        }
        return $this->team2;
    }

    /**
     * @return Team
     */
    //get/set 1st team
    public function getTeam1()
    {
        return $this->team1;
    }

    public function setTeam1($team)
    {
        $this->team1 = $team;
    }

    /**
     * @return Team
     */
    //get/set 2nd team
    public function getTeam2()
    {
        return $this->team2;
    }

    public function setTeam2($team)
    {
        $this->team2 = $team;
    }

    //get/set week of matchup
    public function getWeek()
    {
        return $this->week;
    }

    public function setWeek($week)
    {
        $this->week = $week;
    }

    //get/set date of matchup
    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    //get/set score for 1st team
    public function setScore1($score)
    {
        $this->score1 = $score;
    }

    public function getScore1()
    {
        return $this->score1;
    }

    //get/set score for 2nd team
    public function setScore2($score)
    {
        $this->score2 = $score;
    }

    public function getScore2()
    {
        return $this->score2;
    }

    public function getResultWithLink()
    {
        $matchUrl = Util::getURL($this->page, array("mId" => $this->id));
        return "<a href={$matchUrl}>({$this->score1} - {$this->score2})</a>";
    }

    public function getMatchupWithLink()
    {
        $matchUrl = Util::getURL($this->page, array("mId" => $this->id));
        return "<a href={$matchUrl}>{$this->team1->getName()} vs {$this->team2->getName()} </a>";

    }

    //get/set if game has been played or not
    public function setGamePlayed($gp)
    {
        $this->gameplayed = $gp;
    }

    public function getGamePlayed()
    {
        return $this->gameplayed;
    }

    //get/set 1st team's stats
    public function setT1Stats($stats)
    {
        $this->t1stats = $stats;
    }

    public function getTeamStats($teamIndex)
    {
        if (Constants::TEAM_START_INDEX == $teamIndex) {
            return $this->t1stats;
        }
        return $this->t2stats;
    }

    public function getT1Stats()
    {
        return $this->t1stats;
    }

    //get/set 2nd team's stats
    public function setT2Stats($stats)
    {
        $this->t2stats = $stats;
    }

    public function getT2Stats()
    {
        return $this->t2stats;
    }

    //add matchup to database
    public function addToDb()
    {
        $t1id = $this->team1->getId();
        $t2id = $this->team2->getId();
        $query = $this->db->prepare("Insert into matchup (id, team1id, team2id, week, date, score1, score2, gameplayed)
		Values('', %d, %d, %d, %s, %d, %d, %d)", $t1id, $t2id, $this->week, $this->date, $this->score1, $this->score2, $this->gameplayed);
        $this->db->query($query);
    }

    //update any changes to matchup in database
    public function updateDb()
    {
        $team1Id = $this->team1->getId();
        $team2Id = $this->team2->getId();
        /*print("Update matchup SET team1id = '$team1Id', team2id = '$team2Id',
        week = '$this->week', date = '$this->date', score1
        where id = '$this->id'");*/
        $query = $this->db->prepare("Update matchup SET team1id = %d, team2id = %d,
		week = %d, date = %s, score1 = %d, score2 = %d,	gameplayed = %d where id = %d"
            , $team1Id, $team2Id, $this->week, $this->date, $this->score1, $this->score2, $this->gameplayed, $this->id);

        $this->db->query($query) or print("There was an error updating the match");
    }

    //get a list of all matchups
    public static function getMatchupList()
    {
        global $wpdb;
        $matchList = $wpdb->get_results("Select * from matchup", ARRAY_A);
        return $matchList;
    }

    //remove matchup from database
    public function removeDb()
    {
        $query = $this->db->prepare("Delete from matchup where id = %d", $this->id);
        $this->db->query($query) or print("Error deleting match!");
        //Remove all the stats from the matchup
        for ($i = 0; $i < sizeof($this->t1stats); $i++) {
            $temp = $this->t1stats[$i];
            $temp->deleteFromDb();
        }
        for ($i = 0; $i < sizeof($this->t2stats); $i++) {
            $temp = $this->t2stats[$i];
            $temp->deleteFromDb();
        }
    }
}