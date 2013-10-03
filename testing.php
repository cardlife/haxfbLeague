<?php
require_once 'PHPUnit/Framework.php';
include('admin.php');
class AdminTest extends PHPUnit_Framework_TestCase
{
    public function testAdminFunctions()
    {
        $a = new Admin();
        
        //Test adding a team
    	$arr = array("teamName" => "Team-Cecs440");
    	
        $a->addTeam($arr);
        $row = mysql_fetch_array(mysql_query("Select * from team where name = 'Team-Cecs440'"));
 		$this->assertEquals('Team-Cecs440', $row['teamName']);
        //Test adding a player
 		$arr = array("playerName" => "Night", "password" => "samppw",
 		 "email" => "cecs440@rand.edu", "teamId" => "1");
 		
 		$row = mysql_fetch_array(mysql_query("Select * from team where password = 'samppw'"));
 		$this->assertEquals('Night', $row['name']);
		
    }
}
?>