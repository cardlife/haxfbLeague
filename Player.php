<?php


class Player {
	//attributes
	private $name;
	private $id;
	private $password;
	private $email;
	private $teamid;
	private $alias;
    private $page;
    /** @var wpdb $db */
    private $db;

    private static $playerList = null;
	//creating constructors
	public function __construct($var, $var2 = null, $var3 = null, $var4 = null, $var5 = null) {
		//constructor here
        global $wpdb;
        $this->db = &$wpdb;
        $this->page = Util::getPageUrl(Constants::PAGE_PLAYER_DISP);

		//Check if $id is an array -> then reparse it
		if(is_array($var)) { 
			$this->__construct2($var);
		} else if($var4 != null) {
			$this->__construct3($var, $var2, $var3, $var4, $var5);
		} 
		else {
			$this->__construct1($var);
		}
	}
	public function __construct1($id) {
		$query = $this->db->prepare("Select * from player where id = %d", $id);
		$row = $this->db->get_row($query, ARRAY_A);
		$this->__construct2($row);
	}
	public function __construct2($row) {
		$this->id = $row["id"];
		$this->name = $row["name"];
		$this->password = $row["password"];
		$this->email = $row["email"];
		$this->teamid = $row["teamid"];
		$this->alias = $row["name"];
	}
	public function __construct3($name, $password, $email, $teamid, $alias) {
		$this->name = $name;
		$this->password = $password;
		$this->email = $email;
		$this->teamid = $teamid;
		$this->alias = $alias;
	}
	//get player id
	public function getID() {
		return $this->id;
	}
	/*public function setID($id) {
		$this->id = $id;
	}*/
	//get/set player name
	public function getName() {
		return $this->name;
	}
	public function setName($name) {
		$this->name = $name;
	}

    public function getNameWithLink() {
        $playerUrl = Util::getURL($this->page, array("pId" => $this->id));
        return "<a href={$playerUrl}>{$this->name}</a>";
    }


	//get/set player email
	public function setEmail($email) {
		$this->email = $email;
	}
	public function getEmail() {
		return $this->email;
	}
	//get/set player alias
	public function getAlias() {
		return $this->alias;
	}
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	//get/set player team id
	public function getTeamid() {
		return $this->teamid;
	}
	public function setTeamid($teamid) {
		$this->teamid = $teamid;
	}
	//update player in database, if changes made
	public function updateDb() {
		$query = $this->db->prepare("Update player SET name = %s,
		teamid = %d  where id = %d",$this->name,$this->teamid,$this->id);
        $this->db->query($query);
	}
	//remove player from database
	public function deleteFromDb() {
		$query = $this->db->prepare("Delete from player where id = %d", $this->id);
        $this->db->query($query);
	}
	//add player to database
	public function insertToDb() {
		$query = $this->db->prepare("Insert into player (id, name, teamid)
		Values('', %s, %d)", $this->name, $this->teamid);
        $this->db->query($query)
		    or print("Error adding player to db! <br><br>");
	}

	//get a list of all players
    public static function getPlayerList() {
        global $wpdb;
        if(null == self::$playerList) {
            $playerQuery =  $wpdb->get_results("Select * from player", ARRAY_A);
            self::$playerList = array();
            $i = 0;
            foreach($playerQuery as $row) {
                self::$playerList[$i] = new Player($row['id']);
                $i++;
            }
        }
        return self::$playerList;
    }
}




//Make a sample player and get its name
/*$p1 = new Player(1);
print($p1->getName());
*/
?>
