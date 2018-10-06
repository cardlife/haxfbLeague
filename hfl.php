<?php
/*
Plugin Name: Hax FootBall League Manager
Plugin URI: http://haxfb.com
Description: Custom written haxfootball league plugin
Version: 0.1
Author: Night
License: GPL2
*/


add_action('init', 'load_libraries');
register_activation_hook( __FILE__, 'hfl_install' );

function hfl_install() {
    require_once (dirname (__FILE__) . '/install.php');
    setupHFL();
}


function load_libraries(){
    loadLibraries();
}

/**
 * Simple utility method that checks if current user has an 'administrator' role.
 */
function isAdmin() {
    return  current_user_can('administrator');
}


/**
 * Function that loads all of the plugin page-classes.
 */
function loadLibraries() {
    // Global libraries
    require_once (dirname (__FILE__) . '/Schedule.php');
    require_once (dirname (__FILE__) . '/Teamdisp.php');
    require_once (dirname (__FILE__) . '/Matchdisp.php');
    require_once (dirname (__FILE__) . '/Playerdisp.php');
    require_once (dirname (__FILE__) . '/Leadersdisp.php');
    if(isAdmin()) {
        require_once (dirname (__FILE__) . '/admin.php');
    }
}


add_action('wp_head', 'loadScripts' );
/**
 * Loads the plugin's javascript (including dataTables).
 */
function loadScripts() {
    wp_register_script( 'hflLeague', plugins_url('/js/ajax.js', __FILE__));
    wp_register_script('hflLeagueSorter', plugins_url('/js/jquery.dataTables.min.js', __FILE__));
    wp_print_scripts('hflLeague');
    wp_print_scripts('hflLeagueSorter');
}

add_action('wp_head', 'loadStyle' );

/**
 * Loads the plugin's css.
 */
function loadStyle() {
    wp_register_style( 'prefix-style', plugins_url('/css/hfl.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}
