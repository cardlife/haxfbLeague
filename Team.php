<?php


class Team {
	//attributes
	//private $leader;
	private $name;
	private $id;
    private $homePage;
	private $players;
    private $page;
    /** @var  wpdb $wpdb */
    private $db;


	//creating constructors
	public function __construct($var, $var2 = null, $var3=null) {
		//constructor here
        global $wpdb;
        $this->db = &$wpdb;
		//Check if $id is an array -> then reparse it
		if(is_array($var)) { 
			$this->__construct2($var);
		} else if ($var2 != null) {
			$this->__construct3($var,$var2, $var3);
		} else {
			$this->__construct1($var);
		}
        $this->page = Util::getPageUrl(Constants::PAGE_TEAM_DISP.$this->id);
	}
	public function __construct3($id, $name, $homePage) {
		$this->id = $id;
		$this->name = $name;
        $this->homePage = $homePage;
		
	}
	public function __construct1($id) {
		$row = $this->db->get_row(
            $this->db->prepare("Select * from team where id = %d", $id), ARRAY_A);
		//$row = mysql_fetch_array($query);
		$this->__construct2($row);
	}
	public function __construct2($row) {
		//$this->leader = new Player($row["leader"]);
		$this->name = $row["name"];
		$this->id = $row["id"];
        $this->homePage = $row["homepage"];
		$rows =  $this->db->get_results(
            $this->db->prepare("Select * from player where teamid = %d", $this->id), ARRAY_A);
		$x = 0;
		foreach($rows as $row) {
			$this->players[$x] = new Player($row);
			$x++;
		}
	}
	
	/*public function getLeader() {
		return $this->leader;
	}
	public function setLeader($player) {
		$this->leader = $this->player;
	}*/
	//get/set team id
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
	}
	//get/set team name
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}

    public function getHomePage() {
        return $this->homePage;
    }
    public function setHomePage($homePage) {
        $this->homePage = $homePage;
    }

    public function getNameWithLink() {
        return "<a href='{$this->page}'>{$this->name}</a>" ;
    }


	//add team name to database
	public function addToDb() {
		$this->db->query(
            $this->db->prepare("Insert into team (id, name, homePage) Values('', %s, %s)",
                $this->name, $this->homePage));
	}
	//remove team name from database
	public function removeDb() {
		$this->db->query(
            $this->db->prepare("Delete from team where id = %d",$this->id));
		for($x = 0; $x < sizeof($this->players); $x++) {
			$this->players[$x]->setTeamid(0);
			$this->players[$x]->updateDb();
		}
	}
	//edit team in database
	public function editDb() {
		$result = $this->db->query(
            $this->db->prepare("Update team SET name = %s, homePage = %s where id = %d", $this->name, $this->homePage, $this->id))
		or print("Error editing team.");
	}
	//get players from team
	public function getPlayers() {
		return $this->players;
	}
	//get a list of all teams
	public static function getTeamList() {
		global $wpdb;
        $query = $wpdb->get_results("Select * from team", ARRAY_A);
		return $query;
	}
}

?>