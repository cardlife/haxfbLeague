<?php

function setupHFL() {
    global $wpdb;
    createTable(setupMatchUpTable($wpdb));
    createTable(setupPlayerTable($wpdb));
    createTable(setupStatsTable($wpdb));
    createTable(setupTeamTable($wpdb));
}

function createTable($tableSql){
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $tableSql." $charset_collate;");
}


function setupMatchUpTable($wpdb) {
//    $table_name = $wpdb->prefix.'matchup';
    $table_name = 'matchup';

    return "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		team1id int(11) NOT NULL,
		team2id int(11) NOT NULL,
		week int(11) NOT NULL,
		date date NOT NULL,
		score1 int(11) NOT NULL,
		score2 int(11) NOT NULL,
		gameplayed int(1) NOT NULL,
		PRIMARY KEY  (id)
	)";
}

function setupPlayerTable($wpdb) {
//    $table_name = $wpdb->prefix.'player';
    $table_name = 'player';

    return "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		teamid int(11) NOT NULL,
		PRIMARY KEY  (id)
	)";
}


function setupStatsTable($wpdb) {
//    $table_name = $wpdb->prefix.'stats';
    $table_name = 'stats';

    return "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		playerid int(11) NOT NULL,
		matchupid int(11) NOT NULL,
		teamid int(11) NOT NULL,
		passingYards int(11) NOT NULL,
		rushingYards int(11) NOT NULL,
		receivingYards int(11) NOT NULL,
		receptions int(11) NOT NULL,
		touchdowns int(11) NOT NULL,
		tackles int(11) NOT NULL,
		passDeflections int(11) NOT NULL,
		interceptions int(11) NOT NULL,
		rushingCarries int(11) NOT NULL,
		fieldGoalMade int(11) NOT NULL,
		fieldGoalMiss int(11) NOT NULL,
		fieldGoalYards int(11) NOT NULL,
		passIncomplete int(11) NOT NULL,
		passComplete int(11) NOT NULL,
		PRIMARY KEY  (id)
	)";
}


function setupTeamTable($wpdb) {
    $table_name = 'team';

    return "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		name varchar(100) NOT NULL,
		homepage varchar(100) NOT NULL,
		PRIMARY KEY  (id)
	)";
}


?>