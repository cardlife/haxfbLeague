<?php


include("Beans/RecordedStatistic.php");
include("Beans/CalculatedStatistic.php");
include("Beans/Constants.php");
include("Beans/View.php");
//use hfl\Beans\RecordedStatistics;
class Stats
{
    //Attributes
    private $id;
    private $playerid;
    private $matchupid;
    private $teamid;

    /** @var Stats $instance */
    private static $instance = null;
    private $statList = array();

    private $viewList;
    /** @var  wpdb $db */
    private $db;


    /** Returns an empty stats object
     * @return Stats
     */
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new Stats();
        }
        return self::$instance;
    }


    //creating constructors
    public function __construct($var = null, $var2 = null, $var3 = null, $var4 = null, $var5 = null, $var6 = null, $var7 = null, $var8 = null, $var9 = null)
    {
        //constructor here
        global $wpdb;
        $this->db = & $wpdb;
        $this->setupStatistics();
        //Check if $id is an array -> then reparse it
        if (is_array($var)) {
            $this->__construct2($var);
        } else if ($var2 != null) {
            $this->__construct3($var, $var2, $var3, $var4, $var5, $var6, $var7, $var8, $var9);
        } else if ($var != null) {
            $this->__construct1($var);
        }
    }

    private function getDivideFormula($numerator, $denominator)
    {
        if ($numerator instanceof Statistic) {
            $numerator = $numerator->getSelectPart();
        }
        if ($denominator instanceof Statistic) {
            $denominator = $denominator->getSelectPart();
        }
        return "Round({$numerator} / IF({$denominator} = 0 ,1, {$denominator}), 2)";
    }


    public function setupStatistics()
    {
        $passingYards = new RecordedStatistic("passingYards", "PY", "Passing Yards");
        $rushingYards = new RecordedStatistic("rushingYards", "RY", "Rushing Yards");
        $rushingCarries = new RecordedStatistic("rushingCarries", "RU_C", "Carries");
        $receivingYards = new RecordedStatistic("receivingYards", "RCVY", "Receiving Yards");
        $receptions = new RecordedStatistic("receptions", "RCP", "Receptions");
        $touchDowns = new RecordedStatistic("touchdowns", "TD", "Touch Downs");
        $tackles = new RecordedStatistic("tackles", "TCKL", "Tackles");
        $passDeflections = new RecordedStatistic("passDeflections", "PD", "Pass Deflections");
        $interceptions = new RecordedStatistic("interceptions", "INT", "Interceptions");
        $fieldGoalMade = new RecordedStatistic("fieldGoalMade", "FGM", "Made FieldGoals");
        $fieldGoalMiss = new RecordedStatistic("fieldGoalMiss", "FGMM", "Missed FieldGoals");
        $fieldGoalYards = new RecordedStatistic("fieldGoalYards", "FGY", "FieldGoal Yards");
        $passIncomplete = new RecordedStatistic("passIncomplete", "PI", "Pass Incompletes");
        $passComplete = new RecordedStatistic("passComplete", "PC", "Passes Completed");

        $totalYards = new CalculatedStatistic("totalYards", "TY", "Total Yards",
            "{$passingYards->getSelectPart()} + {$rushingYards->getSelectPart()} + {$receivingYards->getSelectPart()}");
        $rushPerCarry = new CalculatedStatistic("yardsPerCarry", "RPC", "Yards Per Carry",
            $this->getDivideFormula($rushingYards, $rushingCarries));
        $yardsPerCatch = new CalculatedStatistic("yardsPerCatch", "YPC", "Yards Per Catch",
            $this->getDivideFormula($receivingYards, $receptions));
        $completionRate = new CalculatedStatistic("completionRate", "CR", "Completion Rate",
            $this->getDivideFormula($passComplete, $passComplete->getSelectPart() . "+" . $passIncomplete->getSelectPart()) . " * 100");
        $passPerAttempt = new CalculatedStatistic("passPerAttempt", "PPA", "Passing Yards Per Attempt",
            $this->getDivideFormula($passingYards, $passComplete->getSelectPart() . "+" . $passIncomplete->getSelectPart()));
        $fieldGoalRate = new CalculatedStatistic("fieldGoalRate", "FGR", "Field Goal Rate",
            $this->getDivideFormula($fieldGoalMade, $fieldGoalMade->getSelectPart() . "+" . $fieldGoalMiss->getSelectPart()) . " * 100");


        $this->addStatistic($totalYards);
        $this->addStatistic($passingYards);
        $this->addStatistic($rushingYards);
        $this->addStatistic($receivingYards);
        $this->addStatistic($receptions);
        $this->addStatistic($touchDowns);
        $this->addStatistic($tackles);
        $this->addStatistic($passDeflections);
        $this->addStatistic($interceptions);
        $this->addStatistic($rushingCarries);
        $this->addStatistic($rushPerCarry);
        $this->addStatistic($yardsPerCatch);
        $this->addStatistic($fieldGoalMade);
        $this->addStatistic($fieldGoalMiss);
        $this->addStatistic($fieldGoalYards);
        $this->addStatistic($fieldGoalRate);
        $this->addStatistic($passIncomplete);
        $this->addStatistic($passComplete);
        $this->addStatistic($completionRate);
        $this->addStatistic($passPerAttempt);


        $recordedView = new View(Constants::VIEW_RECORDED_STATS);

        foreach ($this->statList as $stat) {
            if ($stat instanceof RecordedStatistic) {
                $recordedView->addStat($stat);
            }
        }

        $recordedView->setShortCode(true);

        $offensePassView = new View(Constants::VIEW_OFFENSE_PASS,
            array($passingYards, $touchDowns,
                $passComplete, $passIncomplete, $completionRate, $passPerAttempt), false, $passingYards);
        $offenseSkillsView = new View(Constants::VIEW_OFFENSE_SKILLS,
            array($receivingYards, $receptions, $yardsPerCatch, $rushingYards, $rushingCarries, $rushPerCarry), false, $receivingYards);

        $defenseView = new View(Constants::VIEW_DEFENSE, array($tackles, $passDeflections, $interceptions), false, $tackles);

        $specialTeamsView = new View(Constants::VIEW_SPECIAL_TEAMS, array($fieldGoalYards, $fieldGoalMade, $fieldGoalMiss, $fieldGoalRate), false, $fieldGoalMade);

        $this->addView($recordedView)
            ->addView($offensePassView)
            ->addView($offenseSkillsView)
            ->addView($defenseView)
            ->addView($specialTeamsView);

        //$this->recordedView = new View(array($passingYards, $rushingYards, $receivingYards, $touchDowns, $tackles, $fieldGoalYards, $fieldGoalMiss, $fieldGoalMade));


    }

    private function addStatistic(Statistic $stat)
    {
        $this->statList[$stat->getLogicalName()] = $stat;
        return $this;
    }

    public function getStatistic($logicalName)
    {
        /** @var Statistic $statistic */
        if (isset($this->statList[$logicalName])) {
            $statistic = $this->statList[$logicalName];
        } else {
            return null;
        }
        return $statistic;
    }

    public function getStatisticList()
    {
        return $this->statList;
    }


    private function addView(View $view)
    {
        $this->viewList[$view->getDisplayName()] = $view;
        return $this;
    }

    public function getViewList()
    {
        return $this->viewList;
    }

    public function getView($displayName)
    {
        /** @var View $view */
        if (isset($this->viewList[$displayName])) {
            $view = $this->viewList[$displayName];
        } else {
            return null;
        }
        return $view;
    }


    /*public function getRecordedStats() {
        array()
        foreach($this->statList as $stat) {
            if($stat instanceof RecordedStatistic) {

            }

        }
    } */
    //Use this constructor to load stat from db with an id
    public function __construct1($id)
    {
        $query = $this->db->prepare("Select * from stats where id = %d", $id);
        $row = $this->db->get_row($query, ARRAY_A);
        $this->__construct2($row);
    }

    //Use this constructor when loading stat from DB row
    public function __construct2($row)
    {
        if (isset($row[Constants::ID])) {
            $this->id = $row[Constants::ID];
        }
        if (isset($row[Constants::PLAYER_ID])) {
            $this->playerid = $row[Constants::PLAYER_ID];
        }
        if (isset($row[Constants::MATCHUP_ID])) {

            $this->matchupid = $row[Constants::MATCHUP_ID];
        }
        if (isset($row[Constants::TEAM_ID])) {

            $this->teamid = $row[Constants::TEAM_ID];
        }

        /** @var Statistic $stat */
        foreach ($this->statList as $stat) {
            if (isset($row["{$stat->getLogicalName()}"])) {
                $stat->setValue($row["{$stat->getLogicalName()}"]);
            }
        }
    }

    public function __construct3($id, $playerid, $matchupid, $teamid)
    {
        $this->id = $id;
        $this->playerid = $playerid;
        $this->matchupid = $matchupid;
        $this->teamid = $teamid;
    }

    //Get stats id
    public function getId()
    {
        return $this->id;
    }

    //get/set player id
    public function getPlayerId()
    {
        return $this->playerid;
    }

    public function setPlayerId($playerid)
    {
        $this->playerid = $playerid;
    }

    //get/set matchup id
    public function getMatchupId()
    {
        return $this->matchupid;
    }

    public function setMatchupId($matchupid)
    {
        $this->matchupid = $matchupid;
    }

    //get/set team id
    public function getTeamId()
    {
        return $this->teamid;
    }

    public function setTeamId($teamid)
    {
        $this->teamid = $teamid;
    }

    /**
     * @return \View
     */
    public function getDefenseView()
    {
        return $this->getView(Constants::VIEW_DEFENSE);
    }

    /**
     * @return \View
     */
    public function getOffensePassView()
    {
        return $this->getView(Constants::VIEW_OFFENSE_PASS);
    }

    /**
     * @return \View
     */
    public function getOffenseSkillsView()
    {
        return $this->getView(Constants::VIEW_OFFENSE_SKILLS);
    }

    /**
     * @return \View
     */
    public function getRecordedView()
    {
        return $this->getView(Constants::VIEW_RECORDED_STATS);
    }

    /**
     * @return \View
     */
    public function getSpecialTeamsView()
    {
        return $this->getView(Constants::VIEW_SPECIAL_TEAMS);
    }


    //update stats in database
    public function updateStats()
    {

        $statUpdateQuery = "";
        /** @var Statistic $stat */
        foreach ($this->statList as $stat) {
            if ($stat instanceof RecordedStatistic) {
                $statName = $stat->getLogicalName();
                $statVal = $stat->getValue();
                $statUpdateQuery .= ", {$statName} = '{$statVal}'";
            }
        }

        $query = $this->db->prepare("Update stats SET playerid = %d ,matchupid = %d, teamid = %d $statUpdateQuery where id = %d"
            , $this->playerid, $this->matchupid, $this->teamid, $this->id);
        $this->db->query($query) or print("Error updating Stats!");

    }

    //add stats to database
    public function addToDb()
    {

        $statInsertQueryName = "";
        $statInsertQueryValues = "";
        /** @var Statistic $stat */
        foreach ($this->statList as $stat) {
            if ($stat instanceof RecordedStatistic) {
                $statName = $stat->getLogicalName();
                $statVal = $stat->getValue();
                $statInsertQueryName .= ", {$statName}";
                $statInsertQueryValues .= ", '{$statVal}'";
            }
        }

        $query = $this->db->prepare("Insert into stats (id, playerid, matchupid, teamid $statInsertQueryName)
		Values('', %d, %d, %d $statInsertQueryValues)", $this->playerid, $this->matchupid, $this->teamid);
        $this->db->query($query) or print("Error inserting Stats!");

    }

    //remove stats from database
    public function deleteFromDb()
    {
        $query = $this->db->prepare("Delete from stats where id = %d", $this->id);
        $this->db->query($query);
    }
}

?>